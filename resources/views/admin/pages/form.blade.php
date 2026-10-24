@php($isEdit = $page->exists)
@extends('layouts.admin')

@section('title', $isEdit ? __('site.admin.pages.edit') : __('site.admin.pages.add'))
@section('page_title', $isEdit ? __('site.admin.pages.edit') : __('site.admin.pages.add'))

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ $isEdit ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="d-grid gap-2">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                @if (! $isEdit)
                    <label class="form-label small fw-semibold">{{ __('site.admin.pages.slug') }} ({{ __('site.admin.pages.slug_hint') }})</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" required pattern="[a-z0-9\-]+">
                @endif

                <label class="form-label small fw-semibold">{{ __('site.admin.pages.title_col') }}</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" class="form-control" required>

                <label class="form-label small fw-semibold">{{ __('site.admin.pages.content') }} (HTML)</label>
                <textarea name="content" rows="14" class="form-control font-monospace" style="font-size: 13px;">{{ old('content', $page->content) }}</textarea>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">{{ __('site.common.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
