<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Faut-il créer un compte pour réserver ?',
                'answer' => 'Non. La réservation se fait sans compte : vous remplissez le formulaire, joignez votre pièce d\'identité, et recevez un code de réservation par email.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Pourquoi dois-je envoyer ma pièce d\'identité ?',
                'answer' => 'La pièce d\'identité est obligatoire pour finaliser toute demande de réservation. Elle permet à l\'agence de vérifier votre identité avant de confirmer. Elle est conservée de manière sécurisée et n\'est jamais accessible publiquement.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Comment récupérer les clés du véhicule ?',
                'answer' => 'Vous devez vous présenter à l\'agence avec votre permis de conduire valide. Le permis est vérifié et photographié sur place avant la remise des clés.',
                'sort_order' => 3,
            ],
            [
                'question' => 'Comment se passe le paiement ?',
                'answer' => 'Le règlement s\'effectue à l\'agence, au moment de la récupération du véhicule. Aucun paiement en ligne n\'est demandé.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Quel est le tarif du chauffeur ?',
                'answer' => 'Le supplément chauffeur avec un véhicule de notre flotte est de 7 000 FCFA par jour. Pour un chauffeur seul (votre propre véhicule), le tarif est de 15 000 FCFA par jour, ramené à 13 000 FCFA par jour dès 4 jours de location.',
                'sort_order' => 5,
            ],
            [
                'question' => 'Comment suivre ou annuler ma réservation ?',
                'answer' => 'Rendez-vous sur la page « Consulter ma réservation » avec votre code de réservation et votre email ou numéro de téléphone. Vous pouvez y suivre le statut et télécharger le récapitulatif.',
                'sort_order' => 6,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], $faq);
        }
    }
}
