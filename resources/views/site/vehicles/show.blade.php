@extends('layouts.public')

@section('title', $vehicle->fullName().' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="{{ route('home', app()->getLocale()) }}" class="text-decoration-none">{{ __('site.nav.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicles.index', app()->getLocale()) }}" class="text-decoration-none">{{ __('site.nav.vehicles') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $vehicle->fullName() }}</li>
                </ol>
            </nav>

            <div class="row g-4">
                <div class="col-lg-8">
                    {{-- Galerie --}}
                    @if ($vehicle->images->count() > 1)
                        <div id="vehicleGallery" class="carousel slide rounded-4 overflow-hidden shadow-sm" data-bs-ride="false">
                            <div class="carousel-inner">
                                @foreach ($vehicle->images as $index => $image)
                                    <div @class(['carousel-item', 'active' => $index === 0])>
                                        <img src="{{ $image->url() }}" class="d-block w-100 object-fit-cover" style="height: 420px;" alt="{{ $vehicle->fullName() }}">
                                    </div>
                                @endforeach
                            </div>
                            @if ($vehicle->images->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#vehicleGallery" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                    <span class="visually-hidden">{{ __('site.common.previous') }}</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#vehicleGallery" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                    <span class="visually-hidden">{{ __('site.common.next') }}</span>
                                </button>
                            @endif
                        </div>
                    @else
                        <img src="{{ $vehicle->images->first()?->url() ?? asset('img/hero-default.svg') }}" class="w-100 rounded-4 object-fit-cover shadow-sm" style="height: 420px;" alt="{{ $vehicle->fullName() }}">
                    @endif

                    @if ($vehicle->video_path)
                        <video src="{{ $vehicle->videoUrl() }}" controls playsinline preload="metadata"
                               aria-label="{{ __('site.vehicles.video') }} — {{ $vehicle->fullName() }}"
                               class="w-100 rounded-4 shadow-sm mt-3" style="max-height: 420px; background: #000;"></video>
                    @endif

                    <h1 class="h3 fw-bold mt-4 mb-1">{{ $vehicle->fullName() }} <span class="text-muted">· {{ $vehicle->year }}</span></h1>
                    <p class="text-muted"><i class="bi bi-tag me-1"></i>{{ $vehicle->category->name }}</p>

                    {{-- Caractéristiques --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4">
                            <div class="border rounded-3 p-3 text-center">
                                <i class="bi bi-people fs-4 text-primary d-block"></i>
                                <span class="small">{{ $vehicle->seats }} {{ __('places') }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="border rounded-3 p-3 text-center">
                                <i class="bi bi-gear fs-4 text-primary d-block"></i>
                                <span class="small">{{ $vehicle->transmission->label() }}</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="border rounded-3 p-3 text-center">
                                <i class="bi bi-fuel-pump fs-4 text-primary d-block"></i>
                                <span class="small">{{ $vehicle->fuel->label() }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($vehicle->description)
                        <h2 class="h5 fw-bold mb-2">{{ __('site.vehicles.description') }}</h2>
                        <p class="text-muted">{{ $vehicle->description }}</p>
                    @endif

                    {{-- Disponibilité --}}
                    <h2 class="h5 fw-bold mb-3 mt-4">{{ __('site.vehicles.availability') }}</h2>
                    <div class="border rounded-4 p-3 p-md-4 bg-surface">
                        <x-availability-calendar :unavailable-dates="$unavailableDates" :months="2" />
                        <p class="small text-muted mb-0 mt-3">
                            <span class="badge bg-danger-subtle text-danger">&nbsp;&nbsp;</span> {{ __('site.vehicles.legend_unavailable') }}
                        </p>
                    </div>
                </div>

                {{-- Carte de réservation --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 90px;">
                        <div class="card-body p-4">
                            <p class="text-muted small mb-1">{{ __('site.vehicles.price_from') }}</p>
                            <p class="h3 fw-bold text-primary mb-1">{{ format_price($vehicle->daily_price) }}<small class="text-muted fs-6 fw-normal">{{ __('site.common.per_day') }}</small></p>
                            <p class="small text-muted">
                                <i class="bi bi-info-circle me-1"></i>{{ __('site.vehicles.payment_agency') }}
                            </p>

                            <div class="alert alert-warning small py-2" role="note">
                                <i class="bi bi-person-vcard me-1"></i>{{ __('site.vehicles.license_reminder') }}
                            </div>

                            <a href="{{ route('booking.create', array_filter(['locale' => app()->getLocale(), 'vehicle' => $vehicle->id, 'pickup_date' => $pickupDate, 'return_date' => $returnDate])) }}"
                               class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-calendar-check me-1"></i>{{ __('site.common.book') }}
                            </a>

                            @if (setting('contact_whatsapp'))
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('contact_whatsapp')) }}?text={{ rawurlencode($vehicle->fullName()) }}"
                                   target="_blank" rel="noopener" class="btn btn-outline-secondary w-100 mt-2">
                                    <i class="bi bi-whatsapp me-1"></i>{{ __('site.common.whatsapp') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Véhicules similaires --}}
            @if ($similar->isNotEmpty())
                <h2 class="h4 fw-bold mt-5 mb-3">{{ __('site.vehicles.similar') }}</h2>
                <div class="row g-4">
                    @foreach ($similar as $vehicle)
                        <div class="col-md-4">
                            @include('site.vehicles.card', ['vehicle' => $vehicle])
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
