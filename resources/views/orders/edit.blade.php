@extends('layouts.main')

@section('title', 'Edit Order')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <x-breadcrumb :breadcrumbs="[
                'Home' => route('dashboard'),
                'Kelola Order' => route('orders.index'),
                'Detail Order' => route('orders.show', $order),
                'Edit Order' => '#'
            ]"/>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center">
                    <i class="ti ti-edit me-2 text-primary fs-5"></i>
                    <h5 class="mb-0 fw-semibold">Edit Order</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('orders.update', $order) }}" method="POST" id="editOrderForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Customer <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" 
                            value="{{ old('customer_name', $order->customer_name) }}" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. Telepon</label>
                            <input type="text" name="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" 
                                value="{{ old('customer_phone', $order->customer_phone) }}">
                            @error('customer_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Order <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" 
                                value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}" required>
                            @error('order_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Customer</label>
                        <textarea name="customer_address" class="form-control @error('customer_address') is-invalid @enderror" rows="3">{{ old('customer_address', $order->customer_address) }}</textarea>
                        @error('customer_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $order->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <!-- Order Items Section -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="form-label fw-semibold mb-0">Detail Produk <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                                <i class="ti ti-plus me-1"></i>Tambah Produk
                            </button>
                        </div>
                        <div id="itemsContainer">
                            <!-- Items will be added here dynamically -->
                        </div>
                        @error('items')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                        @error('items.*')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="incoming" {{ old('status', $order->status) === 'incoming' ? 'selected' : '' }}>Incoming</option>
                            <option value="process" {{ old('status', $order->status) === 'process' ? 'selected' : '' }}>Process</option>
                            <option value="pending" {{ old('status', $order->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="complete" {{ old('status', $order->status) === 'complete' ? 'selected' : '' }}>Complete</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Update Order
                        </button>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemBtn = document.getElementById('addItemBtn');
    let itemIndex = 0;

    const sizes = @json($sizes ?? []);
    const existingItems = @json($order->orderItems ?? []);

    function createItemRow(index, itemData = null) {
        const row = document.createElement('div');
        row.className = 'item-row mb-3 p-3 border rounded';
        row.dataset.index = index;
        
        row.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-muted">Produk #${index + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn">
                    <i class="ti ti-trash"></i> Hapus
                </button>
            </div>
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="items[${index}][product_name]" 
                        class="form-control form-control-sm" 
                        placeholder="Nama produk" 
                        value="${itemData ? itemData.product_name : ''}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Ukuran <span class="text-danger">*</span></label>
                    <select name="items[${index}][size]" class="form-select form-select-sm" required>
                        <option value="">Pilih Ukuran</option>
                        ${sizes.map(size => {
                            const selected = itemData && itemData.size === size.size_label ? 'selected' : '';
                            return `<option value="${size.size_label}" ${selected}>${size.size_label}</option>`;
                        }).join('')}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="items[${index}][quantity]" 
                        class="form-control form-control-sm" 
                        placeholder="Qty" 
                        value="${itemData ? itemData.quantity : ''}" 
                        min="1" required>
                </div>
            </div>
        `;

        // Add remove functionality
        const removeBtn = row.querySelector('.remove-item-btn');
        removeBtn.addEventListener('click', function() {
            if (itemsContainer.children.length > 1) {
                row.remove();
                updateItemNumbers();
            } else {
                alert('Minimal harus ada 1 produk dalam order.');
            }
        });

        return row;
    }

    function updateItemNumbers() {
        const rows = itemsContainer.querySelectorAll('.item-row');
        rows.forEach((row, index) => {
            row.dataset.index = index;
            const header = row.querySelector('h6');
            if (header) {
                header.textContent = `Produk #${index + 1}`;
            }
            // Update input names
            const inputs = row.querySelectorAll('input, select');
            inputs.forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
    }

    addItemBtn.addEventListener('click', function() {
        const row = createItemRow(itemIndex);
        itemsContainer.appendChild(row);
        itemIndex++;
    });

    // Add existing items, old input (on validation error), or create initial empty row
    const oldItems = @json(old('items', []));
    const itemsToLoad = oldItems.length > 0 ? oldItems : existingItems;
    
    if (itemsToLoad.length > 0) {
        itemsToLoad.forEach((item, index) => {
            const row = createItemRow(index, item);
            itemsContainer.appendChild(row);
            itemIndex++;
        });
    } else {
        const initialRow = createItemRow(itemIndex);
        itemsContainer.appendChild(initialRow);
        itemIndex++;
    }
});
</script>
@endpush

{{-- SweetAlert CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#0d6efd',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route('orders.index') }}';
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Gagal!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'OK'
        });
    @endif

    const form = document.getElementById('editOrderForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const customerName = document.querySelector('input[name="customer_name"]').value;

        Swal.fire({
            title: 'Konfirmasi Update Order',
            html: `<p>Apakah Anda yakin ingin mengupdate order untuk customer <strong>${customerName}</strong>?</p>`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endsection

