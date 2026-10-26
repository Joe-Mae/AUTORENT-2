@php($isEdit = $vehicle->exists)
@extends('layouts.admin')

@section('title', ($isEdit ? __('site.admin.vehicles.edit') : __('site.admin.vehicles.add')).' · '.$vehicle->brand)
@section('page_title', $isEdit ? __('site.admin.vehicles.edit') : __('site.admin.vehicles.add'))

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('admin.vehicles.update', $vehicle) : route('admin.vehicles.store') }}"
          enctype="multipart/form-data" class="row g-3">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="brand">{{ __('site.admin.vehicles.brand') }} *</label>
                            <input type="text" id="brand" name="brand" value="{{ old('brand', $vehicle->brand) }}" class="form-control @error('brand') is-invalid @enderror" required>
                            @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="model">{{ __('site.admin.vehicles.model') }} *</label>
                            <input type="text" id="model" name="model" value="{{ old('model', $vehicle->model) }}" class="form-control @error('model') is-invalid @enderror" required>
                            @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="year">{{ __('site.admin.vehicles.year') }} *</label>
                            <input type="number" id="year" name="year" min="1990" max="{{ date('Y') + 1 }}" value="{{ old('year', $vehicle->year) }}" class="form-control @error('year') is-invalid @enderror" required>
                            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="vehicle_category_id">{{ __('site.vehicles.filters.category') }} *</label>
                            <select id="vehicle_category_id" name="vehicle_category_id" class="form-select @error('vehicle_category_id') is-invalid @enderror" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('vehicle_category_id', $vehicle->vehicle_category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('vehicle_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="registration_plate">{{ __('site.admin.vehicles.plate') }} *</label>
                            <input type="text" id="registration_plate" name="registration_plate" value="{{ old('registration_plate', $vehicle->registration_plate) }}" class="form-control @error('registration_plate') is-invalid @enderror" required>
                            @error('registration_plate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="daily_price">{{ __('site.admin.vehicles.daily_price') }} ({{ setting('currency') }}) *</label>
                            <input type="number" id="daily_price" name="daily_price" min="0" step="500" value="{{ old('daily_price', $vehicle->daily_price) }}" class="form-control @error('daily_price') is-invalid @enderror" required>
                            @error('daily_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="seats">{{ __('site.vehicles.filters.seats') }} *</label>
                            <input type="number" id="seats" name="seats" min="1" max="9" value="{{ old('seats', $vehicle->seats) }}" class="form-control" required>
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="transmission">{{ __('site.vehicles.filters.transmission') }} *</label>
                            <select id="transmission" name="transmission" class="form-select">
                                @foreach (\App\Enums\TransmissionType::cases() as $transmission)
                                    <option value="{{ $transmission->value }}" @selected(old('transmission', $vehicle->transmission?->value) === $transmission->value)>{{ $transmission->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="fuel">{{ __('site.vehicles.filters.fuel') }} *</label>
                            <select id="fuel" name="fuel" class="form-select">
                                @foreach (\App\Enums\FuelType::cases() as $fuel)
                                    <option value="{{ $fuel->value }}" @selected(old('fuel', $vehicle->fuel?->value) === $fuel->value)>{{ $fuel->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-6">
                            <label class="form-label small fw-semibold" for="mileage_km">{{ __('site.admin.vehicles.mileage') }} (km) *</label>
                            <input type="number" id="mileage_km" name="mileage_km" min="0" value="{{ old('mileage_km', $vehicle->mileage_km) }}" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold" for="status">{{ __('site.booking.status') }} *</label>
                            <select id="status" name="status" class="form-select">
                                @foreach (\App\Enums\VehicleStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected(old('status', $vehicle->status?->value) === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold" for="description">{{ __('site.vehicles.description') }}</label>
                            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $vehicle->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">{{ __('site.admin.vehicles.photos') }}</h2>

                    @if ($isEdit)
                        <div class="row g-2 mb-3">
                            @foreach ($vehicle->images as $image)
                                <div class="col-6">
                                    <div class="position-relative">
                                        <img src="{{ $image->url() }}" class="w-100 rounded object-fit-cover" style="height: 80px;" alt="">
                                        @if ($image->is_primary)
                                            <span class="position-absolute top-0 start-0 badge text-bg-primary m-1">{{ __('site.admin.vehicles.primary') }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-1 mt-1">
                                        @unless ($image->is_primary)
                                            <form method="POST" action="{{ route('admin.vehicle-images.primary', $image) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-secondary py-0 px-1" title="{{ __('site.admin.vehicles.make_primary') }}"><i class="bi bi-star"></i></button>
                                            </form>
                                        @endunless
                                        <form method="POST" action="{{ route('admin.vehicle-images.destroy', $image) }}" data-confirm="{{ __('site.admin.confirm_delete') }}">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <label class="form-label small fw-semibold" for="photos">{{ $isEdit ? __('site.admin.vehicles.add_photos') : __('site.admin.vehicles.photos') }}</label>
                    <input type="file" id="photos" name="photos[]" multiple accept="image/*" class="form-control @error('photos.*') is-invalid @enderror">
                    <small class="text-muted">{{ __('site.admin.vehicles.photos_hint') }}</small>
                </div>
            </div>

            @if ($isEdit)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">{{ __('site.admin.vehicles.blocked_periods') }}</h2>

                        <div class="mb-3">
                            @forelse ($vehicle->blockedPeriods as $period)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
                                    <span>{{ $period->starts_at->format('d/m/Y') }} → {{ $period->ends_at->format('d/m/Y') }} {{ $period->reason ? '· '.$period->reason : '' }}</span>
                                    <form method="POST" action="{{ route('admin.vehicle-blocked-periods.destroy', $period) }}" data-confirm="{{ __('site.admin.vehicles.blocked_confirm') }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-1"><i class="bi bi-x-lg"></i></button>
                                    </form>
                                </div>
                            @empty
                                <p class="small text-muted mb-2">{{ __('site.admin.vehicles.no_blocked_periods') }}</p>
                            @endforelse
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-0">{{ __('site.admin.reservations.from') }}</label>
                                <input type="date" name="blocked_from" class="form-control form-control-sm">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-0">{{ __('site.admin.reservations.to') }}</label>
                                <input type="date" name="blocked_to" class="form-control form-control-sm">
                            </div>
                            <div class="col-12">
                                <input type="text" name="blocked_reason" class="form-control form-control-sm" placeholder="{{ __('site.admin.vehicles.blocked_reason') }}">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>{{ __('site.common.save') }}</button>
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-secondary">{{ __('site.common.cancel') }}</a>
        </div>
    </form>
@endsection
