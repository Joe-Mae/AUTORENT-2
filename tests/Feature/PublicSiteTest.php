<?php

namespace Tests\Feature;

use App\Enums\ReservationStatus;
use App\Models\Faq;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Database\Seeders\FaqSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, FaqSeeder::class]);
    }

    private function makeVehicle(array $attributes = []): Vehicle
    {
        $category = VehicleCategory::firstOrCreate(['name' => 'Berline']);

        return Vehicle::factory()->create(array_merge([
            'vehicle_category_id' => $category->id,
        ], $attributes));
    }

    public function test_garage_lists_available_vehicles(): void
    {
        $vehicle = $this->makeVehicle(['brand' => 'Toyota', 'model' => 'Corolla']);

        $this->get('/fr/vehicules')
            ->assertOk()
            ->assertSee('Toyota Corolla');
    }

    public function test_garage_hides_unavailable_vehicles(): void
    {
        $this->makeVehicle(['model' => 'Visible']);
        $this->makeVehicle(['model' => 'Invisible', 'status' => 'unavailable']);

        $response = $this->get('/fr/vehicules')->assertOk();

        $this->get('/fr/vehicules')->assertSee('Visible')->assertDontSee('Invisible');
    }

    public function test_garage_filters_by_category(): void
    {
        $berline = VehicleCategory::create(['name' => 'Berline']);
        $suv = VehicleCategory::create(['name' => 'SUV']);

        $this->makeVehicle(['model' => 'Corolla', 'vehicle_category_id' => $berline->id]);
        $this->makeVehicle(['model' => 'RAV4', 'vehicle_category_id' => $suv->id]);

        $this->get('/fr/vehicules?category='.$suv->id)
            ->assertOk()
            ->assertSee('RAV4')
            ->assertDontSee('Corolla');
    }

    public function test_garage_date_filter_excludes_vehicle_with_confirmed_reservation(): void
    {
        $vehicle = $this->makeVehicle(['model' => 'LoueDemain']);

        Reservation::create([
            'code' => 'PDJ-2026-000001',
            'type' => 'vehicle',
            'vehicle_id' => $vehicle->id,
            'first_name' => 'Jean',
            'last_name' => 'Test',
            'phone' => '+229 01 00 00 00 00',
            'email' => 'jean@test.com',
            'starts_at' => Carbon::tomorrow()->setTime(10, 0),
            'ends_at' => Carbon::tomorrow()->addDays(3)->setTime(10, 0),
            'days_count' => 3,
            'total_price' => 100_000,
            'status' => ReservationStatus::Confirmed,
        ]);

        // Sans dates : le véhicule apparaît.
        $this->get('/fr/vehicules')->assertOk()->assertSee('LoueDemain');

        // Sur des dates chevauchantes : il disparaît.
        $this->get('/fr/vehicules?pickup_date='.Carbon::tomorrow()->format('Y-m-d').'&return_date='.Carbon::tomorrow()->addDays(2)->format('Y-m-d'))
            ->assertOk()
            ->assertDontSee('LoueDemain');
    }

    public function test_vehicle_detail_page_displays_vehicle_and_calendar(): void
    {
        $vehicle = $this->makeVehicle(['model' => 'Corolla']);

        $this->get('/fr/vehicules/'.$vehicle->id)
            ->assertOk()
            ->assertSee('Corolla')
            ->assertSee(__('site.vehicles.availability'));
    }

    public function test_faq_page_lists_active_questions(): void
    {
        $this->get('/fr/faq')
            ->assertOk()
            ->assertSee(Faq::first()->question);
    }

    public function test_contact_form_stores_message_and_redirects(): void
    {
        $response = $this->post('/fr/contact', [
            'name' => 'Jean Test',
            'email' => 'jean@test.com',
            'phone' => '+229 01 00 00 00 00',
            'message' => 'Bonjour, je souhaite louer un véhicule.',
        ]);

        $response->assertRedirect()->assertSessionHas('status');

        $this->assertDatabaseHas('contact_messages', ['email' => 'jean@test.com']);
    }

    public function test_contact_form_rejects_invalid_data(): void
    {
        $this->from('/fr/contact')
            ->post('/fr/contact', ['name' => '', 'email' => 'pas-un-email', 'message' => ''])
            ->assertSessionHasErrors(['name', 'email', 'message']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_legal_page_renders_by_slug(): void
    {
        $this->seed(PageSeeder::class);

        $this->get('/fr/pages/mentions-legales')
            ->assertOk()
            ->assertSee('Mentions légales');
    }

    public function test_home_shows_featured_vehicles(): void
    {
        $this->makeVehicle(['model' => 'Vedette']);

        $this->get('/fr')
            ->assertOk()
            ->assertSee('Vedette')
            ->assertSee(__('site.home.featured_title'));
    }
}
