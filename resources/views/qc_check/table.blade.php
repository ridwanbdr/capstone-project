<div class="table-responsive px-5 py-3">
    <table class="table align-middle mb-0">
        <thead class="table-light text-center">
            <tr>
                <th class="ps-4">
                    <span class="fw-semibold text-dark">QC ID</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">Produk</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Qty Lolos</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Qty Reject</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Tanggal</span>
                </th>
                <th>
                    <span class="fw-semibold text-dark">QC Checker</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Status</span>
                </th>
                <th class="text-center">
                    <span class="fw-semibold text-dark">Aksi</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse($qcChecks as $qc)
            <tr class="border-bottom">
                <td class="ps-4">
                    <div class="text-muted">{{ $qc->qc_id }}</div>
                </td>
                <td>
                    <span class="fw-semibold">{{ $qc->detailProduct?->product_name ?? '-' }}</span>
                    @if($qc->detailProduct?->size)
                        <br><small class="text-muted">Size: {{ $qc->detailProduct->size->size_label }}</small>
                    @endif
                </td>
                <td class="text-center">
                    <span class="fw-bold text-success">{{ number_format($qc->qty_passed) }}</span>
                </td>
                <td class="text-center">
                    <span class="fw-bold text-danger">{{ number_format($qc->qty_reject) }}</span>
                </td>
                <td class="text-center">
                    <span class="text-muted">{{ $qc->date->format('d/m/Y') }}</span>
                </td>
                <td>
                    <span class="fw-normal">{{ $qc->qc_checker ?? '-' }}</span>
                </td>
                <td class="text-center">
                    @if ($qc->qc_label === 'PASS')
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold">PASS</span>
                    @elseif ($qc->qc_label === 'FAIL')
                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 fw-semibold">FAIL</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 fw-semibold">PENDING</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('qc_check.edit', $qc->qc_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="ti ti-edit"></i>
                        </a>
                        <form action="{{ route('qc_check.destroy', $qc->qc_id) }}"
                              method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
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
                        <i class="ti ti-clipboard-list fs-1 d-block mb-3"></i>
                        <p class="mb-0">Tidak ada data Quality Control</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($qcChecks) && $qcChecks->hasPages())
<div class="card-footer bg-white border-top py-3">
    <div class="d-flex justify-content-center">
        {{ $qcChecks->links() }}
    </div>
</div>
@endif
