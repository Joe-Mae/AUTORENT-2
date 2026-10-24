@extends('layouts.public')

@section('title', __('site.booking.success_title').' '.$reservation->code)

@section('content')
    <section class="py-5">
        <div class="container text-center" style="max-width: 640px;">
            <span class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-3" style="width: 72px; height: 72px;">
                <i class="bi bi-check-lg fs-1"></i>
            </span>
            <h1 class="h3 fw-bold">{{ __('site.booking.success_title') }}</h1>
            <p class="text-muted">{{ __('site.booking.success_text') }}</p>

            <div class="card border-0 shadow-sm my-4">
                <div class="card-body p-4">
                    <p class="text-muted small mb-1">{{ __('site.booking.your_code') }}</p>
                    <p class="display-6 fw-bold text-primary">{{ $reservation->code }}</p>
                    <hr>
                    <div class="row text-start small">
                        <div class="col-6 col-md-3"><strong>{{ __('site.booking.type_label') }}</strong><br>{{ $reservation->type->label() }}</div>
                        <div class="col-6 col-md-3"><strong>{{ __('site.nav.vehicles') }}</strong><br>{{ $reservation->vehicle?->fullName() ?? '—' }}</div>
                        <div class="col-6 col-md-3"><strong>{{ __('site.home.search.pickup_date') }}</strong><br>{{ $reservation->starts_at->format('d/m/Y H:i') }}</div>
                        <div class="col-6 col-md-3"><strong>{{ __('site.home.search.return_date') }}</strong><br>{{ $reservation->ends_at->format('d/m/Y H:i') }}</div>
                        <div class="col-6 col-md-3 mt-2"><strong>{{ __('site.booking.summary_days') }}</strong><br>{{ $reservation->days_count }}</div>
                        <div class="col-6 col-md-3 mt-2"><strong>{{ __('site.booking.summary_total') }}</strong><br>{{ format_price((float) $reservation->total_price) }}</div>
                    </div>
                </div>
            </div>

            <p class="small text-muted">{{ __('site.booking.success_email_sent') }}</p>

            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route('check-booking', app()->getLocale()) }}" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>{{ __('site.nav.check_booking') }}
                </a>
                <a href="{{ route('home', app()->getLocale()) }}" class="btn btn-outline-secondary">{{ __('site.common.back_home') }}</a>
            </div>
        </div>
    </section>
@endsection
