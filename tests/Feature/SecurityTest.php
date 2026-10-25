<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Spécification §8 : uploads assainis (EXIF supprimées), honeypot,
 * limitation de débit, documents privés inaccessibles par URL et
 * purge RGPD selon la durée de conservation.
 */
class SecurityTest extends TestCase
{
    use RefreshDatabase;

    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, RolesAndPermissionsSeeder::class]);
        Storage::fake('private');

        $category = VehicleCategory::create(['name' => 'Berline']);
        $this->vehicle = Vehicle::factory()->create(['vehicle_category_id' => $category->id]);
    }

    /**
     * @return array<string, mixed>
     */
    private function bookingData(array $overrides = []): array
    {
        return array_merge([
            'type' => 'vehicle',
            'vehicle_id' => $this->vehicle->id,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'return_date' => Carbon::tomorrow()->addDays(1)->format('Y-m-d'),
            'pickup_time' => '10:00',
            'first_name' => 'Jean',
            'last_name' => 'Test',
            'phone' => '+229 01 97 00 00 00',
            'email' => 'jean@test.com',
            'birth_date' => '1990-05-12',
            'id_card_number' => 'C1234567',
            'identity_front' => UploadedFile::fake()->image('identite.jpg'),
            'terms' => '1',
            'identity_consent' => '1',
        ], $overrides);
    }

    public function test_uploaded_identity_image_is_resized_and_reencoded(): void
    {
        // Image large de 3000 px : le stockage doit la ramener à 1600 px
        // max, preuve du ré-encodage (les métadonnées EXIF disparaissent).
        $wideImage = UploadedFile::fake()->image('identite.jpg', 3000, 2000);

        $this->post('/fr/reservation', $this->bookingData(['identity_front' => $wideImage]));

        $document = Reservation::first()->documents->first();
        $stored = Storage::disk('private')->path($document->path);

        [$width] = getimagesize($stored);
        $this->assertSame('image/jpeg', mime_content_type($stored));
        $this->assertLessThanOrEqual(1600, $width);
    }

    public function test_honeypot_blocks_spam_booking(): void
    {
        $this->post('/fr/reservation', $this->bookingData(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_honeypot_blocks_spam_contact_message(): void
    {
        $this->post('/fr/contact', [
            'name' => 'Bot',
            'email' => 'bot@spam.com',
            'message' => 'Achetez mes produits',
            'website' => 'http://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_is_rate_limited(): void
    {
        $data = ['name' => 'Jean', 'email' => 'jean@test.com', 'message' => 'Bonjour'];

        for ($i = 0; $i < 5; $i++) {
            $this->post('/fr/contact', $data);
        }

        $this->post('/fr/contact', $data)->assertStatus(429);
        $this->assertSame(5, ContactMessage::count());
    }

    public function test_identity_documents_are_not_served_by_public_url(): void
    {
        $this->post('/fr/reservation', $this->bookingData());

        $document = Reservation::first()->documents->first();

        // Le fichier vit sur le disque privé : aucune URL publique ne peut
        // le servir, même en devinant le chemin exact.
        $this->get('/storage/identity/'.$document->path)->assertForbidden();
        $this->get('/storage/'.$document->path)->assertForbidden();
    }

    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/fr');

        $response->assertOk();
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
    }

    public function test_purge_command_deletes_expired_identity_documents(): void
    {
        $this->post('/fr/reservation', $this->bookingData());

        $reservation = Reservation::first();
        $reservation->update(['status' => 'completed']);
        // created_at n'est pas fillable : forceFill pour simuler l'ancienneté.
        $reservation->forceFill(['created_at' => now()->subMonths(25)])->save();
        $documentPath = $reservation->documents->first()->path;

        Storage::disk('private')->assertExists($documentPath);

        Artisan::call('reservations:purge-identity-documents');

        Storage::disk('private')->assertMissing($documentPath);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'identity_documents.purged',
            'description' => $reservation->code,
        ]);
    }

    public function test_purge_command_keeps_recent_documents(): void
    {
        $this->post('/fr/reservation', $this->bookingData());

        $reservation = Reservation::first();
        $reservation->update(['status' => 'completed', 'created_at' => now()->subMonth()]);
        $documentPath = $reservation->documents->first()->path;

        Artisan::call('reservations:purge-identity-documents');

        Storage::disk('private')->assertExists($documentPath);
    }

    public function test_super_admin_can_anonymize_a_reservation(): void
    {
        $superAdmin = User::factory()->create()->assignRole(Roles::SUPER_ADMIN);
        $this->post('/fr/reservation', $this->bookingData());

        $reservation = Reservation::first();

        $this->actingAs($superAdmin)
            ->post("/admin/reservations/{$reservation->id}/anonymize")
            ->assertRedirect();

        $this->assertSame('anonymise@localhost', $reservation->fresh()->email);
        $this->assertNull($reservation->fresh()->id_card_number);
        $this->assertSame(0, $reservation->fresh()->documents()->count());
    }

    public function test_manager_cannot_anonymize(): void
    {
        $manager = User::factory()->create()->assignRole(Roles::MANAGER);
        $this->post('/fr/reservation', $this->bookingData());

        $reservation = Reservation::first();

        $this->actingAs($manager)
            ->post("/admin/reservations/{$reservation->id}/anonymize")
            ->assertForbidden();
    }
}
