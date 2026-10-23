@extends('layouts.admin')

@section('title', __('site.admin.title'))
@section('page_title', __('site.admin.title'))

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h2 class="h4 fw-bold">{{ __('site.admin.dashboard_welcome') }}</h2>
            <p class="text-muted mb-0">{{ __('site.admin.dashboard_text') }}</p>
        </div>
    </div>
@endsection
