@php
    /**
     * Sections du menu latéral du back-office, filtrées par permission.
     * Chaque entrée n'apparaît que si sa route existe : les phases à venir
     * ajoutent leurs routes et les entrées s'affichent sans toucher à
     * cette partial.
     */
    $sections = [
        ['route' => 'admin.dashboard', 'icon' => 'speedometer2', 'label' => 'Tableau de bord', 'can' => null],
        ['route' => 'admin.reservations.index', 'icon' => 'journal-check', 'label' => 'Réservations', 'can' => 'reservations.view'],
        ['route' => 'admin.vehicles.index', 'icon' => 'car-front', 'label' => 'Véhicules', 'can' => 'vehicles.view'],
        ['route' => 'admin.drivers.index', 'icon' => 'person-badge', 'label' => 'Chauffeurs', 'can' => 'drivers.view'],
        ['route' => 'admin.contents.index', 'icon' => 'file-richtext', 'label' => 'Contenus', 'can' => 'content.manage'],
        ['route' => 'admin.users.index', 'icon' => 'people', 'label' => 'Utilisateurs', 'can' => 'users.manage'],
        ['route' => 'admin.settings.index', 'icon' => 'gear', 'label' => 'Paramètres', 'can' => 'settings.manage'],
    ];
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
    </ul>

    <div class="mt-auto p-3 small text-white-50 border-top border-secondary-subtle">
        {{ setting('company_name') }} &middot; {{ __('site.admin.title') }}
    </div>
</nav>
