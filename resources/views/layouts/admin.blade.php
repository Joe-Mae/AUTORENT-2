<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('site.admin.title')) · {{ setting('company_name') }}</title>

    <link rel="icon" href="{{ setting('favicon') ? asset('storage/'.setting('favicon')) : asset('favicon.ico') }}">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('partials.theme')
</head>
<body class="bg-surface">
    <div class="admin-shell d-flex min-vh-100">
        <div class="collapse d-lg-block admin-sidebar-collapse" id="adminSidebarMobile">
            @include('admin.partials.sidebar')
        </div>

        <div class="flex-grow-1 d-flex flex-column min-w-0">
            <header class="bg-white border-bottom px-3 px-md-4 py-3 d-flex align-items-center justify-content-between sticky-top">
                <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#adminSidebarMobile" aria-controls="adminSidebarMobile" aria-expanded="false" aria-label="{{ __('site.common.menu') }}">
                    <i class="bi bi-list"></i>
                </button>

                <h1 class="h6 mb-0 fw-semibold text-secondary d-none d-sm-block">@yield('page_title', __('site.admin.title'))</h1>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary">{{ setting('company_name') }}</span>
                </div>
            </header>

            <main class="p-3 p-md-4">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
