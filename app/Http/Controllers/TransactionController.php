<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\AvailStock;
use App\Models\Size;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();

        $validated = $request->validate([
            'date' => 'required|date',
            'is_paid' => 'required|in:lunas,belum_lunas',
            'paid' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
            'due_date_payment' => 'nullable|date',
            'status' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|exists:avail_stocks,id',
            'items.*.qty' => 'required|integer|min:1',
        ], [
            'items.required' => 'Keranjang tidak boleh kosong.',
            'items.*.id.required' => 'Produk wajib dipilih.',
            'items.*.qty.required' => 'Jumlah produk wajib diisi.',
        ]);

        try {
            $date = $validated['date'];
            $isPaid = $validated['is_paid'];
            $paidInput = isset($validated['paid']) ? (float) $validated['paid'] : 0;
            $paymentMethod = $validated['payment_method'] ?? null;
            $dueDate = $validated['due_date_payment'] ?? null;
            $status = $validated['status'] ?? null;

            foreach ($validated['items'] as $item) {
                $avail = AvailStock::find($item['id']);
                if (!$avail) {
                    DB::rollBack();
                    return back()->with('error', 'Produk tidak ditemukan.')->withInput();
                }

                $qty = (int) $item['qty'];
                if ($avail->qty_unit < $qty) {
                    DB::rollBack();
                    return back()->with('error', "Stok tidak cukup untuk {$avail->product_name}. Tersedia: {$avail->qty_unit}")->withInput();
                }

                $price = (float) $avail->price_unit;
                $total = $qty * $price;

                $paid = $isPaid === 'lunas' ? $total : min($paidInput, $total);
                $unpaid = max(0, $total - $paid);

                Transaction::create([
                    'date' => $date,
                    'id' => $avail->id,
                    'product_name' => $avail->product_name,
                    'size' => $avail->size?->size_label,
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $total,
                    'paid' => $paid,
                    'payment_method' => $paymentMethod,
                    'unpaid_amount' => $unpaid,
                    'due_date_payment' => $isPaid === 'belum_lunas' ? $dueDate : null,
                    'status' => $status ?? ($isPaid === 'lunas' ? 'dibayar' : 'pending'),
                ]);

                // kurangi stok
                $avail->qty_unit = max(0, $avail->qty_unit - $qty);
                $avail->save();
            }

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi tersimpan.');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
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
        DB::beginTransaction();
        try {
            $tx = Transaction::findOrFail($transaction_id);

            // merge avail stock data if missing
            if ($request->filled('id')) {
                $avail = AvailStock::find($request->input('id'));
                if ($avail) {
                    $request->merge([
                        'product_name' => $request->input('product_name') ?? $avail->product_name,
                        'price' => $request->input('price') ?? $avail->price_unit,
                    ]);
                }
            }

            // validation rules (due_date conditional if needed)
            $rules = [
                'date' => 'required|date',
                'id' => 'required|integer|exists:avail_stocks,id',
                'product_name' => 'required|string|max:191',
                'size' => 'required|string|max:100',
                'qty' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'paid' => 'nullable|numeric|min:0',
                'payment_method' => 'nullable|string|max:100',
                'status' => 'nullable|string|max:50',
            ];
            if ($request->input('is_paid') === 'belum_lunas') {
                $rules['due_date_payment'] = 'required|date|after_or_equal:date';
            } else {
                $rules['due_date_payment'] = 'nullable|date';
            }

            $validated = $request->validate($rules, [
                'product_name.required' => 'Nama produk wajib dipilih',
            ]);

            // recompute totals
            $validated['total'] = (int)$validated['qty'] * (float)$validated['price'];
            $validated['paid'] = isset($validated['paid']) ? (float)$validated['paid'] : 0;
            $validated['unpaid_amount'] = max(0, $validated['total'] - $validated['paid']);

            // find avail stock to adjust (prefer id)
            $availToUpdate = null;
            if (!empty($validated['id'])) {
                $availToUpdate = AvailStock::find($validated['id']);
            }
            if (!$availToUpdate && !empty($validated['product_name'])) {
                $availToUpdate = AvailStock::where('product_name', $validated['product_name'])->first();
            }

            // compute qty diff and apply to avail stock
            if ($availToUpdate) {
                $oldQty = (int)$tx->qty;
                $newQty = (int)$validated['qty'];
                $diff = $newQty - $oldQty; // positive => need to decrease avail stock; negative => increase avail stock

                if ($diff > 0) {
                    // need to consume additional stock
                    if ($availToUpdate->qty_unit < $diff) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Stok tidak cukup untuk menambah quantity.')->withInput();
                    }
                    $availToUpdate->qty_unit = $availToUpdate->qty_unit - $diff;
                } elseif ($diff < 0) {
                    // return stock
                    $availToUpdate->qty_unit = $availToUpdate->qty_unit + abs($diff);
                }
                $availToUpdate->save();
            }

            // update transaction
            $tx->update($validated);

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi diperbarui.');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('transactions.index')->with('error', 'Data tidak ditemukan');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($transaction_id)
    {
        DB::beginTransaction();
        try {
            $tx = Transaction::findOrFail($transaction_id);

            // cari avail stock berdasarkan id referensi jika ada, fallback by product_name
            $avail = null;
            if (!empty($tx->id)) {
                $avail = AvailStock::find($tx->id);
            }
            if (!$avail && !empty($tx->product_name)) {
                $avail = AvailStock::where('product_name', $tx->product_name)->first();
            }

            // jika ditemukan, tambahkan kembali qty_unit
            if ($avail && isset($tx->qty)) {
                $avail->qty_unit = (int)$avail->qty_unit + (int)$tx->qty;
                $avail->save();
            }

            // hapus transaksi
            $tx->delete();

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaksi dihapus dan stok dikembalikan.');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('transactions.index')->with('error', 'Transaksi tidak ditemukan.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }
}
