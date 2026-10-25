<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Roles;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Gestion des comptes : le Super Admin modifie tous les comptes,
 * le gestionnaire seulement les caissières (policy UserPolicy).
 */
class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_super_admin_can_edit_and_update_a_cashier(): void
    {
        $superAdmin = User::factory()->create()->assignRole(Roles::SUPER_ADMIN);
        $cashier = User::factory()->create(['name' => 'Ancien Nom'])->assignRole(Roles::CASHIER);

        $this->actingAs($superAdmin)
            ->get("/admin/users/{$cashier->id}/edit")
            ->assertOk()
            ->assertSee('Ancien Nom');

        $this->actingAs($superAdmin)
            ->put("/admin/users/{$cashier->id}", [
                'name' => 'Nouveau Nom',
                'email' => $cashier->email,
                'password' => '',
                'is_active' => '1',
            ])
            ->assertRedirect();

        $this->assertSame('Nouveau Nom', $cashier->fresh()->name);
    }

    public function test_manager_cannot_edit_super_admin_account(): void
    {
        $manager = User::factory()->create()->assignRole(Roles::MANAGER);
        $superAdmin = User::factory()->create()->assignRole(Roles::SUPER_ADMIN);

        $this->actingAs($manager)
            ->get("/admin/users/{$superAdmin->id}/edit")
            ->assertForbidden();

        $this->actingAs($manager)
            ->put("/admin/users/{$superAdmin->id}", [
                'name' => 'Piraté',
                'email' => $superAdmin->email,
                'password' => '',
            ])
            ->assertForbidden();
    }

    public function test_manager_can_disable_a_cashier(): void
    {
        $manager = User::factory()->create()->assignRole(Roles::MANAGER);
        $cashier = User::factory()->create()->assignRole(Roles::CASHIER);

        $this->actingAs($manager)
            ->put("/admin/users/{$cashier->id}", [
                'name' => $cashier->name,
                'email' => $cashier->email,
                'password' => '',
                // is_active absent : compte désactivé
            ])
            ->assertRedirect();

        $this->assertFalse($cashier->fresh()->is_active);
    }
}
