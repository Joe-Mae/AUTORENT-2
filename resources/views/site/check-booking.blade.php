@extends('layouts.public')

@section('title', __('site.check_booking.title').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 720px;">
            <div class="text-center mb-4">
                <h1 class="fw-bold">{{ __('site.check_booking.title') }}</h1>
                <p class="text-muted">{{ __('site.check_booking.subtitle') }}</p>
            </div>

            @if (session('lookup_error'))
                <div class="alert alert-danger">{{ session('lookup_error') }}</div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('check-booking.lookup', app()->getLocale()) }}">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="code" class="form-label small fw-semibold">{{ __('site.check_booking.code') }}</label>
                                <input type="text" id="code" name="code" value="{{ old('code') }}" class="form-control" placeholder="PDJ-2026-000123" required>
                            </div>
                            <div class="col-md-5">
                                <label for="contact" class="form-label small fw-semibold">{{ __('site.check_booking.contact') }}</label>
                                <input type="text" id="contact" name="contact" value="{{ old('contact') }}" class="form-control" placeholder="jean@exemple.com" required>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @isset ($reservation)
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <h2 class="h5 fw-bold mb-0">{{ $reservation->code }}</h2>
                            <span class="badge fs-6 {{ match($reservation->status->value) {
                                'pending' => 'text-bg-warning',
                                'identity_verified' => 'text-bg-info',
                                'confirmed' => 'text-bg-success',
                                'ongoing' => 'text-bg-primary',
                                'completed' => 'text-bg-secondary',
                                'cancelled' => 'text-bg-danger',
                                'rejected' => 'text-bg-danger',
                                default => 'text-bg-secondary',
                            } }}">{{ $reservation->status->label() }}</span>
                        </div>

                        <div class="row small">
                            <div class="col-6 col-md-4 mb-2"><strong>{{ __('site.booking.type_label') }}</strong><br>{{ $reservation->type->label() }}</div>
                            <div class="col-6 col-md-4 mb-2"><strong>{{ __('site.nav.vehicles') }}</strong><br>{{ $reservation->vehicle?->fullName() ?? '—' }}</div>
                            <div class="col-6 col-md-4 mb-2"><strong>{{ __('site.booking.summary_days') }}</strong><br>{{ $reservation->days_count }}</div>
                            <div class="col-6 col-md-4 mb-2"><strong>{{ __('site.home.search.pickup_date') }}</strong><br>{{ $reservation->starts_at->format('d/m/Y H:i') }}</div>
                            <div class="col-6 col-md-4 mb-2"><strong>{{ __('site.home.search.return_date') }}</strong><br>{{ $reservation->ends_at->format('d/m/Y H:i') }}</div>
                            <div class="col-6 col-md-4 mb-2"><strong>{{ __('site.booking.summary_total') }}</strong><br>{{ format_price((float) $reservation->total_price) }}</div>
                        </div>

                        <a href="{{ route('check-booking.pdf', ['locale' => app()->getLocale(), 'code' => $reservation->code]) }}"
                           class="btn btn-outline-primary mt-2">
                            <i class="bi bi-file-earmark-pdf me-1"></i>{{ __('site.check_booking.download_pdf') }}
                        </a>
                    </div>
                </div>
            @endisset
        </div>
    </section>
@endsection
