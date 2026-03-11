@extends('admin.layout')

@section('title', 'Order ' . $order->order_number)
@section('page_title', 'Order ' . $order->order_number)
@section('breadcrumb')
    <a href="{{ route('admin.orders.index') }}">Orders</a> › {{ $order->order_number }}
@endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>{{ $order->order_number }}</h1>
        <p style="display:flex;align-items:center;gap:var(--sp-3)">
            @php $sb = $order->status_badge; $pb = $order->payment_badge; @endphp
            <span class="badge {{ $sb['class'] }}">{{ $sb['label'] }}</span>
            <span class="badge {{ $pb['class'] }}">{{ $pb['label'] }}</span>
            <span style="color:var(--admin-muted);font-size:var(--text-sm)">
                {{ $order->created_at->format('M d, Y · H:i') }}
            </span>
        </p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.orders.index') }}" class="abtn abtn-outline">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            All Orders
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:var(--sp-5);align-items:start">

    {{-- ── Left: items + profit ─────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

        {{-- Items table --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">Order Items</span>
                <span style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $order->items->sum('quantity') }} {{ Str::plural('item', $order->items->sum('quantity')) }}
                </span>
            </div>
            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Cost</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Line Total</th>
                            <th class="text-right">Line Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:var(--sp-3)">
                                    @if($item->product?->main_image)
                                        <img src="{{ Storage::url($item->product->main_image) }}"
                                             style="width:40px;height:40px;object-fit:contain;border:1px solid var(--admin-border);border-radius:var(--radius);flex-shrink:0">
                                    @else
                                        <div style="width:40px;height:40px;background:var(--admin-bg);border:1px solid var(--admin-border);border-radius:var(--radius);flex-shrink:0"></div>
                                    @endif
                                    <div>
                                        <div style="font-weight:var(--weight-medium);font-size:var(--text-sm)">
                                            {{ $item->product_name }}
                                        </div>
                                        @if($item->product_sku)
                                            <div style="font-size:10px;color:var(--admin-muted)">SKU: {{ $item->product_sku }}</div>
                                        @endif
                                        @if($item->variant_label)
                                            <div style="font-size:var(--text-xs);color:var(--admin-muted)">{{ $item->variant_label }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-right" style="font-size:var(--text-sm)">
                                {{ $currencySymbol }}{{ number_format($item->product_price, 2) }}
                            </td>
                            <td class="text-right" style="font-size:var(--text-sm);color:var(--admin-muted)">
                                {{ $currencySymbol }}{{ number_format($item->product_cost, 2) }}
                            </td>
                            <td class="text-right" style="font-size:var(--text-sm)">
                                {{ $item->quantity }}
                            </td>
                            <td class="text-right" style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                                {{ $currencySymbol }}{{ number_format($item->line_total, 2) }}
                            </td>
                            <td class="text-right">
                                <span class="{{ $item->line_profit >= 0 ? 'profit-positive' : 'profit-negative' }}"
                                      style="font-size:var(--text-sm)">
                                    {{ $currencySymbol }}{{ number_format($item->line_profit, 2) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align:right;font-size:var(--text-sm);color:var(--admin-muted)">Subtotal</td>
                            <td class="text-right" style="font-size:var(--text-sm)">{{ $currencySymbol }}{{ number_format($order->subtotal, 2) }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align:right;font-size:var(--text-sm);color:var(--admin-muted)">Shipping</td>
                            <td class="text-right" style="font-size:var(--text-sm)">
                                @if($order->shipping_cost == 0)
                                    <span style="color:var(--success)">Free</span>
                                @else
                                    {{ $currencySymbol }}{{ number_format($order->shipping_cost, 2) }}
                                @endif
                            </td>
                            <td></td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td colspan="4" style="text-align:right;font-size:var(--text-sm);color:var(--admin-muted)">Discount</td>
                            <td class="text-right" style="font-size:var(--text-sm);color:var(--danger)">
                                −{{ $currencySymbol }}{{ number_format($order->discount, 2) }}
                            </td>
                            <td></td>
                        </tr>
                        @endif
                        <tr style="border-top:2px solid var(--admin-border)">
                            <td colspan="4" style="text-align:right;font-weight:var(--weight-bold)">Total</td>
                            <td class="text-right" style="font-weight:var(--weight-bold);font-size:var(--text-base)">
                                {{ $currencySymbol }}{{ number_format($order->total, 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Profit breakdown --}}
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">Profit Breakdown</span>
                <span style="font-size:var(--text-xs);color:var(--admin-muted)">Costs frozen at time of sale</span>
            </div>
            <div class="admin-card-body">
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--sp-4)">
                    <div style="background:var(--admin-bg);border-radius:var(--radius-lg);padding:var(--sp-4);text-align:center">
                        <div style="font-size:var(--text-xs);text-transform:uppercase;letter-spacing:var(--tracking-wider);font-weight:var(--weight-semibold);color:var(--admin-muted);margin-bottom:var(--sp-2)">Revenue</div>
                        <div style="font-size:var(--text-2xl);font-weight:var(--weight-bold);color:var(--admin-text)">
                            {{ $currencySymbol }}{{ number_format($order->total, 2) }}
                        </div>
                    </div>
                    <div style="background:var(--admin-bg);border-radius:var(--radius-lg);padding:var(--sp-4);text-align:center">
                        <div style="font-size:var(--text-xs);text-transform:uppercase;letter-spacing:var(--tracking-wider);font-weight:var(--weight-semibold);color:var(--admin-muted);margin-bottom:var(--sp-2)">Cost</div>
                        <div style="font-size:var(--text-2xl);font-weight:var(--weight-bold);color:var(--danger)">
                            {{ $currencySymbol }}{{ number_format($order->cost_total, 2) }}
                        </div>
                    </div>
                    <div style="background:{{ $order->profit >= 0 ? 'var(--success-bg)' : 'var(--danger-bg)' }};border-radius:var(--radius-lg);padding:var(--sp-4);text-align:center">
                        <div style="font-size:var(--text-xs);text-transform:uppercase;letter-spacing:var(--tracking-wider);font-weight:var(--weight-semibold);color:var(--admin-muted);margin-bottom:var(--sp-2)">Profit</div>
                        <div style="font-size:var(--text-2xl);font-weight:var(--weight-bold);color:{{ $order->profit >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                            {{ $currencySymbol }}{{ number_format($order->profit, 2) }}
                        </div>
                        <div style="font-size:var(--text-xs);color:var(--admin-muted);margin-top:4px">
                            {{ $order->profit_margin }}% margin
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order notes --}}
        @if($order->notes)
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Customer Notes</span></div>
            <div class="admin-card-body">
                <p style="font-size:var(--text-sm);color:var(--admin-muted);line-height:var(--leading-relaxed)">
                    {{ $order->notes }}
                </p>
            </div>
        </div>
        @endif

    </div>

    {{-- ── Right sidebar ─────────────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

        {{-- Status control --}}
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Order Status</span></div>
            <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                <select id="statusSelect" class="aform-control">
                    @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $st)
                    <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>
                        {{ ucfirst($st) }}
                    </option>
                    @endforeach
                </select>
                <button type="button" class="abtn abtn-primary abtn-full" onclick="saveStatus()">
                    Update Status
                </button>

                {{-- Status timeline --}}
                @php
                $timeline = ['pending','confirmed','processing','shipped','delivered'];
                $curIdx   = array_search($order->status, $timeline);
                @endphp
                @if($order->status !== 'cancelled')
                <div style="margin-top:var(--sp-3)">
                    @foreach($timeline as $idx => $step)
                    <div style="display:flex;align-items:center;gap:var(--sp-2);padding:var(--sp-1) 0">
                        <div style="width:20px;height:20px;border-radius:var(--radius-full);flex-shrink:0;
                             background:{{ $curIdx !== false && $idx <= $curIdx ? 'var(--success)' : 'var(--gray-200)' }};
                             display:flex;align-items:center;justify-content:center">
                            @if($curIdx !== false && $idx <= $curIdx)
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            @else
                                <div style="width:8px;height:8px;border-radius:var(--radius-full);background:var(--gray-400)"></div>
                            @endif
                        </div>
                        <span style="font-size:var(--text-xs);color:{{ $curIdx !== false && $idx <= $curIdx ? 'var(--admin-text)' : 'var(--admin-muted)' }};
                              font-weight:{{ $order->status === $step ? 'var(--weight-semibold)' : 'normal' }}">
                            {{ ucfirst($step) }}
                        </span>
                    </div>
                    @if(!$loop->last)
                    <div style="width:2px;height:12px;background:{{ $curIdx !== false && $idx < $curIdx ? 'var(--success)' : 'var(--gray-200)' }};margin-left:9px"></div>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Payment --}}
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Payment</span></div>
            <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                <div style="display:flex;justify-content:space-between;font-size:var(--text-sm)">
                    <span style="color:var(--admin-muted)">Method</span>
                    <span style="font-weight:var(--weight-medium)">Cash on Delivery</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:var(--text-sm)">
                    <span style="color:var(--admin-muted)">Status</span>
                    @php $pb = $order->payment_badge; @endphp
                    <span class="badge {{ $pb['class'] }}">{{ $pb['label'] }}</span>
                </div>
                <select id="paymentSelect" class="aform-control" style="margin-top:var(--sp-1)">
                    <option value="unpaid"  {{ $order->payment_status === 'unpaid'   ? 'selected' : '' }}>Unpaid</option>
                    <option value="paid"    {{ $order->payment_status === 'paid'     ? 'selected' : '' }}>Paid</option>
                    <option value="refunded"{{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
                <button type="button" class="abtn abtn-outline abtn-full" onclick="savePayment()">
                    Update Payment
                </button>
            </div>
        </div>

        {{-- Customer --}}
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Customer</span></div>
            <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                @php
                $rows = [
                    ['Name',    $order->customer_name],
                    ['Email',   $order->customer_email],
                    ['Phone',   $order->customer_phone ?? '—'],
                ];
                @endphp
                @foreach($rows as [$label, $value])
                <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);gap:var(--sp-3)">
                    <span style="color:var(--admin-muted);flex-shrink:0">{{ $label }}</span>
                    <span style="font-weight:var(--weight-medium);text-align:right">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Delivery --}}
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Delivery Address</span></div>
            <div class="admin-card-body">
                <div style="font-size:var(--text-sm);line-height:var(--leading-relaxed);color:var(--admin-text)">
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
const orderId = {{ $order->id }};

function saveStatus() {
    const status = document.getElementById('statusSelect').value;
    fetch(`/admin/orders/${orderId}/status`, {
        method:  'PATCH',
        headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': csrfToken() },
        body:    JSON.stringify({ status }),
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { showAdminToast('Status updated to ' + status); }
        else           { showAdminToast('Update failed', 'error'); }
    });
}

function savePayment() {
    const payment_status = document.getElementById('paymentSelect').value;
    fetch(`/admin/orders/${orderId}/payment`, {
        method:  'PATCH',
        headers: { 'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': csrfToken() },
        body:    JSON.stringify({ payment_status }),
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) { showAdminToast('Payment marked as ' + payment_status); }
        else           { showAdminToast('Update failed', 'error'); }
    });
}
</script>
@endpush
