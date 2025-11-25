<?php

namespace App\Http\Controllers;

use App\Models\RawStock;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class RawStockController extends Controller
{
    // Tampilkan semua data raw stock dengan pagination
    public function index(Request $request)
    {
        $stocks = RawStock::query();

        // Cari berdasarkan nama material jika ada parameter pencarian
        if ($request->has('search') && $request->search) {
            $stocks = $stocks->where('material_name', 'like', '%' . $request->search . '%');
        }

        // Urutkan berdasarkan tanggal penambahan secara descending
        $stocks = $stocks->orderBy('added_on', 'desc');

        // Gunakan pagination untuk menghindari loading data yang berlebihan
        $stocks = $stocks->paginate(10)->withQueryString();

        return view('raw_stock.index', compact('stocks'));
    }

    // Tampilkan form tambah data
    public function create()
    {
        return view('raw_stock.create');
    }

    // Simpan data baru ke database
    public function store(Request $request)
    {
        // Validasi input dengan pesan error kustom
        $validated = $request->validate([
            'material_name' => 'required|string|max:255',
            'material_qty' => 'required|integer|min:0',
            'unit_price' => 'required|integer|min:0',
            'added_on' => 'required|date',
        ], [
            'material_name.required' => 'Nama material wajib diisi',
            'material_name.string' => 'Nama material harus berupa teks',
            'material_name.max' => 'Nama material maksimal 255 karakter',
            'material_qty.required' => 'Jumlah material wajib diisi',
            'material_qty.integer' => 'Jumlah material harus berupa angka',
            'material_qty.min' => 'Jumlah material tidak boleh kurang dari 0',
            'unit_price.required' => 'Harga per unit wajib diisi',
            'unit_price.integer' => 'Harga per unit harus berupa angka',
            'unit_price.min' => 'Harga per unit tidak boleh kurang dari 0',
            'added_on.required' => 'Tanggal penambahan wajib diisi',
            'added_on.date' => 'Format tanggal tidak valid',
        ]);

        try {
            $total_price = $validated['material_qty'] * $validated['unit_price'];

            RawStock::create([
                'material_name' => $validated['material_name'],
                'material_qty' => $validated['material_qty'],
                'unit_price' => $validated['unit_price'],
                'total_price' => $total_price,
                'added_on' => $validated['added_on'],
            ]);

            return redirect()->route('raw_stock.index')->with('success', 'Data berhasil ditambahkan');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    // Tampilkan detail satu data
    public function show($id)
    {
        try {
            $stock = RawStock::findOrFail($id);
            return view('raw_stock.show', compact('stock'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('raw_stock.index')->with('error', 'Data tidak ditemukan');
        }
    }

    // Tampilkan form edit data
    public function edit($id)
    {
        try {
            $stock = RawStock::findOrFail($id);
            return view('raw_stock.edit', compact('stock'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('raw_stock.index')->with('error', 'Data tidak ditemukan');
        }
    }

    // Update data yang sudah ada
    public function update(Request $request, $id)
    {
        // Validasi input dengan pesan error kustom
        $validated = $request->validate([
            'material_name' => 'required|string|max:255',
            'material_qty' => 'required|integer|min:0',
            'unit_price' => 'required|integer|min:0',
            'added_on' => 'required|date',
        ], [
            'material_name.required' => 'Nama material wajib diisi',
            'material_name.string' => 'Nama material harus berupa teks',
            'material_name.max' => 'Nama material maksimal 255 karakter',
            'material_qty.required' => 'Jumlah material wajib diisi',
            'material_qty.integer' => 'Jumlah material harus berupa angka',
            'material_qty.min' => 'Jumlah material tidak boleh kurang dari 0',
            'unit_price.required' => 'Harga per unit wajib diisi',
            'unit_price.integer' => 'Harga per unit harus berupa angka',
            'unit_price.min' => 'Harga per unit tidak boleh kurang dari 0',
            'added_on.required' => 'Tanggal penambahan wajib diisi',
            'added_on.date' => 'Format tanggal tidak valid',
        ]);

        try {
            $stock = RawStock::findOrFail($id);

            $total_price = $validated['material_qty'] * $validated['unit_price'];

            $stock->update([
                'material_name' => $validated['material_name'],
                'material_qty' => $validated['material_qty'],
                'unit_price' => $validated['unit_price'],
                'total_price' => $total_price,
                'added_on' => $validated['added_on'],
            ]);

            return redirect()->route('raw_stock.index')->with('success', 'Data berhasil diupdate');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('raw_stock.index')->with('error', 'Data tidak ditemukan');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage())->withInput();
        }
    }

    // Hapus data
    public function destroy($id)
    {
        try {
            $stock = RawStock::findOrFail($id);
            $stock->delete();

            return redirect()->route('raw_stock.index')->with('success', 'Data berhasil dihapus');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('raw_stock.index')->with('error', 'Data tidak ditemukan');
        } catch (Exception $e) {
            return redirect()->route('raw_stock.index')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}