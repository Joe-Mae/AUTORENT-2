<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            ['full_name' => 'Rachid Amadou', 'phone' => '+229 01 97 11 22 33', 'license_number' => 'BJ-482913', 'notes' => 'Spécialisé mariages et cérémonies. Très bonne présentation.'],
            ['full_name' => 'Sylvain Kpodjédo', 'phone' => '+229 01 96 44 55 66', 'license_number' => 'BJ-517264', 'notes' => null],
            ['full_name' => 'Marcelin Agbo', 'phone' => '+229 01 95 77 88 99', 'license_number' => 'BJ-603841', 'notes' => 'Parle anglais, idéal clients internationaux.'],
        ];

        foreach ($drivers as $driver) {
            Driver::updateOrCreate(['phone' => $driver['phone']], $driver);
        }
    }
}
