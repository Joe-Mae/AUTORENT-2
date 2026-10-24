<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Roles;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_login_screen_renders(): void
    {
        $this->get('/admin/login')->assertOk()->assertSee(__('site.admin.submit'));
    }

    public function test_wrong_credentials_are_rejected(): void
    {
        User::factory()->create(['email' => 'caisse@pdj-autorent.com'])->assignRole(Roles::CASHIER);

        $this->from('/admin/login')
            ->post('/admin/login', ['email' => 'caisse@pdj-autorent.com', 'password' => 'mauvais'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_cashier_can_login_and_access_dashboard(): void
    {
        $user = User::factory()->create(['email' => 'caisse@pdj-autorent.com']);
        $user->assignRole(Roles::CASHIER);

        $this->post('/admin/login', ['email' => 'caisse@pdj-autorent.com', 'password' => 'password'])
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($user);

        $this->get('/admin')->assertOk()->assertSee($user->name);
    }

    public function test_inactive_account_cannot_login(): void
    {
        User::factory()->create(['email' => 'desactive@test.com', 'is_active' => false])
            ->assignRole(Roles::CASHIER);

        $this->post('/admin/login', ['email' => 'desactive@test.com', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $user = User::factory()->create()->assignRole(Roles::CASHIER);

        $this->actingAs($user)->post('/admin/logout')->assertRedirect('/admin/login');

        $this->assertGuest();
    }
}
