@extends('layouts.admin')

@section('title', __('site.admin.faqs.title'))
@section('page_title', __('site.admin.faqs.title'))

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">{{ __('site.admin.faqs.add') }}</h2>
                    <form method="POST" action="{{ route('admin.faqs.store') }}" class="d-grid gap-2">
                        @csrf
                        <textarea name="question" rows="2" class="form-control" placeholder="{{ __('site.admin.faqs.question') }}" required></textarea>
                        <textarea name="answer" rows="4" class="form-control" placeholder="{{ __('site.admin.faqs.answer') }}" required></textarea>
                        <input type="number" name="sort_order" min="0" class="form-control" placeholder="{{ __('site.admin.categories.order') }}">
                        <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @forelse ($faqs as $faq)
                        <div class="d-flex justify-content-between align-items-start gap-2 border-bottom p-3">
                            <div>
                                <p class="fw-semibold mb-1">{{ $faq->question }} <span class="badge text-bg-secondary">#{{ $faq->sort_order }}</span></p>
                                <p class="text-muted small mb-1">{{ Str::limit($faq->answer, 140) }}</p>
                                @unless ($faq->is_active)<span class="badge text-bg-secondary">{{ __('site.admin.faqs.hidden') }}</span>@endunless
                            </div>
                            <div class="text-nowrap">
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="d-inline" onsubmit="return confirm('{{ __('site.admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted py-4 mb-0">{{ __('site.admin.faqs.empty') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
