<?php

namespace App\Http\Requests;

use App\Enums\FuelType;
use App\Enums\TransmissionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Filtres du garage : tous optionnels, transmis en query string.
 */
class VehicleFilterRequest extends FormRequest
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
            'category' => ['nullable', 'integer', 'exists:vehicle_categories,id'],
            'brand' => ['nullable', 'string', 'max:50'],
            'seats' => ['nullable', 'integer', 'min:1', 'max:9'],
            'transmission' => ['nullable', Rule::enum(TransmissionType::class)],
            'fuel' => ['nullable', Rule::enum(FuelType::class)],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'gte:price_min'],
            'pickup_date' => ['nullable', 'date', 'after_or_equal:today'],
            'return_date' => ['nullable', 'date', 'after:pickup_date'],
            'sort' => ['nullable', Rule::in(['price_asc', 'price_desc', 'year_desc', 'brand_asc'])],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return array_filter($this->only([
            'category', 'brand', 'seats', 'transmission', 'fuel',
            'price_min', 'price_max', 'pickup_date', 'return_date', 'sort',
        ]), fn ($value) => $value !== null && $value !== '');
    }
}
