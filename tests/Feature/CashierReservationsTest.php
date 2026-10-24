<?php

namespace Tests\Feature;

use App\Mail\ReservationConfirmed;
use App\Mail\ReservationRejected;
use App\Models\Reservation;
use App\Models\ReservationDocument;
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
 * Spécification §6.1 : cycle de vie géré par la caissière, et §11 :
 * vérification côté serveur des permissions des rôles.
 */
class CashierReservationsTest extends TestCase
{
    use RefreshDatabase;

    private User $cashier;

    private Reservation $reservation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, RolesAndPermissionsSeeder::class]);
        Storage::fake('private');

        $this->cashier = User::factory()->create()->assignRole(Roles::CASHIER);

        $category = VehicleCategory::create(['name' => 'Berline']);
        $vehicle = Vehicle::factory()->create(['vehicle_category_id' => $category->id]);

        $this->reservation = Reservation::create([
            'code' => 'PDJ-2026-000001',
            'type' => 'vehicle',
            'vehicle_id' => $vehicle->id,
            'first_name' => 'Jean',
            'last_name' => 'Test',
            'phone' => '+229 01 97 00 00 00',
            'email' => 'jean@test.com',
            'starts_at' => Carbon::tomorrow()->setTime(10, 0),
            'ends_at' => Carbon::tomorrow()->addDays(2)->setTime(10, 0),
            'days_count' => 2,
            'total_price' => 50_000,
            'status' => 'pending',
        ]);
    }

    public function test_cashier_can_list_reservations(): void
    {
        $this->actingAs($this->cashier)
            ->get('/admin/reservations')
            ->assertOk()
            ->assertSee($this->reservation->code);
    }

    public function test_user_without_permission_cannot_list_reservations(): void
    {
        $intruder = User::factory()->create(); // aucun rôle

        $this->actingAs($intruder)->get('/admin/reservations')->assertForbidden();
    }

    public function test_full_lifecycle_verify_confirm_handover_return(): void
    {
        Mail::fake();

        // 1. Vérification d'identité
        $this->actingAs($this->cashier)
            ->post("/admin/reservations/{$this->reservation->id}/verify-identity")
            ->assertRedirect();
        $this->assertSame('identity_verified', $this->reservation->fresh()->status->value);

        // 2. Confirmation (email envoyé)
        $this->actingAs($this->cashier)
            ->post("/admin/reservations/{$this->reservation->id}/decide", ['decision' => 'confirm'])
            ->assertRedirect();
        $this->assertSame('confirmed', $this->reservation->fresh()->status->value);
        Mail::assertQueued(ReservationConfirmed::class);

        // 3. Remise des clés avec notes d'état
        $this->actingAs($this->cashier)
            ->post("/admin/reservations/{$this->reservation->id}/handover", [
                'mileage' => 54_320, 'fuel' => 'full', 'damages' => null,
            ])
            ->assertRedirect();
        $this->assertSame('ongoing', $this->reservation->fresh()->status->value);

        // 4. Retour du véhicule
        $this->actingAs($this->cashier)
            ->post("/admin/reservations/{$this->reservation->id}/return", [
                'mileage' => 55_110, 'fuel' => 'half', 'damages' => 'Rayure pare-choc avant',
            ])
            ->assertRedirect();
        $this->assertSame('completed', $this->reservation->fresh()->status->value);

        // Chaque étape est historisée, attribuée à la caissière, avec les notes.
        $histories = $this->reservation->fresh()->statusHistories()->get();
        $this->assertCount(4, $histories);
        $this->assertTrue($histories->every(fn ($history) => $history->user_id === $this->cashier->id));
        $this->assertSame(55_110, (int) $histories->first()->properties['mileage']);
        $this->assertSame('Rayure pare-choc avant', $histories->first()->properties['damages']);
    }

    public function test_confirm_skips_identity_verification_is_rejected(): void
    {
        // pending → confirm direct : interdit (cycle §5.5)
        $this->actingAs($this->cashier)
            ->post("/admin/reservations/{$this->reservation->id}/decide", ['decision' => 'confirm'])
            ->assertStatus(422);

        $this->assertSame('pending', $this->reservation->fresh()->status->value);
    }

    public function test_reject_sends_email_with_reason(): void
    {
        Mail::fake();

        $this->actingAs($this->cashier)
            ->post("/admin/reservations/{$this->reservation->id}/decide", [
                'decision' => 'reject', 'note' => 'Dates déjà réservées',
            ])
            ->assertRedirect();

        $this->assertSame('rejected', $this->reservation->fresh()->status->value);
        Mail::assertQueued(ReservationRejected::class);
    }

    public function test_cashier_can_upload_license_photo(): void
    {
        $this->actingAs($this->cashier)
            ->post("/admin/reservations/{$this->reservation->id}/license", [
                'license' => UploadedFile::fake()->image('permis.jpg'),
            ])
            ->assertRedirect();

        $document = $this->reservation->fresh()->documents->firstWhere('type', 'license');
        $this->assertNotNull($document);
        Storage::disk('private')->assertExists($document->path);
    }

    public function test_identity_document_view_is_served_and_logged(): void
    {
        $path = UploadedFile::fake()->image('id.jpg')->store('identity/test', 'private');
        $document = ReservationDocument::create([
            'reservation_id' => $this->reservation->id,
            'type' => 'identity_front',
            'path' => $path,
        ]);

        $this->actingAs($this->cashier)
            ->get("/admin/reservations/{$this->reservation->id}/documents/{$document->id}")
            ->assertOk();

        // Chaque consultation d'une pièce d'identité est journalisée (§8).
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->cashier->id,
            'action' => 'document.viewed',
        ]);
    }

    public function test_document_view_requires_dedicated_permission(): void
    {
        $path = UploadedFile::fake()->image('id.jpg')->store('identity/test', 'private');
        $document = ReservationDocument::create([
            'reservation_id' => $this->reservation->id,
            'type' => 'identity_front',
            'path' => $path,
        ]);

        $userWithoutPermission = User::factory()->create();
        $userWithoutPermission->assignRole(Roles::CASHIER);
        // Retire le rôle (les permissions du rôle partent avec) et ne
        // garde que « reservations.view » : la consultation des documents
        // d'identité doit être refusée.
        $userWithoutPermission->removeRole(Roles::CASHIER);
        $userWithoutPermission->givePermissionTo('reservations.view');

        $this->actingAs($userWithoutPermission)
            ->get("/admin/reservations/{$this->reservation->id}/documents/{$document->id}")
            ->assertForbidden();
    }
}
