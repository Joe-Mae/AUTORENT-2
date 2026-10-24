<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Parcours de réservation en ligne, sans compte client :
 * formulaire en 4 étapes puis confirmation avec le code de suivi.
 */
class BookingController extends Controller
{
    public function __construct(private readonly ReservationService $reservations) {}

    public function create(Request $request): View
    {
        $vehicle = Vehicle::available()
            ->with(['category', 'primaryImage'])
            ->findOrFail($request->integer('vehicle'));

        return view('site.booking.create', [
            'vehicle' => $vehicle,
            'pickupDate' => $request->query('pickup_date'),
            'returnDate' => $request->query('return_date'),
            'withDriver' => $request->boolean('with_driver'),
        ]);
    }

    public function store(BookingRequest $request)
    {
        $reservation = $this->reservations->create(
            $request->validated(),
            $request->only(['identity_front', 'identity_back']),
        );

        return redirect()
            ->route('booking.success', ['locale' => app()->getLocale(), 'code' => $reservation->code])
            ->with('booking_email', $reservation->email);
    }

    public function success(string $code): View
    {
        $reservation = Reservation::where('code', $code)->firstOrFail();

        return view('site.booking.success', ['reservation' => $reservation->load('vehicle.category')]);
    }
}
