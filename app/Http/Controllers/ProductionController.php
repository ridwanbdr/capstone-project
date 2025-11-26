<?php

namespace App\Http\Controllers;

use App\Models\Production;
use App\Models\RawStock;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productions = Production::with('rawStocks');

        // Cari berdasarkan production label atau material name jika ada parameter pencarian
        if ($request->has('search') && $request->search) {
            $productions = $productions->where(function($query) use ($request) {
                $query->where('production_label', 'like', '%' . $request->search . '%')
                      ->orWhere('production_lead', 'like', '%' . $request->search . '%')
                      ->orWhereHas('rawStocks', function($q) use ($request) {
                          $q->where('material_name', 'like', '%' . $request->search . '%');
                      });
            });
        }

        // Urutkan berdasarkan tanggal production secara descending
        $productions = $productions->orderBy('production_date', 'desc');

        // Gunakan pagination untuk menghindari loading data yang berlebihan
        $productions = $productions->paginate(10)->withQueryString();

        // Ambil semua raw stock untuk dropdown di form
        $rawStocks = RawStock::orderBy('material_name', 'asc')->get();

        return view('productions.index', compact('productions', 'rawStocks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil semua raw stock untuk dropdown
        $rawStocks = RawStock::orderBy('material_name', 'asc')->get();
        return view('production.create', compact('rawStocks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input dengan pesan error kustom
        $validated = $request->validate([
            'production_lead' => 'required|string|max:255',
            'production_label' => 'required|string|max:255',
            'production_date' => 'required|date',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|integer|exists:raw_stocks,material_id',
            'materials.*.material_qty' => 'required|integer|min:1',
            'total_unit' => 'required|integer|min:1',
        ], [
            'production_lead.required' => 'Production lead wajib diisi',
            'production_lead.string' => 'Production lead harus berupa teks',
            'production_lead.max' => 'Production lead maksimal 255 karakter',
            'production_label.required' => 'Production label wajib diisi',
            'production_label.string' => 'Production label harus berupa teks',
            'production_label.max' => 'Production label maksimal 255 karakter',
            'production_date.required' => 'Tanggal production wajib diisi',
            'production_date.date' => 'Format tanggal tidak valid',
            'materials.required' => 'Minimal 1 material wajib dipilih',
            'materials.array' => 'Format material tidak valid',
            'materials.min' => 'Minimal 1 material wajib dipilih',
            'materials.*.material_id.required' => 'Material wajib dipilih',
            'materials.*.material_id.exists' => 'Material yang dipilih tidak ditemukan',
            'materials.*.material_qty.required' => 'Jumlah material wajib diisi',
            'materials.*.material_qty.integer' => 'Jumlah material harus berupa angka',
            'materials.*.material_qty.min' => 'Jumlah material minimal 1',
            'total_unit.required' => 'Total unit wajib diisi',
            'total_unit.integer' => 'Total unit harus berupa angka',
            'total_unit.min' => 'Total unit minimal 1',
        ]);

        try {
            // Mulai database transaction
            DB::beginTransaction();
            
            // Validasi semua stok cukup sebelum membuat production
            $materialsData = [];
            foreach ($validated['materials'] as $material) {
                $rawStock = RawStock::findOrFail($material['material_id']);
                
                // Validasi stok cukup
                if ($rawStock->material_qty < $material['material_qty']) {
                    throw new Exception("Stok {$rawStock->material_name} tidak cukup. Stok tersedia: {$rawStock->material_qty}, dibutuhkan: {$material['material_qty']}");
                }
                
                $materialsData[$material['material_id']] = ['material_qty' => $material['material_qty']];
            }
            
            // Buat production
            $production = Production::create([
                'production_lead' => $validated['production_lead'],
                'production_label' => $validated['production_label'],
                'production_date' => $validated['production_date'],
                'total_unit' => $validated['total_unit'],
            ]);
            
            // Attach materials dengan pivot data
            $production->rawStocks()->attach($materialsData);
            
            // Kurangi qty raw stock untuk semua materials
            foreach ($validated['materials'] as $material) {
                $rawStock = RawStock::findOrFail($material['material_id']);
                $rawStock->reduceQty($material['material_qty']);
            }
            
            // Commit transaction
            DB::commit();

            return redirect()->route('production.index')->with('success', 'Data production berhasil ditambahkan dan stok material telah dikurangi');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Material tidak ditemukan')->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $production = Production::with('rawStocks')->findOrFail($id);
            return view('production.show', compact('production'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('production.index')->with('error', 'Data tidak ditemukan');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $production = Production::with('rawStocks')->findOrFail($id);
            // Ambil semua raw stock untuk dropdown
            $rawStocks = RawStock::orderBy('material_name', 'asc')->get();
            return view('production.edit', compact('production', 'rawStocks'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('production.index')->with('error', 'Data tidak ditemukan');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi input dengan pesan error kustom
        $validated = $request->validate([
            'production_lead' => 'required|string|max:255',
            'production_label' => 'required|string|max:255',
            'production_date' => 'required|date',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|integer|exists:raw_stocks,material_id',
            'materials.*.material_qty' => 'required|integer|min:1',
            'total_unit' => 'required|integer|min:1',
        ], [
            'production_lead.required' => 'Production lead wajib diisi',
            'production_lead.string' => 'Production lead harus berupa teks',
            'production_lead.max' => 'Production lead maksimal 255 karakter',
            'production_label.required' => 'Production label wajib diisi',
            'production_label.string' => 'Production label harus berupa teks',
            'production_label.max' => 'Production label maksimal 255 karakter',
            'production_date.required' => 'Tanggal production wajib diisi',
            'production_date.date' => 'Format tanggal tidak valid',
            'materials.required' => 'Minimal 1 material wajib dipilih',
            'materials.array' => 'Format material tidak valid',
            'materials.min' => 'Minimal 1 material wajib dipilih',
            'materials.*.material_id.required' => 'Material wajib dipilih',
            'materials.*.material_id.exists' => 'Material yang dipilih tidak ditemukan',
            'materials.*.material_qty.required' => 'Jumlah material wajib diisi',
            'materials.*.material_qty.integer' => 'Jumlah material harus berupa angka',
            'materials.*.material_qty.min' => 'Jumlah material minimal 1',
            'total_unit.required' => 'Total unit wajib diisi',
            'total_unit.integer' => 'Total unit harus berupa angka',
            'total_unit.min' => 'Total unit minimal 1',
        ]);

        try {
            // Mulai database transaction
            DB::beginTransaction();
            
            $production = Production::with('rawStocks')->findOrFail($id);
            
            // Simpan data materials lama untuk dikembalikan stoknya
            $oldMaterials = $production->rawStocks->mapWithKeys(function($rawStock) {
                return [$rawStock->material_id => $rawStock->pivot->material_qty];
            })->toArray();
            
            // Validasi semua stok cukup untuk materials baru
            $materialsData = [];
            foreach ($validated['materials'] as $material) {
                $rawStock = RawStock::findOrFail($material['material_id']);
                
                // Hitung qty yang dibutuhkan (selisih dengan yang lama jika ada)
                $oldQty = $oldMaterials[$material['material_id']] ?? 0;
                $qtyNeeded = $material['material_qty'] - $oldQty;
                
                // Jika qty bertambah, validasi stok cukup
                if ($qtyNeeded > 0) {
                    if ($rawStock->material_qty < $qtyNeeded) {
                        throw new Exception("Stok {$rawStock->material_name} tidak cukup. Stok tersedia: {$rawStock->material_qty}, dibutuhkan: {$qtyNeeded}");
                    }
                }
                
                $materialsData[$material['material_id']] = ['material_qty' => $material['material_qty']];
            }
            
            // Kembalikan stok untuk materials yang dihapus atau dikurangi
            foreach ($oldMaterials as $oldMaterialId => $oldQty) {
                if (!isset($materialsData[$oldMaterialId])) {
                    // Material dihapus, kembalikan semua stok
                    $rawStock = RawStock::findOrFail($oldMaterialId);
                    $rawStock->addQty($oldQty);
                } else {
                    // Material masih ada, hitung selisih
                    $newQty = $materialsData[$oldMaterialId]['material_qty'];
                    $qtyDifference = $newQty - $oldQty;
                    
                    if ($qtyDifference < 0) {
                        // Qty berkurang, kembalikan selisih
                        $rawStock = RawStock::findOrFail($oldMaterialId);
                        $rawStock->addQty(abs($qtyDifference));
                    }
                }
            }
            
            // Kurangi stok untuk materials baru atau yang qty-nya bertambah
            foreach ($validated['materials'] as $material) {
                $rawStock = RawStock::findOrFail($material['material_id']);
                $oldQty = $oldMaterials[$material['material_id']] ?? 0;
                $qtyDifference = $material['material_qty'] - $oldQty;
                
                if ($qtyDifference > 0) {
                    // Qty bertambah, kurangi stok
                    $rawStock->reduceQty($qtyDifference);
                }
            }
            
            // Update production
            $production->update([
                'production_lead' => $validated['production_lead'],
                'production_label' => $validated['production_label'],
                'production_date' => $validated['production_date'],
                'total_unit' => $validated['total_unit'],
            ]);
            
            // Sync materials (update pivot table)
            $production->rawStocks()->sync($materialsData);
            
            // Commit transaction
            DB::commit();

            return redirect()->route('production.index')->with('success', 'Data production berhasil diupdate dan stok material telah disesuaikan');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('production.index')->with('error', 'Data tidak ditemukan');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $production = Production::findOrFail($id);
            $production->delete();

            return redirect()->route('production.index')->with('success', 'Data production berhasil dihapus');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('production.index')->with('error', 'Data tidak ditemukan');
        } catch (Exception $e) {
            return redirect()->route('production.index')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
