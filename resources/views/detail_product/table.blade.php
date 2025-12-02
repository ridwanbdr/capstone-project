<div class="table-responsive px-5 py-3">
    <table class="table align-middle mb-0">
        <thead class="table-light text-center">
            <tr>
                <th class="ps-4">
                    <span class="fw-semibold text-dark">Product ID</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Production ID</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Label</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Product Name</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Size</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Qty Unit</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Price Unit</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Aksi</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($detailProducts as $item)
            <tr class="border-bottom">
                <td class="ps-4">
                    <div class="text-muted">{{ $item->product_id }}</div>
                </td>
                <td>
                    <span class="fw-semibold">{{ $item->production_id }}</span>
                </td>
                <td>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                        {{ $item->production->production_label }}
                    </span>
                </td>
                <td>
                    <span class="fw-semibold">{{ $item->product_name }}</span>
                </td>
                <td class="text-center">
                    <span class="text-muted">
                        {{ $item->size_label ?? ($item->size->size_label ?? '-') }}
                        <!-- {{ $item->size->size_label }} -->
                    </span>
                </td>
                <td class="text-center">
                    <span class="fw-bold text-primary">{{ number_format($item->qty_unit ?? 0) }}</span>
                </td>
                <td class="text-center">
                    <span class="fw-semibold text-success">{{ number_format($item->price_unit ?? 0) }}</span>
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button"
                                class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $item->product_id }}">
                            <i class="ti ti-edit"></i>
                        </button>

                        <form action="{{ route('detail_product.destroy', ['detail_product' => $item->product_id]) }}"
                              method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
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
            <div class="modal fade" id="editModal{{ $item->product_id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $item->product_id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-primary border-0">
                            <h5 class="modal-title text-white fw-semibold" id="editModalLabel{{ $item->product_id }}">
                                <i class="ti ti-edit me-2"></i>Edit Detail Product
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form action="{{ route('detail_product.update', ['detail_product' => $item->product_id]) }}" method="POST" id="editForm{{ $item->product_id }}">
                                @csrf
                                @method('PUT')

                                {{-- Production ID (readonly shown, submitted via hidden input) --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Production ID</label>
                                    <div class="form-control-plaintext">{{ $item->production_id }}</div>
                                    <input type="hidden" name="production_id" value="{{ $item->production_id }}">
                                </div>

                                {{-- Production Label (readonly display only; not submitted since column removed) --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Production Label</label>
                                    <div class="form-control-plaintext">{{ $item->production->production_label ?? '-' }}</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Product Name</label>
                                    <input name="product_name" required type="text" class="form-control" value="{{ old('product_name', $item->product_name) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Size</label>
                                    <select name="size_id" class="form-select" required>
                                        <option value="">-- Pilih ukuran --</option>
                                        @foreach($sizes ?? collect() as $size)
                                            <option value="{{ $size->size_id }}" {{ $item->size_id == $size->size_id ? 'selected' : '' }}>
                                                {{ $size->size_label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Qty Unit</label>
                                    <input name="qty_unit" type="number" min="0" class="form-control" value="{{ old('qty_unit', $item->qty_unit) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Price Unit</label>
                                    <input name="price_unit" type="number" min="0" class="form-control" value="{{ old('price_unit', $item->price_unit) }}">
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
            <div class="modal fade" id="viewModal{{ $item->product_id }}" tabindex="-1" aria-labelledby="viewModalLabel{{ $item->product_id }}" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-secondary border-0">
                            <h5 class="modal-title text-white fw-semibold" id="viewModalLabel{{ $item->product_id }}">
                                <i class="ti ti-eye me-2"></i>Detail Product
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Product ID</label>
                                <div class="form-control-plaintext">{{ $item->product_id }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Production ID</label>
                                <div class="form-control-plaintext">{{ $item->production_id }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Production Label</label>
                                <div class="form-control-plaintext">{{ $item->production_label }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Product Name</label>
                                <div class="form-control-plaintext">{{ $item->product_name }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Size</label>
                                <div class="form-control-plaintext">{{ $item->size_label ?? ($item->size->size_label ?? '-') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Qty Unit</label>
                                <div class="form-control-plaintext">{{ number_format($item->qty_unit ?? 0) }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Price Unit</label>
                                <div class="form-control-plaintext">{{ number_format($item->price_unit ?? 0) }}</div>
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

            @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <div class="text-muted">
                        <i class="ti ti-clipboard-list fs-1 d-block mb-3"></i>
                        <p class="mb-0">Tidak ada data detail product</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($detailProducts) && $detailProducts->hasPages())
<div class="card-footer bg-white border-top py-3">
    <div class="d-flex justify-content-center">
        {{ $detailProducts->links() }}
    </div>
</div>
@endif