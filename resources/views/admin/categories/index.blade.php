@extends('layouts.admin')

@section('title', __('site.admin.categories.title'))
@section('page_title', __('site.admin.categories.title'))

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6 fw-bold mb-3">{{ __('site.admin.categories.add') }}</h2>
                    <form method="POST" action="{{ route('admin.categories.store') }}" class="d-grid gap-2">
                        @csrf
                        <input type="text" name="name" class="form-control" placeholder="{{ __('site.admin.categories.name') }}" required>
                        <textarea name="description" rows="2" class="form-control" placeholder="{{ __('site.vehicles.description') }}"></textarea>
                        <input type="number" name="sort_order" class="form-control" placeholder="{{ __('site.admin.categories.order') }}" min="0">
                        <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>{{ __('site.admin.categories.name') }}</th><th>{{ __('site.nav.vehicles') }}</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td><strong>{{ $category->name }}</strong> <small class="text-muted d-block">{{ $category->description }}</small></td>
                                    <td>{{ $category->vehicles_count }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline" data-confirm="{{ __('site.admin.confirm_delete') }}">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">{{ __('site.admin.categories.empty') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
