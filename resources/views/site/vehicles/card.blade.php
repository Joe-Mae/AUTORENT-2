@php
    /** @var \App\Models\Vehicle $vehicle */
    $image = $vehicle->primaryImage?->url() ?? asset('img/hero-default.svg');
@endphp
<div class="card border-0 shadow-sm h-100 hover-lift card-img-zoom overflow-hidden">
    <a href="{{ route('vehicles.show', [app()->getLocale(), $vehicle]) }}" class="text-decoration-none">
        <div class="ratio ratio-4x3 overflow-hidden bg-surface">
            <img src="{{ $image }}" alt="{{ $vehicle->fullName() }}" class="object-fit-cover w-100 h-100" loading="lazy">
        </div>
    </a>
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <span class="badge bg-secondary-subtle text-secondary">{{ $vehicle->category->name }}</span>
            <span class="fw-bold text-primary">{{ format_price($vehicle->daily_price) }}<small class="text-muted fw-normal">{{ __('site.common.per_day') }}</small></span>
        </div>
        <h3 class="h6 fw-bold mb-2 text-truncate">{{ $vehicle->fullName() }} <span class="text-muted fw-normal">· {{ $vehicle->year }}</span></h3>
        <ul class="list-unstyled small text-muted d-flex flex-wrap gap-3 mb-0">
            <li><i class="bi bi-people me-1"></i>{{ $vehicle->seats }} {{ __('places') }}</li>
            <li><i class="bi bi-gear me-1"></i>{{ $vehicle->transmission->label() }}</li>
            <li><i class="bi bi-fuel-pump me-1"></i>{{ $vehicle->fuel->label() }}</li>
        </ul>
    </div>
    <div class="card-footer bg-white border-0 pt-0 pb-3">
        <a href="{{ route('vehicles.show', [app()->getLocale(), $vehicle]) }}" class="btn btn-sm btn-outline-primary w-100">
            {{ __('site.vehicles.view') }}
        </a>
    </div>
</div>
