@extends('layouts.public')

@section('title', __('site.nav.driver_service').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container">
            <h1 class="fw-bold">{{ __('site.driver_service.title') }}</h1>
            <p class="text-muted">{{ __('site.driver_service.subtitle') }}</p>
        </div>
    </section>
@endsection
