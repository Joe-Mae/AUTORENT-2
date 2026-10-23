@php
    /**
     * Diapositives du carrousel : réglage « hero_slides » (JSON) géré par le
     * Super Admin. Repli sur une diapo unique avec les textes de base si le
     * réglage est absent ou invalide.
     *
     * @var array<int, array{image: ?string, title: ?string, text: ?string}> $slides
     */
    $slides = json_decode((string) setting('hero_slides'), true);
    if (! is_array($slides) || $slides === []) {
        $slides = [['image' => null, 'title' => setting('hero_title'), 'text' => setting('hero_subtitle')]];
    }

    $imageUrl = fn (?string $path): ?string => match (true) {
        empty($path) => null,
        str_starts_with($path, 'http') => $path,
        default => asset($path),
    };

    $stats = [
        ['value' => setting('stats_vehicles', '25'), 'label' => __('site.home.stats.vehicles'), 'icon' => 'car-front'],
        ['value' => setting('stats_clients', '1200'), 'label' => __('site.home.stats.clients'), 'icon' => 'people'],
        ['value' => setting('stats_years', '8'), 'label' => __('site.home.stats.years'), 'icon' => 'award'],
        ['value' => setting('stats_satisfaction', '98'), 'label' => __('site.home.stats.satisfaction'), 'icon' => 'heart'],
    ];

    $advantages = [
        ['icon' => 'shield-check', 'title' => __('site.home.advantages.insurance.0'), 'text' => __('site.home.advantages.insurance.1')],
        ['icon' => 'patch-check', 'title' => __('site.home.advantages.recent.0'), 'text' => __('site.home.advantages.recent.1')],
        ['icon' => 'headset', 'title' => __('site.home.advantages.assistance.0'), 'text' => __('site.home.advantages.assistance.1')],
        ['icon' => 'tags', 'title' => __('site.home.advantages.pricing.0'), 'text' => __('site.home.advantages.pricing.1')],
    ];

    $steps = [
        ['icon' => 'calendar3', 'label' => __('site.home.steps.choose_dates')],
        ['icon' => 'car-front', 'label' => __('site.home.steps.select_vehicle')],
        ['icon' => 'person-vcard', 'label' => __('site.home.steps.send_id')],
        ['icon' => 'patch-check', 'label' => __('site.home.steps.confirmation')],
    ];

    $testimonials = [
        ['quote' => 'Voiture impeccable et disponible au bon moment. Le chauffeur pour notre mariage a été très professionnel.', 'author' => 'Aïcha D.', 'role' => __('Mariage à Cotonou')],
        ['quote' => 'Réservation simple sans créer de compte, confirmation rapide. Je recommande pour les déplacements professionnels.', 'author' => 'Kossi A.', 'role' => __('Déplacement professionnel')],
        ['quote' => 'Tarifs clairs et véhicule propre. L\'équipe de l\'agence explique tout calmement, rien à redire.', 'author' => 'Fatou B.', 'role' => __('Location week-end')],
    ];
@endphp

@extends('layouts.public')

@section('title', setting('company_name').' · '.setting('tagline'))

@section('content')
    {{-- Diaporama d'accueil : défilement automatique, zoom lent (Ken Burns), légendes animées --}}
    <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            @foreach ($slides as $index => $slide)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}" @class(['active' => $index === 0]) aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="{{ __('site.common.slide') }} {{ $index + 1 }}"></button>
            @endforeach
        </div>

        <div class="carousel-inner">
            @foreach ($slides as $index => $slide)
                <div @class(['carousel-item', 'active' => $index === 0])>
                    @if ($url = $imageUrl($slide['image'] ?? null))
                        <img src="{{ $url }}" alt="{{ $slide['title'] ?? setting('company_name') }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" @if ($index === 0) fetchpriority="high" @endif>
                    @endif
                    <div class="hero-shade"></div>

                    <div class="hero-caption">
                        <div class="container">
                            <div class="col-lg-7">
                                @if ($index === 0)
                                    <span class="badge rounded-pill bg-primary mb-3 px-3 py-2 text-uppercase fw-semibold">{{ setting('tagline') }}</span>
                                @endif
                                <h1 @class(['display-6' => $index === 0, 'h1' => $index > 0, 'fw-bold' => true, 'text-white' => true, 'mb-3' => true])>{{ $slide['title'] ?? setting('hero_title') }}</h1>
                                <p class="lead text-white-50 mb-4">{{ $slide['text'] ?? setting('hero_subtitle') }}</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('vehicles.index', app()->getLocale()) }}" class="btn btn-primary btn-lg px-4">{{ __('site.home.cta.button') }}</a>
                                    <a href="{{ route('driver-service', app()->getLocale()) }}" class="btn btn-outline-light btn-lg px-4">{{ __('site.common.view_more') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('site.common.previous') }}</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">{{ __('site.common.next') }}</span>
        </button>
    </div>

    {{-- Recherche rapide, chevauchant le bas du diaporama --}}
    <div class="container search-card">
        <form action="{{ route('vehicles.index', app()->getLocale()) }}" method="GET" class="bg-white rounded-4 shadow-lg p-3 p-md-4">
            <p class="fw-bold text-secondary mb-3">{{ __('site.home.search.title') }}</p>
            <div class="row g-3 align-items-end">
                <div class="col-md-3 col-6">
                    <label for="pickup_date" class="form-label small fw-semibold">{{ __('site.home.search.pickup_date') }}</label>
                    <input type="date" id="pickup_date" name="pickup_date" class="form-control" required>
                </div>
                <div class="col-md-3 col-6">
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
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-search me-2"></i>{{ __('site.home.search.submit') }}</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Services --}}
    <section class="py-5 my-4">
        <div class="container">
            <div class="text-center mb-5" data-reveal>
                <h2 class="fw-bold mb-2">{{ __('site.home.services.title') }}</h2>
                <p class="text-muted">{{ setting('home_services_text') }}</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4" data-reveal>
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body p-4 text-center">
                            <span class="service-icon d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-4 mb-3" style="width: 72px; height: 72px;"><i class="bi bi-car-front fs-2"></i></span>
                            <h3 class="h5 fw-bold">{{ __('site.home.services.simple_title') }}</h3>
                            <p class="text-muted">{{ __('site.home.services.simple_text') }}</p>
                            <a href="{{ route('vehicles.index', app()->getLocale()) }}" class="btn btn-outline-primary btn-sm">{{ __('site.common.view_more') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-reveal style="--reveal-delay: 0.12s">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body p-4 text-center">
                            <span class="service-icon d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-4 mb-3" style="width: 72px; height: 72px;"><i class="bi bi-person-badge fs-2"></i></span>
                            <h3 class="h5 fw-bold">{{ __('site.home.services.with_driver_title') }}</h3>
                            <p class="text-muted">{{ __('site.home.services.with_driver_text') }}</p>
                            <a href="{{ route('driver-service', app()->getLocale()) }}" class="btn btn-outline-primary btn-sm">{{ __('site.common.view_more') }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" data-reveal style="--reveal-delay: 0.24s">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body p-4 text-center">
                            <span class="service-icon d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-4 mb-3" style="width: 72px; height: 72px;"><i class="bi bi-steering-wheel fs-2"></i></span>
                            <h3 class="h5 fw-bold">{{ __('site.home.services.driver_only_title') }}</h3>
                            <p class="text-muted">{{ __('site.home.services.driver_only_text') }}</p>
                            <a href="{{ route('driver-service', app()->getLocale()) }}" class="btn btn-outline-primary btn-sm">{{ __('site.common.view_more') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Chiffres clés animés --}}
    <section class="py-5 bg-secondary text-white">
        <div class="container">
            <div class="row g-4 text-center">
                @foreach ($stats as $index => $stat)
                    <div class="col-6 col-lg-3" data-reveal style="--reveal-delay: {{ $index * 0.1 }}s">
                        <i class="bi bi-{{ $stat['icon'] }} fs-2 text-white-50 d-block mb-2"></i>
                        <span class="display-6 fw-bold" data-counter="{{ $stat['value'] }}">0</span>
                        <p class="text-white-50 small mb-0">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Avantages --}}
    <section class="py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5" data-reveal="left">
                    <h2 class="fw-bold mb-3">{{ __('site.home.advantages.title') }}</h2>
                    <p class="text-muted mb-4">{{ __('site.home.advantages.subtitle') }}</p>
                    <a href="{{ route('contact', app()->getLocale()) }}" class="btn btn-secondary btn-lg"><i class="bi bi-chat-dots me-2"></i>{{ __('site.nav.contact') }}</a>
                </div>
                <div class="col-lg-7">
                    <div class="row g-4">
                        @foreach ($advantages as $index => $advantage)
                            <div class="col-sm-6" data-reveal style="--reveal-delay: {{ $index * 0.1 }}s">
                                <div class="d-flex gap-3">
                                    <span class="flex-shrink-0 d-inline-flex align-items-center justify-content-center bg-accent-subtle text-accent rounded-3" style="width: 48px; height: 48px;">
                                        <i class="bi bi-{{ $advantage['icon'] }} fs-4"></i>
                                    </span>
                                    <div>
                                        <h3 class="h6 fw-bold mb-1">{{ $advantage['title'] }}</h3>
                                        <p class="text-muted small mb-0">{{ $advantage['text'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Étapes de réservation --}}
    <section class="py-5 bg-surface">
        <div class="container">
            <h2 class="text-center fw-bold mb-5" data-reveal>{{ __('site.home.steps.title') }}</h2>
            <div class="row g-4 text-center">
                @foreach ($steps as $index => $step)
                    <div class="col-6 col-lg-3" data-reveal style="--reveal-delay: {{ $index * 0.12 }}s">
                        <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle mb-3 position-relative" style="width: 64px; height: 64px;">
                            <i class="bi bi-{{ $step['icon'] }} fs-4"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">{{ $index + 1 }}</span>
                        </span>
                        <h3 class="h6 fw-bold">{{ $step['label'] }}</h3>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Témoignages : deuxième diaporama, défilement automatique.
         Contenu d'exemple, gérable en back-office à partir de la phase 4. --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5" data-reveal>
                <h2 class="fw-bold mb-2">{{ __('site.home.testimonials.title') }}</h2>
                <p class="text-muted">{{ __('site.home.testimonials.subtitle') }}</p>
            </div>

            <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000" data-reveal="zoom">
                <div class="carousel-inner">
                    @foreach ($testimonials as $index => $testimonial)
                        <div @class(['carousel-item', 'active' => $index === 0])>
                            <figure class="text-center mx-auto mb-4" style="max-width: 640px;">
                                <i class="bi bi-quote fs-1 text-primary"></i>
                                <blockquote class="fs-5 fst-italic text-secondary mb-3">« {{ $testimonial['quote'] }} »</blockquote>
                                <div class="text-warning small mb-2">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                </div>
                                <figcaption class="fw-bold">{{ $testimonial['author'] }} <span class="text-muted fw-normal">· {{ $testimonial['role'] }}</span></figcaption>
                            </figure>
                        </div>
                    @endforeach
                </div>

                <div class="carousel-indicators position-static">
                    @foreach ($testimonials as $index => $testimonial)
                        <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="{{ $index }}" @class(['active' => $index === 0]) aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="{{ __('site.common.slide') }} {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Appel à l'action --}}
    <section class="pb-5">
        <div class="container">
            <div class="float-soft rounded-4 p-4 p-md-5 text-center text-white" style="background: linear-gradient(120deg, var(--color-secondary), color-mix(in srgb, var(--color-primary), var(--color-secondary) 55%));" data-reveal>
                <h2 class="h3 fw-bold mb-2">{{ setting('cta_title', __('site.home.cta.default_title')) }}</h2>
                <p class="text-white-50 mb-4">{{ setting('cta_text', __('site.home.cta.default_text')) }}</p>
                <a href="{{ route('vehicles.index', app()->getLocale()) }}" class="btn btn-light btn-lg px-4 text-primary fw-semibold">{{ __('site.home.cta.button') }}</a>
            </div>
        </div>
    </section>
@endsection
