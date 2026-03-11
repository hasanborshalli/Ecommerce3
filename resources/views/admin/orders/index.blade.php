@extends('admin.layout')

@section('title', 'Orders')
@section('page_title', 'Orders')
@section('breadcrumb') Sales › Orders @endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>Orders</h1>
        <p>Manage and fulfil customer orders.</p>
    </div>
    <div class="admin-page-actions">
        @if($counts['pending'] > 0)
        <span class="badge badge-warning" style="font-size:var(--text-sm);padding:6px 12px">
            {{ $counts['pending'] }} pending
        </span>
        @endif
    </div>
</div>

{{-- Status tabs --}}
<div class="admin-tabs">
    @php $cur = request('status', 'all'); @endphp
    @foreach([
    'all' => 'All (' . $counts['all'] . ')',
    'pending' => 'Pending (' . $counts['pending'] . ')',
    'confirmed' => 'Confirmed (' . $counts['confirmed'] . ')',
    'processing' => 'Processing (' . $counts['processing'] . ')',
    'shipped' => 'Shipped (' . $counts['shipped'] . ')',
    'delivered' => 'Delivered (' . $counts['delivered'] . ')',
    'cancelled' => 'Cancelled (' . $counts['cancelled'] . ')',
    ] as $key => $label)
    <a href="{{ route('admin.orders.index', array_merge(request()->except(['status','page']), ['status' => $key])) }}"
        class="admin-tab{{ $cur === $key ? ' active' : '' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="admin-table-wrap">

    {{-- Search bar --}}
    <div class="admin-table-header">
        <form method="GET" action="{{ route('admin.orders.index') }}"
            style="display:flex;gap:var(--sp-2);flex:1;flex-wrap:wrap">
            <div class="admin-search" style="max-width:300px">
                <span class="admin-search-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                </span>
                <input type="text" name="search" class="admin-search-input" value="{{ request('search') }}"
                    placeholder="Order #, name, or email…">
            </div>
            <select name="payment" class="aform-control" style="height:36px;width:150px;font-size:var(--text-sm)">
                <option value="all" {{ request('payment','all')==='all' ? 'selected' :'' }}>All payments</option>
                <option value="unpaid" {{ request('payment')==='unpaid' ? 'selected' :'' }}>Unpaid</option>
                <option value="paid" {{ request('payment')==='paid' ? 'selected' :'' }}>Paid</option>
            </select>
            @if(request('status') && request('status') !== 'all')
            <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <button type="submit" class="abtn abtn-outline">Filter</button>
            @if(request()->hasAny(['search','payment']))
            <a href="{{ route('admin.orders.index', request()->only('status')) }}" class="abtn abtn-ghost">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th class="col-date">Date</th>
                <th style="width:80px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}"
                        style="font-weight:var(--weight-semibold);color:var(--admin-accent);font-size:var(--text-sm)">
                        {{ $order->order_number }}
                    </a>
                </td>
                <td>
                    <div style="font-size:var(--text-sm);font-weight:var(--weight-medium)">{{ $order->customer_name }}
                    </div>
                    <div style="font-size:var(--text-xs);color:var(--admin-muted)">{{ $order->customer_email }}</div>
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $order->items->sum('quantity') }} {{ Str::plural('item', $order->items->sum('quantity')) }}
                </td>
                <td style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                    {{ $currencySymbol }}{{ number_format($order->total, 2) }}
                </td>

                <td>
                    @php $pb = $order->payment_badge; @endphp
                    <span class="badge {{ $pb['class'] }}">{{ $pb['label'] }}</span>
                </td>
                <td>
                    {{-- Inline status dropdown --}}
                    <select class="aform-control"
                        style="height:30px;font-size:var(--text-xs);width:120px;padding:0 var(--sp-2)"
                        onchange="updateOrderStatus({{ $order->id }}, this.value, this)">
                        @foreach(['pending','confirmed','processing','shipped','delivered','cancelled'] as $st)
                        <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>
                            {{ ucfirst($st) }}
                        </option>
                        @endforeach
                    </select>
                </td>
                <td class="col-date" style="font-size:var(--text-xs);color:var(--admin-muted);white-space:nowrap">
                    {{ $order->created_at->format('M d, Y') }}<br>
                    <span style="opacity:0.7">{{ $order->created_at->format('H:i') }}</span>
                </td>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}" class="table-action" title="View order">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:var(--sp-12);color:var(--admin-muted)">
                    No orders found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="admin-table-footer">
        <span>{{ $orders->total() }} {{ Str::plural('order', $orders->total()) }}</span>
        {{ $orders->links() }}
    </div>

</div>

@endsection

@push('scripts')
<script>
    function updateOrderStatus(orderId, status, selectEl) {
    const original = selectEl.dataset.original || selectEl.value;
    selectEl.dataset.original = selectEl.querySelector('[selected]')?.value || status;
    selectEl.disabled = true;

    fetch(`/admin/orders/${orderId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept':       'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({ status }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) showAdminToast('Status updated to ' + status);
        else showAdminToast('Update failed', 'error');
    })
    .catch(() => showAdminToast('Update failed', 'error'))
    .finally(() => { selectEl.disabled = false; });
}
</script>
@endpush