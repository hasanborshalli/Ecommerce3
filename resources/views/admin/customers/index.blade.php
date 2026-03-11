@extends('admin.layout')
@section('title', 'Customers')
@section('page_title', 'Customers')
@section('breadcrumb') Sales › Customers @endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>Customers</h1>
        <p>Registered accounts and order history.</p>
    </div>
</div>

<div class="admin-stat-grid" style="margin-bottom:var(--sp-5)">
    <div class="stat-card">
        <div class="stat-card-label">Total Customers</div>
        <div class="stat-card-value">{{ number_format($totalCustomers) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">New This Month</div>
        <div class="stat-card-value" style="color:var(--success)">{{ $newThisMonth }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">With Orders</div>
        <div class="stat-card-value">{{ $customersWithOrders }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-label">Guest Shoppers</div>
        <div class="stat-card-value" style="color:var(--admin-muted)">{{ $totalCustomers - $customersWithOrders }}</div>
        <div class="stat-card-sub">no orders yet</div>
    </div>
</div>

<div class="admin-table-wrap">
    <div class="admin-table-header">
        <form method="GET" action="{{ route('admin.customers.index') }}" style="display:flex;gap:var(--sp-2);flex:1">
            <div class="admin-search" style="max-width:300px">
                <span class="admin-search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" name="search" class="admin-search-input" value="{{ request('search') }}" placeholder="Name or email…">
            </div>
            <button type="submit" class="abtn abtn-outline">Search</button>
            @if(request('search'))<a href="{{ route('admin.customers.index') }}" class="abtn abtn-ghost">Clear</a>@endif
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>Phone</th>
                <th>Orders</th>
                <th class="text-right">Total Spent</th>
                <th>Joined</th>
                <th style="width:60px"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:var(--sp-3)">
                        <div class="customer-avatar-sm" style="background:var(--amber);color:white">
                            {{ strtoupper(substr($customer->first_name, 0, 1)) }}{{ strtoupper(substr($customer->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:var(--weight-medium);font-size:var(--text-sm)">{{ $customer->full_name }}</div>
                            <div style="font-size:var(--text-xs);color:var(--admin-muted)">{{ $customer->email }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">{{ $customer->phone ?? '—' }}</td>
                <td>
                    @if($customer->orders_count > 0)
                        <span style="font-size:var(--text-sm);font-weight:var(--weight-semibold)">{{ $customer->orders_count }}</span>
                    @else
                        <span style="font-size:var(--text-sm);color:var(--admin-muted)">0</span>
                    @endif
                </td>
                <td class="text-right">
                    <span style="font-size:var(--text-sm);font-weight:var(--weight-semibold)">
                        {{ $currencySymbol }}{{ number_format($customer->total_spent ?? 0, 2) }}
                    </span>
                </td>
                <td style="font-size:var(--text-xs);color:var(--admin-muted)">{{ $customer->created_at->format('M d, Y') }}</td>
                <td>
                    <a href="{{ route('admin.customers.show', $customer) }}" class="table-action" title="View">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:var(--sp-12);color:var(--admin-muted)">No customers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="admin-table-footer">
        <span>{{ $customers->total() }} {{ Str::plural('customer', $customers->total()) }}</span>
        {{ $customers->links() }}
    </div>
</div>

@endsection
