<form action="{{ isset($qcCheck) ? route('qc_check.update', $qcCheck->qc_id) : route('qc_check.store') }}" method="POST" id="qcCheckForm">
    @csrf
    @if (isset($qcCheck))
        @method('PUT')
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
            {{-- Production Selection (only for create) --}}
            @if(!isset($qcCheck))
            <div class="mb-3">
                <label for="production_id" class="form-label fw-semibold">
                    <i class="ti ti-building-factory me-1 text-primary"></i>Pilih Production
                </label>
                <select class="form-select @error('production_id') is-invalid @enderror" 
                        id="production_id" name="production_id" onchange="filterProducts()" required>
                    <option value="">-- Pilih Production --</option>
                    @foreach($productions ?? [] as $prod)
                        <option value="{{ $prod->production_id }}" 
                                {{ old('production_id', request('production_id')) == $prod->production_id ? 'selected' : '' }}>
                            {{ $prod->production_label }} (ID: {{ $prod->production_id }})
                        </option>
                    @endforeach
                </select>
                @error('production_id')
                    <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
                <small class="text-muted">Pilih production terlebih dahulu untuk melihat produk yang tersedia</small>
            </div>
            @endif

            {{-- Product Selection --}}
            <div class="mb-3">
                <label for="product_id" class="form-label fw-semibold">
                    <i class="ti ti-package me-1 text-primary"></i>Produk
                </label>
                <select class="form-select @error('product_id') is-invalid @enderror" 
                        id="product_id" name="product_id" required onchange="updateProductInfo()">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($detailProducts as $prod)
                        <option value="{{ $prod->product_id }}" 
                                data-production-id="{{ $prod->production_id }}"
                                data-qty-unit="{{ $prod->qty_unit }}"
                                data-product-name="{{ $prod->product_name }}"
                                data-size-label="{{ $prod->size->size_label ?? '-' }}"
                                {{ old('product_id', $qcCheck->product_id ?? '') == $prod->product_id ? 'selected' : '' }}>
                            {{ $prod->product_name }} 
                            @if($prod->size)
                                - {{ $prod->size->size_label }}
                            @endif
                            (Qty: {{ number_format($prod->qty_unit) }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
                <div class="mt-2">
                    <small class="text-muted" id="product-info">
                        @if(isset($qcCheck) && $qcCheck->detailProduct)
                            <div class="card bg-light border-0 p-2 mt-2">
                                <small class="d-block"><strong>Total Unit Produk:</strong> <span class="text-primary fw-bold">{{ number_format($qcCheck->detailProduct->qty_unit) }}</span></small>
                                <small class="d-block"><strong>Size:</strong> {{ $qcCheck->detailProduct->size->size_label ?? '-' }}</small>
                            </div>
                        @endif
                    </small>
                </div>
            </div>

            {{-- Date --}}
            <div class="mb-3">
                <label for="date" class="form-label fw-semibold">
                    <i class="ti ti-calendar-event me-1 text-primary"></i>Tanggal
                </label>
                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                       id="date" name="date" value="{{ old('date', $qcCheck->date ?? date('Y-m-d')) }}" required>
                @error('date')
                    <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="col-lg-6">
            {{-- Qty Passed --}}
            <div class="mb-3">
                <label for="qty_passed" class="form-label fw-semibold">
                    <i class="ti ti-check me-1 text-success"></i>Qty Lolos
                </label>
                <input type="number" class="form-control @error('qty_passed') is-invalid @enderror" 
                       id="qty_passed" name="qty_passed" value="{{ old('qty_passed', $qcCheck->qty_passed ?? 0) }}" 
                       min="0" required onchange="validateTotal()">
                @error('qty_passed')
                    <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            {{-- Qty Reject --}}
            <div class="mb-3">
                <label for="qty_reject" class="form-label fw-semibold">
                    <i class="ti ti-x me-1 text-danger"></i>Qty Reject
                </label>
                <input type="number" class="form-control @error('qty_reject') is-invalid @enderror" 
                       id="qty_reject" name="qty_reject" value="{{ old('qty_reject', $qcCheck->qty_reject ?? 0) }}" 
                       min="0" required onchange="validateTotal()">
                @error('qty_reject')
                    <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>

            {{-- Total Qty Info --}}
            <div class="mb-3">
                <div class="card bg-light border-0 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted fw-semibold">Total:</small>
                        <span class="fw-bold fs-5" id="total-qty">0</span>
                    </div>
                    <div id="total-warning" class="small"></div>
                </div>
            </div>

            {{-- QC Checker --}}
            <div class="mb-3">
                <label for="qc_checker" class="form-label fw-semibold">
                    <i class="ti ti-user me-1 text-primary"></i>QC Checker <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control @error('qc_checker') is-invalid @enderror" 
                       id="qc_checker" name="qc_checker" value="{{ old('qc_checker', $qcCheck->qc_checker ?? '') }}" 
                       placeholder="Masukkan nama QC Checker" required>
                @error('qc_checker')
                    <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Reject Reason --}}
    <div class="mb-3">
        <label for="reject_reason" class="form-label fw-semibold">
            <i class="ti ti-note me-1 text-primary"></i>Keterangan (Opsional)
        </label>
        <textarea class="form-control @error('reject_reason') is-invalid @enderror" 
                  id="reject_reason" name="reject_reason" rows="3"
                  placeholder="Masukkan keterangan reject atau catatan lainnya">{{ old('reject_reason', $qcCheck->reject_reason ?? '') }}</textarea>
        @error('reject_reason')
            <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
        @enderror
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('qc_check.index') }}" class="btn btn-outline-secondary px-4">
            <i class="ti ti-arrow-left"></i> Kembali
        </a>
        <button type="submit" class="btn btn-primary px-5 shadow-sm">
            <i class="ti ti-check"></i> {{ isset($qcCheck) ? 'Perbarui' : 'Simpan' }}
        </button>
    </div>
</form>

@push('scripts')
<script>
const allProducts = @json($detailProducts ?? []);
const productionSelect = document.getElementById('production_id');
const productSelect = document.getElementById('product_id');

// Filter products based on selected production
function filterProducts() {
    if (!productionSelect) return;
    
    const selectedProductionId = productionSelect.value;
    const options = productSelect.querySelectorAll('option');
    
    // Show/hide options based on production_id
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block'; // Always show placeholder
        } else {
            const productProductionId = option.getAttribute('data-production-id');
            if (selectedProductionId === '' || productProductionId === selectedProductionId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        }
    });
    
    // Reset product selection if current selection is hidden
    const selectedOption = productSelect.options[productSelect.selectedIndex];
    if (selectedOption && selectedOption.style.display === 'none') {
        productSelect.value = '';
        updateProductInfo();
    }
}

// Update product info display
function updateProductInfo() {
    const select = document.getElementById('product_id');
    const selectedOption = select.options[select.selectedIndex];
    const qtyUnit = selectedOption.getAttribute('data-qty-unit');
    const productName = selectedOption.getAttribute('data-product-name');
    const sizeLabel = selectedOption.getAttribute('data-size-label');
    
    const productInfoDiv = document.getElementById('product-info');
    
    if (qtyUnit && productName) {
        productInfoDiv.innerHTML = `
            <div class="card bg-light border-0 p-2 mt-2">
                <small class="d-block"><strong>Total Unit Produk:</strong> <span class="text-primary fw-bold">${parseInt(qtyUnit).toLocaleString()}</span></small>
                <small class="d-block"><strong>Size:</strong> ${sizeLabel || '-'}</small>
            </div>
        `;
    } else {
        productInfoDiv.innerHTML = '';
    }
    
    validateTotal();
}

// Validate total qty
function validateTotal() {
    const qtyPassed = parseInt(document.getElementById('qty_passed').value) || 0;
    const qtyReject = parseInt(document.getElementById('qty_reject').value) || 0;
    const total = qtyPassed + qtyReject;
    
    const select = document.getElementById('product_id');
    const selectedOption = select.options[select.selectedIndex];
    const productQtyUnit = parseInt(selectedOption.getAttribute('data-qty-unit')) || 0;
    
    document.getElementById('total-qty').textContent = total.toLocaleString();
    
    const warningDiv = document.getElementById('total-warning');
    
    if (productQtyUnit > 0) {
        if (total > productQtyUnit) {
            warningDiv.innerHTML = `<span class="text-danger"><i class="ti ti-alert-triangle"></i> Total melebihi kapasitas produk (${productQtyUnit.toLocaleString()} unit)!</span>`;
            warningDiv.className = 'small text-danger';
        } else if (total < productQtyUnit) {
            const diff = productQtyUnit - total;
            warningDiv.innerHTML = `<span class="text-warning"><i class="ti ti-alert-triangle"></i> Total kurang ${diff.toLocaleString()} unit (harus sama dengan ${productQtyUnit.toLocaleString()})</span>`;
            warningDiv.className = 'small text-warning';
        } else {
            warningDiv.innerHTML = `<span class="text-success"><i class="ti ti-check"></i> Total sesuai dengan unit produk</span>`;
            warningDiv.className = 'small text-success';
        }
    } else {
        warningDiv.innerHTML = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (productionSelect) {
        productionSelect.addEventListener('change', filterProducts);
        filterProducts(); // Initial filter
    } else {
        // If no production select (edit mode), show all products
        const options = productSelect.querySelectorAll('option');
        options.forEach(option => {
            option.style.display = 'block';
        });
    }
    
    updateProductInfo();
    validateTotal();
});
</script>
@endpush
