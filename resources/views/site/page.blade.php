@extends('layouts.public')

@section('title', $page->title.' · '.setting('company_name'))

@section('content')
    <section class="py-5">
        <div class="container" style="max-width: 860px;">
            <h1 class="fw-bold mb-4">{{ $page->title }}</h1>

            <div class="page-content">
                {!! $page->content !!}
            </div>
        </div>
    </section>
@endsection
