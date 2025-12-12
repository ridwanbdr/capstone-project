<div class="table-responsive px-5 py-2">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light text-center">
            <tr>
                <th class="ps-4">
                    <span class="fw-semibold text-dark">Tanggal Input</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Nama Material</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Quantity</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Satuan</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Kategori</span>
                </th>
                <th class="text-end">
                    <span class="fw-semibold text-dark">Harga Satuan</span>
                </th>
                <th class="text-end pe-4">
                    <span class="fw-semibold text-dark">Total Harga</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Aksi</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($stocks as $stock)
            <tr class="border-bottom">
                <td class="ps-4">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar text-muted me-2"></i>
                        <span class="text-muted">{{ $stock->added_on ? \Carbon\Carbon::parse($stock->added_on)->format('d M Y') : '-' }}</span>
                    </div>
                </td>

                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                            <i class="ti ti-package text-primary"></i>
                        </div>
                        <span class="fw-semibold">{{ $stock->material_name }}</span>
                    </div>
                </td>

                <td class="text-center">
                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                        {{ number_format($stock->material_qty) }}
                    </span>
                </td>

                <td class="text-center">
                    <span class="text-muted">{{ strtoupper($stock->satuan) }}</span>
                </td>

                <td>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                        {{ $stock->category }}
                    </span>
                </td>

                <td class="text-end">
                    <span class="fw-semibold text-success">Rp {{ number_format($stock->unit_price, 0, ',', '.') }}</span>
                </td>

                <td class="text-end pe-4">
                    <span class="fw-bold text-primary">Rp {{ number_format($stock->material_qty * $stock->unit_price, 0, ',', '.') }}</span>
                </td>

                <td class="text-center">
                    <div class="d-flex gap-2 justify-content-center">
                        {{-- Edit Button --}}
                        <button type="button" 
                                class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal{{ $stock->material_id }}"
                                title="Edit">
                            <i class="ti ti-edit"></i>
                        </button>

                        {{-- Delete Button --}}
                        <form action="{{ route('raw_stock.destroy', $stock->material_id) }}" 
                              method="POST" 
                              style="display:inline"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus material ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                    title="Hapus">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            {{-- Edit Modal --}}
            <div class="modal fade" id="editModal{{ $stock->material_id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $stock->material_id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-primary border-0">
                            <h5 class="modal-title fw-semibold text-white" id="editModalLabel{{ $stock->material_id }}">
                                <i class="ti ti-edit me-2"></i>Update Material
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form action="{{ route('raw_stock.update', ['raw_stock' => $stock->material_id]) }}" method="POST" id="editForm{{ $stock->material_id }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-3">
                                    <label for="materialName{{ $stock->material_id }}" class="form-label fw-semibold">
                                        <i class="ti ti-tag me-1 text-primary"></i>Nama Material
                                    </label>
                                    <input name="material_name" 
                                           required 
                                           type="text" 
                                           class="form-control" 
                                           id="materialName{{ $stock->material_id }}" 
                                           value="{{ old('material_name', $stock->material_name) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="category{{ $stock->material_id }}" class="form-label fw-semibold">
                                        <i class="ti ti-category me-1 text-primary"></i>Kategori
                                    </label>
                                    <select name="category" class="form-select" required id="category{{ $stock->material_id }}">
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="Kain Utama" {{ old('category', $stock->category) == 'Kain Utama' ? 'selected' : '' }}>Kain Utama</option>
                                        <option value="Benang" {{ old('category', $stock->category) == 'Benang' ? 'selected' : '' }}>Benang</option>
                                        <option value="Aksesoris" {{ old('category', $stock->category) == 'Aksesoris' ? 'selected' : '' }}>Aksesoris</option>
                                        <option value="Bahan pelengkap" {{ old('category', $stock->category) == 'Bahan pelengkap' ? 'selected' : '' }}>Bahan pelengkap</option>
                                        <option value="Bahan kemasan" {{ old('category', $stock->category) == 'Bahan kemasan' ? 'selected' : '' }}>Bahan kemasan</option>
                                        <option value="Bahan lainnya" {{ old('category', $stock->category) == 'Bahan lainnya' ? 'selected' : '' }}>Bahan lainnya</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="materialQty{{ $stock->material_id }}" class="form-label fw-semibold">
                                            <i class="ti ti-hash me-1 text-primary"></i>Quantity
                                        </label>
                                        <input name="material_qty" 
                                               required 
                                               type="number" 
                                               min="0" 
                                               class="form-control" 
                                               id="materialQty{{ $stock->material_id }}" 
                                               value="{{ old('material_qty', $stock->material_qty) }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="satuan{{ $stock->material_id }}" class="form-label fw-semibold">
                                            <i class="ti ti-ruler me-1 text-primary"></i>Satuan
                                        </label>
                                        <select name="satuan" class="form-select" required id="satuan{{ $stock->material_id }}">
                                            <option value="">-- Pilih Satuan --</option>
                                            <option value="pcs" {{ old('satuan', $stock->satuan) == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                            <option value="roll" {{ old('satuan', $stock->satuan) == 'roll' ? 'selected' : '' }}>Roll</option>
                                            <option value="kg" {{ old('satuan', $stock->satuan) == 'kg' ? 'selected' : '' }}>Kg</option>
                                            <option value="meter" {{ old('satuan', $stock->satuan) == 'meter' ? 'selected' : '' }}>Meter</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="unitPrice{{ $stock->material_id }}" class="form-label fw-semibold">
                                        <i class="ti ti-currency-rupiah me-1 text-primary"></i>Harga Satuan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input name="unit_price" 
                                               required 
                                               type="number" 
                                               step="0.01" 
                                               min="0" 
                                               class="form-control" 
                                               id="unitPrice{{ $stock->material_id }}" 
                                               value="{{ old('unit_price', $stock->unit_price) }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="addedOn{{ $stock->material_id }}" class="form-label fw-semibold">
                                        <i class="ti ti-calendar me-1 text-primary"></i>Tanggal Input
                                    </label>
                                    <input name="added_on" 
                                           required 
                                           type="date" 
                                           class="form-control" 
                                           id="addedOn{{ $stock->material_id }}" 
                                           value="{{ old('added_on', $stock->added_on) }}">
                                </div>

                                <div class="modal-footer border-0 pt-3">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        <i class="ti ti-x me-1"></i>Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-check me-1"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-muted">
                        <i class="ti ti-inbox fs-1 d-block mb-3"></i>
                        <p class="mb-0">Tidak ada data material stock</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if(isset($stocks) && $stocks->hasPages())
<div class="card-footer bg-white border-top py-3">
    <div class="d-flex justify-content-center">
        {{ $stocks->links() }}
    </div>
</div>
@endif
