@extends('admin.layout')

@section('title', 'PO ' . $purchaseOrder->reference_number)
@section('page_title', $purchaseOrder->reference_number)
@section('breadcrumb')
<a href="{{ route('admin.purchase_orders.index') }}">Purchase Orders</a>
› {{ $purchaseOrder->reference_number }}
@endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1 style="font-family:var(--font-mono)">{{ $purchaseOrder->reference_number }}</h1>
        <p style="display:flex;align-items:center;gap:var(--sp-3)">
            @php $b = $purchaseOrder->status_badge; @endphp
            <span class="badge {{ $b['class'] }}">{{ $b['label'] }}</span>
            <span style="color:var(--admin-muted);font-size:var(--text-sm)">
                Created {{ $purchaseOrder->created_at->format('M d, Y') }}
            </span>
        </p>
    </div>
    <div class="admin-page-actions">
        @if($purchaseOrder->status === 'ordered')
        <form method="POST" action="{{ route('admin.purchase_orders.receive', $purchaseOrder) }}"
            onsubmit="return confirm('Mark as received? This will add stock to all products and cannot be undone.')">
            @csrf
            <button type="submit" class="abtn abtn-amber abtn-lg">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                Mark as Received
            </button>
        </form>
        @endif
        @if(in_array($purchaseOrder->status, ['draft','ordered']))
        <a href="{{ route('admin.purchase_orders.edit', $purchaseOrder) }}" class="abtn abtn-outline">
            Edit
        </a>
        @endif
        <a href="{{ route('admin.purchase_orders.index') }}" class="abtn abtn-ghost">← Back</a>
    </div>
</div>

{{-- Received success banner --}}
@if($purchaseOrder->status === 'received')
<div
    style="background:var(--success-bg);border:1px solid var(--success-border);border-radius:var(--radius-lg);padding:var(--sp-4) var(--sp-5);display:flex;align-items:center;gap:var(--sp-3);margin-bottom:var(--sp-5)">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
        <polyline points="22 4 12 14.01 9 11.01" />
    </svg>
    <div>
        <div style="font-weight:var(--weight-semibold);color:var(--success)">Stock received</div>
        <div style="font-size:var(--text-sm);color:var(--admin-muted)">
            All product quantities and costs were updated on {{ $purchaseOrder->received_date?->format('M d, Y') ??
            'record' }}.
        </div>
    </div>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 300px;gap:var(--sp-5);align-items:start">

    {{-- LEFT: items table --}}
    <div style="display:flex;flex-direction:column;gap:var(--sp-5);min-width:0;overflow:hidden">

        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">Line Items</span>
                <span style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $purchaseOrder->items->sum('quantity_ordered') }} units across
                    {{ $purchaseOrder->items->count() }} {{ Str::plural('product', $purchaseOrder->items->count()) }}
                </span>
            </div>
            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-right">Cost / Unit</th>
                            <th class="text-right">Qty Ordered</th>
                            <th class="text-right">Qty Received</th>
                            <th class="text-right">Line Total</th>
                            @if($purchaseOrder->status === 'received')
                            <th>Stock Impact</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $item)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:var(--sp-3)">
                                    @if($item->product?->main_image)
                                    <img src="{{ Storage::url($item->product->main_image) }}"
                                        style="width:36px;height:36px;object-fit:contain;border:1px solid var(--admin-border);border-radius:var(--radius-sm);flex-shrink:0">
                                    @endif
                                    <div>
                                        <div style="font-size:var(--text-sm);font-weight:var(--weight-medium)">
                                            {{ $item->product->name ?? '(deleted product)' }}
                                        </div>
                                        @if($item->product?->sku)
                                        <div
                                            style="font-size:var(--text-xs);color:var(--admin-muted);font-family:var(--font-mono)">
                                            {{ $item->product->sku }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-right" style="font-size:var(--text-sm)">
                                {{ $currencySymbol }}{{ number_format($item->cost_per_unit, 2) }}
                            </td>
                            <td class="text-right" style="font-size:var(--text-sm)">
                                {{ $item->quantity_ordered }}
                            </td>
                            <td class="text-right" style="font-size:var(--text-sm)">
                                @if($item->quantity_received > 0)
                                <span class="profit-positive">{{ $item->quantity_received }}</span>
                                @else
                                <span style="color:var(--admin-muted)">0</span>
                                @endif
                            </td>
                            <td class="text-right" style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                                {{ $currencySymbol }}{{ number_format($item->total_cost, 2) }}
                            </td>
                            @if($purchaseOrder->status === 'received')
                            <td>
                                <span style="font-size:var(--text-xs);color:var(--success)">
                                    +{{ $item->quantity_received }} added
                                </span>
                                <div style="font-size:10px;color:var(--admin-muted)">
                                    Cost → {{ $currencySymbol }}{{ number_format($item->cost_per_unit, 2) }}
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="{{ $purchaseOrder->status === 'received' ? 4 : 3 }}"
                                style="text-align:right;font-weight:var(--weight-bold);padding:var(--sp-3)">
                                Total Cost
                            </td>
                            <td class="text-right"
                                style="font-size:var(--text-lg);font-weight:var(--weight-black);color:var(--ink);padding:var(--sp-3)">
                                {{ $currencySymbol }}{{ number_format($purchaseOrder->total_cost, 2) }}
                            </td>
                            @if($purchaseOrder->status === 'received') <td></td> @endif
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Notes --}}
        @if($purchaseOrder->notes)
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Notes</span></div>
            <div class="admin-card-body">
                <p
                    style="font-size:var(--text-sm);color:var(--admin-muted);line-height:var(--leading-relaxed);white-space:pre-wrap">
                    {{ $purchaseOrder->notes }}</p>
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT: sidebar --}}
    <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

        {{-- Summary --}}
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Summary</span></div>
            <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                @foreach([
                ['PO Number', '<code
                    style="font-family:var(--font-mono);font-size:11px;background:var(--admin-bg);padding:2px 6px;border-radius:4px">' . $purchaseOrder->reference_number . '</code>'],
                ['Status', '<span class="badge ' . $purchaseOrder->status_badge['class'] . '">' .
                    $purchaseOrder->status_badge['label'] . '</span>'],
                ['Total Cost', $currencySymbol . number_format($purchaseOrder->total_cost, 2)],
                ['Items', $purchaseOrder->items->count() . ' ' . Str::plural('product',
                $purchaseOrder->items->count())],
                ['Units', $purchaseOrder->items->sum('quantity_ordered') . ' units'],
                ] as [$label, $value])
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:var(--text-sm)">
                    <span style="color:var(--admin-muted)">{{ $label }}</span>
                    {!! $value !!}
                </div>
                @endforeach
            </div>
        </div>

        {{-- Dates --}}
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Dates</span></div>
            <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                @foreach([
                ['Order Date', $purchaseOrder->order_date->format('M d, Y')],
                ['Expected', $purchaseOrder->expected_date?->format('M d, Y') ?? '—'],
                ['Received', $purchaseOrder->received_date?->format('M d, Y') ?? '—'],
                ] as [$label, $value])
                <div style="display:flex;justify-content:space-between;font-size:var(--text-sm)">
                    <span style="color:var(--admin-muted)">{{ $label }}</span>
                    <span>{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Supplier --}}
        @if($purchaseOrder->supplier)
        <div class="admin-card">
            <div class="admin-card-header">
                <span class="admin-card-title">Supplier</span>
                <a href="{{ route('admin.suppliers.edit', $purchaseOrder->supplier) }}"
                    style="font-size:var(--text-xs);color:var(--admin-accent)">Edit</a>
            </div>
            <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-2)">
                <div style="font-weight:var(--weight-medium)">{{ $purchaseOrder->supplier->name }}</div>
                @if($purchaseOrder->supplier->contact_person)
                <div style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $purchaseOrder->supplier->contact_person }}
                </div>
                @endif
                @if($purchaseOrder->supplier->email)
                <a href="mailto:{{ $purchaseOrder->supplier->email }}"
                    style="font-size:var(--text-sm);color:var(--admin-accent)">
                    {{ $purchaseOrder->supplier->email }}
                </a>
                @endif
                @if($purchaseOrder->supplier->phone)
                <div style="font-size:var(--text-sm);color:var(--admin-muted)">
                    {{ $purchaseOrder->supplier->phone }}
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Quick actions --}}
        <div class="admin-card">
            <div class="admin-card-header"><span class="admin-card-title">Actions</span></div>
            <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-2)">
                @if($purchaseOrder->status === 'ordered')
                <form method="POST" action="{{ route('admin.purchase_orders.receive', $purchaseOrder) }}"
                    onsubmit="return confirm('Mark as received? Stock will be updated immediately.')">
                    @csrf
                    <button type="submit" class="abtn abtn-amber abtn-full">
                        ✓ Mark as Received
                    </button>
                </form>
                @endif
                @if(in_array($purchaseOrder->status, ['draft','ordered']))
                <a href="{{ route('admin.purchase_orders.edit', $purchaseOrder) }}" class="abtn abtn-outline abtn-full">
                    Edit Order
                </a>
                @endif
                <a href="{{ route('admin.stock.index') }}" class="abtn abtn-ghost abtn-full">
                    View Stock Levels
                </a>
            </div>
        </div>

    </div>
</div>

@endsection