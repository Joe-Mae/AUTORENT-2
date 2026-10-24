@extends('layouts.admin')

@section('title', __('site.admin.vehicles.title'))
@section('page_title', __('site.admin.vehicles.title'))

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">{{ __('site.booking.status') }} : {{ __('site.vehicles.filters.all') }}</option>
                @foreach (\App\Enums\VehicleStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">{{ __('site.vehicles.filters.category') }} : {{ __('site.vehicles.filters.all') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </form>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('site.admin.categories.title') }}</a>
            <a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>{{ __('site.admin.vehicles.add') }}</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>{{ __('site.nav.vehicles') }}</th>
                        <th>{{ __('site.vehicles.filters.category') }}</th>
                        <th>{{ __('site.admin.vehicles.plate') }}</th>
                        <th class="text-end">{{ __('site.admin.vehicles.daily_price') }}</th>
                        <th>{{ __('site.booking.status') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicles as $vehicle)
                        <tr>
                            <td style="width: 72px;">
                                @if ($vehicle->primaryImage)
                                    <img src="{{ $vehicle->primaryImage->url() }}" alt="" class="rounded object-fit-cover" style="width: 64px; height: 44px;">
                                @else
                                    <span class="d-inline-block bg-surface rounded" style="width: 64px; height: 44px;"></span>
                                @endif
                            </td>
                            <td><strong>{{ $vehicle->fullName() }}</strong> <small class="text-muted">· {{ $vehicle->year }}</small></td>
                            <td><small>{{ $vehicle->category->name }}</small></td>
                            <td><small>{{ $vehicle->registration_plate }}</small></td>
                            <td class="text-end">{{ format_price($vehicle->daily_price) }}</td>
                            <td>
                                <span class="badge {{ match ($vehicle->status->value) {
                                    'available' => 'text-bg-success',
                                    'maintenance' => 'text-bg-warning',
                                    default => 'text-bg-secondary',
                                } }}">{{ $vehicle->status->label() }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}" class="d-inline" onsubmit="return confirm('{{ __('site.admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">{{ __('site.admin.vehicles.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $vehicles->links() }}</div>
@endsection
