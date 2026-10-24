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
        $stats = [
            'reservations' => Reservation::count(),
            'pending' => Reservation::status(ReservationStatus::Pending)->count(),
            'vehicles' => Vehicle::count(),
            'vehicles_available' => Vehicle::where('status', VehicleStatus::Available)->count(),
            'drivers' => Driver::active()->count(),
            'messages' => ContactMessage::unread()->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
