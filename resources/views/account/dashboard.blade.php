@extends('layouts.app')

@section('title', 'My Account — ' . $siteName)

@section('content')
<div class="container">
<div class="account-layout">

    {{-- ── Sidebar nav ─────────────────────────────────── --}}
    @include('account.partials.nav', ['active' => 'dashboard'])

    {{-- ── Main ───────────────────────────────────────── --}}
    <div class="account-main">

        <div class="account-section">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--sp-6)">
                <div>
                    <h1 class="account-section-title">Hello, {{ $customer->first_name }}!</h1>
                    <p style="color:var(--text-muted);font-size:var(--text-sm);margin-top:2px">
                        Here's a snapshot of your account
                    </p>
                </div>
                <div class="customer-avatar-lg">
                    {{ strtoupper(substr($customer->first_name, 0, 1)) }}{{ strtoupper(substr($customer->last_name, 0, 1)) }}
                </div>
            </div>

            {{-- Stats row --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--sp-4);margin-bottom:var(--sp-8)">
                <div style="background:var(--bg-subtle);border-radius:var(--radius-lg);padding:var(--sp-5);text-align:center;border:1px solid var(--border)">
                    <div style="font-size:var(--text-2xl);font-weight:var(--weight-bold);color:var(--amber)">{{ $totalOrders }}</div>
                    <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:2px;text-transform:uppercase;letter-spacing:var(--tracking-wider)">Orders</div>
                </div>
                <div style="background:var(--bg-subtle);border-radius:var(--radius-lg);padding:var(--sp-5);text-align:center;border:1px solid var(--border)">
                    <div style="font-size:var(--text-2xl);font-weight:var(--weight-bold);color:var(--amber)">{{ $currencySymbol }}{{ number_format($totalSpent, 0) }}</div>
                    <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:2px;text-transform:uppercase;letter-spacing:var(--tracking-wider)">Spent</div>
                </div>
                <div style="background:var(--bg-subtle);border-radius:var(--radius-lg);padding:var(--sp-5);text-align:center;border:1px solid var(--border)">
                    <div style="font-size:var(--text-2xl);font-weight:var(--weight-bold);color:var(--amber)">{{ $totalAddresses }}</div>
                    <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:2px;text-transform:uppercase;letter-spacing:var(--tracking-wider)">Addresses</div>
                </div>
            </div>

            {{-- Recent orders --}}
            @if($recentOrders->count())
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--sp-4)">
                    <h2 style="font-size:var(--text-base);font-weight:var(--weight-semibold)">Recent Orders</h2>
                    <a href="{{ route('account.orders') }}" class="auth-footer-link" style="font-size:var(--text-sm)">View all</a>
                </div>

                <div style="display:flex;flex-direction:column;gap:var(--sp-3)">
                    @foreach($recentOrders as $order)
                    <div class="order-row">
                        <div class="order-row-number">
                            <div style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">{{ $order->order_number }}</div>
                            <div style="font-size:var(--text-xs);color:var(--text-muted)">{{ $order->created_at->format('M j, Y') }}</div>
                        </div>
                        <div>
                            <span class="order-status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </div>
                        <div style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                            {{ $currencySymbol }}{{ number_format($order->total, 2) }}
                        </div>
                        <div>
                            <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-outline">
                                View
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div style="text-align:center;padding:var(--sp-10) 0;color:var(--text-muted)">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" style="margin:0 auto var(--sp-4);display:block;opacity:0.35">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                </svg>
                <p style="font-size:var(--text-sm)">You haven't placed any orders yet.</p>
                <a href="{{ route('shop') }}" class="btn btn-primary" style="margin-top:var(--sp-4)">Start Shopping</a>
            </div>
            @endif
        </div>

    </div>{{-- /account-main --}}
</div>
</div>
@endsection
