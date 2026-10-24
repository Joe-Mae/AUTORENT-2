<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Consultation d'une réservation : code + email ou téléphone.
 * Limité en fréquence pour empêcher l'énumération des codes (§8).
 */
class BookingLookupRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:30'],
            'contact' => ['required', 'string', 'max:190'],
        ];
    }
}
