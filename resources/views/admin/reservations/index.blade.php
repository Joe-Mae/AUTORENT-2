@extends('layouts.admin')

@section('title', __('site.admin.reservations.title'))
@section('page_title', __('site.admin.reservations.title'))

@section('content')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reservations.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold" for="f-q">{{ __('site.admin.reservations.search') }}</label>
                    <input type="text" id="f-q" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control form-control-sm" placeholder="Code, nom, téléphone…">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold" for="f-status">{{ __('site.booking.status') }}</label>
                    <select id="f-status" name="status" class="form-select form-select-sm">
                        <option value="">{{ __('site.vehicles.filters.all') }}</option>
                        @foreach (\App\Enums\ReservationStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold" for="f-vehicle">{{ __('site.nav.vehicles') }}</label>
                    <select id="f-vehicle" name="vehicle_id" class="form-select form-select-sm">
                        <option value="">{{ __('site.vehicles.filters.all') }}</option>
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" @selected(($filters['vehicle_id'] ?? null) == $vehicle->id)>{{ $vehicle->brand }} {{ $vehicle->model }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold" for="f-from">{{ __('site.admin.reservations.from') }}</label>
                    <input type="date" id="f-from" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold" for="f-to">{{ __('site.admin.reservations.to') }}</label>
                    <input type="date" id="f-to" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                </div>
            </form>
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
                        <th>{{ __('site.check_booking.code') }}</th>
                        <th>{{ __('site.booking.client') }}</th>
                        <th>{{ __('site.booking.type_label') }}</th>
                        <th>{{ __('site.nav.vehicles') }}</th>
                        <th>{{ __('site.booking.summary_dates') }}</th>
                        <th class="text-end">{{ __('site.booking.summary_total') }}</th>
                        <th>{{ __('site.booking.status') }}</th>
                        <th>{{ __('site.admin.payments.title') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $reservation)
                        <tr>
                            <td><a href="{{ route('admin.reservations.show', $reservation) }}" class="fw-semibold text-decoration-none">{{ $reservation->code }}</a></td>
                            <td>{{ $reservation->clientFullName() }}<br><small class="text-muted">{{ $reservation->phone }}</small></td>
                            <td><small>{{ $reservation->type->label() }}</small></td>
                            <td><small>{{ $reservation->vehicle?->fullName() ?? '—' }}</small></td>
                            <td><small>{{ $reservation->starts_at->format('d/m/Y') }} → {{ $reservation->ends_at->format('d/m/Y') }}</small><br><small class="text-muted">{{ $reservation->days_count }} j</small></td>
                            <td class="text-end">{{ format_price((float) $reservation->total_price) }}</td>
                            <td><x-reservation-status :status="$reservation->status" /></td>
                            <td><x-payment-status :reservation="$reservation" /></td>
                            <td class="text-end">
                                <a href="{{ route('admin.reservations.show', $reservation) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">{{ __('site.admin.reservations.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $reservations->links() }}
    </div>
@endsection
