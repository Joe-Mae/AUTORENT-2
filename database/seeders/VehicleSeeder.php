<?php

namespace Database\Seeders;

use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\VehicleImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VehicleSeeder extends Seeder
{
    /**
     * Véhicules de démonstration. Les photos publiques d'exemple sont
     * copiées dans storage/app/public/vehicles : le client final les
     * remplacera par ses propres photos depuis le back-office.
     */
    public function run(): void
    {
        $this->copyDemoImages();

        $vehicles = [
            [
                'category' => 'Citadine', 'brand' => 'Hyundai', 'model' => 'i10', 'year' => 2022,
                'registration_plate' => 'AB-123-CD', 'seats' => 4, 'transmission' => TransmissionType::Manual,
                'fuel' => FuelType::Petrol, 'mileage_km' => 24_500, 'daily_price' => 25_000,
                'description' => 'Petite citadine agile, parfaite pour les trajets urbains. Faible consommation.',
                'image' => 'vehicles/toyota-rouge.jpg',
            ],
            [
                'category' => 'Citadine', 'brand' => 'Kia', 'model' => 'Picanto', 'year' => 2023,
                'registration_plate' => 'EF-456-GH', 'seats' => 4, 'transmission' => TransmissionType::Automatic,
                'fuel' => FuelType::Petrol, 'mileage_km' => 12_800, 'daily_price' => 28_000,
                'description' => 'Citadine récente en boîte automatique, climatisation et Bluetooth.',
                'image' => 'vehicles/mercedes-noir.jpg',
            ],
            [
                'category' => 'Berline', 'brand' => 'Toyota', 'model' => 'Corolla', 'year' => 2021,
                'registration_plate' => 'IJ-789-KL', 'seats' => 5, 'transmission' => TransmissionType::Automatic,
                'fuel' => FuelType::Diesel, 'mileage_km' => 68_000, 'daily_price' => 40_000,
                'description' => 'Berline fiable et confortable, idéale pour les longs trajets.',
                'image' => 'vehicles/sedane-blanche.jpg',
            ],
            [
                'category' => 'Berline', 'brand' => 'Peugeot', 'model' => '301', 'year' => 2020,
                'registration_plate' => 'MN-012-OP', 'seats' => 5, 'transmission' => TransmissionType::Manual,
                'fuel' => FuelType::Diesel, 'mileage_km' => 95_400, 'daily_price' => 32_000,
                'description' => 'Berline économique, grand coffre, parfaite pour les déplacements pro.',
                'image' => 'vehicles/sedane-grise.jpg',
            ],
            [
                'category' => 'SUV / 4x4', 'brand' => 'Toyota', 'model' => 'RAV4', 'year' => 2022,
                'registration_plate' => 'QR-345-ST', 'seats' => 5, 'transmission' => TransmissionType::Automatic,
                'fuel' => FuelType::Hybrid, 'mileage_km' => 31_200, 'daily_price' => 65_000,
                'description' => 'SUV hybride spacieux et surélevé, très confortable sur toutes les routes.',
                'image' => 'vehicles/suv-gris.jpg',
            ],
            [
                'category' => 'Premium', 'brand' => 'Mercedes', 'model' => 'Classe E', 'year' => 2023,
                'registration_plate' => 'UV-678-WX', 'seats' => 5, 'transmission' => TransmissionType::Automatic,
                'fuel' => FuelType::Petrol, 'mileage_km' => 9_800, 'daily_price' => 90_000,
                'description' => 'Berline premium pour mariages et cérémonies : élégance et confort absolus.',
                'image' => 'vehicles/mercedes-noir.jpg',
            ],
        ];

        foreach ($vehicles as $data) {
            $image = $data['image'];
            unset($data['image']);

            $category = VehicleCategory::where('name', $data['category'])->first();
            unset($data['category']);

            $vehicle = Vehicle::updateOrCreate(
                ['registration_plate' => $data['registration_plate']],
                array_merge($data, ['vehicle_category_id' => $category->id]),
            );

            if (! $vehicle->images()->exists()) {
                VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'path' => $image,
                    'is_primary' => true,
                ]);
            }
        }
    }

    private function copyDemoImages(): void
    {
        Storage::disk('public')->makeDirectory('vehicles');

        $sources = [
            public_path('img/hero/pexels-pavel-danilyuk-8425044.jpg') => 'vehicles/toyota-rouge.jpg',
            public_path('img/hero/pexels-vladalex94-1402787.jpg') => 'vehicles/mercedes-noir.jpg',
            public_path('img/hero/pexels-pavel-danilyuk-8425047.jpg') => 'vehicles/sedane-blanche.jpg',
            public_path('img/hero/pexels-svjae-3802510.jpg') => 'vehicles/sedane-grise.jpg',
            public_path('img/hero/pexels-svjae-3752194.jpg') => 'vehicles/suv-gris.jpg',
        ];

        foreach ($sources as $source => $destination) {
            if (file_exists($source)) {
                File::copy($source, Storage::disk('public')->path($destination));
            }
        }
    }
}
