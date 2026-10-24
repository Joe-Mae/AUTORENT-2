@extends('layouts.admin')

@section('title', __('site.admin.drivers.edit'))
@section('page_title', __('site.admin.drivers.edit'))

@section('content')
    <div class="card border-0 shadow-sm" style="max-width: 560px;">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.drivers.update', $driver) }}" class="d-grid gap-2">
                @csrf @method('PUT')
                <label class="form-label small fw-semibold">{{ __('site.admin.drivers.name') }}</label>
                <input type="text" name="full_name" value="{{ old('full_name', $driver->full_name) }}" class="form-control" required>
                <label class="form-label small fw-semibold">{{ __('site.contact.phone') }}</label>
                <input type="tel" name="phone" value="{{ old('phone', $driver->phone) }}" class="form-control" required>
                <label class="form-label small fw-semibold">{{ __('site.admin.drivers.license') }}</label>
                <input type="text" name="license_number" value="{{ old('license_number', $driver->license_number) }}" class="form-control" required>
                <label class="form-label small fw-semibold">{{ __('site.booking.notes') }}</label>
                <textarea name="notes" rows="2" class="form-control">{{ old('notes', $driver->notes) }}</textarea>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" id="driver-active-edit" value="1" @checked($driver->is_active)>
                    <label class="form-check-label small" for="driver-active-edit">{{ __('site.admin.drivers.active') }}</label>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-secondary">{{ __('site.common.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
