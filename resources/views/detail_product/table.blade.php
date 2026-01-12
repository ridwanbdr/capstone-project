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
                        {{-- Edit now navigates to dedicated edit page --}}
                        <a href="{{ route('detail_product.edit', $item->product_id) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="ti ti-edit"></i>
                        </a>

                        <form action="{{ route('detail_product.destroy', ['detail_product' => $item->product_id]) }}"
                              method="POST"
                              style="display:inline"
                              class="deleteForm"
                              data-product-name="{{ $item->product_name }}">
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

{{-- SweetAlert CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('.deleteForm');

    deleteForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const productName = form.getAttribute('data-product-name');

            Swal.fire({
                title: 'Hapus Produk?',
                html: `
                    <p>Apakah Anda yakin ingin menghapus produk:</p>
                    <p style="font-weight: bold; color: #0d6efd;">${productName}</p>
                    <p style="font-size: 12px; color: #dc3545;"><i class="ti ti-alert-circle"></i> Tindakan ini tidak dapat dibatalkan</p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>