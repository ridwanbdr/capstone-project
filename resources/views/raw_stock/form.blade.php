<form action="{{ route('raw_stock.store') }}" method="POST" class="mb-4">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3 d-flex flex-column gap-3">
            <div class="col">
                <label for="material_name" class="form-label">Nama Material</label>
                <input type="text" name="material_name" class="form-control" placeholder="Nama Material" required>
            </div>
            {{-- list dropdown kategori --}}
            <div class="col">
                <label for="category" class="form-label">Kategori</label>
                <select name="category" class="form-control">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Kain Utama">Kain Utama</option>
                    <option value="Benang">Benang</option>
                    <option value="Aksesoris">Aksesoris</option>
                    <option value="Bahan pelengkap">Bahan pelengkap</option>
                    <option value="Bahan kemasan">Bahan kemasan</option>
                    <option value="Bahan lainnya">Bahan lainnya</option>
                </select>
            </div>
            <div class="col">
                <label for="material_qty" class="form-label">Quantity</label>
                <input type="number" name="material_qty" class="form-control" placeholder="Qty" required>
            </div>            
        </div>
        <div class="col-md-6 mb-3 d-flex flex-column gap-3">
            <div class="col">
                <label for="satuan" class="form-label">Satuan</label>
                <select name="satuan" class="form-control">
                    <option value="">-- Pilih Satuan --</option>
                    <option value="pcs">Pcs</option>
                    <option value="roll">Roll</option>
                    <option value="kg">Kg</option>
                    <option value="meter">Meter</option>
                </select>
            </div>        
            <div class="col">
                <label for="unit_price" class="form-label">Harga Satuan (Rp)</label>
                <input type="number" name="unit_price" class="form-control" placeholder="Harga Satuan" required>
            </div>
            <div class="col">
                <div class="row">
                    <div class="col">
                        <label for="added_on" class="form-label">Tanggal Beli</label>
                        <input type="date" name="added_on" class="form-control" required>
                    </div>
                    <div class="col d-flex justify-content-end align-items-end">
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </div>                
            </div>        
        </div>
    </div>
</form>

{{-- script konversi format rupiah --}}
<script>
    const unitPriceInput = document.getElementById('unit_price');
    unitPriceInput.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value) {
            this.value = formatRupiah(value);
        }
    });
</script>