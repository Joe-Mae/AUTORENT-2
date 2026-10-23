<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Back-office
|--------------------------------------------------------------------------
| Préfixé « admin » et nommé « admin.* » (déclaré dans bootstrap/app.php).
| Le middleware d'authentification et les permissions par rôle sont
| ajoutés en phase 3.
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
