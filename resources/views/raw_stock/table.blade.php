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
                {{-- <th class="text-end pe-4">
                    <span class="fw-semibold text-dark">Total Harga</span>
                </th> --}}
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

                {{-- <td class="text-end pe-4">
                    <span class="fw-bold text-primary">Rp {{ number_format($stock->material_qty * $stock->unit_price, 0, ',', '.') }}</span>
                </td> --}}

                <td class="text-center">
                    <div class="d-flex gap-2 justify-content-center">
                        {{-- Edit Button now navigates to dedicated edit page --}}
                        <a href="{{ route('raw_stock.edit', $stock->material_id) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3"
                           title="Update Stok">
                            <i class="ti ti-edit"></i>
                        </a>

                        {{-- Delete Button --}}
                        <form action="{{ route('raw_stock.destroy', $stock->material_id) }}" 
                              method="POST" 
                              style="display:inline"
                              class="deleteForm"
                              data-material-name="{{ $stock->material_name }}">
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

{{-- SweetAlert CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.deleteForm');

    deleteButtons.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const materialName = form.getAttribute('data-material-name');

            // Konfirmasi hapus dengan SweetAlert
            Swal.fire({
                title: 'Hapus Material?',
                html: `
                    <p>Apakah Anda yakin ingin menghapus material:</p>
                    <p style="font-weight: bold; color: #0d6efd;">${materialName}</p>
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
