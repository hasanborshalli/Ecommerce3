@extends('admin.layout')
@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('content')

<div class="admin-stat-grid-5">
    <div class="stat-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div class="stat-card-label">Total Revenue</div>
            <div class="stat-card-icon stat-icon-amber"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23" />
                    <path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6" />
                </svg></div>
        </div>
        <div class="stat-card-value">{{ $currencySymbol }}{{ number_format($totalRevenue, 0) }}</div>
        <div class="stat-card-sub">
            @if($revenueGrowth !== null)
            @if($revenueGrowth >= 0)<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--success)"
                stroke-width="2.5">
                <polyline points="18 15 12 9 6 15" />
            </svg><span style="color:var(--success)">+{{ $revenueGrowth }}%</span>
            @else<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2.5">
                <polyline points="6 9 12 15 18 9" />
            </svg><span style="color:var(--danger)">{{ $revenueGrowth }}%</span>@endif
            vs last month
            @else All time @endif
        </div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div class="stat-card-label">Total Orders</div>
            <div class="stat-card-icon stat-icon-navy"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                </svg></div>
        </div>
        <div class="stat-card-value">{{ number_format($totalOrders) }}</div>
        <div class="stat-card-sub">
            @if($pendingOrders > 0)<span style="color:var(--warning)">{{ $pendingOrders }} pending</span>
            @else<span style="color:var(--success)">All fulfilled</span>@endif
        </div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div class="stat-card-label">Customers</div>
            <div class="stat-card-icon stat-icon-blue"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 00-3-3.87" />
                    <path d="M16 3.13a4 4 0 010 7.75" />
                </svg></div>
        </div>
        <div class="stat-card-value">{{ $totalCustomers }}</div>
        <div class="stat-card-sub">{{ $newCustomersThisMonth }} this month</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div class="stat-card-label">Active Products</div>
            <div class="stat-card-icon stat-icon-warning"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <path d="M16 10a4 4 0 01-8 0" />
                </svg></div>
        </div>
        <div class="stat-card-value">{{ $totalProducts }}</div>
        <div class="stat-card-sub">
            @if($lowStockCount > 0)<span style="color:var(--warning)">{{ $lowStockCount }} low stock</span>
            @else All stocked @endif
        </div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div class="stat-card-label">Pending Reviews</div>
            <div class="stat-card-icon stat-icon-amber"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <polygon
                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg></div>
        </div>
        <div class="stat-card-value">{{ $pendingReviews }}</div>
        <div class="stat-card-sub">
            @if($pendingReviews > 0)<a href="{{ route('admin.reviews.index') }}" style="color:var(--amber)">Review
                now</a>
            @else All moderated @endif
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:var(--sp-5);margin-bottom:var(--sp-5)">
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title">Revenue &amp; Profit — Last 30 Days</span>
            <div style="display:flex;gap:var(--sp-3);font-size:var(--text-xs);align-items:center">
                <span style="display:flex;align-items:center;gap:var(--sp-1)"><span
                        style="width:10px;height:3px;background:var(--amber);border-radius:2px;display:inline-block"></span>
                    Revenue</span>
                <span style="display:flex;align-items:center;gap:var(--sp-1)"><span
                        style="width:10px;height:3px;background:var(--success);border-radius:2px;display:inline-block"></span>
                    Profit</span>
            </div>
        </div>
        <div class="admin-card-body" style="padding:var(--sp-4)">
            <div class="chart-wrap" style="height:260px"><canvas id="salesChart"></canvas></div>
        </div>
    </div>
    <div class="admin-card">
        <div class="admin-card-header"><span class="admin-card-title">Orders by Status</span></div>
        <div class="admin-card-body" style="display:flex;flex-direction:column;align-items:center">
            <div style="height:180px;width:180px;position:relative"><canvas id="statusChart"></canvas></div>
            <div style="width:100%;margin-top:var(--sp-4);display:flex;flex-direction:column;gap:var(--sp-2)">
                @php
                $statusColors =
                ['pending'=>'#D97706','confirmed'=>'#0284C7','processing'=>'#7C3AED','shipped'=>'#2563EB','delivered'=>'#16A34A','cancelled'=>'#DC2626'];
                $statusLabels =
                ['pending'=>'Pending','confirmed'=>'Confirmed','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'];
                @endphp
                @foreach($statusColors as $key => $color)
                @if(($ordersByStatus[$key] ?? 0) > 0)
                <div style="display:flex;align-items:center;justify-content:space-between;font-size:var(--text-xs)">
                    <div style="display:flex;align-items:center;gap:var(--sp-2)">
                        <span
                            style="width:10px;height:10px;border-radius:var(--radius-sm);background:{{ $color }};flex-shrink:0"></span>
                        <span style="color:var(--admin-muted)">{{ $statusLabels[$key] }}</span>
                    </div>
                    <span style="font-weight:var(--weight-semibold);color:var(--admin-text)">{{ $ordersByStatus[$key]
                        }}</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:var(--sp-5)">
    <div class="admin-card">
        <div class="admin-card-header">
            <span class="admin-card-title">Recent Orders</span>
            <a href="{{ route('admin.orders.index') }}" class="abtn abtn-outline abtn-sm">View All</a>
        </div>
        <div class="table-wrap" style="border:none;border-radius:0">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Profit</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}"
                                style="font-weight:var(--weight-semibold);color:var(--admin-accent);font-size:var(--text-xs)">{{
                                $order->order_number }}</a></td>
                        <td style="font-size:var(--text-sm)">{{ $order->customer_name }}</td>
                        <td style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">{{ $currencySymbol }}{{
                            number_format($order->total, 2) }}</td>
                        <td style="font-size:var(--text-sm)">
                            @if($order->status !== 'cancelled')
                            <span class="{{ $order->profit >= 0 ? 'profit-positive' : 'profit-negative' }}">{{
                                $currencySymbol }}{{ number_format($order->profit, 2) }}</span>
                            @else<span style="color:var(--admin-muted)">—</span>@endif
                        </td>
                        <td>@php $b = $order->status_badge; @endphp<span class="badge {{ $b['class'] }}">{{ $b['label']
                                }}</span></td>
                        <td style="font-size:var(--text-xs);color:var(--admin-muted)">{{ $order->created_at->format('M
                            d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;color:var(--admin-muted);padding:var(--sp-8)">No orders
                            yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:var(--sp-5)">
        @if($lowStockProducts->count())
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title"
                    style="color:var(--warning);display:flex;align-items:center;gap:var(--sp-2)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                        <line x1="12" y1="9" x2="12" y2="13" />
                    </svg>
                    Low Stock
                </span>
                <a href="{{ route('admin.stock.index') }}" class="abtn abtn-outline abtn-sm">Manage</a>
            </div>
            <div class="admin-card-body" style="padding:0">
                @foreach($lowStockProducts as $p)
                <div
                    style="display:flex;align-items:center;justify-content:space-between;padding:var(--sp-3) var(--sp-4);border-bottom:1px solid var(--admin-border)">
                    <div style="display:flex;align-items:center;gap:var(--sp-2);min-width:0">
                        @if($p->main_image)<img src="{{ Storage::url($p->main_image) }}" alt=""
                            style="width:32px;height:32px;object-fit:contain;border-radius:var(--radius-sm);border:1px solid var(--admin-border);flex-shrink:0">@endif
                        <span
                            style="font-size:var(--text-xs);font-weight:var(--weight-medium);color:var(--admin-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{
                            $p->name }}</span>
                    </div>
                    <span class="badge badge-{{ $p->stock_status === 'out_of_stock' ? 'out' : 'low-stock' }}"
                        style="flex-shrink:0;margin-left:var(--sp-2)">{{ $p->stock }} left</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($latestPendingReviews->count())
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title" style="display:flex;align-items:center;gap:var(--sp-2)">
                    <span
                        style="width:8px;height:8px;border-radius:999px;background:var(--amber);display:inline-block"></span>
                    Awaiting Review
                </span>
                <a href="{{ route('admin.reviews.index') }}" class="abtn abtn-outline abtn-sm">Moderate</a>
            </div>
            <div class="admin-card-body" style="padding:0">
                @foreach($latestPendingReviews as $review)
                <div style="padding:var(--sp-3) var(--sp-4);border-bottom:1px solid var(--admin-border)">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2px">
                        <span
                            style="font-size:var(--text-xs);font-weight:var(--weight-medium);color:var(--admin-text)">{{
                            $review->customer_name }}</span>
                        <span style="font-size:10px;color:var(--amber);font-weight:var(--weight-semibold)">{{
                            str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                    </div>
                    <div
                        style="font-size:11px;color:var(--admin-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        {{ Str::limit($review->body, 55) }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Top Products</span></div>
            <div class="admin-card-body" style="padding:0">
                @forelse($topProducts as $i => $tp)
                <div
                    style="display:flex;align-items:center;justify-content:space-between;padding:var(--sp-3) var(--sp-4);border-bottom:1px solid var(--admin-border)">
                    <div style="display:flex;align-items:center;gap:var(--sp-3);min-width:0">
                        <span
                            style="width:22px;height:22px;background:{{ $i === 0 ? 'var(--amber)' : 'var(--gray-200)' }};color:{{ $i === 0 ? 'white' : 'var(--gray-600)' }};border-radius:var(--radius-full);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:var(--weight-bold);flex-shrink:0">{{
                            $i + 1 }}</span>
                        <div style="min-width:0">
                            <div
                                style="font-size:var(--text-xs);font-weight:var(--weight-medium);color:var(--admin-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                {{ $tp->product_name }}</div>
                            <div style="font-size:10px;color:var(--admin-muted)">{{ $tp->units_sold }} units</div>
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <div style="font-size:var(--text-xs);font-weight:var(--weight-semibold)">{{ $currencySymbol }}{{
                            number_format($tp->revenue, 0) }}</div>
                        <div style="font-size:10px;color:var(--success)">+{{ $currencySymbol }}{{
                            number_format($tp->profit, 0) }}</div>
                    </div>
                </div>
                @empty
                <div style="padding:var(--sp-6);text-align:center;color:var(--admin-muted);font-size:var(--text-sm)">No
                    sales data yet</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
    const salesCtx = document.getElementById('salesChart')?.getContext('2d');
if (salesCtx) {
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($chartDates),
            datasets: [
                { label: 'Revenue', data: @json($chartRevenue), borderColor: 'rgba(217,119,6,1)', backgroundColor: 'rgba(217,119,6,0.08)', fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 4, borderWidth: 2 },
                { label: 'Profit',  data: @json($chartProfit),  borderColor: 'rgba(22,163,74,1)',  backgroundColor: 'rgba(22,163,74,0.06)', fill: true, tension: 0.4, pointRadius: 0, pointHoverRadius: 4, borderWidth: 2 },
            ],
        },
        options: {
            responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(24,24,27,0.92)', titleColor: 'rgba(255,255,255,0.6)', bodyColor: 'white', padding: 10, callbacks: { label: ctx => ' ' + ctx.dataset.label + ': {{ $currencySymbol }}' + ctx.parsed.y.toFixed(2) } } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94A3B8', font: { size: 10 }, maxTicksLimit: 8 } },
                y: { grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#94A3B8', font: { size: 10 }, callback: v => '{{ $currencySymbol }}' + (v >= 1000 ? (v/1000).toFixed(1)+'k' : v) }, beginAtZero: true },
            },
        },
    });
}
const statusCtx = document.getElementById('statusChart')?.getContext('2d');
if (statusCtx) {
    const statusData = @json(array_values($ordersByStatus));
    const statusLabels = @json(array_keys($ordersByStatus));
    const colorMap = {pending:'#D97706',confirmed:'#0284C7',processing:'#7C3AED',shipped:'#2563EB',delivered:'#16A34A',cancelled:'#DC2626'};
    new Chart(statusCtx, {
        type: 'doughnut',
        data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: statusLabels.map(l => colorMap[l] || '#94A3B8'), borderWidth: 2, borderColor: '#fff', hoverOffset: 4 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false } } },
    });
}
</script>
@endpush