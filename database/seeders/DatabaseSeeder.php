<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Les comptes du back-office (Super Admin, gestionnaire, caissière)
     * sont créés en phase 3, avec les rôles et permissions.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            VehicleCategorySeeder::class,
            VehicleSeeder::class,
            DriverSeeder::class,
            FaqSeeder::class,
            TestimonialSeeder::class,
            PageSeeder::class,
        ]);
    }
}
