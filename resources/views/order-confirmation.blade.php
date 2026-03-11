@extends('layouts.app')

@section('title', 'Order Confirmed — ' . $siteName)

@section('content')
<div class="container">
<div class="confirmation-page">

    {{-- Success icon --}}
    <div class="confirmation-icon">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>

    <h1 style="font-size:var(--text-3xl);font-weight:var(--weight-bold);letter-spacing:var(--tracking-tight);margin-bottom:var(--sp-2)">
        Order Confirmed!
    </h1>
    <p style="color:var(--text-secondary);font-size:var(--text-lg);margin-bottom:var(--sp-2)">
        Thank you, <strong>{{ $order->customer_name }}</strong>.
    </p>
    <p style="color:var(--text-muted);font-size:var(--text-sm)">
        Order <strong style="color:var(--ink)">{{ $order->order_number }}</strong>
        · Confirmation sent to {{ $order->customer_email }}
    </p>

    {{-- Order details box --}}
    <div class="confirmation-order-box">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-6);margin-bottom:var(--sp-6)">
            <div>
                <div class="label-overline" style="margin-bottom:var(--sp-2)">Delivering to</div>
                <div style="font-size:var(--text-sm);color:var(--text-primary);line-height:var(--leading-relaxed)">
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}
                </div>
            </div>
            <div>
                <div class="label-overline" style="margin-bottom:var(--sp-2)">Payment</div>
                <div style="font-size:var(--text-sm);color:var(--text-primary)">Cash on Delivery</div>
                <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:2px">Pay when your order arrives</div>
            </div>
        </div>

        {{-- Items --}}
        <div style="border-top:1px solid var(--border);padding-top:var(--sp-4);margin-bottom:var(--sp-4)">
            @foreach($order->items as $item)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:var(--sp-2) 0;font-size:var(--text-sm)">
                <div>
                    <span style="font-weight:var(--weight-medium)">{{ $item->product_name }}</span>
                    @if(!empty($item->variant))
                        <span style="color:var(--text-muted);font-size:var(--text-xs)"> · {{ $item->variant_label }}</span>
                    @endif
                    <span style="color:var(--text-muted)"> × {{ $item->quantity }}</span>
                </div>
                <div style="font-weight:var(--weight-semibold)">
                    {{ $currencySymbol }}{{ number_format($item->line_total, 2) }}
                </div>
            </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div style="border-top:1px solid var(--border);padding-top:var(--sp-4)">
            <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);color:var(--text-secondary);margin-bottom:var(--sp-2)">
                <span>Subtotal</span>
                <span>{{ $currencySymbol }}{{ number_format($order->subtotal, 2) }}</span>
            </div>

            @if($order->coupon_discount > 0)
            <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);color:var(--text-secondary);margin-bottom:var(--sp-2)">
                <span>
                    Discount
                    @if($order->coupon_code)
                    <span style="font-size:var(--text-xs);background:var(--amber-light);color:var(--amber-hover);padding:1px 6px;border-radius:4px;margin-left:4px">{{ $order->coupon_code }}</span>
                    @endif
                </span>
                <span style="color:var(--success)">−{{ $currencySymbol }}{{ number_format($order->coupon_discount, 2) }}</span>
            </div>
            @endif

            <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);color:var(--text-secondary);margin-bottom:var(--sp-3)">
                <span>Shipping</span>
                <span>
                    @if($order->shipping_cost == 0)
                        <span style="color:var(--success)">Free</span>
                    @else
                        {{ $currencySymbol }}{{ number_format($order->shipping_cost, 2) }}
                    @endif
                </span>
            </div>

            <div style="display:flex;justify-content:space-between;font-size:var(--text-base);font-weight:var(--weight-bold);color:var(--ink)">
                <span>Total</span>
                <span>{{ $currencySymbol }}{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        @if($order->notes)
        <div style="margin-top:var(--sp-4);padding:var(--sp-3);background:var(--bg-subtle);border-radius:var(--radius);font-size:var(--text-sm);color:var(--text-secondary)">
            <span style="font-weight:var(--weight-medium);color:var(--text-primary)">Note: </span>{{ $order->notes }}
        </div>
        @endif
    </div>

    {{-- Account CTA for guests --}}
    @if(!session('customer_id'))
    <div style="background:var(--amber-light);border:1px solid var(--amber-mid);border-radius:var(--radius-lg);padding:var(--sp-5) var(--sp-6);margin-bottom:var(--sp-6);text-align:center;max-width:480px;margin-left:auto;margin-right:auto">
        <div style="font-weight:var(--weight-semibold);margin-bottom:var(--sp-1)">Track your orders easily</div>
        <p style="font-size:var(--text-sm);color:var(--text-secondary);margin-bottom:var(--sp-4)">Create a free account to view your order history and manage addresses.</p>
        <a href="{{ route('account.register') }}" class="btn btn-amber">Create Account</a>
    </div>
    @endif

    <div style="display:flex;gap:var(--sp-3);justify-content:center;flex-wrap:wrap">
        <a href="{{ route('shop') }}" class="btn btn-primary btn-lg">Continue Shopping</a>
        @if(session('customer_id'))
        <a href="{{ route('account.orders') }}" class="btn btn-outline btn-lg">View My Orders</a>
        @else
        <a href="{{ route('home') }}" class="btn btn-outline btn-lg">Back to Home</a>
        @endif
    </div>

</div>
</div>
@endsection
