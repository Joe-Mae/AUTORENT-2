@extends('layouts.admin')

@section('title', __('site.admin.title'))
@section('page_title', __('site.admin.title'))

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">{{ __('site.admin.stats.reservations') }}</p>
                            <p class="h2 fw-bold mb-0">{{ $stats['reservations'] }}</p>
                            <span class="badge text-bg-warning">{{ $stats['pending'] }} {{ __('site.admin.stats.pending') }}</span>
                        </div>
                        <i class="bi bi-journal-check fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">{{ __('site.admin.stats.month_revenue') }}</p>
                            <p class="h4 fw-bold mb-0 text-primary">{{ format_price((float) $revenue) }}</p>
                            <span class="badge text-bg-secondary">{{ __('site.admin.stats.occupancy') }} : {{ $occupancy }} %</span>
                        </div>
                        <i class="bi bi-cash-stack fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">{{ __('site.admin.stats.vehicles') }}</p>
                            <p class="h2 fw-bold mb-0">{{ $stats['vehicles'] }}</p>
                            <span class="badge text-bg-success">{{ $stats['vehicles_available'] }} {{ __('site.admin.stats.vehicles_available') }}</span>
                            <span class="badge text-bg-info ms-1">{{ $stats['vehicles_rented'] }} {{ __('site.admin.stats.vehicles_rented') }}</span>
                        </div>
                        <i class="bi bi-car-front fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">{{ __('site.admin.stats.drivers') }}</p>
                            <p class="h2 fw-bold mb-0">{{ $stats['drivers'] }}</p>
                            @if ($stats['messages'] > 0)
                                <span class="badge text-bg-danger">{{ $stats['messages'] }} {{ __('site.admin.stats.messages') }}</span>
                            @endif
                        </div>
                        <i class="bi bi-person-badge fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-box-arrow-right me-1 text-primary"></i>{{ __('site.admin.stats.departures_today') }}</h2>
                    @forelse ($departuresToday as $reservation)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <a href="{{ route('admin.reservations.show', $reservation) }}" class="fw-semibold text-decoration-none">{{ $reservation->code }}</a>
                                <small class="text-muted d-block">{{ $reservation->clientFullName() }} · {{ $reservation->vehicle?->fullName() ?? '—' }}</small>
                            </div>
                            <span class="badge text-bg-primary">{{ $reservation->starts_at->format('H:i') }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('site.admin.stats.no_departures') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-box-arrow-in-left me-1 text-primary"></i>{{ __('site.admin.stats.returns_today') }}</h2>
                    @forelse ($returnsToday as $reservation)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <a href="{{ route('admin.reservations.show', $reservation) }}" class="fw-semibold text-decoration-none">{{ $reservation->code }}</a>
                                <small class="text-muted d-block">{{ $reservation->clientFullName() }} · {{ $reservation->vehicle?->fullName() ?? '—' }}</small>
                            </div>
                            <span class="badge text-bg-secondary">{{ $reservation->ends_at->format('H:i') }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('site.admin.stats.no_returns') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
