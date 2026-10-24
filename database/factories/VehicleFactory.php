<?php

namespace Database\Factories;

use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Enums\VehicleStatus;
use App\Models\VehicleCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vehicle_category_id' => VehicleCategory::factory(),
            'brand' => fake()->randomElement(['Toyota', 'Hyundai', 'Kia', 'Renault', 'Peugeot', 'Mercedes']),
            'model' => fake()->word(),
            'year' => fake()->numberBetween(2018, 2026),
            'registration_plate' => strtoupper(fake()->unique()->bothify('??-###-??')),
            'seats' => fake()->numberBetween(2, 7),
            'transmission' => fake()->randomElement(TransmissionType::cases()),
            'fuel' => fake()->randomElement(FuelType::cases()),
            'mileage_km' => fake()->numberBetween(1_000, 120_000),
            'daily_price' => fake()->numberBetween(25_000, 90_000),
            'status' => VehicleStatus::Available,
            'description' => fake()->paragraph(),
        ];
    }

    public function maintenance(): static
    {
        return $this->state(fn () => ['status' => VehicleStatus::Maintenance]);
    }
}
