@extends('layouts.main')

@section('title', 'Kelola Akun')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <x-breadcrumb :breadcrumbs="[
                'Home' => route('dashboard'),
                'Kelola Akun' => '#'
            ]"/>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i>Tambah Akun
            </a>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="ti ti-alert-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="ti ti-users me-2 text-primary fs-5"></i>
                    <h5 class="mb-0 fw-semibold">Daftar Akun</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tanggal Dibuat</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->nama_lengkap ?? $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge 
                                        @if($user->role === 'admin') bg-danger
                                        @elseif($user->role === 'staff_operasional') bg-warning
                                        @elseif($user->role === 'qc_staff') bg-info
                                        @else bg-secondary
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline" class="deleteUserForm" data-username="{{ $user->username }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada data akun</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteUserForms = document.querySelectorAll('.deleteUserForm');

    deleteUserForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const username = form.getAttribute('data-username');

            Swal.fire({
                title: 'Hapus Akun?',
                html: `
                    <p>Apakah Anda yakin ingin menghapus akun:</p>
                    <p style="font-weight: bold; color: #0d6efd;">${username}</p>
                    <p style="font-size: 12px; color: #dc3545;"><i class="ti ti-alert-circle"></i> Tindakan ini tidak dapat dibatalkan</p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection

