<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\View\View;

/**
 * Réservation en ligne : le parcours complet arrive en phase 5
 * (upload de pièce d'identité, calcul du prix, code unique).
 * Cette page garde le bouton « Réserver » fonctionnel entre-temps.
 */
class BookingController extends Controller
{
    public function create(): View
    {
        return view('site.booking.create', [
            'vehicle' => Vehicle::available()->with(['category', 'primaryImage'])->findOrFail(request()->integer('vehicle')),
        ]);
    }
}
