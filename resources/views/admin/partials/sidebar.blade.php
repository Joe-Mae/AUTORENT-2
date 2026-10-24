@php
    /**
     * Sections du menu latéral du back-office, filtrées par permission.
     * Les contenus forment un sous-menu déroulant.
     */
    $sections = [
        ['route' => 'admin.dashboard', 'icon' => 'speedometer2', 'label' => 'Tableau de bord', 'can' => null],
        ['route' => 'admin.reservations.index', 'icon' => 'journal-check', 'label' => 'Réservations', 'can' => 'reservations.view'],
        ['route' => 'admin.vehicles.index', 'icon' => 'car-front', 'label' => 'Véhicules', 'can' => 'vehicles.view'],
        ['route' => 'admin.drivers.index', 'icon' => 'person-badge', 'label' => 'Chauffeurs', 'can' => 'drivers.view'],
        ['route' => 'admin.reports.index', 'icon' => 'graph-up-arrow', 'label' => 'Rapports', 'can' => 'reports.view'],
        ['route' => 'admin.users.index', 'icon' => 'people', 'label' => 'Utilisateurs', 'can' => 'users.manage_cashiers'],
        ['route' => 'admin.audit.index', 'icon' => 'clock-history', 'label' => 'Journal d\'activité', 'can' => 'audit.view'],
        ['route' => 'admin.settings.index', 'icon' => 'gear', 'label' => 'Paramètres', 'can' => 'settings.manage'],
    ];

    $contentLinks = [
        ['route' => 'admin.faqs.index', 'label' => 'FAQ'],
        ['route' => 'admin.testimonials.index', 'label' => 'Témoignages'],
        ['route' => 'admin.messages.index', 'label' => 'Messages'],
    ];
    $contentLinks = array_filter($contentLinks, fn ($link) => Route::has($link['route']));
@endphp
<nav class="admin-sidebar bg-secondary d-flex flex-column">
    <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none fw-bold px-3 py-4 d-flex align-items-center gap-2 border-bottom border-secondary-subtle">
        @if (setting('logo'))
            <img src="{{ asset('storage/'.setting('logo')) }}" alt="{{ setting('company_name') }}" height="32">
        @else
            <i class="bi bi-car-front fs-5"></i>
        @endif
        <span>{{ setting('company_name') }}</span>
    </a>

    <ul class="nav flex-column p-2 gap-1">
        @foreach ($sections as $section)
            @continue(! Route::has($section['route']))
            @continue($section['can'] !== null && ! auth()->user()?->can($section['can']))
            <li class="nav-item">
                <a class="nav-link text-white-50 rounded {{ request()->routeIs($section['route']) || request()->routeIs($section['route'].'.*') ? 'active text-white fw-semibold' : '' }}"
                   href="{{ route($section['route']) }}">
                    <i class="bi bi-{{ $section['icon'] }} me-2"></i>{{ $section['label'] }}
                </a>
            </li>
        @endforeach

        @if ($contentLinks !== [] && auth()->user()?->can('content.manage'))
            <li class="nav-item mt-1">
                <a class="nav-link text-white-50 rounded d-flex justify-content-between align-items-center {{ request()->routeIs('admin.faqs.*') || request()->routeIs('admin.testimonials.*') || request()->routeIs('admin.messages.*') ? 'active text-white fw-semibold' : '' }}"
                   data-bs-toggle="collapse" href="#contentMenu" role="button" aria-expanded="{{ request()->routeIs('admin.faqs.*') || request()->routeIs('admin.testimonials.*') || request()->routeIs('admin.messages.*') ? 'true' : 'false' }}">
                    <span><i class="bi bi-file-richtext me-2"></i>Contenus</span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.faqs.*') || request()->routeIs('admin.testimonials.*') || request()->routeIs('admin.messages.*') ? 'show' : '' }}" id="contentMenu">
                    <ul class="nav flex-column ms-3 border-start border-secondary-subtle ps-2 mt-1">
                        @foreach ($contentLinks as $link)
                            <li class="nav-item">
                                <a class="nav-link text-white-50 small py-1 {{ request()->routeIs($link['route'].'.*') ? 'text-white fw-semibold' : '' }}" href="{{ route($link['route']) }}">{{ $link['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </li>
        @endif
    </ul>

    <div class="mt-auto p-3 small text-white-50 border-top border-secondary-subtle">
        {{ setting('company_name') }} &middot; {{ __('site.admin.title') }}
    </div>
</nav>
