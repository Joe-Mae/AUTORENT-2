<?php

namespace Tests\Feature;

use App\Enums\ReservationStatus;
use App\Mail\ReservationRequested;
use App\Mail\ReservationRequestedInternal;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Services\PricingService;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Spécification §11 : réservation sans compte, refus sans pièce
 * d'identité, prix recalculé côté serveur, consultation par code.
 */
class ReservationFlowTest extends TestCase
{
    use RefreshDatabase;

    private Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingsSeeder::class);
        Storage::fake('private');

        $category = VehicleCategory::create(['name' => 'Berline']);
        $this->vehicle = Vehicle::factory()->create([
            'vehicle_category_id' => $category->id,
            'daily_price' => 25_000,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validBookingData(array $overrides = []): array
    {
        return array_merge([
            'type' => 'vehicle',
            'vehicle_id' => $this->vehicle->id,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'return_date' => Carbon::tomorrow()->addDays(2)->format('Y-m-d'),
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

    public function test_full_booking_without_account(): void
    {
        Mail::fake();

        $response = $this->post('/fr/reservation', $this->validBookingData());

        $reservation = Reservation::first();

        $response->assertRedirect(route('booking.success', ['locale' => 'fr', 'code' => $reservation->code]));

        $this->assertNotNull($reservation);
        $this->assertSame('pending', $reservation->status->value);
        $this->assertMatchesRegularExpression('/^PDJ-\d{4}-\d{6}$/', $reservation->code);
        $this->assertSame(50_000.0, (float) $reservation->total_price); // 25 000 × 2 jours

        // Pièce d'identité stockée sur le disque privé, jamais public.
        $document = $reservation->documents->first();
        $this->assertSame('identity_front', $document->type->value);
        Storage::disk('private')->assertExists($document->path);

        // Historique de statut initial.
        $this->assertSame('pending', $reservation->statusHistories->first()->to_status);

        // Emails client et agence mis en file d'attente.
        Mail::assertQueued(ReservationRequested::class);
        Mail::assertQueued(ReservationRequestedInternal::class);
    }

    public function test_booking_is_impossible_without_identity_document(): void
    {
        $this->post('/fr/reservation', $this->validBookingData(['identity_front' => null]))
            ->assertSessionHasErrors('identity_front');

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_booking_rejects_unavailable_vehicle_dates(): void
    {
        // Une réservation confirmée chevauchante bloque le véhicule.
        Reservation::create([
            'code' => 'PDJ-2026-000001',
            'type' => 'vehicle',
            'vehicle_id' => $this->vehicle->id,
            'first_name' => 'Autre',
            'last_name' => 'Client',
            'phone' => '+229 01 00 00 00 00',
            'email' => 'autre@test.com',
            'starts_at' => Carbon::tomorrow()->setTime(10, 0),
            'ends_at' => Carbon::tomorrow()->addDays(3)->setTime(10, 0),
            'days_count' => 3,
            'total_price' => 75_000,
            'status' => ReservationStatus::Confirmed,
        ]);

        $this->post('/fr/reservation', $this->validBookingData())
            ->assertSessionHasErrors('return_date');

        $this->assertDatabaseCount('reservations', 1); // seule la réservation initiale
    }

    public function test_price_is_computed_server_side(): void
    {
        $this->post('/fr/reservation', $this->validBookingData([
            'type' => 'vehicle_with_driver',
        ]));

        $reservation = Reservation::first();
        $expected = app(PricingService::class)->quote(
            $reservation->type,
            $this->vehicle,
            $reservation->starts_at,
            $reservation->ends_at,
        );

        $this->assertSame($expected['total_price'], (float) $reservation->total_price);
        $this->assertSame(7_000.0, (float) $reservation->driver_daily_price);
    }

    public function test_check_booking_finds_reservation_with_code_and_email(): void
    {
        Mail::fake();
        $this->post('/fr/reservation', $this->validBookingData());

        $code = Reservation::first()->code;

        $this->post('/fr/consulter-reservation', ['code' => $code, 'contact' => 'jean@test.com'])
            ->assertOk()
            ->assertSee($code)
            ->assertSee('En attente');
    }

    public function test_check_booking_rejects_wrong_contact(): void
    {
        Mail::fake();
        $this->post('/fr/reservation', $this->validBookingData());

        $code = Reservation::first()->code;

        $this->post('/fr/consulter-reservation', ['code' => $code, 'contact' => 'quelquun@autre.com'])
            ->assertSessionHas('lookup_error');
    }

    public function test_check_booking_finds_reservation_with_code_alone(): void
    {
        Mail::fake();
        $this->post('/fr/reservation', $this->validBookingData());

        $code = Reservation::first()->code;

        // Code saisi en minuscules et sans autre critère.
        $this->post('/fr/consulter-reservation', ['code' => strtolower($code)])
            ->assertOk()
            ->assertSee($code);
    }

    public function test_check_booking_finds_reservation_by_name(): void
    {
        Mail::fake();
        $this->post('/fr/reservation', $this->validBookingData([
            'first_name' => 'Joe-Maria',
            'last_name' => 'ADJANOR',
        ]));

        $code = Reservation::first()->code;

        $this->post('/fr/consulter-reservation', ['contact' => 'adjanor'])
            ->assertOk()
            ->assertSee($code);

        $this->post('/fr/consulter-reservation', ['contact' => 'Joe-Maria ADJ'])
            ->assertOk()
            ->assertSee($code);
    }

    public function test_check_booking_finds_reservation_by_phone_whatever_the_formatting(): void
    {
        Mail::fake();
        $this->post('/fr/reservation', $this->validBookingData([
            'phone' => '+22897224219',
        ]));

        $code = Reservation::first()->code;

        $this->post('/fr/consulter-reservation', ['contact' => '+228 97 22 42 19'])
            ->assertOk()
            ->assertSee($code);
    }

    public function test_check_booking_combines_code_and_name(): void
    {
        Mail::fake();
        $this->post('/fr/reservation', $this->validBookingData());

        $code = Reservation::first()->code;

        $this->post('/fr/consulter-reservation', ['code' => $code, 'contact' => 'Jean'])
            ->assertOk()
            ->assertSee($code);

        $this->post('/fr/consulter-reservation', ['code' => $code, 'contact' => 'Autre Nom'])
            ->assertSessionHas('lookup_error');
    }

    public function test_check_booking_lists_all_reservations_matching_a_name(): void
    {
        Mail::fake();

        Reservation::factory()->count(2)->create([
            'first_name' => 'Joe-Maria',
            'last_name' => 'ADJANOR',
            'email' => 'adjjoemaria16@gmail.com',
        ]);

        $codes = Reservation::pluck('code');

        $response = $this->post('/fr/consulter-reservation', ['contact' => 'ADJANOR'])
            ->assertOk()
            ->assertSee('2 réservations trouvées');

        foreach ($codes as $code) {
            $response->assertSee($code);
        }
    }

    public function test_check_booking_requires_at_least_one_criterion(): void
    {
        $this->post('/fr/consulter-reservation', [])
            ->assertSessionHasErrors('code');
    }

    public function test_pdf_download_requires_prior_verification(): void
    {
        Mail::fake();
        $this->post('/fr/reservation', $this->validBookingData());

        $code = Reservation::first()->code;

        // Sans vérification préalable : interdit.
        $this->get("/fr/consulter-reservation/{$code}/pdf")->assertForbidden();

        // Après vérification : PDF téléchargeable.
        $this->post('/fr/consulter-reservation', ['code' => $code, 'contact' => 'jean@test.com']);

        $this->get("/fr/consulter-reservation/{$code}/pdf")
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }
}
