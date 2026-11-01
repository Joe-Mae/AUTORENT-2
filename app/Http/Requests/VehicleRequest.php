<?php

namespace App\Http\Requests;

use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Enums\VehicleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'vehicle_category_id' => ['required', 'exists:vehicle_categories,id'],
            'brand' => ['required', 'string', 'max:60'],
            'model' => ['required', 'string', 'max:60'],
            'year' => ['required', 'integer', 'min:1990', 'max:'.(int) date('Y') + 1],
            'registration_plate' => ['required', 'string', 'max:20', Rule::unique('vehicles')->ignore($this->route('vehicle'))],
            'seats' => ['required', 'integer', 'min:1', 'max:9'],
            'transmission' => ['required', Rule::enum(TransmissionType::class)],
            'fuel' => ['required', Rule::enum(FuelType::class)],
            'mileage_km' => ['required', 'integer', 'min:0'],
            'daily_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(VehicleStatus::class)],
            'description' => ['nullable', 'string', 'max:2000'],
            'video' => [
                'nullable',
                'file',
                'mimes:mp4,webm',
                'max:'.((int) setting('upload_max_size_mb', '5') * 1024),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'video.mimes' => __('site.admin.vehicles.video_invalid'),
            'video.max' => __('site.admin.vehicles.video_max', ['size' => setting('upload_max_size_mb', '5')]),
            'video.uploaded' => __('site.admin.vehicles.video_invalid'),
        ];
    }

    /**
     * Données prêtes pour le modèle.
     *
     * @return array<string, mixed>
     */
    public function vehicleData(): array
    {
        return $this->only([
            'vehicle_category_id', 'brand', 'model', 'year', 'registration_plate',
            'seats', 'transmission', 'fuel', 'mileage_km', 'daily_price', 'status', 'description',
        ]);
    }
}
