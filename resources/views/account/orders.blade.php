@extends('layouts.app')

@section('title', 'My Orders — ' . $siteName)

@section('content')
<div class="container">
<div class="account-layout">

    @include('account.partials.nav', ['active' => 'orders'])

    <div class="account-main">
        <div class="account-section">

            <h1 class="account-section-title" style="margin-bottom:var(--sp-6)">Order History</h1>

            @include('partials.flash')

            @if($orders->count())

            <div style="display:flex;flex-direction:column;gap:var(--sp-3)">
                @foreach($orders as $order)
                <div class="order-row">
                    <div class="order-row-number">
                        <div style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                            {{ $order->order_number }}
                        </div>
                        <div style="font-size:var(--text-xs);color:var(--text-muted)">
                            {{ $order->created_at->format('M j, Y') }}
                            · {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                        </div>
                    </div>
                    <div>
                        <span class="order-status-badge status-{{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div>
                        <div style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                            {{ $currencySymbol }}{{ number_format($order->total, 2) }}
                        </div>
                        <div style="font-size:var(--text-xs);color:var(--text-muted)">
                            @if($order->payment_status === 'paid')
                                <span style="color:var(--success)">Paid</span>
                            @else
                                Cash on delivery
                            @endif
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-outline">
                            View Details
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div style="margin-top:var(--sp-6)">
                {{ $orders->links() }}
            </div>

            @else
            <div style="text-align:center;padding:var(--sp-12) 0;color:var(--text-muted)">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" style="margin:0 auto var(--sp-5);display:block;opacity:0.3">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 01-8 0"/>
                </svg>
                <h3 style="font-size:var(--text-base);font-weight:var(--weight-semibold);margin-bottom:var(--sp-2)">No orders yet</h3>
                <p style="font-size:var(--text-sm)">When you place an order, it'll appear here.</p>
                <a href="{{ route('shop') }}" class="btn btn-primary" style="margin-top:var(--sp-5)">
                    Start Shopping
                </a>
            </div>
            @endif

        </div>
    </div>

</div>
</div>
@endsection
