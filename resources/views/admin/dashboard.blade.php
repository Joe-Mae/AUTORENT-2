@extends('layouts.admin')

@section('title', __('site.admin.title'))
@section('page_title', __('site.admin.title'))

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
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

        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">{{ __('site.admin.stats.vehicles') }}</p>
                            <p class="h2 fw-bold mb-0">{{ $stats['vehicles'] }}</p>
                            <span class="badge text-bg-success">{{ $stats['vehicles_available'] }} {{ __('site.admin.stats.vehicles_available') }}</span>
                        </div>
                        <i class="bi bi-car-front fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
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

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h2 class="h5 fw-bold">{{ __('site.admin.dashboard_welcome') }}</h2>
            <p class="text-muted mb-0">{{ __('site.admin.dashboard_text') }}</p>
        </div>
    </div>
@endsection
