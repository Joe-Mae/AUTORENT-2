@extends('layouts.admin')

@section('title', __('site.admin.users.title'))
@section('page_title', __('site.admin.users.title'))

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i>{{ __('site.admin.users.add') }}</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>{{ __('site.admin.users.name') }}</th><th>{{ __('site.admin.email') }}</th><th>{{ __('site.admin.users.role') }}</th><th>{{ __('site.booking.status') }}</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge text-bg-secondary">{{ $user->roleLabel() }}</span></td>
                            <td><span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-danger' }}">{{ $user->is_active ? __('site.admin.users.active') : __('site.admin.users.disabled') }}</span></td>
                            <td class="text-end">
                                @can('update', $user)
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                @endcan
                                @can('delete', $user)
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('{{ __('site.admin.confirm_delete') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ __('site.admin.users.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
