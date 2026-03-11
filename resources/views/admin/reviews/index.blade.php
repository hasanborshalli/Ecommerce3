@extends('admin.layout')
@section('title', 'Reviews')
@section('page_title', 'Reviews')
@section('breadcrumb') Community › Reviews @endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>Reviews</h1>
        <p>Moderate customer reviews before they appear on product pages.</p>
    </div>
</div>

<div class="admin-tabs">
    @php $cur = request('status', 'all'); @endphp
    @foreach(['all' => 'All (' . $counts['all'] . ')', 'pending' => 'Pending (' . $counts['pending'] . ')', 'approved'
    => 'Approved (' . $counts['approved'] . ')', 'rejected' => 'Rejected (' . $counts['rejected'] . ')'] as $key =>
    $label)
    <a href="{{ route('admin.reviews.index', array_merge(request()->except(['status','page']), ['status' => $key])) }}"
        class="admin-tab{{ $cur === $key ? ' active' : '' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="admin-table-header"
    style="background:white;border:1px solid var(--admin-border);border-bottom:none;border-radius:var(--radius) var(--radius) 0 0;padding:var(--sp-3) var(--sp-4)">
    <form method="GET" action="{{ route('admin.reviews.index') }}"
        style="display:flex;gap:var(--sp-2);flex:1;flex-wrap:wrap">
        @if(request('status') && request('status') !== 'all')
        <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="admin-search" style="max-width:260px">
            <span class="admin-search-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg></span>
            <input type="text" name="search" class="admin-search-input" value="{{ request('search') }}"
                placeholder="Customer or product…">
        </div>
        <select name="rating" class="aform-control" style="height:36px;width:130px;font-size:var(--text-sm)">
            <option value="">All ratings</option>
            @for($r=5;$r>=1;$r--)
            <option value="{{ $r }}" {{ request('rating')==$r ? 'selected' :'' }}>{{ str_repeat('★',$r) }}{{
                str_repeat('☆',5-$r) }}</option>
            @endfor
        </select>
        <button type="submit" class="abtn abtn-outline">Filter</button>
        @if(request()->hasAny(['search','rating']))<a
            href="{{ route('admin.reviews.index', request()->only('status')) }}" class="abtn abtn-ghost">Clear</a>@endif
    </form>
</div>

<div
    style="border:1px solid var(--admin-border);border-radius:0 0 var(--radius) var(--radius);background:white;display:flex;flex-direction:column;gap:0">

    @forelse($reviews as $review)
    <div class="review-admin-card {{ $review->status === 'pending' ? 'review-admin-card-pending' : '' }}">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:var(--sp-4);flex-wrap:wrap">
            <div style="display:flex;align-items:flex-start;gap:var(--sp-3);min-width:0">
                <div class="customer-avatar-sm" style="background:var(--amber);color:white;flex-shrink:0">
                    {{ strtoupper(substr($review->customer_name, 0, 1)) }}
                </div>
                <div style="min-width:0">
                    <div style="display:flex;align-items:center;gap:var(--sp-2);flex-wrap:wrap">
                        <span style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">{{
                            $review->customer_name }}</span>
                        <span style="font-size:var(--text-xs);color:var(--amber)">
                            {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                        </span>
                        <span style="font-size:var(--text-xs);color:var(--admin-muted)">{{
                            $review->created_at->format('M d, Y') }}</span>
                        @if($review->status === 'pending')
                        <span class="review-pending-badge">Pending</span>
                        @elseif($review->status === 'approved')
                        <span class="review-approved-badge">Approved</span>
                        @else
                        <span class="badge badge-danger">Rejected</span>
                        @endif
                    </div>
                    @if($review->product)
                    <div style="font-size:var(--text-xs);color:var(--admin-muted);margin-top:2px">
                        On: <a href="{{ route('admin.products.edit', $review->product) }}"
                            style="color:var(--admin-accent)">{{ $review->product->name }}</a>
                    </div>
                    @endif
                    <div
                        style="margin-top:var(--sp-2);font-size:var(--text-sm);color:var(--admin-text);line-height:1.6">
                        {{ $review->body }}
                    </div>
                </div>
            </div>

            <div class="review-admin-actions">
                @if($review->status !== 'approved')
                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="abtn abtn-sm"
                        style="background:var(--success);color:white;border:none">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        Approve
                    </button>
                </form>
                @endif
                @if($review->status !== 'rejected')
                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="abtn abtn-sm"
                        style="background:var(--gray-100);color:var(--admin-text);border:1px solid var(--admin-border)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18" />
                            <line x1="6" y1="6" x2="18" y2="18" />
                        </svg>
                        Reject
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                    onsubmit="return confirm('Delete this review?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="table-action delete" title="Delete review">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <polyline points="3 6 5 6 21 6" />
                            <path d="M19 6l-1 14H6L5 6" />
                            <path d="M9 6V4h6v2" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div style="padding:var(--sp-16);text-align:center;color:var(--admin-muted)">No reviews found.</div>
    @endforelse

</div>

@if($reviews->hasPages())
<div
    style="display:flex;justify-content:space-between;align-items:center;margin-top:var(--sp-4);font-size:var(--text-sm);color:var(--admin-muted)">
    <span>{{ $reviews->total() }} {{ Str::plural('review', $reviews->total()) }}</span>
    {{ $reviews->links() }}
</div>
@endif

@endsection