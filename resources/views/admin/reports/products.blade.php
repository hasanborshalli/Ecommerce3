@extends('admin.layout')

@section('title', 'Products Report')
@section('page_title', 'Products Report')
@section('breadcrumb') Reports › By Product @endsection

@section('content')

<div class="admin-tabs" style="margin-bottom:var(--sp-5)">
    <a href="{{ route('admin.reports.sales') }}" class="admin-tab">Sales</a>
    <a href="{{ route('admin.reports.profit') }}" class="admin-tab">Profit</a>
    <a href="{{ route('admin.reports.products') }}" class="admin-tab active">By Product</a>
    <a href="{{ route('admin.reports.categories') }}" class="admin-tab">By Category</a>
</div>

<div class="admin-card" style="margin-bottom:var(--sp-5)">
    <div class="admin-card-body">
        <form method="GET" action="{{ route('admin.reports.products') }}"
              style="display:flex;align-items:center;gap:var(--sp-3);flex-wrap:wrap">
            @foreach(['7'=>'7 days','30'=>'30 days','90'=>'90 days'] as $val => $label)
            <a href="{{ route('admin.reports.products', ['period' => $val]) }}"
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
                <a href="{{ route('admin.reports.export.excel', array_merge(request()->query(), ['type'=>'products'])) }}" class="abtn abtn-outline abtn-sm">↓ CSV</a>
                <a href="{{ route('admin.reports.export.pdf', array_merge(request()->query(), ['type'=>'products'])) }}" class="abtn abtn-outline abtn-sm" target="_blank">↓ PDF</a>
            </div>
        </form>
    </div>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width:36px">#</th>
                <th>Product</th>
                <th class="text-right">Units Sold</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Cost</th>
                <th class="text-right">Profit</th>
                <th class="text-right">Margin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $i => $row)
            <tr>
                <td style="font-size:var(--text-sm);color:var(--admin-muted);font-weight:var(--weight-bold)">
                    {{ ($topProducts->currentPage() - 1) * $topProducts->perPage() + $i + 1 }}
                </td>
                <td style="font-weight:var(--weight-medium);font-size:var(--text-sm)">{{ $row->product_name }}</td>
                <td class="text-right" style="font-size:var(--text-sm)">{{ $row->units_sold }}</td>
                <td class="text-right" style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                    {{ $currencySymbol }}{{ number_format($row->revenue, 2) }}
                </td>
                <td class="text-right" style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $currencySymbol }}{{ number_format($row->cost, 2) }}
                </td>
                <td class="text-right">
                    <span class="{{ $row->profit >= 0 ? 'profit-positive' : 'profit-negative' }}" style="font-size:var(--text-sm)">
                        {{ $currencySymbol }}{{ number_format($row->profit, 2) }}
                    </span>
                </td>
                <td class="text-right" style="font-size:var(--text-sm);color:var(--admin-muted)">
                    @php $m = $row->revenue > 0 ? round(($row->profit / $row->revenue) * 100, 1) : 0; @endphp
                    {{ $m }}%
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;color:var(--admin-muted);padding:var(--sp-8)">
                    No sales data in this period.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="admin-table-footer">
        <span>{{ $topProducts->total() }} products</span>
        {{ $topProducts->links() }}
    </div>
</div>

@endsection
