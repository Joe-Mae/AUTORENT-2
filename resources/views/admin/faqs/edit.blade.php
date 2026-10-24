@extends('layouts.admin')

@section('title', __('site.admin.faqs.edit'))
@section('page_title', __('site.admin.faqs.edit'))

@section('content')
    <div class="card border-0 shadow-sm" style="max-width: 640px;">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" class="d-grid gap-2">
                @csrf @method('PUT')
                <label class="form-label small fw-semibold">{{ __('site.admin.faqs.question') }}</label>
                <textarea name="question" rows="2" class="form-control" required>{{ old('question', $faq->question) }}</textarea>
                <label class="form-label small fw-semibold">{{ __('site.admin.faqs.answer') }}</label>
                <textarea name="answer" rows="5" class="form-control" required>{{ old('answer', $faq->answer) }}</textarea>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">{{ __('site.admin.categories.order') }}</label>
                        <input type="number" name="sort_order" min="0" class="form-control" value="{{ old('sort_order', $faq->sort_order) }}">
                    </div>
                    <div class="col-6 d-flex align-items-end">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="faq-active" value="1" @checked($faq->is_active)>
                            <label class="form-check-label small" for="faq-active">{{ __('site.admin.faqs.visible') }}</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">{{ __('site.common.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
