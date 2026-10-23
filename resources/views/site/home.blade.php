@extends('layouts.public')

@section('title', setting('company_name').' · '.setting('tagline'))

@section('content')
    {{-- Hero : textes et visuel pilotés par les settings du Super Admin --}}
    <section class="hero text-white" style="background-image: linear-gradient(rgba(20, 33, 61, 0.75), rgba(20, 33, 61, 0.75)), url('{{ setting('hero_image') ? asset('storage/'.setting('hero_image')) : asset('img/hero-default.svg') }}');">
        <div class="container py-5">
            <div class="row justify-content-center text-center py-5">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">{{ setting('hero_title') }}</h1>
                    <p class="lead text-white-50 mb-4">{{ setting('hero_subtitle') }}</p>

                    {{-- Recherche rapide : renvoie vers le garage avec les dates en query string (exploitées en phase 4) --}}
                    <form action="{{ route('vehicles.index', app()->getLocale()) }}" method="GET" class="bg-white text-body rounded-3 shadow p-3 p-md-4 text-start">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="pickup_date" class="form-label small fw-semibold">{{ __('site.home.search.pickup_date') }}</label>
                                <input type="date" id="pickup_date" name="pickup_date" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="return_date" class="form-label small fw-semibold">{{ __('site.home.search.return_date') }}</label>
                                <input type="date" id="return_date" name="return_date" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label for="with_driver" class="form-label small fw-semibold">{{ __('site.home.search.formula') }}</label>
                                <select id="with_driver" name="with_driver" class="form-select">
                                    <option value="0">{{ __('site.home.search.simple') }}</option>
                                    <option value="1">{{ __('site.home.search.with_driver') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-grid">
                                <button type="submit" class="btn btn-primary">{{ __('site.home.search.submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Services --}}
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-2">{{ __('site.home.services.title') }}</h2>
            <p class="text-center text-muted mb-5">{{ setting('home_services_text') }}</p>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-car-front fs-1 text-primary"></i>
                            <h3 class="h5 fw-bold mt-3">{{ __('site.home.services.simple_title') }}</h3>
                            <p class="text-muted">{{ __('site.home.services.simple_text') }}</p>
                            <a href="{{ route('vehicles.index', app()->getLocale()) }}" class="btn btn-outline-primary btn-sm">{{ __('site.common.view_more') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-person-badge fs-1 text-primary"></i>
                            <h3 class="h5 fw-bold mt-3">{{ __('site.home.services.with_driver_title') }}</h3>
                            <p class="text-muted">{{ __('site.home.services.with_driver_text') }}</p>
                            <a href="{{ route('driver-service', app()->getLocale()) }}" class="btn btn-outline-primary btn-sm">{{ __('site.common.view_more') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <i class="bi bi-steering-wheel fs-1 text-primary"></i>
                            <h3 class="h5 fw-bold mt-3">{{ __('site.home.services.driver_only_title') }}</h3>
                            <p class="text-muted">{{ __('site.home.services.driver_only_text') }}</p>
                            <a href="{{ route('driver-service', app()->getLocale()) }}" class="btn btn-outline-primary btn-sm">{{ __('site.common.view_more') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Étapes de réservation --}}
    <section class="py-5 bg-surface">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">{{ __('site.home.steps.title') }}</h2>
            <div class="row g-4 text-center">
                <div class="col-6 col-lg-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 56px; height: 56px;"><i class="bi bi-calendar3 fs-4"></i></span>
                    <h3 class="h6 fw-bold">{{ __('site.home.steps.choose_dates') }}</h3>
                </div>
                <div class="col-6 col-lg-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 56px; height: 56px;"><i class="bi bi-car-front fs-4"></i></span>
                    <h3 class="h6 fw-bold">{{ __('site.home.steps.select_vehicle') }}</h3>
                </div>
                <div class="col-6 col-lg-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 56px; height: 56px;"><i class="bi bi-person-vcard fs-4"></i></span>
                    <h3 class="h6 fw-bold">{{ __('site.home.steps.send_id') }}</h3>
                </div>
                <div class="col-6 col-lg-3">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3" style="width: 56px; height: 56px;"><i class="bi bi-patch-check fs-4"></i></span>
                    <h3 class="h6 fw-bold">{{ __('site.home.steps.confirmation') }}</h3>
                </div>
            </div>
        </div>
    </section>

    {{-- Appel à l'action --}}
    <section class="py-5">
        <div class="container">
            <div class="bg-secondary text-white rounded-3 p-4 p-md-5 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <h2 class="h4 fw-bold mb-1">{{ setting('cta_title', __('site.home.cta.default_title')) }}</h2>
                    <p class="mb-0 text-white-50">{{ setting('cta_text', __('site.home.cta.default_text')) }}</p>
                </div>
                <a href="{{ route('vehicles.index', app()->getLocale()) }}" class="btn btn-primary btn-lg flex-shrink-0">{{ __('site.home.cta.button') }}</a>
            </div>
        </div>
    </section>
@endsection
