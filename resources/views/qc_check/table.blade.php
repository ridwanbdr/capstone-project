<table class="table text-nowrap mb-0 align-middle">
  <thead class="text-dark fs-4">
    <tr>
      <th class="border-bottom-0">
        <h6 class="fw-semibold mb-0">QC ID</h6>
      </th>
      <th class="border-bottom-0">
        <h6 class="fw-semibold mb-0">Produk</h6>
      </th>
      <th class="border-bottom-0">
        <h6 class="fw-semibold mb-0">Qty Lolos</h6>
      </th>
      <th class="border-bottom-0">
        <h6 class="fw-semibold mb-0">Qty Reject</h6>
      </th>
      <th class="border-bottom-0">
        <h6 class="fw-semibold mb-0">Tanggal</h6>
      </th>
      <th class="border-bottom-0">
        <h6 class="fw-semibold mb-0">QC Checker</h6>
      </th>
      <th class="border-bottom-0">
        <h6 class="fw-semibold mb-0">Status</h6>
      </th>
      <th class="border-bottom-0">
        <h6 class="fw-semibold mb-0">Aksi</h6>
      </th>
    </tr>
  </thead>
  <tbody>
    @forelse($qcChecks as $qc)
      <tr>
        <td class="border-bottom-0">
          <h6 class="fw-semibold mb-0">{{ $qc->qc_id }}</h6>
        </td>
        <td class="border-bottom-0">
          <p class="mb-0 fw-normal">{{ $qc->detailProduct?->product_name ?? '-' }}</p>
        </td>
        <td class="border-bottom-0">
          <h6 class="fw-semibold mb-0">{{ $qc->qty_passed }}</h6>
        </td>
        <td class="border-bottom-0">
          <h6 class="fw-semibold mb-0 text-danger">{{ $qc->qty_reject }}</h6>
        </td>
        <td class="border-bottom-0">
          <p class="mb-0 fw-normal">{{ $qc->date->format('d/m/Y') }}</p>
        </td>
        <td class="border-bottom-0">
          <p class="mb-0 fw-normal">{{ $qc->qc_checker ?? '-' }}</p>
        </td>
        <td class="border-bottom-0">
          @if ($qc->qc_label === 'PASS')
            <span class="badge bg-success rounded-3 fw-semibold">PASS</span>
          @elseif ($qc->qc_label === 'FAIL')
            <span class="badge bg-danger rounded-3 fw-semibold">FAIL</span>
          @else
            <span class="badge bg-warning rounded-3 fw-semibold">PENDING</span>
          @endif
        </td>
        <td class="border-bottom-0">
          <a href="{{ route('qc_check.edit', $qc->qc_id) }}" class="btn btn-sm btn-info">
            <i class="ti ti-edit"></i> Edit
          </a>
          <button type="button" class="btn btn-sm btn-danger" onclick="deleteQc({{ $qc->qc_id }})">
            <i class="ti ti-trash"></i> Hapus
          </button>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="8" class="text-center py-4">
          <p class="text-muted mb-0">Tidak ada data Quality Control</p>
        </td>
      </tr>
    @endforelse
  </tbody>
</table>

@push('scripts')
<script>
function deleteQc(id) {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    document.getElementById('delete-form-' + id).submit();
  }
}
</script>
@endpush
