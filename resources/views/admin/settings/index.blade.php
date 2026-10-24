@extends('layouts.admin')

@section('title', __('site.admin.settings.title'))
@section('page_title', __('site.admin.settings.title'))

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf

        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-identity" type="button" role="tab">{{ __('site.admin.settings.identity') }}</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-home" type="button" role="tab">{{ __('site.admin.settings.home') }}</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-contact" type="button" role="tab">{{ __('site.admin.settings.contact_tab') }}</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pricing" type="button" role="tab">{{ __('site.admin.settings.pricing') }}</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-system" type="button" role="tab">{{ __('site.admin.settings.system') }}</button></li>
        </ul>

        <div class="tab-content">
            {{-- Identité et apparence --}}
            <div class="tab-pane fade show active" id="tab-identity" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.company_name') }} *</label>
                                <input type="text" name="company_name" value="{{ setting('company_name') }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.legal_name') }}</label>
                                <input type="text" name="company_legal_name" value="{{ setting('company_legal_name') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.tagline') }}</label>
                                <input type="text" name="tagline" value="{{ setting('tagline') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.logo') }}</label>
                                <input type="file" name="logo" accept="image/*" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.favicon') }}</label>
                                <input type="file" name="favicon" accept=".png,.ico" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.color_primary') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="color_primary" value="{{ setting('color_primary', '#d95319') }}" class="form-control form-control-color" oninput="document.documentElement.style.setProperty('--color-primary', this.value)">
                                    <code class="align-self-center">{{ setting('color_primary') }}</code>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.color_secondary') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="color_secondary" value="{{ setting('color_secondary', '#14213d') }}" class="form-control form-control-color" oninput="document.documentElement.style.setProperty('--color-secondary', this.value)">
                                    <code class="align-self-center">{{ setting('color_secondary') }}</code>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.color_accent') }}</label>
                                <div class="d-flex gap-2">
                                    <input type="color" name="color_accent" value="{{ setting('color_accent', '#2a9d8f') }}" class="form-control form-control-color" oninput="document.documentElement.style.setProperty('--color-accent', this.value)">
                                    <code class="align-self-center">{{ setting('color_accent') }}</code>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.footer_text') }}</label>
                                <textarea name="footer_text" rows="2" class="form-control">{{ setting('footer_text') }}</textarea>
                            </div>

                            <div class="col-12">
                                <p class="small text-muted mb-0"><i class="bi bi-palette me-1"></i>{{ __('site.admin.settings.colors_hint') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Accueil et diaporama --}}
            <div class="tab-pane fade" id="tab-home" role="tabpanel">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.hero_title') }}</label>
                                <input type="text" name="hero_title" value="{{ setting('hero_title') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.services_text') }}</label>
                                <input type="text" name="home_services_text" value="{{ setting('home_services_text') }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.hero_subtitle') }}</label>
                                <textarea name="hero_subtitle" rows="2" class="form-control">{{ setting('hero_subtitle') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.cta_title') }}</label>
                                <input type="text" name="cta_title" value="{{ setting('cta_title') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.cta_text') }}</label>
                                <input type="text" name="cta_text" value="{{ setting('cta_text') }}" class="form-control">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-semibold">{{ __('site.home.stats.vehicles') }}</label>
                                <input type="number" name="stats_vehicles" min="0" value="{{ setting('stats_vehicles') }}" class="form-control">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-semibold">{{ __('site.home.stats.clients') }}</label>
                                <input type="number" name="stats_clients" min="0" value="{{ setting('stats_clients') }}" class="form-control">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-semibold">{{ __('site.home.stats.years') }}</label>
                                <input type="number" name="stats_years" min="0" value="{{ setting('stats_years') }}" class="form-control">
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label small fw-semibold">{{ __('site.home.stats.satisfaction') }} (%)</label>
                                <input type="number" name="stats_satisfaction" min="0" max="100" value="{{ setting('stats_satisfaction') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-bold mb-1">{{ __('site.admin.settings.slides_title') }}</h2>
                        <p class="small text-muted">{{ __('site.admin.settings.slides_hint') }}</p>

                        <div class="d-grid gap-3">
                            @foreach ($slides as $index => $slide)
                                @include('admin.settings.slide-row', ['index' => $index, 'slide' => $slide])
                            @endforeach
                            @include('admin.settings.slide-row', ['index' => count($slides), 'slide' => null])
                        </div>
                    </div>
                </div>
            </div>

            {{-- Coordonnées --}}
            <div class="tab-pane fade" id="tab-contact" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.address') }}</label>
                                <input type="text" name="contact_address" value="{{ setting('contact_address') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.phone') }}</label>
                                <input type="text" name="contact_phone" value="{{ setting('contact_phone') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.phone_2') }}</label>
                                <input type="text" name="contact_phone_2" value="{{ setting('contact_phone_2') }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.email') }}</label>
                                <input type="email" name="contact_email" value="{{ setting('contact_email') }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">WhatsApp</label>
                                <input type="text" name="contact_whatsapp" value="{{ setting('contact_whatsapp') }}" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.hours') }}</label>
                                <input type="text" name="contact_hours" value="{{ setting('contact_hours') }}" class="form-control">
                            </div>
                            <div class="col-md-3"><label class="form-label small fw-semibold">Facebook</label><input type="url" name="social_facebook" value="{{ setting('social_facebook') }}" class="form-control" placeholder="https://…"></div>
                            <div class="col-md-3"><label class="form-label small fw-semibold">Instagram</label><input type="url" name="social_instagram" value="{{ setting('social_instagram') }}" class="form-control" placeholder="https://…"></div>
                            <div class="col-md-3"><label class="form-label small fw-semibold">TikTok</label><input type="url" name="social_tiktok" value="{{ setting('social_tiktok') }}" class="form-control" placeholder="https://…"></div>
                            <div class="col-md-3"><label class="form-label small fw-semibold">X</label><input type="url" name="social_x" value="{{ setting('social_x') }}" class="form-control" placeholder="https://…"></div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.map') }}</label>
                                <textarea name="map_embed_url" rows="2" class="form-control font-monospace" placeholder='<iframe src="https://www.google.com/maps/embed?..."></iframe>'>{{ setting('map_embed_url') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tarifs --}}
            <div class="tab-pane fade" id="tab-pricing" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.currency') }} *</label>
                                <input type="text" name="currency" value="{{ setting('currency') }}" class="form-control" required maxlength="8">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.driver_with_car_price') }} *</label>
                                <input type="number" name="driver_with_car_price" min="0" step="500" value="{{ setting('driver_with_car_price') }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.driver_with_car_mode') }}</label>
                                <select name="driver_with_car_mode" class="form-select">
                                    <option value="supplement" @selected(setting('driver_with_car_mode') === 'supplement')>{{ __('site.admin.settings.mode_supplement') }}</option>
                                    <option value="package" @selected(setting('driver_with_car_mode') === 'package')>{{ __('site.admin.settings.mode_package') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.driver_only_price') }} *</label>
                                <input type="number" name="driver_only_price" min="0" step="500" value="{{ setting('driver_only_price') }}" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.driver_only_reduced') }} *</label>
                                <input type="number" name="driver_only_price_reduced" min="0" step="500" value="{{ setting('driver_only_price_reduced') }}" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.threshold_days') }} *</label>
                                <input type="number" name="driver_only_threshold_days" min="1" value="{{ setting('driver_only_threshold_days') }}" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Système --}}
            <div class="tab-pane fade" id="tab-system" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.code_prefix') }} *</label>
                                <input type="text" name="reservation_code_prefix" value="{{ setting('reservation_code_prefix') }}" class="form-control" required maxlength="8">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.day_count_mode') }}</label>
                                <select name="day_count_mode" class="form-select">
                                    <option value="started_periods" @selected(setting('day_count_mode') === 'started_periods')>{{ __('site.admin.settings.mode_periods') }}</option>
                                    <option value="calendar" @selected(setting('day_count_mode') === 'calendar')>{{ __('site.admin.settings.mode_calendar') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.pending_blocks') }}</label>
                                <select name="pending_blocks_vehicle" class="form-select">
                                    <option value="0" @selected(setting('pending_blocks_vehicle', '0') === '0')>{{ __('site.admin.settings.no') }}</option>
                                    <option value="1" @selected(setting('pending_blocks_vehicle') === '1')>{{ __('site.admin.settings.yes') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.upload_max') }} (Mo) *</label>
                                <input type="number" name="upload_max_size_mb" min="1" max="20" value="{{ setting('upload_max_size_mb') }}" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">{{ __('site.admin.settings.retention') }} ({{ __('site.admin.settings.months') }}) *</label>
                                <input type="number" name="identity_retention_months" min="1" value="{{ setting('identity_retention_months') }}" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-1"></i>{{ __('site.common.save') }}</button>
        </div>
    </form>
@endsection
