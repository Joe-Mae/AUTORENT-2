@extends('layouts.admin')

@section('title', __('site.admin.drivers.title'))
@section('page_title', __('site.admin.drivers.title'))

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">{{ __('site.admin.drivers.add') }}</h2>
                    <form method="POST" action="{{ route('admin.drivers.store') }}" class="d-grid gap-2">
                        @csrf
                        <input type="text" name="full_name" class="form-control" placeholder="{{ __('site.admin.drivers.name') }}" required>
                        <input type="tel" name="phone" class="form-control" placeholder="{{ __('site.contact.phone') }}" required>
                        <input type="text" name="license_number" class="form-control" placeholder="{{ __('site.admin.drivers.license') }}" required>
                        <textarea name="notes" rows="2" class="form-control" placeholder="{{ __('site.booking.notes') }}"></textarea>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="driver-active" value="1" checked>
                            <label class="form-check-label small" for="driver-active">{{ __('site.admin.drivers.active') }}</label>
                        </div>
                        <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>{{ __('site.admin.drivers.name') }}</th><th>{{ __('site.contact.phone') }}</th><th>{{ __('site.admin.drivers.license') }}</th><th>{{ __('site.admin.drivers.bookings') }}</th><th>{{ __('site.booking.status') }}</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse ($drivers as $driver)
                                <tr class="{{ $driver->is_active ? '' : 'table-secondary' }}">
                                    <td><strong>{{ $driver->full_name }}</strong> <small class="text-muted d-block">{{ $driver->notes }}</small></td>
                                    <td>{{ $driver->phone }}</td>
                                    <td>{{ $driver->license_number }}</td>
                                    <td>{{ $driver->reservations_count }}</td>
                                    <td><span class="badge {{ $driver->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $driver->is_active ? __('site.admin.drivers.active') : __('site.admin.drivers.inactive') }}</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.drivers.edit', $driver) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="{{ route('admin.drivers.destroy', $driver) }}" class="d-inline" data-confirm="{{ __('site.admin.confirm_delete') }}">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">{{ __('site.admin.drivers.empty') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
