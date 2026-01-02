@extends('layouts.main')

@section('title', 'Detail Task')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <x-breadcrumb :breadcrumbs="[
                'Home' => route('dashboard'),
                'Kelola Task' => route('tasks.index'),
                'Detail Task' => '#'
            ]"/>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="ti ti-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-semibold">{{ $task->title }}</h5>
                        <small class="text-muted">Task ID: {{ $task->task_id }}</small>
                    </div>
                    <span class="badge 
                        @if($task->status === 'completed') bg-success
                        @elseif($task->status === 'in_progress') bg-primary
                        @elseif($task->status === 'cancelled') bg-secondary
                        @else bg-warning
                        @endif fs-6">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Ditugaskan Oleh</label>
                        <p class="mb-0">{{ $task->assignedBy->nama_lengkap ?? $task->assignedBy->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Ditugaskan Kepada</label>
                        <p class="mb-0">{{ $task->assignedTo->nama_lengkap ?? $task->assignedTo->name }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Prioritas</label>
                        <p class="mb-0">
                            <span class="badge 
                                @if($task->priority === 'high') bg-danger
                                @elseif($task->priority === 'medium') bg-warning
                                @else bg-info
                                @endif">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Jatuh Tempo</label>
                        <p class="mb-0">
                            @if($task->due_date)
                                {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y H:i') }}
                                @if($task->due_date < now() && $task->status !== 'completed')
                                <span class="badge bg-danger ms-2">Terlambat</span>
                                @endif
                            @else
                                <span class="text-muted">Tidak ditentukan</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($task->description)
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">Deskripsi</label>
                    <p class="mb-0">{{ $task->description }}</p>
                </div>
                @endif

                @if($task->completed_at)
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">Diselesaikan Pada</label>
                    <p class="mb-0">{{ \Carbon\Carbon::parse($task->completed_at)->format('d M Y H:i') }}</p>
                </div>
                @endif

                @if(!Auth::user()->isAdmin() && $task->assigned_to === Auth::id())
                <div class="border-top pt-3 mt-3">
                    <label class="form-label fw-semibold">Update Status</label>
                    <form action="{{ route('tasks.updateStatus', $task) }}" method="POST" class="d-flex gap-2" id="taskStatusForm">
                        @csrf
                        @method('PUT')
                        <select name="status" class="form-select" required>
                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $task->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check"></i> Update
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection

