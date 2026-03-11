{{--
    Partial: account/partials/nav
    Usage: @include('account.partials.nav', ['active' => 'dashboard'])
    $active: 'dashboard' | 'orders' | 'addresses' | 'profile'
--}}
<aside class="account-nav">
    <div class="account-nav-header">
        <div class="customer-avatar-sm">
            {{ strtoupper(substr(session('customer_first_name', 'U'), 0, 1)) }}
        </div>
        <div>
            <div style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                {{ session('customer_name', 'My Account') }}
            </div>
            <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:140px">
                {{ session('customer_email', '') }}
            </div>
        </div>
    </div>

    <nav>
        <a href="{{ route('account.dashboard') }}"
           class="account-nav-link{{ ($active ?? '') === 'dashboard' ? ' active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Overview
        </a>
        <a href="{{ route('account.orders') }}"
           class="account-nav-link{{ ($active ?? '') === 'orders' ? ' active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
            Orders
        </a>
        <a href="{{ route('account.addresses') }}"
           class="account-nav-link{{ ($active ?? '') === 'addresses' ? ' active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Addresses
        </a>
        <a href="{{ route('account.profile') }}"
           class="account-nav-link{{ ($active ?? '') === 'profile' ? ' active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Profile
        </a>
    </nav>

    <div style="border-top:1px solid var(--border);padding-top:var(--sp-4);margin-top:var(--sp-4)">
        <form action="{{ route('account.logout') }}" method="POST">
            @csrf
            <button type="submit" class="account-nav-link account-nav-logout" style="width:100%;text-align:left;background:none;border:none;cursor:pointer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>
