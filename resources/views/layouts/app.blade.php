<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $siteName ?? 'Luma')</title>
    <meta name="description" content="@yield('meta_description', $settings['meta_description'] ?? '')">

    <meta property="og:title" content="@yield('title', $siteName ?? 'Luma')">
    <meta property="og:description" content="@yield('meta_description', $settings['meta_description'] ?? '')">
    <meta property="og:type" content="website">

    {{-- Fonts: Inter + DM Serif Display --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">

    @stack('styles')
</head>

<body>

    {{-- Announcement bar --}}
    <div class="announcement-bar">
        @if(!empty($settings['announcement_text']))
        {{ $settings['announcement_text'] }}
        @if(!empty($settings['announcement_link']))
        &nbsp;·&nbsp; <a href="{{ $settings['announcement_link'] }}">{{ $settings['announcement_link_text'] ?? 'Learn
            more' }}</a>
        @endif
        @else
        Free shipping on orders over {{ $currencySymbol ?? '$' }}{{ number_format($freeShippingOver ?? 150) }}
        &nbsp;·&nbsp; Cash on delivery available
        @endif
    </div>

    {{-- ── Header: Logo Left · Nav Center · Icons Right ─────── --}}
    <header class="site-header" id="siteHeader">
        <div class="container">
            <div class="header-inner">

                {{-- Left: Logo --}}
                <a href="{{ route('home') }}" class="header-logo">
                    @if(!empty($settings['site_logo']))
                    <img src="{{ Storage::url($settings['site_logo']) }}" alt="{{ $siteName ?? 'Luma' }}">
                    @else
                    {{ $siteName ?? 'Luma' }}<span class="header-logo-dot">.</span>
                    @endif
                </a>

                {{-- Center: Nav (absolutely centered) --}}
                <nav class="header-nav-center" aria-label="Primary navigation">
                    <a href="{{ route('home') }}"
                        class="hdr-link{{ request()->routeIs('home') ? ' active' : '' }}">Home</a>

                    <div class="hdr-dropdown">
                        <a href="{{ route('shop') }}"
                            class="hdr-link{{ request()->routeIs('shop','product.show') ? ' active' : '' }}">
                            Shop
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </a>
                        <div class="hdr-dropdown-panel">
                            <a href="{{ route('shop') }}" class="hdr-dropdown-item" style="font-weight:500">All
                                Products</a>
                            @foreach($navCategories as $navCat)
                            <a href="{{ route('shop', ['category' => $navCat->slug]) }}" class="hdr-dropdown-item">{{
                                $navCat->name }}</a>
                            @endforeach
                            <a href="{{ route('shop', ['filter' => 'sale']) }}" class="hdr-dropdown-item"
                                style="color:var(--amber)">Sale</a>
                        </div>
                    </div>

                    <a href="{{ route('about') }}"
                        class="hdr-link{{ request()->routeIs('about') ? ' active' : '' }}">About</a>

                    <a href="{{ route('contact') }}"
                        class="hdr-link{{ request()->routeIs('contact') ? ' active' : '' }}">Contact</a>
                </nav>

                {{-- Right: Account + Cart --}}
                <div class="header-right">

                    {{-- Mobile hamburger --}}
                    <button class="nav-hamburger" id="hamburgerBtn" aria-label="Open menu" aria-expanded="false">
                        <span></span><span></span><span></span>
                    </button>

                    {{-- Account --}}
                    @if(session('customer_id'))
                    <a href="{{ route('account.dashboard') }}" class="hdr-icon"
                        title="{{ session('customer_first_name') }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.75">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </a>
                    @else
                    <a href="{{ route('account.login') }}" class="hdr-icon" title="Sign in">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.75">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </a>
                    @endif

                    {{-- Cart --}}
                    <a href="{{ route('cart.index') }}" class="hdr-icon" aria-label="Cart">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.75">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <path d="M16 10a4 4 0 01-8 0" />
                        </svg>
                        @if($cartCount > 0)
                        <span class="cart-badge" id="cartBadge">{{ $cartCount }}</span>
                        @else
                        <span class="cart-badge" id="cartBadge" style="display:none">0</span>
                        @endif
                    </a>
                </div>

            </div>
        </div>
    </header>

    {{-- ── Mobile Menu (slides from right) ──────────────────── --}}
    <div class="mobile-menu" id="mobileMenu" aria-modal="true" role="dialog">
        <div class="mobile-menu-overlay" id="menuOverlay"></div>
        <div class="mobile-menu-panel">
            <div class="mobile-menu-header">
                <span class="mobile-menu-logo">{{ $siteName ?? 'Luma' }}<span>.</span></span>
                <button class="mobile-menu-close" id="menuClose" aria-label="Close">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>
            <nav class="mobile-menu-nav">
                <a href="{{ route('home') }}" class="mobile-nav-link">Home</a>
                <div>
                    <div class="mobile-nav-link"
                        onclick="this.nextElementSibling.classList.toggle('open');this.querySelector('svg').style.transform=this.nextElementSibling.classList.contains('open')?'rotate(180deg)':''">
                        Shop
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" style="transition:transform 0.2s">
                            <polyline points="6 9 12 15 18 9" />
                        </svg>
                    </div>
                    <div class="mobile-nav-sub">
                        <a href="{{ route('shop') }}">All Products</a>
                        @foreach($navCategories as $c)
                        <a href="{{ route('shop', ['category' => $c->slug]) }}">{{ $c->name }}</a>
                        @endforeach
                        <a href="{{ route('shop', ['filter' => 'sale']) }}" style="color:var(--amber)">Sale</a>
                    </div>
                </div>
                <a href="{{ route('about') }}" class="mobile-nav-link">About</a>
                <a href="{{ route('contact') }}" class="mobile-nav-link">Contact</a>

                <div style="border-top:1px solid var(--border);margin-top:var(--sp-4);padding-top:var(--sp-4)">
                    @if(session('customer_id'))
                    <a href="{{ route('account.dashboard') }}" class="mobile-nav-link">My Account</a>
                    @else
                    <a href="{{ route('account.login') }}" class="mobile-nav-link">Sign In</a>
                    <a href="{{ route('account.register') }}" class="mobile-nav-link">Create Account</a>
                    @endif
                    <a href="{{ route('cart.index') }}" class="mobile-nav-link">
                        Cart
                        @if($cartCount > 0)<span
                            style="background:var(--amber);color:white;font-size:10px;padding:1px 6px;border-radius:99px;font-weight:600">{{
                            $cartCount }}</span>@endif
                    </a>
                </div>
            </nav>
        </div>
    </div>

    {{-- ── Flash messages ─────────────────────────────────────── --}}
    @if(session('success'))
    <div id="flashMsg" style="display:none">{{ session('success') }}</div>
    @endif

    {{-- ── Main content ─────────────────────────────────────────── --}}
    <main id="mainContent">
        @yield('content')
    </main>

    {{-- ── Footer ─────────────────────────────────────────────── --}}
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">

                {{-- Brand col --}}
                <div>
                    <div class="footer-logo">{{ $siteName ?? 'Luma' }}<span>.</span></div>
                    <p class="footer-about">
                        {{ $settings['footer_about'] ?? 'Thoughtfully made goods that bring warmth and quiet to everyday
                        life.' }}
                    </p>
                    <div class="footer-social">
                        @if(!empty($settings['social_instagram']))
                        <a href="{{ $settings['social_instagram'] }}" aria-label="Instagram">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                                <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z" />
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                            </svg>
                        </a>
                        @endif
                        @if(!empty($settings['social_facebook']))
                        <a href="{{ $settings['social_facebook'] }}" aria-label="Facebook">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z" />
                            </svg>
                        </a>
                        @endif
                        @if(!empty($settings['social_twitter']))
                        <a href="{{ $settings['social_twitter'] }}" aria-label="Twitter/X">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path
                                    d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z" />
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Shop col --}}
                <div class="footer-col">
                    <div class="footer-col-title">Shop</div>
                    <ul>
                        <li><a href="{{ route('shop') }}">All Products</a></li>
                        @foreach($navCategories->take(4) as $c)
                        <li><a href="{{ route('shop', ['category' => $c->slug]) }}">{{ $c->name }}</a></li>
                        @endforeach
                        <li><a href="{{ route('shop', ['filter' => 'new']) }}">New Arrivals</a></li>
                        <li><a href="{{ route('shop', ['filter' => 'sale']) }}" style="color:var(--amber)">Sale</a></li>
                    </ul>
                </div>

                {{-- Company col --}}
                <div class="footer-col">
                    <div class="footer-col-title">Company</div>
                    <ul>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        <li><a href="{{ route('account.login') }}">My Account</a></li>
                        <li><a href="{{ route('cart.index') }}">Cart</a></li>
                    </ul>
                </div>

                {{-- Contact col --}}
                <div class="footer-col">
                    <div class="footer-col-title">Get in Touch</div>
                    @if(!empty($settings['contact_email']))
                    <p style="font-size:var(--text-sm);color:rgba(255,255,255,0.5);margin-bottom:var(--sp-2)">{{
                        $settings['contact_email'] }}</p>
                    @endif
                    @if(!empty($settings['contact_phone']))
                    <p style="font-size:var(--text-sm);color:rgba(255,255,255,0.5);margin-bottom:var(--sp-2)">{{
                        $settings['contact_phone'] }}</p>
                    @endif
                    @if(!empty($settings['contact_address']))
                    <p style="font-size:var(--text-sm);color:rgba(255,255,255,0.35);line-height:1.6">{{
                        $settings['contact_address'] }}</p>
                    @endif
                </div>

            </div>
        </div>

        <div class="container">
            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} {{ $siteName ?? 'Luma' }}. All rights reserved.</span>
                <span class="powered-by">Built by <span>brndng.</span></span>
            </div>
        </div>
    </footer>

    {{-- Toast notification --}}
    <div class="site-toast" id="siteToast">
        <span class="site-toast-dot"></span>
        <span id="toastMsg"></span>
    </div>

    @stack('scripts')
    <script>
        // ── Header scroll ─────────────────────────────────────────
const siteHeader = document.getElementById('siteHeader');
window.addEventListener('scroll', () => {
    siteHeader.classList.toggle('scrolled', window.scrollY > 10);
}, { passive: true });

// ── Mobile menu ───────────────────────────────────────────
const mobileMenu  = document.getElementById('mobileMenu');
const hamburgerBtn = document.getElementById('hamburgerBtn');
const menuClose   = document.getElementById('menuClose');
const menuOverlay = document.getElementById('menuOverlay');

function openMenu()  { mobileMenu.classList.add('open'); document.body.style.overflow = 'hidden'; hamburgerBtn.setAttribute('aria-expanded','true'); }
function closeMenu() { mobileMenu.classList.remove('open'); document.body.style.overflow = ''; hamburgerBtn.setAttribute('aria-expanded','false'); }

hamburgerBtn?.addEventListener('click', openMenu);
menuClose?.addEventListener('click', closeMenu);
menuOverlay?.addEventListener('click', closeMenu);

// ── Cart badge ────────────────────────────────────────────
function updateCartBadge(count) {
    const badge = document.getElementById('cartBadge');
    if (!badge) return;
    badge.textContent = count;
    badge.style.display = count > 0 ? 'flex' : 'none';
}

// ── Toast ──────────────────────────────────────────────────
let toastTimer;
function showToast(msg) {
    const toast = document.getElementById('siteToast');
    const label = document.getElementById('toastMsg');
    if (!toast || !label) return;
    label.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 2800);
}

// ── Flash via JS ──────────────────────────────────────────
const flashEl = document.getElementById('flashMsg');
if (flashEl) { showToast(flashEl.textContent.trim()); }

// ── Add to cart helper ─────────────────────────────────────
function addToCart(productId, qty, variant, btn) {
    const origText = btn ? btn.textContent : '';
    if (btn) { btn.disabled = true; btn.textContent = '…'; }
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ product_id: productId, quantity: qty, variant: variant }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.cart_count);
            showToast(data.message || 'Added to cart');
        } else {
            showToast(data.message || 'Could not add to cart');
        }
        if (btn) { btn.disabled = false; btn.textContent = origText; }
    })
    .catch(() => { if (btn) { btn.disabled = false; btn.textContent = origText; } });
}
    </script>
</body>

</html>