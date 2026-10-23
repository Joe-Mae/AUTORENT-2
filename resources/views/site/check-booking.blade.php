@extends('layouts.public')

@section('title', __('site.nav.check_booking').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container">
            <h1 class="fw-bold">{{ __('site.check_booking.title') }}</h1>
            <p class="text-muted">{{ __('site.check_booking.subtitle') }}</p>
        </div>
    </section>
@endsection
