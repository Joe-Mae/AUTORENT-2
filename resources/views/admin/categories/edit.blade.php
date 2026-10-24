@extends('layouts.admin')

@section('title', __('site.admin.categories.edit'))
@section('page_title', __('site.admin.categories.edit'))

@section('content')
    <div class="card border-0 shadow-sm" style="max-width: 560px;">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="d-grid gap-2">
                @csrf @method('PUT')
                <label class="form-label small fw-semibold">{{ __('site.admin.categories.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
                <label class="form-label small fw-semibold">{{ __('site.vehicles.description') }}</label>
                <textarea name="description" rows="2" class="form-control">{{ old('description', $category->description) }}</textarea>
                <label class="form-label small fw-semibold">{{ __('site.admin.categories.order') }}</label>
                <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $category->sort_order) }}" class="form-control">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">{{ __('site.common.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
