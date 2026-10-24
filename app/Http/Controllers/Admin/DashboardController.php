<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReservationStatus;
use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Driver;
use App\Models\Reservation;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->startOfDay();

        $stats = [
            'reservations' => Reservation::count(),
            'pending' => Reservation::status(ReservationStatus::Pending)->count(),
            'vehicles' => Vehicle::count(),
            'vehicles_available' => Vehicle::where('status', VehicleStatus::Available)->count(),
            'vehicles_rented' => Reservation::status(ReservationStatus::Ongoing)->distinct('vehicle_id')->count(),
            'drivers' => Driver::active()->count(),
            'messages' => ContactMessage::unread()->count(),
        ];

        // Chiffre d'affaires du mois (réservations facturables).
        $revenue = Reservation::whereIn('status', [
            ReservationStatus::Confirmed,
            ReservationStatus::Ongoing,
            ReservationStatus::Completed,
        ])
            ->whereBetween('starts_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_price');

        // Taux d'occupation : part des véhicules actuellement en location.
        $occupancy = $stats['vehicles'] > 0
            ? round($stats['vehicles_rented'] / $stats['vehicles'] * 100)
            : 0;

        $departuresToday = Reservation::with('vehicle')
            ->whereDate('starts_at', $today)
            ->whereIn('status', [ReservationStatus::Confirmed, ReservationStatus::Ongoing])
            ->orderBy('starts_at')
            ->get();

        $returnsToday = Reservation::with('vehicle')
            ->whereDate('ends_at', $today)
            ->where('status', ReservationStatus::Ongoing)
            ->orderBy('ends_at')
            ->get();

        return view('admin.dashboard', compact('stats', 'revenue', 'occupancy', 'departuresToday', 'returnsToday'));
    }
}
