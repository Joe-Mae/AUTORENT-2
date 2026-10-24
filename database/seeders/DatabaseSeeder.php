<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            RolesAndPermissionsSeeder::class,
            AdminUsersSeeder::class,
            VehicleCategorySeeder::class,
            VehicleSeeder::class,
            DriverSeeder::class,
            FaqSeeder::class,
            TestimonialSeeder::class,
            PageSeeder::class,
        ]);
    }
}
