<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReservationController;
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
    });
});
