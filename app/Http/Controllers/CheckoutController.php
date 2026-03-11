<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUse;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $subtotal     = $this->cartSubtotal($cart);
        $freeShipping = (float) SiteSetting::get('free_shipping_over', 150);
        $shippingCost = (float) SiteSetting::get('shipping_cost', 9);

        // Coupon discount
        $discount = 0;
        $shipping = $subtotal >= $freeShipping ? 0 : $shippingCost;

        if (session('coupon')) {
            $couponData = session('coupon');
            if ($couponData['type'] === 'free_shipping') {
                $shipping = 0;
                $discount = 0; // shown separately as free shipping
            } else {
                $coupon  = Coupon::where('code', $couponData['code'])->first();
                $discount = $coupon ? $coupon->calculateDiscount($subtotal) : 0;
            }
        }

        $total = max(0, $subtotal - $discount + $shipping);

        // Pre-fill for logged-in customer
        $customerName    = null;
        $customerEmail   = null;
        $customerPhone   = null;
        $savedAddresses  = [];
        $defaultAddress  = null;

        if ($customerId = session('customer_id')) {
            $customer = Customer::find($customerId);
            if ($customer) {
                $customerName   = $customer->full_name;
                $customerEmail  = $customer->email;
                $customerPhone  = $customer->phone;
                $savedAddresses = CustomerAddress::where('customer_id', $customerId)
                    ->orderByDesc('is_default')
                    ->get();
                $defaultAddress = $savedAddresses->where('is_default', true)->first()
                    ?? $savedAddresses->first();
            }
        }

        return view('checkout', compact(
            'cart', 'subtotal', 'discount', 'shipping', 'total',
            'customerName', 'customerEmail', 'customerPhone',
            'savedAddresses', 'defaultAddress'
        ));
    }

    public function submit(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'customer_name'    => 'required|string|max:100',
            'customer_email'   => 'required|email|max:150',
            'customer_phone'   => 'nullable|string|max:30',
            'shipping_address' => 'required|string|max:200',
            'shipping_city'    => 'required|string|max:100',
            'notes'            => 'nullable|string|max:500',
        ]);

        $subtotal     = $this->cartSubtotal($cart);
        $freeShipping = (float) SiteSetting::get('free_shipping_over', 150);
        $shippingCost = (float) SiteSetting::get('shipping_cost', 9);
        $shipping     = $subtotal >= $freeShipping ? 0 : $shippingCost;
        $discount     = 0;
        $couponId     = null;
        $couponCode   = null;

        // Validate coupon again at submit time
        if (session('coupon')) {
            $couponData = session('coupon');
            $coupon     = Coupon::where('code', $couponData['code'])->first();

            if ($coupon) {
                $result = $coupon->validate(
                    $subtotal,
                    session('customer_id'),
                    $validated['customer_email']
                );

                if ($result['valid']) {
                    $couponId   = $coupon->id;
                    $couponCode = $coupon->code;
                    if ($coupon->type === 'free_shipping') {
                        $shipping = 0;
                    } else {
                        $discount = $coupon->calculateDiscount($subtotal);
                    }
                }
            }
        }

        $total = max(0, $subtotal - $discount + $shipping);

        DB::transaction(function () use ($validated, $cart, $subtotal, $shipping, $discount, $total, $couponId, $couponCode) {

            $costTotal = 0;
            $lineItems = [];

            foreach ($cart as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) continue;

                $qty        = (int) $item['quantity'];
                $price      = (float) $item['price'];
                $cost       = (float) $product->cost_price;
                $lineTotal  = round($price * $qty, 2);
                $lineCost   = round($cost  * $qty, 2);
                $costTotal += $lineCost;

                $lineItems[] = [
                    'product_id'    => $product->id,
                    'product_name'  => $item['name'],
                    'product_sku'   => $product->sku,
                    'product_price' => $price,
                    'product_cost'  => $cost,
                    'quantity'      => $qty,
                    'variant'       => !empty($item['variant']) ? json_encode($item['variant']) : null,
                    'line_total'    => $lineTotal,
                    'line_cost'     => $lineCost,
                    'line_profit'   => round($lineTotal - $lineCost, 2),
                ];

                $product->deductStock($qty, 'order', null, 'Sale');
            }

            $order = Order::create([
                'order_number'     => Order::generateOrderNumber(),
                'customer_id'      => session('customer_id'),
                'customer_name'    => $validated['customer_name'],
                'customer_email'   => $validated['customer_email'],
                'customer_phone'   => $validated['customer_phone'] ?? null,
                'shipping_address' => $validated['shipping_address'],
                'shipping_city'    => $validated['shipping_city'],
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shipping,
                'coupon_id'        => $couponId,
                'coupon_code'      => $couponCode,
                'coupon_discount'  => $discount,
                'total'            => $total,
                'cost_total'       => round($costTotal, 2),
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'payment_method'   => 'cash_on_delivery',
                'notes'            => $validated['notes'] ?? null,
            ]);

            foreach ($lineItems as $li) {
                $order->items()->create($li);
            }

            // Fix stock movement references
            \App\Models\StockMovement::whereNull('reference_id')
                ->where('reference_type', 'order')
                ->where('created_at', '>=', now()->subMinutes(1))
                ->update(['reference_id' => $order->id]);

            // Record coupon use
            if ($couponId) {
                CouponUse::create([
                    'coupon_id'      => $couponId,
                    'order_id'       => $order->id,
                    'customer_id'    => session('customer_id'),
                    'customer_email' => $validated['customer_email'],
                    'discount_amount'=> $discount,
                ]);
                // Increment coupon use count
                Coupon::where('id', $couponId)->increment('used_count');
            }

            session([
                'cart'               => [],
                'coupon'             => null,
                'last_order_number'  => $order->order_number,
                'last_order_id'      => $order->id,
            ]);
        });

        return redirect()->route('checkout.confirmation', session('last_order_id'));
    }

    public function confirmation(Order $order)
    {
        if ($order->order_number !== session('last_order_number')) {
            abort(403);
        }

        $order->load('items');
        return view('order-confirmation', compact('order'));
    }

    // ── Helper ───────────────────────────────────────────────

    private function cartSubtotal(array $cart): float
    {
        return round(array_sum(array_map(
            fn($i) => $i['price'] * $i['quantity'],
            $cart
        )), 2);
    }
}
