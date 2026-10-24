<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

/**
 * Pages de contenu éditable : conditions de location, confidentialité,
 * mentions légales. Le HTML saisi est de la responsabilité du Super
 * Admin (compte de confiance), rendu tel quel côté site public.
 */
class PageController extends Controller
{
    public function index()
    {
        return view('admin.pages.index', [
            'pages' => Page::orderBy('slug')->get(),
        ]);
    }

    public function edit(Page $page)
    {
        return view('admin.pages.form', ['page' => $page]);
    }

    public function create()
    {
        return view('admin.pages.form', ['page' => new Page]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:120', 'alpha_dash', 'unique:pages,slug'],
            'title' => ['required', 'string', 'max:190'],
            'content' => ['nullable', 'string'],
        ]);

        Page::create($validated);

        return redirect()->route('admin.pages.index')->with('status', __('site.admin.pages.created'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'content' => ['nullable', 'string'],
        ]);

        $page->update($validated);

        return redirect()->route('admin.pages.index')->with('status', __('site.admin.pages.updated'));
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return back()->with('status', __('site.admin.pages.deleted'));
    }
}
