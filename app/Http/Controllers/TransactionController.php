<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\AvailStock;
use App\Models\Size;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class TransactionController extends Controller
{
    public function index(Request $request, $avail_stock_id = null)
    {
        if (is_null($avail_stock_id)) {
            $avail_stock_id = $request->query('id', null);
        }

        $query = Transaction::with('availStock')->orderBy('transaction_id', 'desc');

        if (!is_null($avail_stock_id) && $avail_stock_id !== '') {
            $query->where('id', $avail_stock_id);
        }

        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%'.$request->search.'%');
        }

        $transactions = $query->paginate(10)->withQueryString();
        $availStocks = AvailStock::orderBy('id')->get();
        $sizes = Size::orderBy('size_id')->get();

        $availStockLabel = null;
        if (!empty($avail_stock_id)) {
            $a = AvailStock::find($avail_stock_id);
            $availStockLabel = $a ? ($a->product_name ?? "#{$a->id}") : null;
        } elseif ($request->filled('id')) {
            $a = AvailStock::find($request->id);
            $availStockLabel = $a ? ($a->product_name ?? "#{$a->id}") : null;
        }

        return view('transactions.index', compact('transactions', 'availStocks', 'availStockLabel', 'sizes'));
    }

    public function create()
    {
        $availStocks = AvailStock::orderBy('id')->get();
        $sizes = Size::orderBy('size_id')->get();
        return view('transactions.form', compact('availStocks', 'sizes'));
    }

    public function store(Request $request)
    {
        // jika avail stock tersedia, pastikan product_name & price di-merge ke request
        if ($request->filled('id')) {
            $avail = AvailStock::find($request->input('id'));
            if ($avail) {
                $request->merge([
                    'product_name' => $request->input('product_name') ?? $avail->product_name,
                    // jika JS gagal mengisi price, ambil dari avail stock price_unit
                    'price' => $request->input('price') ?? $avail->price_unit,
                ]);
            }
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'id' => 'required|integer|exists:avail_stocks,id',
            'product_name' => 'required|string|max:191', // { changed code }
            'size' => 'nullable|string|max:100',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'due_date_payment' => 'nullable|date',
            'status' => 'nullable|string|max:50',
        ], [
            'date.required' => 'Tanggal wajib diisi',
            'id.required' => 'Referensi stok wajib dipilih',
            'id.exists' => 'Referensi stok tidak ditemukan',
            'qty.required' => 'Quantity wajib diisi',
            'qty.integer' => 'Quantity harus berupa angka',
            'price.required' => 'Harga satuan wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'product_name.required' => 'Nama produk wajib dipilih', // { changed code }
        ]);

        try {
            $validated['total'] = (int)$validated['qty'] * (float)$validated['price'];
            $validated['paid'] = isset($validated['paid']) ? (float)$validated['paid'] : 0;
            $validated['unpaid_amount'] = max(0, $validated['total'] - $validated['paid']);

            Transaction::create($validated);

            // redirect tanpa passing id sesuai permintaan
            return redirect()->route('transactions.index')->with('success', 'Transaksi tersimpan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function show($transaction_id)
    {
        return redirect()->route('transactions.edit', $transaction_id);
    }

    public function edit($transaction_id)
    {
        $transaction = Transaction::findOrFail($transaction_id);
        $availStocks = AvailStock::orderBy('id')->get();
        $sizes = Size::orderBy('size_id')->get();
        return view('transactions.form', compact('transaction', 'availStocks', 'sizes'));
    }

    public function update(Request $request, $transaction_id)
    {
        // merge product_name & price from avail stock if missing
        if ($request->filled('id')) {
            $avail = AvailStock::find($request->input('id'));
            if ($avail) {
                $request->merge([
                    'product_name' => $request->input('product_name') ?? $avail->product_name,
                    'price' => $request->input('price') ?? $avail->price_unit,
                ]);
            }
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'id' => 'required|integer|exists:avail_stocks,id',
            'product_name' => 'required|string|max:191', // { changed code }
            'size' => 'nullable|string|max:100',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'due_date_payment' => 'nullable|date',
            'status' => 'nullable|string|max:50',
        ], [
            'date.required' => 'Tanggal wajib diisi',
            'id.required' => 'Referensi stok wajib dipilih',
            'id.exists' => 'Referensi stok tidak ditemukan',
            'qty.required' => 'Quantity wajib diisi',
            'qty.integer' => 'Quantity harus berupa angka',
            'price.required' => 'Harga satuan wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'product_name.required' => 'Nama produk wajib dipilih', // { changed code }
        ]);

        try {
            $validated['total'] = (int)$validated['qty'] * (float)$validated['price'];
            $validated['paid'] = isset($validated['paid']) ? (float)$validated['paid'] : 0;
            $validated['unpaid_amount'] = max(0, $validated['total'] - $validated['paid']);

            $tx = Transaction::findOrFail($transaction_id);
            $tx->update($validated);

            // redirect tanpa passing id sesuai permintaan
            return redirect()->route('transactions.index')->with('success', 'Transaksi diperbarui.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('transactions.index')->with('error', 'Data tidak ditemukan');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($transaction_id)
    {
        try {
            $tx = Transaction::findOrFail($transaction_id);
            $refId = $tx->id;
            $tx->delete();

            return redirect()->route('transactions.index', ['id' => $refId])->with('success', 'Transaksi dihapus.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('transactions.index')->with('error', 'Data tidak ditemukan');
        } catch (Exception $e) {
            return redirect()->route('transactions.index')->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }
}
