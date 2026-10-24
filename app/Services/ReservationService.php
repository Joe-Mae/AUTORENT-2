<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use App\Mail\ReservationRequested;
use App\Mail\ReservationRequestedInternal;
use App\Models\Reservation;
use App\Models\ReservationDocument;
use App\Models\Vehicle;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Création d'une demande de réservation côté site public.
 *
 * Toutes les étapes critiques sont dans une transaction : contrôle de
 * disponibilité, calcul du prix serveur, code unique, stockage des
 * pièces d'identité sur le disque privé, historique de statut, emails.
 */
class ReservationService
{
    public function __construct(
        private readonly AvailabilityService $availability,
        private readonly PricingService $pricing,
    ) {}

    /**
     * @param  array<string, mixed>  $data  Données validées du formulaire
     * @param  array<string, UploadedFile>  $documents  Fichiers d'identité ('identity_front' requis, 'identity_back' optionnel)
     */
    public function create(array $data, array $documents): Reservation
    {
        $type = ReservationType::from($data['type']);
        $startsAt = Carbon::parse($data['pickup_date'].' '.$data['pickup_time']);
        $endsAt = Carbon::parse($data['return_date'].' '.$data['pickup_time']);

        $vehicle = null;
        if ($type->requiresVehicle()) {
            /** @var Vehicle $vehicle */
            $vehicle = Vehicle::available()->findOrFail($data['vehicle_id']);
        }

        if ($vehicle && ! $this->availability->isAvailable($vehicle, $startsAt, $endsAt)) {
            throw ValidationException::withMessages([
                'return_date' => __('site.booking.errors.vehicle_unavailable'),
            ]);
        }

        $quote = $this->pricing->quote($type, $vehicle, $startsAt, $endsAt);

        $reservation = DB::transaction(function () use ($data, $documents, $type, $vehicle, $startsAt, $endsAt, $quote): Reservation {
            // Seconde vérification sous verrou sur la ligne du véhicule :
            // deux demandes simultanées sur les mêmes dates ne peuvent
            // pas toutes deux passer.
            if ($vehicle) {
                $locked = Vehicle::whereKey($vehicle->id)->lockForUpdate()->first();

                if (! $this->availability->isAvailable($locked, $startsAt, $endsAt)) {
                    throw ValidationException::withMessages([
                        'return_date' => __('site.booking.errors.vehicle_unavailable'),
                    ]);
                }
            }

            $reservation = Reservation::create([
                'code' => $this->generateCode(),
                'type' => $type,
                'vehicle_id' => $vehicle?->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'email' => strtolower($data['email']),
                'birth_date' => $data['birth_date'] ?? null,
                'id_card_number' => $data['id_card_number'],
                'address' => $data['address'] ?? null,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'days_count' => $quote['days'],
                'pickup_location' => $data['pickup_location'] ?? null,
                'notes' => $data['notes'] ?? null,
                'vehicle_daily_price' => $quote['vehicle_daily_price'],
                'driver_daily_price' => $quote['driver_daily_price'],
                'total_price' => $quote['total_price'],
                'currency' => setting('currency', 'FCFA'),
                'event_reason' => $data['event_reason'] ?? null,
                'itinerary' => $data['itinerary'] ?? null,
                'passenger_count' => $data['passenger_count'] ?? null,
                'client_vehicle_brand' => $data['client_vehicle_brand'] ?? null,
                'client_vehicle_plate' => $data['client_vehicle_plate'] ?? null,
                'status' => ReservationStatus::Pending,
                'consent_accepted_at' => now(),
            ]);

            foreach ($documents as $field => $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                $documentType = match ($field) {
                    'identity_back' => DocumentType::IdentityBack,
                    default => DocumentType::IdentityFront,
                };

                $path = $file->store('identity/'.$reservation->code, 'private');

                ReservationDocument::create([
                    'reservation_id' => $reservation->id,
                    'type' => $documentType,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }

            $reservation->statusHistories()->create([
                'from_status' => null,
                'to_status' => ReservationStatus::Pending->value,
                'note' => __('site.booking.history.created_online'),
            ]);

            return $reservation;
        });

        // Emails après la transaction : la demande est enregistrée même
        // si l'envoi échoue (file d'attente + réessais).
        Mail::to($reservation->email)->queue(new ReservationRequested($reservation));
        Mail::to(setting('contact_email'))->queue(new ReservationRequestedInternal($reservation));

        return $reservation;
    }

    /**
     * Code de réservation séquentiel : PDJ-2026-000123.
     * Le préfixe vient des settings ; l'unicité est garantie par l'index
     * unique. À appeler dans une transaction pour éviter les collisions.
     */
    public function generateCode(): string
    {
        $prefix = setting('reservation_code_prefix', 'PDJ');
        $year = now()->year;

        $sequence = (int) Reservation::withTrashed()
            ->whereYear('created_at', $year)
            ->where('code', 'like', $prefix."-{$year}-%")
            ->lockForUpdate()
            ->max(DB::raw('CAST(SUBSTRING(code, -6) AS UNSIGNED)'));

        return sprintf('%s-%d-%06d', $prefix, $year, $sequence + 1);
    }

    /**
     * Supprime les documents d'identité d'une réservation (droit à
     * l'effacement, spécification §8).
     */
    public function purgeIdentityDocuments(Reservation $reservation): void
    {
        foreach ($reservation->documents as $document) {
            Storage::disk('private')->delete($document->path);
            $document->delete();
        }
    }
}
