@extends('layouts.main')

@section('title', 'Kelola Task')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <x-breadcrumb :breadcrumbs="[
                'Home' => route('dashboard'),
                'Kelola Task' => '#'
            ]"/>
            @if(Auth::user()->isAdmin())
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i>Tambah Task
            </a>
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
                    <i class="ti ti-clipboard-list me-2 text-primary fs-5"></i>
                    <h5 class="mb-0 fw-semibold">{{ Auth::user()->isAdmin() ? 'Daftar Task Karyawan' : 'My Task' }}</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Task ID</th>
                                <th>Judul</th>
                                @if(Auth::user()->isAdmin())
                                <th>Ditugaskan Kepada</th>
                                @else
                                <th>Ditugaskan Oleh</th>
                                @endif
                                <th>Prioritas</th>
                                <th>Status</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                            <tr>
                                <td>{{ $task->task_id }}</td>
                                <td>
                                    <strong>{{ $task->title }}</strong>
                                    @if($task->description)
                                    <br><small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                                    @endif
                                </td>
                                @if(Auth::user()->isAdmin())
                                <td>{{ $task->assignedTo->nama_lengkap ?? $task->assignedTo->name }}</td>
                                @else
                                <td>{{ $task->assignedBy->nama_lengkap ?? $task->assignedBy->name }}</td>
                                @endif
                                <td>
                                    <span class="badge 
                                        @if($task->priority === 'high') bg-danger
                                        @elseif($task->priority === 'medium') bg-warning
                                        @else bg-info
                                        @endif">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($task->status === 'completed') bg-success
                                        @elseif($task->status === 'in_progress') bg-primary
                                        @elseif($task->status === 'cancelled') bg-secondary
                                        @else bg-warning
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($task->due_date)
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                                        @if($task->due_date < now() && $task->status !== 'completed')
                                        <br><small class="text-danger">Terlambat</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        @if(!Auth::user()->isAdmin() && $task->assigned_to === Auth::id() && $task->status !== 'completed')
                                        <form action="{{ route('tasks.updateStatus', $task) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as Completed" onclick="return confirm('Tandai task sebagai selesai?');">
                                                <i class="ti ti-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @if(Auth::user()->isAdmin())
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus task ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ Auth::user()->isAdmin() ? '7' : '7' }}" class="text-center py-4 text-muted">Tidak ada task</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

