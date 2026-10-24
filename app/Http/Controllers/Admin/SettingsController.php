<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Atelier de personnalisation de la marque blanche : identité, apparence,
 * textes de l'accueil, diaporama, coordonnées, tarifs globaux et
 * paramètres système. Toutes les valeurs alimentent le site public sans
 * toucher au code.
 */
class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'slides' => json_decode((string) setting('hero_slides', '[]'), true) ?: [],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate($this->rules());

        $settings = app(Settings::class);
        $values = collect($validated)
            ->except(['logo', 'favicon', 'slides', 'hero_images'])
            ->map(fn ($value) => $value === '' ? null : $value)
            ->all();

        // Identité visuelle : logo et favicon sur le disque public.
        foreach (['logo', 'favicon'] as $brandFile) {
            if ($request->hasFile($brandFile)) {
                $old = setting($brandFile);
                if ($old) {
                    Storage::disk('public')->delete($old);
                }
                $values[$brandFile] = $request->file($brandFile)->store('branding', 'public');
            }
        }

        // Diaporama : reconstruction du JSON à partir des lignes du
        // formulaire, avec remplacement optionnel des images.
        if ($request->has('slides')) {
            $values['hero_slides'] = $this->buildSlides($request);
        }

        $settings->setMany($values);

        return back()->with('status', __('site.admin.settings.saved'));
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $hex = ['regex:/^#[0-9a-fA-F]{6}$/'];

        return [
            'company_name' => ['sometimes', 'required', 'string', 'max:120'],
            'company_legal_name' => ['nullable', 'string', 'max:190'],
            'tagline' => ['nullable', 'string', 'max:190'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'mimes:png,ico', 'max:512'],
            'color_primary' => array_merge(['sometimes'], $hex),
            'color_secondary' => array_merge(['sometimes'], $hex),
            'color_accent' => array_merge(['sometimes'], $hex),

            'hero_title' => ['nullable', 'string', 'max:190'],
            'hero_subtitle' => ['nullable', 'string', 'max:400'],
            'home_services_text' => ['nullable', 'string', 'max:300'],
            'cta_title' => ['nullable', 'string', 'max:190'],
            'cta_text' => ['nullable', 'string', 'max:300'],
            'stats_vehicles' => ['nullable', 'integer', 'min:0'],
            'stats_clients' => ['nullable', 'integer', 'min:0'],
            'stats_years' => ['nullable', 'integer', 'min:0'],
            'stats_satisfaction' => ['nullable', 'integer', 'min:0', 'max:100'],

            'contact_address' => ['nullable', 'string', 'max:190'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_phone_2' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:190'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30'],
            'contact_hours' => ['nullable', 'string', 'max:190'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_x' => ['nullable', 'url', 'max:255'],
            'map_embed_url' => ['nullable', 'string', 'max:1000'],

            'currency' => ['sometimes', 'required', 'string', 'max:8'],
            'driver_with_car_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'driver_with_car_mode' => ['sometimes', 'required', 'in:supplement,package'],
            'driver_only_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'driver_only_price_reduced' => ['sometimes', 'required', 'numeric', 'min:0'],
            'driver_only_threshold_days' => ['sometimes', 'required', 'integer', 'min:1'],
            'day_count_mode' => ['sometimes', 'required', 'in:started_periods,calendar'],
            'reservation_code_prefix' => ['sometimes', 'required', 'string', 'max:8', 'alpha_dash'],
            'pending_blocks_vehicle' => ['sometimes', 'required', 'in:0,1'],
            'upload_max_size_mb' => ['sometimes', 'required', 'integer', 'min:1', 'max:20'],
            'identity_retention_months' => ['sometimes', 'required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return string JSON des diapositives
     */
    private function buildSlides(Request $request): string
    {
        $slides = [];

        foreach ($request->input('slides', []) as $index => $slide) {
            $image = $slide['existing_image'] ?? null;

            if ($request->hasFile("hero_images.{$index}")) {
                $image = $request->file("hero_images.{$index}")->store('hero', 'public');
            }

            if (empty($slide['title']) && empty($slide['text']) && empty($image)) {
                continue;
            }

            $slides[] = [
                'image' => $image,
                'title' => $slide['title'] ?? null,
                'text' => $slide['text'] ?? null,
            ];
        }

        return json_encode($slides, JSON_UNESCAPED_UNICODE) ?: '[]';
    }
}
