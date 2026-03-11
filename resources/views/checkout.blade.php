@extends('layouts.app')

@section('title', 'Checkout — ' . $siteName)

@section('content')
<div class="container">
<div class="checkout-page">

    <div style="margin-bottom:var(--sp-6)">
        <h1 style="font-size:var(--text-3xl);font-weight:var(--weight-bold);letter-spacing:var(--tracking-tight)">Checkout</h1>
        <p style="color:var(--text-muted);font-size:var(--text-sm);margin-top:var(--sp-1)">
            <a href="{{ route('cart.index') }}" style="color:var(--amber);display:inline-flex;align-items:center;gap:4px">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Edit cart
            </a>
        </p>
    </div>

    <form action="{{ route('checkout.submit') }}" method="POST" id="checkoutForm">
    @csrf

    <div class="checkout-grid">

        {{-- ── Left: form sections ─────────────────────── --}}
        <div>

            {{-- Saved addresses (logged-in customers only) --}}
            @if(session('customer_id') && !empty($savedAddresses))
            <div class="checkout-section">
                <div class="checkout-section-header">
                    <span class="checkout-step-num">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <span class="checkout-section-title">Saved Addresses</span>
                </div>
                <div class="checkout-section-body">
                    <div style="display:flex;flex-direction:column;gap:var(--sp-3)">
                        @foreach($savedAddresses as $addr)
                        <label class="address-option{{ $loop->first ? ' selected' : '' }}" id="addrOpt{{ $addr->id }}">
                            <input type="radio" name="_saved_address_id" value="{{ $addr->id }}"
                                   {{ $loop->first ? 'checked' : '' }}
                                   onchange="fillAddress({{ $addr->id }}, '{{ addslashes($addr->full_name ?? $customerName ?? '') }}', '{{ addslashes($addr->phone ?? '') }}', '{{ addslashes($addr->address_line1 . ($addr->address_line2 ? ', '.$addr->address_line2 : '')) }}', '{{ addslashes($addr->city) }}')">
                            <div class="address-option-inner">
                                <div style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                                    {{ $addr->label ?? 'Address' }}
                                    @if($addr->is_default)
                                        <span class="address-default-badge">Default</span>
                                    @endif
                                </div>
                                <div style="color:var(--text-secondary);font-size:var(--text-sm);margin-top:2px">{{ $addr->one_line }}</div>
                            </div>
                        </label>
                        @endforeach
                        <label class="address-option" id="addrOptNew">
                            <input type="radio" name="_saved_address_id" value="new"
                                   onchange="clearAddress()">
                            <div class="address-option-inner">
                                <div style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:4px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Enter a new address
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            @endif

            {{-- Step 1: Contact & Shipping --}}
            <div class="checkout-section">
                <div class="checkout-section-header">
                    <span class="checkout-step-num">1</span>
                    <span class="checkout-section-title">Contact & Shipping</span>
                </div>
                <div class="checkout-section-body">

                    <div class="form-row-2" style="margin-bottom:var(--sp-4)">
                        <div class="form-group">
                            <label class="form-label" for="customer_name">Full Name <span class="req">*</span></label>
                            <input type="text" id="customer_name" name="customer_name"
                                   class="form-control{{ $errors->has('customer_name') ? ' error' : '' }}"
                                   value="{{ old('customer_name', $customerName ?? '') }}"
                                   placeholder="Your full name" required>
                            @error('customer_name') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="customer_phone">Phone Number</label>
                            <input type="tel" id="customer_phone" name="customer_phone"
                                   class="form-control{{ $errors->has('customer_phone') ? ' error' : '' }}"
                                   value="{{ old('customer_phone', $customerPhone ?? '') }}"
                                   placeholder="+1 555 000 0000">
                            @error('customer_phone') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:var(--sp-4)">
                        <label class="form-label" for="customer_email">Email Address <span class="req">*</span></label>
                        <input type="email" id="customer_email" name="customer_email"
                               class="form-control{{ $errors->has('customer_email') ? ' error' : '' }}"
                               value="{{ old('customer_email', $customerEmail ?? '') }}"
                               placeholder="you@example.com" required>
                        <span class="form-hint">Your order confirmation will be sent here.</span>
                        @error('customer_email') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom:var(--sp-4)">
                        <label class="form-label" for="shipping_address">Delivery Address <span class="req">*</span></label>
                        <input type="text" id="shipping_address" name="shipping_address"
                               class="form-control{{ $errors->has('shipping_address') ? ' error' : '' }}"
                               value="{{ old('shipping_address', $defaultAddress->address_line1 ?? '') }}"
                               placeholder="Street address, apartment, etc." required>
                        @error('shipping_address') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="shipping_city">City <span class="req">*</span></label>
                        <input type="text" id="shipping_city" name="shipping_city"
                               class="form-control{{ $errors->has('shipping_city') ? ' error' : '' }}"
                               value="{{ old('shipping_city', $defaultAddress->city ?? '') }}"
                               placeholder="City" required>
                        @error('shipping_city') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

            {{-- Step 2: Notes --}}
            <div class="checkout-section">
                <div class="checkout-section-header">
                    <span class="checkout-step-num">2</span>
                    <span class="checkout-section-title">Order Notes <span style="font-weight:normal;color:var(--text-muted)">(optional)</span></span>
                </div>
                <div class="checkout-section-body">
                    <div class="form-group">
                        <label class="form-label" for="notes">Special instructions</label>
                        <textarea id="notes" name="notes" class="form-control"
                                  rows="3" placeholder="e.g. Leave at door, call before delivery…">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Step 3: Payment --}}
            <div class="checkout-section">
                <div class="checkout-section-header">
                    <span class="checkout-step-num">3</span>
                    <span class="checkout-section-title">Payment</span>
                </div>
                <div class="checkout-section-body">
                    <div style="display:flex;align-items:center;gap:var(--sp-3);padding:var(--sp-4);background:var(--bg-subtle);border-radius:var(--radius-lg);border:1.5px solid var(--success-border)">
                        <div style="width:40px;height:40px;background:var(--success-bg);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        </div>
                        <div>
                            <div style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">Cash on Delivery</div>
                            <div style="font-size:var(--text-xs);color:var(--text-muted)">Pay when your order arrives. No card required.</div>
                        </div>
                        <div style="margin-left:auto">
                            <div style="width:20px;height:20px;background:var(--success);border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:var(--sp-2)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Place Order
            </button>
            <p style="font-size:var(--text-xs);color:var(--text-muted);text-align:center;margin-top:var(--sp-3)">
                By placing your order you agree to our terms of service.
            </p>
        </div>

        {{-- ── Right: order summary ─────────────────────── --}}
        <div>
            <div class="checkout-summary">
                <div class="checkout-summary-header">
                    <div class="checkout-summary-title">Order Summary ({{ count($cart) }} {{ Str::plural('item', count($cart)) }})</div>
                </div>

                <div class="checkout-items">
                    @foreach($cart as $item)
                    <div class="checkout-item">
                        <div class="checkout-item-img">
                            @if($item['image'])
                                <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}">
                            @else
                                <img src="{{ asset('images/placeholder-product.png') }}" alt="{{ $item['name'] }}">
                            @endif
                            <span class="checkout-item-qty">{{ $item['quantity'] }}</span>
                        </div>
                        <div class="checkout-item-info">
                            <div class="checkout-item-name">{{ $item['name'] }}</div>
                            @if(!empty($item['variant']))
                            <div class="checkout-item-variant">
                                @foreach($item['variant'] as $k => $v)
                                    {{ ucfirst($k) }}: {{ $v }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="checkout-item-price">
                            {{ $currencySymbol }}{{ number_format($item['price'] * $item['quantity'], 2) }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="checkout-summary-totals">
                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span>{{ $currencySymbol }}{{ number_format($subtotal, 2) }}</span>
                    </div>

                    @if(session('coupon') && $discount > 0)
                    <div class="summary-line discount">
                        <span>
                            Discount
                            <span style="font-size:var(--text-xs);background:var(--amber-light);color:var(--amber-hover);padding:1px 6px;border-radius:4px;margin-left:4px">
                                {{ session('coupon')['code'] }}
                            </span>
                        </span>
                        <span style="color:var(--success)">−{{ $currencySymbol }}{{ number_format($discount, 2) }}</span>
                    </div>
                    @endif

                    <div class="summary-line">
                        <span>Shipping</span>
                        <span>
                            @if($shipping == 0)
                                <span style="color:var(--success);font-weight:600">Free</span>
                            @else
                                {{ $currencySymbol }}{{ number_format($shipping, 2) }}
                            @endif
                        </span>
                    </div>
                    <div class="summary-line total">
                        <span>Total</span>
                        <span style="color:var(--ink)">{{ $currencySymbol }}{{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /checkout-grid --}}
    </form>

</div>
</div>
@endsection

@push('scripts')
<script>
// Fill address fields from saved address selection
function fillAddress(id, name, phone, address, city) {
    document.getElementById('customer_name').value    = name;
    document.getElementById('customer_phone').value   = phone;
    document.getElementById('shipping_address').value = address;
    document.getElementById('shipping_city').value    = city;

    // Update selected styling
    document.querySelectorAll('.address-option').forEach(el => el.classList.remove('selected'));
    document.getElementById('addrOpt' + id)?.classList.add('selected');
}
function clearAddress() {
    document.getElementById('shipping_address').value = '';
    document.getElementById('shipping_city').value    = '';
    document.querySelectorAll('.address-option').forEach(el => el.classList.remove('selected'));
    document.getElementById('addrOptNew')?.classList.add('selected');
}
</script>
@endpush
