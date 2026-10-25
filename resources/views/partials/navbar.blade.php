<nav class="navbar navbar-expand-lg navbar-public sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="{{ route('home', app()->getLocale()) }}">
            @if (setting('logo'))
                <img src="{{ asset('storage/'.setting('logo')) }}" alt="{{ setting('company_name') }}" height="40">
            @else
                <span class="brand-badge bg-primary text-white rounded d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-car-front fs-5"></i>
                </span>
            @endif
            <span class="text-secondary">{{ setting('company_name') }}</span>
        </a>

        <div class="d-flex align-items-center gap-2 order-lg-3">
            <button type="button" class="btn btn-outline-secondary btn-sm theme-toggle" aria-label="{{ __('site.common.theme_toggle') }}" title="{{ __('site.common.theme_toggle') }}">
                <i class="bi bi-moon-stars theme-icon-dark"></i><i class="bi bi-sun theme-icon-light"></i>
            </button>
            <a href="tel:{{ preg_replace('/\s+/', '', setting('contact_phone')) }}" class="btn btn-primary btn-sm d-none d-sm-inline-flex align-items-center gap-1">
                <i class="bi bi-telephone"></i> {{ __('site.common.call') }}
            </a>
            @if (setting('contact_whatsapp'))
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', setting('contact_whatsapp')) }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm d-none d-md-inline-flex align-items-center gap-1">
                    <i class="bi bi-whatsapp"></i> {{ __('site.common.whatsapp') }}
                </a>
            @endif
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="{{ __('site.common.menu') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-lg-auto mb-2 mb-lg-0 gap-lg-1">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home', app()->getLocale()) }}">{{ __('site.nav.home') }}</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index', app()->getLocale()) }}">{{ __('site.nav.vehicles') }}</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('driver-service') ? 'active' : '' }}" href="{{ route('driver-service', app()->getLocale()) }}">{{ __('site.nav.driver_service') }}</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('check-booking') ? 'active' : '' }}" href="{{ route('check-booking', app()->getLocale()) }}">{{ __('site.nav.check_booking') }}</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('faq') ? 'active' : '' }}" href="{{ route('faq', app()->getLocale()) }}">{{ __('site.nav.faq') }}</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact', app()->getLocale()) }}">{{ __('site.nav.contact') }}</a></li>
            </ul>
            <a href="tel:{{ preg_replace('/\s+/', '', setting('contact_phone')) }}" class="btn btn-primary btn-sm d-sm-none w-100 mb-2">
                <i class="bi bi-telephone me-1"></i> {{ __('site.common.call') }}
            </a>
        </div>
    </div>
</nav>
