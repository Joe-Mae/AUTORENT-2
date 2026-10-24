<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Page;
use Illuminate\View\View;

/**
 * Pages du site public alimentées par les contenus (FAQ, pages légales).
 * Les formules « chauffeur » affichent les tarifs des settings.
 */
class PageController extends Controller
{
    public function driverService(): View
    {
        return view('site.driver-service');
    }

    public function faq(): View
    {
        return view('site.faq', [
            'faqs' => Faq::active()->get(),
        ]);
    }

    public function contact(): View
    {
        return view('site.contact');
    }

    public function page(Page $page): View
    {
        return view('site.page', ['page' => $page]);
    }
}
