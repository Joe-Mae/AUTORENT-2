<?php

namespace Tests\Feature;

use App\Mail\ReservationCancelled;
use App\Models\Driver;
use App\Models\Faq;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Support\Roles;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Spécification §6.2 : le gestionnaire gère la flotte, les réservations
 * (création manuelle, modification, annulation), les chauffeurs et les
 * contenus — mais pas les réglages du site (§14).
 */
class ManagerBackOfficeTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    private User $cashier;

    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, RolesAndPermissionsSeeder::class]);
        Storage::fake('public');

        $this->manager = User::factory()->create()->assignRole(Roles::MANAGER);
        $this->cashier = User::factory()->create()->assignRole(Roles::CASHIER);

        $category = VehicleCategory::create(['name' => 'Berline']);
        $this->vehicle = Vehicle::factory()->create(['vehicle_category_id' => $category->id, 'daily_price' => 30_000]);
    }

    public function test_manager_can_create_vehicle_with_photos(): void
    {
        $category = VehicleCategory::first();

        $response = $this->actingAs($this->manager)
            ->post('/admin/vehicles', [
                'vehicle_category_id' => $category->id,
                'brand' => 'Toyota',
                'model' => 'Yaris',
                'year' => 2024,
                'registration_plate' => 'ZZ-999-ZZ',
                'seats' => 5,
                'transmission' => 'automatic',
                'fuel' => 'petrol',
                'mileage_km' => 1000,
                'daily_price' => 30_000,
                'status' => 'available',
                'photos' => [UploadedFile::fake()->image('yaris.jpg')],
            ]);

        $response->assertRedirect(route('admin.vehicles.index'));

        $vehicle = Vehicle::where('registration_plate', 'ZZ-999-ZZ')->first();
        $this->assertNotNull($vehicle);
        $this->assertTrue($vehicle->images->first()->is_primary);
        Storage::disk('public')->assertExists($vehicle->images->first()->path);
    }

    public function test_cashier_cannot_manage_vehicles(): void
    {
        $this->actingAs($this->cashier)
            ->get('/admin/vehicles')
            ->assertForbidden();
    }

    public function test_manager_creates_manual_reservation_confirmed(): void
    {
        $response = $this->actingAs($this->manager)
            ->post('/admin/reservations', [
                'type' => 'vehicle',
                'vehicle_id' => $this->vehicle->id,
                'first_name' => 'Client',
                'last_name' => 'Agence',
                'phone' => '+229 01 00 11 22 33',
                'email' => 'agence@test.com',
                'starts_at' => Carbon::tomorrow()->setTime(9, 0)->format('Y-m-d H:i'),
                'ends_at' => Carbon::tomorrow()->addDays(2)->setTime(9, 0)->format('Y-m-d H:i'),
            ]);

        $reservation = Reservation::where('email', 'agence@test.com')->first();

        $this->assertNotNull($reservation);
        $this->assertSame('confirmed', $reservation->status->value);
        $this->assertSame(60_000.0, (float) $reservation->total_price); // 30 000 × 2 j
        $response->assertRedirect(route('admin.reservations.show', $reservation));
    }

    public function test_manager_can_cancel_reservation_with_email(): void
    {
        Mail::fake();

        $reservation = Reservation::create([
            'code' => 'PDJ-2026-000042',
            'type' => 'vehicle',
            'vehicle_id' => $this->vehicle->id,
            'first_name' => 'Jean',
            'last_name' => 'Test',
            'phone' => '+229 01 00 00 00 00',
            'email' => 'jean@test.com',
            'starts_at' => Carbon::tomorrow(),
            'ends_at' => Carbon::tomorrow()->addDays(2),
            'days_count' => 2,
            'total_price' => 60_000,
            'status' => 'confirmed',
        ]);

        $this->actingAs($this->manager)
            ->post("/admin/reservations/{$reservation->id}/cancel", ['note' => 'Client absent'])
            ->assertRedirect();

        $this->assertSame('cancelled', $reservation->fresh()->status->value);
        Mail::assertQueued(ReservationCancelled::class);
    }

    public function test_driver_cannot_be_double_booked(): void
    {
        $driver = Driver::create(['full_name' => 'Test Driver', 'phone' => '+229 01 00 00 00 01', 'license_number' => 'X1']);

        Reservation::create([
            'code' => 'PDJ-2026-000010',
            'type' => 'vehicle_with_driver',
            'driver_id' => $driver->id,
            'first_name' => 'A',
            'last_name' => 'B',
            'phone' => '+229 01 00 00 00 02',
            'email' => 'a@test.com',
            'starts_at' => Carbon::tomorrow(),
            'ends_at' => Carbon::tomorrow()->addDays(2),
            'days_count' => 2,
            'total_price' => 50_000,
            'status' => 'confirmed',
        ]);

        $other = Reservation::create([
            'code' => 'PDJ-2026-000011',
            'type' => 'vehicle_with_driver',
            'first_name' => 'C',
            'last_name' => 'D',
            'phone' => '+229 01 00 00 00 03',
            'email' => 'c@test.com',
            'starts_at' => Carbon::tomorrow()->addDay(),
            'ends_at' => Carbon::tomorrow()->addDays(3),
            'days_count' => 2,
            'total_price' => 50_000,
            'status' => 'confirmed',
        ]);

        // Affectation refusée : le chauffeur est déjà pris en chevauchement.
        $this->actingAs($this->manager)
            ->post("/admin/reservations/{$other->id}/driver", ['driver_id' => $driver->id])
            ->assertSessionHas('error');

        $this->assertNull($other->fresh()->driver_id);
    }

    public function test_manager_can_manage_faqs(): void
    {
        $this->actingAs($this->manager)
            ->post('/admin/faqs', ['question' => 'Nouvelle question ?', 'answer' => 'Réponse claire.'])
            ->assertRedirect();

        $faq = Faq::where('question', 'Nouvelle question ?')->first();
        $this->assertNotNull($faq);
        $this->assertTrue($faq->is_active);

        // Visible immédiatement côté site public.
        $this->get('/fr/faq')->assertOk()->assertSee('Nouvelle question ?');
    }

    public function test_cashier_cannot_manage_users(): void
    {
        $this->actingAs($this->cashier)
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_manager_creates_cashier_account(): void
    {
        $response = $this->actingAs($this->manager)
            ->post('/admin/users', [
                'name' => 'Nouvelle Caissière',
                'email' => 'caisse2@pdj-autorent.com',
                'password' => 'motdepasse8',
                'role' => 'cashier',
            ]);

        $response->assertRedirect();

        $user = User::where('email', 'caisse2@pdj-autorent.com')->first();
        $this->assertTrue($user->hasRole(Roles::CASHIER));
    }

    public function test_manager_cannot_create_manager_account(): void
    {
        $this->actingAs($this->manager)
            ->post('/admin/users', [
                'name' => 'Escalade',
                'email' => 'hack@pdj-autorent.com',
                'password' => 'motdepasse8',
                'role' => 'manager',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'hack@pdj-autorent.com']);
    }

    public function test_manager_can_export_reservations_csv(): void
    {
        Reservation::create([
            'code' => 'PDJ-2026-000077',
            'type' => 'vehicle',
            'vehicle_id' => $this->vehicle->id,
            'first_name' => 'Csv',
            'last_name' => 'Test',
            'phone' => '+229 01 00 00 00 09',
            'email' => 'csv@test.com',
            'starts_at' => now(),
            'ends_at' => now()->addDays(1),
            'days_count' => 1,
            'total_price' => 30_000,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->manager)
            ->get('/admin/reports/export/csv?from='.now()->format('Y-m-d').'&to='.now()->addDay()->format('Y-m-d'));

        $response->assertOk();
        $this->assertSame('text/csv; charset=utf-8', strtolower($response->headers->get('Content-Type')));
    }
}
