@extends('layouts.admin')

@section('title', __('site.admin.messages.title'))
@section('page_title', __('site.admin.messages.title'))

@section('content')
    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>{{ __('site.contact.name') }}</th><th>{{ __('site.contact.email') }}</th><th>{{ __('site.contact.message') }}</th><th>{{ __('site.booking.status') }}</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($messages as $message)
                        <tr class="{{ $message->is_read ? '' : 'fw-semibold' }}">
                            <td>{{ $message->name }}</td>
                            <td><small>{{ $message->email }}<br>{{ $message->phone }}</small></td>
                            <td style="max-width: 320px;"><small>{{ Str::limit($message->message, 90) }}</small></td>
                            <td>
                                <span class="badge {{ $message->is_read ? 'text-bg-secondary' : 'text-bg-primary' }}">
                                    {{ $message->is_read ? __('site.admin.messages.read') : __('site.admin.messages.unread') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" class="d-inline" data-confirm="{{ __('site.admin.confirm_delete') }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ __('site.admin.messages.empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $messages->links() }}</div>
@endsection
