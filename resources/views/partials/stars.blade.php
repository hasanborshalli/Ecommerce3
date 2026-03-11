{{--
Partial: stars
Usage: @include('partials.stars', ['rating' => $product->review_avg, 'size' => 'sm'])
$rating — float 0–5
$size — 'sm' | '' | 'lg' (default '')
--}}
@php
$r = (float)($rating ?? 0);
$cls = 'star-svg' . (isset($size) && $size ? ' ' . $size : '');
@endphp
<span class="stars" aria-label="{{ number_format($r, 1) }} out of 5 stars">
    @for($i = 1; $i <= 5; $i++) <svg class="{{ $cls }}{{ $i <= round($r) ? ' filled' : ' empty' }}" viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
        </svg>
        @endfor
</span>