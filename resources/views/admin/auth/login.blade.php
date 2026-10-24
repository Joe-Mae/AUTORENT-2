<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('site.admin.login_title') }} · {{ setting('company_name') }}</title>

    <link rel="icon" href="{{ setting('favicon') ? asset('storage/'.setting('favicon')) : asset('favicon.ico') }}">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('partials.theme')
</head>
<body class="bg-surface min-vh-100 d-flex align-items-center py-5">
    <main class="container" style="max-width: 440px;">
        <div class="card border-0 shadow-lg">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-4 mb-3" style="width: 56px; height: 56px;">
                        <i class="bi bi-car-front fs-3"></i>
                    </span>
                    <h1 class="h4 fw-bold mb-1">{{ setting('company_name') }}</h1>
                    <p class="text-muted small mb-0">{{ __('site.admin.login_subtitle') }}</p>
                </div>

                <form method="POST" action="{{ route('admin.login.attempt') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold">{{ __('site.admin.email') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label small fw-semibold">{{ __('site.admin.password') }}</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('email') is-invalid @enderror" required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small" for="remember">{{ __('site.admin.remember') }}</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-box-arrow-in-right me-1"></i> {{ __('site.admin.submit') }}
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center mt-3">
            <a href="{{ route('home', app()->getLocale()) }}" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i>{{ __('site.admin.view_site') }}
            </a>
        </p>
    </main>
</body>
</html>
