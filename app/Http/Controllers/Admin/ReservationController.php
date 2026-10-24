<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentType;
use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Mail\ReservationConfirmed;
use App\Mail\ReservationRejected;
use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\ReservationDocument;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Gestion des réservations côté agence : liste, fiche détaillée et
 * cycle de vie (vérification d'identité, décision, permis, clés, retour).
 * Les permissions fines sont posées sur les routes ; les transitions
 * de statut sont revérifiées ici (spécification §5.5).
 */
class ReservationController extends Controller
{
    /**
     * Transitions autorisées du cycle de vie : statut de départ (valeur de
     * l'enum, utilisable comme clé de tableau) → statut d'arrivée.
     *
     * @var array<string, array<string, ReservationStatus>>
     */
    private const TRANSITIONS = [
        'verify_identity' => ['pending' => ReservationStatus::IdentityVerified],
        'confirm' => ['identity_verified' => ReservationStatus::Confirmed],
        'reject' => [
            'pending' => ReservationStatus::Rejected,
            'identity_verified' => ReservationStatus::Rejected,
        ],
        'handover' => ['confirmed' => ReservationStatus::Ongoing],
        'return' => ['ongoing' => ReservationStatus::Completed],
    ];

    public function index(Request $request): View
    {
        $filters = array_filter($request->only(['status', 'vehicle_id', 'from', 'to', 'q']), fn ($value) => $value !== null && $value !== '');

        $reservations = Reservation::query()
            ->with(['vehicle.category', 'driver'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['vehicle_id'] ?? null, fn ($query, $vehicle) => $query->where('vehicle_id', $vehicle))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('starts_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('starts_at', '<=', $to))
            ->when($filters['q'] ?? null, function ($query, $q): void {
                $query->where(fn ($sub) => $sub
                    ->where('code', 'like', "%{$q}%")
                    ->orWhere('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reservations.index', [
            'reservations' => $reservations,
            'filters' => $filters,
            'vehicles' => Vehicle::orderBy('brand')->orderBy('model')->get(['id', 'brand', 'model']),
        ]);
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load(['vehicle.category', 'driver', 'documents.uploader', 'statusHistories.user']);

        return view('admin.reservations.show', ['reservation' => $reservation]);
    }

    /**
     * Vérifie l'identité du client après consultation de sa pièce.
     */
    public function verifyIdentity(Request $request, Reservation $reservation)
    {
        $this->transition($request, $reservation, 'verify_identity');

        return back()->with('status', __('site.admin.reservations.identity_verified'));
    }

    /**
     * Confirme ou refuse la demande.
     */
    public function decide(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:confirm,reject'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $action = $validated['decision'] === 'confirm' ? 'confirm' : 'reject';
        $note = $validated['note'] ?? null;

        $reservation = $this->transition($request, $reservation, $action, $note);

        if ($validated['decision'] === 'confirm') {
            Mail::to($reservation->email)->queue(new ReservationConfirmed($reservation));
        } else {
            Mail::to($reservation->email)->queue(new ReservationRejected($reservation, $note));
        }

        return back()->with('status', __('site.admin.reservations.'.$action.'ed'));
    }

    /**
     * Ajoute le permis scanné ou photographié à l'agence (§6.1).
     */
    public function uploadLicense(Request $request, Reservation $reservation)
    {
        $maxKb = ((int) setting('upload_max_size_mb', '5')) * 1024;

        $validated = $request->validate([
            'license' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:'.$maxKb],
        ]);

        $path = $validated['license']->store('license/'.$reservation->code, 'private');

        ReservationDocument::create([
            'reservation_id' => $reservation->id,
            'type' => DocumentType::License,
            'path' => $path,
            'original_name' => $validated['license']->getClientOriginalName(),
            'uploaded_by' => $request->user()->id,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'reservation.license_uploaded',
            'description' => $reservation->code,
        ]);

        return back()->with('status', __('site.admin.reservations.license_added'));
    }

    /**
     * Remise des clés : la location passe « en cours ».
     */
    public function handover(Request $request, Reservation $reservation)
    {
        $validated = $this->validateStateNotes($request);
        $this->transition($request, $reservation, 'handover', null, $validated);

        return back()->with('status', __('site.admin.reservations.handed_over'));
    }

    /**
     * Retour du véhicule : la location passe « terminée ».
     */
    public function returnVehicle(Request $request, Reservation $reservation)
    {
        $validated = $this->validateStateNotes($request);
        $this->transition($request, $reservation, 'return', null, $validated);

        return back()->with('status', __('site.admin.reservations.returned'));
    }

    /**
     * Sert un document depuis le disque privé et journalise l'accès (§8).
     */
    public function document(Request $request, Reservation $reservation, ReservationDocument $document)
    {
        abort_unless($document->reservation_id === $reservation->id, 404);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'document.viewed',
            'description' => $document->type->value.' · '.$reservation->code,
        ]);

        return response()->file(Storage::disk('private')->path($document->path));
    }

    public function pdf(Reservation $reservation)
    {
        $reservation->load('vehicle.category');

        return Pdf::loadView('pdf.reservation', ['reservation' => $reservation])
            ->download($reservation->code.'.pdf');
    }

    /**
     * Applique une transition après vérification, historise et journalise.
     *
     * @param  array<string, mixed>|null  $properties
     */
    private function transition(Request $request, Reservation $reservation, string $action, ?string $note = null, ?array $properties = null): Reservation
    {
        $from = $reservation->status;
        $allowed = self::TRANSITIONS[$action] ?? [];

        abort_unless(isset($allowed[$from->value]), 422, __('site.admin.reservations.invalid_transition'));

        $to = $allowed[$from->value];

        $reservation->update(['status' => $to]);

        $reservation->statusHistories()->create([
            'from_status' => $from->value,
            'to_status' => $to->value,
            'user_id' => $request->user()->id,
            'note' => $note,
            'properties' => $properties,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'reservation.'.$action,
            'description' => $reservation->code.' : '.$from->value.' → '.$to->value,
        ]);

        return $reservation;
    }

    /**
     * @return array<string, mixed>
     */
    private function validateStateNotes(Request $request): array
    {
        return $request->validate([
            'mileage' => ['nullable', 'integer', 'min:0'],
            'fuel' => ['nullable', 'in:eighth,quarter,half,three_quarters,full'],
            'damages' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
