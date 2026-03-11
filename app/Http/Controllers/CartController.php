<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // ── Helpers ──────────────────────────────────────────────

    private function getCart(): array
    {
        return session('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    private function cartRowId(int $productId, array $variant): string
    {
        return md5($productId . serialize($variant));
    }

    private function cartCount(array $cart): int
    {
        return array_sum(array_column($cart, 'quantity'));
    }

    private function cartSubtotal(array $cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return round($total, 2);
    }

    private function calcShipping(float $subtotal): array
    {
        $freeOver = (float) SiteSetting::get('free_shipping_over', 150);
        $shipCost = (float) SiteSetting::get('shipping_cost', 9);
        $shipping = $subtotal >= $freeOver ? 0 : $shipCost;
        return [$freeOver, $shipCost, $shipping];
    }

    // ── Actions ───────────────────────────────────────────────

    public function index()
    {
        $cart         = $this->getCart();
        $subtotal     = $this->cartSubtotal($cart);
        [$freeShipping, $shippingCost, $shipping] = $this->calcShipping($subtotal);

        // Apply coupon discount
        $discount = 0;
        if (session('coupon')) {
            $coupon   = Coupon::where('code', session('coupon')['code'])->first();
            $discount = $coupon ? $coupon->calculateDiscount($subtotal) : 0;
            // Coupon may cover shipping
            if (session('coupon')['type'] === 'free_shipping') {
                $shipping = 0;
                $discount = $shippingCost;
            }
        }

        $total      = max(0, $subtotal - $discount + $shipping);
        $amountToFree = max(0, $freeShipping - $subtotal);

        return view('cart', compact(
            'cart', 'subtotal', 'discount', 'shipping', 'total',
            'freeShipping', 'amountToFree', 'shippingCost'
        ));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
            'variant'    => 'nullable|array',
        ]);

        $product = Product::where('id', $validated['product_id'])
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'This product is not available.',
            ], 422);
        }

        $variant = $validated['variant'] ?? [];
        $rowId   = $this->cartRowId($product->id, $variant);
        $cart    = $this->getCart();
        $qty     = (int) $validated['quantity'];

        if (isset($cart[$rowId])) {
            $cart[$rowId]['quantity'] = min(
                $cart[$rowId]['quantity'] + $qty,
                $product->stock
            );
        } else {
            $cart[$rowId] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'slug'       => $product->slug,
                'price'      => (float) $product->effective_price,
                'image'      => $product->main_image,
                'variant'    => $variant,
                'quantity'   => min($qty, $product->stock),
                'stock'      => $product->stock,
            ];
        }

        $this->saveCart($cart);

        return response()->json([
            'success'    => true,
            'message'    => $product->name . ' added to cart',
            'cart_count' => $this->cartCount($cart),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'row_id'   => 'required|string',
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        $cart  = $this->getCart();
        $rowId = $validated['row_id'];
        $qty   = (int) $validated['quantity'];

        if (!isset($cart[$rowId])) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        if ($qty <= 0) {
            unset($cart[$rowId]);
        } else {
            $cart[$rowId]['quantity'] = min($qty, $cart[$rowId]['stock']);
        }

        $this->saveCart($cart);

        $subtotal = $this->cartSubtotal($cart);
        [$freeOver, $shipCost, $shipping] = $this->calcShipping($subtotal);

        return response()->json([
            'success'    => true,
            'cart_count' => $this->cartCount($cart),
            'cart_empty' => empty($cart),
            'subtotal'   => $subtotal,
            'shipping'   => $shipping,
            'total'      => round($subtotal + $shipping, 2),
        ]);
    }

    public function remove(Request $request)
    {
        $validated = $request->validate(['row_id' => 'required|string']);
        $cart = $this->getCart();
        unset($cart[$validated['row_id']]);
        $this->saveCart($cart);

        $subtotal = $this->cartSubtotal($cart);
        [$freeOver, $shipCost, $shipping] = $this->calcShipping($subtotal);

        return response()->json([
            'success'    => true,
            'cart_count' => $this->cartCount($cart),
            'cart_empty' => empty($cart),
            'subtotal'   => $subtotal,
            'shipping'   => $shipping,
            'total'      => round($subtotal + $shipping, 2),
        ]);
    }

    public function clear()
    {
        $this->saveCart([]);
        return response()->json(['success' => true, 'cart_count' => 0]);
    }

    public function count()
    {
        return response()->json(['count' => $this->cartCount($this->getCart())]);
    }

    // ── Coupon ────────────────────────────────────────────────

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);

        $code    = strtoupper(trim($request->code));
        $coupon  = Coupon::where('code', $code)->first();
        $cart    = $this->getCart();
        $subtotal = $this->cartSubtotal($cart);

        if (!$coupon) {
            return back()->with('coupon_error', 'Coupon code not found.');
        }

        $result = $coupon->validate(
            $subtotal,
            session('customer_id'),
            session('customer_email')
        );

        if (!$result['valid']) {
            return back()->with('coupon_error', $result['error']);
        }

        session(['coupon' => [
            'code'     => $coupon->code,
            'type'     => $coupon->type,
            'value'    => $coupon->value,
            'discount' => $coupon->calculateDiscount($subtotal),
        ]]);

        return back()->with('success', 'Coupon "' . $coupon->code . '" applied!');
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Coupon removed.');
    }
}
