<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Roles;

/**
 * Contrôle d'accès à la gestion des comptes du back-office.
 * Le gestionnaire gère les caissières ; seul le Super Admin gère
 * tous les comptes (gestionnaires inclus).
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['users.manage', 'users.manage_cashiers']);
    }

    public function view(User $user, User $target): bool
    {
        return $this->update($user, $target);
    }

    public function create(User $user): bool
    {
        return $user->canAny(['users.manage', 'users.manage_cashiers']);
    }

    public function update(User $user, User $target): bool
    {
        // Un compte peut toujours modifier son propre profil.
        if ($user->is($target)) {
            return true;
        }

        // Le Super Admin gère tout le monde, sauf les autres super admins.
        if ($user->isSuperAdmin()) {
            return ! $target->isSuperAdmin();
        }

        // Le gestionnaire gère uniquement les caissières.
        return $user->hasRole(Roles::MANAGER)
            && $target->hasRole(Roles::CASHIER);
    }

    public function delete(User $user, User $target): bool
    {
        if ($user->is($target)) {
            return false;
        }

        return $user->isSuperAdmin()
            ? ! $target->isSuperAdmin()
            : $user->hasRole(Roles::MANAGER) && $target->hasRole(Roles::CASHIER);
    }
}
