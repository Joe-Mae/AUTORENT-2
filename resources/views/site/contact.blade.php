@extends('layouts.public')

@section('title', __('site.contact.title').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h1 class="fw-bold">{{ __('site.contact.title') }}</h1>
                <p class="text-muted">{{ __('site.contact.subtitle') }}</p>
            </div>

            <div class="row g-4">
                {{-- Coordonnées --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <ul class="list-unstyled d-grid gap-3 mb-0">
                                @if (setting('contact_address'))
                                    <li class="d-flex gap-3">
                                        <i class="bi bi-geo-alt fs-4 text-primary"></i>
                                        <div><strong>{{ __('site.contact.address') }}</strong><br><span class="text-muted">{{ setting('contact_address') }}</span></div>
                                    </li>
                                @endif
                                @if (setting('contact_phone'))
                                    <li class="d-flex gap-3">
                                        <i class="bi bi-telephone fs-4 text-primary"></i>
                                        <div><strong>{{ __('site.contact.phone') }}</strong><br>
                                            <a href="tel:{{ preg_replace('/\s+/', '', setting('contact_phone')) }}" class="text-decoration-none">{{ setting('contact_phone') }}</a>
                                        </div>
                                    </li>
                                @endif
                                @if (setting('contact_email'))
                                    <li class="d-flex gap-3">
                                        <i class="bi bi-envelope fs-4 text-primary"></i>
                                        <div><strong>{{ __('site.contact.email') }}</strong><br><a href="mailto:{{ setting('contact_email') }}" class="text-decoration-none">{{ setting('contact_email') }}</a></div>
                                    </li>
                                @endif
                                @if (setting('contact_hours'))
                                    <li class="d-flex gap-3">
                                        <i class="bi bi-clock fs-4 text-primary"></i>
                                        <div><strong>{{ __('site.contact.hours') }}</strong><br><span class="text-muted">{{ setting('contact_hours') }}</span></div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Formulaire --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <form method="POST" action="{{ route('contact.submit', app()->getLocale()) }}">
                                @csrf
                                {{-- Honeypot : invisible pour les humains, les robots le remplissent --}}
                                <div style="position: absolute; left: -9999px;" aria-hidden="true">
                                    <label for="website">Site web</label>
                                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label small fw-semibold">{{ __('site.contact.name') }} *</label>
                                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label small fw-semibold">{{ __('site.contact.email') }} *</label>
                                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="phone" class="form-label small fw-semibold">{{ __('site.contact.phone') }}</label>
                                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="message" class="form-label small fw-semibold">{{ __('site.contact.message') }} *</label>
                                        <textarea id="message" name="message" rows="5" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12 d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-send me-1"></i>{{ __('site.contact.send') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if (setting('map_embed_url'))
                <div class="mt-4 ratio ratio-21x9 rounded-4 overflow-hidden shadow-sm">
                    {!! setting('map_embed_url') !!}
                </div>
            @endif
        </div>
    </section>
@endsection
