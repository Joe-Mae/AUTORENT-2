# Guide de personnalisation — Livrer PDJ-AUTORENT à une nouvelle entreprise

Ce guide explique comment déployer et **rebrander** la plateforme pour une entreprise cliente (ex. « AUTORENT PRO ») en **15 à 30 minutes**, sans aucune modification de code. Une installation = une entreprise.

## Étape 0 — Installation dédiée

```sh
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
npm ci && npm run build
```

Dans `.env` :

```ini
APP_NAME="AUTORENT PRO"
APP_URL=https://location-autorent-pro.com   # domaine définitif

DB_DATABASE=autorent_pro
DB_USERNAME=…
DB_PASSWORD=…

MAIL_MAILER=smtp
MAIL_HOST=…            # SMTP de l'entreprise
MAIL_FROM_ADDRESS="contact@location-autorent-pro.com"

ADMIN_PASSWORD="MotDePasseTresFort"   # Super Admin initial
```

Puis :

```sh
php artisan migrate --seed   # réglages par défaut + Super Admin
php artisan storage:link
```

## Étape 1 — Connexion au back-office

`https://…/admin` avec l'email du Super Admin (`admin@pdj-autorent.com` par défaut — à renommer à l'étape 6) et `ADMIN_PASSWORD`.

> Changer le mot de passe passe par la fiche du compte (Utilisateurs → Super Admin → Modifier).

## Étape 2 — Identité et apparence

**Paramètres → Identité & apparence** :

1. **Nom de l'entreprise** (affiché partout : navbar, footer, emails, PDF) et raison sociale.
2. **Slogan** (badge du diaporama, meta description).
3. **Logo** (PNG/JPG/WebP/SVG) et **favicon**.
4. **Couleurs** : principale (boutons, liens, accents), secondaire (bandeaux, footer), accent — **aperçu en direct** pendant le réglage.
5. **Texte du pied de page**.

Enregistrer : le site public est mis à jour immédiatement (aucun cache à vider).

## Étape 3 — Accueil et diaporama

**Paramètres → Accueil & diaporama** :

- Modifier les **diapositives** : image (photos de la flotte locale, véhicules réels de l'entreprise), titre, texte. Ligne vide = retirée ; dernière ligne = ajout.
- Renseigner les **4 chiffres clés** (véhicules, clients, années, satisfaction) — ils s'animent au défilement.
- Ajuster le bandeau d'appel à l'action.

## Étape 4 — Coordonnées

**Paramètres → Coordonnées** : adresse, téléphones, email (reçoit les notifications de réservation et les messages de contact), WhatsApp, horaires, réseaux sociaux, et le **code iframe Google Maps** pour la page Contact.

## Étape 5 — Tarifs

**Paramètres → Tarifs** :

| Réglage | Défaut | Rôle |
|---|---|---|
| Devise | FCFA | Affichée partout et figée sur chaque réservation |
| Supplément chauffeur/jour | 7 000 | Ajouté au tarif du véhicule |
| Mode chauffeur | Supplément | « Forfait » si le tarif remplace celui du véhicule |
| Chauffeur seul/jour | 15 000 | Pour le véhicule du client |
| Tarif réduit/jour | 13 000 | Appliqué à **toute** la durée dès le seuil dépassé |
| Seuil (jours) | 3 | Réduction à partir de 4 jours |

Les tarifs **figés à la réservation** : modifier un réglage ne change jamais les réservations passées.

## Étape 6 — Comptes du personnel

**Utilisateurs** :

1. Créer le **vrai compte Super Admin** de l'entreprise, se reconnecter avec, puis supprimer/renommer le compte d'installation.
2. Créer les **gestionnaires** (rôle Gestionnaire) et les **caissières** (rôle Caissière).

Rappel des droits : la caissière gère les réservations ; le gestionnaire gère en plus la flotte, les contenus et les caissières ; seul le Super Admin touche à l'apparence, aux tarifs, à tous les comptes et au journal d'audit.

## Étape 7 — Contenus légaux

- **Paramètres → Pages** : adapter les **conditions de location**, la **politique de confidentialité** (durée de conservation des pièces : Paramètres → Système) et les **mentions légales** aux informations de l'entreprise.
- **Contenus → FAQ** : conserver/adapter les questions sur les règles métier (permis à l'agence, paiement sur place, tarifs).
- **Contenus → Témoignages** : saisir de vrais avis clients.

## Étape 8 — Flotte et chauffeurs

- **Véhicules** : créer chaque véhicule (marque, modèle, immatriculation, caractéristiques, **prix/jour**), ajouter les photos (la première devient la photo principale ; ré-encodage automatique, EXIF supprimées).
- **Catégories** : ajuster (Citadine, Berline, SUV…).
- **Chauffeurs** : saisir l'équipe (téléphone, permis, disponibilité).

## Étape 9 — Système

**Paramètres → Système** : préfixe des codes de réservation (ex. `ARP` → `ARP-2026-000001`), mode de calcul des jours, règle « en attente bloque le véhicule », taille max des pièces uploadées, **durée de conservation RGPD** des pièces d'identité (purge automatique nocturne).

## Checklist de livraison

- [ ] `.env` de production (APP_DEBUG=false, HTTPS, SMTP réel, ADMIN_PASSWORD fort changé)
- [ ] Nom, logo, favicon, couleurs enregistrés et vérifiés sur le site public
- [ ] Diaporama avec les vraies photos de l'entreprise
- [ ] Coordonnées complètes (email de notification testé)
- [ ] Tarifs validés par l'entreprise
- [ ] Compte Super Admin définitif + comptes du personnel créés
- [ ] Pages légales adaptées et signées par l'entreprise
- [ ] Flotte et chauffeurs saisis
- [ ] `queue:work` actif (emails) et cron `schedule:run` en place (purge RGPD)
- [ ] Test complet d'une réservation de bout en bout (demande → vérification → confirmation → PDF)

## Problèmes fréquents

| Symptôme | Cause / solution |
|---|---|
| Images de véhicules ou logo ne s'affichent pas | Lancer `php artisan storage:link` (crée `public/storage`) |
| Emails non reçus | Le worker `queue:work` n'est pas démarré, ou SMTP mal configuré |
| Le site n'affiche pas les derniers réglages | Vider le cache : `php artisan cache:clear` (le cache des réglages se vide normalement tout seul) |
| Page blanche après un `npm run build` | Le HTML du navigateur référençait d'anciens assets : recharger avec Ctrl+F5 ; en production, vider le cache du CDN/serveur |
