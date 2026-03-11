@extends('admin.layout')

@section('title', 'Profit Report')
@section('page_title', 'Profit Report')
@section('breadcrumb') Reports › Profit @endsection

@section('content')

<div class="admin-tabs" style="margin-bottom:var(--sp-5)">
    <a href="{{ route('admin.reports.sales') }}" class="admin-tab">Sales</a>
    <a href="{{ route('admin.reports.profit') }}" class="admin-tab active">Profit</a>
    <a href="{{ route('admin.reports.products') }}" class="admin-tab">By Product</a>
    <a href="{{ route('admin.reports.categories') }}" class="admin-tab">By Category</a>
</div>

{{-- Date filter --}}
<div class="admin-card" style="margin-bottom:var(--sp-5)">
    <div class="admin-card-body">
        <form method="GET" action="{{ route('admin.reports.profit') }}"
              style="display:flex;align-items:center;gap:var(--sp-3);flex-wrap:wrap">
            @foreach(['7'=>'7 days','30'=>'30 days','90'=>'90 days'] as $val => $label)
            <a href="{{ route('admin.reports.profit', ['period' => $val]) }}"
               class="abtn abtn-sm {{ $period == $val && !request('from') ? 'abtn-primary' : 'abtn-outline' }}">
                {{ $label }}
            </a>
            @endforeach
            <div style="display:flex;align-items:center;gap:var(--sp-2)">
            <input type="date" name="from" class="aform-control" style="height:36px;width:145px" value="{{ $from }}">
            <span style="color:var(--admin-muted);font-size:var(--text-sm)">to</span>
            <input type="date" name="to" class="aform-control" style="height:36px;width:145px" value="{{ $to }}">
            <button type="submit" class="abtn abtn-outline abtn-sm">Apply</button>
            </div>
            <div style="margin-left:auto;display:flex;gap:var(--sp-2)">
                <a href="{{ route('admin.reports.export.excel', array_merge(request()->query(), ['type'=>'profit'])) }}" class="abtn abtn-outline abtn-sm">↓ CSV</a>
                <a href="{{ route('admin.reports.export.pdf', array_merge(request()->query(), ['type'=>'profit'])) }}" class="abtn abtn-outline abtn-sm" target="_blank">↓ PDF</a>
            </div>
        </form>
    </div>
</div>

{{-- Summary --}}
@if($totals)
<div class="report-summary-grid" style="margin-bottom:var(--sp-5)">
    @foreach([
        ['Revenue', $currencySymbol . number_format($totals->revenue, 2),  null],
        ['Cost',    $currencySymbol . number_format($totals->cost, 2),     'var(--danger)'],
        ['Profit',  $currencySymbol . number_format($totals->profit, 2),   'var(--success)'],
        ['Margin',  ($totals->revenue > 0 ? round(($totals->profit / $totals->revenue) * 100, 1) : 0) . '%', null],
    ] as [$label, $val, $color])
    <div class="stat-card">
        <div class="stat-card-label">{{ $label }}</div>
        <div class="stat-card-value" @if($color) style="color:{{ $color }}" @endif>{{ $val }}</div>
    </div>
    @endforeach
</div>
@endif

{{-- Order-by-order profit table --}}
<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Date</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Cost</th>
                <th class="text-right">Profit</th>
                <th class="text-right">Margin</th>
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
                <td style="font-size:var(--text-sm)">{{ $order->customer_name }}</td>
                <td style="font-size:var(--text-xs);color:var(--admin-muted)">{{ $order->created_at->format('M d, Y') }}</td>
                <td class="text-right" style="font-size:var(--text-sm)">
                    {{ $currencySymbol }}{{ number_format($order->total, 2) }}
                </td>
                <td class="text-right" style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $currencySymbol }}{{ number_format($order->cost_total, 2) }}
                </td>
                <td class="text-right">
                    <span class="{{ $order->profit >= 0 ? 'profit-positive' : 'profit-negative' }}" style="font-size:var(--text-sm)">
                        {{ $currencySymbol }}{{ number_format($order->profit, 2) }}
                    </span>
                </td>
                <td class="text-right" style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $order->profit_margin }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:var(--admin-muted);padding:var(--sp-8)">
                    No orders in this period.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="admin-table-footer">
        <span>{{ $orders->total() }} orders</span>
        {{ $orders->links() }}
    </div>
</div>

@endsection
