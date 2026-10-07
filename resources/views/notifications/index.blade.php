@extends('layouts.admin')

@section('title', 'Notifikasi')
@section('heading', 'Notifikasi')

@section('content')
    <div class="mb-3">
        <form method="POST" action="{{ route('notifications.read-all') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">Tandai semua sudah dibaca</button>
        </form>
    </div>

    <div class="list-group">
        @forelse ($notifications as $notification)
            <a href="{{ route('notifications.open', $notification) }}"
               class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'list-group-item-warning' }}">
                <div class="d-flex justify-content-between">
                    <strong>{{ $notification->title }}</strong>
                    <small class="text-muted">{{ $notification->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="small">{{ $notification->message }}</div>
            </a>
        @empty
            <div class="list-group-item text-center text-muted py-4">Belum ada notifikasi.</div>
        @endforelse
    </div>

    <div class="mt-3">{{ $notifications->links('pagination::bootstrap-5') }}</div>
@endsection
