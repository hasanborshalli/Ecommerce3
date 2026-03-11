<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier')
            ->withCount('items')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        if ($request->filled('search')) {
            $query->where('reference_number', 'like', '%' . $request->search . '%');
        }

        $purchaseOrders = $query->paginate(20)->withQueryString();

        $counts = [
            'all'      => PurchaseOrder::count(),
            'draft'    => PurchaseOrder::where('status', 'draft')->count(),
            'ordered'  => PurchaseOrder::where('status', 'ordered')->count(),
            'received' => PurchaseOrder::where('status', 'received')->count(),
        ];

        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('admin.purchase-orders.index',
            compact('purchaseOrders', 'counts', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products  = Product::where('is_active', true)
            ->select('id', 'name', 'sku', 'cost_price', 'stock')
            ->orderBy('name')
            ->get();

        return view('admin.purchase-orders.form', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $this->validatePO($request);

        $items = $this->parseItems($request);
        if (empty($items)) {
            return back()->withInput()
                ->with('error', 'Add at least one product to the purchase order.');
        }

        DB::transaction(function () use ($request, $items) {
            $po = PurchaseOrder::create([
                'supplier_id'      => $request->supplier_id ?: null,
                'reference_number' => PurchaseOrder::generateReference(),
                'order_date'       => $request->order_date,
                'expected_date'    => $request->expected_date ?: null,
                'status'           => $request->status ?? 'draft',
                'notes'            => $request->notes,
                'total_cost'       => 0,
            ]);

            $total = 0;
            foreach ($items as $item) {
                $lineCost    = round($item['qty'] * $item['cost'], 2);
                $total      += $lineCost;
                $productName = \App\Models\Product::find($item['product_id'])?->name ?? 'Unknown Product';

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id'        => $item['product_id'],
                    'product_name'      => $productName,
                    'quantity_ordered'  => $item['qty'],
                    'quantity_received' => 0,
                    'cost_per_unit'     => $item['cost'],
                    'total_cost'        => $lineCost,
                ]);
            }

            $po->update(['total_cost' => round($total, 2)]);
        });

        return redirect()->route('admin.purchase_orders.index')
            ->with('success', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('supplier', 'items.product');
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return redirect()->route('admin.purchase_orders.show', $purchaseOrder)
                ->with('error', 'Received purchase orders cannot be edited.');
        }

        $suppliers = Supplier::active()->orderBy('name')->get();
        $products  = Product::where('is_active', true)
            ->select('id', 'name', 'sku', 'cost_price', 'stock')
            ->orderBy('name')
            ->get();

        $purchaseOrder->load('items.product');

        return view('admin.purchase-orders.form',
            compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'Received purchase orders cannot be edited.');
        }

        $this->validatePO($request);

        $items = $this->parseItems($request);
        if (empty($items)) {
            return back()->withInput()->with('error', 'Add at least one product.');
        }

        DB::transaction(function () use ($request, $purchaseOrder, $items) {
            $purchaseOrder->items()->delete();

            $total = 0;
            foreach ($items as $item) {
                $lineCost    = round($item['qty'] * $item['cost'], 2);
                $total      += $lineCost;
                $productName = \App\Models\Product::find($item['product_id'])?->name ?? 'Unknown Product';

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id'        => $item['product_id'],
                    'product_name'      => $productName,
                    'quantity_ordered'  => $item['qty'],
                    'quantity_received' => 0,
                    'cost_per_unit'     => $item['cost'],
                    'total_cost'        => $lineCost,
                ]);
            }

            $purchaseOrder->update([
                'supplier_id'   => $request->supplier_id ?: null,
                'order_date'    => $request->order_date,
                'expected_date' => $request->expected_date ?: null,
                'status'        => $request->status ?? $purchaseOrder->status,
                'notes'         => $request->notes,
                'total_cost'    => round($total, 2),
            ]);
        });

        return redirect()->route('admin.purchase_orders.show', $purchaseOrder)
            ->with('success', 'Purchase order updated.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error',
                'Received orders cannot be deleted — stock has already been updated.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
        });

        return redirect()->route('admin.purchase_orders.index')
            ->with('success', 'Purchase order deleted.');
    }

    /**
     * Receive all items: add stock, update cost_price, log StockMovements.
     */
    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'received') {
            return back()->with('error', 'This order has already been received.');
        }

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->load('items.product');
            $purchaseOrder->markReceived();
        });

        return redirect()->route('admin.purchase_orders.show', $purchaseOrder)
            ->with('success', 'Stock received. All quantities and costs have been updated.');
    }

    // ── Private helpers ──────────────────────────────────────

    private function validatePO(Request $request): void
    {
        $request->validate([
            'supplier_id'            => 'nullable|exists:suppliers,id',
            'order_date'             => 'required|date',
            'expected_date'          => 'nullable|date|after_or_equal:order_date',
            'status'                 => 'nullable|in:draft,ordered',
            'notes'                  => 'nullable|string|max:1000',
            'items'                  => 'required|array|min:1',
            'items.*.product_id'     => 'required|exists:products,id',
            'items.*.qty'            => 'required|integer|min:1',
            'items.*.cost'           => 'required|numeric|min:0',
        ]);
    }

    private function parseItems(Request $request): array
    {
        $out = [];
        foreach ($request->input('items', []) as $row) {
            if (empty($row['product_id']) || empty($row['qty'])) continue;
            $out[] = [
                'product_id' => (int)   $row['product_id'],
                'qty'        => (int)   $row['qty'],
                'cost'       => (float) ($row['cost'] ?? 0),
            ];
        }
        return $out;
    }
}