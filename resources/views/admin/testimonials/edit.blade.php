@extends('layouts.admin')

@section('title', __('site.admin.testimonials.edit'))
@section('page_title', __('site.admin.testimonials.edit'))

@section('content')
    <div class="card border-0 shadow-sm" style="max-width: 640px;">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" class="d-grid gap-2">
                @csrf @method('PUT')
                <div class="row g-2">
                    <div class="col-7">
                        <label class="form-label small fw-semibold">{{ __('site.admin.testimonials.author') }}</label>
                        <input type="text" name="author_name" value="{{ old('author_name', $testimonial->author_name) }}" class="form-control" required>
                    </div>
                    <div class="col-5">
                        <label class="form-label small fw-semibold">{{ __('site.admin.testimonials.context') }}</label>
                        <input type="text" name="context" value="{{ old('context', $testimonial->context) }}" class="form-control">
                    </div>
                </div>
                <label class="form-label small fw-semibold">{{ __('site.contact.message') }}</label>
                <textarea name="content" rows="4" class="form-control" required>{{ old('content', $testimonial->content) }}</textarea>
                <div class="row g-2">
                    <div class="col-4">
                        <label class="form-label small fw-semibold">{{ __('site.admin.testimonials.rating') }}</label>
                        <select name="rating" class="form-select">
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @selected($testimonial->rating === $i)>{{ $i }} ★</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label small fw-semibold">{{ __('site.admin.categories.order') }}</label>
                        <input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $testimonial->sort_order) }}">
                    </div>
                    <div class="col-4 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_visible" id="testimonial-visible" value="1" @checked($testimonial->is_visible)>
                            <label class="form-check-label small" for="testimonial-visible">{{ __('site.admin.faqs.visible') }}</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">{{ __('site.common.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
