@extends('layouts.admin')

@section('title', __('site.admin.testimonials.title'))
@section('page_title', __('site.admin.testimonials.title'))

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">{{ __('site.admin.testimonials.add') }}</h2>
                    <form method="POST" action="{{ route('admin.testimonials.store') }}" class="d-grid gap-2">
                        @csrf
                        <input type="text" name="author_name" class="form-control" placeholder="{{ __('site.admin.testimonials.author') }}" required>
                        <input type="text" name="context" class="form-control" placeholder="{{ __('site.admin.testimonials.context') }}">
                        <textarea name="content" rows="3" class="form-control" placeholder="{{ __('site.contact.message') }}" required></textarea>
                        <select name="rating" class="form-select">
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @selected($i === 5)>{{ $i }} ★</option>
                            @endfor
                        </select>
                        <input type="number" name="sort_order" min="0" class="form-control" placeholder="{{ __('site.admin.categories.order') }}">
                        <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @forelse ($testimonials as $testimonial)
                        <div class="d-flex justify-content-between align-items-start gap-2 border-bottom p-3">
                            <div>
                                <p class="fw-semibold mb-1">
                                    {{ $testimonial->author_name }}
                                    <span class="text-warning">{{ str_repeat('★', $testimonial->rating) }}</span>
                                    @unless ($testimonial->is_visible)<span class="badge text-bg-secondary">{{ __('site.admin.testimonials.hidden') }}</span>@endunless
                                </p>
                                <p class="text-muted small mb-0">{{ Str::limit($testimonial->content, 140) }}</p>
                            </div>
                            <div class="text-nowrap">
                                <a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}" class="d-inline" onsubmit="return confirm('{{ __('site.admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted py-4 mb-0">{{ __('site.admin.testimonials.empty') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
