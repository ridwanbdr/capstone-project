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
                id="product_id" name="product_id" required>
          <option value="">-- Pilih Produk --</option>
          @foreach($detailProducts as $prod)
            <option value="{{ $prod->product_id }}" 
                    {{ old('product_id', $qcCheck->product_id ?? '') == $prod->product_id ? 'selected' : '' }}>
              {{ $prod->product_name }}
            </option>
          @endforeach
        </select>
        @error('product_id')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
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
               min="0" required>
        @error('qty_passed')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>
    </div>
    <div class="col-lg-6">
      <div class="mb-3">
        <label for="qty_reject" class="form-label">Qty Reject</label>
        <input type="number" class="form-control @error('qty_reject') is-invalid @enderror" 
               id="qty_reject" name="qty_reject" value="{{ old('qty_reject', $qcCheck->qty_reject ?? 0) }}" 
               min="0" required>
        @error('qty_reject')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
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

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
      <i class="ti ti-check"></i> {{ isset($qcCheck) ? 'Update' : 'Simpan' }}
    </button>
    <a href="{{ route('qc_check.index') }}" class="btn btn-secondary">
      <i class="ti ti-arrow-left"></i> Kembali
    </a>
  </div>
</form>
