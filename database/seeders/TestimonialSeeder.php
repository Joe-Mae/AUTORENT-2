<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'author_name' => 'Aïcha D.',
                'context' => 'Mariage à Cotonou',
                'content' => 'Voiture impeccable et disponible au bon moment. Le chauffeur pour notre mariage a été très professionnel.',
                'rating' => 5,
                'sort_order' => 1,
            ],
            [
                'author_name' => 'Kossi A.',
                'context' => 'Déplacement professionnel',
                'content' => 'Réservation simple sans créer de compte, confirmation rapide. Je recommande pour les déplacements professionnels.',
                'rating' => 5,
                'sort_order' => 2,
            ],
            [
                'author_name' => 'Fatou B.',
                'context' => 'Location week-end',
                'content' => 'Tarifs clairs et véhicule propre. L\'équipe de l\'agence explique tout calmement, rien à redire.',
                'rating' => 4,
                'sort_order' => 3,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(['author_name' => $testimonial['author_name']], $testimonial);
        }
    }
}
