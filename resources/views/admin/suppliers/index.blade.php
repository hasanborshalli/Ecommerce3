@extends('admin.layout')

@section('title', 'Suppliers')
@section('page_title', 'Suppliers')
@section('breadcrumb') Inventory › Suppliers @endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>Suppliers</h1>
        <p>Manage your product suppliers and vendors.</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.suppliers.create') }}" class="abtn abtn-amber">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Supplier
        </a>
    </div>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Phone</th>
                <th>POs</th>
                <th>Status</th>
                <th style="width:80px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $supplier)
            <tr>
                <td>
                    <div style="font-weight:var(--weight-medium)">{{ $supplier->name }}</div>
                    @if($supplier->notes)
                        <div style="font-size:var(--text-xs);color:var(--admin-muted)">{{ Str::limit($supplier->notes, 50) }}</div>
                    @endif
                </td>
                <td style="font-size:var(--text-sm)">{{ $supplier->contact_person ?? '—' }}</td>
                <td style="font-size:var(--text-sm)">
                    @if($supplier->email)
                        <a href="mailto:{{ $supplier->email }}" style="color:var(--admin-accent)">{{ $supplier->email }}</a>
                    @else
                        <span style="color:var(--admin-muted)">—</span>
                    @endif
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">{{ $supplier->phone ?? '—' }}</td>
                <td style="font-size:var(--text-sm)">
                    <a href="{{ route('admin.purchase_orders.index', ['supplier' => $supplier->id]) }}"
                       style="color:var(--admin-accent)">{{ $supplier->purchase_orders_count }}</a>
                </td>
                <td>
                    @if($supplier->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-neutral">Inactive</span>
                    @endif
                </td>
                <td>
                    <div class="table-actions">
                        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="table-action" title="Edit">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}"
                              onsubmit="return confirm('Delete {{ addslashes($supplier->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="table-action delete" title="Delete">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:var(--sp-10);color:var(--admin-muted)">
                    No suppliers yet.
                    <a href="{{ route('admin.suppliers.create') }}" style="color:var(--admin-accent)">Add your first supplier</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="admin-table-footer">
        <span>{{ $suppliers->total() }} {{ Str::plural('supplier', $suppliers->total()) }}</span>
        {{ $suppliers->links() }}
    </div>
</div>

@endsection
