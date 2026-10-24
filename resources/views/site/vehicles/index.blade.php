@extends('layouts.public')

@section('title', __('site.vehicles.title').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
                <div>
                    <h1 class="fw-bold mb-1">{{ __('site.vehicles.title') }}</h1>
                    <p class="text-muted mb-0">{{ __('site.vehicles.results', ['count' => $vehicles->total()]) }}</p>
                </div>
                <form method="GET" action="{{ route('vehicles.index', app()->getLocale()) }}" class="d-flex align-items-center gap-2" id="sort-form">
                    <label for="sort" class="small text-muted">{{ __('site.vehicles.sort_label') }}</label>
                    <select name="sort" id="sort" class="form-select form-select-sm" onchange="document.getElementById('sort-form').submit()">
                        @foreach ([
                            'year_desc' => __('site.vehicles.sort.year_desc'),
                            'price_asc' => __('site.vehicles.sort.price_asc'),
                            'price_desc' => __('site.vehicles.sort.price_desc'),
                            'brand_asc' => __('site.vehicles.sort.brand_asc'),
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['sort'] ?? null) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="row g-4">
                {{-- Filtres --}}
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm sticky-top" style="top: 90px;">
                        <div class="card-body">
                            <h2 class="h6 fw-bold mb-3"><i class="bi bi-funnel me-1 text-primary"></i>{{ __('site.vehicles.filters_title') }}</h2>
                            <form method="GET" action="{{ route('vehicles.index', app()->getLocale()) }}">
                                @if (! empty($filters['sort']))
                                    <input type="hidden" name="sort" value="{{ $filters['sort'] }}">
                                @endif

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold" for="f-category">{{ __('site.vehicles.filters.category') }}</label>
                                    <select class="form-select form-select-sm" id="f-category" name="category">
                                        <option value="">{{ __('site.vehicles.filters.all') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold" for="f-brand">{{ __('site.vehicles.filters.brand') }}</label>
                                    <select class="form-select form-select-sm" id="f-brand" name="brand">
                                        <option value="">{{ __('site.vehicles.filters.all') }}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand }}" @selected(($filters['brand'] ?? null) === $brand)>{{ $brand }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold" for="f-seats">{{ __('site.vehicles.filters.seats') }}</label>
                                    <select class="form-select form-select-sm" id="f-seats" name="seats">
                                        <option value="">{{ __('site.vehicles.filters.seats_any') }}</option>
                                        @foreach ([2, 4, 5, 7] as $seats)
                                            <option value="{{ $seats }}" @selected(($filters['seats'] ?? null) == $seats)>{{ $seats }}+ {{ __('places') }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold" for="f-transmission">{{ __('site.vehicles.filters.transmission') }}</label>
                                        <select class="form-select form-select-sm" id="f-transmission" name="transmission">
                                            <option value="">{{ __('site.vehicles.filters.all') }}</option>
                                            <option value="automatic" @selected(($filters['transmission'] ?? null) === 'automatic')>{{ __('site.vehicles.transmission.automatic') }}</option>
                                            <option value="manual" @selected(($filters['transmission'] ?? null) === 'manual')>{{ __('site.vehicles.transmission.manual') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold" for="f-fuel">{{ __('site.vehicles.filters.fuel') }}</label>
                                        <select class="form-select form-select-sm" id="f-fuel" name="fuel">
                                            <option value="">{{ __('site.vehicles.filters.all') }}</option>
                                            @foreach (\App\Enums\FuelType::cases() as $fuel)
                                                <option value="{{ $fuel->value }}" @selected(($filters['fuel'] ?? null) === $fuel->value)>{{ $fuel->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold" for="f-price-min">{{ __('site.vehicles.filters.price_min') }}</label>
                                        <input type="number" min="0" step="1000" class="form-control form-control-sm" id="f-price-min" name="price_min" value="{{ $filters['price_min'] ?? '' }}" placeholder="0">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold" for="f-price-max">{{ __('site.vehicles.filters.price_max') }}</label>
                                        <input type="number" min="0" step="1000" class="form-control form-control-sm" id="f-price-max" name="price_max" value="{{ $filters['price_max'] ?? '' }}" placeholder="∞">
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold" for="f-pickup">{{ __('site.home.search.pickup_date') }}</label>
                                        <input type="date" class="form-control form-control-sm" id="f-pickup" name="pickup_date" value="{{ $filters['pickup_date'] ?? '' }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold" for="f-return">{{ __('site.home.search.return_date') }}</label>
                                        <input type="date" class="form-control form-control-sm" id="f-return" name="return_date" value="{{ $filters['return_date'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>{{ __('site.common.search') }}</button>
                                    <a href="{{ route('vehicles.index', app()->getLocale()) }}" class="btn btn-outline-secondary btn-sm">{{ __('site.vehicles.filters.reset') }}</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Résultats --}}
                <div class="col-lg-9">
                    @if ($vehicles->isEmpty())
                        <div class="alert alert-light border d-flex align-items-center gap-3" role="status">
                            <i class="bi bi-search fs-4 text-muted"></i>
                            <div>{{ __('site.vehicles.empty') }}</div>
                        </div>
                    @else
                        <div class="row g-4">
                            @foreach ($vehicles as $vehicle)
                                <div class="col-md-6 col-xl-4">
                                    @include('site.vehicles.card', ['vehicle' => $vehicle])
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $vehicles->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
