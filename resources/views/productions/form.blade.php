<form action="{{ route('production.store') }}" method="POST" class="mb-4" id="productionForm">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3 d-flex flex-column gap-3">
            <div class="col">
                <label for="production_lead" class="form-label">Production Lead</label>
                <input type="text" name="production_lead" class="form-control" placeholder="Production Lead" value="{{ old('production_lead') }}" required>
                @error('production_lead')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label for="production_label" class="form-label">Production Label</label>
                <input type="text" name="production_label" class="form-control" placeholder="Production Label" value="{{ old('production_label') }}" required>
                @error('production_label')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label for="production_date" class="form-label">Tanggal Production</label>
                <input type="date" name="production_date" class="form-control" value="{{ old('production_date', date('Y-m-d')) }}" required>
                @error('production_date')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col">
                <label for="total_unit" class="form-label">Total Unit Produksi</label>
                <input type="number" name="total_unit" class="form-control" placeholder="Total Unit" value="{{ old('total_unit') }}" min="1" required>
                @error('total_unit')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <label class="form-label mb-0">Materials</label>
                <button type="button" class="btn btn-sm btn-success" id="addMaterialBtn">
                    <i class="ti ti-plus"></i> Tambah Material
                </button>
            </div>
            <div id="materialsContainer">
                {{-- Material rows akan ditambahkan di sini --}}
            </div>
            @error('materials')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
            @error('materials.*')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
            <div class="col d-flex justify-content-end align-items-end mt-3">
                <button type="submit" class="btn btn-primary">Tambah Production</button>
            </div>
        </div>
    </div>
</form>

<script>
let materialIndex = 0;
const rawStocks = @json($rawStocks ?? []);

// Add material row
document.getElementById('addMaterialBtn').addEventListener('click', function() {
    addMaterialRow();
});

// Add initial material row
addMaterialRow();

function addMaterialRow() {
    const container = document.getElementById('materialsContainer');
    const row = document.createElement('div');
    row.className = 'material-row mb-3 border p-3 rounded';
    row.setAttribute('data-index', materialIndex);
    
    row.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Material</label>
                <select name="materials[${materialIndex}][material_id]" class="form-control material-select" required>
                    <option value="">-- Pilih Material --</option>
                    ${rawStocks.map(stock => `
                        <option value="${stock.material_id}">
                            ${stock.material_name} (Stok: ${stock.material_qty} ${stock.satuan})
                        </option>
                    `).join('')}
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Quantity</label>
                <input type="number" name="materials[${materialIndex}][material_qty]" class="form-control" placeholder="Qty" min="1" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-danger w-100 remove-material" ${materialIndex === 0 ? 'disabled' : ''}>
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(row);
    
    // Add remove event listener
    row.querySelector('.remove-material').addEventListener('click', function() {
        if (container.children.length > 1) {
            row.remove();
            updateMaterialIndices();
        }
    });
    
    materialIndex++;
}

function updateMaterialIndices() {
    const container = document.getElementById('materialsContainer');
    const rows = container.querySelectorAll('.material-row');
    rows.forEach((row, index) => {
        row.setAttribute('data-index', index);
        const select = row.querySelector('.material-select');
        const qtyInput = row.querySelector('input[type="number"]');
        const removeBtn = row.querySelector('.remove-material');
        
        // Update names
        select.name = `materials[${index}][material_id]`;
        qtyInput.name = `materials[${index}][material_qty]`;
        
        // Enable/disable remove button
        removeBtn.disabled = rows.length === 1;
    });
}
</script>
