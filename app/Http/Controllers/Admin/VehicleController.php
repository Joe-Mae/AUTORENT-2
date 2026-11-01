<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use App\Models\VehicleBlockedPeriod;
use App\Models\VehicleCategory;
use App\Models\VehicleImage;
use App\Services\UploadSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $filters = array_filter($request->only(['status', 'category']), fn ($value) => $value !== null && $value !== '');

        $vehicles = Vehicle::with(['category', 'primaryImage'])
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['category'] ?? null, fn ($query, $category) => $query->where('vehicle_category_id', $category))
            ->orderBy('brand')
            ->paginate(12)
            ->withQueryString();

        return view('admin.vehicles.index', [
            'vehicles' => $vehicles,
            'filters' => $filters,
            'categories' => VehicleCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.vehicles.form', [
            'vehicle' => new Vehicle(['status' => 'available', 'seats' => 5, 'transmission' => 'manual', 'fuel' => 'petrol']),
            'categories' => VehicleCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function store(VehicleRequest $request)
    {
        $vehicle = Vehicle::create($request->vehicleData());
        $this->storeImages($request, $vehicle);
        $this->storeVideo($request, $vehicle);

        return redirect()->route('admin.vehicles.index')->with('status', __('site.admin.vehicles.created'));
    }

    public function edit(Vehicle $vehicle): View
    {
        $vehicle->load(['images', 'blockedPeriods']);

        return view('admin.vehicles.form', [
            'vehicle' => $vehicle,
            'categories' => VehicleCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->vehicleData());
        $this->storeImages($request, $vehicle);
        $this->storeVideo($request, $vehicle);

        // Blocage de périodes (maintenance) depuis la fiche.
        if ($request->filled(['blocked_from', 'blocked_to'])) {
            $request->validate([
                'blocked_from' => ['date'],
                'blocked_to' => ['date', 'after:blocked_from'],
                'blocked_reason' => ['nullable', 'string', 'max:190'],
            ]);

            $vehicle->blockedPeriods()->create([
                'starts_at' => $request->input('blocked_from'),
                'ends_at' => $request->input('blocked_to'),
                'reason' => $request->input('blocked_reason'),
            ]);
        }

        return back()->with('status', __('site.admin.vehicles.updated'));
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')->with('status', __('site.admin.vehicles.deleted'));
    }

    public function destroyImage(VehicleImage $image)
    {
        $image->delete();

        return back()->with('status', __('site.admin.vehicles.image_deleted'));
    }

    public function makePrimary(VehicleImage $image)
    {
        VehicleImage::where('vehicle_id', $image->vehicle_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('status', __('site.admin.vehicles.image_primary'));
    }

    public function destroyBlockedPeriod(VehicleBlockedPeriod $period)
    {
        $period->delete();

        return back()->with('status', __('site.admin.vehicles.blocked_period_removed'));
    }

    /**
     * Enregistre les photos ajoutées sur le disque public ; la première
     * devient la photo principale si le véhicule n'en a pas.
     */
    private function storeImages(Request $request, Vehicle $vehicle): void
    {
        if (! $request->hasFile('photos')) {
            return;
        }

        $request->validate(['photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096']]);

        $hasPrimary = $vehicle->images()->where('is_primary', true)->exists();

        $sanitizer = app(UploadSanitizer::class);

        foreach ($request->file('photos') as $index => $file) {
            VehicleImage::create([
                'vehicle_id' => $vehicle->id,
                'path' => $sanitizer->store($file, 'vehicles', 'public'),
                'is_primary' => ! $hasPrimary && $index === 0,
            ]);
        }
    }

    /**
     * Vidéo de présentation : une seule par véhicule, remplacée à chaque
     * nouvel upload (l'ancien fichier est supprimé du disque public).
     */
    private function storeVideo(Request $request, Vehicle $vehicle): void
    {
        if (! $request->hasFile('video')) {
            return;
        }

        if ($vehicle->video_path) {
            Storage::disk('public')->delete($vehicle->video_path);
        }

        $vehicle->update([
            'video_path' => $request->file('video')->store('vehicles/videos', 'public'),
        ]);
    }
}
