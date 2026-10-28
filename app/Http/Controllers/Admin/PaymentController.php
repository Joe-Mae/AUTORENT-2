<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReservationStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Caisse de l'agence : suivi des encaissements des locations (§5.4 —
 * le règlement se fait sur place). La caissière encaisse ; gestionnaire
 * et Super Admin héritent du même droit (rôles cumulatifs).
 */
class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $date = $request->date('date') ? Carbon::parse($request->input('date')) : now();

        $payments = Payment::with(['reservation.vehicle', 'receiver'])
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        // Locations à encaisser : actives ou passées non soldées.
        $outstanding = Reservation::with('vehicle')
            ->whereIn('status', [ReservationStatus::Confirmed, ReservationStatus::Ongoing, ReservationStatus::Completed])
            ->where('starts_at', '<=', now()->addDays(2))
            ->get()
            ->filter(fn (Reservation $reservation) => ! $reservation->isFullyPaid())
            ->sortBy('starts_at');

        return view('admin.payments.index', [
            'date' => $date,
            'payments' => $payments,
            'dayTotal' => $payments->sum('amount'),
            'byMethod' => $payments->groupBy('method')->map->sum('amount'),
            'outstanding' => $outstanding,
        ]);
    }

    public function store(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:100', 'max:'.$reservation->paymentBalance()],
            'method' => ['required', 'in:cash,mobile_money,card,transfer'],
            'reference' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'amount.max' => __('site.admin.payments.errors.too_much', ['balance' => format_price($reservation->paymentBalance())]),
        ]);

        $reservation->payments()->create($validated + [
            'received_by' => $request->user()->id,
            'currency' => setting('currency', 'FCFA'),
        ]);

        return back()->with('status', __('site.admin.payments.recorded'));
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return back()->with('status', __('site.admin.payments.deleted'));
    }
}
