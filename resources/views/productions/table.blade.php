<div class="table-responsive px-5 py-3">
    <table class="table align-middle mb-0">
        <thead class="table-light text-center">
            <tr>
                <th class="ps-4">
                    <span class="fw-semibold text-dark">Tanggal Produksi</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Tim Produksi</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Label</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Bahan Baku</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Total Bahan Baku</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Total Unit</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Aksi</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($productions as $production)
            <tr class="border-bottom">
                <td class="ps-4">
                    <div class="d-flex align-items-center text-muted">
                        <i class="ti ti-calendar me-2"></i>
                        {{ $production->production_date ? \Carbon\Carbon::parse($production->production_date)->format('d M Y') : '-' }}
                    </div>
                </td>
                <td>
                    <span class="fw-semibold">{{ $production->production_lead }}</span>
                </td>
                <td>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                        {{ $production->production_label }}
                    </span>
                </td>
                <td>
                    @if($production->rawStocks && $production->rawStocks->count() > 0)
                        <ul class="list-unstyled mb-0">
                            @foreach($production->rawStocks as $rawStock)
                                <li class="d-flex justify-content-between text-muted">
                                    <span>{{ $rawStock->material_name }}</span>
                                    <small class="fw-semibold">{{ number_format($rawStock->pivot->material_qty) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-center">
                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                        {{ $production->rawStocks ? number_format($production->rawStocks->sum('pivot.material_qty')) : 0 }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="fw-bold text-primary">{{ number_format($production->total_unit) }}</span>
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button"
                                class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $production->production_id }}">
                            <i class="ti ti-edit"></i>
                        </button>

                        <!-- View button (mata) - navigasi ke halaman detail_product.index dengan production_id dan production_label -->
                        <a href="{{ route('detail_product.index', ['production_id' => $production->production_id, 'production_label' => $production->production_label]) }}"
                           class="btn btn-sm btn-outline-warning rounded-pill px-3"
                           title="Lihat detail produk">
                            <i class="ti ti-eye"></i>
                        </a>

                        <form action="{{ route('production.destroy', $production->production_id) }}"
                              method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus production ini? Stok material TIDAK akan dikembalikan.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            {{-- Modal Edit --}}
            <div class="modal fade" id="editModal{{ $production->production_id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $production->production_id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-primary border-0">
                            <h5 class="modal-title text-white fw-semibold" id="editModalLabel{{ $production->production_id }}">
                                <i class="ti ti-edit me-2"></i>Edit Produksi
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form action="{{ route('production.update', ['production' => $production->production_id]) }}" method="POST" id="editForm{{ $production->production_id }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tim Produksi</label>
                                    <input name="production_lead" required type="text" class="form-control" value="{{ old('production_lead', $production->production_lead) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Label Produksi</label>
                                    <input name="production_label" required type="text" class="form-control" value="{{ old('production_label', $production->production_label) }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal Produksi</label>
                                    <input name="production_date" required type="date" class="form-control" value="{{ old('production_date', $production->production_date) }}">
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Bahan Baku</label>
                                        <button type="button" class="btn btn-sm btn-success add-material-edit" data-production-id="{{ $production->production_id }}">
                                            <i class="ti ti-plus"></i> Tambah
                                        </button>
                                    </div>
                                    <div id="editMaterialsContainer{{ $production->production_id }}">
                                        @if($production->rawStocks && $production->rawStocks->count() > 0)
                                            @foreach($production->rawStocks as $index => $rawStock)
                                                <div class="material-row-edit border rounded-3 p-2 mb-2">
                                                    <div class="row g-2">
                                                        <div class="col-md-6">
                                                            <select name="materials[{{ $index }}][material_id]" class="form-select form-select-sm" required>
                                                                <option value="">-- Pilih Bahan Baku --</option>
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
                                                            <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-material-edit" {{ $production->rawStocks->count() == 1 ? 'disabled' : '' }}>
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="material-row-edit border rounded-3 p-2 mb-2">
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <select name="materials[0][material_id]" class="form-select form-select-sm" required>
                                                            <option value="">-- Pilih Bahan Baku --</option>
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
                                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-material-edit" disabled>
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
                                    <input name="total_unit" required type="number" min="1" class="form-control" value="{{ old('total_unit', $production->total_unit) }}">
                                </div>
                                <div class="modal-footer border-0 pt-3">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        <i class="ti ti-x"></i> Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal View (readonly) --}}
            <div class="modal fade" id="viewModal{{ $production->production_id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $production->production_id }}" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-secondary border-0">
                            <h5 class="modal-title text-white fw-semibold" id="viewModalLabel{{ $production->production_id }}">
                                <i class="ti ti-eye me-2"></i>Detail Produksi
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Produksi</label>
                                <div class="form-control-plaintext">{{ $production->production_date ? \Carbon\Carbon::parse($production->production_date)->format('d M Y') : '-' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tim Produksi</label>
                                <div class="form-control-plaintext">{{ $production->production_lead }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Label Produksi</label>
                                <div class="form-control-plaintext">{{ $production->production_label }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Bahan Baku</label>
                                @if($production->rawStocks && $production->rawStocks->count() > 0)
                                    <ul class="list-unstyled mb-0">
                                        @foreach($production->rawStocks as $rawStock)
                                            <li class="d-flex justify-content-between text-muted">
                                                <span>{{ $rawStock->material_name }}</span>
                                                <small class="fw-semibold">{{ number_format($rawStock->pivot->material_qty) }} {{ $rawStock->satuan }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-muted">-</div>
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Total Unit</label>
                                <div class="form-control-plaintext">{{ number_format($production->total_unit) }}</div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-3">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="ti ti-x"></i> Tutup
                            </button>
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
                            row.className = 'material-row-edit border rounded-3 p-2 mb-2';
                            row.innerHTML = `
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <select name="materials[${editMaterialIndex}][material_id]" class="form-select form-select-sm" required>
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
                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-material-edit">
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
            @empty
            <tr>
                <td colspan="7" class="text-center py-5">
                    <div class="text-muted">
                        <i class="ti ti-clipboard-list fs-1 d-block mb-3"></i>
                        <p class="mb-0">Tidak ada data production</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($productions) && $productions->hasPages())
<div class="card-footer bg-white border-top py-3">
    <div class="d-flex justify-content-center">
        {{ $productions->links() }}
    </div>
</div>
@endif