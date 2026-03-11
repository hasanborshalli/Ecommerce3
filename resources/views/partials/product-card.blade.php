@php
$isOnSale = $product->is_on_sale && $product->sale_price;
$isNew = $product->is_new;
@endphp
<div class="product-card">

    <div class="product-card-image-wrap">
        <a href="{{ route('product.show', $product->slug) }}" aria-label="{{ $product->name }}">
            @if($product->main_image)
            <img class="product-card-image" src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}"
                loading="lazy">
            @else
            <div class="product-card-image"
                style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--warm-stone)"
                    stroke-width="1.25">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <circle cx="8.5" cy="8.5" r="1.5" />
                    <polyline points="21 15 16 10 5 21" />
                </svg>
            </div>
            @endif
        </a>

        {{-- Quick-add slides up on hover --}}
        <div class="product-card-cta">
            @if($product->stock > 0)
            <button onclick="addToCart({{ $product->id }}, 1, {}, this)">Add to Cart</button>
            @else
            <button disabled>Out of Stock</button>
            @endif
        </div>

        {{-- Badges --}}
        @if($isNew || $isOnSale || $product->stock === 0)
        <div class="product-card-badges">
            @if($product->stock === 0)
            <span class="badge" style="background:rgba(24,24,27,0.8);color:white">Sold Out</span>
            @elseif($isOnSale)
            <span class="badge badge-sale">Sale</span>
            @elseif($isNew)
            <span class="badge badge-new">New</span>
            @endif
        </div>
        @endif
    </div>

    <div class="product-card-info">
        @if($product->category)
        <div class="product-card-category">{{ $product->category->name }}</div>
        @endif

        @if($product->review_count > 0)
        <div class="product-card-stars">
            @for($i = 1; $i <= 5; $i++) <svg
                class="star-svg sm {{ $i <= round($product->review_avg) ? 'filled' : 'empty' }}" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
                @endfor
                <span style="font-size:10px;color:var(--text-muted);margin-left:2px">({{ $product->review_count
                    }})</span>
        </div>
        @endif

        <a href="{{ route('product.show', $product->slug) }}" class="product-card-name">{{ $product->name }}</a>

        <div class="product-card-price">
            @if($isOnSale)
            <span class="product-card-price-sale">{{ $currencySymbol }}{{ number_format($product->sale_price, 2)
                }}</span>
            <span class="product-card-price-original">{{ $currencySymbol }}{{ number_format($product->price, 2)
                }}</span>
            @else
            <span>{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
            @endif
        </div>
    </div>

</div>