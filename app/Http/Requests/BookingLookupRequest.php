<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Consultation d'une réservation : par code, par nom/prénom ou par
 * email/téléphone — au moins un des deux champs. Limité en fréquence
 * pour empêcher l'énumération des codes (§8).
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
            'code' => ['nullable', 'string', 'max:30'],
            'contact' => ['nullable', 'string', 'max:190'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (blank($this->input('code')) && blank($this->input('contact'))) {
                $validator->errors()->add('code', __('site.check_booking.criteria_required'));
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => is_string($this->input('code')) ? trim($this->input('code')) : $this->input('code'),
            'contact' => is_string($this->input('contact')) ? trim($this->input('contact')) : $this->input('contact'),
        ]);
    }
}
