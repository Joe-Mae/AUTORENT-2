# PDJ-AUTORENT — Plateforme de location de voitures en marque blanche

Plateforme web complète de location de véhicules **avec ou sans chauffeur** et de **mise à disposition de chauffeurs**, vendue en marque blanche : chaque entreprise cliente déploie sa propre installation et la personnalise en quelques minutes depuis le back-office (nom, logo, couleurs, textes, tarifs), **sans toucher au code**.

> Guide de livraison à une nouvelle entreprise : [docs/GUIDE-ENTREPRISE.md](docs/GUIDE-ENTREPRISE.md)

## Fonctionnalités

**Site public** (préfixe de langue `/fr`, architecture i18n prête pour d'autres langues) :

- Accueil dynamique : diaporama auto-défilant (modifiable en back-office), recherche rapide, véhicules en vedette, compteurs animés, témoignages en carrousel.
- Garage : filtres (catégorie, marque, places, boîte, carburant, prix, **disponibilité sur dates**), tri, pagination.
- Fiche véhicule : galerie, caractéristiques, **calendrier de disponibilité** rendu côté serveur, véhicules similaires.
- Réservation en ligne **sans compte client** : formulaire en 4 étapes, prix calculé en direct côté navigateur **et recalculé côté serveur** (seule source de vérité), **pièce d'identité obligatoire** (recto + verso optionnel), double consentement.
- Page « Consulter ma réservation » : par code, nom/prénom ou email/téléphone → statut, détails, **PDF récapitulatif**.
- Location avec chauffeur : les deux formules, tarifs issus des réglages, exemples d'usage.
- FAQ dynamique (accordéon), contact (enregistrement base + email en file d'attente + carte Google Maps configurable), pages légales éditables.

**Back-office** — 3 rôles cumulatifs (Super Admin ⊃ Gestionnaire ⊃ Caissière) :

- **Caissière** : liste filtrable des réservations, vérification d'identité, confirmation/refus (emails au client), photo/scan du permis à l'agence, remise des clés et retour avec notes d'état (kilométrage, carburant, dégâts), PDF, **encaissements** (espèces, mobile money, carte, virement).
- **Gestionnaire** : flotte (photos multiples, périodes de blocage), catégories, chauffeurs + affectation **anti double-booking**, réservations manuelles/modification/annulation, FAQ, témoignages, messages, comptes caissières, **rapports et exports CSV/PDF**.
- **Super Admin** : identité et apparence du site (logo, couleurs avec aperçu, diaporama), textes, coordonnées, **tarifs globaux** (supplément/forfait chauffeur, seuils, devise), préfixe des codes, règle « en attente bloque », pages légales, tous les comptes, **journal d'audit**.

**Sécurité** (données d'identité = priorité haute) :

- Pièces d'identité et permis sur **disque privé** — jamais d'URL publique ; accès par route authentifiée **journalisée**.
- Images uploadées **ré-encodées** (métadonnées EXIF/GPS supprimées) et redimensionnées.
- Anti brute-force (connexion), limitation de débit (réservation, consultation, contact), **honeypot** anti-spam.
- Contrôles serveur partout (permissions par rôle, transitions de statut verrouillées, prix jamais lus depuis le navigateur).
- **RGPD** : consentement à l'upload, durée de conservation configurable, **purge automatique nocturne** + anonymisation manuelle par le Super Admin.

## Stack technique

- **Back-end** : Laravel 13, PHP 8.2+ — Services métier (`PricingService`, `AvailabilityService`, `ReservationService`, `UploadSanitizer`), enums PHP (`ReservationStatus`, `ReservationType`…), Form Requests, policies.
- **Base de données** : MySQL (migrations réversibles, seeders de démonstration, factories).
- **Front-end** : Bootstrap 5 + SASS, JavaScript vanille (aucun framework JS), Vite.
- **Packages** : `spatie/laravel-permission` (rôles/permissions), `barryvdh/laravel-dompdf` (PDF), `intervention/image` (traitement des images).

## Prérequis

- PHP 8.2+ avec extensions usuelles (pdo_mysql, gd, fileinfo, mbstring)
- Composer 2
- MySQL 5.7+ / 8 (ou MariaDB)
- Node.js 18+ et npm

## Installation en développement

```sh
# 1. Dépendances PHP et JS
composer install
npm install

# 2. Configuration
cp .env.example .env
php artisan key:generate
# Renseigner dans .env : DB_DATABASE, DB_USERNAME, DB_PASSWORD
# et (optionnel, mot de passe du Super Admin initial) : ADMIN_PASSWORD

# 3. Base de données (crée les tables, les réglages PDJ-AUTORENT,
#    les comptes de démo et un jeu de véhicules d'exemple)
php artisan migrate --seed

# 4. Lien symbolique du stockage public (photos véhicules, logo)
php artisan storage:link

# 5. Assets
npm run build     # production ; `npm run dev` pour le hot-reload

# 6. Démarrer
php artisan serve
```

Le site est sur `http://localhost:8000/fr`, le back-office sur `http://localhost:8000/admin`.

### Comptes de démonstration

Créés par le seeder (à désactiver en production — voir déploiement) :

| Rôle | Email | Mot de passe |
|---|---|---|
| Super Admin | `admin@pdj-autorent.com` | `ADMIN_PASSWORD` (défaut : `password`) |
| Gestionnaire | `gestion@pdj-autorent.com` | `password` |
| Caissière | `caisse@pdj-autorent.com` | `password` |

## Commandes utiles

```sh
php artisan serve              # serveur de développement
php artisan queue:work         # traite les emails (file d'attente)
php artisan schedule:work      # exécute les tâches planifiées (purge RGPD, 3h)
php artisan test               # suite de tests (72 tests)
vendor/bin/pint                # formatage du code
```

**Important** : sans `queue:work`, les emails (confirmation, refus, annulation, contact) restent en file d'attente. En développement, `MAIL_MAILER=log` écrit les emails dans `storage/logs/laravel.log`.

## Tests

```sh
php artisan test
```

La suite couvre : tarification réglementaire (chauffeur seul 1/3/4 jours, seuils, tranches de 24 h), parcours de réservation complet sans compte, refus sans pièce d'identité, chevauchements de dates, permissions des 3 rôles, cycle de vie caissière, actions gestionnaire, réglages Super Admin, sécurité (EXIF, honeypot, débit, URLs privées, purge RGPD).

## Structure du code

```
app/
├── Console/Commands/          # purge RGPD planifiée
├── Enums/                     # ReservationStatus, ReservationType, VehicleStatus…
├── Http/Controllers/Admin/    # back-office (rôles, flotte, contenus, réglages…)
├── Http/Controllers/Site/     # site public
├── Http/Requests/             # validation des formulaires
├── Mail/                      # emails client et agence (file d'attente)
├── Models/                    # Eloquent + relations + scopes métier
├── Policies/                  # contrôle d'accès (comptes)
├── Services/                  # PricingService, AvailabilityService,
│                              # ReservationService, UploadSanitizer
└── Support/                   # Settings (marque blanche), Roles (permissions)
```

## Déploiement en production

1. `composer install --no-dev --optimize-autoloader`
2. `npm ci && npm run build`
3. `.env` de production : `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` du domaine, identifiants MySQL, **SMTP réel** (`MAIL_MAILER=smtp`…), `ADMIN_PASSWORD` fort.
4. `php artisan migrate --seed` (à l'installation initiale uniquement — le seeder crée les réglages et le Super Admin ; les données de démo peuvent être supprimées ensuite).
5. `php artisan storage:link`
6. Mettre en place :
   - un worker de file d'attente permanent (`queue:work` sous Supervisor, `systemd`…) ;
   - le cron du planificateur : `* * * * * php artisan schedule:run` ;
   - un serveur web (Nginx/Apache) pointant vers `public/`, PHP-FPM, HTTPS.
7. `php artisan config:cache && php artisan route:cache && php artisan view:cache`

## Hypothèses métier (modifiables dans le back-office)

Reprises de la spécification, toutes paramétrables par le Super Admin (Paramètres → Tarifs / Système) :

- **Devise** : FCFA, format « 7 000 FCFA ».
- **Chauffeur avec véhicule** : 7 000 FCFA/jour en **supplément** du tarif du véhicule (mode « forfait » disponible).
- **Chauffeur seul** : 15 000 FCFA/jour, **13 000 FCFA/jour appliqués à toute la durée dès 4 jours** (seuil 3 jours modifiable).
- **Jours facturés** : tranches de 24 h entamées (mode « jours calendaires » disponible).
- **Paiement à l'agence** (pas de paiement en ligne en v1).
- Une réservation **en attente ne bloque pas** le véhicule (règle activable).
- **Permis** : jamais demandé en ligne, contrôlé et photographié à l'agence.

## Licence

Projet livré dans le cadre d'une prestation. Tous droits réservés.
