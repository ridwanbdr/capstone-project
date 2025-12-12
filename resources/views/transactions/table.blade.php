<div class="table-responsive px-5 py-2">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-center">
            <tr>
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
            <tr class="border-bottom">
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        <span class="text-muted">{{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d M Y') : '-' }}</span>
                    </div>
                </td>

                <td>
                    <div class="d-flex align-items-center">
                        <span class="fw-semibold">{{ $transaction->product_name }}</span>
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
                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                        {{ $transaction->status ?? '-' }}
                    </span>
                </td>

                <td class="text-end">
                    <span class="fw-semibold text-success">Rp {{ number_format($transaction->price, 0, ',', '.') }}</span>
                </td>

                <td class="text-end pe-4">
                    <span class="fw-bold text-primary">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                </td>

                <td class="text-center">
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editModal{{ $transaction->transaction_id }}">
                            <i class="ti ti-edit"></i>
                        </button>

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

            {{-- Edit Modal --}}
            <div class="modal fade" id="editModal{{ $transaction->transaction_id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $transaction->transaction_id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-primary border-0">
                            <h5 class="modal-title fw-semibold text-white" id="editModalLabel{{ $transaction->transaction_id }}">Edit Transaksi</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form action="{{ route('transactions.update', $transaction->transaction_id) }}" method="POST" id="editForm{{ $transaction->transaction_id }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal</label>
                                    <!-- tampilkan tanggal tidak bisa diedit, tapi tetap kirimkan nilainya lewat hidden input -->
                                    <input type="date" class="form-control" value="{{ old('date', optional($transaction->date)->format('Y-m-d')) }}" disabled>
                                    <input type="hidden" name="date" value="{{ old('date', optional($transaction->date)->format('Y-m-d')) }}">
                                </div>

                                <!-- keep avail_stock id as hidden (not editable) -->
                                <input type="hidden" name="id" value="{{ $transaction->id }}">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Produk</label>
                                    <input type="text" name="product_name" class="form-control" value="{{ old('product_name', $transaction->product_name) }}" readonly>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Quantity</label>
                                        <input type="number" name="qty" class="form-control" min="1" value="{{ old('qty', $transaction->qty) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Ukuran</label>
                                        <input type="text" name="size" class="form-control" value="{{ old('size', $transaction->size) }}" readonly>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Harga Satuan (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="text" name="price" class="form-control" value="{{ old('price', $transaction->price) }}" readonly>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Paid (Rp)</label>
                                    <input type="text" name="paid" class="form-control" value="{{ old('paid', $transaction->paid) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Payment Method</label>
                                    <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method', $transaction->payment_method) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Due Date Payment</label>
                                    <input type="date" name="due_date_payment" class="form-control" value="{{ old('due_date_payment', optional($transaction->due_date_payment)->format('Y-m-d')) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Status</label>
                                    <input type="text" name="status" class="form-control" value="{{ old('status', $transaction->status) }}">
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

            @empty
            <tr>
                <td colspan="8" class="text-center py-5">
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
<div class="card-footer bg-white border-top py-3">
    <div class="d-flex justify-content-center">
        {{ $transactions->links() }}
    </div>
</div>
@endif
