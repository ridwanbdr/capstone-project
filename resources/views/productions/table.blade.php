<div class="col-lg-12 d-flex align-items-stretch">
    <div class="card w-100">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover table-bordered text-nowrap text-center mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Tanggal Production</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Production Lead</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Production Label</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Material</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Qty Material</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">Total Unit</h6>
                            </th>
                            <th class="border-bottom-0">
                                <h6 class="fw-semibold mb-0 text-center">Aksi</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productions as $production)
                        <tr>
                            <!-- Tanggal Production -->
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $production->production_date ? \Carbon\Carbon::parse($production->production_date)->format('Y-m-d') : '' }}</h6>
                            </td>

                            <!-- Production Lead -->
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $production->production_lead }}</h6>
                            </td>

                            <!-- Production Label -->
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $production->production_label }}</h6>
                            </td>

                            <!-- Materials (dari relasi many-to-many) -->
                            <td class="border-bottom-0">
                                @if($production->rawStocks && $production->rawStocks->count() > 0)
                                    <ul class="list-unstyled mb-0">
                                        @foreach($production->rawStocks as $rawStock)
                                            <li>
                                                <small>{{ $rawStock->material_name }}: {{ $rawStock->pivot->material_qty }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <h6 class="fw-semibold mb-0">N/A</h6>
                                @endif
                            </td>

                            <!-- Total Qty Material -->
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">
                                    {{ $production->rawStocks ? $production->rawStocks->sum('pivot.material_qty') : 0 }}
                                </h6>
                            </td>

                            <!-- Total Unit -->
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $production->total_unit }}</h6>
                            </td>

                            <td class="border-bottom-0 text-center">
                                {{-- Delete form --}}
                                <form action="{{ route('production.destroy', $production->production_id) }}" method="POST" style="display:inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus production ini? Stok material TIDAK akan dikembalikan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>

                                {{-- Tombol Edit (memanggil modal) --}}
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $production->production_id }}">
                                    Edit
                                </button>

                                {{-- Modal Edit (setiap baris punya ID unik) --}}
                                <div class="modal fade" id="editModal{{ $production->production_id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $production->production_id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="editModalLabel{{ $production->production_id }}">Edit Production</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <form action="{{ route('production.update', ['production' => $production->production_id]) }}" method="POST" id="editForm{{ $production->production_id }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-3">
                                                        <label for="productionLead{{ $production->production_id }}" class="col-form-label">Production Lead:</label>
                                                        <input name="production_lead" required type="text" class="form-control" id="productionLead{{ $production->production_id }}" value="{{ old('production_lead', $production->production_lead) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="productionLabel{{ $production->production_id }}" class="col-form-label">Production Label:</label>
                                                        <input name="production_label" required type="text" class="form-control" id="productionLabel{{ $production->production_id }}" value="{{ old('production_label', $production->production_label) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="productionDate{{ $production->production_id }}" class="col-form-label">Tanggal Production:</label>
                                                        <input name="production_date" required type="date" class="form-control" id="productionDate{{ $production->production_id }}" value="{{ old('production_date', $production->production_date) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <label class="col-form-label mb-0">Materials:</label>
                                                            <button type="button" class="btn btn-sm btn-success add-material-edit" data-production-id="{{ $production->production_id }}">
                                                                <i class="ti ti-plus"></i> Tambah
                                                            </button>
                                                        </div>
                                                        <div id="editMaterialsContainer{{ $production->production_id }}">
                                                            @if($production->rawStocks && $production->rawStocks->count() > 0)
                                                                @foreach($production->rawStocks as $index => $rawStock)
                                                                    <div class="material-row-edit mb-2 border p-2 rounded">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <select name="materials[{{ $index }}][material_id]" class="form-control form-control-sm" required>
                                                                                    <option value="">-- Pilih Material --</option>
                                                                                    @foreach($rawStocks ?? [] as $stock)
                                                                                        <option value="{{ $stock->material_id }}" {{ $rawStock->material_id == $stock->material_id ? 'selected' : '' }}>
                                                                                            {{ $stock->material_name }} (Stok: {{ $stock->material_qty }} {{ $stock->satuan }})
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <input type="number" name="materials[{{ $index }}][material_qty]" class="form-control form-control-sm" placeholder="Qty" min="1" value="{{ $rawStock->pivot->material_qty }}" required>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <button type="button" class="btn btn-sm btn-danger w-100 remove-material-edit" {{ $production->rawStocks->count() == 1 ? 'disabled' : '' }}>
                                                                                    <i class="ti ti-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="material-row-edit mb-2 border p-2 rounded">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <select name="materials[0][material_id]" class="form-control form-control-sm" required>
                                                                                <option value="">-- Pilih Material --</option>
                                                                                @foreach($rawStocks ?? [] as $stock)
                                                                                    <option value="{{ $stock->material_id }}">
                                                                                        {{ $stock->material_name }} (Stok: {{ $stock->material_qty }} {{ $stock->satuan }})
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <input type="number" name="materials[0][material_qty]" class="form-control form-control-sm" placeholder="Qty" min="1" required>
                                                                        </div>
                                                                        <div class="col-md-2">
                                                                            <button type="button" class="btn btn-sm btn-danger w-100 remove-material-edit" disabled>
                                                                                <i class="ti ti-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="totalUnit{{ $production->production_id }}" class="col-form-label">Total Unit:</label>
                                                        <input name="total_unit" required type="number" min="1" class="form-control" id="totalUnit{{ $production->production_id }}" value="{{ old('total_unit', $production->total_unit) }}">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <script>
                                (function() {
                                    const productionId = {{ $production->production_id }};
                                    let editMaterialIndex = {{ $production->rawStocks ? $production->rawStocks->count() : 1 }};
                                    const rawStocks = @json($rawStocks ?? []);
                                    
                                    const addBtn = document.querySelector(`#editModal${productionId} .add-material-edit`);
                                    const container = document.getElementById(`editMaterialsContainer${productionId}`);
                                    
                                    if (addBtn) {
                                        addBtn.addEventListener('click', function() {
                                            const row = document.createElement('div');
                                            row.className = 'material-row-edit mb-2 border p-2 rounded';
                                            row.innerHTML = `
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select name="materials[${editMaterialIndex}][material_id]" class="form-control form-control-sm" required>
                                                            <option value="">-- Pilih Material --</option>
                                                            ${rawStocks.map(stock => `
                                                                <option value="${stock.material_id}">
                                                                    ${stock.material_name} (Stok: ${stock.material_qty} ${stock.satuan})
                                                                </option>
                                                            `).join('')}
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="number" name="materials[${editMaterialIndex}][material_qty]" class="form-control form-control-sm" placeholder="Qty" min="1" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-sm btn-danger w-100 remove-material-edit">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            `;
                                            container.appendChild(row);
                                            
                                            row.querySelector('.remove-material-edit').addEventListener('click', function() {
                                                if (container.children.length > 1) {
                                                    row.remove();
                                                    updateEditIndices(productionId);
                                                }
                                            });
                                            
                                            editMaterialIndex++;
                                            updateEditIndices(productionId);
                                        });
                                    }
                                    
                                    // Add remove listeners to existing rows
                                    container.querySelectorAll('.remove-material-edit').forEach(btn => {
                                        btn.addEventListener('click', function() {
                                            if (container.children.length > 1) {
                                                btn.closest('.material-row-edit').remove();
                                                updateEditIndices(productionId);
                                            }
                                        });
                                    });
                                    
                                    function updateEditIndices(prodId) {
                                        const cont = document.getElementById(`editMaterialsContainer${prodId}`);
                                        const rows = cont.querySelectorAll('.material-row-edit');
                                        rows.forEach((row, index) => {
                                            const select = row.querySelector('select');
                                            const qtyInput = row.querySelector('input[type="number"]');
                                            const removeBtn = row.querySelector('.remove-material-edit');
                                            
                                            select.name = `materials[${index}][material_id]`;
                                            qtyInput.name = `materials[${index}][material_qty]`;
                                            removeBtn.disabled = rows.length === 1;
                                        });
                                    }
                                })();
                                </script>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <h6 class="fw-semibold mb-0">Tidak ada data production</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination links --}}
            @if(isset($productions) && $productions->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $productions->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

