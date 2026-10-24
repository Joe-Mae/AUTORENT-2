@extends('layouts.admin')

@section('title', __('site.admin.audit.title'))
@section('page_title', __('site.admin.audit.title'))

@section('content')
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold mb-0" for="f-action">{{ __('site.admin.audit.action') }}</label>
                    <select id="f-action" name="action" class="form-select form-select-sm">
                        <option value="">{{ __('site.vehicles.filters.all') }}</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(($filters['action'] ?? null) === $action)>{{ $action }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-semibold mb-0" for="f-user">{{ __('site.admin.users.title') }}</label>
                    <select id="f-user" name="user_id" class="form-select form-select-sm">
                        <option value="">{{ __('site.vehicles.filters.all') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? null) == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary btn-sm">{{ __('site.common.search') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>{{ __('site.admin.audit.when') }}</th><th>{{ __('site.admin.users.name') }}</th><th>{{ __('site.admin.audit.action') }}</th><th>{{ __('site.admin.audit.description') }}</th></tr></thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td><small>{{ $log->created_at->format('d/m/Y H:i:s') }}</small></td>
                            <td><small>{{ $log->user?->name ?? '—' }}</small></td>
                            <td><code class="small">{{ $log->action }}</code></td>
                            <td><small>{{ $log->description }}</small></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">{{ __('site.admin.audit.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $logs->links() }}</div>
@endsection
