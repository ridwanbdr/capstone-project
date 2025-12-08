<form action="{{ isset($qcCheck) ? route('qc_check.update', $qcCheck->qc_id) : route('qc_check.store') }}" method="POST">
  @csrf
  @if (isset($qcCheck))
    @method('PUT')
  @endif

  <div class="row">
    <div class="col-lg-6">
      <div class="mb-3">
        <label for="product_id" class="form-label">Produk</label>
        <select class="form-control @error('product_id') is-invalid @enderror" 
                id="product_id" name="product_id" required onchange="updateTotalUnit()">
          <option value="">-- Pilih Produk --</option>
          @foreach($detailProducts as $prod)
            <option value="{{ $prod->product_id }}" 
                    data-total-unit="{{ $prod->production->total_unit }}"
                    {{ old('product_id', $qcCheck->product_id ?? '') == $prod->product_id ? 'selected' : '' }}>
              {{ $prod->product_name }}
            </option>
          @endforeach
        </select>
        @error('product_id')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
        <small class="text-muted" id="total-unit-info">
          @if(isset($qcCheck) && $qcCheck->detailProduct)
            Total unit produksi: <strong>{{ $qcCheck->detailProduct->production->total_unit }}</strong>
          @endif
        </small>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="mb-3">
        <label for="date" class="form-label">Tanggal</label>
        <input type="date" class="form-control @error('date') is-invalid @enderror" 
               id="date" name="date" value="{{ old('date', $qcCheck->date ?? date('Y-m-d')) }}" required>
        @error('date')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <div class="mb-3">
        <label for="qty_passed" class="form-label">Qty Lolos</label>
        <input type="number" class="form-control @error('qty_passed') is-invalid @enderror" 
               id="qty_passed" name="qty_passed" value="{{ old('qty_passed', $qcCheck->qty_passed ?? 0) }}" 
               min="0" required onchange="validateTotal()">
        @error('qty_passed')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
        <small class="text-muted">Total: <span id="total-qty">0</span></small>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="mb-3">
        <label for="qty_reject" class="form-label">Qty Reject</label>
        <input type="number" class="form-control @error('qty_reject') is-invalid @enderror" 
               id="qty_reject" name="qty_reject" value="{{ old('qty_reject', $qcCheck->qty_reject ?? 0) }}" 
               min="0" required onchange="validateTotal()">
        @error('qty_reject')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
        <small class="text-muted" id="total-warning"></small>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <div class="mb-3">
        <label for="qc_checker" class="form-label">QC Checker</label>
        <input type="text" class="form-control @error('qc_checker') is-invalid @enderror" 
               id="qc_checker" name="qc_checker" value="{{ old('qc_checker', $qcCheck->qc_checker ?? '') }}" 
               placeholder="Nama QC Checker">
        @error('qc_checker')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>
    </div>
  </div>

  <div class="mb-3">
    <label for="reject_reason" class="form-label">Keterangan (Opsional)</label>
    <textarea class="form-control @error('reject_reason') is-invalid @enderror" 
              id="reject_reason" name="reject_reason" rows="3"
              placeholder="Masukkan keterangan">{{ old('reject_reason', $qcCheck->reject_reason ?? '') }}</textarea>
    @error('reject_reason')
      <span class="invalid-feedback">{{ $message }}</span>
    @enderror
  </div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
      <i class="ti ti-check"></i> {{ isset($qcCheck) ? 'Update' : 'Simpan' }}
    </button>
    <a href="{{ route('qc_check.index') }}" class="btn btn-secondary">
      <i class="ti ti-arrow-left"></i> Kembali
    </a>
  </div>
</form>

@push('scripts')
<script>
function updateTotalUnit() {
  const select = document.getElementById('product_id');
  const selectedOption = select.options[select.selectedIndex];
  const totalUnit = selectedOption.getAttribute('data-total-unit');
  
  if (totalUnit) {
    document.getElementById('total-unit-info').innerHTML = `Total unit produksi: <strong>${totalUnit}</strong>`;
  } else {
    document.getElementById('total-unit-info').innerHTML = '';
  }
  
  validateTotal();
}

function validateTotal() {
  const qtyPassed = parseInt(document.getElementById('qty_passed').value) || 0;
  const qtyReject = parseInt(document.getElementById('qty_reject').value) || 0;
  const total = qtyPassed + qtyReject;
  
  const select = document.getElementById('product_id');
  const selectedOption = select.options[select.selectedIndex];
  const totalUnit = parseInt(selectedOption.getAttribute('data-total-unit')) || 0;
  
  document.getElementById('total-qty').textContent = total;
  
  if (totalUnit > 0) {
    if (total > totalUnit) {
      document.getElementById('total-warning').innerHTML = `<span class="text-danger">⚠️ Total melebihi kapasitas ${totalUnit} unit!</span>`;
    } else if (total < totalUnit) {
      document.getElementById('total-warning').innerHTML = `<span class="text-warning">⚠️ Total kurang ${totalUnit - total} unit (harus sama dengan ${totalUnit})</span>`;
    } else {
      document.getElementById('total-warning').innerHTML = `<span class="text-success">✓ Total sesuai kapasitas</span>`;
    }
  } else {
    document.getElementById('total-warning').innerHTML = '';
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  updateTotalUnit();
});
</script>
@endpush
