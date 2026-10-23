<?php

/**
 * Chaines de l'interface du site public et du back-office.
 * Les contenus éditables (textes marketing, coordonnées…) ne sont pas ici :
 * ils vivent dans les settings pour rester modifiables par le Super Admin.
 */

return [

    'nav' => [
        'home' => 'Accueil',
        'vehicles' => 'Garage',
        'driver_service' => 'Chauffeurs',
        'check_booking' => 'Consulter ma réservation',
        'faq' => 'Questions fréquentes',
        'contact' => 'Contact',
    ],

    'common' => [
        'call' => 'Appeler',
        'whatsapp' => 'WhatsApp',
        'book' => 'Réserver',
        'view_more' => 'En savoir plus',
        'search' => 'Rechercher',
        'send' => 'Envoyer',
        'back_home' => 'Retour à l\'accueil',
        'per_day' => '/ jour',
        'not_found_title' => 'Page introuvable',
        'not_found_text' => 'La page que vous cherchez n\'existe pas ou a été déplacée.',
        'menu' => 'Menu',
        'close' => 'Fermer',
        'navigation' => 'Navigation',
        'follow_us' => 'Suivez-nous',
        'all_rights_reserved' => 'Tous droits réservés.',
        'slide' => 'Diapositive',
        'previous' => 'Précédent',
        'next' => 'Suivant',
    ],

    'admin' => [
        'title' => 'Back-office',
        'dashboard_welcome' => 'Bienvenue dans le back-office',
        'dashboard_text' => 'L\'authentification et les modules de gestion arrivent avec les prochaines phases du projet.',
    ],

    'home' => [
        'search' => [
            'title' => 'Trouvez votre véhicule',
            'pickup_date' => 'Date de prise',
            'return_date' => 'Date de retour',
            'formula' => 'Formule',
            'simple' => 'Location simple',
            'with_driver' => 'Avec chauffeur',
            'submit' => 'Rechercher un véhicule',
        ],
        'services' => [
            'title' => 'Nos services',
            'simple_title' => 'Location de voiture',
            'simple_text' => 'Louez le véhicule qui vous convient, sans compte à créer. Réservation rapide, règlement à l\'agence.',
            'with_driver_title' => 'Voiture avec chauffeur',
            'with_driver_text' => 'Mariages, événements, déplacements professionnels : nous fournissons la voiture et le chauffeur.',
            'driver_only_title' => 'Chauffeur seul',
            'driver_only_text' => 'Vous avez votre véhicule ? Nos chauffeurs conduisent pour vos courses, à la journée.',
        ],
        'stats' => [
            'vehicles' => 'Véhicules au garage',
            'clients' => 'Clients satisfaits',
            'years' => 'Années d\'expérience',
            'satisfaction' => '% de satisfaction',
        ],
        'advantages' => [
            'title' => 'Pourquoi nous choisir',
            'subtitle' => 'Un service pensé pour vous simplifier la route.',
            'insurance' => ['Assurance incluse', 'Tous nos véhicules sont couverts pendant toute la durée de la location.'],
            'recent' => ['Véhicules récents', 'Une flotte entretenue et contrôlée régulièrement.'],
            'assistance' => ['Assistance 24/7', 'Une équipe joignable à tout moment en cas de besoin.'],
            'pricing' => ['Tarifs transparents', 'Le prix affiché est le prix payé, sans surprise à l\'agence.'],
        ],
        'steps' => [
            'title' => 'Réserver en 4 étapes',
            'choose_dates' => 'Choisissez vos dates',
            'select_vehicle' => 'Sélectionnez le véhicule',
            'send_id' => 'Envoyez votre pièce d\'identité',
            'confirmation' => 'Recevez la confirmation',
        ],
        'testimonials' => [
            'title' => 'Ils nous font confiance',
            'subtitle' => 'Ce que disent nos clients de nos services.',
        ],
        'cta' => [
            'button' => 'Découvrir le garage',
            'default_title' => 'Prêt à partir ?',
            'default_text' => 'Consultez notre garage et réservez en quelques minutes.',
        ],
    ],

    'vehicles' => [
        'title' => 'Notre garage',
        'subtitle' => 'Trouvez le véhicule adapté à vos besoins et à votre budget.',
        'coming_soon' => 'Le catalogue des véhicules arrive avec la prochaine phase du site.',
    ],

    'driver_service' => [
        'title' => 'Location avec chauffeur',
        'subtitle' => 'Deux formules : la voiture avec son chauffeur, ou le chauffeur seul pour votre propre véhicule.',
    ],

    'check_booking' => [
        'title' => 'Consulter ma réservation',
        'subtitle' => 'Suivez l\'état de votre demande avec votre code de réservation.',
    ],

    'faq' => [
        'title' => 'Questions fréquentes',
        'subtitle' => 'Les réponses aux questions que l\'on nous pose le plus souvent.',
    ],

    'contact' => [
        'title' => 'Contact',
        'subtitle' => 'Écrivez-nous, appelez-nous ou passez à l\'agence.',
        'name' => 'Nom complet',
        'email' => 'Adresse email',
        'phone' => 'Téléphone',
        'message' => 'Message',
        'send' => 'Envoyer le message',
    ],

];
