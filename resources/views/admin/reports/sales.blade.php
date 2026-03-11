@extends('admin.layout')

@section('title', 'Sales Report')
@section('page_title', 'Sales Report')
@section('breadcrumb') Reports › Sales @endsection

@section('content')

{{-- Report nav --}}
<div class="admin-tabs" style="margin-bottom:var(--sp-5)">
    <a href="{{ route('admin.reports.sales') }}" class="admin-tab active">Sales</a>
    <a href="{{ route('admin.reports.profit') }}" class="admin-tab">Profit</a>
    <a href="{{ route('admin.reports.products') }}" class="admin-tab">By Product</a>
    <a href="{{ route('admin.reports.categories') }}" class="admin-tab">By Category</a>
</div>

{{-- Date range filter --}}
<div class="admin-card" style="margin-bottom:var(--sp-5)">
    <div class="admin-card-body">
        <form method="GET" action="{{ route('admin.reports.sales') }}"
              style="display:flex;align-items:center;gap:var(--sp-3);flex-wrap:wrap">
            <div style="display:flex;gap:var(--sp-2);align-items:center">
                @foreach(['7'=>'7 days','30'=>'30 days','90'=>'90 days'] as $val => $label)
                <a href="{{ route('admin.reports.sales', ['period' => $val]) }}"
                   class="abtn abtn-sm {{ $period == $val && !request('from') ? 'abtn-primary' : 'abtn-outline' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
            <div style="display:flex;align-items:center;gap:var(--sp-2)">
                <input type="date" name="from" class="aform-control" style="height:36px;width:145px"
                       value="{{ $from }}">
                <span style="color:var(--admin-muted);font-size:var(--text-sm)">to</span>
                <input type="date" name="to" class="aform-control" style="height:36px;width:145px"
                       value="{{ $to }}">
                <button type="submit" class="abtn abtn-outline abtn-sm">Apply</button>
            </div>
            <div style="margin-left:auto;display:flex;gap:var(--sp-2)">
                <a href="{{ route('admin.reports.export.excel', array_merge(request()->query(), ['type'=>'sales'])) }}" class="abtn abtn-outline abtn-sm">
                    ↓ Excel
                </a>
                <a href="{{ route('admin.reports.export.pdf', array_merge(request()->query(), ['type'=>'sales'])) }}" class="abtn abtn-outline abtn-sm">
                    ↓ PDF
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Summary cards --}}
<div class="report-summary-grid">
    @php
    $kpis = [
        ['Revenue',   $currencySymbol . number_format($summary['revenue'], 2),  'stat-icon-amber',    '#2563EB'],
        ['Orders',    number_format($summary['orders']),                         'stat-icon-navy',    null],
        ['Profit',    $currencySymbol . number_format($summary['profit'], 2),   'stat-icon-green',   '#16A34A'],
        ['Avg Order', $currencySymbol . number_format($summary['avg_order'], 2), 'stat-icon-warning', null],
    ];
    @endphp
    @foreach($kpis as [$label, $val, $iconClass, $valColor])
    <div class="stat-card">
        <div class="stat-card-label">{{ $label }}</div>
        <div class="stat-card-value" @if($valColor) style="color:{{ $valColor }}" @endif>{{ $val }}</div>
    </div>
    @endforeach
</div>

{{-- Chart --}}
<div class="admin-card" style="margin-bottom:var(--sp-5)">
    <div class="admin-card-header">
        <span class="admin-card-title">Daily Revenue · {{ \Carbon\Carbon::parse($from)->format('M d') }} – {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</span>
    </div>
    <div class="admin-card-body" style="padding:var(--sp-4)">
        <div class="chart-wrap" style="height:260px">
            <canvas id="salesReportChart"></canvas>
        </div>
    </div>
</div>

{{-- Daily breakdown table --}}
<div class="admin-table-wrap">
    <div class="admin-table-header">
        <span style="font-weight:var(--weight-semibold);color:var(--admin-text)">Daily Breakdown</span>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-right">Orders</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Profit</th>
                <th class="text-right">Margin</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daily as $row)
            <tr>
                <td style="font-size:var(--text-sm)">{{ \Carbon\Carbon::parse($row->date)->format('M d, Y') }}</td>
                <td class="text-right" style="font-size:var(--text-sm)">{{ $row->orders }}</td>
                <td class="text-right" style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                    {{ $currencySymbol }}{{ number_format($row->revenue, 2) }}
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
                <td colspan="5" style="text-align:center;color:var(--admin-muted);padding:var(--sp-8)">
                    No sales in this period.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($daily->count())
        <tfoot>
            <tr>
                <td style="font-weight:var(--weight-bold)">Total</td>
                <td class="text-right" style="font-weight:var(--weight-bold)">{{ $summary['orders'] }}</td>
                <td class="text-right" style="font-weight:var(--weight-bold)">{{ $currencySymbol }}{{ number_format($summary['revenue'], 2) }}</td>
                <td class="text-right">
                    <span class="{{ $summary['profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        {{ $currencySymbol }}{{ number_format($summary['profit'], 2) }}
                    </span>
                </td>
                <td class="text-right">
                    @php $tm = $summary['revenue'] > 0 ? round(($summary['profit'] / $summary['revenue']) * 100, 1) : 0; @endphp
                    {{ $tm }}%
                </td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>

@endsection

@push('styles')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
const ctx = document.getElementById('salesReportChart')?.getContext('2d');
if (ctx) {
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($daily->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray()),
            datasets: [
                {
                    label:           'Revenue',
                    data:            @json($daily->pluck('revenue')->map(fn($v) => round((float)$v, 2))->toArray()),
                    backgroundColor: 'rgba(37,99,235,0.7)',
                    borderRadius:    4,
                    order:           1,
                },
                {
                    label:           'Profit',
                    data:            @json($daily->pluck('profit')->map(fn($v) => round((float)$v, 2))->toArray()),
                    backgroundColor: 'rgba(22,163,74,0.7)',
                    borderRadius:    4,
                    order:           2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { font: { size: 12 }, boxWidth: 12 } },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.92)',
                    bodyColor: 'white',
                    callbacks: { label: ctx => ' ' + ctx.dataset.label + ': {{ $currencySymbol }}' + ctx.parsed.y.toFixed(2) },
                },
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 }, maxTicksLimit: 10 } },
                y: {
                    ticks: {
                        color: '#94A3B8', font: { size: 10 },
                        callback: v => '{{ $currencySymbol }}' + (v >= 1000 ? (v/1000).toFixed(1)+'k' : v),
                    },
                    beginAtZero: true,
                },
            },
        },
    });
}
</script>
@endpush
