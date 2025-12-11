@if(isset($detailProduct))
    {{-- Edit Form (Single Product) --}}
    <form action="{{ route('detail_product.update', $detailProduct->product_id) }}" method="POST" id="productionForm">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-lg-6">
                {{-- Product ID (auto-increment) - show only when editing --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-hash me-1 text-primary"></i>Product ID
                    </label>
                    <div class="form-control-plaintext">{{ $detailProduct->product_id }}</div>
                </div>

                {{-- Production ID (from route) - readonly, included as input so browser validation can run --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-building-factory me-1 text-primary"></i>Production ID
                    </label>
                    <input type="text"
                           name="production_id"
                           class="form-control form-control-plaintext @error('production_id') is-invalid @enderror"
                           readonly
                           required
                           value="{{ old('production_id', $detailProduct->production_id ?? request()->route('production_id') ?? request('production_id') ?? '') }}">
                    @error('production_id')
                        <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Production Label (from route/query) - readonly input, submitted --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-note me-1 text-primary"></i>Production Label
                    </label>
                    <input type="text"
                           name="production_label"
                           class="form-control form-control-plaintext @error('production_label') is-invalid @enderror"
                           readonly
                           required
                           value="{{ old('production_label', $detailProduct->production_label ?? ($productionLabel ?? request('production_label')) ?? '') }}">
                    @error('production_label')
                        <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Product Name --}}
                <div class="mb-3">
                    <label for="product_name" class="form-label fw-semibold">
                        <i class="ti ti-package me-1 text-primary"></i>Product Name
                    </label>
                    <input type="text"
                           name="product_name"
                           id="product_name"
                           class="form-control @error('product_name') is-invalid @enderror"
                           placeholder="Masukkan nama produk"
                           value="{{ old('product_name', $detailProduct->product_name ?? '') }}"
                           required>
                    @error('product_name')
                        <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-6">
                {{-- Size dropdown (show only size_label in options) --}}
                <div class="mb-3">
                    <label for="size_id" class="form-label fw-semibold">
                        <i class="ti ti-ruler-measure me-1 text-primary"></i>Size
                    </label>
                    <select name="size_id"
                            id="size_id"
                            class="form-select @error('size_id') is-invalid @enderror"
                            required>
                        <option value="">-- Pilih ukuran --</option>
                        @foreach($sizes ?? collect() as $size)
                            <option value="{{ $size->size_id }}"
                                    {{ (string) old('size_id', $detailProduct->size_id ?? '') === (string) $size->size_id ? 'selected' : '' }}>
                                {{ $size->size_label }}
                            </option>
                        @endforeach
                    </select>
                    @error('size_id')
                        <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="qty_unit" class="form-label fw-semibold">
                            <i class="ti ti-stack me-1 text-primary"></i>Qty Unit
                        </label>
                        <input type="number"
                               name="qty_unit"
                               id="qty_unit"
                               class="form-control @error('qty_unit') is-invalid @enderror"
                               value="{{ old('qty_unit', $detailProduct->qty_unit ?? '') }}"
                               min="1"
                               required>
                        @error('qty_unit')
                            <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="price_unit" class="form-label fw-semibold">
                            <i class="ti ti-currency-dollar me-1 text-primary"></i>Price Unit
                        </label>
                        <input type="number"
                               name="price_unit"
                               id="price_unit"
                               class="form-control @error('price_unit') is-invalid @enderror"
                               value="{{ old('price_unit', $detailProduct->price_unit ?? '') }}"
                               min="1"
                               step="1"
                               required>
                        @error('price_unit')
                            <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-secondary px-4">
                        <i class="ti ti-refresh"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="ti ti-cloud-upload"></i> Perbarui
                    </button>
                </div>
            </div>
        </div>
    </form>
@else
    {{-- Create Form (Multiple Products) --}}
    <form action="{{ route('detail_product.store') }}" method="POST" id="productionForm">
        @csrf

        <div class="row g-4">
            <div class="col-lg-6">
                {{-- Production ID (from route) - readonly --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-building-factory me-1 text-primary"></i>Production ID
                    </label>
                    <input type="text"
                           name="production_id"
                           class="form-control form-control-plaintext @error('production_id') is-invalid @enderror"
                           readonly
                           required
                           value="{{ old('production_id', $production_id ?? request('production_id') ?? '') }}">
                    @error('production_id')
                        <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Production Label (from route/query) - readonly --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-note me-1 text-primary"></i>Production Label
                    </label>
                    <input type="text"
                           name="production_label"
                           class="form-control form-control-plaintext @error('production_label') is-invalid @enderror"
                           readonly
                           value="{{ old('production_label', ($productionLabel ?? request('production_label')) ?? '') }}">
                    @error('production_label')
                        <div class="text-danger small mt-1"><i class="ti ti-alert-circle me-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                {{-- Total Unit Production Info --}}
                @if(isset($totalUnitLimit) && $totalUnitLimit > 0)
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-packages me-1 text-primary"></i>Informasi Total Unit Produksi
                    </label>
                    <div class="card bg-light border-0 p-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted d-block">Total Limit</small>
                                <span class="fw-bold text-primary fs-5">{{ number_format($totalUnitLimit ?? 0) }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Terpakai</small>
                                <span class="fw-bold text-warning fs-5">{{ number_format($currentTotal ?? 0) }}</span>
                            </div>
                            <div class="col-12 mt-2 pt-2 border-top">
                                <small class="text-muted d-block">Sisa Tersedia</small>
                                <span class="fw-bold {{ ($remainingUnit ?? 0) > 0 ? 'text-success' : 'text-danger' }} fs-5">
                                    {{ number_format($remainingUnit ?? 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-6">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-label fw-semibold mb-0">
                        <i class="ti ti-layers-linked me-1 text-primary"></i>Daftar Produk
                    </label>
                    <button type="button" class="btn btn-sm btn-success" id="addProductBtn">
                        <i class="ti ti-plus"></i> Tambah Produk
                    </button>
                </div>

                <div id="productsContainer" class="d-flex flex-column gap-3"></div>

                @error('products')
                    <div class="text-danger small mt-2">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
                @error('products.*')
                    <div class="text-danger small mt-2">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-secondary px-4">
                        <i class="ti ti-refresh"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        <i class="ti ti-cloud-upload"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
    let productIndex = 0;
    const sizes = @json($sizes ?? []);
    const oldProducts = @json(old('products', []));

    const addProductBtn = document.getElementById('addProductBtn');
    const productsContainer = document.getElementById('productsContainer');

    addProductBtn.addEventListener('click', addProductRow);

    // Restore old input if validation failed
    if (oldProducts && oldProducts.length > 0) {
        oldProducts.forEach((product, index) => {
            addProductRow(product);
        });
    } else {
        // initial row
        addProductRow();
    }

    function addProductRow(oldProduct = null) {
        const row = document.createElement('div');
        row.className = 'product-row border rounded-3 p-3 position-relative';
        row.setAttribute('data-index', productIndex);

        const productName = oldProduct?.product_name || '';
        const sizeId = oldProduct?.size_id || '';
        const qtyUnit = oldProduct?.qty_unit || '';
        const priceUnit = oldProduct?.price_unit || '';

        row.innerHTML = `
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Product Name</label>
                    <input type="text"
                           name="products[${productIndex}][product_name]"
                           class="form-control product-name"
                           placeholder="Masukkan nama produk"
                           value="${productName}"
                           required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Size</label>
                    <select name="products[${productIndex}][size_id]"
                            class="form-select size-select"
                            required>
                        <option value="">-- Pilih --</option>
                        ${sizes.map(size => `
                            <option value="${size.size_id}" ${sizeId == size.size_id ? 'selected' : ''}>
                                ${size.size_label}
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Qty</label>
                    <input type="number"
                           name="products[${productIndex}][qty_unit]"
                           class="form-control qty-input"
                           placeholder="Qty"
                           value="${qtyUnit}"
                           min="1"
                           required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button"
                            class="btn btn-outline-danger w-100 remove-product"
                            ${productIndex === 0 ? 'disabled' : ''}>
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Price Unit</label>
                    <input type="number"
                           name="products[${productIndex}][price_unit]"
                           class="form-control price-input"
                           placeholder="Harga per unit"
                           value="${priceUnit}"
                           min="1"
                           step="1"
                           required>
                </div>
            </div>
        `;

        productsContainer.appendChild(row);

        row.querySelector('.remove-product').addEventListener('click', function () {
            if (productsContainer.children.length > 1) {
                row.remove();
                updateProductIndices();
            }
        });

        productIndex++;
    }

    function updateProductIndices() {
        const rows = productsContainer.querySelectorAll('.product-row');
        rows.forEach((row, index) => {
            row.setAttribute('data-index', index);
            const nameInput = row.querySelector('.product-name');
            const sizeSelect = row.querySelector('.size-select');
            const qtyInput = row.querySelector('.qty-input');
            const priceInput = row.querySelector('.price-input');
            const removeBtn = row.querySelector('.remove-product');

            nameInput.name = `products[${index}][product_name]`;
            sizeSelect.name = `products[${index}][size_id]`;
            qtyInput.name = `products[${index}][qty_unit]`;
            priceInput.name = `products[${index}][price_unit]`;
            removeBtn.disabled = rows.length === 1;
        });
    }
    </script>
@endif
