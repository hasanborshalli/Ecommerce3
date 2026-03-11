@extends('layouts.app')
@section('title', ($category ? $category->name . ' — ' : '') . 'Shop — ' . ($siteName ?? 'Luma'))
@section('meta_description', $category ? $category->description : 'Browse our full collection of thoughtfully made
goods.')

@section('content')
<div class="shop-page">
    <div class="container">
        <div class="shop-layout">

            {{-- ── Sidebar ─────────────────────────────────────── --}}
            <aside class="shop-sidebar">

                <div class="sidebar-section">
                    <div class="sidebar-title">Categories</div>
                    <a href="{{ route('shop', array_filter(['sort' => request('sort')])) }}"
                        class="sidebar-link{{ !request('category') && !in_array(request('filter'),['new','sale']) ? ' active' : '' }}">
                        All Products
                        <span class="count">{{ $products->total() }}</span>
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('shop', array_filter(['category' => $cat->slug, 'sort' => request('sort')])) }}"
                        class="sidebar-link{{ request('category') === $cat->slug ? ' active' : '' }}">
                        {{ $cat->name }}
                        <span class="count">{{ $cat->products_count ?? '' }}</span>
                    </a>
                    @endforeach
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-title">Filter</div>
                    <a href="{{ route('shop', array_filter(['filter' => 'new', 'sort' => request('sort')])) }}"
                        class="sidebar-link{{ request('filter') === 'new' ? ' active' : '' }}">
                        New Arrivals
                    </a>
                    <a href="{{ route('shop', array_filter(['filter' => 'sale', 'sort' => request('sort')])) }}"
                        class="sidebar-link{{ request('filter') === 'sale' ? ' active' : '' }}"
                        style="{{ request('filter') === 'sale' ? '' : 'color:var(--amber)' }}">
                        Sale Items
                    </a>
                </div>

                <div class="sidebar-section">
                    <div class="sidebar-title">Sort By</div>
                    @php
                    $sortBase = request()->except('sort');
                    $sorts = [
                    '' => 'Featured',
                    'newest' => 'Newest',
                    'price_asc' => 'Price: Low → High',
                    'price_desc' => 'Price: High → Low',
                    'top_rated' => 'Top Rated',
                    'name' => 'Name A–Z',
                    ];
                    @endphp
                    @foreach($sorts as $val => $label)
                    <a href="{{ route('shop', array_merge($sortBase, $val ? ['sort' => $val] : [])) }}"
                        class="sidebar-link{{ request('sort', '') === $val ? ' active' : '' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>

            </aside>

            {{-- ── Main content ─────────────────────────────── --}}
            <div>
                <div class="shop-header">
                    <div>
                        <h1 class="shop-title">
                            @if($category) {{ $category->name }}
                            @elseif(request('filter') === 'new') New Arrivals
                            @elseif(request('filter') === 'sale') Sale
                            @else The Collection
                            @endif
                        </h1>
                        <span class="shop-count">{{ $products->total() }} {{ Str::plural('item', $products->total())
                            }}</span>
                    </div>
                    <div class="shop-sort">
                        <span>Sort:</span>
                        <select onchange="location.href=this.value">
                            @foreach($sorts as $val => $label)
                            <option value="{{ route('shop', array_merge($sortBase, $val ? ['sort' => $val] : [])) }}" {{
                                request('sort', '' )===$val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($products->count())
                <div class="products-grid">
                    @foreach($products as $product)
                    @include('partials.product-card')
                    @endforeach
                </div>
                <div class="pagination-wrap">{{ $products->links() }}</div>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.25">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <path d="M16 10a4 4 0 01-8 0" />
                        </svg>
                    </div>
                    <h3>Nothing here yet</h3>
                    <p>No products match your current filters.</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary">Browse All</a>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection