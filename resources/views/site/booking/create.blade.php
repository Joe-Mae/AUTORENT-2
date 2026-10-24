@extends('layouts.public')

@section('title', __('site.booking.title').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 720px;">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 fw-bold mb-2">{{ __('site.booking.title') }}</h1>

                    <div class="d-flex align-items-center gap-3 border rounded-4 p-3 mb-4 bg-surface">
                        <img src="{{ $vehicle->primaryImage?->url() ?? asset('img/hero-default.svg') }}" alt="{{ $vehicle->fullName() }}" class="rounded-3 object-fit-cover" style="width: 96px; height: 72px;">
                        <div>
                            <p class="fw-bold mb-0">{{ $vehicle->fullName() }} · {{ $vehicle->year }}</p>
                            <p class="text-primary fw-semibold mb-0">{{ format_price($vehicle->daily_price) }}{{ __('site.common.per_day') }}</p>
                        </div>
                    </div>

                    <div class="alert alert-primary d-flex gap-3" role="status">
                        <i class="bi bi-hourglass-split fs-4"></i>
                        <div>{{ __('site.booking.coming_soon') }}</div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('vehicles.show', [app()->getLocale(), $vehicle]) }}" class="btn btn-outline-secondary">{{ __('site.booking.back_to_vehicle') }}</a>
                        <a href="tel:{{ preg_replace('/\s+/', '', setting('contact_phone')) }}" class="btn btn-primary"><i class="bi bi-telephone me-1"></i>{{ __('site.common.call') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
