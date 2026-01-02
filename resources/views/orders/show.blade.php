@extends('layouts.main')

@section('title', 'Detail Order')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <x-breadcrumb :breadcrumbs="[
                'Home' => route('dashboard'),
                'Kelola Order' => route('orders.index'),
                'Detail Order' => '#'
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
                        <h5 class="mb-0 fw-semibold">{{ $order->customer_name }}</h5>
                        <small class="text-muted">Order ID: {{ $order->order_id }}</small>
                    </div>
                    <span class="badge 
                        @if($order->status === 'complete') bg-success
                        @elseif($order->status === 'process') bg-primary
                        @elseif($order->status === 'pending') bg-warning
                        @else bg-info
                        @endif fs-6">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Nama Customer</label>
                        <p class="mb-0">{{ $order->customer_name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">No. Telepon</label>
                        <p class="mb-0">{{ $order->customer_phone ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Tanggal Order</label>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-muted">Status</label>
                        <p class="mb-0">
                            <span class="badge 
                                @if($order->status === 'complete') bg-success
                                @elseif($order->status === 'process') bg-primary
                                @elseif($order->status === 'pending') bg-warning
                                @else bg-info
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($order->customer_address)
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">Alamat Customer</label>
                    <p class="mb-0">{{ $order->customer_address }}</p>
                </div>
                @endif

                @if($order->description)
                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">Deskripsi</label>
                    <p class="mb-0">{{ $order->description }}</p>
                </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-semibold text-muted">Dibuat Pada</label>
                    <p class="mb-0">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="card border shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="ti ti-package me-2 text-primary fs-5"></i>
                    <h5 class="mb-0 fw-semibold">Detail Produk</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Ukuran</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->orderItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $item->product_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $item->size }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-semibold">{{ $item->quantity }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="ti ti-package-off fs-1 d-block mb-2"></i>
                                    Tidak ada produk dalam order ini
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($order->orderItems->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="fw-semibold text-end">Total Produk:</td>
                                <td class="text-end fw-bold">
                                    {{ $order->orderItems->sum('quantity') }} item
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">
                <i class="ti ti-edit"></i> Edit Order
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection

