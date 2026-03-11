<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class AdminStockController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filter === 'low') {
            $query->lowStock();
        } elseif ($request->filter === 'out') {
            $query->where('stock', '<=', 0);
        }

        $products     = $query->orderBy('stock', 'asc')->paginate(25)->withQueryString();
        $lowCount     = Product::lowStock()->count();
        $outCount     = Product::where('stock', '<=', 0)->where('is_active', true)->count();

        return view('admin.stock.index', compact('products', 'lowCount', 'outCount'));
    }

    public function history(Product $product)
    {
        $movements = $product->stockMovements()->latest()->paginate(30);
        return view('admin.stock.history', compact('product', 'movements'));
    }

    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'adjustment' => 'required|integer',
            'reason'     => 'required|string|max:200',
        ]);

        $adj = (int) $request->adjustment;

        if ($adj > 0) {
            // addStock(qty, unitCost, refType, refId, notes)
            $product->addStock($adj, 0, 'manual', null, $request->reason);
        } elseif ($adj < 0) {
            // deductStock(qty, refType, refId, notes)
            $product->deductStock(abs($adj), 'manual', null, $request->reason);
        }

        return back()->with('success', 'Stock adjusted by ' . ($adj > 0 ? '+' : '') . $adj . '.');
    }
}
