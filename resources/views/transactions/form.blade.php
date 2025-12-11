@php $isEdit = isset($transaction); @endphp

<div class="row g-4">
    {{-- Katalog Produk --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-shopping-cart-plus text-primary fs-5"></i>
                    <h5 class="mb-0 fw-semibold">Katalog Produk</h5>
                </div>
                <div class="w-50">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-search"></i></span>
                        <input type="text" id="catalogSearch" class="form-control border-start-0" placeholder="Cari produk...">
                    </div>
                </div>
            </div>
            <div class="card-body p-3">
                <div id="catalogGrid" class="row g-3"></div>
            </div>
        </div>
    </div>

    {{-- Keranjang & Pembayaran --}}
    <div class="col-lg-4">
        <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
            @csrf
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                    <i class="ti ti-basket text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Keranjang</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal</label>
                        <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>

                    <div id="cartEmpty" class="text-center text-muted py-3">
                        <i class="ti ti-shopping-cart-off fs-1 d-block mb-2"></i>
                        Keranjang kosong. Pilih produk dari katalog.
                    </div>

                    <div id="cartItems" class="d-flex flex-column gap-2"></div>

                    <div class="mt-3 border-top pt-3">
                        <div class="d-flex justify-content-between fw-semibold">
                            <span>Total</span>
                            <span id="cartTotalDisplay" class="text-primary">Rp 0</span>
                        </div>
                        <input type="hidden" name="cart_total" id="cartTotal" value="0">
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                    <i class="ti ti-cash text-success"></i>
                    <h6 class="mb-0 fw-semibold">Pembayaran</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Pembayaran</label>
                        <select name="is_paid" id="is_paid" class="form-select" required>
                            <option value="lunas">Lunas</option>
                            <option value="belum_lunas">Belum Lunas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Paid (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="text" name="paid" id="paid" class="form-control" placeholder="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran</label>
                        <input type="text" name="payment_method" class="form-control" placeholder="Cash / Transfer / QRIS">
                    </div>
                    <div class="mb-3 d-none" id="dueDateWrapper">
                        <label class="form-label fw-semibold">Jatuh Tempo</label>
                        <input type="date" name="due_date_payment" id="due_date_payment" class="form-control">
                        <div id="dueDateError" class="text-danger small mt-1 d-none">
                            <i class="ti ti-alert-circle me-1"></i>Jatuh tempo tidak boleh sebelum tanggal transaksi.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <input type="text" name="status" class="form-control" placeholder="contoh: dibayar / pending">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="ti ti-cloud-upload"></i> Simpan Transaksi
                        </button>
                    </div>
                </div>
            </div>

            {{-- Hidden container for items --}}
            <div id="itemsContainer"></div>
        </form>
    </div>
</div>

@php
    $catalogJson = $availStocks->map(function($a) use($sizes){
        $sizeLabel = $sizes?->firstWhere('size_id', $a->size_id)->size_label ?? '';
        return [
            'id' => $a->id,
            'product_name' => $a->product_name,
            'size_label' => $sizeLabel,
            'price' => (int) $a->price_unit,
            'stock' => (int) $a->qty_unit,
        ];
    })->values()->toJson();
@endphp

<script>
document.addEventListener('DOMContentLoaded', () => {
    const catalog = {!! $catalogJson !!};
    const catalogGrid = document.getElementById('catalogGrid');
    const catalogSearch = document.getElementById('catalogSearch');
    const cartItems = document.getElementById('cartItems');
    const cartEmpty = document.getElementById('cartEmpty');
    const cartTotalDisplay = document.getElementById('cartTotalDisplay');
    const cartTotalInput = document.getElementById('cartTotal');
    const itemsContainer = document.getElementById('itemsContainer');
    const isPaidSelect = document.getElementById('is_paid');
    const paidInput = document.getElementById('paid');
    const dueDateWrapper = document.getElementById('dueDateWrapper');
    const dueDateInput = document.getElementById('due_date_payment');
    const dueDateError = document.getElementById('dueDateError');
    const dateInput = document.querySelector('input[name="date"]');

    let cart = [];

    function formatRupiah(n) {
        return 'Rp ' + (Number(n) || 0).toLocaleString('id-ID');
    }

    function renderCatalog(list) {
        catalogGrid.innerHTML = list.map(item => `
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="fw-semibold text-dark">${item.product_name}</div>
                        <small class="text-muted">Size: ${item.size_label || '-'}</small>
                        <div class="mt-2 fw-bold text-primary">${formatRupiah(item.price)}</div>
                        <small class="text-muted">Stok: ${item.stock}</small>
                        <div class="mt-auto d-flex gap-2 align-items-center">
                            <input type="number" class="form-control form-control-sm qty-input" min="1" value="1" data-id="${item.id}">
                            <button type="button" class="btn btn-sm btn-primary w-100" data-id="${item.id}"><i class="ti ti-plus"></i> Tambah</button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        catalogGrid.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = Number(btn.dataset.id);
                const qtyInput = catalogGrid.querySelector(`input.qty-input[data-id="${id}"]`);
                const qty = Math.max(1, Number(qtyInput.value) || 1);
                addToCart(id, qty);
            });
        });
    }

    function renderCart() {
        if (!cart.length) {
            cartEmpty.classList.remove('d-none');
            cartItems.innerHTML = '';
        } else {
            cartEmpty.classList.add('d-none');
            cartItems.innerHTML = cart.map((item, idx) => `
                <div class="border rounded-3 p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-semibold">${item.product_name}</div>
                        <small class="text-muted">Size: ${item.size_label || '-'} | Stok: ${item.stock}</small>
                        <div class="text-primary fw-bold">${formatRupiah(item.price)}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" class="form-control form-control-sm cart-qty" min="1" max="${item.stock}" value="${item.qty}" data-idx="${idx}">
                        <div class="fw-semibold">${formatRupiah(item.price * item.qty)}</div>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-remove="${idx}"><i class="ti ti-trash"></i></button>
                    </div>
                </div>
            `).join('');

            cartItems.querySelectorAll('input.cart-qty').forEach(inp => {
                inp.addEventListener('input', () => {
                    const idx = Number(inp.dataset.idx);
                    const val = Math.min(Math.max(1, Number(inp.value) || 1), cart[idx].stock);
                    inp.value = val;
                    cart[idx].qty = val;
                    updateCart();
                });
            });
            cartItems.querySelectorAll('button[data-remove]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const idx = Number(btn.dataset.remove);
                    cart.splice(idx, 1);
                    updateCart();
                });
            });
        }
    }

    function renderHiddenInputs() {
        itemsContainer.innerHTML = cart.map((item, idx) => `
            <input type="hidden" name="items[${idx}][id]" value="${item.id}">
            <input type="hidden" name="items[${idx}][qty]" value="${item.qty}">
        `).join('');
    }

    function updateTotals() {
        const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        cartTotalDisplay.textContent = formatRupiah(total);
        cartTotalInput.value = total;
    }

    function updateCart() {
        renderCart();
        renderHiddenInputs();
        updateTotals();
    }

    function addToCart(id, qty) {
        const prod = catalog.find(x => Number(x.id) === Number(id));
        if (!prod) return;
        const existing = cart.find(x => x.id === prod.id);
        if (existing) {
            existing.qty = Math.min(existing.qty + qty, prod.stock);
        } else {
            cart.push({ ...prod, qty: Math.min(qty, prod.stock) });
        }
        updateCart();
    }

    catalogSearch?.addEventListener('input', () => {
        const term = catalogSearch.value.toLowerCase();
        const filtered = catalog.filter(c => c.product_name.toLowerCase().includes(term));
        renderCatalog(filtered);
    });

    function toggleDueDate() {
        if (isPaidSelect.value === 'belum_lunas') {
            dueDateWrapper.classList.remove('d-none');
        } else {
            dueDateWrapper.classList.add('d-none');
            if (dueDateInput) dueDateInput.value = '';
            dueDateError?.classList.add('d-none');
        }
    }

    isPaidSelect?.addEventListener('change', toggleDueDate);

    // validate due date on submit
    document.getElementById('transactionForm')?.addEventListener('submit', (e) => {
        if (!cart.length) {
            e.preventDefault();
            alert('Tambahkan minimal 1 produk ke keranjang.');
            return;
        }
        if (isPaidSelect.value === 'belum_lunas') {
            if (!dueDateInput.value) {
                e.preventDefault();
                dueDateError.textContent = 'Jatuh tempo wajib diisi.';
                dueDateError.classList.remove('d-none');
                return;
            }
            if (dateInput && dueDateInput.value < dateInput.value) {
                e.preventDefault();
                dueDateError.textContent = 'Jatuh tempo tidak boleh sebelum tanggal transaksi.';
                dueDateError.classList.remove('d-none');
                return;
            }
        }
    });

    // init render
    renderCatalog(catalog);
    renderCart();
    toggleDueDate();
});
</script>
