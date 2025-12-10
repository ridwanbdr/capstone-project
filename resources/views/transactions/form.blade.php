@php $isEdit = isset($transaction); @endphp

<form action="{{ $isEdit ? route('transactions.update', $transaction->transaction_id) : route('transactions.store') }}" method="POST" id="transactionForm">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="date" class="form-label fw-semibold">Tanggal</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ old('date', $isEdit ? optional($transaction->date)->format('Y-m-d') : date('Y-m-d')) }}" required>
            </div>

            <div class="mb-3">
                <label for="id" class="form-label fw-semibold">Nama Produk</label>
                <select name="id" id="id" class="form-select" required>
                    <option value="">-- Pilih Stok --</option>
                    @foreach($availStocks as $a)
                        @php $sizeLabel = isset($sizes) ? ($sizes->firstWhere('size_id', $a->size_id)->size_label ?? '') : ''; @endphp
                        <option value="{{ $a->id }}"
                                data-price="{{ $a->price_unit }}"
                                data-product-name="{{ $a->product_name }}"
                                data-size-label="{{ $sizeLabel }}"
                                {{ old('id', $isEdit ? $transaction->id : request('id')) == $a->id ? 'selected' : '' }}>
                            {{ $a->product_name ?? "#{$a->id}" }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="product_name" id="product_name" value="{{ old('product_name', $isEdit ? ($transaction->product_name ?? '') : '') }}">
            </div>

            <div class="mb-3">
                <label for="size" class="form-label fw-semibold">Ukuran</label>
                <select name="size" id="size" class="form-select" required>
                    <option value="">-- Pilih Ukuran --</option>
                    @if(isset($sizes) && $sizes->count())
                        @foreach($sizes->sortBy('size_id') as $s)
                            <option value="{{ $s->size_label }}" {{ old('size', $isEdit ? $transaction->size : '') == $s->size_label ? 'selected' : '' }}>
                                {{ $s->size_label }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="row gx-3">
                <div class="col-md-4 mb-3">
                    <label for="qty" class="form-label fw-semibold">Quantity</label>
                    <input type="number" name="qty" id="qty" class="form-control" value="{{ old('qty', $isEdit ? $transaction->qty : '') }}" min="1" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="price" class="form-label fw-semibold">Harga Satuan (Rp)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">Rp</span>
                        <input type="text" name="price" id="price" class="form-control" placeholder="0" value="{{ old('price', $isEdit ? $transaction->price : '') }}" readonly>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Total</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">Rp</span>
                        <input type="text" id="totalDisplay" class="form-control bg-info bg-opacity-10 text-primary fw-bold" value="0" readonly>
                    </div>
                    <input type="hidden" name="total" id="total" value="{{ old('total', $isEdit ? ($transaction->total ?? 0) : 0) }}">
                </div>
            </div>
        </div>

        <!-- right column (payment/status) with wrappers for toggle -->
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="is_paid" class="form-label fw-semibold">Status Pembayaran</label>
                <select name="is_paid" id="is_paid" class="form-select" required>
                    <option value="belum_lunas" {{ old('is_paid', ($isEdit && ($transaction->unpaid_amount ?? 0) > 0) ? 'belum_lunas' : 'lunas') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="lunas" {{ old('is_paid', ($isEdit && ($transaction->unpaid_amount ?? 0) > 0) ? 'belum_lunas' : 'lunas') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>

            <div class="mb-3" id="paidWrapper">
                <label for="paid" class="form-label fw-semibold">Paid (Rp)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">Rp</span>
                    <input type="text" name="paid" id="paid" class="form-control" placeholder="0" value="{{ old('paid', $isEdit ? $transaction->paid : '') }}">
                </div>
            </div>

            <div class="mb-3 d-none" id="unpaidWrapper">
                <label class="form-label fw-semibold">Unpaid (Rp)</label>
                <div class="input-group">
                    <span class="input-group-text bg-light">Rp</span>
                    <input type="text" id="unpaidDisplay" class="form-control" readonly>
                </div>
                <input type="hidden" name="unpaid_amount" id="unpaid_amount" value="{{ old('unpaid_amount', $isEdit ? ($transaction->unpaid_amount ?? 0) : 0) }}">
            </div>

            <div class="mb-3" id="paymentMethodWrapper">
                <label for="payment_method" class="form-label fw-semibold">Payment Method</label>
                <input type="text" name="payment_method" id="payment_method" class="form-control" value="{{ old('payment_method', $isEdit ? $transaction->payment_method : '') }}">
            </div>

            <div class="mb-3 d-none" id="dueDateWrapper">
                <label for="due_date_payment" class="form-label fw-semibold">Due Date Payment</label>
                <input type="date" name="due_date_payment" id="due_date_payment" class="form-control" value="{{ old('due_date_payment', $isEdit ? optional($transaction->due_date_payment)->format('Y-m-d') : '') }}">
                <div id="dueDateError" class="text-danger small mt-1 d-none"><i class="ti ti-alert-circle me-1"></i>Due date tidak boleh sebelum tanggal transaksi.</div>
            </div>

            <div class="mb-3" id="statusWrapper">
                <label for="status" class="form-label fw-semibold">Status</label>
                <input type="text" name="status" id="status" class="form-control" value="{{ old('status', $isEdit ? $transaction->status : '') }}">
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-outline-secondary px-4">Reset</button>
            <button type="submit" class="btn btn-primary px-5 shadow-sm">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Transaksi' }}</button>
        </div>
    </div>
</form>

<?php
    // prepare JS data
    $availStocksJson = $availStocks->map(function($a){
        return [
            'id' => $a->id,
            'product_name' => $a->product_name,
            'size_id' => $a->size_id,
            'price_unit' => $a->price_unit,
        ];
    })->values()->toJson();

    $sizesJson = isset($sizes)
        ? $sizes->map(function($s){
            return ['size_id' => $s->size_id, 'size_label' => $s->size_label];
        })->values()->toJson()
        : '[]';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const availSelect = document.getElementById('id');
    const priceInput = document.getElementById('price');
    const sizeSelect = document.getElementById('size');
    const productNameInput = document.getElementById('product_name');
    const qtyInput = document.getElementById('qty');
    const totalDisplay = document.getElementById('totalDisplay');
    const totalInput = document.getElementById('total');

    const isPaidSelect = document.getElementById('is_paid');
    const paidWrapper = document.getElementById('paidWrapper');
    const paidInput = document.getElementById('paid');
    const unpaidWrapper = document.getElementById('unpaidWrapper');
    const unpaidDisplay = document.getElementById('unpaidDisplay');
    const unpaidInput = document.getElementById('unpaid_amount');
    const paymentMethodWrapper = document.getElementById('paymentMethodWrapper');
    const dueDateWrapper = document.getElementById('dueDateWrapper');
    const dueDateInput = document.getElementById('due_date_payment');
    const dueDateError = document.getElementById('dueDateError');
    const statusWrapper = document.getElementById('statusWrapper');

    // data for lookup
    const availStocks = {!! $availStocksJson !!};
    const sizesList = {!! $sizesJson !!}; // array of {size_id, size_label}

    // helper: build size options for selected product_name
    function populateSizesForProduct(productName, selectedSizeLabel = '') {
        if (!sizeSelect) return;
        // find all size_id for this product_name
        const sizeIds = availStocks
            .filter(a => (a.product_name ?? '').trim() === (productName ?? '').trim())
            .map(a => Number(a.size_id))
            .filter((v, i, arr) => arr.indexOf(v) === i) // unique
            .sort((a,b) => a - b);

        // map sizeIds -> labels using sizesList
        const options = [{ value: '', label: '-- Pilih Ukuran --' }];
        sizeIds.forEach(id => {
            const s = sizesList.find(x => Number(x.size_id) === Number(id));
            if (s) options.push({ value: s.size_label, label: s.size_label, size_id: id });
        });

        // render
        sizeSelect.innerHTML = options.map(o => `<option value="${o.value}" ${o.value === selectedSizeLabel ? 'selected' : ''}>${o.label}</option>`).join('');
    }

    // Due date: set min and show error if invalid
    function updateDueDateMin() {
        if (!dueDateInput || !dateInput) return;
        if (isPaidSelect && isPaidSelect.value === 'belum_lunas') {
            dueDateInput.min = dateInput.value || '';
            if (dueDateInput.value && dateInput.value && dueDateInput.value < dateInput.value) {
                dueDateError?.classList.remove('d-none');
            } else {
                dueDateError?.classList.add('d-none');
            }
        } else {
            dueDateInput.min = '';
            dueDateError?.classList.add('d-none');
        }
    }

    function formatRupiah(numberish) {
        if (numberish === '' || numberish === null) return '';
        const s = String(numberish).replace(/\D/g,'');
        if (!s) return '';
        return s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    function parseRupiah(str) {
        if (!str) return 0;
        return Number(String(str).replace(/\./g, '').replace(/,/g, '.')) || 0;
    }

    function applyAvailToForm(opt) {
        if (!opt) { priceInput.value = ''; if (productNameInput) productNameInput.value = ''; return; }

        let price = opt.dataset?.price ?? '';
        const prodName = opt.dataset?.productName ?? opt.textContent?.trim() ?? '';
        const sizeLabel = opt.dataset?.sizeLabel ?? '';

        if (price !== '' && !isNaN(Number(price))) {
            priceInput.value = formatRupiah(String(Math.round(Number(price))));
        } else {
            priceInput.value = '';
        }

        if (productNameInput) productNameInput.value = prodName;
        if (sizeSelect && sizeLabel) sizeSelect.value = sizeLabel;

        computeTotal();
    }

    function computeTotal() {
        const qty = Number(qtyInput?.value) || 0;
        const rawPrice = parseRupiah(priceInput?.value);
        const total = Math.round(qty * rawPrice);
        totalDisplay.value = total > 0 ? formatRupiah(String(total)) : '0';
        totalInput.value = total;
        computeUnpaid();
    }

    function computeUnpaid() {
        const totalRaw = Number(totalInput?.value) || 0;
        const paidRaw = parseRupiah(paidInput?.value);
        const unpaid = Math.max(0, Math.round(totalRaw - paidRaw));
        unpaidDisplay.value = unpaid > 0 ? formatRupiah(String(unpaid)) : '';
        if (unpaidInput) unpaidInput.value = unpaid;
    }

    function togglePaymentFields() {
        if (!isPaidSelect) return;
        if (isPaidSelect.value === 'lunas') {
            // show only payment method & status
            paymentMethodWrapper.classList.remove('d-none');
            statusWrapper.classList.remove('d-none');

            paidWrapper.classList.add('d-none');
            unpaidWrapper.classList.add('d-none');
            dueDateWrapper.classList.add('d-none');
        } else {
            // belum lunas: show paid, unpaid, due date, payment method, status
            paidWrapper.classList.remove('d-none');
            unpaidWrapper.classList.remove('d-none');
            dueDateWrapper.classList.remove('d-none');
            paymentMethodWrapper.classList.remove('d-none');
            statusWrapper.classList.remove('d-none');
        }
    }

    availSelect?.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const prodName = opt?.dataset?.productName ?? opt?.textContent?.trim() ?? '';
        // populate sizes for product; do not auto-select anything unless matches old value
        populateSizesForProduct(prodName, '');
        applyAvailToForm(opt);
    });
    qtyInput?.addEventListener('input', computeTotal);
    paidInput?.addEventListener('input', function() {
        // format typed digits as rupiah while keeping caret simple
        let v = this.value.replace(/\D/g,'');
        if (v) this.value = formatRupiah(v);
        computeUnpaid();
    });

    isPaidSelect?.addEventListener('change', function() {
        togglePaymentFields();
        updateDueDateMin();
    });

    const dateInput = document.getElementById('date');
    
    // init
    if (availSelect && availSelect.value) {
        applyAvailToForm(availSelect.options[availSelect.selectedIndex]);
    }
    togglePaymentFields();
    computeTotal();

    // submit handler: ensure values set for server
    const form = document.getElementById('transactionForm');
    form?.addEventListener('submit', function(e) {
        computeTotal();

        // if lunas, force paid = total and unpaid = 0, clear due date if any
        if (isPaidSelect && isPaidSelect.value === 'lunas') {
            if (paidInput) paidInput.value = String(Number(totalInput?.value || 0));
            if (unpaidInput) unpaidInput.value = '0';
            if (unpaidDisplay) unpaidDisplay.value = '';
            if (dueDateInput) dueDateInput.value = '';
        } else {
            // ensure unpaid sync
            if (unpaidInput) unpaidInput.value = String(parseRupiah(unpaidDisplay?.value || '0'));
        }

        // convert formatted fields to raw numbers for server
        if (priceInput) priceInput.value = String(parseRupiah(priceInput.value));
        if (paidInput) paidInput.value = String(parseRupiah(paidInput.value));
        if (unpaidInput) unpaidInput.value = String(parseRupiah(unpaidInput.value || '0'));
        // product_name hidden already set by applyAvailToForm

        // Due date: client-side check
        if (isPaidSelect && isPaidSelect.value === 'belum_lunas') {
            if (!dueDateInput || !dueDateInput.value) {
                e.preventDefault();
                dueDateError.textContent = 'Due date wajib diisi untuk transaksi belum lunas.';
                dueDateError.classList.remove('d-none');
                dueDateInput.focus();
                return;
            }
            if (dateInput && dueDateInput.value < dateInput.value) {
                e.preventDefault();
                dueDateError.textContent = 'Due date tidak boleh sebelum tanggal transaksi.';
                dueDateError.classList.remove('d-none');
                dueDateInput.focus();
                return;
            }
        }
    });
});
</script>
