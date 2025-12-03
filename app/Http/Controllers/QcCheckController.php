<?php

namespace App\Http\Controllers;

use App\Models\QcCheck;
use App\Models\DetailProduct;
use Illuminate\Http\Request;

class QcCheckController extends Controller
{
    /**
     * Display a listing of QC Checks.
     */
    public function index()
    {
        $qcChecks = QcCheck::with(['detailProduct'])->paginate(10);
        return view('qc_check.index', compact('qcChecks'));
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
        ]);

        // Calculate pass rate and determine status
        $total = $validated['qty_passed'] + $validated['qty_reject'];
        if ($total > 0) {
            $passRate = ($validated['qty_passed'] / $total) * 100;
            $validated['qc_label'] = $passRate >= 95 ? 'PASS' : 'FAIL';
        } else {
            $validated['qc_label'] = 'PENDING';
        }

        QcCheck::create($validated);

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
        ]);

        // Calculate pass rate and determine status
        $total = $validated['qty_passed'] + $validated['qty_reject'];
        if ($total > 0) {
            $passRate = ($validated['qty_passed'] / $total) * 100;
            $validated['qc_label'] = $passRate >= 95 ? 'PASS' : 'FAIL';
        } else {
            $validated['qc_label'] = 'PENDING';
        }

        $qcCheck->update($validated);

        return redirect()->route('qc_check.index')->with('success', 'Quality Control berhasil diupdate');
    }

    /**
     * Remove the specified QC Check from storage.
     */
    public function destroy(QcCheck $qcCheck)
    {
        $qcCheck->delete();
        return redirect()->route('qc_check.index')->with('success', 'Quality Control berhasil dihapus');
    }
}
