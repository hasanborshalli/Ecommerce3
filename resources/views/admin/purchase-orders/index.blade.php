@extends('admin.layout')

@section('title', 'Purchase Orders')
@section('page_title', 'Purchase Orders')
@section('breadcrumb') Inventory › Purchase Orders @endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>Purchase Orders</h1>
        <p>Track incoming stock from suppliers.</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.purchase_orders.create') }}" class="abtn abtn-amber">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Purchase Order
        </a>
    </div>
</div>

{{-- Status tabs --}}
<div class="admin-tabs">
    @php $curStatus = request('status', 'all'); @endphp
    @foreach([
        'all'      => 'All ('       . $counts['all']      . ')',
        'draft'    => 'Drafts ('    . $counts['draft']    . ')',
        'ordered'  => 'Ordered ('   . $counts['ordered']  . ')',
        'received' => 'Received ('  . $counts['received'] . ')',
    ] as $key => $label)
    <a href="{{ route('admin.purchase_orders.index', array_merge(request()->except(['status','page']), ['status'=>$key])) }}"
       class="admin-tab{{ $curStatus === $key ? ' active' : '' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="admin-table-wrap">

    {{-- Search / filter bar --}}
    <div class="admin-table-header">
        <form method="GET" action="{{ route('admin.purchase_orders.index') }}"
              style="display:flex;gap:var(--sp-2);flex:1;flex-wrap:wrap">
            <div class="admin-search" style="max-width:240px">
                <span class="admin-search-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="search" class="admin-search-input"
                       value="{{ request('search') }}" placeholder="Search PO number…">
            </div>
            <select name="supplier" class="aform-control" style="height:36px;width:160px;font-size:var(--text-sm)">
                <option value="">All suppliers</option>
                @foreach($suppliers as $s)
                <option value="{{ $s->id }}" {{ request('supplier') == $s->id ? 'selected' : '' }}>
                    {{ $s->name }}
                </option>
                @endforeach
            </select>
            @if(request('status') && request('status') !== 'all')
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <button type="submit" class="abtn abtn-outline">Filter</button>
            @if(request()->hasAny(['search','supplier']))
                <a href="{{ route('admin.purchase_orders.index', request()->only('status')) }}" class="abtn abtn-ghost">Clear</a>
            @endif
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Items</th>
                <th class="text-right">Total Cost</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Expected</th>
                <th style="width:90px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders as $po)
            <tr>
                <td>
                    <a href="{{ route('admin.purchase_orders.show', $po) }}"
                       style="font-weight:var(--weight-semibold);color:var(--admin-accent);font-size:var(--text-sm);font-family:var(--font-mono)">
                        {{ $po->reference_number }}
                    </a>
                </td>
                <td style="font-size:var(--text-sm)">{{ $po->supplier->name ?? '—' }}</td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $po->items_count }} {{ Str::plural('item', $po->items_count) }}
                </td>
                <td class="text-right" style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                    {{ $currencySymbol }}{{ number_format($po->total_cost, 2) }}
                </td>
                <td>
                    @php $b = $po->status_badge; @endphp
                    <span class="badge {{ $b['class'] }}">{{ $b['label'] }}</span>
                </td>
                <td style="font-size:var(--text-xs);color:var(--admin-muted)">
                    {{ $po->order_date->format('M d, Y') }}
                </td>
                <td style="font-size:var(--text-xs);color:var(--admin-muted)">
                    {{ $po->expected_date ? $po->expected_date->format('M d, Y') : '—' }}
                </td>
                <td>
                    <div class="table-actions">
                        <a href="{{ route('admin.purchase_orders.show', $po) }}"
                           class="table-action" title="View">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        @if(in_array($po->status, ['draft','ordered']))
                        <a href="{{ route('admin.purchase_orders.edit', $po) }}"
                           class="table-action" title="Edit">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:var(--sp-12);color:var(--admin-muted)">
                    No purchase orders found.
                    <a href="{{ route('admin.purchase_orders.create') }}" style="color:var(--admin-accent)">Create the first one</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="admin-table-footer">
        <span>{{ $purchaseOrders->total() }} {{ Str::plural('order', $purchaseOrders->total()) }}</span>
        {{ $purchaseOrders->links() }}
    </div>
</div>

@endsection
