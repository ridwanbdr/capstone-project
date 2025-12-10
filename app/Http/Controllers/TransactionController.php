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

            // create transaction
            $tx = Transaction::create($validated);

            // reduce stock qty_unit on the corresponding avail stock (by id if present)
            $availToUpdate = null;

            if (!empty($validated['id'])) {
                $availToUpdate = AvailStock::find($validated['id']);
            }
            // fallback: find by product_name (shouldn't be necessary if id provided)
            if (!$availToUpdate && !empty($validated['product_name'])) {
                $availToUpdate = AvailStock::where('product_name', $validated['product_name'])->first();
            }

            if ($availToUpdate) {
                // cek stok cukup
                if ($availToUpdate->qty_unit < $validated['qty']) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Stok tidak cukup untuk produk ini.')->withInput();
                }

                $availToUpdate->qty_unit = max(0, $availToUpdate->qty_unit - $validated['qty']);
                $availToUpdate->save();
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
