@extends('layouts.main')

@section('title', 'Edit Produksi')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <x-breadcrumb :breadcrumbs="[
                    'Home' => route('dashboard'),
                    'Produksi' => route('production.index'),
                    'Edit Produksi' => '#'
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
                        <h5 class="mb-0 fw-semibold">Edit Produksi #{{ $production->production_id }}</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('production.update', $production->production_id) }}" method="POST" id="productionEditForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tim Produksi</label>
                                    <input name="production_lead"
                                           required
                                           type="text"
                                           class="form-control @error('production_lead') is-invalid @enderror"
                                           value="{{ old('production_lead', $production->production_lead) }}">
                                    @error('production_lead')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Label Produksi</label>
                                    <input name="production_label"
                                           required
                                           type="text"
                                           class="form-control @error('production_label') is-invalid @enderror"
                                           value="{{ old('production_label', $production->production_label) }}">
                                    @error('production_label')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal Produksi</label>
                                    <input name="production_date"
                                           required
                                           type="date"
                                           class="form-control @error('production_date') is-invalid @enderror"
                                           value="{{ old('production_date', optional($production->production_date)->format('Y-m-d')) }}">
                                    @error('production_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Bahan Baku</label>
                                        <button type="button"
                                                class="btn btn-sm btn-success"
                                                id="addMaterialRowBtn">
                                            <i class="ti ti-plus"></i> Tambah
                                        </button>
                                    </div>
                                    <div id="materialsContainer">
                                        @php
                                            $existingMaterials = $production->rawStocks ?? collect();
                                        @endphp
                                        @if($existingMaterials->count() > 0)
                                            @foreach($existingMaterials as $index => $rawStock)
                                                <div class="material-row border rounded-3 p-2 mb-2">
                                                    <div class="row g-2">
                                                        <div class="col-md-6">
                                                            <select name="materials[{{ $index }}][material_id]"
                                                                    class="form-select form-select-sm"
                                                                    required>
                                                                <option value="">-- Pilih Bahan Baku --</option>
                                                                @foreach($rawStocks as $stock)
                                                                    <option value="{{ $stock->material_id }}"
                                                                        {{ $rawStock->material_id == $stock->material_id ? 'selected' : '' }}>
                                                                        {{ $stock->material_name }} (Stok: {{ $stock->material_qty }} {{ $stock->satuan }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="number"
                                                                   name="materials[{{ $index }}][material_qty]"
                                                                   class="form-control form-control-sm"
                                                                   placeholder="Qty"
                                                                   min="1"
                                                                   value="{{ $rawStock->pivot->material_qty }}"
                                                                   required>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-danger w-100 remove-material-row"
                                                                    {{ $existingMaterials->count() == 1 ? 'disabled' : '' }}>
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="material-row border rounded-3 p-2 mb-2">
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <select name="materials[0][material_id]"
                                                                class="form-select form-select-sm"
                                                                required>
                                                            <option value="">-- Pilih Bahan Baku --</option>
                                                            @foreach($rawStocks as $stock)
                                                                <option value="{{ $stock->material_id }}">
                                                                    {{ $stock->material_name }} (Stok: {{ $stock->material_qty }} {{ $stock->satuan }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number"
                                                               name="materials[0][material_qty]"
                                                               class="form-control form-control-sm"
                                                               placeholder="Qty"
                                                               min="1"
                                                               required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger w-100 remove-material-row"
                                                                disabled>
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Total Unit</label>
                                    <input name="total_unit"
                                           required
                                           type="number"
                                           min="1"
                                           class="form-control @error('total_unit') is-invalid @enderror"
                                           value="{{ old('total_unit', $production->total_unit) }}">
                                    @error('total_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('production.index') }}" class="btn btn-outline-secondary px-4">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const materialsContainer = document.getElementById('materialsContainer');
    const addBtn = document.getElementById('addMaterialRowBtn');
    const rawStocks = @json($rawStocks);

    function updateIndices() {
        const rows = materialsContainer.querySelectorAll('.material-row');
        rows.forEach((row, index) => {
            const select = row.querySelector('select');
            const qtyInput = row.querySelector('input[type="number"]');
            const removeBtn = row.querySelector('.remove-material-row');

            select.name = `materials[${index}][material_id]`;
            qtyInput.name = `materials[${index}][material_qty]`;
            removeBtn.disabled = rows.length === 1;
        });
    }

    addBtn.addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'material-row border rounded-3 p-2 mb-2';
        const index = materialsContainer.querySelectorAll('.material-row').length;

        row.innerHTML = `
            <div class="row g-2">
                <div class="col-md-6">
                    <select name="materials[${index}][material_id]" class="form-select form-select-sm" required>
                        <option value="">-- Pilih Bahan Baku --</option>
                        ${rawStocks.map(stock => `
                            <option value="${stock.material_id}">
                                ${stock.material_name} (Stok: ${stock.material_qty} ${stock.satuan})
                            </option>
                        `).join('')}
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number"
                           name="materials[${index}][material_qty]"
                           class="form-control form-control-sm"
                           placeholder="Qty"
                           min="1"
                           required>
                </div>
                <div class="col-md-2">
                    <button type="button"
                            class="btn btn-sm btn-outline-danger w-100 remove-material-row">
                        <i class="ti ti-trash"></i>
                    </button>
                </div>
            </div>
        `;

        materialsContainer.appendChild(row);

        row.querySelector('.remove-material-row').addEventListener('click', function () {
            if (materialsContainer.children.length > 1) {
                row.remove();
                updateIndices();
            }
        });

        updateIndices();
    });

    materialsContainer.querySelectorAll('.remove-material-row').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = btn.closest('.material-row');
            if (materialsContainer.children.length > 1) {
                row.remove();
                updateIndices();
            }
        });
    });

    updateIndices();
});
</script>
@endpush
@endsection


