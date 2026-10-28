@props(['reservation'])

{{-- Utilise la somme pré-chargée (withSum) si présente, sinon la calcule. --}}
@php
    $paid = (float) ($reservation->getAttribute('paid_total') ?? $reservation->paidAmount());
    $balance = max(0, (float) $reservation->total_price - $paid);
    $fullyPaid = $paid > 0 && $balance <= 0.01;
@endphp
<span @class([
    'badge',
    'text-bg-danger' => $paid <= 0,
    'text-bg-success' => $fullyPaid,
    'text-bg-warning' => $paid > 0 && ! $fullyPaid,
])>
    @if ($paid <= 0)
        {{ __('site.admin.payments.unpaid') }}
    @elseif ($fullyPaid)
        {{ __('site.admin.payments.paid') }}
    @else
        {{ __('site.admin.payments.partial') }} · {{ format_price($balance) }}
    @endif
</span>
