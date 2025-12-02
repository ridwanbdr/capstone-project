<form action="{{ isset($qcCheck) ? route('qc_check.update', $qcCheck->id) : route('qc_check.store') }}" method="POST">
  @csrf
  @if (isset($qcCheck))
    @method('PUT')
  @endif

  <div class="mb-3">
    <label for="qc_id" class="form-label">QC ID</label>
    <input type="text" class="form-control @error('qc_id') is-invalid @enderror" 
           id="qc_id" name="qc_id" value="{{ old('qc_id', $qcCheck->qc_id ?? '') }}" 
           {{ isset($qcCheck) ? 'readonly' : '' }}>
    @error('qc_id')
      <span class="invalid-feedback">{{ $message }}</span>
    @enderror
  </div>

  <div class="row">
    <div class="col-lg-6">
      <div class="mb-3">
        <label for="product_name" class="form-label">Produk</label>
        <select class="form-control @error('product_name') is-invalid @enderror" 
                id="product_name" name="product_name" required>
          <option value="">-- Pilih Produk --</option>
          @foreach($productions as $prod)
            <option value="{{ $prod->product_name }}" 
                    {{ old('product_name', $qcCheck->product_name ?? '') === $prod->product_name ? 'selected' : '' }}>
              {{ $prod->product_name }}
            </option>
          @endforeach
        </select>
        @error('product_name')
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
        <select class="form-control @error('qc_checker') is-invalid @enderror" 
                id="qc_checker" name="qc_checker">
          <option value="">-- Pilih QC Checker --</option>
          @foreach($users as $user)
            <option value="{{ $user->user_id }}" 
                    {{ old('qc_checker', $qcCheck->qc_checker ?? '') === $user->user_id ? 'selected' : '' }}>
              {{ $user->nama_lengkap }}
            </option>
          @endforeach
        </select>
        @error('qc_checker')
          <span class="invalid-feedback">{{ $message }}</span>
        @enderror
      </div>
    </div>
    <div class="col-lg-6">
      <div class="mb-3">
        <label for="qc_label" class="form-label">Status</label>
        <select class="form-control @error('qc_label') is-invalid @enderror" 
                id="qc_label" name="qc_label" required>
          <option value="">-- Pilih Status --</option>
          <option value="PASS" {{ old('qc_label', $qcCheck->qc_label ?? '') === 'PASS' ? 'selected' : '' }}>PASS</option>
          <option value="FAIL" {{ old('qc_label', $qcCheck->qc_label ?? '') === 'FAIL' ? 'selected' : '' }}>FAIL</option>
          <option value="PENDING" {{ old('qc_label', $qcCheck->qc_label ?? '') === 'PENDING' ? 'selected' : '' }}>PENDING</option>
        </select>
        @error('qc_label')
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
