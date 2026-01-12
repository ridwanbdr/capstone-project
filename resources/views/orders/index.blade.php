@extends('layouts.main')

@section('title', 'Kelola Order')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <x-breadcrumb :breadcrumbs="[
                'Home' => route('dashboard'),
                'Kelola Order' => '#'
            ]"/>
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-2"></i>Tambah Order
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

        <div class="card border shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="ti ti-shopping-cart me-2 text-primary fs-5"></i>
                    <h5 class="mb-0 fw-semibold">Daftar Order</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Nama Customer</th>
                                <th>No. Telepon</th>
                                <th>Tanggal Order</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->order_id }}</td>
                                <td>
                                    <strong>{{ $order->customer_name }}</strong>
                                    @if($order->description)
                                    <br><small class="text-muted">{{ Str::limit($order->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $order->customer_phone ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge 
                                        @if($order->status === 'complete') bg-success
                                        @elseif($order->status === 'process') bg-primary
                                        @elseif($order->status === 'pending') bg-warning
                                        @else bg-info
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-outline-info">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display:inline" class="deleteOrderForm" data-customer-name="{{ $order->customer_name }}">
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
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada order</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white border-top">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteOrderForms = document.querySelectorAll('.deleteOrderForm');

    deleteOrderForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const customerName = form.getAttribute('data-customer-name');

            Swal.fire({
                title: 'Hapus Order?',
                html: `
                    <p>Apakah Anda yakin ingin menghapus order dari customer:</p>
                    <p style="font-weight: bold; color: #0d6efd;">${customerName}</p>
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

