@php
    $pickupTime = old('pickup_time', '10:00');
    $prefillType = old('type', $withDriver ? 'vehicle_with_driver' : 'vehicle');
@endphp
@extends('layouts.public')

@section('title', __('site.booking.title').' · '.$vehicle->fullName().' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 960px;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="{{ route('vehicles.index', app()->getLocale()) }}" class="text-decoration-none">{{ __('site.nav.vehicles') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('vehicles.show', [app()->getLocale(), $vehicle]) }}" class="text-decoration-none">{{ $vehicle->fullName() }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('site.booking.title') }}</li>
                </ol>
            </nav>

            <div class="d-flex align-items-center gap-3 border rounded-4 p-3 mb-4 bg-surface">
                <img src="{{ $vehicle->primaryImage?->url() ?? asset('img/hero-default.svg') }}" alt="{{ $vehicle->fullName() }}" class="rounded-3 object-fit-cover" style="width: 96px; height: 72px;">
                <div class="flex-grow-1">
                    <p class="fw-bold mb-0">{{ $vehicle->fullName() }} · {{ $vehicle->year }}</p>
                    <p class="text-muted small mb-0">{{ $vehicle->category->name }} · {{ $vehicle->seats }} {{ __('places') }} · {{ $vehicle->transmission->label() }}</p>
                </div>
                <p class="text-primary fw-bold mb-0" data-vehicle-price="{{ $vehicle->daily_price }}">{{ format_price($vehicle->daily_price) }}{{ __('site.common.per_day') }}</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <p class="mb-1 fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i>{{ __('site.booking.errors.title') }}</p>
                    <ul class="small mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('booking.store', app()->getLocale()) }}"
                  enctype="multipart/form-data" id="booking-form" class="needs-validation" novalidate
                  data-driver-price="{{ setting('driver_with_car_price', '7000') }}"
                  data-currency="{{ setting('currency', 'FCFA') }}">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                {{-- Honeypot : invisible pour les humains, les robots le remplissent --}}
                <div style="position: absolute; left: -9999px;" aria-hidden="true">
                    <label for="website">Site web</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                {{-- Étape 1 : dates et formule --}}
                <div class="card border-0 shadow-sm mb-3 booking-step" data-step="1">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3"><span class="badge bg-primary rounded-circle me-2">1</span>{{ __('site.booking.step_dates') }}</h2>

                        <div class="row g-3">
                            <div class="col-md-4 col-6">
                                <label for="pickup_date" class="form-label small fw-semibold">{{ __('site.home.search.pickup_date') }} *</label>
                                <input type="date" id="pickup_date" name="pickup_date" value="{{ old('pickup_date', $pickupDate) }}"
                                       class="form-control @error('pickup_date') is-invalid @enderror" required>
                                @error('pickup_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 col-6">
                                <label for="return_date" class="form-label small fw-semibold">{{ __('site.home.search.return_date') }} *</label>
                                <input type="date" id="return_date" name="return_date" value="{{ old('return_date', $returnDate) }}"
                                       class="form-control @error('return_date') is-invalid @enderror" required>
                                @error('return_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="pickup_time" class="form-label small fw-semibold">{{ __('site.booking.pickup_time') }} *</label>
                                <input type="time" id="pickup_time" name="pickup_time" value="{{ $pickupTime }}" class="form-control @error('pickup_time') is-invalid @enderror" required>
                                @error('pickup_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">{{ __('site.home.search.formula') }} *</label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="border rounded-3 p-3 d-flex gap-2 h-100">
                                            <input type="radio" name="type" value="vehicle" class="form-check-input mt-1" data-toggle="driver-fields" data-driver-fields="none" @checked(old('type', 'vehicle') === 'vehicle' || (! old('type') && ! $withDriver))>
                                            <span>
                                                <strong>{{ __('site.booking.type.vehicle') }}</strong><br>
                                                <small class="text-muted">{{ __('site.booking.type.vehicle_text') }}</small>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="border rounded-3 p-3 d-flex gap-2 h-100">
                                            <input type="radio" name="type" value="vehicle_with_driver" class="form-check-input mt-1" data-driver-fields="service" @checked(old('type') === 'vehicle_with_driver' || (! old('type') && $withDriver))>
                                            <span>
                                                <strong>{{ __('site.booking.type.vehicle_with_driver') }}</strong><br>
                                                <small class="text-muted">+ {{ format_price((float) setting('driver_with_car_price', '7000')) }}{{ __('site.common.per_day') }}</small>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                                @error('type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="pickup_location" class="form-label small fw-semibold">{{ __('site.booking.pickup_location') }}</label>
                                <input type="text" id="pickup_location" name="pickup_location" value="{{ old('pickup_location') }}" class="form-control" placeholder="{{ setting('contact_address') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Étape 2 : options chauffeur --}}
                <div class="card border-0 shadow-sm mb-3 booking-step" data-step="2" hidden>
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3"><span class="badge bg-primary rounded-circle me-2">2</span>{{ __('site.booking.step_driver') }}</h2>
                        <p class="small text-muted">{{ __('site.booking.step_driver_text') }}</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="event_reason" class="form-label small fw-semibold">{{ __('site.booking.event_reason') }}</label>
                                <select id="event_reason" name="event_reason" class="form-select">
                                    <option value="">{{ __('site.booking.event_reason_none') }}</option>
                                    @foreach (['wedding' => __('site.driver_service.use_cases.wedding'), 'event' => __('site.driver_service.use_cases.event'), 'business' => __('site.driver_service.use_cases.business'), 'errands' => __('site.driver_service.use_cases.errands')] as $value => $label)
                                        <option value="{{ $label }}" @selected(old('event_reason') === $label)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="passenger_count" class="form-label small fw-semibold">{{ __('site.booking.passenger_count') }}</label>
                                <input type="number" min="1" max="20" id="passenger_count" name="passenger_count" value="{{ old('passenger_count') }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="itinerary" class="form-label small fw-semibold">{{ __('site.booking.itinerary') }}</label>
                                <textarea id="itinerary" name="itinerary" rows="3" class="form-control" placeholder="{{ __('site.booking.itinerary_placeholder') }}">{{ old('itinerary') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Étape 3 : informations et pièce d'identité --}}
                <div class="card border-0 shadow-sm mb-3 booking-step" data-step="3" hidden>
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3"><span class="badge bg-primary rounded-circle me-2">3</span>{{ __('site.booking.step_identity') }}</h2>

                        <div class="alert alert-warning small py-2">
                            <i class="bi bi-shield-exclamation me-1"></i>
                            {{ __('site.booking.identity_notice') }}
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label small fw-semibold">{{ __('site.booking.first_name') }} *</label>
                                <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror" required>
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label small fw-semibold">{{ __('site.booking.last_name') }} *</label>
                                <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror" required>
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label small fw-semibold">{{ __('site.contact.phone') }} *</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" required>
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-semibold">{{ __('site.contact.email') }} *</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="birth_date" class="form-label small fw-semibold">{{ __('site.booking.birth_date') }} *</label>
                                <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" class="form-control @error('birth_date') is-invalid @enderror" required>
                                @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="id_card_number" class="form-label small fw-semibold">{{ __('site.booking.id_card_number') }} *</label>
                                <input type="text" id="id_card_number" name="id_card_number" value="{{ old('id_card_number') }}" class="form-control @error('id_card_number') is-invalid @enderror" required>
                                @error('id_card_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label small fw-semibold">{{ __('site.booking.address') }}</label>
                                <input type="text" id="address" name="address" value="{{ old('address') }}" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label for="identity_front" class="form-label small fw-semibold">{{ __('site.booking.identity_front') }} * <small class="text-muted">(JPG, PNG ou PDF · {{ setting('upload_max_size_mb', '5') }} Mo max)</small></label>
                                <input type="file" id="identity_front" name="identity_front" accept=".jpg,.jpeg,.png,.pdf"
                                       class="form-control @error('identity_front') is-invalid @enderror" required>
                                <p class="small text-muted mt-1 mb-0" data-file-label="identity_front">{{ __('site.booking.no_file') }}</p>
                                @error('identity_front')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="identity_back" class="form-label small fw-semibold">{{ __('site.booking.identity_back') }} <small class="text-muted">({{ __('site.common.optional') }})</small></label>
                                <input type="file" id="identity_back" name="identity_back" accept=".jpg,.jpeg,.png,.pdf"
                                       class="form-control @error('identity_back') is-invalid @enderror">
                                <p class="small text-muted mt-1 mb-0" data-file-label="identity_back">{{ __('site.booking.no_file') }}</p>
                                @error('identity_back')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label small fw-semibold">{{ __('site.booking.notes') }}</label>
                                <textarea id="notes" name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Étape 4 : récapitulatif et validation --}}
                <div class="card border-0 shadow-sm mb-3 booking-step" data-step="4" hidden>
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3"><span class="badge bg-primary rounded-circle me-2">4</span>{{ __('site.booking.step_summary') }}</h2>

                        <div class="bg-surface rounded-4 p-3 mb-3">
                            <div class="d-flex justify-content-between small mb-1"><span>{{ __('site.booking.summary_dates') }}</span><strong data-summary="dates"></strong></div>
                            <div class="d-flex justify-content-between small mb-1"><span>{{ __('site.booking.summary_days') }}</span><strong data-summary="days"></strong></div>
                            <div class="d-flex justify-content-between small mb-1"><span>{{ __('site.booking.summary_vehicle_price') }}</span><strong data-summary="vehicle-price"></strong></div>
                            <div class="d-flex justify-content-between small mb-1 d-none" data-summary-row="driver-price"><span>{{ __('site.booking.summary_driver_price') }}</span><strong data-summary="driver-price"></strong></div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between"><strong>{{ __('site.booking.summary_total') }}</strong><strong class="text-primary fs-5" data-summary="total"></strong></div>
                            <p class="small text-muted mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>{{ __('site.booking.price_disclaimer') }}</p>
                        </div>

                        <div class="alert alert-warning small py-2">
                            <i class="bi bi-person-vcard me-1"></i>{{ __('site.booking.license_reminder') }}
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" name="terms" id="terms" @checked(old('terms')) required>
                            <label class="form-check-label small" for="terms">
                                {{ __('site.booking.terms_accept', ['url' => route('page', ['locale' => app()->getLocale(), 'page' => 'conditions-de-location'])]) }}
                            </label>
                            @error('terms')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input @error('identity_consent') is-invalid @enderror" type="checkbox" name="identity_consent" id="identity_consent" @checked(old('identity_consent')) required>
                            <label class="form-check-label small" for="identity_consent">{{ __('site.booking.consent_accept') }}</label>
                            @error('identity_consent')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-step-prev>{{ __('site.common.previous') }}</button>
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                <i class="bi bi-check-circle me-1"></i>{{ __('site.booking.submit') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Navigation des étapes --}}
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-step-prev hidden>{{ __('site.common.previous') }}</button>
                    <button type="button" class="btn btn-primary" data-step-next>{{ __('site.common.next') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
