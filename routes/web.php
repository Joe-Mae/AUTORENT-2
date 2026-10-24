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
        Route::get('/reservation', [Site\BookingController::class, 'create'])->name('booking.create');
        Route::get('/location-avec-chauffeur', [Site\PageController::class, 'driverService'])->name('driver-service');
        Route::get('/consulter-reservation', [Site\PageController::class, 'checkBooking'])->name('check-booking');
        Route::get('/faq', [Site\PageController::class, 'faq'])->name('faq');
        Route::get('/contact', [Site\PageController::class, 'contact'])->name('contact');
        Route::post('/contact', [Site\ContactController::class, 'store'])->name('contact.submit');
        Route::get('/pages/{page}', [Site\PageController::class, 'page'])->name('page');
    });
