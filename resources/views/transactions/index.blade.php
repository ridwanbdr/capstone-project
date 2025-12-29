@extends('layouts.main')
@section('title', 'Transaksi')

@section('content')
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <x-breadcrumb :breadcrumbs="[
                    'Home' => route('dashboard'),
                    'Transaksi' => route('transactions.index'),
                    'Daftar Transaksi' => '#'
                ]"/>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 gap-3">
            {{-- Alert Messages --}}
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

            {{-- Form Card --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header py-3">
                    <div class="d-flex align-items-center text-primary">
                        <i class="ti ti-receipt me-2 fs-5"></i>
                        <h5 class="mb-0 fw-semibold">Tambah / Edit Transaksi</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    @include('transactions.form')
                </div>
            </div>

            {{-- Table Card --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-table me-2 text-primary fs-5"></i>
                                <h5 class="mb-0 fw-semibold">Daftar Transaksi</h5>
                            </div>
                        </div>
                        <div class="col-md-6">
                            {{-- Search and Filter Form --}}
                            <form action="{{ route('transactions.index') }}" method="GET" class="d-flex flex-column gap-2">
                                <div class="d-flex gap-2">
                                    <div class="input-group flex-grow-1">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="ti ti-search"></i>
                                        </span>
                                        <input type="text" 
                                            name="search" 
                                            class="form-control border-start-0" 
                                            placeholder="Cari produk..." 
                                            value="{{ request('search') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-search"></i> Cari
                                    </button>
                                    @if(request('search') || request('status') || request('date_from') || request('date_to'))
                                        <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                                            <i class="ti ti-x"></i> Reset
                                        </a>
                                    @endif
                                </div>
                                <div class="d-flex gap-2 flex-wrap">
                                    <select name="status" class="form-select form-select-sm" style="width: auto;">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="dibayar" {{ request('status') === 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                                        <option value="belum_lunas" {{ request('status') === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                    </select>
                                    <input type="date" 
                                        name="date_from" 
                                        class="form-control form-control-sm" 
                                        placeholder="Dari Tanggal" 
                                        value="{{ request('date_from') }}"
                                        style="width: auto;">
                                    <input type="date" 
                                        name="date_to" 
                                        class="form-control form-control-sm" 
                                        placeholder="Sampai Tanggal" 
                                        value="{{ request('date_to') }}"
                                        style="width: auto;">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    {{-- Bulk Actions Bar --}}
                    <div id="bulkActionsBar" class="d-none bg-light border-bottom px-4 py-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fw-semibold text-muted">
                                <span id="selectedCount">0</span> item dipilih
                            </span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary" id="bulkUpdateBtn" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal">
                                    <i class="ti ti-edit"></i> Update Terpilih
                                </button>
                                <button type="button" class="btn btn-sm btn-success" id="bulkMarkPaidBtn">
                                    <i class="ti ti-wallet"></i> Tandai Dibayar
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" id="bulkDeleteBtn">
                                    <i class="ti ti-trash"></i> Hapus Terpilih
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearSelectionBtn">
                                    <i class="ti ti-x"></i> Batal
                                </button>
                            </div>
                        </div>
                    </div>
                    @include('transactions.table')
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Update Modal --}}
    <div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary border-0">
                    <h5 class="modal-title fw-semibold text-white" id="bulkUpdateModalLabel">Update Transaksi Terpilih</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form id="bulkUpdateForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="dibayar">Dibayar</option>
                                <option value="belum_lunas">Belum Lunas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Payment Method</label>
                            <input type="text" name="payment_method" class="form-control" placeholder="Cash, Transfer, QRIS, dll">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Paid (Rp)</label>
                            <input type="number" name="paid" class="form-control" min="0" step="0.01" placeholder="0">
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
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.transaction-checkbox');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCount = document.getElementById('selectedCount');
        const clearSelectionBtn = document.getElementById('clearSelectionBtn');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkUpdateBtn = document.getElementById('bulkUpdateBtn');
        const bulkMarkPaidBtn = document.getElementById('bulkMarkPaidBtn');
        const bulkUpdateForm = document.getElementById('bulkUpdateForm');

        function updateBulkActions() {
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            const count = selected.length;
            
            if (count > 0) {
                bulkActionsBar.classList.remove('d-none');
                selectedCount.textContent = count;
            } else {
                bulkActionsBar.classList.add('d-none');
            }
        }

        selectAll?.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActions();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkActions);
        });

        clearSelectionBtn?.addEventListener('click', function() {
            checkboxes.forEach(cb => cb.checked = false);
            selectAll.checked = false;
            updateBulkActions();
        });

        bulkDeleteBtn?.addEventListener('click', function() {
            const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            if (selected.length === 0) {
                alert('Pilih minimal 1 transaksi untuk dihapus.');
                return;
            }
            if (confirm(`Hapus ${selected.length} transaksi terpilih?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("transactions.bulkDestroy") }}';
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                    ${selected.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });

        bulkMarkPaidBtn?.addEventListener('click', function() {
            const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            if (selected.length === 0) {
                alert('Pilih minimal 1 transaksi.');
                return;
            }
            if (confirm(`Tandai ${selected.length} transaksi terpilih sebagai dibayar?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("transactions.bulkMarkPaid") }}';
                form.innerHTML = `
                    @csrf
                    @method('PUT')
                    ${selected.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });

        bulkUpdateForm?.addEventListener('submit', function(e) {
            e.preventDefault();
            const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
            if (selected.length === 0) {
                alert('Pilih minimal 1 transaksi.');
                return;
            }
            
            const formData = new FormData(this);
            selected.forEach(id => formData.append('ids[]', id));
            
            fetch('{{ route("transactions.bulkUpdate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                }
            }).then(() => {
                window.location.reload();
            });
        });
    });
    </script>
@endsection
