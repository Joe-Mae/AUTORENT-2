<?php

namespace App\Support;

/**
 * Catalogue central des rôles et permissions du back-office.
 *
 * Les rôles sont cumulatifs (spécification §6) : les permissions de la
 * caissière sont incluses dans celles du gestionnaire, elles-mêmes
 * incluses dans celles du super admin. Le seeder attribue à chaque rôle
 * l'union complète de ses permissions : un simple `$user->can(...)`
 * suffit ensuite partout dans le code.
 */
class Roles
{
    public const SUPER_ADMIN = 'super_admin';

    public const MANAGER = 'manager';

    public const CASHIER = 'cashier';

    /**
     * Toutes les permissions existantes, par domaine.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_values(array_unique(array_merge(
            self::cashierPermissions(),
            self::managerPermissions(),
            self::superAdminPermissions(),
        )));
    }

    /**
     * Caissière (accès de base) : suivi et cycle de vie des réservations.
     *
     * @return array<int, string>
     */
    public static function cashierPermissions(): array
    {
        return [
            'reservations.view',
            'reservations.verify_identity',
            'reservations.decide', // confirmer ou refuser une demande
            'reservations.upload_license',
            'reservations.update_status', // remise des clés, retour
            'reservations.view_documents',
        ];
    }

    /**
     * Gestionnaire : tout le métier (flotte, chauffeurs, contenus,
     * caissières), sans l'apparence ni les réglages globaux.
     *
     * @return array<int, string>
     */
    public static function managerPermissions(): array
    {
        return [
            'reservations.manage', // création manuelle, modification, annulation
            'vehicles.view',
            'vehicles.manage',
            'drivers.view',
            'drivers.manage',
            'content.manage', // FAQ, témoignages, messages de contact
            'users.manage_cashiers',
            'reports.view',
        ];
    }

    /**
     * Super Admin : identité du site, tarifs, utilisateurs, audit.
     *
     * @return array<int, string>
     */
    public static function superAdminPermissions(): array
    {
        return [
            'settings.manage',
            'users.manage',
            'audit.view',
        ];
    }
}
