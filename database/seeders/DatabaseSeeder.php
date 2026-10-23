<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Les comptes du back-office (Super Admin, gestionnaire, caissière)
     * arrivent en phase 3 ; les données métier (catégories, véhicules,
     * FAQ) en phase 2 et 4.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
        ]);
    }
}
