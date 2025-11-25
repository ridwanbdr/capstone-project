<form action="{{ route('raw_stock.store') }}" method="POST" class="mb-4">
    @csrf
    <div class="row mb-3">
        <div class="col">
            <input type="text" name="material_name" class="form-control" placeholder="Nama Material" required>
        </div>
        <div class="col">
            <input type="number" name="material_qty" class="form-control" placeholder="Qty" required>
        </div>
        <div class="col">
            <input type="number" name="unit_price" class="form-control" placeholder="Harga Satuan" required>
        </div>
        <div class="col">
            <input type="date" name="added_on" class="form-control" required>
        </div>
        <div class="col">
            <button type="submit" class="btn btn-primary">Tambah</button>
        </div>
    </div>
</form>