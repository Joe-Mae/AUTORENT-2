<?php

use App\Http\Controllers\Site;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site public
|--------------------------------------------------------------------------
| Toutes les URL publiques passent par un préfixe de langue. Seul « fr »
| existe en v1 ; ajouter une locale dans SetLocale::LOCALES suffit à
| l'exposer. La racine redirige vers la locale par défaut.
*/

Route::redirect('/', '/fr');

Route::prefix('{locale}')
    ->whereIn('locale', SetLocale::LOCALES)
    ->middleware(SetLocale::class)
    ->group(function (): void {
        Route::get('/', [Site\HomeController::class, 'index'])->name('home');

        Route::get('/vehicules', [Site\VehicleController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicules/{vehicle}', [Site\VehicleController::class, 'show'])->name('vehicles.show');

        // Réservation en ligne (sans compte) : formulaire, envoi, confirmation.
        Route::get('/reservation', [Site\BookingController::class, 'create'])->name('booking.create');
        Route::post('/reservation', [Site\BookingController::class, 'store'])->middleware('throttle:booking')->name('booking.store');
        Route::get('/reservation/confirmation/{code}', [Site\BookingController::class, 'success'])->name('booking.success');

        // Consultation d'une réservation par code + email/téléphone.
        Route::get('/consulter-reservation', [Site\CheckBookingController::class, 'index'])->name('check-booking');
        Route::post('/consulter-reservation', [Site\CheckBookingController::class, 'lookup'])->middleware('throttle:lookup')->name('check-booking.lookup');
        Route::get('/consulter-reservation/{code}/pdf', [Site\CheckBookingController::class, 'pdf'])->name('check-booking.pdf');

        Route::get('/location-avec-chauffeur', [Site\PageController::class, 'driverService'])->name('driver-service');
        Route::get('/faq', [Site\PageController::class, 'faq'])->name('faq');
        Route::get('/contact', [Site\PageController::class, 'contact'])->name('contact');
        Route::post('/contact', [Site\ContactController::class, 'store'])->name('contact.submit');
        Route::get('/pages/{page}', [Site\PageController::class, 'page'])->name('page');
    });
