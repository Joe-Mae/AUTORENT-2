@extends('layouts.public')

@section('title', __('site.common.not_found_title'))

@section('content')
    <section class="py-5 text-center">
        <div class="container py-5">
            <p class="display-1 fw-bold text-primary">404</p>
            <h1 class="h3 fw-bold mb-3">{{ __('site.common.not_found_title') }}</h1>
            <p class="text-muted mb-4">{{ __('site.common.not_found_text') }}</p>
            <a href="{{ route('home', app()->getLocale()) }}" class="btn btn-primary">{{ __('site.common.back_home') }}</a>
        </div>
    </section>
@endsection
