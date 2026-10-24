<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleCategoryController;
use App\Http\Controllers\Admin\VehicleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Back-office
|--------------------------------------------------------------------------
| Préfixé « admin » et nommé « admin.* » (déclaré dans bootstrap/app.php).
| Connexion réservée aux comptes de l'agence : Super Admin, Gestionnaire,
| Caissière. Chaque module vérifie ses permissions côté serveur.
*/

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Réservations : liste et fiche pour tous les rôles habilités.
    Route::middleware('can:reservations.view')->group(function (): void {
        Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::get('reservations/{reservation}/pdf', [ReservationController::class, 'pdf'])->name('reservations.pdf');

        // Consultation des pièces d'identité et permis (journalisée).
        Route::get('reservations/{reservation}/documents/{document}', [ReservationController::class, 'document'])
            ->middleware('can:reservations.view_documents')
            ->name('reservations.documents.show');

        // Cycle de vie : chaque action porte sa permission fine.
        Route::post('reservations/{reservation}/verify-identity', [ReservationController::class, 'verifyIdentity'])
            ->middleware('can:reservations.verify_identity')->name('reservations.verify-identity');
        Route::post('reservations/{reservation}/decide', [ReservationController::class, 'decide'])
            ->middleware('can:reservations.decide')->name('reservations.decide');
        Route::post('reservations/{reservation}/license', [ReservationController::class, 'uploadLicense'])
            ->middleware('can:reservations.upload_license')->name('reservations.license');
        Route::post('reservations/{reservation}/handover', [ReservationController::class, 'handover'])
            ->middleware('can:reservations.update_status')->name('reservations.handover');
        Route::post('reservations/{reservation}/return', [ReservationController::class, 'returnVehicle'])
            ->middleware('can:reservations.update_status')->name('reservations.return');

        // Gestion complète (gestionnaire) : création manuelle, modification,
        // annulation, affectation d'un chauffeur.
        Route::middleware('can:reservations.manage')->group(function (): void {
            Route::get('reservations-create', [ReservationController::class, 'create'])->name('reservations.create');
            Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
            Route::get('reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
            Route::put('reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
            Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
            Route::post('reservations/{reservation}/driver', [ReservationController::class, 'assignDriver'])->name('reservations.assign-driver');
        });
    });

    // Flotte (gestionnaire).
    Route::middleware('can:vehicles.manage')->group(function (): void {
        Route::resource('vehicles', VehicleController::class)->except(['show']);
        Route::delete('vehicle-images/{image}', [VehicleController::class, 'destroyImage'])->name('vehicle-images.destroy');
        Route::post('vehicle-images/{image}/primary', [VehicleController::class, 'makePrimary'])->name('vehicle-images.primary');
        Route::delete('vehicle-blocked-periods/{period}', [VehicleController::class, 'destroyBlockedPeriod'])->name('vehicle-blocked-periods.destroy');
        Route::resource('categories', VehicleCategoryController::class)->except(['show'])->names('categories');
    });

    // Chauffeurs (gestionnaire).
    Route::middleware('can:drivers.manage')->group(function (): void {
        Route::resource('drivers', DriverController::class)->except(['show']);
    });

    // Contenus : FAQ, témoignages, messages de contact (gestionnaire).
    Route::middleware('can:content.manage')->group(function (): void {
        Route::resource('faqs', FaqController::class)->except(['show']);
        Route::resource('testimonials', TestimonialController::class)->except(['show']);
        Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
        Route::post('messages/{message}/toggle-read', [ContactMessageController::class, 'toggleRead'])->name('messages.toggle-read');
        Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');
    });

    // Comptes : caissières (gestionnaire) puis tous les comptes (super admin).
    Route::get('users', [UserController::class, 'index'])->middleware('can:users.manage_cashiers')->name('users.index');
    Route::get('users/create', [UserController::class, 'create'])->middleware('can:users.manage_cashiers')->name('users.create');
    Route::post('users', [UserController::class, 'store'])->middleware('can:users.manage_cashiers')->name('users.store');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->middleware('can:users.manage_cashiers')->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->middleware('can:users.manage_cashiers')->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('can:users.manage_cashiers')->name('users.destroy');

    // Rapports et exports (gestionnaire).
    Route::get('reports', [ReportController::class, 'index'])->middleware('can:reports.view')->name('reports.index');
    Route::get('reports/export/csv', [ReportController::class, 'exportCsv'])->middleware('can:reports.view')->name('reports.export-csv');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->middleware('can:reports.view')->name('reports.export-pdf');

    // Identité, apparence, tarifs et paramètres : Super Admin uniquement.
    Route::middleware('can:settings.manage')->group(function (): void {
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::resource('pages', PageController::class)->except(['show']);
    });

    // Journal d'activité : Super Admin uniquement.
    Route::get('audit', [AuditLogController::class, 'index'])->middleware('can:audit.view')->name('audit.index');
});
