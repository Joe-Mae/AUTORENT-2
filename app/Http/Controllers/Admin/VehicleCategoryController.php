<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class VehicleCategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => VehicleCategory::withCount('vehicles')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:vehicle_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        VehicleCategory::create($validated);

        return back()->with('status', __('site.admin.categories.created'));
    }

    public function edit(VehicleCategory $category)
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, VehicleCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:vehicle_categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('status', __('site.admin.categories.updated'));
    }

    public function destroy(VehicleCategory $category)
    {
        try {
            $category->delete();
        } catch (QueryException) {
            // Des véhicules dépendent de cette catégorie (FK restrict).
            return back()->with('error', __('site.admin.categories.has_vehicles'));
        }

        return back()->with('status', __('site.admin.categories.deleted'));
    }
}
