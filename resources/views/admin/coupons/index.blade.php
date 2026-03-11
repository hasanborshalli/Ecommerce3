@extends('admin.layout')
@section('title', 'Coupons')
@section('page_title', 'Coupons')
@section('breadcrumb') Sales › Coupons @endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left"><h1>Coupons</h1><p>Create and manage discount codes.</p></div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.coupons.create') }}" class="abtn abtn-blue">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Coupon
        </a>
    </div>
</div>

<div class="admin-tabs">
    @php $cur = request('status', 'all'); @endphp
    @foreach(['all' => 'All (' . $counts['all'] . ')', 'active' => 'Active (' . $counts['active'] . ')', 'expired' => 'Expired (' . $counts['expired'] . ')'] as $key => $label)
    <a href="{{ route('admin.coupons.index', array_merge(request()->except(['status','page']), ['status' => $key])) }}"
       class="admin-tab{{ $cur === $key ? ' active' : '' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="admin-table-wrap">
    <div class="admin-table-header">
        <form method="GET" action="{{ route('admin.coupons.index') }}" style="display:flex;gap:var(--sp-2);flex:1">
            <div class="admin-search" style="max-width:260px">
                <span class="admin-search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></span>
                <input type="text" name="search" class="admin-search-input" value="{{ request('search') }}" placeholder="Coupon code…">
            </div>
            <select name="type" class="aform-control" style="height:36px;width:160px;font-size:var(--text-sm)">
                <option value="all">All types</option>
                <option value="percentage" {{ request('type')==='percentage' ? 'selected':'' }}>Percentage</option>
                <option value="fixed"      {{ request('type')==='fixed'      ? 'selected':'' }}>Fixed amount</option>
                <option value="free_shipping" {{ request('type')==='free_shipping' ? 'selected':'' }}>Free shipping</option>
            </select>
            @if(request('status') && request('status') !== 'all')
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <button type="submit" class="abtn abtn-outline">Filter</button>
            @if(request()->hasAny(['search','type']))<a href="{{ route('admin.coupons.index', request()->only('status')) }}" class="abtn abtn-ghost">Clear</a>@endif
        </form>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Min Order</th>
                <th>Usage</th>
                <th>Expires</th>
                <th>Status</th>
                <th style="width:90px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($coupons as $coupon)
            <tr>
                <td>
                    <code style="font-family:var(--font-mono);font-size:var(--text-sm);font-weight:var(--weight-bold);background:var(--admin-bg);padding:2px 8px;border-radius:4px;letter-spacing:0.05em">{{ $coupon->code }}</code>
                    @if($coupon->description)
                        <div style="font-size:var(--text-xs);color:var(--admin-muted);margin-top:2px">{{ $coupon->description }}</div>
                    @endif
                </td>
                <td>
                    <span class="coupon-type-badge coupon-type-{{ $coupon->type }}">
                        {{ $coupon->type === 'percentage' ? '%' : ($coupon->type === 'fixed' ? '$' : '🚚') }}
                        {{ ucfirst(str_replace('_', ' ', $coupon->type)) }}
                    </span>
                </td>
                <td style="font-size:var(--text-sm);font-weight:var(--weight-semibold)">
                    @if($coupon->type === 'percentage') {{ $coupon->value }}%
                    @elseif($coupon->type === 'fixed') {{ $currencySymbol }}{{ number_format($coupon->value, 2) }}
                    @else <span style="color:var(--admin-muted)">—</span>
                    @endif
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $coupon->min_order_amount ? $currencySymbol . number_format($coupon->min_order_amount, 2) : '—' }}
                </td>
                <td style="min-width:120px">
                    @php $usesCount = $coupon->uses_count ?? 0; @endphp
                    <div style="font-size:var(--text-sm)">
                        {{ $usesCount }}
                        @if($coupon->max_uses) / {{ $coupon->max_uses }} @endif
                    </div>
                    @if($coupon->max_uses)
                    @php $pct = min(100, round($usesCount / $coupon->max_uses * 100)); @endphp
                    <div class="coupon-usage-bar">
                        <div class="coupon-usage-fill" style="width:{{ $pct }}%"></div>
                    </div>
                    @endif
                </td>
                <td style="font-size:var(--text-xs);color:var(--admin-muted)">
                    @if($coupon->expires_at)
                        @if($coupon->is_expired)
                            <span style="color:var(--danger)">Expired {{ $coupon->expires_at->format('M d, Y') }}</span>
                        @else
                            {{ $coupon->expires_at->format('M d, Y') }}
                        @endif
                    @else
                        <span style="color:var(--admin-muted)">No expiry</span>
                    @endif
                </td>
                <td>
                    @if(!$coupon->is_active)
                        <span class="badge badge-neutral">Inactive</span>
                    @elseif($coupon->is_expired)
                        <span class="badge badge-danger">Expired</span>
                    @elseif($coupon->is_used_up)
                        <span class="badge badge-warning">Used up</span>
                    @else
                        <span class="badge badge-success">Active</span>
                    @endif
                </td>
                <td>
                    <div class="table-actions">
                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="table-action" title="Edit">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete coupon {{ addslashes($coupon->code) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="table-action delete" title="Delete">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:var(--sp-12);color:var(--admin-muted)">No coupons yet. <a href="{{ route('admin.coupons.create') }}" style="color:var(--admin-accent)">Create one</a></td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="admin-table-footer">
        <span>{{ $coupons->total() }} {{ Str::plural('coupon', $coupons->total()) }}</span>
        {{ $coupons->links() }}
    </div>
</div>

@endsection
