@extends('layouts.app')

@section('title', 'Order ' . $order->order_number . ' — ' . $siteName)

@section('content')
<div class="container">
    <div class="account-layout">

        @include('account.partials.nav', ['active' => 'orders'])

        <div class="account-main">
            <div class="account-section">

                {{-- Header --}}
                <div
                    style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:var(--sp-3);margin-bottom:var(--sp-6)">
                    <div>
                        <a href="{{ route('account.orders') }}"
                            style="display:inline-flex;align-items:center;gap:4px;font-size:var(--text-sm);color:var(--text-muted);margin-bottom:var(--sp-2)">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                            All orders
                        </a>
                        <h1 class="account-section-title">{{ $order->order_number }}</h1>
                        <p style="font-size:var(--text-sm);color:var(--text-muted);margin-top:2px">
                            Placed {{ $order->created_at->format('F j, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <span class="order-status-badge status-{{ $order->status }}"
                        style="font-size:var(--text-sm);padding:var(--sp-2) var(--sp-4)">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                {{-- Two-column grid --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-6);margin-bottom:var(--sp-6)">
                    <div
                        style="background:var(--bg-subtle);padding:var(--sp-5);border-radius:var(--radius-lg);border:1px solid var(--border)">
                        <div class="label-overline" style="margin-bottom:var(--sp-3)">Delivery Address</div>
                        <div style="font-size:var(--text-sm);line-height:var(--leading-relaxed)">
                            {{ $order->customer_name }}<br>
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}
                            @if($order->shipping_state) , {{ $order->shipping_state }} @endif
                            @if($order->shipping_postal_code) {{ $order->shipping_postal_code }} @endif
                        </div>
                        @if($order->customer_phone)
                        <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:var(--sp-2)">
                            {{ $order->customer_phone }}
                        </div>
                        @endif
                    </div>
                    <div
                        style="background:var(--bg-subtle);padding:var(--sp-5);border-radius:var(--radius-lg);border:1px solid var(--border)">
                        <div class="label-overline" style="margin-bottom:var(--sp-3)">Payment</div>
                        <div style="font-size:var(--text-sm)">Cash on Delivery</div>
                        <div style="margin-top:var(--sp-2)">
                            @if($order->payment_status === 'paid')
                            <span
                                style="font-size:var(--text-xs);color:var(--success);font-weight:var(--weight-semibold)">✓
                                Paid</span>
                            @else
                            <span style="font-size:var(--text-xs);color:var(--text-muted)">Pay on delivery</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Order items --}}
                <div
                    style="border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;margin-bottom:var(--sp-6)">
                    <div
                        style="padding:var(--sp-4) var(--sp-5);background:var(--bg-subtle);border-bottom:1px solid var(--border)">
                        <span style="font-size:var(--text-sm);font-weight:var(--weight-semibold)">
                            Items ({{ $order->items->count() }})
                        </span>
                    </div>
                    @foreach($order->items as $item)
                    <div
                        style="display:flex;align-items:center;gap:var(--sp-4);padding:var(--sp-4) var(--sp-5);{{ !$loop->last ? 'border-bottom:1px solid var(--border)' : '' }}">
                        <div
                            style="width:52px;height:52px;background:var(--bg-subtle);border-radius:var(--radius);flex-shrink:0;overflow:hidden">
                            @if($item->product?->main_image)
                            <img src="{{ Storage::url($item->product->main_image) }}" alt="{{ $item->product_name }}"
                                style="width:100%;height:100%;object-fit:contain">
                            @endif
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:var(--text-sm);font-weight:var(--weight-medium)">
                                {{ $item->product_name }}
                            </div>
                            @if(!empty($item->variant))
                            <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:1px">
                                {{ $item->variant_label }}
                            </div>
                            @endif
                            <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:1px">
                                Qty: {{ $item->quantity }}
                                · {{ $currencySymbol }}{{ number_format($item->product_price, 2) }} each
                            </div>
                        </div>
                        <div style="font-size:var(--text-sm);font-weight:var(--weight-semibold);white-space:nowrap">
                            {{ $currencySymbol }}{{ number_format($item->line_total, 2) }}
                        </div>

                        {{-- Review button if product exists and order is delivered --}}
                        @if($item->product && in_array($order->status, ['delivered', 'shipped', 'processing']))
                        <div>
                            @php $hasReviewed = \App\Models\Review::where('product_id',
                            $item->product_id)->where('customer_id', session('customer_id'))->exists(); @endphp
                            @if($hasReviewed)
                            <span style="font-size:var(--text-xs);color:var(--text-muted)">Reviewed</span>
                            @else
                            <a href="{{ route('product.show', $item->product->slug) }}#reviewsSection"
                                class="btn btn-sm btn-outline">
                                Review
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div style="max-width:320px;margin-left:auto">
                    <div class="summary-line" style="padding:var(--sp-2) 0">
                        <span>Subtotal</span>
                        <span>{{ $currencySymbol }}{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->coupon_discount > 0)
                    <div class="summary-line discount" style="padding:var(--sp-2) 0">
                        <span>
                            Discount
                            @if($order->coupon_code)
                            <span
                                style="font-size:var(--text-xs);background:var(--amber-light);color:var(--amber-hover);padding:1px 5px;border-radius:3px;margin-left:4px">{{
                                $order->coupon_code }}</span>
                            @endif
                        </span>
                        <span style="color:var(--success)">−{{ $currencySymbol }}{{
                            number_format($order->coupon_discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="summary-line" style="padding:var(--sp-2) 0">
                        <span>Shipping</span>
                        <span>
                            @if($order->shipping_cost == 0)
                            <span style="color:var(--success)">Free</span>
                            @else
                            {{ $currencySymbol }}{{ number_format($order->shipping_cost, 2) }}
                            @endif
                        </span>
                    </div>
                    <div class="summary-line total" style="padding:var(--sp-3) 0;margin-top:var(--sp-2)">
                        <span>Total</span>
                        <span>{{ $currencySymbol }}{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

                @if($order->notes)
                <div
                    style="margin-top:var(--sp-5);padding:var(--sp-4);background:var(--bg-subtle);border-radius:var(--radius);border:1px solid var(--border);font-size:var(--text-sm)">
                    <span style="font-weight:var(--weight-semibold)">Order note: </span>
                    <span style="color:var(--text-secondary)">{{ $order->notes }}</span>
                </div>
                @endif

            </div>
        </div>

    </div>
</div>
@endsection