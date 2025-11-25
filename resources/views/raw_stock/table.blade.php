<div class="col-lg-12 d-flex align-items-stretch">
    <div class="card w-100">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap mb-0 align-middle">
                    <thead class="text-dark fs-4">
                        <tr>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Tanggal Input</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Nama Material</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Quantity</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Harga Satuan</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Total Harga</h6>
                        </th>
                        <th class="border-bottom-0">
                            <h6 class="fw-semibold mb-0">Aksi</h6>
                        </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stocks as $stock)
                        <tr>
                            <!-- Tanggal Input (sesuai urutan header) -->
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $stock->added_on ? \Carbon\Carbon::parse($stock->added_on)->format('Y-m-d') : '' }}</h6>
                            </td>

                            <!-- Nama Material -->
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $stock->material_name }}</h6>
                            </td>

                            <!-- Quantity -->
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ $stock->material_qty }}</h6>
                            </td>

                            <!-- Harga Satuan -->
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ number_format($stock->unit_price) }}</h6>
                            </td>

                            <!-- Total Harga = quantity * harga satuan -->
                            <td class="border-bottom-0">
                                <h6 class="fw-semibold mb-0">{{ number_format($stock->material_qty * $stock->unit_price) }}</h6>
                            </td>

                            <td class="border-bottom-0">
                                {{-- Delete form (tidak mengandung modal) --}}
                                <form action="{{ route('raw_stock.destroy', $stock->material_id) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>

                                {{-- Tombol Edit (memanggil modal) --}}
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $stock->material_id }}">
                                    Edit
                                </button>

                                {{-- Modal (setiap baris punya ID unik) --}}
                                <div class="modal fade" id="editModal{{ $stock->material_id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $stock->material_id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="editModalLabel{{ $stock->material_id }}">Edit Raw Material</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('raw_stock.update', ['raw_stock' => $stock->material_id]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-3">
                                                        <label for="materialName{{ $stock->material_id }}" class="col-form-label">Nama Material:</label>
                                                        <input name="material_name" required type="text" class="form-control" id="materialName{{ $stock->material_id }}" value="{{ old('material_name', $stock->material_name) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="materialQty{{ $stock->material_id }}" class="col-form-label">Quantity:</label>
                                                        <input name="material_qty" required type="number" min="0" class="form-control" id="materialQty{{ $stock->material_id }}" value="{{ old('material_qty', $stock->material_qty) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="unitPrice{{ $stock->material_id }}" class="col-form-label">Harga Satuan:</label>
                                                        <input name="unit_price" required type="number" step="0.01" min="0" class="form-control" id="unitPrice{{ $stock->material_id }}" value="{{ old('unit_price', $stock->unit_price) }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="addedOn{{ $stock->material_id }}" class="col-form-label">Tanggal Input:</label>
                                                        <input name="added_on" required type="date" class="form-control" id="addedOn{{ $stock->material_id }}" value="{{ old('added_on', $stock->added_on) }}">
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

                            </td>
                        </tr>
                        @endforeach    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>