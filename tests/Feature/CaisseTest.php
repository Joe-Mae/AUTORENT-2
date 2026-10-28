<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Support\Roles;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Caisse : encaissements à l'agence. Les 3 rôles encaissent
 * (rôles cumulatifs) ; la validation bloque le trop-perçu.
 */
class CaisseTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;

    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, RolesAndPermissionsSeeder::class]);

        $this->cashier = User::factory()->create()->assignRole(Roles::CASHIER);

        $category = VehicleCategory::create(['name' => 'Berline']);
        $vehicle = Vehicle::factory()->create(['vehicle_category_id' => $category->id]);

        $this->reservation = Reservation::create([
            'code' => 'PDJ-2026-000009',
            'type' => 'vehicle',
            'vehicle_id' => $vehicle->id,
            'first_name' => 'Jean',
            'last_name' => 'Test',
            'phone' => '+229 01 00 00 00 07',
            'email' => 'jean@test.com',
            'starts_at' => Carbon::tomorrow(),
            'ends_at' => Carbon::tomorrow()->addDays(2),
            'days_count' => 2,
            'total_price' => 50_000,
            'status' => 'confirmed',
        ]);
    }

    public function test_cashier_records_a_payment(): void
    {
        $this->actingAs($this->cashier)
            ->post("/admin/reservations/{$this->reservation->id}/payments", [
                'amount' => 20_000,
                'method' => 'mobile_money',
                'reference' => 'MOMO-889001',
            ])
            ->assertRedirect();

        $payment = Payment::first();
        $this->assertSame(20_000.0, (float) $payment->amount);
        $this->assertSame($this->cashier->id, $payment->received_by);

        // Statut partiel puis soldé
        $this->assertFalse($this->reservation->fresh()->isFullyPaid());
        $this->assertSame(30_000.0, $this->reservation->fresh()->paymentBalance());

        $this->actingAs($this->cashier)
            ->post("/admin/reservations/{$this->reservation->id}/payments", ['amount' => 30_000, 'method' => 'cash'])
            ->assertRedirect();

        $this->assertTrue($this->reservation->fresh()->isFullyPaid());
    }

    public function test_overpayment_is_rejected(): void
    {
        $this->actingAs($this->cashier)
            ->post("/admin/reservations/{$this->reservation->id}/payments", [
                'amount' => 60_000, // total 50 000
                'method' => 'cash',
            ])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_caisse_page_shows_day_totals_and_outstanding(): void
    {
        $this->actingAs($this->cashier)->post("/admin/reservations/{$this->reservation->id}/payments", [
            'amount' => 10_000, 'method' => 'cash',
        ]);

        $response = $this->actingAs($this->cashier)->get('/admin/payments');

        $response->assertOk()
            ->assertSee($this->reservation->code)
            ->assertSee('Encaissé');
    }

    public function test_user_without_permission_cannot_access_caisse(): void
    {
        $intruder = User::factory()->create(); // aucun rôle

        $this->actingAs($intruder)->get('/admin/payments')->assertForbidden();

        $this->actingAs($intruder)
            ->post("/admin/reservations/{$this->reservation->id}/payments", ['amount' => 1000, 'method' => 'cash'])
            ->assertForbidden();
    }

    public function test_deleting_a_payment_updates_the_balance(): void
    {
        $payment = $this->reservation->payments()->create([
            'amount' => 50_000,
            'method' => 'cash',
            'received_by' => $this->cashier->id,
        ]);

        $this->assertTrue($this->reservation->fresh()->isFullyPaid());

        $this->actingAs($this->cashier)->delete("/admin/payments/{$payment->id}")->assertRedirect();

        $this->assertFalse($this->reservation->fresh()->isFullyPaid());
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_all_three_roles_can_access_caisse(): void
    {
        foreach ([Roles::CASHIER, Roles::MANAGER, Roles::SUPER_ADMIN] as $role) {
            $user = User::factory()->create()->assignRole($role);

            $this->actingAs($user)->get('/admin/payments')->assertOk();
        }
    }
}
