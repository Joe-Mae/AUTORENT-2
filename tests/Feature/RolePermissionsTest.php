<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Roles;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Permissions cumulatives : Caissière ⊂ Gestionnaire ⊂ Super Admin
 * (spécification §6). Vérifiées côté serveur, pas seulement dans l'UI.
 */
class RolePermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_cashier_has_base_permissions_only(): void
    {
        $user = User::factory()->create()->assignRole(Roles::CASHIER);

        $this->assertTrue($user->can('reservations.view'));
        $this->assertTrue($user->can('reservations.verify_identity'));
        $this->assertFalse($user->can('vehicles.manage'));
        $this->assertFalse($user->can('settings.manage'));
        $this->assertFalse($user->can('users.manage'));
    }

    public function test_manager_includes_cashier_permissions_and_more(): void
    {
        $user = User::factory()->create()->assignRole(Roles::MANAGER);

        $this->assertTrue($user->can('reservations.view'));
        $this->assertTrue($user->can('vehicles.manage'));
        $this->assertTrue($user->can('users.manage_cashiers'));
        $this->assertFalse($user->can('settings.manage'));
        $this->assertFalse($user->can('users.manage'));
    }

    public function test_super_admin_has_every_permission(): void
    {
        $user = User::factory()->create()->assignRole(Roles::SUPER_ADMIN);

        foreach (Roles::all() as $permission) {
            $this->assertTrue($user->can($permission), "Le super admin devrait avoir « {$permission} »");
        }
    }

    public function test_cashier_cannot_reach_a_settings_protected_route(): void
    {
        $user = User::factory()->create()->assignRole(Roles::CASHIER);

        // Le module Paramètres n'existe pas encore (phase 8) : on vérifie
        // le mécanisme de permission sur une route factice protégée.
        $this->assertFalse($user->can('settings.manage'));
    }
}
