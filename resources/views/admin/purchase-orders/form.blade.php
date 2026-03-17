@extends('admin.layout')

@php $editing = isset($purchaseOrder); @endphp
@section('title', $editing ? 'Edit ' . $purchaseOrder->reference_number : 'New Purchase Order')
@section('page_title', $editing ? 'Edit Purchase Order' : 'New Purchase Order')
@section('breadcrumb')
<a href="{{ route('admin.purchase_orders.index') }}">Purchase Orders</a> ›
{{ $editing ? $purchaseOrder->reference_number : 'New' }}
@endsection

@section('content')

<form method="POST" action="{{ $editing
            ? route('admin.purchase_orders.update', $purchaseOrder)
            : route('admin.purchase_orders.store') }}" id="poForm">
    @csrf
    @if($editing) @method('PUT') @endif

    @php
    $productsJson = $products->map(fn($p) => [
    'id' => $p->id,
    'name' => $p->name,
    'sku' => $p->sku,
    'cost_price' => (float) $p->cost_price,
    'stock' => $p->stock,
    ])->values()->toArray();
    @endphp
    <script>
        const PRODUCTS = @json($productsJson);
    </script>

    <div class="layout-grid-main-aside">
        {{-- LEFT --}}
        <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

            {{-- Line items --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title">Products to Order</span>
                    <button type="button" class="abtn abtn-outline abtn-sm" onclick="addRow()">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        Add Product
                    </button>
                </div>
                <div style="overflow-x:auto">
                    <table class="po-items-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th class="col-product">Product</th>
                                <th class="col-stock">Current Stock</th>
                                <th class="col-qty">Qty to Order</th>
                                <th class="col-cost">Cost / Unit ({{ $currencySymbol }})</th>
                                <th class="col-total">Line Total</th>
                                <th class="col-remove"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @if($editing)
                            @foreach($purchaseOrder->items as $i => $item)
                            <tr class="item-row" data-index="{{ $i }}">
                                <td class="col-product">
                                    <select name="items[{{ $i }}][product_id]" class="product-select"
                                        onchange="onProductChange(this, {{ $i }})" required>
                                        <option value="">Choose product…</option>
                                        @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-cost="{{ $p->cost_price }}"
                                            data-stock="{{ $p->stock }}" {{ $item->product_id == $p->id ? 'selected' :
                                            '' }}>
                                            {{ $p->name }}{{ $p->sku ? ' — ' . $p->sku : '' }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-stock">
                                    <span class="row-stock">
                                        {{ $item->product->stock ?? 0 }}
                                    </span>
                                </td>
                                <td class="col-qty">
                                    <input type="number" name="items[{{ $i }}][qty]" class="row-qty"
                                        value="{{ $item->quantity_ordered }}" min="1" required
                                        oninput="recalcRow({{ $i }}); recalcTotal()">
                                </td>
                                <td class="col-cost">
                                    <input type="number" name="items[{{ $i }}][cost]" class="row-cost"
                                        value="{{ number_format($item->cost_per_unit, 2, '.', '') }}" min="0"
                                        step="0.01" required oninput="recalcRow({{ $i }}); recalcTotal()">
                                </td>
                                <td class="col-total">
                                    <span class="row-total">
                                        {{ $currencySymbol }}{{ number_format($item->total_cost, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" onclick="removeRow(this)"
                                        style="background:none;border:none;cursor:pointer;color:var(--admin-muted);padding:4px">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18" />
                                            <line x1="6" y1="6" x2="18" y2="18" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr id="emptyRow" {{ $editing && $purchaseOrder->items->count() ? 'style=display:none' : ''
                                }}>
                                <td colspan="6"
                                    style="text-align:center;padding:var(--sp-8);color:var(--admin-muted);font-size:var(--text-sm)">
                                    No products added yet — click <strong>Add Product</strong> above.
                                </td>
                            </tr>
                            <tr style="border-top:2px solid var(--admin-border)">
                                <td colspan="4"
                                    style="text-align:right;font-weight:var(--weight-bold);padding:var(--sp-3)">
                                    Order Total
                                </td>
                                <td
                                    style="text-align:right;font-size:var(--text-xl);font-weight:var(--weight-black);color:var(--ink);padding:var(--sp-3)">
                                    <span id="grandTotal">
                                        {{ $currencySymbol }}{{ $editing ? number_format($purchaseOrder->total_cost, 2)
                                        : '0.00' }}
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Notes --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Notes</span></div>
                <div class="admin-card-body">
                    <textarea name="notes" class="aform-control" rows="3"
                        placeholder="Lead time, delivery instructions, internal notes…">{{ old('notes', $editing ? $purchaseOrder->notes : '') }}</textarea>
                </div>
            </div>

        </div>

        {{-- RIGHT --}}
        <div style="display:flex;flex-direction:column;gap:var(--sp-5)">



            {{-- Order details --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Order Details</span></div>
                <div class="admin-card-body">

                    <div class="aform-group">
                        <label class="aform-label" for="supplier_id">Supplier</label>
                        <select id="supplier_id" name="supplier_id" class="aform-control">
                            <option value="">— No supplier —</option>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ old('supplier_id', $editing ? $purchaseOrder->supplier_id :
                                '') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                            @endforeach
                        </select>
                        <span class="aform-hint">
                            <a href="{{ route('admin.suppliers.create') }}" target="_blank"
                                style="color:var(--admin-accent)">+ Add new supplier</a>
                        </span>
                    </div>

                    <div class="aform-group">
                        <label class="aform-label" for="order_date">Order Date <span class="req">*</span></label>
                        <input type="date" id="order_date" name="order_date" class="aform-control"
                            value="{{ old('order_date', $editing ? $purchaseOrder->order_date?->format('Y-m-d') : now()->format('Y-m-d')) }}"
                            required>
                    </div>

                    <div class="aform-group">
                        <label class="aform-label" for="expected_date">Expected Delivery</label>
                        <input type="date" id="expected_date" name="expected_date" class="aform-control"
                            value="{{ old('expected_date', $editing ? $purchaseOrder->expected_date?->format('Y-m-d') : '') }}">
                    </div>

                </div>
            </div>

            {{-- PO reference (edit only) --}}
            @if($editing)
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Reference</span></div>
                <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                    @foreach([
                    ['PO Number', '<code
                        style="font-family:var(--font-mono);font-size:11px;background:var(--admin-bg);padding:2px 6px;border-radius:4px">' . $purchaseOrder->reference_number . '</code>'],
                    ['Status', '<span class="badge ' . $purchaseOrder->status_badge['class'] . '">' .
                        $purchaseOrder->status_badge['label'] . '</span>'],
                    ['Created', $purchaseOrder->created_at->format('M d, Y')],
                    ] as [$label, $value])
                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:var(--text-sm)">
                        <span style="color:var(--admin-muted)">{{ $label }}</span>
                        {!! $value !!}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            {{-- Save / Place Order --}}
            <div class="admin-card">
                <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">

                    <button type="submit" name="status" value="ordered" class="abtn abtn-amber abtn-full abtn-lg">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        Place Order
                    </button>

                    <button type="submit" name="status" value="{{ $editing ? $purchaseOrder->status : 'draft' }}"
                        class="abtn abtn-outline abtn-full">
                        Save as Draft
                    </button>

                    <a href="{{ $editing ? route('admin.purchase_orders.show', $purchaseOrder) : route('admin.purchase_orders.index') }}"
                        class="abtn abtn-ghost abtn-full">
                        Cancel
                    </a>

                    @if($editing)
                    <div style="border-top:1px solid var(--admin-border);padding-top:var(--sp-3)">
                        <button type="button" class="abtn abtn-danger abtn-full abtn-sm"
                            onclick="if(confirm('Delete this purchase order permanently?')) document.getElementById('deletePOForm').submit()">
                            Delete Order
                        </button>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</form>

@if($editing)
<form id="deletePOForm" method="POST" action="{{ route('admin.purchase_orders.destroy', $purchaseOrder) }}"
    style="display:none">
    @csrf @method('DELETE')
</form>
@endif

@endsection

@push('scripts')
<script>
    const CURRENCY = '{{ $currencySymbol }}';
let rowIndex   = {{ $editing ? $purchaseOrder->items->count() : 0 }};

function addRow() {
    document.getElementById('emptyRow').style.display = 'none';
    const idx  = rowIndex++;
    const opts = PRODUCTS.map(p =>
        `<option value="${p.id}" data-cost="${p.cost_price}" data-stock="${p.stock}">
            ${escHtml(p.name)}${p.sku ? ' — ' + escHtml(p.sku) : ''}
        </option>`
    ).join('');

    const tr = document.createElement('tr');
    tr.className     = 'item-row';
    tr.dataset.index = idx;
    tr.innerHTML = `
        <td class="col-product">
            <select name="items[${idx}][product_id]"
                    class="product-select"
                    onchange="onProductChange(this,${idx})" required>
                <option value="">Choose product…</option>
                ${opts}
            </select>
        </td>
        <td class="col-stock"><span class="row-stock">—</span></td>
        <td class="col-qty">
            <input type="number" name="items[${idx}][qty]"
                   class="row-qty" value="1" min="1" required
                   oninput="recalcRow(${idx});recalcTotal()">
        </td>
        <td class="col-cost">
            <input type="number" name="items[${idx}][cost]"
                   class="row-cost" value="0.00" min="0" step="0.01" required
                   oninput="recalcRow(${idx});recalcTotal()">
        </td>
        <td class="col-total">
            <span class="row-total">${CURRENCY}0.00</span>
        </td>
        <td class="col-remove">
            <button type="button" onclick="removeRow(this)"
                    style="background:none;border:none;cursor:pointer;color:var(--danger);font-size:18px;line-height:1">
                ×
            </button>
        </td>
    `;
    document.getElementById('itemsBody').appendChild(tr);
}

function removeRow(btn) {
    btn.closest('tr').remove();
    recalcTotal();
    if (!document.querySelectorAll('.item-row').length) {
        document.getElementById('emptyRow').style.display = '';
    }
}

function onProductChange(select, idx) {
    const opt  = select.options[select.selectedIndex];
    const row  = select.closest('tr');
    row.querySelector('.row-stock').textContent = opt.dataset.stock ?? '—';
    row.querySelector('.row-cost').value        = parseFloat(opt.dataset.cost || 0).toFixed(2);
    recalcRow(idx);
    recalcTotal();
}

function recalcRow(idx) {
    const row = document.querySelector(`.item-row[data-index="${idx}"]`);
    if (!row) return;
    const qty  = parseFloat(row.querySelector('.row-qty').value)  || 0;
    const cost = parseFloat(row.querySelector('.row-cost').value) || 0;
    row.querySelector('.row-total').textContent = CURRENCY + (qty * cost).toFixed(2);
}

function recalcTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty  = parseFloat(row.querySelector('.row-qty')?.value)  || 0;
        const cost = parseFloat(row.querySelector('.row-cost')?.value) || 0;
        total += qty * cost;
    });
    document.getElementById('grandTotal').textContent = CURRENCY + total.toFixed(2);
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Init existing rows on edit
document.querySelectorAll('.item-row').forEach(row => recalcRow(parseInt(row.dataset.index)));
recalcTotal();

// Guard: prevent submit with zero rows
document.getElementById('poForm').addEventListener('submit', function(e) {
    if (!document.querySelectorAll('.item-row').length) {
        e.preventDefault();
        alert('Please add at least one product before saving.');
    }
});
</script>
@endpush