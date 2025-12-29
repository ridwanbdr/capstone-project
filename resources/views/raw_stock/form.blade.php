<form action="{{ route('raw_stock.store') }}" method="POST" id="rawStockForm">
    @csrf
    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="existing_material" class="form-label fw-semibold">
                    <i class="ti ti-tag me-1 text-primary"></i>Nama Material
                </label>

                <select name="material_id" id="existing_material" class="form-select mb-2">
                    <option value="">-- Tambah Nama Baru --</option>
                    @if(isset($materials))
                        @foreach($materials as $m)
                            <option value="{{ $m->material_id }}"
                                    data-name="{{ $m->material_name }}"
                                    data-satuan="{{ $m->satuan }}"
                                    data-category="{{ $m->category }}"
                                    data-unit_price="{{ $m->unit_price }}">
                                {{ $m->material_name }} - {{ $m->satuan }}
                            </option>
                        @endforeach
                    @endif
                </select>

                <input type="text" 
                    name="material_name" 
                    id="material_name"
                    class="form-control" 
                    placeholder="Masukkan nama material" 
                    value="{{ old('material_name') }}"
                    required>
                @error('material_name')
                    <div class="text-danger small mt-1">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category" class="form-label fw-semibold">
                    <i class="ti ti-category me-1 text-primary"></i>Kategori
                </label>
                <select name="category" id="category" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Kain Utama" {{ old('category') == 'Kain Utama' ? 'selected' : '' }}>Kain Utama</option>
                    <option value="Benang" {{ old('category') == 'Benang' ? 'selected' : '' }}>Benang</option>
                    <option value="Aksesoris" {{ old('category') == 'Aksesoris' ? 'selected' : '' }}>Aksesoris</option>
                    <option value="Bahan pelengkap" {{ old('category') == 'Bahan pelengkap' ? 'selected' : '' }}>Bahan pelengkap</option>
                    <option value="Bahan kemasan" {{ old('category') == 'Bahan kemasan' ? 'selected' : '' }}>Bahan kemasan</option>
                    <option value="Bahan lainnya" {{ old('category') == 'Bahan lainnya' ? 'selected' : '' }}>Bahan lainnya</option>
                </select>
                @error('category')
                    <div class="text-danger small mt-1">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="material_qty" class="form-label fw-semibold">
                    <i class="ti ti-hash me-1 text-primary"></i>Quantity
                </label>
                <input type="number" 
                    name="material_qty" 
                    id="material_qty"
                    class="form-control" 
                    placeholder="0" 
                    value="{{ old('material_qty') }}"
                    min="0"
                    required>
                @error('material_qty')
                    <div class="text-danger small mt-1">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-lg-6">
            <div class="mb-3">
                <label for="satuan" class="form-label fw-semibold">
                    <i class="ti ti-ruler me-1 text-primary"></i>Satuan
                </label>
                <select name="satuan" id="satuan" class="form-select" required>
                    <option value="">-- Pilih Satuan --</option>
                    <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                    <option value="roll" {{ old('satuan') == 'roll' ? 'selected' : '' }}>Roll</option>
                    <option value="kg" {{ old('satuan') == 'kg' ? 'selected' : '' }}>Kg</option>
                    <option value="meter" {{ old('satuan') == 'meter' ? 'selected' : '' }}>Meter</option>
                </select>
                @error('satuan')
                    <div class="text-danger small mt-1">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="unit_price" class="form-label fw-semibold">
                    <i class="ti ti-currency-rupiah me-1 text-primary"></i>Harga Satuan (Rp)
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light">Rp</span>
                    <input type="text" 
                        name="unit_price" 
                        id="unit_price"
                        class="form-control" 
                        placeholder="0" 
                        value="{{ old('unit_price') }}"
                        required>
                </div>
                @error('unit_price')
                    <div class="text-danger small mt-1">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="added_on" class="form-label fw-semibold">
                    <i class="ti ti-calendar me-1 text-primary"></i>Tanggal Pembelian
                </label>
                <input type="date" 
                    name="added_on" 
                    id="added_on"
                    class="form-control" 
                    value="{{ old('added_on', date('Y-m-d')) }}"
                    required>
                @error('added_on')
                    <div class="text-danger small mt-1">
                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>

    {{-- Submit Button --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-outline-secondary px-4">
                    <i class="ti ti-refresh"></i>Reset
                </button>
                <button type="submit" class="btn btn-primary px-5 shadow-sm">
                    <i class="ti ti-plus"></i>Tambah Material
                </button>
            </div>
        </div>
    </div>
</form>

{{-- Script untuk format rupiah --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitPriceInput = document.getElementById('unit_price');
    const existingSelect = document.getElementById('existing_material');
    const materialNameInput = document.getElementById('material_name');
    const categorySelect = document.getElementById('category');
    const satuanSelect = document.getElementById('satuan');
    
    if (unitPriceInput) {
        // Format saat input
        unitPriceInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value) {
                this.value = formatRupiah(value);
            }
        });
        
        // Konversi kembali ke angka saat submit
        const form = document.getElementById('rawStockForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const priceValue = unitPriceInput.value.replace(/\./g, '');
                unitPriceInput.value = priceValue;
            });
        }
    }

    // Jika user memilih material existing, isi input terkait dan disable editing
    if (existingSelect) {
        existingSelect.addEventListener('change', function() {
            const sel = this.options[this.selectedIndex];
            const id = this.value;

            if (id) {
                const name = sel.dataset.name || '';
                const satuan = sel.dataset.satuan || '';
                const category = sel.dataset.category || '';
                const unit_price = sel.dataset.unit_price || '';

                materialNameInput.value = name;
                materialNameInput.setAttribute('readonly', 'readonly');

                // set category
                if (category) {
                    for (let i=0;i<categorySelect.options.length;i++){
                        if (categorySelect.options[i].value === category) {
                            categorySelect.selectedIndex = i; break;
                        }
                    }
                    categorySelect.setAttribute('disabled', 'disabled');
                }

                // set satuan
                if (satuan) {
                    for (let i=0;i<satuanSelect.options.length;i++){
                        if (satuanSelect.options[i].value === satuan) {
                            satuanSelect.selectedIndex = i; break;
                        }
                    }
                    satuanSelect.setAttribute('disabled', 'disabled');
                }

                // set unit price (raw number)
                if (unit_price !== undefined) {
                    unitPriceInput.value = unit_price;
                }
            } else {
                // new entry: clear and enable
                materialNameInput.removeAttribute('readonly');
                materialNameInput.value = '';
                categorySelect.removeAttribute('disabled');
                categorySelect.selectedIndex = 0;
                satuanSelect.removeAttribute('disabled');
                satuanSelect.selectedIndex = 0;
                unitPriceInput.value = '';
            }
        });
    }
});

function formatRupiah(angka) {
    const number_string = angka.toString();
    const split = number_string.split(',');
    const sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    const ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
    if (ribuan) {
        const separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return rupiah;
}
</script>
