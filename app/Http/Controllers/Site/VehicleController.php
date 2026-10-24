<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleFilterRequest;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function __construct(private readonly AvailabilityService $availability) {}

    public function index(VehicleFilterRequest $request): View
    {
        $filters = $request->filters();

        $query = Vehicle::query()
            ->available()
            ->with(['category', 'primaryImage'])
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('vehicle_category_id', $category))
            ->when($filters['brand'] ?? null, fn ($q, $brand) => $q->where('brand', $brand))
            ->when($filters['seats'] ?? null, fn ($q, $seats) => $q->where('seats', '>=', $seats))
            ->when($filters['transmission'] ?? null, fn ($q, $transmission) => $q->where('transmission', $transmission))
            ->when($filters['fuel'] ?? null, fn ($q, $fuel) => $q->where('fuel', $fuel))
            ->when($filters['price_min'] ?? null, fn ($q, $min) => $q->where('daily_price', '>=', $min))
            ->when($filters['price_max'] ?? null, fn ($q, $max) => $q->where('daily_price', '<=', $max));

        // Disponibilité sur des dates : on exclut les véhicules déjà
        // réservés ou bloqués en maintenance (règle §5.4 configurable).
        if (! empty($filters['pickup_date']) && ! empty($filters['return_date'])) {
            $pickup = $filters['pickup_date'].' 00:00:00';
            $return = $filters['return_date'].' 23:59:59';
            $blockingStatuses = $this->availability->blockingStatuses();

            $query->whereNotExists(function ($sub) use ($pickup, $return, $blockingStatuses): void {
                $sub->selectRaw(1)
                    ->from('reservations')
                    ->whereColumn('reservations.vehicle_id', 'vehicles.id')
                    ->whereIn('reservations.status', $blockingStatuses)
                    ->where('reservations.starts_at', '<', $return)
                    ->where('reservations.ends_at', '>', $pickup);
            })->whereNotExists(function ($sub) use ($pickup, $return): void {
                $sub->selectRaw(1)
                    ->from('vehicle_blocked_periods')
                    ->whereColumn('vehicle_blocked_periods.vehicle_id', 'vehicles.id')
                    ->where('vehicle_blocked_periods.starts_at', '<', $return)
                    ->where('vehicle_blocked_periods.ends_at', '>', $pickup);
            });
        }

        match ($filters['sort'] ?? null) {
            'price_asc' => $query->orderBy('daily_price'),
            'price_desc' => $query->orderByDesc('daily_price'),
            'year_desc' => $query->orderByDesc('year'),
            'brand_asc' => $query->orderBy('brand'),
            default => $query->orderByDesc('year'),
        };

        return view('site.vehicles.index', [
            'vehicles' => $query->paginate(9)->withQueryString(),
            'categories' => VehicleCategory::orderBy('sort_order')->get(),
            'brands' => Vehicle::available()->distinct()->orderBy('brand')->pluck('brand'),
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, Vehicle $vehicle): View
    {
        abort_unless($vehicle->status?->value === 'available' || $vehicle->status?->value === 'maintenance', 404);

        $vehicle->load(['images', 'category']);

        $calendarFrom = Carbon::today();
        $calendarTo = Carbon::today()->addMonths(2)->endOfMonth();

        return view('site.vehicles.show', [
            'vehicle' => $vehicle,
            'unavailableDates' => $this->availability->unavailableDates($vehicle, $calendarFrom, $calendarTo),
            'similar' => Vehicle::available()
                ->with(['primaryImage', 'category'])
                ->where('vehicle_category_id', $vehicle->vehicle_category_id)
                ->whereKeyNot($vehicle->id)
                ->take(3)
                ->get(),
            'pickupDate' => $request->query('pickup_date'),
            'returnDate' => $request->query('return_date'),
        ]);
    }
}
