@extends('layouts.admin')

@section('title', __('site.admin.reports.title'))
@section('page_title', __('site.admin.reports.title'))

@section('content')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small fw-semibold mb-0" for="from">{{ __('site.admin.reservations.from') }}</label>
                    <input type="date" id="from" name="from" value="{{ $from->format('Y-m-d') }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <label class="form-label small fw-semibold mb-0" for="to">{{ __('site.admin.reservations.to') }}</label>
                    <input type="date" id="to" name="to" value="{{ $to->format('Y-m-d') }}" class="form-control form-control-sm">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm">{{ __('site.common.search') }}</button>
                </div>
                <div class="col-auto ms-auto d-flex gap-2">
                    <a href="{{ route('admin.reports.export-csv', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-filetype-csv me-1"></i>CSV</a>
                    <a href="{{ route('admin.reports.export-pdf', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <p class="text-muted small mb-1">{{ __('site.admin.reports.billed_reservations') }}</p>
                <p class="h3 fw-bold mb-0">{{ $summary['count'] }}</p>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <p class="text-muted small mb-1">{{ __('site.admin.reports.revenue') }}</p>
                <p class="h3 fw-bold mb-0 text-primary">{{ format_price((float) $summary['revenue']) }}</p>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <p class="text-muted small mb-1">{{ __('site.admin.reports.rented_days') }}</p>
                <p class="h3 fw-bold mb-0">{{ $summary['days'] }}</p>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm"><div class="card-body">
                <p class="text-muted small mb-1">{{ __('site.booking.type_label') }}</p>
                {{-- groupBy renvoie les valeurs d'enum en clés de tableau (string) --}}
                @foreach ($summary['by_type'] as $type => $count)
                    <span class="badge text-bg-secondary me-1">{{ \App\Enums\ReservationType::from($type)->label() }} : {{ $count }}</span>
                @endforeach
            </div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>{{ __('site.check_booking.code') }}</th><th>{{ __('site.booking.client') }}</th><th>{{ __('site.nav.vehicles') }}</th><th>{{ __('site.booking.summary_dates') }}</th><th class="text-end">{{ __('site.booking.summary_total') }}</th></tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $reservation)
                        <tr>
                            <td><a href="{{ route('admin.reservations.show', $reservation) }}" class="text-decoration-none">{{ $reservation->code }}</a></td>
                            <td><small>{{ $reservation->clientFullName() }}</small></td>
                            <td><small>{{ $reservation->vehicle?->fullName() ?? '—' }}</small></td>
                            <td><small>{{ $reservation->starts_at->format('d/m/Y') }} → {{ $reservation->ends_at->format('d/m/Y') }}</small></td>
                            <td class="text-end">{{ format_price((float) $reservation->total_price) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ __('site.admin.reports.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
