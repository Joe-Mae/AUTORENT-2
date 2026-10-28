@extends('layouts.admin')

@section('title', __('site.admin.payments.title'))
@section('page_title', __('site.admin.payments.title'))

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <p class="text-muted small mb-1">{{ __('site.admin.payments.day_total') }}</p>
                <p class="h3 fw-bold mb-0 text-primary">{{ format_price((float) $dayTotal) }}</p>
                <p class="small text-muted mb-0">{{ $payments->count() }} {{ __('site.admin.payments.encaissements') }}</p>
            </div></div>
        </div>
        @foreach (\App\Enums\PaymentMethod::cases() as $method)
            <div class="col-6 col-lg-2">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <p class="text-muted small mb-1"><i class="bi bi-{{ $method->icon() }} me-1"></i>{{ $method->label() }}</p>
                    <p class="h5 fw-bold mb-0">{{ format_price((float) ($byMethod->get($method->value) ?? 0)) }}</p>
                </div></div>
            </div>
        @endforeach
        <div class="col-12 col-lg-3">
            <div class="card border-0 shadow-sm h-100"><div class="card-body">
                <form method="GET" class="d-flex gap-2 align-items-end">
                    <div class="flex-grow-1">
                        <label class="form-label small fw-semibold mb-0" for="date">{{ __('site.admin.payments.day') }}</label>
                        <input type="date" id="date" name="date" value="{{ $date->format('Y-m-d') }}" class="form-control form-control-sm">
                    </div>
                    <button class="btn btn-primary btn-sm"><i class="bi bi-arrow-clockwise"></i></button>
                </form>
            </div></div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Encaissements du jour --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-receipt me-1 text-primary"></i>{{ __('site.admin.payments.day_list') }}</h2>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>{{ __('site.check_booking.code') }}</th><th>{{ __('site.admin.payments.method') }}</th><th class="text-end">{{ __('site.admin.payments.amount') }}</th><th></th></tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.reservations.show', $payment->reservation) }}" class="text-decoration-none fw-semibold">{{ $payment->reservation->code }}</a>
                                            <small class="text-muted d-block">{{ $payment->receiver?->name }} · {{ $payment->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <small><i class="bi bi-{{ $payment->method->icon() }} me-1"></i>{{ $payment->method->label() }}</small>
                                            @if ($payment->reference)<small class="text-muted d-block">{{ $payment->reference }}</small>@endif
                                        </td>
                                        <td class="text-end fw-semibold">{{ format_price((float) $payment->amount) }}</td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('admin.payments.destroy', $payment) }}"
                                                  data-confirm="{{ __('site.admin.payments.delete_confirm') }}">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">{{ __('site.admin.payments.empty_day') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- À encaisser --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-hourglass-split me-1 text-warning"></i>{{ __('site.admin.payments.outstanding') }}</h2>
                    @forelse ($outstanding as $reservation)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <a href="{{ route('admin.reservations.show', $reservation) }}" class="fw-semibold text-decoration-none">{{ $reservation->code }}</a>
                                <small class="text-muted d-block">
                                    {{ $reservation->clientFullName() }} · {{ $reservation->starts_at->format('d/m') }}
                                    · {{ format_price((float) $reservation->total_price) }}
                                </small>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 fw-bold text-danger">{{ format_price($reservation->paymentBalance()) }}</p>
                                <x-payment-status :reservation="$reservation" />
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('site.admin.payments.no_outstanding') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
