<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        return view('admin.drivers.index', [
            'drivers' => Driver::withCount('reservations')->orderBy('full_name')->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'unique:drivers,phone'],
            'license_number' => ['required', 'string', 'max:60'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Driver::create($validated + ['is_active' => $request->boolean('is_active', true)]);

        return back()->with('status', __('site.admin.drivers.created'));
    }

    public function edit(Driver $driver)
    {
        return view('admin.drivers.edit', ['driver' => $driver]);
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'unique:drivers,phone,'.$driver->id],
            'license_number' => ['required', 'string', 'max:60'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $driver->update($validated + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.drivers.index')->with('status', __('site.admin.drivers.updated'));
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();

        return back()->with('status', __('site.admin.drivers.deleted'));
    }
}
