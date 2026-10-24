<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\Vehicle;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('site.home', [
            'featuredVehicles' => Vehicle::available()
                ->with(['primaryImage', 'category'])
                ->orderByDesc('year')
                ->take(4)
                ->get(),
            'testimonials' => Testimonial::visible()->take(10)->get(),
        ]);
    }
}
