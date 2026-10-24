<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Support\Roles;
use App\Support\Settings;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Spécification §6.3 : le Super Admin pilote l'identité du site et les
 * tarifs sans toucher au code ; le gestionnaire n'y a pas accès (§14).
 */
class SuperAdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, RolesAndPermissionsSeeder::class]);

        $this->superAdmin = User::factory()->create()->assignRole(Roles::SUPER_ADMIN);
        $this->manager = User::factory()->create()->assignRole(Roles::MANAGER);
    }

    public function test_super_admin_can_open_settings(): void
    {
        $this->actingAs($this->superAdmin)
            ->get('/admin/settings')
            ->assertOk()
            ->assertSee(__('site.admin.settings.identity'));
    }

    public function test_manager_cannot_access_settings(): void
    {
        $this->actingAs($this->manager)
            ->get('/admin/settings')
            ->assertForbidden();
    }

    public function test_saving_settings_updates_the_site_immediately(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post('/admin/settings', [
                'company_name' => 'AUTORENT PRO',
                'tagline' => 'Location pro',
                'color_primary' => '#1a7f37',
                'currency' => 'FCFA',
                'driver_with_car_price' => '8000',
                'driver_with_car_mode' => 'supplement',
                'driver_only_price' => '15000',
                'driver_only_price_reduced' => '12000',
                'driver_only_threshold_days' => '3',
                'day_count_mode' => 'started_periods',
                'reservation_code_prefix' => 'AR',
                'pending_blocks_vehicle' => '1',
                'upload_max_size_mb' => '5',
                'identity_retention_months' => '24',
            ]);

        $response->assertRedirect();

        // Cache invalidé : la nouvelle valeur est lisible immédiatement.
        $this->assertSame('AUTORENT PRO', setting('company_name'));
        $this->assertSame('#1a7f37', setting('color_primary'));
        $this->assertSame('8000', setting('driver_with_car_price'));

        // Le site public reflète le changement sans déploiement.
        $this->get('/fr')
            ->assertOk()
            ->assertSee('AUTORENT PRO')
            ->assertSee('--color-primary: #1a7f37');
    }

    public function test_invalid_color_is_rejected(): void
    {
        $this->actingAs($this->superAdmin)
            ->post('/admin/settings', [
                'company_name' => 'Test',
                'color_primary' => 'vert',
                'currency' => 'FCFA',
                'driver_with_car_price' => '7000',
                'driver_with_car_mode' => 'supplement',
                'driver_only_price' => '15000',
                'driver_only_price_reduced' => '13000',
                'driver_only_threshold_days' => '3',
                'day_count_mode' => 'started_periods',
                'reservation_code_prefix' => 'PDJ',
                'pending_blocks_vehicle' => '0',
                'upload_max_size_mb' => '5',
                'identity_retention_months' => '24',
            ])
            ->assertSessionHasErrors('color_primary');
    }

    public function test_pending_reservation_blocks_when_setting_enabled(): void
    {
        $category = VehicleCategory::create(['name' => 'Berline']);
        $vehicle = Vehicle::factory()->create([
            'vehicle_category_id' => $category->id,
            'brand' => 'MarqueUnique',
            'model' => 'ModeleUnique',
        ]);

        Reservation::create([
            'code' => 'PDJ-2026-000005',
            'type' => 'vehicle',
            'vehicle_id' => $vehicle->id,
            'first_name' => 'A',
            'last_name' => 'B',
            'phone' => '+229 01 00 00 00 04',
            'email' => 'a@test.com',
            'starts_at' => Carbon::tomorrow(),
            'ends_at' => Carbon::tomorrow()->addDays(2),
            'days_count' => 2,
            'total_price' => 50_000,
            'status' => 'pending',
        ]);

        // Par défaut : en attente ne bloque pas.
        $this->get('/fr/vehicules?pickup_date='.Carbon::tomorrow()->format('Y-m-d').'&return_date='.Carbon::tomorrow()->addDay()->format('Y-m-d'))
            ->assertOk()
            ->assertSee($vehicle->model);

        // Réglage activé : le véhicule disparaît du garage sur ces dates.
        app(Settings::class)->set('pending_blocks_vehicle', '1');

        $this->get('/fr/vehicules?pickup_date='.Carbon::tomorrow()->format('Y-m-d').'&return_date='.Carbon::tomorrow()->addDay()->format('Y-m-d'))
            ->assertOk()
            ->assertDontSee($vehicle->model);
    }

    public function test_super_admin_can_manage_legal_pages(): void
    {
        $this->actingAs($this->superAdmin)
            ->post('/admin/pages', [
                'slug' => 'a-propos',
                'title' => 'À propos',
                'content' => '<h2>Notre histoire</h2>',
            ])
            ->assertRedirect();

        $this->get('/fr/pages/a-propos')->assertOk()->assertSee('Notre histoire');

        $this->actingAs($this->superAdmin)
            ->get('/admin/pages')
            ->assertOk()
            ->assertSee('a-propos');
    }

    public function test_audit_log_lists_entries_and_filters_by_user(): void
    {
        ActivityLog::create([
            'user_id' => $this->superAdmin->id,
            'action' => 'settings.updated',
            'description' => 'Paramètres modifiés',
        ]);

        $this->actingAs($this->superAdmin)
            ->get('/admin/audit')
            ->assertOk()
            ->assertSee('settings.updated');

        $this->actingAs($this->superAdmin)
            ->get('/admin/audit?user_id='.$this->manager->id)
            ->assertOk()
            ->assertDontSee('Paramètres modifiés');
    }

    public function test_manager_cannot_access_audit(): void
    {
        $this->actingAs($this->manager)
            ->get('/admin/audit')
            ->assertForbidden();
    }
}
