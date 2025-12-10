<?php

namespace App\Http\Controllers;

use App\Models\QcCheck;
use App\Models\DetailProduct;
use Illuminate\Http\Request;
use App\Models\AvailStock; // added import

class QcCheckController extends Controller
{
    /**
     * Display a listing of QC Checks.
     */
    public function index()
    {
        $qcChecks = QcCheck::with(['detailProduct'])->paginate(10);
        $detailProducts = DetailProduct::with('production')->get();
        return view('qc_check.index', compact('qcChecks', 'detailProducts'));
    }

    /**
     * Show the form for creating a new QC Check.
     */
    public function create()
    {
        $detailProducts = DetailProduct::all();
        return view('qc_check.create', compact('detailProducts'));
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
            'qc_checker' => 'nullable|string',
            'reject_reason' => 'nullable|string',
        ]);

        // Get detail product and its production info
        $detailProduct = DetailProduct::find($validated['product_id']);
        $production = $detailProduct->production;

        // Validate total qty must equal production total_unit
        $total_qty = $validated['qty_passed'] + $validated['qty_reject'];
        if ($total_qty != $production->total_unit) {
            return back()
                ->withErrors([
                    'qty_passed' => "Total barang lolos + reject ({$total_qty}) harus sama dengan total unit produksi ({$production->total_unit})"
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
        $detailProducts = DetailProduct::all();
        return view('qc_check.edit', compact('qcCheck', 'detailProducts'));
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
            'qc_checker' => 'nullable|string',
            'reject_reason' => 'nullable|string',
        ]);

        // New detail product and production info
        $detailProductNew = DetailProduct::find($validated['product_id']);
        $production = $detailProductNew->production;

        // Validate total qty must equal production total_unit
        $total_qty = $validated['qty_passed'] + $validated['qty_reject'];
        if ($total_qty != $production->total_unit) {
            return back()
                ->withErrors([
                    'qty_passed' => "Total barang lolos + reject ({$total_qty}) harus sama dengan total unit produksi ({$production->total_unit})"
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
}
