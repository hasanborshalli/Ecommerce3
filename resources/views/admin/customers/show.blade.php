@extends('admin.layout')
@section('title', $customer->full_name)
@section('page_title', $customer->full_name)
@section('breadcrumb')
    <a href="{{ route('admin.customers.index') }}">Customers</a> › {{ $customer->full_name }}
@endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left" style="display:flex;align-items:center;gap:var(--sp-4)">
        <div class="customer-avatar-lg" style="background:var(--amber);color:white;font-size:var(--text-xl)">
            {{ strtoupper(substr($customer->first_name, 0, 1)) }}{{ strtoupper(substr($customer->last_name, 0, 1)) }}
        </div>
        <div>
            <h1>{{ $customer->full_name }}</h1>
            <p style="color:var(--admin-muted);font-size:var(--text-sm)">
                {{ $customer->email }}
                @if($customer->phone) · {{ $customer->phone }} @endif
                · Joined {{ $customer->created_at->format('M d, Y') }}
            </p>
        </div>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.customers.index') }}" class="abtn abtn-ghost">← All Customers</a>
    </div>
</div>

{{-- Stats row --}}
<div class="admin-stat-grid" style="margin-bottom:var(--sp-5)">
    <div class="stat-card">
        <div class="stat-card-label">Total Orders</div>
        <div class="stat-card-value">{{ $totalOrders }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">Total Spent</div>
        <div class="stat-card-value">{{ $currencySymbol }}{{ number_format($totalSpent, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">Reviews Left</div>
        <div class="stat-card-value">{{ $reviews->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">Saved Addresses</div>
        <div class="stat-card-value">{{ $customer->addresses->count() }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:var(--sp-5);align-items:start">

    {{-- Orders table --}}
    <div style="display:flex;flex-direction:column;gap:var(--sp-5)">
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">Order History</span>
                <a href="{{ route('admin.orders.index', ['search' => $customer->email]) }}" class="abtn abtn-ghost abtn-sm">All orders →</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr><th>Order</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}" style="color:var(--admin-accent);font-weight:var(--weight-semibold);font-size:var(--text-xs)">{{ $order->order_number }}</a></td>
                        <td style="font-size:var(--text-sm);color:var(--admin-muted)">{{ $order->items->sum('quantity') }}</td>
                        <td style="font-size:var(--text-sm);font-weight:var(--weight-semibold)">{{ $currencySymbol }}{{ number_format($order->total, 2) }}</td>
                        <td>@php $b = $order->status_badge; @endphp<span class="badge {{ $b['class'] }}">{{ $b['label'] }}</span></td>
                        <td style="font-size:var(--text-xs);color:var(--admin-muted)">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:var(--sp-8);color:var(--admin-muted)">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($orders->hasPages())
            <div class="admin-table-footer">{{ $orders->links() }}</div>
            @endif
        </div>

        @if($reviews->count())
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Reviews Left</span></div>
            <div class="admin-card-body" style="padding:0">
                @foreach($reviews as $review)
                <div style="padding:var(--sp-4);border-bottom:1px solid var(--admin-border)">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--sp-2)">
                        <div>
                            <span style="font-size:var(--text-sm);font-weight:var(--weight-medium)">{{ $review->product->name ?? '(deleted)' }}</span>
                            <span style="font-size:10px;color:var(--amber);margin-left:var(--sp-2)">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                        </div>
                        <span class="badge {{ $review->status === 'approved' ? 'badge-success' : ($review->status === 'rejected' ? 'badge-danger' : 'badge-warning') }}">
                            {{ ucfirst($review->status) }}
                        </span>
                    </div>
                    <div style="font-size:var(--text-sm);color:var(--admin-muted)">{{ $review->comment }}</div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div style="display:flex;flex-direction:column;gap:var(--sp-5)">
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Account Info</span></div>
            <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                @foreach([['Name', $customer->full_name], ['Email', $customer->email], ['Phone', $customer->phone ?? '—']] as [$label, $value])
                <div style="display:flex;justify-content:space-between;font-size:var(--text-sm);gap:var(--sp-3)">
                    <span style="color:var(--admin-muted);flex-shrink:0">{{ $label }}</span>
                    <span style="font-weight:var(--weight-medium);text-align:right">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        @if($customer->addresses->count())
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Saved Addresses</span></div>
            <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                @foreach($customer->addresses as $addr)
                <div style="background:var(--admin-bg);border-radius:var(--radius);padding:var(--sp-3);font-size:var(--text-sm)">
                    @if($addr->is_default)<span class="badge badge-success" style="margin-bottom:var(--sp-1)">Default</span>@endif
                    <div>{{ $addr->address_line1 }}</div>
                    @if($addr->address_line2)<div>{{ $addr->address_line2 }}</div>@endif
                    <div>{{ $addr->city }}@if($addr->state), {{ $addr->state }}@endif</div>
                    @if($addr->country)<div style="color:var(--admin-muted)">{{ $addr->country }}</div>@endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

</div>

@endsection
