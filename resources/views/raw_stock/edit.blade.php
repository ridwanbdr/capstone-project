@extends('layouts.main')

@section('title', 'Edit Bahan Baku')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <x-breadcrumb :breadcrumbs="[
                    'Home' => route('dashboard'),
                    'Bahan Baku' => route('raw_stock.index'),
                    'Edit Bahan Baku' => '#'
                ]"/>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            {{-- Alert Messages --}}
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
                        <h5 class="mb-0 fw-semibold">Edit Bahan Baku #{{ $stock->material_id }}</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('raw_stock.update', $stock->material_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="ti ti-tag me-1 text-primary"></i>Nama Material
                                    </label>
                                    <input type="text"
                                           name="material_name"
                                           class="form-control @error('material_name') is-invalid @enderror"
                                           value="{{ old('material_name', $stock->material_name) }}"
                                           required>
                                    @error('material_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="ti ti-category me-1 text-primary"></i>Kategori
                                    </label>
                                    <select name="category"
                                            class="form-select @error('category') is-invalid @enderror"
                                            required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Kain Utama" {{ old('category', $stock->category) == 'Kain Utama' ? 'selected' : '' }}>Kain Utama</option>
                                        <option value="Benang" {{ old('category', $stock->category) == 'Benang' ? 'selected' : '' }}>Benang</option>
                                        <option value="Aksesoris" {{ old('category', $stock->category) == 'Aksesoris' ? 'selected' : '' }}>Aksesoris</option>
                                        <option value="Bahan pelengkap" {{ old('category', $stock->category) == 'Bahan pelengkap' ? 'selected' : '' }}>Bahan pelengkap</option>
                                        <option value="Bahan kemasan" {{ old('category', $stock->category) == 'Bahan kemasan' ? 'selected' : '' }}>Bahan kemasan</option>
                                        <option value="Bahan lainnya" {{ old('category', $stock->category) == 'Bahan lainnya' ? 'selected' : '' }}>Bahan lainnya</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="ti ti-hash me-1 text-primary"></i>Quantity
                                        </label>
                                        <input type="number"
                                               name="material_qty"
                                               min="0"
                                               class="form-control @error('material_qty') is-invalid @enderror"
                                               value="{{ old('material_qty', $stock->material_qty) }}"
                                               required>
                                        @error('material_qty')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="ti ti-ruler me-1 text-primary"></i>Satuan
                                        </label>
                                        <select name="satuan"
                                                class="form-select @error('satuan') is-invalid @enderror"
                                                required>
                                            <option value="">-- Pilih Satuan --</option>
                                            <option value="pcs" {{ old('satuan', $stock->satuan) == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                            <option value="roll" {{ old('satuan', $stock->satuan) == 'roll' ? 'selected' : '' }}>Roll</option>
                                            <option value="kg" {{ old('satuan', $stock->satuan) == 'kg' ? 'selected' : '' }}>Kg</option>
                                            <option value="meter" {{ old('satuan', $stock->satuan) == 'meter' ? 'selected' : '' }}>Meter</option>
                                        </select>
                                        @error('satuan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="ti ti-currency-rupiah me-1 text-primary"></i>Harga Satuan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="number"
                                               name="unit_price"
                                               step="1"
                                               min="0"
                                               class="form-control @error('unit_price') is-invalid @enderror"
                                               value="{{ old('unit_price', $stock->unit_price) }}"
                                               required>
                                        @error('unit_price')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="ti ti-calendar me-1 text-primary"></i>Tanggal Input
                                    </label>
                                    <input type="date"
                                           name="added_on"
                                           class="form-control @error('added_on') is-invalid @enderror"
                                           value="{{ old('added_on', optional($stock->added_on)->format('Y-m-d')) }}"
                                           required>
                                    @error('added_on')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('raw_stock.index') }}" class="btn btn-outline-secondary px-4">
                                <i class="ti ti-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="ti ti-check"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


