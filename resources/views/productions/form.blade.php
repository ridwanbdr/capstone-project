<form action="{{ route('production.store') }}" method="POST" id="productionForm">
    @csrf
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="production_lead" class="form-label fw-semibold">
                    <i class="ti ti-user me-1 text-primary"></i>Kepala Tim Produksi
                </label>
                <input type="text"
                       name="production_lead"
                       id="production_lead"
                       class="form-control"
                       placeholder="Masukkan nama penanggung jawab"
                       value="{{ old('production_lead') }}"
                       required>
                @error('production_lead')
                    <div class="text-danger small mt-1">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="production_label" class="form-label fw-semibold">
                    <i class="ti ti-note me-1 text-primary"></i>Label Produksi
                </label>
                <input type="text"
                       name="production_label"
                       id="production_label"
                       class="form-control"
                       placeholder="Masukkan label produksi"
                       value="{{ old('production_label') }}"
                       required>
                @error('production_label')
                    <div class="text-danger small mt-1">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="production_date" class="form-label fw-semibold">
                    <i class="ti ti-calendar-event me-1 text-primary"></i>Tanggal Produksi
                </label>
                <input type="date"
                       name="production_date"
                       id="production_date"
                       class="form-control"
                       value="{{ old('production_date', date('Y-m-d')) }}"
                       required>
                @error('production_date')
                    <div class="text-danger small mt-1">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="total_unit" class="form-label fw-semibold">
                    <i class="ti ti-packages me-1 text-primary"></i>Total Unit Produksi
                </label>
                <input type="number"
                       name="total_unit"
                       id="total_unit"
                       class="form-control"
                       placeholder="0"
                       value="{{ old('total_unit') }}"
                       min="1"
                       required>
                @error('total_unit')
                    <div class="text-danger small mt-1">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="form-label fw-semibold mb-0">
                    <i class="ti ti-layers-linked me-1 text-primary"></i>Daftar Bahan Baku
                </label>
                <button type="button" class="btn btn-sm btn-success" id="addMaterialBtn">
                    <i class="ti ti-plus"></i> Tambah Bahan Baku
                </button>
            </div>

            <div id="materialsContainer" class="d-flex flex-column gap-3"></div>

            @error('materials')
                <div class="text-danger small mt-2">
                    <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                </div>
            @enderror
            @error('materials.*')
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
                    <i class="ti ti-cloud-upload"></i> Simpan Produksi
                </button>
            </div>
        </div>
    </div>
</form>

<script>
let materialIndex = 0;
const rawStocks = @json($rawStocks ?? []);

const addMaterialBtn = document.getElementById('addMaterialBtn');
const materialsContainer = document.getElementById('materialsContainer');

addMaterialBtn.addEventListener('click', addMaterialRow);

// initial row
addMaterialRow();

function addMaterialRow() {
    const row = document.createElement('div');
    row.className = 'material-row border rounded-3 p-3 position-relative';
    row.setAttribute('data-index', materialIndex);

    row.innerHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Material</label>
                <select name="materials[${materialIndex}][material_id]"
                        class="form-select material-select"
                        required>
                    <option value="">-- Pilih Material --</option>
                    ${rawStocks.map(stock => `
                        <option value="${stock.material_id}">
                            ${stock.material_name} (Stok: ${stock.material_qty} ${stock.satuan})
                        </option>
                    `).join('')}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity</label>
                <input type="number"
                       name="materials[${materialIndex}][material_qty]"
                       class="form-control"
                       placeholder="Qty"
                       min="1"
                       required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button"
                        class="btn btn-outline-danger w-100 remove-material"
                        ${materialIndex === 0 ? 'disabled' : ''}>
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        </div>
    `;

    materialsContainer.appendChild(row);

    row.querySelector('.remove-material').addEventListener('click', function () {
        if (materialsContainer.children.length > 1) {
            row.remove();
            updateMaterialIndices();
        }
    });

    materialIndex++;
}

function updateMaterialIndices() {
    const rows = materialsContainer.querySelectorAll('.material-row');
    rows.forEach((row, index) => {
        row.setAttribute('data-index', index);
        const select = row.querySelector('.material-select');
        const qtyInput = row.querySelector('input[type="number"]');
        const removeBtn = row.querySelector('.remove-material');

        select.name = `materials[${index}][material_id]`;
        qtyInput.name = `materials[${index}][material_qty]`;
        removeBtn.disabled = rows.length === 1;
    });
}
</script>
