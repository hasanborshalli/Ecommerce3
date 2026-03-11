@extends('admin.layout')

@section('title', 'Products')
@section('page_title', 'Products')
@section('breadcrumb') Catalogue › Products @endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>Products</h1>
        <p>Manage your product catalogue, pricing, and stock.</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.products.create') }}" class="abtn abtn-amber">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Product
        </a>
    </div>
</div>

{{-- Status tabs --}}
<div class="admin-tabs">
    @php $curStatus = request('status', ''); @endphp
    <a href="{{ route('admin.products.index', request()->except(['status','page'])) }}"
       class="admin-tab{{ !$curStatus ? ' active' : '' }}">
        All <span class="admin-tab-count">{{ $counts['all'] }}</span>
    </a>
    <a href="{{ route('admin.products.index', array_merge(request()->except(['status','page']), ['status'=>'active'])) }}"
       class="admin-tab{{ $curStatus==='active' ? ' active' : '' }}">
        Active <span class="admin-tab-count">{{ $counts['active'] }}</span>
    </a>
    <a href="{{ route('admin.products.index', array_merge(request()->except(['status','page']), ['status'=>'low_stock'])) }}"
       class="admin-tab{{ $curStatus==='low_stock' ? ' active' : '' }}">
        Low Stock <span class="admin-tab-count">{{ $counts['low_stock'] }}</span>
    </a>
    <a href="{{ route('admin.products.index', array_merge(request()->except(['status','page']), ['status'=>'out_of_stock'])) }}"
       class="admin-tab{{ $curStatus==='out_of_stock' ? ' active' : '' }}">
        Out of Stock <span class="admin-tab-count">{{ $counts['out'] }}</span>
    </a>
</div>

<div class="admin-table-wrap">

    {{-- Toolbar --}}
    <div class="admin-table-header">
        <form method="GET" action="{{ route('admin.products.index') }}"
              style="display:flex;gap:var(--sp-2);flex:1;flex-wrap:wrap">
            <div class="admin-search" style="max-width:260px">
                <span class="admin-search-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" name="search" class="admin-search-input"
                       value="{{ request('search') }}" placeholder="Search name or SKU…">
            </div>
            <select name="category" class="aform-control" style="height:36px;width:160px;font-size:var(--text-sm)">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
                @endforeach
            </select>
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <button type="submit" class="abtn abtn-outline">Filter</button>
            @if(request()->hasAny(['search','category','status']))
                <a href="{{ route('admin.products.index') }}" class="abtn abtn-ghost">Clear</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width:50px"></th>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Cost</th>
                <th>Margin</th>
                <th>Stock</th>
                <th>Status</th>
                <th style="width:100px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td>
                    @if($product->main_image)
                        <img src="{{ Storage::url($product->main_image) }}"
                             alt="{{ $product->name }}" class="table-product-img">
                    @else
                        <div class="table-product-img" style="display:flex;align-items:center;justify-content:center">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    @endif
                </td>
                <td>
                    <div class="table-product-info" style="display:block">
                        <div class="table-product-name">{{ $product->name }}</div>
                        <div class="table-product-sku">{{ $product->sku ?? '—' }}</div>
                    </div>
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $product->category->name ?? '—' }}
                </td>
                <td style="font-size:var(--text-sm);font-weight:var(--weight-semibold)">
                    {{ $currencySymbol }}{{ number_format($product->effective_price, 2) }}
                    @if($product->is_on_sale)
                        <div style="font-size:10px;color:var(--admin-muted);font-weight:normal;text-decoration:line-through">
                            {{ $currencySymbol }}{{ number_format($product->price, 2) }}
                        </div>
                    @endif
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $currencySymbol }}{{ number_format($product->cost_price, 2) }}
                </td>
                <td>
                    @php $margin = $product->margin_percent; @endphp
                    <span class="{{ $margin >= 40 ? 'profit-positive' : ($margin >= 20 ? '' : 'profit-negative') }}"
                          style="font-size:var(--text-sm)">
                        {{ $margin }}%
                    </span>
                </td>
                <td>
                    <div class="stock-bar-wrap">
                        @php
                            $pct   = $product->stock > 0 ? min(100, ($product->stock / max($product->low_stock_threshold * 3, 30)) * 100) : 0;
                            $level = $product->stock <= 0 ? 'zero' : ($product->is_low_stock ? 'low' : ($pct < 60 ? 'medium' : 'high'));
                        @endphp
                        <div class="stock-bar" style="min-width:50px">
                            <div class="stock-bar-fill {{ $level }}" style="width:{{ $pct }}%"></div>
                        </div>
                        <span style="font-size:var(--text-xs);color:var(--admin-muted);white-space:nowrap">
                            {{ $product->stock }}
                        </span>
                    </div>
                </td>
                <td>
                    @if($product->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-neutral">Inactive</span>
                    @endif
                    @if($product->is_featured)
                        <span class="badge badge-primary" style="margin-top:2px">Featured</span>
                    @endif
                </td>
                <td>
                    <div class="table-actions">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="table-action" title="Edit">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                              onsubmit="return confirm('Delete {{ addslashes($product->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="table-action delete" title="Delete">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:var(--sp-12);color:var(--admin-muted)">
                    No products found.
                    <a href="{{ route('admin.products.create') }}" style="color:var(--admin-accent)">Add your first product</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="admin-table-footer">
        <span>Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}</span>
        {{ $products->links() }}
    </div>

</div>

@endsection
