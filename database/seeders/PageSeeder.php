<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

/**
 * Pages légales éditables. Contenus de départ volontairement génériques :
 * chaque entreprise les adaptera depuis le back-office.
 */
class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'conditions-de-location',
                'title' => 'Conditions de location',
                'content' => "<h2>Conditions générales de location</h2>\n<p>La location est ouverte à toute personne majeure disposant d'un permis de conduire valide présenté à l'agence lors de la remise des clés.</p>\n<h3>Réservation</h3>\n<p>Toute demande de réservation doit être accompagnée d'une pièce d'identité en cours de validité. La réservation n'est confirmée qu'après vérification de l'identité par l'agence.</p>\n<h3>Paiement</h3>\n<p>Le règlement s'effectue à l'agence, à la récupération du véhicule. Le prix est calculé sur la base du tarif journalier du véhicule multiplié par le nombre de jours de location.</p>\n<h3>Carburant et état du véhicule</h3>\n<p>Le véhicule est remis avec un niveau de carburant et un kilométrage consignés. Il doit être restitué dans un état compatible avec l'usage convenu.</p>",
            ],
            [
                'slug' => 'confidentialite',
                'title' => 'Politique de confidentialité',
                'content' => "<h2>Vos données personnelles</h2>\n<p>Nous collectons uniquement les données nécessaires au traitement de votre réservation : identité, coordonnées, pièce d'identité et, le cas échéant, permis de conduire.</p>\n<h3>Conservation et sécurité</h3>\n<p>Les pièces d'identité sont stockées sur un espace privé, inaccessible publiquement. Elles sont conservées pendant la durée nécessaire aux obligations légales, puis supprimées.</p>\n<h3>Vos droits</h3>\n<p>Vous pouvez demander l'accès, la rectification ou la suppression de vos données en contactant l'agence.</p>",
            ],
            [
                'slug' => 'mentions-legales',
                'title' => 'Mentions légales',
                'content' => "<h2>Éditeur du site</h2>\n<p>Ce site est édité par la société exploitant l'agence de location. Les coordonnées complètes figurent sur la page Contact.</p>\n<h2>Propriété intellectuelle</h2>\n<p>L'ensemble des contenus (textes, photographies, logo) est protégé. Toute reproduction sans autorisation est interdite.</p>",
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
