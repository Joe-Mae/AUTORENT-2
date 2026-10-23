@extends('layouts.public')

@section('title', __('site.nav.faq').' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container">
            <h1 class="fw-bold">{{ __('site.faq.title') }}</h1>
            <p class="text-muted">{{ __('site.faq.subtitle') }}</p>
        </div>
    </section>
@endsection
