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
                        {{-- Edit now navigates to dedicated edit page --}}
                        <a href="{{ route('transactions.edit', $transaction->transaction_id) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="ti ti-edit"></i>
                        </a>

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