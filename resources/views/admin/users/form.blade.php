@php($isEdit = $user->exists)
@extends('layouts.admin')

@section('title', $isEdit ? __('site.admin.users.edit') : __('site.admin.users.add'))
@section('page_title', $isEdit ? __('site.admin.users.edit') : __('site.admin.users.add'))

@section('content')
    <div class="card border-0 shadow-sm" style="max-width: 560px;">
        <div class="card-body">
            <form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" class="d-grid gap-2">
                @csrf
                @if ($isEdit) @method('PUT') @endif

                <label class="form-label small fw-semibold">{{ __('site.admin.users.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>

                <label class="form-label small fw-semibold">{{ __('site.admin.email') }}</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>

                <label class="form-label small fw-semibold">
                    {{ __('site.admin.password') }}
                    @if ($isEdit)<small class="text-muted">({{ __('site.admin.users.password_hint') }})</small>@endif
                </label>
                <input type="password" name="password" class="form-control" {{ $isEdit ? '' : 'required' }} minlength="8" autocomplete="new-password">

                @if (! $isEdit)
                    <label class="form-label small fw-semibold">{{ __('site.admin.users.role') }}</label>
                    <select name="role" class="form-select">
                        <option value="cashier">{{ __('site.admin.roles.cashier') }}</option>
                        @if (auth()->user()->isSuperAdmin())
                            <option value="manager">{{ __('site.admin.roles.manager') }}</option>
                            <option value="super_admin">{{ __('site.admin.roles.super_admin') }}</option>
                        @endif
                    </select>
                @else
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="user-active" value="1" @checked($user->is_active ?? true)>
                        <label class="form-check-label small" for="user-active">{{ __('site.admin.users.account_active') }}</label>
                    </div>
                @endif

                <div class="d-flex gap-2">
                    <button class="btn btn-primary">{{ __('site.common.save') }}</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">{{ __('site.common.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
@endsection
