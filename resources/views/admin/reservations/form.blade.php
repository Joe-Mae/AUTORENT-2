@php($isEdit = $reservation->exists)
@extends('layouts.admin')

@section('title', $isEdit ? $reservation->code : __('site.admin.reservations.create'))
@section('page_title', $isEdit ? __('site.admin.reservations.edit') : __('site.admin.reservations.create'))

@section('content')
    @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="card border-0 shadow-sm" style="max-width: 860px;">
        <div class="card-body">
            <form method="POST" action="{{ $isEdit ? route('admin.reservations.update', $reservation) : route('admin.reservations.store') }}" class="row g-3">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                @if (! $isEdit)
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">{{ __('site.booking.type_label') }} *</label>
                        <select name="type" class="form-select" required>
                            <option value="vehicle">{{ __('site.booking.type.vehicle') }}</option>
                            <option value="vehicle_with_driver">{{ __('site.booking.type.vehicle_with_driver') }}</option>
                            <option value="driver_only">{{ __('site.home.services.driver_only_title') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">{{ __('site.nav.vehicles') }}</label>
                        <select name="vehicle_id" class="form-select">
                            <option value="">{{ __('site.admin.reservations.no_vehicle') }}</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->fullName() }} · {{ $vehicle->registration_plate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">{{ __('site.admin.drivers.title') }}</label>
                        <select name="driver_id" class="form-select">
                            <option value="">{{ __('site.admin.reservations.no_driver') }}</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">{{ __('site.booking.first_name') }} *</label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">{{ __('site.booking.last_name') }} *</label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">{{ __('site.contact.phone') }} *</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">{{ __('site.contact.email') }} *</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">{{ __('site.admin.reservations.client_vehicle_brand') }}</label>
                        <input type="text" name="client_vehicle_brand" value="{{ old('client_vehicle_brand') }}" class="form-control" placeholder="{{ __('site.booking.type_label') }} chauffeur seul">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">{{ __('site.admin.reservations.client_vehicle_plate') }}</label>
                        <input type="text" name="client_vehicle_plate" value="{{ old('client_vehicle_plate') }}" class="form-control">
                    </div>
                @endif

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">{{ __('site.admin.reservations.starts_at') }} *</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $isEdit ? $reservation->starts_at->format('Y-m-d\TH:i') : '') }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">{{ __('site.admin.reservations.ends_at') }} *</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $isEdit ? $reservation->ends_at->format('Y-m-d\TH:i') : '') }}" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">{{ __('site.booking.pickup_location') }}</label>
                    <input type="text" name="pickup_location" value="{{ old('pickup_location', $reservation->pickup_location) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">{{ __('site.admin.reservations.internal_notes') }}</label>
                    <textarea name="internal_notes" rows="2" class="form-control">{{ old('internal_notes', $reservation->internal_notes) }}</textarea>
                </div>
                @if ($isEdit)
                    <div class="col-12">
                        <label class="form-label small fw-semibold">{{ __('site.booking.notes') }}</label>
                        <textarea name="notes" rows="2" class="form-control">{{ old('notes', $reservation->notes) }}</textarea>
                    </div>
                @endif

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    <a href="{{ $isEdit ? route('admin.reservations.show', $reservation) : route('admin.reservations.index') }}" class="btn btn-outline-secondary">{{ __('site.common.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
