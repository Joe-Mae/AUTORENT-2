<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use App\Support\Roles;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rubrique « Clients » : les messages du formulaire de contact sont
 * lisibles par les 3 rôles ; le traitement (lu/non lu, suppression)
 * reste réservé au gestionnaire et au Super Admin.
 */
class ClientsMessagesTest extends TestCase
{
    use RefreshDatabase;

    private ContactMessage $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->message = ContactMessage::create([
            'name' => 'Awa Test',
            'email' => 'awa@test.com',
            'phone' => '+229 01 00 00 00 08',
            'message' => 'Bonjour, avez-vous un SUV disponible ce week-end ?',
        ]);
    }

    public function test_all_three_roles_can_read_client_messages(): void
    {
        foreach ([Roles::CASHIER, Roles::MANAGER, Roles::SUPER_ADMIN] as $role) {
            $user = User::factory()->create()->assignRole($role);

            $this->actingAs($user)
                ->get('/admin/messages')
                ->assertOk()
                ->assertSee('Awa Test');
        }
    }

    public function test_reading_a_message_marks_it_as_read(): void
    {
        $cashier = User::factory()->create()->assignRole(Roles::CASHIER);

        $this->actingAs($cashier)
            ->get("/admin/messages/{$this->message->id}")
            ->assertOk();

        $this->assertTrue($this->message->fresh()->is_read);
    }

    public function test_cashier_cannot_delete_or_toggle_messages(): void
    {
        $cashier = User::factory()->create()->assignRole(Roles::CASHIER);

        $this->actingAs($cashier)
            ->post("/admin/messages/{$this->message->id}/toggle-read")
            ->assertForbidden();

        $this->actingAs($cashier)
            ->delete("/admin/messages/{$this->message->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('contact_messages', ['id' => $this->message->id]);
    }

    public function test_manager_can_delete_a_message(): void
    {
        $manager = User::factory()->create()->assignRole(Roles::MANAGER);

        $this->actingAs($manager)
            ->delete("/admin/messages/{$this->message->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('contact_messages', ['id' => $this->message->id]);
    }

    public function test_user_without_role_cannot_read_messages(): void
    {
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->get('/admin/messages')->assertForbidden();
    }
}
