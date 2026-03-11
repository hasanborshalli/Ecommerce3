<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — Admin · {{ $siteName ?? 'Luma' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles')
</head>
<body>

<div class="admin-layout">

    <div class="admin-sidebar-overlay" id="adminOverlay"
         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:199"
         onclick="closeSidebar()"></div>

    <aside class="admin-sidebar" id="adminSidebar">

        <a href="{{ route('admin.dashboard') }}" class="admin-logo">
            <div class="admin-logo-mark">L</div>
            <span class="admin-logo-name">{{ $siteName ?? 'Luma' }}</span>
            <span class="admin-logo-badge">ADMIN</span>
        </a>

        <nav class="admin-nav">

            <div class="admin-nav-section">
                <div class="admin-nav-label">Overview</div>
                <a href="{{ route('admin.dashboard') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.dashboard') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
            </div>

            <div class="admin-nav-section">
                <div class="admin-nav-label">Catalogue</div>
                <a href="{{ route('admin.products.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.products.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    Products
                </a>
                <a href="{{ route('admin.categories.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.categories.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                    Categories
                </a>
            </div>

            <div class="admin-nav-section">
                <div class="admin-nav-label">Sales</div>
                <a href="{{ route('admin.orders.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.orders.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Orders
                </a>
                <a href="{{ route('admin.customers.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.customers.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Customers
                </a>
                <a href="{{ route('admin.coupons.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.coupons.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                    Coupons
                </a>
            </div>

            <div class="admin-nav-section">
                <div class="admin-nav-label">Inventory</div>
                <a href="{{ route('admin.stock.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.stock.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    Stock
                    @php $lowStockCount = \App\Models\Product::lowStock()->count(); @endphp
                    @if($lowStockCount > 0)
                        <span class="nav-count warning">{{ $lowStockCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.purchase_orders.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.purchase_orders.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    Purchase Orders
                </a>
                <a href="{{ route('admin.suppliers.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.suppliers.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    Suppliers
                </a>
            </div>

            <div class="admin-nav-section">
                <div class="admin-nav-label">Insights</div>
                <a href="{{ route('admin.reports.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.reports.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Reports
                </a>
            </div>

            <div class="admin-nav-section">
                <div class="admin-nav-label">Community</div>
                <a href="{{ route('admin.reviews.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.reviews.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    Reviews
                    @php $pendingReviews = \App\Models\Review::where('status','pending')->count(); @endphp
                    @if($pendingReviews > 0)
                        <span class="nav-count">{{ $pendingReviews }}</span>
                    @endif
                </a>
            </div>

            <div class="admin-nav-section">
                <div class="admin-nav-label">Admin</div>
                <a href="{{ route('admin.messages.index') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.messages.*') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    Messages
                    @if(($unreadMessages ?? 0) > 0)
                        <span class="nav-count">{{ $unreadMessages }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.settings') }}"
                   class="admin-nav-item{{ request()->routeIs('admin.settings') ? ' active' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                    Settings
                </a>
                <a href="{{ route('home') }}" target="_blank" class="admin-nav-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    View Store
                </a>
            </div>

        </nav>

        <div class="admin-sidebar-footer">
            <div class="admin-user-row">
                <div class="admin-avatar">A</div>
                <div>
                    <div class="admin-user-name">Administrator</div>
                    <div class="admin-user-role">{{ config('admin.email') }}</div>
                </div>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="admin-logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Log Out
                </button>
            </form>
        </div>

    </aside>

    <div class="admin-main">

        <header class="admin-topbar">
            <div style="display:flex;align-items:center;gap:var(--sp-3)">
                <button onclick="toggleSidebar()" style="display:none;background:none;border:none;cursor:pointer;padding:var(--sp-2);color:var(--admin-muted)" id="sidebarToggle">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div>
                    <div class="admin-page-title">@yield('page_title', 'Dashboard')</div>
                    @hasSection('breadcrumb')
                    <div class="admin-breadcrumb">@yield('breadcrumb')</div>
                    @endif
                </div>
            </div>
            <div class="admin-topbar-right">
                @php $lowCount = \App\Models\Product::lowStock()->count(); @endphp
                @if($lowCount > 0)
                <a href="{{ route('admin.stock.index') }}" class="topbar-alert">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    {{ $lowCount }} low stock
                </a>
                @endif
                @if(($unreadMessages ?? 0) > 0)
                <a href="{{ route('admin.messages.index') }}" class="topbar-alert" style="background:var(--amber-light);border-color:var(--amber);color:var(--amber-hover)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    {{ $unreadMessages }} new
                </a>
                @endif
            </div>
        </header>

        <main class="admin-content">
            @include('partials.flash')
            @yield('content')
        </main>

    </div>

</div>

<script>
(function() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('adminOverlay');
    const toggle  = document.getElementById('sidebarToggle');

    function checkMobile() {
        const isMobile = window.innerWidth <= 900;
        if (toggle) toggle.style.display = isMobile ? 'flex' : 'none';
    }
    window.addEventListener('resize', checkMobile);
    checkMobile();

    window.toggleSidebar = function() {
        sidebar?.classList.toggle('open');
        if (overlay) overlay.style.display = sidebar?.classList.contains('open') ? 'block' : 'none';
        document.body.style.overflow = sidebar?.classList.contains('open') ? 'hidden' : '';
    };
    window.closeSidebar = function() {
        sidebar?.classList.remove('open');
        if (overlay) overlay.style.display = 'none';
        document.body.style.overflow = '';
    };
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebar(); });

    window.csrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    window.showAdminToast = function(msg, type) {
        const color = type === 'error' ? 'var(--danger)' : 'var(--amber)';
        const toast = document.createElement('div');
        toast.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:99999;
            background:var(--ink);color:#fff;padding:12px 20px;border-radius:8px;
            font-size:14px;font-weight:500;box-shadow:0 10px 25px rgba(0,0,0,0.2);
            display:flex;align-items:center;gap:8px;border-left:3px solid ${color};`;
        toast.innerHTML = `<span>${msg}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    };
})();
</script>

@stack('scripts')
</body>
</html>
