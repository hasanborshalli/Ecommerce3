@extends('admin.layout')

@section('title', 'Stock Management')
@section('page_title', 'Stock Management')
@section('breadcrumb') Inventory › Stock @endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>Stock Management</h1>
        <p>Monitor inventory levels and make manual adjustments.</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.purchase_orders.create') }}" class="abtn abtn-amber">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Purchase Order
        </a>
    </div>
</div>

{{-- Summary strip --}}
<div class="stock-summary-grid" style="margin-bottom:var(--sp-5)">
    <div class="stat-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div class="stat-card-label">Total Products</div>
            <div class="stat-card-icon stat-icon-navy">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
            </div>
        </div>
        <div class="stat-card-value">{{ $products->total() }}</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div class="stat-card-label">Low Stock</div>
            <div class="stat-card-icon stat-icon-warning">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/></svg>
            </div>
        </div>
        <div class="stat-card-value" style="color:var(--warning)">{{ $lowCount }}</div>
        <div class="stat-card-sub">Below threshold</div>
    </div>
    <div class="stat-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between">
            <div class="stat-card-label">Out of Stock</div>
            <div class="stat-card-icon" style="background:var(--danger-bg);color:var(--danger)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
        </div>
        <div class="stat-card-value" style="color:var(--danger)">{{ $outCount }}</div>
        <div class="stat-card-sub">Need restocking</div>
    </div>
</div>

{{-- Filter tabs --}}
<div class="admin-tabs">
    <a href="{{ route('admin.stock.index') }}" class="admin-tab{{ !request('filter') ? ' active' : '' }}">All</a>
    <a href="{{ route('admin.stock.index', ['filter' => 'low']) }}" class="admin-tab{{ request('filter')==='low' ? ' active' : '' }}">
        Low Stock <span class="admin-tab-count">{{ $lowCount }}</span>
    </a>
    <a href="{{ route('admin.stock.index', ['filter' => 'out']) }}" class="admin-tab{{ request('filter')==='out' ? ' active' : '' }}">
        Out of Stock <span class="admin-tab-count">{{ $outCount }}</span>
    </a>
</div>

<div class="admin-table-wrap">
    {{-- Search --}}
    <div class="admin-table-header">
        <form method="GET" action="{{ route('admin.stock.index') }}" style="display:flex;gap:var(--sp-2);flex:1">
            <div class="admin-search" style="max-width:280px">
                <span class="admin-search-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="search" class="admin-search-input"
                       value="{{ request('search') }}" placeholder="Search products…">
            </div>
            @if(request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif
            <button type="submit" class="abtn abtn-outline">Search</button>
            @if(request()->hasAny(['search','filter']))
                <a href="{{ route('admin.stock.index') }}" class="abtn abtn-ghost">Clear</a>
            @endif
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th style="width:50px"></th>
                <th>Product</th>
                <th>Category</th>
                <th>SKU</th>
                <th>Cost</th>
                <th>Stock Level</th>
                <th>Threshold</th>
                <th style="width:120px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    @if($product->main_image)
                        <img src="{{ Storage::url($product->main_image) }}"
                             style="width:40px;height:40px;object-fit:contain;border-radius:var(--radius-sm);border:1px solid var(--admin-border)">
                    @else
                        <div style="width:40px;height:40px;background:var(--admin-bg);border-radius:var(--radius-sm);border:1px solid var(--admin-border)"></div>
                    @endif
                </td>
                <td>
                    <div class="table-product-name">{{ $product->name }}</div>
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $product->category->name ?? '—' }}
                </td>
                <td style="font-size:var(--text-xs);color:var(--admin-muted);font-family:var(--font-mono)">
                    {{ $product->sku ?? '—' }}
                </td>
                <td style="font-size:var(--text-sm)">
                    {{ $currencySymbol }}{{ number_format($product->cost_price, 2) }}
                </td>
                <td>
                    <div class="stock-bar-wrap">
                        @php
                            $max  = max($product->low_stock_threshold * 3, 30);
                            $pct  = $product->stock > 0 ? min(100, ($product->stock / $max) * 100) : 0;
                            $lvl  = $product->stock <= 0 ? 'zero' : ($product->is_low_stock ? 'low' : ($pct < 60 ? 'medium' : 'high'));
                        @endphp
                        <div class="stock-bar" style="min-width:80px">
                            <div class="stock-bar-fill {{ $lvl }}" style="width:{{ $pct }}%"></div>
                        </div>
                        <span style="font-size:var(--text-sm);font-weight:var(--weight-semibold);
                              color:{{ $product->stock <= 0 ? 'var(--danger)' : ($product->is_low_stock ? 'var(--warning)' : 'var(--admin-text)') }}">
                            {{ $product->stock }}
                        </span>
                    </div>
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $product->low_stock_threshold }}
                </td>
                <td>
                    <div class="table-actions">
                        <button type="button"
                                class="abtn abtn-outline abtn-sm"
                                data-url="{{ route('admin.stock.adjust', $product) }}"
                                onclick="openAdjust(this.dataset.url, '{{ addslashes($product->name) }}', {{ $product->stock }})"
                                title="Adjust stock">
                            ±
                        </button>
                        <a href="{{ route('admin.stock.history', $product) }}"
                           class="table-action" title="View history">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:var(--sp-10);color:var(--admin-muted)">No products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="admin-table-footer">
        <span>{{ $products->total() }} products</span>
        {{ $products->links() }}
    </div>
</div>

{{-- ── Adjust modal ──────────────────────────────────────── --}}
<div id="adjustModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center">
    <div style="background:white;border-radius:var(--radius-xl);width:400px;max-width:90vw;overflow:hidden;box-shadow:var(--shadow-xl)">
        <div style="padding:var(--sp-5) var(--sp-6);border-bottom:1px solid var(--admin-border);display:flex;align-items:center;justify-content:space-between">
            <h3 style="font-size:var(--text-lg);font-weight:var(--weight-bold)">Adjust Stock</h3>
            <button onclick="closeAdjust()" style="background:none;border:none;cursor:pointer;color:var(--admin-muted);font-size:20px;line-height:1">×</button>
        </div>
        <form id="adjustForm" method="POST">
            @csrf
            <div style="padding:var(--sp-6);display:flex;flex-direction:column;gap:var(--sp-4)">
                <div style="background:var(--admin-bg);border-radius:var(--radius);padding:var(--sp-3) var(--sp-4);font-size:var(--text-sm)">
                    <strong id="adjustProductName"></strong>
                    <div style="color:var(--admin-muted);margin-top:2px">Current stock: <strong id="adjustCurrentStock"></strong></div>
                </div>
                <div class="aform-group">
                    <label class="aform-label">Adjustment</label>
                    <input type="number" name="adjustment" class="aform-control"
                           placeholder="e.g. +10 to add, -5 to remove" required>
                    <span class="aform-hint">Positive adds stock, negative removes stock.</span>
                </div>
                <div class="aform-group">
                    <label class="aform-label">Reason <span class="req">*</span></label>
                    <input type="text" name="reason" class="aform-control"
                           placeholder="e.g. Found in warehouse, Damaged units removed" required>
                </div>
            </div>
            <div style="padding:var(--sp-4) var(--sp-6);border-top:1px solid var(--admin-border);display:flex;gap:var(--sp-2);justify-content:flex-end">
                <button type="button" onclick="closeAdjust()" class="abtn abtn-outline">Cancel</button>
                <button type="submit" class="abtn abtn-amber">Apply Adjustment</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openAdjust(url, name, stock) {
    document.getElementById('adjustProductName').textContent = name;
    document.getElementById('adjustCurrentStock').textContent = stock;
    document.getElementById('adjustForm').action = url;
    const modal = document.getElementById('adjustModal');
    modal.style.display = 'flex';
    modal.querySelector('input[name="adjustment"]').value = '';
    modal.querySelector('input[name="reason"]').value = '';
    modal.querySelector('input[name="adjustment"]').focus();
}
function closeAdjust() {
    document.getElementById('adjustModal').style.display = 'none';
}
document.getElementById('adjustModal').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeAdjust();
});
</script>
@endpush
