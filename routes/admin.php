<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
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
});
