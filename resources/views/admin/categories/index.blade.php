@extends('admin.layout')

@section('title', 'Categories')
@section('page_title', 'Categories')
@section('breadcrumb') Catalogue › Categories @endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>Categories</h1>
        <p>Organise your products into collections.</p>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.categories.create') }}" class="abtn abtn-amber">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Category
        </a>
    </div>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width:60px"></th>
                <th>Name</th>
                <th>Slug</th>
                <th>Products</th>
                <th>Sort</th>
                <th>Status</th>
                <th style="width:100px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td>
                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}"
                             style="width:44px;height:44px;object-fit:cover;border-radius:var(--radius);border:1px solid var(--admin-border)">
                    @else
                        <div style="width:44px;height:44px;background:var(--admin-bg);border-radius:var(--radius);border:1px solid var(--admin-border);display:flex;align-items:center;justify-content:center">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                        </div>
                    @endif
                </td>
                <td style="font-weight:var(--weight-medium)">{{ $category->name }}</td>
                <td style="font-size:var(--text-xs);color:var(--admin-muted);font-family:var(--font-mono)">{{ $category->slug }}</td>
                <td style="font-size:var(--text-sm)">
                    <a href="{{ route('admin.products.index', ['category' => $category->id]) }}"
                       style="color:var(--admin-accent)">{{ $category->products_count }}</a>
                </td>
                <td style="font-size:var(--text-sm);color:var(--admin-muted)">{{ $category->sort_order }}</td>
                <td>
                    @if($category->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-neutral">Hidden</span>
                    @endif
                </td>
                <td>
                    <div class="table-actions">
                        <a href="{{ route('admin.categories.edit', $category) }}"
                           class="table-action" title="Edit">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                              onsubmit="return confirm('Delete {{ addslashes($category->name) }}? Products in this category cannot be deleted.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="table-action delete" title="Delete">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:var(--sp-12);color:var(--admin-muted)">
                    No categories yet.
                    <a href="{{ route('admin.categories.create') }}" style="color:var(--admin-accent)">Add one</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="admin-table-footer">
        <span>{{ $categories->total() }} {{ Str::plural('category', $categories->total()) }}</span>
        {{ $categories->links() }}
    </div>
</div>

@endsection
