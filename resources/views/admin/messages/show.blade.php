@extends('layouts.admin')

@section('title', __('site.admin.messages.title'))
@section('page_title', $message->name)

@section('content')
    <div class="card border-0 shadow-sm" style="max-width: 720px;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1">{{ $message->name }}</h2>
                    <p class="text-muted small mb-0">
                        {{ $message->email }} @if ($message->phone) · {{ $message->phone }} @endif
                        · {{ $message->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.messages.toggle-read', $message) }}">
                    @csrf
                    <button class="btn btn-sm {{ $message->is_read ? 'btn-outline-secondary' : 'btn-primary' }}">
                        <i class="bi bi-check2-all me-1"></i>{{ $message->is_read ? __('site.admin.messages.mark_unread') : __('site.admin.messages.mark_read') }}
                    </button>
                </form>
            </div>

            <div class="bg-surface rounded-3 p-3">
                {!! nl2br(e($message->message)) !!}
            </div>

            <div class="mt-3">
                <a href="mailto:{{ $message->email }}" class="btn btn-primary btn-sm"><i class="bi bi-reply me-1"></i>{{ __('site.admin.messages.reply') }}</a>
                <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('site.common.cancel') }}</a>
            </div>
        </div>
    </div>
@endsection
