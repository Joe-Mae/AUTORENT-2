@props(['status'])

@php($colors = [
    'pending' => 'text-bg-warning',
    'identity_verified' => 'text-bg-info',
    'confirmed' => 'text-bg-success',
    'ongoing' => 'text-bg-primary',
    'completed' => 'text-bg-secondary',
    'cancelled' => 'text-bg-danger',
    'rejected' => 'text-bg-danger',
])

<span @class(['badge', $colors[$status->value] ?? 'text-bg-secondary'])>{{ $status->label() }}</span>
