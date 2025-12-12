<?php

namespace App\Http\Controllers;

use App\Models\QcCheck;
use App\Models\DetailProduct;
use App\Models\Production;
use Illuminate\Http\Request;
use App\Models\AvailStock; // added import

class QcCheckController extends Controller
{
    /**
     * Display a listing of QC Checks.
     */
    public function index(Request $request)
    {
        $productions = Production::orderBy('production_id', 'desc')->get();
        $selectedProductionId = $request->query('production_id');
        $completionFilter = $request->query('completion'); // completed | pending | all

        // Calculate QC completion status for each production
        $productionStatuses = [];
        foreach ($productions as $production) {
            $totalProducts = DetailProduct::where('production_id', $production->production_id)->count();

            $qcCheckedProductIds = QcCheck::whereHas('detailProduct', function($q) use ($production) {
                $q->where('production_id', $production->production_id);
            })->pluck('product_id')->unique()->count();

            $productionStatuses[$production->production_id] = [
                'total' => $totalProducts,
                'checked' => $qcCheckedProductIds,
                'completed' => $totalProducts > 0 && $qcCheckedProductIds >= $totalProducts,
                'percentage' => $totalProducts > 0 ? round(($qcCheckedProductIds / $totalProducts) * 100, 1) : 0
            ];
        }

        // Apply completion filter to displayed productions
        $filteredProductions = $productions->filter(function ($prod) use ($productionStatuses, $completionFilter) {
            $status = $productionStatuses[$prod->production_id] ?? ['completed' => false];
            if ($completionFilter === 'completed') {
                return $status['completed'] === true;
            }
            if ($completionFilter === 'pending') {
                return $status['completed'] === false;
            }
            return true; // all
        });

        // Only load QC checks & products when a production is selected
        if ($selectedProductionId) {
            $query = QcCheck::with(['detailProduct.production']);
            $query->whereHas('detailProduct', function($q) use ($selectedProductionId) {
                $q->where('production_id', $selectedProductionId);
            });
            $qcChecks = $query->orderBy('qc_id', 'desc')->paginate(10)->withQueryString();
            $detailProducts = DetailProduct::with('production')
                ->where('production_id', $selectedProductionId)
                ->get();
        } else {
            $qcChecks = collect();
            $detailProducts = collect();
        }

        return view('qc_check.index', [
            'qcChecks' => $qcChecks,
            'detailProducts' => $detailProducts,
            'productions' => $filteredProductions,
            'selectedProductionId' => $selectedProductionId,
            'productionStatuses' => $productionStatuses,
            'completionFilter' => $completionFilter,
        ]);
    }

    /**
     * Show the form for creating a new QC Check.
     */
    public function create()
    {
        $productions = Production::orderBy('production_id', 'desc')->get();
        $detailProducts = DetailProduct::with('production')->get();
        return view('qc_check.create', compact('detailProducts', 'productions'));
    }

    /**
     * Store a newly created QC Check in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:detail_product,product_id',
            'qty_passed' => 'required|integer|min:0',
            'qty_reject' => 'required|integer|min:0',
            'date' => 'required|date',
            'qc_checker' => 'required|string|max:255',
            'reject_reason' => 'nullable|string',
        ]);

        // Get detail product
        $detailProduct = DetailProduct::find($validated['product_id']);

        // Validate total qty must equal product qty_unit
        $total_qty = $validated['qty_passed'] + $validated['qty_reject'];
        $productQtyUnit = (int) ($detailProduct->qty_unit ?? 0);
        if ($total_qty != $productQtyUnit) {
            return back()
                ->withErrors([
                    'qty_passed' => "Total barang lolos + reject ({$total_qty}) harus sama dengan total unit produk ({$productQtyUnit})"
                ])
                ->withInput();
        }

        // Calculate pass rate and determine status
        if ($total_qty > 0) {
            $passRate = ($validated['qty_passed'] / $total_qty) * 100;
            $validated['qc_label'] = $passRate >= 95 ? 'PASS' : 'FAIL';
        } else {
            $validated['qc_label'] = 'PENDING';
        }

        QcCheck::create($validated);

        // Jika sudah ada avail_stocks dengan product_name yang sama -> tambah qty_unit dengan qty_passed
        // Jika belum ada -> buat record baru
        $productName = $detailProduct->product_name ?? '';
        $qtyToAdd = (int) ($validated['qty_passed'] ?? 0);

        $existing = AvailStock::where('product_name', $productName)->orderBy('id', 'desc')->first();

        if ($existing) {
            $existing->qty_unit = max(0, (int)$existing->qty_unit + $qtyToAdd);
            $existing->save();
        } else {
            AvailStock::create([
                'product_name' => $productName,
                'size_id'      => $detailProduct->size_id ?? null,
                'qty_unit'     => $qtyToAdd,
                'price_unit'   => (float) ($detailProduct->price_unit ?? 0),
            ]);
        }

        return redirect()->route('qc_check.index')->with('success', 'Quality Control berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified QC Check.
     */
    public function edit(QcCheck $qcCheck)
    {
        $productions = Production::orderBy('production_id', 'desc')->get();
        $detailProducts = DetailProduct::with('production')->get();
        return view('qc_check.edit', compact('qcCheck', 'detailProducts', 'productions'));
    }

    /**
     * Update the specified QC Check in storage.
     */
    public function update(Request $request, QcCheck $qcCheck)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:detail_product,product_id',
            'qty_passed' => 'required|integer|min:0',
            'qty_reject' => 'required|integer|min:0',
            'date' => 'required|date',
            'qc_checker' => 'required|string|max:255',
            'reject_reason' => 'nullable|string',
        ]);

        // New detail product
        $detailProductNew = DetailProduct::find($validated['product_id']);

        // Validate total qty must equal product qty_unit
        $total_qty = $validated['qty_passed'] + $validated['qty_reject'];
        $productQtyUnit = (int) ($detailProductNew->qty_unit ?? 0);
        if ($total_qty != $productQtyUnit) {
            return back()
                ->withErrors([
                    'qty_passed' => "Total barang lolos + reject ({$total_qty}) harus sama dengan total unit produk ({$productQtyUnit})"
                ])
                ->withInput();
        }

        // Calculate pass rate and determine status
        if ($total_qty > 0) {
            $passRate = ($validated['qty_passed'] / $total_qty) * 100;
            $validated['qc_label'] = $passRate >= 95 ? 'PASS' : 'FAIL';
        } else {
            $validated['qc_label'] = 'PENDING';
        }

        // --- Adjust avail_stocks based on difference between new and old qty_passed ---
        $oldQty = (int) ($qcCheck->qty_passed ?? 0);
        $oldDetail = $qcCheck->detailProduct ?? DetailProduct::find($qcCheck->product_id);
        $oldProductName = $oldDetail->product_name ?? null;

        $newQty = (int) ($validated['qty_passed'] ?? 0);
        $newProductName = $detailProductNew->product_name ?? null;

        // Case: same product name -> apply difference (can be + or -)
        if ($oldProductName !== null && $oldProductName === $newProductName) {
            $diff = $newQty - $oldQty;
            if ($diff !== 0) {
                $avail = AvailStock::where('product_name', $oldProductName)->orderBy('id', 'desc')->first();
                if ($avail) {
                    $avail->qty_unit = max(0, (int)$avail->qty_unit + $diff);
                    $avail->save();
                } else {
                    // If no avail row exists and diff positive -> create
                    if ($diff > 0) {
                        AvailStock::create([
                            'product_name' => $newProductName,
                            'size_id'      => $detailProductNew->size_id ?? null,
                            'qty_unit'     => $diff,
                            'price_unit'   => (float) ($detailProductNew->price_unit ?? 0),
                        ]);
                    }
                }
            }
        } else {
            // Different product: subtract oldQty from old product avail, add newQty to new product avail
            if ($oldProductName !== null && $oldQty > 0) {
                $oldAvail = AvailStock::where('product_name', $oldProductName)->orderBy('id', 'desc')->first();
                if ($oldAvail) {
                    $oldAvail->qty_unit = max(0, (int)$oldAvail->qty_unit - $oldQty);
                    $oldAvail->save();
                }
            }

            if ($newProductName !== null && $newQty > 0) {
                $newAvail = AvailStock::where('product_name', $newProductName)->orderBy('id', 'desc')->first();
                if ($newAvail) {
                    $newAvail->qty_unit = max(0, (int)$newAvail->qty_unit + $newQty);
                    $newAvail->save();
                } else {
                    AvailStock::create([
                        'product_name' => $newProductName,
                        'size_id'      => $detailProductNew->size_id ?? null,
                        'qty_unit'     => $newQty,
                        'price_unit'   => (float) ($detailProductNew->price_unit ?? 0),
                    ]);
                }
            }
        }
        // --- end adjustments ---

        $qcCheck->update($validated);

        return redirect()->route('qc_check.index')->with('success', 'Quality Control berhasil diupdate');
    }

    /**
     * Remove the specified QC Check from storage.
     */
    public function destroy(QcCheck $qcCheck)
    {
        // kurangi qty_unit pada avail_stocks yang memiliki product_name sama dengan detail product QC
        $qtyToReduce = (int) ($qcCheck->qty_passed ?? 0);

        // Ambil nama product dari relasi detailProduct jika tersedia, fallback ke product_id lookup
        $detailProduct = $qcCheck->detailProduct ?? DetailProduct::find($qcCheck->product_id);
        $productName = $detailProduct->product_name ?? null;

        if ($productName !== null && $qtyToReduce > 0) {
            // Cari satu record avail_stock yang cocok (ambil yang terbaru)
            $avail = AvailStock::where('product_name', $productName)->orderBy('id', 'desc')->first();

            if ($avail) {
                $avail->qty_unit = max(0, (int)$avail->qty_unit - $qtyToReduce);
                $avail->save();
            }
        }

        $qcCheck->delete();

        return redirect()->route('qc_check.index')->with('success', 'Quality Control berhasil dihapus dan avail_stock diperbarui');
    }

    /**
     * Bulk delete QC for a specific production (utility for card action).
     */
    public function destroyByProduction($productionId)
    {
        $qcChecks = QcCheck::whereHas('detailProduct', function ($q) use ($productionId) {
            $q->where('production_id', $productionId);
        })->get();

        foreach ($qcChecks as $qcCheck) {
            // reuse logic similar to destroy
            $qtyToReduce = (int) ($qcCheck->qty_passed ?? 0);
            $detailProduct = $qcCheck->detailProduct ?? DetailProduct::find($qcCheck->product_id);
            $productName = $detailProduct->product_name ?? null;

            if ($productName !== null && $qtyToReduce > 0) {
                $avail = AvailStock::where('product_name', $productName)->orderBy('id', 'desc')->first();
                if ($avail) {
                    $avail->qty_unit = max(0, (int) $avail->qty_unit - $qtyToReduce);
                    $avail->save();
                }
            }

            $qcCheck->delete();
        }

        return redirect()->route('qc_check.index', ['production_id' => $productionId])
            ->with('success', 'Semua QC untuk production ini telah dihapus.');
    }
}
