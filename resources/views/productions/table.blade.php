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
                        {{-- Edit now navigates to dedicated edit page --}}
                        <a href="{{ route('production.edit', $production->production_id) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="ti ti-edit"></i>
                        </a>

                        <!-- View button (mata) - navigasi ke halaman detail_product.index dengan production_id dan production_label -->
                        <a href="{{ route('detail_product.index', ['production_id' => $production->production_id, 'production_label' => $production->production_label]) }}"
                           class="btn btn-sm btn-outline-warning rounded-pill px-3"
                           title="Lihat detail produk">
                            <i class="ti ti-eye"></i>
                        </a>

                        <form action="{{ route('production.destroy', $production->production_id) }}"
                              method="POST"
                              style="display:inline"
                              class="deleteProductionForm"
                              data-production-label="{{ $production->production_label }}">
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

{{-- SweetAlert CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteProductionForms = document.querySelectorAll('.deleteProductionForm');

    deleteProductionForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const productionLabel = form.getAttribute('data-production-label');

            Swal.fire({
                title: 'Hapus Produksi?',
                html: `
                    <p>Apakah Anda yakin ingin menghapus produksi:</p>
                    <p style="font-weight: bold; color: #0d6efd;">${productionLabel}</p>
                    <p style="font-size: 12px; color: #dc3545;"><i class="ti ti-alert-circle"></i> Stok material TIDAK akan dikembalikan</p>
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
</div>