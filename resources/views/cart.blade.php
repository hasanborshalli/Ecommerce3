@extends('layouts.app')

@section('title', 'Cart — ' . $siteName)

@section('content')
<div class="container">
<div class="cart-page">

    <h1 class="cart-page-title">Your Cart</h1>
    <p class="cart-page-count">{{ count($cart) }} {{ Str::plural('item', count($cart)) }}</p>

    @if(empty($cart))
    <div class="cart-empty">
        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.25">
            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 01-8 0"/>
        </svg>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added anything yet.</p>
        <a href="{{ route('shop') }}" class="btn btn-primary btn-lg">Start Shopping</a>
    </div>

    @else
    <div class="cart-layout">

        {{-- Items column --}}
        <div>
            <div class="cart-items" id="cartItemsList">
                @foreach($cart as $rowId => $item)
                <div class="cart-item" id="cart-row-{{ $rowId }}">

                    <a href="{{ route('product.show', $item['slug']) }}" class="cart-item-image">
                        @if($item['image'])
                            <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}">
                        @else
                            <img src="{{ asset('images/placeholder-product.png') }}" alt="{{ $item['name'] }}">
                        @endif
                    </a>

                    <div class="cart-item-body">
                        <div class="cart-item-top">
                            <div>
                                <a href="{{ route('product.show', $item['slug']) }}"
                                   class="cart-item-name">{{ $item['name'] }}</a>
                                @if(!empty($item['variant']))
                                <div class="cart-item-variant">
                                    @foreach($item['variant'] as $k => $v)
                                        {{ ucfirst($k) }}: {{ $v }}@if(!$loop->last), @endif
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div>
                                <div class="cart-item-price-total" id="line-total-{{ $rowId }}">
                                    {{ $currencySymbol }}{{ number_format($item['price'] * $item['quantity'], 2) }}
                                </div>
                                <div class="cart-item-price-unit">
                                    {{ $currencySymbol }}{{ number_format($item['price'], 2) }} each
                                </div>
                            </div>
                        </div>

                        <div class="cart-item-bottom">
                            <div class="qty-control">
                                <button type="button"
                                        onclick="updateQty('{{ $rowId }}', {{ $item['quantity'] - 1 }}, {{ $item['price'] }})">−</button>
                                <input type="number" class="qty-input" value="{{ $item['quantity'] }}"
                                       min="0" max="{{ $item['stock'] }}"
                                       id="qty-{{ $rowId }}"
                                       onchange="updateQty('{{ $rowId }}', this.value, {{ $item['price'] }})">
                                <button type="button"
                                        onclick="updateQty('{{ $rowId }}', {{ $item['quantity'] + 1 }}, {{ $item['price'] }})">+</button>
                            </div>
                            <button class="cart-remove-btn" onclick="removeItem('{{ $rowId }}')" type="button">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                Remove
                            </button>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>

            <div class="cart-footer">
                <a href="{{ route('shop') }}" class="btn btn-outline">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Continue Shopping
                </a>
                <button type="button" onclick="clearCart()" class="btn btn-ghost" style="color:var(--danger)">
                    Clear Cart
                </button>
            </div>
        </div>

        {{-- Summary sidebar --}}
        <div>
            <div class="cart-summary">
                <div class="cart-summary-title">Order Summary</div>

                {{-- Free shipping progress --}}
                @if($amountToFree > 0)
                <p style="font-size:var(--text-sm);color:var(--text-secondary);margin-bottom:var(--sp-2)">
                    Add <strong style="color:var(--ink)">{{ $currencySymbol }}{{ number_format($amountToFree, 2) }}</strong> more for free shipping
                </p>
                <div class="free-shipping-bar">
                    <div class="free-shipping-progress"
                         style="width:{{ min(100, ($subtotal / $freeShipping) * 100) }}%"></div>
                </div>
                @else
                <div class="alert alert-success" style="margin-bottom:var(--sp-4);font-size:var(--text-xs)">
                    🎉 You qualify for free shipping!
                </div>
                @endif

                {{-- Coupon field --}}
                <div class="coupon-wrap">
                    <div class="coupon-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                        Discount Code
                    </div>

                    @if(session('coupon'))
                    {{-- Coupon applied --}}
                    <div class="coupon-applied">
                        <div class="coupon-applied-code">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            {{ session('coupon')['code'] }}
                        </div>
                        <form action="{{ route('coupon.remove') }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="coupon-remove-btn" title="Remove coupon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </form>
                    </div>
                    @else
                    <form action="{{ route('coupon.apply') }}" method="POST">
                        @csrf
                        <div class="coupon-input-row">
                            <input type="text" name="code" class="form-control"
                                   placeholder="ENTER CODE"
                                   value="{{ old('code') }}"
                                   style="text-transform:uppercase">
                            <button type="submit" class="btn btn-outline">Apply</button>
                        </div>
                        @if(session('coupon_error'))
                        <div class="coupon-error">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ session('coupon_error') }}
                        </div>
                        @endif
                    </form>
                    @endif
                </div>

                {{-- Totals --}}
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span id="summarySubtotal">{{ $currencySymbol }}{{ number_format($subtotal, 2) }}</span>
                </div>

                @if(session('coupon') && $discount > 0)
                <div class="summary-line discount">
                    <span>Discount ({{ session('coupon')['code'] }})</span>
                    <span>−{{ $currencySymbol }}{{ number_format($discount, 2) }}</span>
                </div>
                @endif

                <div class="summary-line">
                    <span>Shipping</span>
                    <span id="summaryShipping">
                        @if($shipping == 0)
                            <span style="color:var(--success);font-weight:600">Free</span>
                        @else
                            {{ $currencySymbol }}{{ number_format($shipping, 2) }}
                        @endif
                    </span>
                </div>
                <div class="summary-line total">
                    <span>Total</span>
                    <span id="summaryTotal">{{ $currencySymbol }}{{ number_format($total, 2) }}</span>
                </div>

                <a href="{{ route('checkout') }}" class="btn btn-primary btn-full btn-lg" style="margin-top:var(--sp-4)">
                    Proceed to Checkout
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>

                <div class="secure-note">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    Secure checkout · Cash on delivery
                </div>
            </div>
        </div>

    </div>
    @endif

</div>
</div>
@endsection

@push('scripts')
<script>
const currSym      = '{{ $currencySymbol }}';
const csrfToken    = document.querySelector('meta[name="csrf-token"]').content;
const freeOver     = {{ $freeShipping }};
const baseShipping = {{ $shippingCost }};

function fmt(n) {
    return currSym + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function updateQty(rowId, qty, price) {
    qty = parseInt(qty);
    if (isNaN(qty)) qty = 1;
    const qtyInput = document.getElementById('qty-' + rowId);
    if (qtyInput) qtyInput.value = Math.max(0, qty);

    fetch('{{ route("cart.update") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ row_id: rowId, quantity: qty }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        if (qty <= 0) {
            document.getElementById('cart-row-' + rowId)?.remove();
        } else {
            const lt = document.getElementById('line-total-' + rowId);
            if (lt) lt.textContent = fmt(price * qty);
        }
        updateSummary(data);
        updateCartBadge(data.cart_count);
        if (data.cart_empty) location.reload();
    });
}

function removeItem(rowId) {
    fetch('{{ route("cart.remove") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ row_id: rowId }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        document.getElementById('cart-row-' + rowId)?.remove();
        updateSummary(data);
        updateCartBadge(data.cart_count);
        showToast('Item removed');
        if (data.cart_empty) location.reload();
    });
}

function clearCart() {
    fetch('{{ route("cart.clear") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': csrfToken },
    })
    .then(() => location.reload());
}

function updateSummary(data) {
    const sub  = parseFloat(data.subtotal)  || 0;
    const ship = parseFloat(data.shipping)  || 0;
    const tot  = parseFloat(data.total)     || 0;
    const elSub  = document.getElementById('summarySubtotal');
    const elShip = document.getElementById('summaryShipping');
    const elTot  = document.getElementById('summaryTotal');
    if (elSub)  elSub.textContent  = fmt(sub);
    if (elShip) elShip.innerHTML   = ship === 0
        ? '<span style="color:var(--success);font-weight:600">Free</span>' : fmt(ship);
    if (elTot)  elTot.textContent  = fmt(tot);
}
</script>
@endpush
