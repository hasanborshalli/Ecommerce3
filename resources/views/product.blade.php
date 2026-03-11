@extends('layouts.app')
@section('title', $product->name . ' — ' . ($siteName ?? 'Luma'))
@section('meta_description', Str::limit(strip_tags($product->description ?? $product->name), 155))

@section('content')
<div class="product-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span class="breadcrumb-sep">›</span>
            <a href="{{ route('shop') }}">Shop</a>
            @if($product->category)
            <span class="breadcrumb-sep">›</span>
            <a href="{{ route('shop', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
            @endif
            <span class="breadcrumb-sep">›</span>
            <span class="breadcrumb-current">{{ $product->name }}</span>
        </nav>

        <div class="product-layout">

            {{-- Gallery --}}
            <div class="product-gallery">

                {{-- Main image --}}
                <div class="product-main-img">
                    @if($product->main_image)
                    <img id="mainImage" src="{{ Storage::url($product->main_image) }}" alt="{{ $product->name }}"
                        style="width:100%;height:100%;object-fit:contain;display:block;">
                    @else
                    <div
                        style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--warm-cream)">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--warm-stone)"
                            stroke-width="1">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21 15 16 10 5 21" />
                        </svg>
                    </div>
                    @endif
                </div>

                {{-- Thumbnails — main image + gallery array --}}
                @php
                $allImages = [];
                if ($product->main_image) $allImages[] = $product->main_image;
                if (!empty($product->gallery) && is_array($product->gallery)) {
                foreach ($product->gallery as $g) { if ($g !== $product->main_image) $allImages[] = $g; }
                }
                @endphp
                @if(count($allImages) > 1)
                <div class="product-thumbs">
                    @foreach($allImages as $i => $img)
                    <button class="product-thumb {{ $i === 0 ? 'active' : '' }}"
                        onclick="switchImage('{{ Storage::url($img) }}', this)" type="button">
                        <img src="{{ Storage::url($img) }}" alt="{{ $product->name }} {{ $i+1 }}" loading="lazy">
                    </button>
                    @endforeach
                </div>
                @endif

            </div>

            {{-- Product info --}}
            <div class="product-info">

                @if($product->category)
                <div class="product-info-category">{{ $product->category->name }}</div>
                @endif

                <h1 class="product-info-name">{{ $product->name }}</h1>

                @if($product->review_count > 0)
                <div class="product-rating">
                    @include('partials.stars', ['rating' => $product->review_avg, 'size' => 'sm'])
                    <span>{{ number_format($product->review_avg, 1) }}</span>
                    <button onclick="scrollToReviews()"
                        style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:var(--text-sm)">
                        ({{ $product->review_count }} {{ Str::plural('review', $product->review_count) }})
                    </button>
                </div>
                @endif

                <div style="margin-bottom:var(--sp-5)">
                    <div class="product-price{{ $product->is_on_sale && $product->sale_price ? ' on-sale' : '' }}">
                        {{ $currencySymbol }}{{ number_format($product->effective_price, 2) }}
                        @if($product->is_on_sale && $product->sale_price)
                        <span class="product-price-original">{{ $currencySymbol }}{{ number_format($product->price, 2)
                            }}</span>
                        @endif
                    </div>
                </div>

                {{-- Stock --}}
                <div class="stock-indicator" style="margin-bottom:var(--sp-5)">
                    @if($product->stock === 0)
                    <span class="stock-dot out"></span>
                    <span style="color:var(--danger)">Out of Stock</span>
                    @elseif($product->stock <= $product->low_stock_threshold)
                        <span class="stock-dot low"></span>
                        <span style="color:var(--warning)">Only {{ $product->stock }} left</span>
                        @else
                        <span class="stock-dot in"></span>
                        <span style="color:var(--success)">In Stock</span>
                        @endif
                </div>

                @if($product->short_description)
                <p class="product-short-desc">{{ $product->short_description }}</p>
                @endif

                {{-- Add to cart --}}
                @if($product->stock > 0)
                <div class="product-add-actions" style="margin-bottom:var(--sp-6)">
                    <div class="qty-control">
                        <button type="button" onclick="changeQty(-1)">−</button>
                        <input type="number" class="qty-input" id="productQty" value="1" min="1"
                            max="{{ $product->stock }}">
                        <button type="button" onclick="changeQty(1)">+</button>
                    </div>
                    <button class="btn btn-primary" style="flex:1"
                        onclick="addToCart({{ $product->id }}, parseInt(document.getElementById('productQty').value), {}, this)">
                        Add to Cart
                    </button>
                    <a href="{{ route('checkout') }}" class="btn btn-amber"
                        onclick="addToCart({{ $product->id }}, parseInt(document.getElementById('productQty').value), {}, null)"
                        style="white-space:nowrap">Buy Now</a>
                </div>
                @else
                <button class="btn btn-outline btn-full" disabled style="margin-bottom:var(--sp-6)">Out of
                    Stock</button>
                @endif

                {{-- Product meta --}}
                <div class="product-meta" style="margin-bottom:var(--sp-6)">
                    @if($product->sku)
                    <div class="product-meta-row">
                        <span class="product-meta-label">SKU</span>
                        <span class="product-meta-value">{{ $product->sku }}</span>
                    </div>
                    @endif
                    @if($product->category)
                    <div class="product-meta-row">
                        <span class="product-meta-label">Category</span>
                        <span class="product-meta-value">
                            <a href="{{ route('shop', ['category' => $product->category->slug]) }}"
                                style="color:var(--amber)">{{ $product->category->name }}</a>
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Accordion --}}
                <div class="accordion">
                    @if($product->description)
                    <div class="accordion-item">
                        <button class="accordion-btn open" onclick="toggleAccordion(this)" type="button">
                            Description
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </button>
                        <div class="accordion-panel open" style="display:block">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                    @endif
                    <div class="accordion-item">
                        <button class="accordion-btn" onclick="toggleAccordion(this)" type="button">
                            Shipping &amp; Returns
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </button>
                        <div class="accordion-panel">
                            Free shipping on orders over {{ $currencySymbol }}{{ number_format($freeShippingOver ?? 150)
                            }}. Cash on delivery available. Returns accepted within 14 days of delivery.
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Reviews section ─────────────────────────────── --}}
        <div id="reviewsSection" class="reviews-section">
            <div class="reviews-section-header">
                <h2 class="reviews-section-title">
                    Reviews
                    @if($product->review_count > 0)
                    <span
                        style="font-family:var(--font);font-size:var(--text-lg);color:var(--text-muted);font-weight:400">({{
                        $product->review_count }})</span>
                    @endif
                </h2>
            </div>

            @if($reviews->count())
            <div class="reviews-summary">
                <div style="text-align:center">
                    <div class="reviews-avg-score">{{ number_format($product->review_avg, 1) }}</div>
                    @include('partials.stars', ['rating' => $product->review_avg, 'size' => 'lg'])
                    <div class="reviews-avg-label">out of 5</div>
                </div>
                <div class="reviews-bars">
                    @for($star = 5; $star >= 1; $star--)
                    @php
                    $count = $reviews->where('rating', $star)->count();
                    $pct = $reviews->count() ? round($count / $reviews->count() * 100) : 0;
                    @endphp
                    <div class="reviews-bar-row">
                        <span class="reviews-bar-label">{{ $star }}★</span>
                        <div class="reviews-bar-track">
                            <div class="reviews-bar-fill" style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="reviews-bar-count">{{ $count }}</span>
                    </div>
                    @endfor
                </div>
            </div>

            <div>
                @foreach($reviews as $review)
                <div class="review-card">
                    <div class="review-header">
                        <div>
                            <div class="review-stars">
                                @include('partials.stars', ['rating' => $review->rating, 'size' => 'sm'])
                            </div>
                            @if($review->title)
                            <div class="review-title">{{ $review->title }}</div>
                            @endif
                        </div>
                        <div style="text-align:right;flex-shrink:0">
                            <div class="review-author">{{ $review->author_name }}</div>
                            <div class="review-date">{{ $review->created_at->format('M j, Y') }}</div>
                        </div>
                    </div>
                    @if($review->body)
                    <p class="review-body">{{ $review->body }}</p>
                    @endif
                    @if($product->hasBeenOrdered($review->customer_id ?? 0))
                    <div class="review-verified">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Verified Purchase
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p style="color:var(--text-muted);font-size:var(--text-sm);padding:var(--sp-6) 0">
                No reviews yet — be the first to share your experience.
            </p>
            @endif

            {{-- Write a review --}}
            @if(session('customer_id') && !$customerHasReviewed)
            <div class="review-form-wrap">
                <h3 class="review-form-title">Write a Review</h3>

                @if(session('review_success'))
                <div class="alert alert-success" style="margin-bottom:var(--sp-5)">
                    Your review has been submitted and is pending approval. Thank you!
                </div>
                @endif

                <form action="{{ route('reviews.store', $product->slug) }}" method="POST"
                    style="display:flex;flex-direction:column;gap:var(--sp-5)">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Your Rating <span class="req">*</span></label>
                        <div class="star-picker" id="starPicker">
                            @for($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating')==$i
                                ? 'checked' : '' }}>
                            <label for="star{{ $i }}" title="{{ $i }} star{{ $i > 1 ? 's' : '' }}">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                            </label>
                            @endfor
                        </div>
                        @error('rating') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="review_title">Title</label>
                        <input type="text" id="review_title" name="title"
                            class="form-control{{ $errors->has('title') ? ' error' : '' }}" value="{{ old('title') }}"
                            placeholder="Sum up your experience">
                        @error('title') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="review_body">Your Review</label>
                        <textarea id="review_body" name="body" rows="4"
                            class="form-control{{ $errors->has('body') ? ' error' : '' }}"
                            placeholder="What did you think?">{{ old('body') }}</textarea>
                        @error('body') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                    <div><button type="submit" class="btn btn-primary">Submit Review</button></div>
                </form>
            </div>
            @elseif(!session('customer_id'))
            <div class="review-form-wrap" style="text-align:center">
                <h3 class="review-form-title">Have something to say?</h3>
                <p style="color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--sp-5)">Sign in to leave a
                    review for this product.</p>
                <a href="{{ route('account.login') }}" class="btn btn-primary">Sign In to Review</a>
            </div>
            @elseif($customerHasReviewed)
            <div class="alert alert-info" style="margin-top:var(--sp-6)">You've already reviewed this product. Thank
                you!</div>
            @endif
        </div>

        {{-- Related --}}
        @if($related->count())
        <div class="related-section">
            <div class="section-header">
                <span class="section-eyebrow">You may also like</span>
                <h2 class="section-title">Related Products</h2>
            </div>
            <div class="products-grid">
                @foreach($related as $product)
                @include('partials.product-card')
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
    function switchImage(src, btn) {
    const img = document.getElementById('mainImage');
    if (img) img.src = src;
    document.querySelectorAll('.product-thumb').forEach(t => t.classList.remove('active'));
    btn?.classList.add('active');
}
function changeQty(delta) {
    const input = document.getElementById('productQty');
    if (!input) return;
    const max = parseInt(input.max) || 99;
    input.value = Math.max(1, Math.min(max, parseInt(input.value || 1) + delta));
}
function toggleAccordion(btn) {
    const panel = btn.nextElementSibling;
    const isOpen = btn.classList.contains('open');
    btn.classList.toggle('open', !isOpen);
    panel.classList.toggle('open', !isOpen);
}
function scrollToReviews() {
    document.getElementById('reviewsSection')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}
</script>
@endpush