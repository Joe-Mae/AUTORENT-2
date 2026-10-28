<?php

namespace Tests\Feature;

use App\Mail\ReservationRequestedInternal;
use App\Mail\TestMail;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Support\MailSettings;
use App\Support\Roles;
use App\Support\Settings;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Configuration des emails par le Super Admin (§6.3) : SMTP en base,
 * email de test, et routage des notifications vers l'email d'alerte.
 */
class MailSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingsSeeder::class, RolesAndPermissionsSeeder::class]);

        $this->superAdmin = User::factory()->create()->assignRole(Roles::SUPER_ADMIN);
    }

    public function test_saving_smtp_settings_stores_them(): void
    {
        $this->actingAs($this->superAdmin)
            ->post('/admin/settings', [
                'mail_host' => 'smtp.proviseur.com',
                'mail_port' => '587',
                'mail_username' => 'no-reply@agence.com',
                'mail_password' => 'secret-smtp',
                'mail_encryption' => 'auto',
                'mail_from_address' => 'no-reply@agence.com',
                'mail_from_name' => 'Agence Location',
                'notify_email' => 'alertes@agence.com',
            ])
            ->assertRedirect();

        $this->assertSame('smtp.proviseur.com', setting('mail_host'));
        $this->assertSame('alertes@agence.com', setting('notify_email'));
        $this->assertSame('secret-smtp', setting('mail_password'));
    }

    public function test_blank_password_keeps_the_existing_one(): void
    {
        $this->actingAs($this->superAdmin)
            ->post('/admin/settings', ['mail_password' => 'premier-mot', 'mail_host' => 'smtp.test.com'])
            ->assertRedirect();

        $this->actingAs($this->superAdmin)
            ->post('/admin/settings', ['mail_host' => 'smtp.test.com', 'mail_password' => ''])
            ->assertRedirect();

        $this->assertSame('premier-mot', setting('mail_password'));
    }

    public function test_mail_settings_apply_overrides_runtime_config(): void
    {
        app(Settings::class)->setMany([
            'mail_host' => 'smtp.proviseur.com',
            'mail_port' => '465',
            'mail_encryption' => 'smtps',
            'mail_from_address' => 'no-reply@agence.com',
        ]);

        app(MailSettings::class)->apply();

        $this->assertSame('smtp.proviseur.com', config('mail.mailers.smtp.host'));
        $this->assertSame(465, config('mail.mailers.smtp.port'));
        $this->assertSame('smtps', config('mail.mailers.smtp.scheme'));
        $this->assertSame('no-reply@agence.com', config('mail.from.address'));
        // Transport par défaut basculé sur SMTP même si MAIL_MAILER=log
        $this->assertSame('smtp', config('mail.default'));
    }

    public function test_apply_does_nothing_when_host_is_empty(): void
    {
        config(['mail.mailers.smtp.host' => 'env-host.local']);

        app(MailSettings::class)->apply();

        $this->assertSame('env-host.local', config('mail.mailers.smtp.host'));
    }

    public function test_send_test_email_mails_the_given_address(): void
    {
        Mail::fake();

        $this->actingAs($this->superAdmin)
            ->post('/admin/settings/test-email', ['test_to' => ' patron@agence.com'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertSent(TestMail::class,
            fn (TestMail $mail) => $mail->hasTo('patron@agence.com'));
    }

    public function test_internal_notifications_go_to_the_notify_email(): void
    {
        Mail::fake();
        app(Settings::class)->set('notify_email', 'alertes@agence.com');

        $category = VehicleCategory::create(['name' => 'Berline']);
        $vehicle = Vehicle::factory()->create(['vehicle_category_id' => $category->id]);

        $this->post('/fr/reservation', [
            'type' => 'vehicle',
            'vehicle_id' => $vehicle->id,
            'pickup_date' => Carbon::tomorrow()->format('Y-m-d'),
            'return_date' => Carbon::tomorrow()->addDays(1)->format('Y-m-d'),
            'pickup_time' => '10:00',
            'first_name' => 'Jean',
            'last_name' => 'Test',
            'phone' => '+229 01 00 00 00 06',
            'email' => 'jean@test.com',
            'birth_date' => '1990-05-12',
            'id_card_number' => 'C1234567',
            'identity_front' => UploadedFile::fake()->image('id.jpg'),
            'terms' => '1',
            'identity_consent' => '1',
        ]);

        Mail::assertQueued(ReservationRequestedInternal::class,
            fn (ReservationRequestedInternal $mail) => $mail->hasTo('alertes@agence.com'));
        Mail::assertNotQueued(ReservationRequestedInternal::class,
            fn (ReservationRequestedInternal $mail) => $mail->hasTo(setting('contact_email')));
    }

    public function test_manager_cannot_send_test_email(): void
    {
        $manager = User::factory()->create()->assignRole(Roles::MANAGER);

        $this->actingAs($manager)
            ->post('/admin/settings/test-email', ['test_to' => 'x@y.com'])
            ->assertForbidden();
    }
}
