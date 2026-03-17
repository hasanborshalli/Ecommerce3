@extends('admin.layout')

@php $editing = isset($supplier); @endphp
@section('title', $editing ? 'Edit Supplier' : 'New Supplier')
@section('page_title', $editing ? 'Edit Supplier' : 'New Supplier')
@section('breadcrumb')
<a href="{{ route('admin.suppliers.index') }}">Suppliers</a> ›
{{ $editing ? $supplier->name : 'New' }}
@endsection

@section('content')

<form method="POST"
    action="{{ $editing ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="layout-grid-main-aside">
        <div style="display:flex;flex-direction:column;gap:var(--sp-5)">
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Supplier Details</span></div>
                <div class="admin-card-body">

                    <div class="aform-row-2">
                        <div class="aform-group">
                            <label class="aform-label" for="name">Company Name <span class="req">*</span></label>
                            <input type="text" id="name" name="name" class="aform-control"
                                value="{{ old('name', $supplier->name ?? '') }}" required>
                        </div>
                        <div class="aform-group">
                            <label class="aform-label" for="contact_person">Contact Person</label>
                            <input type="text" id="contact_person" name="contact_person" class="aform-control"
                                value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
                        </div>
                    </div>

                    <div class="aform-row-2">
                        <div class="aform-group">
                            <label class="aform-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="aform-control"
                                value="{{ old('email', $supplier->email ?? '') }}">
                        </div>
                        <div class="aform-group">
                            <label class="aform-label" for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" class="aform-control"
                                value="{{ old('phone', $supplier->phone ?? '') }}">
                        </div>
                    </div>

                    <div class="aform-group">
                        <label class="aform-label" for="address">Address</label>
                        <textarea id="address" name="address" class="aform-control"
                            rows="2">{{ old('address', $supplier->address ?? '') }}</textarea>
                    </div>

                    <div class="aform-group">
                        <label class="aform-label" for="notes">Notes</label>
                        <textarea id="notes" name="notes" class="aform-control" rows="3"
                            placeholder="Payment terms, lead times, etc.">{{ old('notes', $supplier->notes ?? '') }}</textarea>
                    </div>

                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Status</span></div>
                <div class="admin-card-body">
                    <label class="toggle-wrap">
                        <div class="toggle">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $editing ?
                                ($supplier->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </div>
                        <span class="toggle-label">Active supplier</span>
                    </label>
                </div>
            </div>
            <div class="admin-card">
                <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                    <button type="submit" class="abtn abtn-amber abtn-full">
                        {{ $editing ? 'Save Changes' : 'Create Supplier' }}
                    </button>
                    <a href="{{ route('admin.suppliers.index') }}" class="abtn abtn-outline abtn-full">Cancel</a>
                </div>
            </div>
        </div>

    </div>
</form>

@endsection