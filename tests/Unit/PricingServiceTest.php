<?php

namespace Tests\Unit;

use App\Enums\ReservationType;
use App\Models\Vehicle;
use App\Services\PricingService;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Tests imposés par la spécification §11 : chauffeur seul à 1, 3 et 4 jours
 * (bascule 15 000 → 13 000 FCFA), voiture + chauffeur, et le calcul des
 * jours par tranches de 24 h entamées.
 */
class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    private PricingService $pricing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pricing = app(PricingService::class);

        app(Settings::class)->setMany([
            'driver_with_car_price' => '7000',
            'driver_with_car_mode' => 'supplement',
            'driver_only_price' => '15000',
            'driver_only_price_reduced' => '13000',
            'driver_only_threshold_days' => '3',
            'day_count_mode' => 'started_periods',
        ]);
    }

    private function period(int $days, int $startHour = 10): array
    {
        return [
            Carbon::parse('2026-03-02 '.$startHour.':00'),
            Carbon::parse('2026-03-02 '.$startHour.':00')->addDays($days),
        ];
    }

    public function test_driver_only_one_day_costs_15000(): void
    {
        [$start, $end] = $this->period(1);

        $quote = $this->pricing->quote(ReservationType::DriverOnly, null, $start, $end);

        $this->assertSame(1, $quote['days']);
        $this->assertSame(15_000.0, $quote['total_price']);
    }

    public function test_driver_only_three_days_costs_45000(): void
    {
        [$start, $end] = $this->period(3);

        $quote = $this->pricing->quote(ReservationType::DriverOnly, null, $start, $end);

        $this->assertSame(3, $quote['days']);
        $this->assertSame(45_000.0, $quote['total_price']);
    }

    public function test_driver_only_four_days_apply_reduced_rate_to_all_days(): void
    {
        [$start, $end] = $this->period(4);

        $quote = $this->pricing->quote(ReservationType::DriverOnly, null, $start, $end);

        $this->assertSame(4, $quote['days']);
        $this->assertSame(13_000.0, $quote['driver_daily_price']);
        $this->assertSame(52_000.0, $quote['total_price']);
    }

    public function test_vehicle_with_driver_adds_daily_supplement(): void
    {
        [$start, $end] = $this->period(2);
        $vehicle = new Vehicle(['daily_price' => 25_000]);
        $vehicle->exists = true;

        $quote = $this->pricing->quote(ReservationType::VehicleWithDriver, $vehicle, $start, $end);

        $this->assertSame(25_000.0, $quote['vehicle_daily_price']);
        $this->assertSame(7_000.0, $quote['driver_daily_price']);
        $this->assertSame(64_000.0, $quote['total_price']); // (25 000 + 7 000) × 2
    }

    public function test_vehicle_only_multiplies_daily_rate(): void
    {
        [$start, $end] = $this->period(3);
        $vehicle = new Vehicle(['daily_price' => 40_000]);
        $vehicle->exists = true;

        $quote = $this->pricing->quote(ReservationType::Vehicle, $vehicle, $start, $end);

        $this->assertSame(120_000.0, $quote['total_price']);
    }

    public function test_started_period_of_one_hour_counts_as_one_day(): void
    {
        $start = Carbon::parse('2026-03-02 10:00');
        $end = Carbon::parse('2026-03-02 12:00');

        $this->assertSame(1, $this->pricing->daysCount($start, $end));
    }

    public function test_25_hours_count_as_two_days(): void
    {
        $start = Carbon::parse('2026-03-02 10:00');
        $end = Carbon::parse('2026-03-03 11:00');

        $this->assertSame(2, $this->pricing->daysCount($start, $end));
    }
}
