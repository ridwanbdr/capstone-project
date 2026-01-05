<div class="table-responsive px-5 py-2">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-center">
            <tr>
                <th class="ps-4" style="width: 40px;">
                    <input type="checkbox" id="selectAll" class="form-check-input">
                </th>
                <th class="ps-4">
                    <span class="fw-semibold text-dark">Tanggal</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Nama Produk</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Quantity</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Ukuran</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Status</span>
                </th>
                <th class="text-end">
                    <span class="fw-semibold text-dark">Harga Satuan</span>
                </th>
                <th class="text-end pe-4">
                    <span class="fw-semibold text-dark">Total Harga</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Aksi</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            @php
                $isPending = strtolower($transaction->status ?? '') === 'pending' || strtolower($transaction->status ?? '') === 'belum_lunas';
                $isPaid = strtolower($transaction->status ?? '') === 'paid' || strtolower($transaction->status ?? '') === 'dibayar' || strtolower($transaction->status ?? '') === 'lunas';
            @endphp
            <tr class="border-bottom">
                <td class="ps-4">
                    <input type="checkbox" class="form-check-input transaction-checkbox" value="{{ $transaction->transaction_id }}" data-transaction-id="{{ $transaction->transaction_id }}">
                </td>
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        <span class="text-muted">{{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d M Y') : '-' }}</span>
                    </div>
                </td>

                <td>
                    <div class="d-flex align-items-center">
                        <span class="fw-semibold text-dark">{{ $transaction->product_name }}</span>
                    </div>
                </td>

                <td class="text-center">
                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                        {{ number_format($transaction->qty) }}
                    </span>
                </td>

                <td class="text-center">
                    <span class="text-muted">{{ strtoupper($transaction->size ?? '-') }}</span>
                </td>

                <td>
                    @if($isPending)
                        <span class="badge bg-danger text-white px-3 py-2 fw-semibold">
                            Pending
                        </span>
                    @elseif($isPaid)
                        <span class="badge bg-success text-white px-3 py-2 fw-semibold">
                            Paid
                        </span>
                    @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                            {{ $transaction->status ?? '-' }}
                        </span>
                    @endif
                </td>

                <td class="text-end">
                    <span class="fw-semibold text-success">Rp {{ number_format($transaction->price, 0, ',', '.') }}</span>
                </td>

                <td class="text-end pe-4">
                    <span class="fw-bold text-primary">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                </td>

                <td class="text-center">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 edit-transaction-btn" 
                            data-transaction-id="{{ $transaction->transaction_id }}"
                            data-id="{{ $transaction->id }}"
                            data-date="{{ optional($transaction->date)->format('Y-m-d') }}"
                            data-product-name="{{ $transaction->product_name }}"
                            data-qty="{{ $transaction->qty }}"
                            data-size="{{ $transaction->size }}"
                            data-price="{{ $transaction->price }}"
                            data-paid="{{ $transaction->paid }}"
                            data-payment-method="{{ $transaction->payment_method }}"
                            data-due-date="{{ optional($transaction->due_date_payment)->format('Y-m-d') }}"
                            data-status="{{ $transaction->status }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal">
                            <i class="ti ti-edit"></i>
                        </button>

                        {{-- Bulk mark pending for this product as paid --}}
                        @if($transaction->id)
                        <form action="{{ route('transactions.markPaidByProduct', $transaction->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Tandai semua transaksi pending untuk produk ini sebagai dibayar?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3" title="Tandai Semua Dibayar">
                                <i class="ti ti-wallet"></i>
                            </button>
                        </form>
                        @endif

                        <form action="{{ route('transactions.destroy', $transaction->transaction_id) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus transaksi ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div class="text-muted">
                        Tidak ada data transaksi
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}

@if(isset($transactions) && $transactions->hasPages())
<div class="card-footer bg-white border-top py-2">
    <div class="d-flex justify-content-center">
        {{ $transactions->links() }}
    </div>
</div>
@endif  

{{-- Reusable Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary border-0">
                <h5 class="modal-title fw-semibold text-white" id="editModalLabel">Edit Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" class="form-control" id="date" disabled>
                        <input type="hidden" name="date" id="dateHidden">
                    </div>

                    <input type="hidden" name="id" id="transactionId">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Produk</label>
                        <input type="text" name="product_name" id="productName" class="form-control" readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Quantity</label>
                            <input type="number" name="qty" id="qty" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Ukuran</label>
                            <input type="text" name="size" id="size" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Harga Satuan (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="text" name="price" id="price" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Paid (Rp)</label>
                        <input type="text" name="paid" id="paid" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method</label>
                        <input type="text" name="payment_method" id="paymentMethod" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Due Date Payment</label>
                        <input type="date" name="due_date_payment" id="dueDate" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <input type="text" name="status" id="status" class="form-control">
                    </div>

                    <div class="modal-footer border-0 pt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit modal population
    const editButtons = document.querySelectorAll('.edit-transaction-btn');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = document.getElementById('editForm');
            const transactionId = this.getAttribute('data-transaction-id');
            const id = this.getAttribute('data-id');
            
            // Update form action URL
            form.action = '{{ route("transactions.update", ":id") }}'.replace(':id', transactionId);
            
            // Populate form fields
            document.getElementById('transactionId').value = id;
            document.getElementById('date').value = this.getAttribute('data-date');
            document.getElementById('dateHidden').value = this.getAttribute('data-date');
            document.getElementById('productName').value = this.getAttribute('data-product-name');
            document.getElementById('qty').value = this.getAttribute('data-qty');
            document.getElementById('size').value = this.getAttribute('data-size');
            document.getElementById('price').value = this.getAttribute('data-price');
            document.getElementById('paid').value = this.getAttribute('data-paid');
            document.getElementById('paymentMethod').value = this.getAttribute('data-payment-method');
            document.getElementById('dueDate').value = this.getAttribute('data-due-date');
            document.getElementById('status').value = this.getAttribute('data-status');
        });
    });
    
    // Handle form submission
    const editForm = document.getElementById('editForm');
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button to prevent double submission
        const submitBtn = editForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
        
        fetch(editForm.action, {
            method: 'POST',
            body: new FormData(editForm),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                // Close modal and reload page on success
                const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                modal.hide();
                
                // Show success message and reload
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                alert('Terjadi kesalahan saat menyimpan data');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Simpan';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Simpan';
        });
    });
});
</script>

<style>
/* Compact pagination styling */
.pagination {
    margin: 0;
    gap: 0.25rem;
}

.pagination .page-link {
    padding: 0.375rem 0.625rem;
    font-size: 0.875rem;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
    color: #0d6efd;
    min-width: auto;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    font-weight: 500;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    opacity: 0.5;
}

.pagination .page-link:hover:not(.disabled) {
    background-color: #e7f1ff;
    border-color: #0d6efd;
}
</style>