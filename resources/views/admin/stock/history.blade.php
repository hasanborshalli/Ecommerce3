@extends('admin.layout')

@section('title', 'Stock History — ' . $product->name)
@section('page_title', 'Stock History')
@section('breadcrumb')
    <a href="{{ route('admin.stock.index') }}">Stock</a> › {{ $product->name }}
@endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left" style="display:flex;align-items:center;gap:var(--sp-4)">
        @if($product->main_image)
            <img src="{{ Storage::url($product->main_image) }}"
                 style="width:52px;height:52px;object-fit:contain;border:1px solid var(--admin-border);border-radius:var(--radius-lg)">
        @endif
        <div>
            <h1>{{ $product->name }}</h1>
            <p style="display:flex;align-items:center;gap:var(--sp-3)">
                <span>SKU: <code style="font-size:var(--text-xs);font-family:var(--font-mono)">{{ $product->sku ?? '—' }}</code></span>
                <span style="color:var(--admin-muted)">·</span>
                <span>Current stock:
                    <strong style="color:{{ $product->stock <= 0 ? 'var(--danger)' : ($product->is_low_stock ? 'var(--warning)' : 'var(--success)') }}">
                        {{ $product->stock }}
                    </strong>
                </span>
            </p>
        </div>
    </div>
    <div class="admin-page-actions">
        <button type="button" class="abtn abtn-outline"
                onclick="openAdjust({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }})">
            ± Adjust Stock
        </button>
        <a href="{{ route('admin.stock.index') }}" class="abtn abtn-ghost">← Back</a>
    </div>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Note</th>
                <th class="text-right">Change</th>
                <th class="text-right">Before</th>
                <th class="text-right">After</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $m)
            <tr>
                <td style="font-size:var(--text-xs);color:var(--admin-muted);white-space:nowrap">
                    {{ $m->created_at->format('M d, Y') }}<br>
                    {{ $m->created_at->format('H:i') }}
                </td>
                <td>
                    <span class="badge {{ $m->quantity > 0 ? 'badge-success' : 'badge-warning' }}">
                        {{ $m->type_label }}
                    </span>
                </td>
                <td style="font-size:var(--text-xs);color:var(--admin-muted);font-family:var(--font-mono)">
                    {{ $m->reference_label ?? '—' }}
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $m->notes ?? '—' }}
                </td>
                <td class="text-right">
                    <span class="{{ $m->quantity > 0 ? 'profit-positive' : 'profit-negative' }}"
                          style="font-size:var(--text-sm);font-weight:var(--weight-bold)">
                        {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                    </span>
                </td>
                <td class="text-right" style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $m->stock_before }}
                </td>
                <td class="text-right" style="font-size:var(--text-sm);font-weight:var(--weight-semibold)">
                    {{ $m->stock_after }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:var(--sp-10);color:var(--admin-muted)">
                    No stock movements recorded for this product.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="admin-table-footer">
        <span>{{ $movements->total() }} {{ Str::plural('movement', $movements->total()) }}</span>
        {{ $movements->links() }}
    </div>
</div>

{{-- Reuse adjust modal (same as stock/index) --}}
<div id="adjustModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center">
    <div style="background:white;border-radius:var(--radius-xl);width:400px;max-width:90vw;overflow:hidden;box-shadow:var(--shadow-xl)">
        <div style="padding:var(--sp-5) var(--sp-6);border-bottom:1px solid var(--admin-border);display:flex;align-items:center;justify-content:space-between">
            <h3 style="font-size:var(--text-lg);font-weight:var(--weight-bold)">Adjust Stock</h3>
            <button onclick="closeAdjust()" style="background:none;border:none;cursor:pointer;color:var(--admin-muted);font-size:20px;line-height:1">×</button>
        </div>
        <form id="adjustForm" method="POST">
            @csrf
            <div style="padding:var(--sp-6);display:flex;flex-direction:column;gap:var(--sp-4)">
                <div style="background:var(--admin-bg);border-radius:var(--radius);padding:var(--sp-3) var(--sp-4);font-size:var(--text-sm)">
                    <strong id="adjustProductName"></strong>
                    <div style="color:var(--admin-muted);margin-top:2px">Current stock: <strong id="adjustCurrentStock"></strong></div>
                </div>
                <div class="aform-group">
                    <label class="aform-label">Adjustment</label>
                    <input type="number" name="adjustment" class="aform-control"
                           placeholder="e.g. +10 to add, -5 to remove" required>
                    <span class="aform-hint">Positive adds stock, negative removes stock.</span>
                </div>
                <div class="aform-group">
                    <label class="aform-label">Reason <span class="req">*</span></label>
                    <input type="text" name="reason" class="aform-control"
                           placeholder="e.g. Damaged units removed" required>
                </div>
            </div>
            <div style="padding:var(--sp-4) var(--sp-6);border-top:1px solid var(--admin-border);display:flex;gap:var(--sp-2);justify-content:flex-end">
                <button type="button" onclick="closeAdjust()" class="abtn abtn-outline">Cancel</button>
                <button type="submit" class="abtn abtn-amber">Apply</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openAdjust(productId, name, stock) {
    document.getElementById('adjustProductName').textContent = name;
    document.getElementById('adjustCurrentStock').textContent = stock;
    document.getElementById('adjustForm').action = `/admin/stock/${productId}/adjust`;
    const modal = document.getElementById('adjustModal');
    modal.style.display = 'flex';
    modal.querySelector('input[name="adjustment"]').value = '';
    modal.querySelector('input[name="reason"]').value = '';
}
function closeAdjust() { document.getElementById('adjustModal').style.display = 'none'; }
document.getElementById('adjustModal').addEventListener('click', e => { if (e.target === e.currentTarget) closeAdjust(); });
</script>
@endpush
