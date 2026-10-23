@extends('layouts.public')

@section('title', __('site.nav.vehicles').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container">
            <h1 class="fw-bold">{{ __('site.vehicles.title') }}</h1>
            <p class="text-muted">{{ __('site.vehicles.subtitle') }}</p>

            {{-- Filtres et liste des véhicules : alimentés en phase 4 --}}
            <div class="alert alert-light border" role="status">
                {{ __('site.vehicles.coming_soon') }}
            </div>
        </div>
    </section>
@endsection
