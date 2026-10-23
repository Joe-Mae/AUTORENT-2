<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Pages statiques du site public. Les contenus éditoriaux viennent
 * des settings ; les pages alimentées par des modèles (véhicules,
 * FAQ…) basculeront sur leurs contrôleurs dédiés dans les phases 4+.
 */
class PageController extends Controller
{
    public function driverService(): View
    {
        return view('site.driver-service');
    }

    public function checkBooking(): View
    {
        return view('site.check-booking');
    }

    public function faq(): View
    {
        return view('site.faq');
    }

    public function contact(): View
    {
        return view('site.contact');
    }
}
