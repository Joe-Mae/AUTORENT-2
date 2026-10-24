<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return view('admin.faqs.index', [
            'faqs' => Faq::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:3000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Faq::create($validated + ['is_active' => true]);

        return back()->with('status', __('site.admin.faqs.created'));
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', ['faq' => $faq]);
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:3000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $faq->update($validated + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.faqs.index')->with('status', __('site.admin.faqs.updated'));
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with('status', __('site.admin.faqs.deleted'));
    }
}
