<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $days = fake()->numberBetween(1, 6);
        $startsAt = fake()->dateTimeBetween('now', '+20 days')->setTime(10, 0);

        return [
            'code' => 'PDJ-'.date('Y').'-'.str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'type' => ReservationType::Vehicle,
            'vehicle_id' => null,
            'driver_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify("+{$days} days"),
            'days_count' => $days,
            'vehicle_daily_price' => 0,
            'driver_daily_price' => 0,
            'total_price' => 0,
            'status' => ReservationStatus::Pending,
            'consent_accepted_at' => now(),
        ];
    }
}
