<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailProduct;
use App\Models\Size;
use App\Models\Production; // added import

class DetailProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $production_id
     */
    public function index(Request $request, $production_id = null)
    {
        // prefer production_id from route, fallback to query string
        if (is_null($production_id)) {
            $production_id = $request->query('production_id', null);
        }

        // eager load relations
        $query = DetailProduct::with(['size', 'production'])->orderBy('product_id', 'desc');

        // enforce exact production_id filter first (if provided)
        if (!is_null($production_id) && $production_id !== '') {
            $query->where('production_id', $production_id);
        }

        // apply search (keeps production_id filter)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($qry) use ($search) {
                $qry->where('product_name', 'like', "%{$search}%")
                    ->orWhereHas('production', function ($q) use ($search) {
                        $q->where('production_label', 'like', "%{$search}%");
                    });
            });
        }

        $detailProducts = $query->paginate(15)->withQueryString();

        // sizes for dropdown ordered by id
        $sizes = Size::orderBy('size_id')->get();

        // determine productionLabel: prefer query param, else lookup from productions table
        $productionLabel = $request->query('production_label', null);
        if (empty($productionLabel) && !empty($production_id)) {
            $prod = Production::find($production_id);
            $productionLabel = $prod ? $prod->production_label : null;
        }

        return view('detail_product.index', compact('detailProducts', 'sizes', 'productionLabel'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sizes = Size::orderBy('size_id')->get();
        return view('detail_product.create', compact('sizes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'production_id'    => 'required|integer|exists:productions,production_id',
            'product_name'     => 'required|string|max:255',
            'size_id'          => 'required|integer|exists:size,size_id',
            'qty_unit'         => 'required|integer|min:1',
            'price_unit'       => 'required|integer|min:1',
        ]);

        $incomingQty = (int) $validated['qty_unit'];
        $priceUnit = (int) $validated['price_unit'];

        $production = Production::find($validated['production_id']);
        $totalUnitLimit = $production ? (int) $production->total_unit : 0;
        $productionLabel = $production ? $production->production_label : null;

        $currentTotal = (int) DetailProduct::where('production_id', $validated['production_id'])->sum('qty_unit');

        if ($currentTotal + $incomingQty > $totalUnitLimit) {
            return redirect()->route('detail_product.index', [
                'production_id' => $validated['production_id'],
                'production_label' => $productionLabel,
            ])->withInput()->with('error', 'Gagal input! Kuantitas melebihi batas');
        }

        DetailProduct::create($validated);

        return redirect()->route('detail_product.index', [
            'production_id' => $validated['production_id'],
            'production_label' => $productionLabel,
        ])->with('success', 'Detail product created.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detailProduct = DetailProduct::with('size')->findOrFail($id);
        return view('detail_product.show', compact('detailProduct'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $detailProduct = DetailProduct::findOrFail($id);
        $sizes = Size::orderBy('size_id')->get();
        return view('detail_product.edit', compact('detailProduct', 'sizes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'production_id'    => 'required|integer|exists:productions,production_id',
            'product_name'     => 'required|string|max:255',
            'size_id'          => 'required|integer|exists:size,size_id',
            'qty_unit'         => 'required|integer|min:1',
            'price_unit'       => 'required|integer|min:1',
        ]);

        $incomingQty = (int) $validated['qty_unit'];

        $detailProduct = DetailProduct::findOrFail($id);

        $production = Production::find($validated['production_id']);
        $totalUnitLimit = $production ? (int) $production->total_unit : 0;
        $productionLabel = $production ? $production->production_label : null;

        $currentTotalExcluding = (int) DetailProduct::where('production_id', $validated['production_id'])
            ->where('product_id', '!=', $id)
            ->sum('qty_unit');

        if ($currentTotalExcluding + $incomingQty > $totalUnitLimit) {
            return redirect()->route('detail_product.index', [
                'production_id' => $validated['production_id'],
                'production_label' => $productionLabel,
            ])->withInput()->with('error', 'Gagal input! Kuantitas melebihi batas');
        }

        $detailProduct->update($validated);

        return redirect()->route('detail_product.index', [
            'production_id' => $validated['production_id'],
            'production_label' => $productionLabel,
        ])->with('success', 'Detail product updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $detailProduct = DetailProduct::findOrFail($id);

        // capture production_id before deletion
        $productionId = $detailProduct->production_id;

        $detailProduct->delete();

        // retrieve production_label for redirect (if available)
        $production = Production::find($productionId);
        $productionLabel = $production ? $production->production_label : null;

        return redirect()->route('detail_product.index', [
            'production_id' => $productionId,
            'production_label' => $productionLabel,
        ])->with('success', 'Detail product deleted.');
    }
}
