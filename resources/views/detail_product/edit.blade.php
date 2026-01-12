@extends('layouts.main')

@section('title', 'Edit Detail Product')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <x-breadcrumb :breadcrumbs="[
                    'Home' => route('dashboard'),
                    'Detail Produk' => route('detail_product.index'),
                    'Edit Detail Produk' => '#'
                ]"/>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="ti ti-checks me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-header py-3">
                    <div class="d-flex align-items-center text-primary">
                        <i class="ti ti-edit me-2 fs-5"></i>
                        <h5 class="mb-0 fw-semibold">Edit Detail Produk #{{ $detailProduct->product_id }}</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('detail_product.update', $detailProduct->product_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Production ID</label>
                                    <div class="form-control-plaintext">{{ $detailProduct->production_id }}</div>
                                    <input type="hidden"
                                           name="production_id"
                                           value="{{ old('production_id', $detailProduct->production_id) }}">
                                    @error('production_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Production Label</label>
                                    <div class="form-control-plaintext">
                                        {{ optional($detailProduct->production)->production_label ?? '-' }}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Product Name</label>
                                    <input name="product_name"
                                           required
                                           type="text"
                                           class="form-control @error('product_name') is-invalid @enderror"
                                           value="{{ old('product_name', $detailProduct->product_name) }}">
                                    @error('product_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Size</label>
                                    <select name="size_id"
                                            class="form-select @error('size_id') is-invalid @enderror"
                                            required>
                                        <option value="">-- Pilih ukuran --</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->size_id }}"
                                                {{ old('size_id', $detailProduct->size_id) == $size->size_id ? 'selected' : '' }}>
                                                {{ $size->size_label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('size_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Qty Unit</label>
                                    <input name="qty_unit"
                                           type="number"
                                           min="1"
                                           class="form-control @error('qty_unit') is-invalid @enderror"
                                           value="{{ old('qty_unit', $detailProduct->qty_unit) }}"
                                           required>
                                    @error('qty_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Price Unit</label>
                                    <input name="price_unit"
                                           type="number"
                                           min="1"
                                           class="form-control @error('price_unit') is-invalid @enderror"
                                           value="{{ old('price_unit', $detailProduct->price_unit) }}"
                                           required>
                                    @error('price_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('detail_product.index', ['production_id' => $detailProduct->production_id]) }}"
                               class="btn btn-outline-secondary px-4">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="ti ti-device-floppy"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


