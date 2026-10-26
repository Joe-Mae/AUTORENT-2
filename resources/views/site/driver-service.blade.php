@extends('layouts.public')

@section('title', __('site.driver_service.title').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="fw-bold">{{ __('site.driver_service.title') }}</h1>
                <p class="text-muted">{{ __('site.driver_service.subtitle') }}</p>
            </div>

            {{-- Les deux formules, tarifs pilotés par les settings --}}
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body p-4">
                            <span class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-4 mb-3" style="width: 64px; height: 64px;">
                                <i class="bi bi-person-badge fs-3"></i>
                            </span>
                            <h2 class="h4 fw-bold">{{ __('site.driver_service.car_with_driver') }}</h2>
                            <p class="text-muted">{{ __('site.driver_service.car_with_driver_text') }}</p>
                            <p class="h5 fw-bold text-primary">
                                + {{ format_price((float) setting('driver_with_car_price', '7000')) }}{{ __('site.common.per_day') }}
                            </p>
                            <p class="small text-muted">{{ __('site.driver_service.per_day_supplement', ['price' => format_price((float) setting('driver_with_car_price', '7000'))]) }}</p>
                            <a href="{{ route('vehicles.index', [app()->getLocale(), 'with_driver' => 1]) }}" class="btn btn-primary">{{ __('site.driver_service.choose_vehicle') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body p-4">
                            <span class="d-inline-flex align-items-center justify-content-center bg-accent-subtle text-accent rounded-4 mb-3" style="width: 64px; height: 64px;">
                                <i class="bi bi-key-fill fs-3"></i>
                            </span>
                            <h2 class="h4 fw-bold">{{ __('site.driver_service.driver_only') }}</h2>
                            <p class="text-muted">{{ __('site.driver_service.driver_only_text') }}</p>
                            <p class="h5 fw-bold text-primary">{{ format_price((float) setting('driver_only_price', '15000')) }}{{ __('site.common.per_day') }}</p>
                            <p class="small text-success">
                                <i class="bi bi-percent me-1"></i>{{ __('site.driver_service.discount', [
                                    'price' => format_price((float) setting('driver_only_price_reduced', '13000')),
                                    'days' => setting('driver_only_threshold_days', '3'),
                                ]) }}
                            </p>
                            <a href="{{ route('contact', app()->getLocale()) }}" class="btn btn-accent">{{ __('site.driver_service.ask_driver') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Exemples d'usage --}}
            <h2 class="h4 fw-bold text-center mb-4">{{ __('site.driver_service.use_cases_title') }}</h2>
            <div class="row g-4 mb-5">
                @foreach ([
                    ['icon' => 'heart-fill', 'title' => __('site.driver_service.use_cases.wedding'), 'text' => __('site.driver_service.use_cases.wedding_text')],
                    ['icon' => 'stars', 'title' => __('site.driver_service.use_cases.event'), 'text' => __('site.driver_service.use_cases.event_text')],
                    ['icon' => 'briefcase', 'title' => __('site.driver_service.use_cases.business'), 'text' => __('site.driver_service.use_cases.business_text')],
                    ['icon' => 'cart', 'title' => __('site.driver_service.use_cases.errands'), 'text' => __('site.driver_service.use_cases.errands_text')],
                ] as $useCase)
                    <div class="col-6 col-lg-3">
                        <div class="border rounded-4 p-3 h-100 bg-surface">
                            <i class="bi bi-{{ $useCase['icon'] }} fs-3 text-primary d-block mb-2"></i>
                            <h3 class="h6 fw-bold">{{ $useCase['title'] }}</h3>
                            <p class="small text-muted mb-0">{{ $useCase['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-secondary text-white rounded-4 p-4 p-md-5 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <p class="mb-0 lead">{{ __('site.driver_service.cta_text') }}</p>
                <a href="{{ route('contact', app()->getLocale()) }}" class="btn btn-light btn-lg flex-shrink-0 text-primary fw-semibold">{{ __('site.nav.contact') }}</a>
            </div>
        </div>
    </section>
@endsection
