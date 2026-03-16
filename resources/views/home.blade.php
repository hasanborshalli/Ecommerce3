@extends('layouts.app')
@section('title', ($siteName ?? 'Luma') . ' — Warm Minimal Living')

@section('content')

{{-- ── 1. HERO: dark panel left + full-bleed image right ── --}}
<section class="hero-editorial">
    <div class="hero-editorial-text">
        <div class="hero-eyebrow">New Collection</div>
        <h1 class="hero-headline">
            Made for<br>
            the quiet<br>
            <em>moments</em>
        </h1>
        <p class="hero-subline">
            Objects crafted with intention — for the home, the table, and the everyday.
        </p>
        <div class="hero-actions">
            <a href="{{ route('shop') }}" class="btn btn-amber btn-lg">Shop Now</a>
            @if($onSale->count())
            <a href="{{ route('shop', ['filter' => 'sale']) }}" class="btn btn-lg"
                style="background:rgba(255,255,255,0.1);color:white;border:1px solid rgba(255,255,255,0.25)">Sale
                Items</a>
            @endif
        </div>
    </div>
    <div class="hero-editorial-image">
        @if($heroSlides->first()?->image)
        <img src="{{ Storage::url($heroSlides->first()->image) }}" alt="{{ $siteName ?? 'Luma' }}">
        @else
        <div class="hero-editorial-image-placeholder">
            <img src="/img/hero.webp" />
        </div>
        @endif
    </div>
</section>

{{-- ── 2. CATEGORIES: asymmetric grid (1 tall + 2 stacked) ── --}}
@if($categories->count())
<section class="section-md" style="background:white">
    <div class="container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--sp-8)">
            <div class="section-header" style="margin-bottom:0">
                <span class="section-eyebrow">Browse</span>
                <h2 class="section-title">Shop by Category</h2>
            </div>
            <a href="{{ route('shop') }}"
                style="font-size:var(--text-sm);color:var(--text-muted);white-space:nowrap;margin-bottom:var(--sp-1)">
                View all →
            </a>
        </div>

        <div class="category-tiles-asym" style="max-height:580px">
            {{-- Left: tall main tile --}}
            <div class="cat-main" style="height:580px">
                @if($categories->first())
                @php $mainCat = $categories->first(); @endphp
                <a href="{{ route('shop', ['category' => $mainCat->slug]) }}" class="category-tile"
                    style="height:100%;border-radius:var(--radius-md);display:block">
                    @if($mainCat->image)
                    <img src="{{ Storage::url($mainCat->image) }}" alt="{{ $mainCat->name }}" loading="lazy"
                        style="width:100%;height:100%;object-fit:cover">
                    @else
                    <div style="width:100%;height:100%;background:var(--warm-cream)"></div>
                    @endif
                    <div class="category-tile-overlay"></div>
                    <div class="category-tile-text">
                        <div class="category-tile-name">{{ $mainCat->name }}</div>
                        <div class="category-tile-count">{{ $mainCat->products_count ?? '' }}</div>
                    </div>
                </a>
                @endif
            </div>

            {{-- Right: 2 stacked tiles --}}
            <div style="display:flex;flex-direction:column;gap:var(--sp-3);height:580px">
                @foreach($categories->skip(1)->take(2) as $cat)
                <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="category-tile"
                    style="flex:1;border-radius:var(--radius-md);min-height:0">
                    @if($cat->image)
                    <img src="{{ Storage::url($cat->image) }}" alt="{{ $cat->name }}" loading="lazy"
                        style="width:100%;height:100%;object-fit:cover">
                    @else
                    <div style="width:100%;height:100%;background:var(--warm-cream)"></div>
                    @endif
                    <div class="category-tile-overlay"></div>
                    <div class="category-tile-text">
                        <div class="category-tile-name">{{ $cat->name }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── 3. FEATURED: asymmetric — 1 large left + 3 products right ── --}}
@if($featured->count())
<section class="section-md featured" style="background:var(--warm-cream)">
    <div class="container">
        <div class="section-header" style="margin-bottom:var(--sp-8)">
            <span class="section-eyebrow">Curated</span>
            <h2 class="section-title">Featured Pieces</h2>
        </div>

        <div class="feature-asym">
            {{-- Large left tile --}}
            @if($featured->first())
            @php $hero = $featured->first(); $isOnSale = $hero->is_on_sale && $hero->sale_price; @endphp
            <div class="feature-asym-main">
                @if($hero->main_image)
                <img src="{{ Storage::url($hero->main_image) }}" alt="{{ $hero->name }}" loading="lazy">
                @else
                <div style="aspect-ratio:3/4;background:var(--warm-stone)"></div>
                @endif
                <div class="feature-asym-overlay">
                    <div class="feature-asym-info">
                        <div class="cat-tag">{{ $hero->category->name ?? 'Featured' }}</div>
                        <div class="prod-name">{{ $hero->name }}</div>
                        <div class="prod-price">
                            @if($isOnSale)
                            {{ $currencySymbol }}{{ number_format($hero->sale_price, 2) }}
                            @else
                            {{ $currencySymbol }}{{ number_format($hero->price, 2) }}
                            @endif
                        </div>
                        <a href="{{ route('product.show', $hero->slug) }}" class="btn btn-amber btn-sm"
                            style="margin-top:var(--sp-3)">Shop Now</a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Right: compact horizontal mini-cards --}}
            <div style="display:flex;flex-direction:column;gap:var(--sp-3)">
                @foreach($featured->skip(1)->take(3) as $product)
                @php $isOnSale = $product->is_on_sale && $product->sale_price; @endphp
                <a href="{{ route('product.show', $product->slug) }}"
                    style="display:flex;gap:var(--sp-4);align-items:center;background:white;border-radius:var(--radius-md);padding:var(--sp-4);transition:box-shadow var(--duration) var(--ease);text-decoration:none"
                    onmouseover="this.style.boxShadow='0 4px 20px rgba(24,24,27,0.08)'"
                    onmouseout="this.style.boxShadow='none'">

                    {{-- Square thumbnail --}}
                    <div
                        style="width:88px;height:88px;flex-shrink:0;border-radius:var(--radius);overflow:hidden;background:var(--warm-cream)">
                        @if($product->main_image)
                        <img src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}"
                            style="width:100%;height:100%;object-fit:cover">
                        @endif
                    </div>

                    {{-- Info --}}
                    <div style="min-width:0;flex:1">
                        @if($product->category)
                        <div
                            style="font-size:10px;font-weight:600;letter-spacing:0.07em;text-transform:uppercase;color:var(--amber);margin-bottom:4px">
                            {{ $product->category->name }}
                        </div>
                        @endif
                        <div
                            style="font-size:var(--text-sm);font-weight:var(--weight-medium);color:var(--ink);line-height:1.35;margin-bottom:var(--sp-2);overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical">
                            {{ $product->name }}
                        </div>
                        <div
                            style="font-size:var(--text-sm);color:var(--gray-600);display:flex;align-items:center;gap:var(--sp-2)">
                            @if($isOnSale)
                            <span style="font-weight:600;color:var(--ink)">{{ $currencySymbol }}{{
                                number_format($product->sale_price, 2) }}</span>
                            <span style="text-decoration:line-through;font-size:var(--text-xs);color:var(--gray-400)">{{
                                $currencySymbol }}{{ number_format($product->price, 2) }}</span>
                            @else
                            <span style="font-weight:600;color:var(--ink)">{{ $currencySymbol }}{{
                                number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Arrow --}}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)"
                        stroke-width="2" style="flex-shrink:0">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>

                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── 4. BRAND QUOTE STRIP ────────────────────────────── --}}
<div class="brand-strip">
    <div class="brand-strip-rule"></div>
    <p class="brand-strip-quote">
        "We make things that last — not just in material, but in meaning."
    </p>
    <p class="brand-strip-cta">
        <a href="{{ route('about') }}">Our story →</a>
    </p>
</div>

{{-- ── 5. NEW ARRIVALS: horizontal scroll ─────────────── --}}
@if($newArrivals->count())
<section class="section-md" style="background:white">
    <div class="container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--sp-8)">
            <div class="section-header" style="margin-bottom:0">
                <span class="section-eyebrow">Just In</span>
                <h2 class="section-title">New Arrivals</h2>
            </div>
            <a href="{{ route('shop', ['filter' => 'new']) }}"
                style="font-size:var(--text-sm);color:var(--text-muted);white-space:nowrap;margin-bottom:var(--sp-1)">
                View all →
            </a>
        </div>
    </div>
    <div class="container">
        <div class="hscroll-strip">
            <div class="hscroll-inner">
                @foreach($newArrivals as $product)
                @include('partials.product-card')
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── 6. ON SALE (if any) ─────────────────────────────── --}}
@if($onSale->count())
<section class="section-md" style="background:var(--warm-cream)">
    <div class="container">
        <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--sp-8)">
            <div class="section-header" style="margin-bottom:0">
                <span class="section-eyebrow" style="color:var(--amber)">Limited Time</span>
                <h2 class="section-title">On Sale</h2>
            </div>
            <a href="{{ route('shop', ['filter' => 'sale']) }}"
                style="font-size:var(--text-sm);color:var(--text-muted);white-space:nowrap;margin-bottom:var(--sp-1)">
                View all →
            </a>
        </div>
        <div class="products-grid" style="grid-template-columns:repeat(4,1fr)">
            @foreach($onSale->take(4) as $product)
            @include('partials.product-card')
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection