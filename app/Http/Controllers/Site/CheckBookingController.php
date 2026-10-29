<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingLookupRequest;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * « Consulter ma réservation » : le client peut retrouver sa demande
 * par son code, par son nom / prénom, ou par email / téléphone.
 * Aucun compte : la vérification se fait par correspondance, et le PDF
 * n'est téléchargeable qu'après une recherche réussie.
 */
class CheckBookingController extends Controller
{
    public function index(): View
    {
        return view('site.check-booking');
    }

    public function lookup(BookingLookupRequest $request)
    {
        $code = trim((string) $request->input('code', ''));
        $contact = trim((string) $request->input('contact', ''));

        $query = Reservation::query()->with(['vehicle.category']);

        if ($code !== '') {
            // Insensible à la casse : le client retape souvent son code
            // en minuscules.
            $query->whereRaw('UPPER(code) = ?', [mb_strtoupper($code)]);
        }

        if ($contact !== '') {
            $this->whereContactMatches($query, $contact);
        }

        $reservations = $query->orderByDesc('starts_at')->limit(20)->get();

        if ($reservations->isEmpty()) {
            return back()
                ->withInput()
                ->with('lookup_error', __('site.check_booking.not_found'));
        }

        // Mémorise la vérification pour autoriser le téléchargement du PDF.
        foreach ($reservations as $reservation) {
            session(['verified_reservations.'.$reservation->code => now()]);
        }

        if ($reservations->count() === 1) {
            return view('site.check-booking', [
                'reservation' => $reservations->first()->load('statusHistories.user'),
            ]);
        }

        return view('site.check-booking', ['reservations' => $reservations]);
    }

    public function pdf(Request $request, string $code)
    {
        $verifiedAt = $request->session()->get('verified_reservations.'.$code);

        if (! $verifiedAt || now()->diffInMinutes(Carbon::parse($verifiedAt)) > 60) {
            abort(403, __('site.check_booking.pdf_forbidden'));
        }

        $reservation = Reservation::where('code', $code)->firstOrFail();

        return Pdf::loadView('pdf.reservation', [
            'reservation' => $reservation->load('vehicle.category'),
        ])->download(Str::slug($reservation->code).'.pdf');
    }

    /**
     * Email exact, téléphone comparé chiffre par chiffre (les espaces,
     * tirets et indicatifs varient selon la saisie) et nom/prénom par
     * mots (« Joe-Maria Adj » retrouve « Joe-Maria ADJANOR »).
     */
    private function whereContactMatches(Builder $query, string $contact): void
    {
        $needle = mb_strtolower($contact);
        $digits = preg_replace('/\D+/', '', $contact) ?? '';

        $query->where(function (Builder $query) use ($needle, $digits): void {
            $query->whereRaw('LOWER(email) = ?', [$needle]);

            if (strlen($digits) >= 6) {
                $query->orWhereRaw(
                    "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '.', ''), '(', ''), ')', '') LIKE ?",
                    ['%'.$digits.'%'],
                );
            }

            $tokens = preg_split('/\s+/', $needle, -1, PREG_SPLIT_NO_EMPTY) ?: [];

            if ($tokens !== []) {
                $query->orWhere(function (Builder $query) use ($tokens): void {
                    foreach ($tokens as $token) {
                        $query->where(function (Builder $query) use ($token): void {
                            $query->whereRaw('LOWER(first_name) LIKE ?', ['%'.$token.'%'])
                                ->orWhereRaw('LOWER(last_name) LIKE ?', ['%'.$token.'%']);
                        });
                    }
                });
            }
        });
    }
}
