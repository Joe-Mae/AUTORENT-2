<?php

namespace Database\Seeders;

use App\Models\VehicleCategory;
use Illuminate\Database\Seeder;

class VehicleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Citadine', 'description' => 'Compactes et économiques, idéales en ville.', 'sort_order' => 1],
            ['name' => 'Berline', 'description' => 'Confort et espace pour vos trajets longue distance.', 'sort_order' => 2],
            ['name' => 'SUV / 4x4', 'description' => 'Robustes et spacieuses, pour la ville comme la brousse.', 'sort_order' => 3],
            ['name' => 'Premium', 'description' => 'Berlines haut de gamme pour vos occasions spéciales.', 'sort_order' => 4],
            ['name' => 'Utilitaire', 'description' => 'Transports de marchandises et déménagements.', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            VehicleCategory::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
