<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Comptes du back-office. En production, le mot de passe du Super Admin
 * vient de la variable d'environnement ADMIN_PASSWORD.
 */
class AdminUsersSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@pdj-autorent.com'],
            ['name' => 'Super Admin', 'password' => Hash::make($adminPassword), 'is_active' => true],
        );
        $superAdmin->assignRole(Roles::SUPER_ADMIN);

        $manager = User::updateOrCreate(
            ['email' => 'gestion@pdj-autorent.com'],
            ['name' => 'Gestionnaire Démo', 'password' => Hash::make('password'), 'is_active' => true],
        );
        $manager->assignRole(Roles::MANAGER);

        $cashier = User::updateOrCreate(
            ['email' => 'caisse@pdj-autorent.com'],
            ['name' => 'Caissière Démo', 'password' => Hash::make('password'), 'is_active' => true],
        );
        $cashier->assignRole(Roles::CASHIER);
    }
}
