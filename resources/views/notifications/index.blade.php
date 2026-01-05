@extends('layouts.main')

@section('title', 'Notifikasi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <x-breadcrumb :breadcrumbs="[
                'Home' => route('dashboard'),
                'Notifikasi' => '#'
            ]"/>
            @if($notifications->where('is_read', false)->count() > 0)
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-outline-primary">
                    <i class="ti ti-check me-2"></i>Tandai Semua Dibaca
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="ti ti-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="ti ti-bell-ringing me-2 text-primary fs-5"></i>
                    <h5 class="mb-0 fw-semibold">Notifikasi</h5>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse($notifications as $notification)
                <div class="border-bottom p-3 {{ !$notification->is_read ? 'bg-light' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="mb-0 fw-semibold">{{ $notification->title }}</h6>
                                @if(!$notification->is_read)
                                <span class="badge bg-primary">Baru</span>
                                @endif
                            </div>
                            <p class="mb-2 text-muted">{{ $notification->message }}</p>
                            <small class="text-muted">
                                <i class="ti ti-clock me-1"></i>
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                            @if($notification->task)
                            <div class="mt-2">
                                <a href="{{ route('tasks.show', $notification->task) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-eye me-1"></i>Lihat Task
                                </a>
                            </div>
                            @endif
                        </div>
                        @if(!$notification->is_read)
                        <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST" class="ms-2">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Tandai sebagai dibaca">
                                <i class="ti ti-check"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="ti ti-bell-off fs-1 d-block mb-2"></i>
                    Tidak ada notifikasi
                </div>
                @endforelse
            </div>
            @if($notifications->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

