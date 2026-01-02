<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\DetailProduct;
use App\Models\Size;
use App\Models\Production; // added import
use Illuminate\Routing\Controller;


class DetailProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = \App\Models\User::find(Auth::id());
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }
            // Admin and Staff Operasional can access
            if ($user->isAdmin()) {
                return $next($request);
            }
            if ($user->isStaffOperasional()) {
                return $next($request);
            }
            abort(403, 'Unauthorized access. Only Admin and Staff Operasional can access this module.');
        });
    }

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

        // Get all productions for card display
        $productions = Production::orderBy('production_id', 'desc')->get();
        
        // Calculate product count and stats for each production
        $productionStats = [];
        foreach ($productions as $prod) {
            $productCount = DetailProduct::where('production_id', $prod->production_id)->count();
            $totalQty = (int) DetailProduct::where('production_id', $prod->production_id)->sum('qty_unit');
            $totalUnitLimit = (int) $prod->total_unit;
            $remainingUnit = max(0, $totalUnitLimit - $totalQty);
            
            $productionStats[$prod->production_id] = [
                'product_count' => $productCount,
                'total_qty' => $totalQty,
                'total_limit' => $totalUnitLimit,
                'remaining_unit' => $remainingUnit,
                'percentage' => $totalUnitLimit > 0 ? round(($totalQty / $totalUnitLimit) * 100, 1) : 0
            ];
        }

        // If no production_id selected, show production cards only
        if (empty($production_id)) {
            return view('detail_product.index', compact('productions', 'productionStats'));
        }

        // If production_id selected, show form and table for that production
        // eager load relations
        $query = DetailProduct::with(['size', 'production'])->orderBy('product_id', 'desc');

        // enforce exact production_id filter
        $query->where('production_id', $production_id);

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

        // Get production info
        $production = Production::find($production_id);
        $productionLabel = $production ? $production->production_label : null;
        $totalUnitLimit = $production ? (int) $production->total_unit : 0;
        $currentTotal = (int) DetailProduct::where('production_id', $production_id)->sum('qty_unit');
        $remainingUnit = max(0, $totalUnitLimit - $currentTotal);

        return view('detail_product.index', compact('detailProducts', 'sizes', 'productionLabel', 'production', 'totalUnitLimit', 'currentTotal', 'remainingUnit', 'productions', 'productionStats', 'production_id'));
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
            'products'         => 'required|array|min:1',
            'products.*.product_name' => 'required|string|max:255',
            'products.*.size_id'      => 'required|integer|exists:size,size_id',
            'products.*.qty_unit'     => 'required|integer|min:1',
            'products.*.price_unit'   => 'required|integer|min:1',
        ]);

        $production = Production::find($validated['production_id']);
        $totalUnitLimit = $production ? (int) $production->total_unit : 0;
        $productionLabel = $production ? $production->production_label : null;

        $currentTotal = (int) DetailProduct::where('production_id', $validated['production_id'])->sum('qty_unit');
        
        // Calculate total incoming quantity
        $totalIncomingQty = 0;
        foreach ($validated['products'] as $product) {
            $totalIncomingQty += (int) $product['qty_unit'];
        }

        if ($currentTotal + $totalIncomingQty > $totalUnitLimit) {
            return redirect()->route('detail_product.index', [
                'production_id' => $validated['production_id'],
                'production_label' => $productionLabel,
            ])->withInput()->with('error', 'Gagal input! Total kuantitas melebihi batas. Limit: ' . number_format($totalUnitLimit) . ', Terpakai: ' . number_format($currentTotal) . ', Sisa: ' . number_format($totalUnitLimit - $currentTotal));
        }

        // Create all products
        $createdCount = 0;
        foreach ($validated['products'] as $product) {
            DetailProduct::create([
                'production_id' => $validated['production_id'],
                'product_name' => $product['product_name'],
                'size_id' => $product['size_id'],
                'qty_unit' => $product['qty_unit'],
                'price_unit' => $product['price_unit'],
            ]);
            $createdCount++;
        }

        return redirect()->route('detail_product.index', [
            'production_id' => $validated['production_id'],
            'production_label' => $productionLabel,
        ])->with('success', $createdCount . ' detail product(s) created.');
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
