<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Liste publique des véhicules du garage. Phase 1 : structure de la
 * page ; les filtres et la pagination arrivent avec la phase 4.
 */
class VehicleController extends Controller
{
    public function index(): View
    {
        return view('site.vehicles.index');
    }
}
