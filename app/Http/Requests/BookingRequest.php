<?php

namespace App\Http\Requests;

use App\Enums\ReservationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Demande de réservation depuis le site public. Aucun compte requis :
 * tout est vérifié ici, y compris la pièce d'identité obligatoire.
 */
class BookingRequest extends FormRequest
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
        $maxKb = ((int) setting('upload_max_size_mb', '5')) * 1024;
        $identityRules = ['file', 'mimes:jpg,jpeg,png,pdf', 'max:'.$maxKb];

        return [
            'type' => ['required', Rule::enum(ReservationType::class)],
            'vehicle_id' => ['required_unless:type,driver_only', 'integer', 'exists:vehicles,id'],

            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after:pickup_date'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'pickup_location' => ['nullable', 'string', 'max:190'],

            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:190'],
            'birth_date' => ['required', 'date', 'before:today'],
            'id_card_number' => ['required', 'string', 'max:60'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],

            // Pièce d'identité obligatoire (spécification §5.1), verso facultatif.
            'identity_front' => array_merge(['required'], $identityRules),
            'identity_back' => array_merge(['nullable'], $identityRules),

            // Champs service chauffeur
            'event_reason' => ['nullable', 'string', 'max:120'],
            'itinerary' => ['nullable', 'string', 'max:2000'],
            'passenger_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'client_vehicle_brand' => ['required_if:type,driver_only', 'nullable', 'string', 'max:120'],
            'client_vehicle_plate' => ['required_if:type,driver_only', 'nullable', 'string', 'max:30'],

            // Consentements obligatoires
            'terms' => ['accepted'],
            'identity_consent' => ['accepted'],

            // Honeypot anti-spam : champ invisible, doit rester vide (§8).
            'website' => ['prohibited'],
        ];
    }

    /**
     * La pièce d'identité ne peut pas contourner la validation client :
     * on renomme côté serveur et on stocke sur le disque privé.
     */
    public function messages(): array
    {
        return [
            'identity_front.required' => __('site.booking.errors.identity_required'),
            'identity_front.mimes' => __('site.booking.errors.identity_mimes'),
            'identity_front.max' => __('site.booking.errors.identity_max', ['size' => setting('upload_max_size_mb', '5')]),
            'terms.accepted' => __('site.booking.errors.terms'),
            'identity_consent.accepted' => __('site.booking.errors.consent'),
        ];
    }
}
