<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Accueil du back-office. L'authentification et les rôles arrivent
 * en phase 3 ; la page sert pour l'instant de socle visuel.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard');
    }
}
