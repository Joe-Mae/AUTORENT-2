@extends('layouts.admin')

@section('title', __('site.admin.pages.title'))
@section('page_title', __('site.admin.pages.title'))

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>{{ __('site.admin.pages.add') }}</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>{{ __('site.admin.pages.slug') }}</th><th>{{ __('site.admin.pages.title_col') }}</th><th></th></tr></thead>
                <tbody>
                    @forelse ($pages as $page)
                        <tr>
                            <td><code>/fr/pages/{{ $page->slug }}</code></td>
                            <td>{{ $page->title }}</td>
                            <td class="text-end">
                                <a href="{{ route('page', ['locale' => 'fr', 'page' => $page->slug]) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-up-right"></i></a>
                                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" class="d-inline" onsubmit="return confirm('{{ __('site.admin.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">{{ __('site.admin.pages.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
