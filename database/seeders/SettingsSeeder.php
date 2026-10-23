<?php

namespace Database\Seeders;

use App\Support\Settings;
use Illuminate\Database\Seeder;

/**
 * Réglages par défaut de l'entreprise de démonstration « PDJ-AUTORENT ».
 * Une nouvelle entreprise personnalisera ces valeurs depuis le back-office
 * (phase 8) sans toucher au code.
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        app(Settings::class)->setMany([
            // Identité
            'company_name' => 'PDJ-AUTORENT',
            'company_legal_name' => 'PDJ-AUTORENT SARL',
            'tagline' => 'Location de voitures avec ou sans chauffeur',
            'logo' => null,
            'favicon' => null,

            // Thème
            'color_primary' => '#d95319',
            'color_secondary' => '#14213d',
            'color_accent' => '#2a9d8f',

            // Accueil
            'hero_title' => 'Louez une voiture en toute simplicité',
            'hero_subtitle' => 'Avec ou sans chauffeur, pour vos déplacements, mariages et événements. Réservation sans compte, règlement à l\'agence.',
            'hero_image' => null,
            'home_services_text' => 'Des formules pensées pour tous vos trajets.',
            'cta_title' => null,
            'cta_text' => null,

            // Pied de page et contact
            'footer_text' => 'Location de véhicules avec ou sans chauffeur, et mise à disposition de chauffeurs pour votre propre voiture.',
            'contact_address' => 'Cotonou, Bénin',
            'contact_phone' => '+229 01 00 00 00 00',
            'contact_phone_2' => null,
            'contact_email' => 'contact@pdj-autorent.com',
            'contact_whatsapp' => '+229 01 00 00 00 00',
            'contact_hours' => 'Lundi – Samedi : 8h00 à 19h00',
            'social_facebook' => null,
            'social_instagram' => null,
            'social_tiktok' => null,
            'social_x' => null,
            'map_embed_url' => null,

            // Tarification (hypothèses de la spécification, modifiables par le Super Admin)
            'currency' => 'FCFA',
            'driver_with_car_price' => '7000',
            'driver_with_car_mode' => 'supplement',
            'driver_only_price' => '15000',
            'driver_only_price_reduced' => '13000',
            'driver_only_threshold_days' => '3',
            'day_count_mode' => 'started_periods',

            // Réservations et pièces d'identité
            'reservation_code_prefix' => 'PDJ',
            'pending_blocks_vehicle' => '0',
            'upload_max_size_mb' => '5',
            'identity_retention_months' => '24',
        ]);
    }
}
