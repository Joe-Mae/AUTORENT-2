<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingLookupRequest;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * « Consulter ma réservation » : le client saisit son code et son email
 * ou téléphone. Aucun compte : la vérification se fait par correspondance
 * exacte, et le PDF n'est téléchargeable qu'après une recherche réussie.
 */
class CheckBookingController extends Controller
{
    public function index(): View
    {
        return view('site.check-booking');
    }

    public function lookup(BookingLookupRequest $request)
    {
        $contact = strtolower(trim($request->string('contact')));

        $reservation = Reservation::where('code', trim($request->string('code')))
            ->where(fn ($query) => $query
                ->where('email', $contact)
                ->orWhere('phone', 'like', "%{$contact}%"))
            ->first();

        if (! $reservation) {
            return back()
                ->withInput()
                ->with('lookup_error', __('site.check_booking.not_found'));
        }

        // Mémorise la vérification pour autoriser le téléchargement du PDF.
        session(['verified_reservations.'.$reservation->code => now()]);

        return view('site.check-booking', [
            'reservation' => $reservation->load(['vehicle.category', 'statusHistories.user']),
        ]);
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
}
