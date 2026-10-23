@extends('layouts.public')

@section('title', __('site.nav.contact').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container">
            <h1 class="fw-bold">{{ __('site.contact.title') }}</h1>
            <p class="text-muted">{{ __('site.contact.subtitle') }}</p>
        </div>
    </section>
@endsection
