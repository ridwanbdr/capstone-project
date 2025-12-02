<?php

namespace App\Http\Controllers;

use App\Models\QcCheck;
use App\Models\Production;
use App\Models\User;
use Illuminate\Http\Request;

class QcCheckController extends Controller
{
    /**
     * Display a listing of QC Checks.
     */
    public function index()
    {
        $qcChecks = QcCheck::with(['checker', 'production'])->paginate(10);
        return view('qc_check.index', compact('qcChecks'));
    }

    /**
     * Show the form for creating a new QC Check.
     */
    public function create()
    {
        $productions = Production::all();
        $users = User::where('role', 'qc_checker')->orWhere('role', 'admin')->get();
        return view('qc_check.create', compact('productions', 'users'));
    }

    /**
     * Store a newly created QC Check in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'qc_id' => 'required|string|unique:qc_checks',
            'product_name' => 'required|string|exists:productions,product_name',
            'qty_passed' => 'required|integer|min:0',
            'qty_reject' => 'required|integer|min:0',
            'date' => 'required|date',
            'qc_checker' => 'nullable|exists:users,user_id',
            'qc_label' => 'required|in:PASS,FAIL,PENDING',
        ]);

        QcCheck::create($validated);

        return redirect()->route('qc_check.index')->with('success', 'Quality Control berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified QC Check.
     */
    public function edit(QcCheck $qcCheck)
    {
        $productions = Production::all();
        $users = User::where('role', 'qc_checker')->orWhere('role', 'admin')->get();
        return view('qc_check.edit', compact('qcCheck', 'productions', 'users'));
    }

    /**
     * Update the specified QC Check in storage.
     */
    public function update(Request $request, QcCheck $qcCheck)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|exists:productions,product_name',
            'qty_passed' => 'required|integer|min:0',
            'qty_reject' => 'required|integer|min:0',
            'date' => 'required|date',
            'qc_checker' => 'nullable|exists:users,user_id',
            'qc_label' => 'required|in:PASS,FAIL,PENDING',
        ]);

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
