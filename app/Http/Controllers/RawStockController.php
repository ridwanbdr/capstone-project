<?php

namespace App\Http\Controllers;

use App\Models\RawStock;
use App\Models\RawStockTransaction;
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

        // juga kirim daftar material yang ada untuk pilihan input cepat
        $materials = RawStock::select('material_id', 'material_name', 'satuan', 'category', 'unit_price')
                             ->orderBy('material_name')
                             ->get();

        return view('raw_stock.index', compact('stocks', 'materials'));
    }

    // Tampilkan form tambah data
    public function create()
    {
        $materials = RawStock::select('material_id', 'material_name', 'satuan', 'category', 'unit_price')
                             ->orderBy('material_name')
                             ->get();
        return view('raw_stock.create', compact('materials'));
    }

    // Simpan data baru ke database
    public function store(Request $request)
    {
        // Validasi input: support memilih material existing via material_id
        $validated = $request->validate([
            'material_id' => 'nullable|exists:raw_stocks,material_id',
            'material_name' => 'required_without:material_id|string|max:255',
            'material_qty' => 'required|integer|min:0', 
            'satuan' => 'required_without:material_id|string|max:255',
            'category' => 'required_without:material_id|string|max:255',
            'unit_price' => 'required|integer|min:0',
            'added_on' => 'required|date',
        ], [
            'material_id.exists' => 'Material yang dipilih tidak ditemukan',
            'material_name.required_without' => 'Nama material wajib diisi jika tidak memilih material yang sudah ada',
            'material_name.string' => 'Nama material harus berupa teks',
            'material_name.max' => 'Nama material maksimal 255 karakter',
            'material_qty.required' => 'Jumlah material wajib diisi',
            'material_qty.integer' => 'Jumlah material harus berupa angka',
            'material_qty.min' => 'Jumlah material tidak boleh kurang dari 0',
            'satuan.required_without' => 'Satuan material wajib diisi jika tidak memilih material yang sudah ada',
            'satuan.string' => 'Satuan material harus berupa teks',
            'satuan.max' => 'Satuan material maksimal 255 karakter',
            'category.required_without' => 'Kategori material wajib diisi jika tidak memilih material yang sudah ada',
            'category.string' => 'Kategori material harus berupa teks',
            'category.max' => 'Kategori material maksimal 255 karakter',
            'unit_price.required' => 'Harga per unit wajib diisi',
            'unit_price.integer' => 'Harga per unit harus berupa angka',
            'unit_price.min' => 'Harga per unit tidak boleh kurang dari 0',
            'added_on.required' => 'Tanggal penambahan wajib diisi',
            'added_on.date' => 'Format tanggal tidak valid',
        ]);

        try {
            $total_price = $validated['material_qty'] * $validated['unit_price'];

            // Tentukan apakah material sudah ada (prioritaskan material_id jika diberikan)
            $existing = null;
            if (!empty($validated['material_id'])) {
                $existing = RawStock::find($validated['material_id']);
            } else {
                if (!empty($validated['material_name']) && !empty($validated['satuan'])) {
                    $existing = RawStock::where('material_name', $validated['material_name'])
                                        ->where('satuan', $validated['satuan'])
                                        ->first();
                }
            }

            // Tentukan nilai-nilai untuk transaksi: jika existing dipilih, gunakan field dari existing
            if ($existing) {
                $transMaterialName = $existing->material_name;
                $transSatuan = $existing->satuan;
                $transCategory = $existing->category;
            } else {
                $transMaterialName = $validated['material_name'];
                $transSatuan = $validated['satuan'] ?? null;
                $transCategory = $validated['category'] ?? null;
            }

            // Simpan transaksi pembelian bahan baku (kaitkan material_id nanti jika perlu)
            $transaction = RawStockTransaction::create([
                'material_id' => $existing? $existing->material_id : null,
                'material_name' => $transMaterialName,
                'qty' => $validated['material_qty'],
                'satuan' => $transSatuan,
                'unit_price' => $validated['unit_price'],
                'total_price' => $total_price,
                'added_on' => $validated['added_on'],
            ]);

            if ($existing) {
                $existing->addQty($validated['material_qty']);
                // update harga satuan dan tanggal terakhir masuk sesuai transaksi terbaru
                $existing->unit_price = $validated['unit_price'];
                $existing->added_on = $validated['added_on'];
                $existing->save();
            } else {
                // Jika belum ada, buat record master baru
                $new = RawStock::create([
                    'material_name' => $validated['material_name'],
                    'material_qty' => $validated['material_qty'],
                    'satuan' => $validated['satuan'],
                    'category' => $validated['category'],
                    'unit_price' => $validated['unit_price'],
                    'total_price' => $total_price,
                    'added_on' => $validated['added_on'],
                ]);

                // Kaitkan transaksi ke material baru
                $transaction->material_id = $new->material_id;
                $transaction->save();
            }

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
            'satuan' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'unit_price' => 'required|integer|min:0',
            'added_on' => 'required|date',
        ], [
            'material_name.required' => 'Nama material wajib diisi',
            'material_name.string' => 'Nama material harus berupa teks',
            'material_name.max' => 'Nama material maksimal 255 karakter',
            'material_qty.required' => 'Jumlah material wajib diisi',
            'material_qty.integer' => 'Jumlah material harus berupa angka',
            'material_qty.min' => 'Jumlah material tidak boleh kurang dari 0',
            'satuan.required' => 'Satuan material wajib diisi',
            'satuan.string' => 'Satuan material harus berupa teks',
            'satuan.max' => 'Satuan material maksimal 255 karakter',
            'category.required' => 'Kategori material wajib diisi',
            'category.string' => 'Kategori material harus berupa teks',
            'category.max' => 'Kategori material maksimal 255 karakter',
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
                'satuan' => $validated['satuan'],
                'category' => $validated['category'],
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
            
            // Check if material is used in any production
            $usedInProductions = $stock->productions()->count();
            if ($usedInProductions > 0) {
                // Detach from all productions first to avoid foreign key constraint errors
                $stock->productions()->detach();
            }
            
            // Delete the raw stock
            $stock->delete();

            return redirect()->route('raw_stock.index')->with('success', 'Data berhasil dihapus' . ($usedInProductions > 0 ? ' (terputus dari ' . $usedInProductions . ' production)' : ''));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('raw_stock.index')->with('error', 'Data tidak ditemukan');
        } catch (Exception $e) {
            // Check if it's a foreign key constraint error
            if (strpos($e->getMessage(), 'foreign key constraint') !== false || strpos($e->getMessage(), '1451') !== false) {
                return redirect()->route('raw_stock.index')->with('error', 'Tidak dapat menghapus material karena masih digunakan dalam production. Silakan hapus atau ubah production terlebih dahulu.');
            }
            return redirect()->route('raw_stock.index')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}