<form action="{{ isset($detailProduct) ? route('detail_product.update', $detailProduct->product_id) : route('detail_product.store') }}" method="POST" id="productionForm">
    @csrf
    @if(isset($detailProduct))
        @method('PUT')
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
            {{-- Product ID (auto-increment) - show only when editing --}}
            @if(isset($detailProduct))
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="ti ti-hash me-1 text-primary"></i>Product ID
                    </label>
                    <div class="form-control-plaintext">{{ $detailProduct->product_id }}</div>
                </div>
            @endif

            {{-- Production ID (from route) - readonly, included as hidden input --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="ti ti-building-factory me-1 text-primary"></i>Production ID
                </label>
                <div class="form-control-plaintext">
                    {{ old('production_id', $detailProduct->production_id ?? request()->route('production_id') ?? request('production_id') ?? '-') }}
                </div>
                <input type="hidden" name="production_id" value="{{ old('production_id', $detailProduct->production_id ?? request()->route('production_id') ?? request('production_id') ?? '') }}">
            </div>

            {{-- Production Label (from route/query) - readonly, NOT submitted --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    <i class="ti ti-note me-1 text-primary"></i>Production Label
                </label>
                <div class="form-control-plaintext">
                    {{ old('production_label', $detailProduct->production_label ?? ($productionLabel ?? request('production_label')) ?? '-') }}
                </div>
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
                {{-- NOTE: size_label column removed from DB — not submitted --}}
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
                           placeholder="0"
                           value="{{ old('qty_unit', $detailProduct->qty_unit ?? 0) }}"
                           min="0">
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
                           placeholder="0"
                           value="{{ old('price_unit', $detailProduct->price_unit ?? 0) }}"
                           min="0" step="1">
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
                    <i class="ti ti-cloud-upload"></i> {{ isset($detailProduct) ? 'Perbarui' : 'Simpan' }}
                </button>
            </div>
        </div>
    </div>
</form>
