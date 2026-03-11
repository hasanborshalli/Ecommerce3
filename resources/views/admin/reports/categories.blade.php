@extends('admin.layout')

@section('title', 'Category Report')
@section('page_title', 'Category Report')
@section('breadcrumb') Reports › By Category @endsection

@section('content')

<div class="admin-tabs" style="margin-bottom:var(--sp-5)">
    <a href="{{ route('admin.reports.sales') }}" class="admin-tab">Sales</a>
    <a href="{{ route('admin.reports.profit') }}" class="admin-tab">Profit</a>
    <a href="{{ route('admin.reports.products') }}" class="admin-tab">By Product</a>
    <a href="{{ route('admin.reports.categories') }}" class="admin-tab active">By Category</a>
</div>

<div class="admin-card" style="margin-bottom:var(--sp-5)">
    <div class="admin-card-body">
        <form method="GET" action="{{ route('admin.reports.categories') }}"
              style="display:flex;align-items:center;gap:var(--sp-3);flex-wrap:wrap">
            @foreach(['7'=>'7 days','30'=>'30 days','90'=>'90 days'] as $val => $label)
            <a href="{{ route('admin.reports.categories', ['period' => $val]) }}"
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
                <a href="{{ route('admin.reports.export.excel', array_merge(request()->query(), ['type'=>'categories'])) }}" class="abtn abtn-outline abtn-sm">↓ CSV</a>
                <a href="{{ route('admin.reports.export.pdf', array_merge(request()->query(), ['type'=>'categories'])) }}" class="abtn abtn-outline abtn-sm" target="_blank">↓ PDF</a>
            </div>
        </form>
    </div>
</div>

@php $grandTotal = $catData->sum('total_revenue'); @endphp

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Category</th>
                <th>Products</th>
                <th class="text-right">Revenue</th>
                <th>Share</th>
            </tr>
        </thead>
        <tbody>
            @forelse($catData as $i => $cat)
            <tr>
                <td style="color:var(--admin-muted);font-size:var(--text-sm)">{{ $i + 1 }}</td>
                <td>
                    <a href="{{ route('admin.products.index', ['category' => $cat->id]) }}"
                       style="font-weight:var(--weight-medium);color:var(--admin-accent)">
                        {{ $cat->name }}
                    </a>
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">{{ $cat->products->count() }}</td>
                <td class="text-right" style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                    {{ $currencySymbol }}{{ number_format($cat->total_revenue, 2) }}
                </td>
                <td style="min-width:160px">
                    @php $pct = $grandTotal > 0 ? round(($cat->total_revenue / $grandTotal) * 100, 1) : 0; @endphp
                    <div style="display:flex;align-items:center;gap:var(--sp-2)">
                        <div style="flex:1;height:6px;background:var(--admin-border);border-radius:999px;overflow:hidden">
                            <div style="width:{{ $pct }}%;height:100%;background:var(--amber);border-radius:999px"></div>
                        </div>
                        <span style="font-size:var(--text-xs);color:var(--admin-muted);width:36px;text-align:right">{{ $pct }}%</span>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:var(--admin-muted);padding:var(--sp-8)">
                    No sales data in this period.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
