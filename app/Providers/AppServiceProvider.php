<?php

namespace App\Providers;

use App\Support\MailSettings;
use App\Support\Settings;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Settings::class);
        $this->app->singleton(MailSettings::class);
    }

    public function boot(): void
    {
        // Configuration SMTP pilotée depuis le back-office : appliquée à
        // chaque requête HTTP, et avant chaque job de la file (les emails
        // partent avec la config la plus récente, sans redémarrer le worker).
        app(MailSettings::class)->apply();
        Queue::looping(function (): void {
            app(MailSettings::class)->apply();
            app('mail.manager')->purge();
        });

        // Limitation anti-abus (spécification §8) : création de réservation
        // et consultation par code (anti-énumération).
        RateLimiter::for('booking', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip()),
        ]);

        RateLimiter::for('lookup', fn (Request $request) => [
            Limit::perMinute(10)->by($request->ip()),
        ]);

        RateLimiter::for('contact', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip()),
        ]);
    }
}
