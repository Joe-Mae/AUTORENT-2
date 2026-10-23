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
            // Diapositives du carrousel d'accueil : image (chemin public ou URL), titre et texte.
            // Gérées par le Super Admin ; une diapo sans image utilise le dégradé par défaut.
            'hero_slides' => json_encode([
                ['image' => 'img/hero/pexels-vladalex94-1402787.jpg', 'title' => 'Louez une voiture en toute simplicité', 'text' => 'Avec ou sans chauffeur, pour vos déplacements, mariages et événements.'],
                ['image' => 'img/hero/pexels-pavel-danilyuk-8425044.jpg', 'title' => 'Des véhicules récents et entretenus', 'text' => 'Une flotte variée : citadines, berlines, SUV et 4x4, prêts à partir.'],
                ['image' => 'img/hero/pexels-cesarperez209-733745.jpg', 'title' => 'Votre chauffeur, votre programme', 'text' => 'Mariages, événements, déplacements professionnels : nous conduisons, vous profitez.'],
                ['image' => 'img/hero/pexels-svjae-3752194.jpg', 'title' => 'Réservation rapide, sans compte', 'text' => 'Quelques clics, votre pièce d\'identité, et c\'est parti. Règlement à l\'agence.'],
            ], JSON_UNESCAPED_UNICODE),
            'home_services_text' => 'Des formules pensées pour tous vos trajets.',
            'cta_title' => null,
            'cta_text' => null,

            // Chiffres clés (compteurs animés de l'accueil)
            'stats_vehicles' => '25',
            'stats_clients' => '1200',
            'stats_years' => '8',
            'stats_satisfaction' => '98',

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
