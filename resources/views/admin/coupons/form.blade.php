@extends('admin.layout')
@section('title', isset($coupon) ? 'Edit Coupon' : 'New Coupon')
@section('page_title', isset($coupon) ? 'Edit Coupon' : 'New Coupon')
@section('breadcrumb')
    <a href="{{ route('admin.coupons.index') }}">Coupons</a> › {{ isset($coupon) ? $coupon->code : 'New' }}
@endsection

@section('content')

<div class="admin-page-header">
    <div class="admin-page-header-left">
        <h1>{{ isset($coupon) ? 'Edit ' . $coupon->code : 'Create Coupon' }}</h1>
    </div>
    <div class="admin-page-actions">
        <a href="{{ route('admin.coupons.index') }}" class="abtn abtn-ghost">← Back</a>
    </div>
</div>

<form method="POST"
      action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}"
      style="max-width:620px">
    @csrf
    @if(isset($coupon)) @method('PUT') @endif

    @if($errors->any())
    <div class="admin-alert alert-danger" style="margin-bottom:var(--sp-5)">
        <ul style="margin:0;padding-left:var(--sp-4)">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="admin-card">
        <div class="admin-card-header"><span class="admin-card-title">Coupon Details</span></div>
        <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-5)">

            <div class="aform-group">
                <label class="aform-label">Code <span style="color:var(--danger)">*</span></label>
                <div style="display:flex;gap:var(--sp-2)">
                    <input type="text" name="code" id="couponCode"
                           class="aform-control @error('code') is-invalid @enderror"
                           value="{{ old('code', isset($coupon) ? $coupon->code : '') }}"
                           placeholder="e.g. SAVE15" style="text-transform:uppercase;font-family:var(--font-mono);font-weight:var(--weight-semibold);letter-spacing:0.05em">
                    @if(!isset($coupon))
                    <button type="button" onclick="generateCode()" class="abtn abtn-outline abtn-sm" style="white-space:nowrap">Generate</button>
                    @endif
                </div>
                @error('code')<div class="aform-error">{{ $message }}</div>@enderror
            </div>

            <div class="aform-group">
                <label class="aform-label">Description</label>
                <input type="text" name="description" class="aform-control"
                       value="{{ old('description', $coupon->description ?? '') }}"
                       placeholder="Short note (shown in admin)">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-4)">
                <div class="aform-group">
                    <label class="aform-label">Discount Type <span style="color:var(--danger)">*</span></label>
                    <select name="type" id="couponType" class="aform-control @error('type') is-invalid @enderror" onchange="handleTypeChange()">
                        <option value="percentage" {{ old('type', $coupon->type ?? '') === 'percentage' ? 'selected':'' }}>Percentage (%)</option>
                        <option value="fixed"      {{ old('type', $coupon->type ?? '') === 'fixed'      ? 'selected':'' }}>Fixed Amount ($)</option>
                        <option value="free_shipping" {{ old('type', $coupon->type ?? '') === 'free_shipping' ? 'selected':'' }}>Free Shipping</option>
                    </select>
                    @error('type')<div class="aform-error">{{ $message }}</div>@enderror
                </div>

                <div class="aform-group" id="valueGroup">
                    <label class="aform-label" id="valueLabel">Value <span style="color:var(--danger)">*</span></label>
                    <div style="position:relative">
                        <span id="valuePrefix" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--admin-muted);font-size:var(--text-sm)"></span>
                        <input type="number" name="value" id="couponValue"
                               class="aform-control @error('value') is-invalid @enderror"
                               value="{{ old('value', $coupon->value ?? '') }}"
                               min="0" step="0.01" style="padding-left:24px">
                    </div>
                    @error('value')<div class="aform-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--sp-4)">
                <div class="aform-group">
                    <label class="aform-label">Minimum Order</label>
                    <div style="position:relative">
                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--admin-muted);font-size:var(--text-sm)">{{ $currencySymbol }}</span>
                        <input type="number" name="min_order_amount" class="aform-control"
                               value="{{ old('min_order_amount', $coupon->min_order_amount ?? '') }}"
                               min="0" step="0.01" placeholder="0.00" style="padding-left:24px">
                    </div>
                </div>
                <div class="aform-group">
                    <label class="aform-label">Max Uses</label>
                    <input type="number" name="max_uses" class="aform-control"
                           value="{{ old('max_uses', $coupon->max_uses ?? '') }}"
                           min="1" placeholder="Unlimited">
                </div>
            </div>

            <div class="aform-group">
                <label class="aform-label">Expiry Date</label>
                <input type="date" name="expires_at" class="aform-control"
                       value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}">
                <div class="aform-hint">Leave empty for no expiry.</div>
            </div>

            <div class="aform-toggle-row">
                <label class="aform-toggle">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }}>
                    <span class="aform-toggle-slider"></span>
                </label>
                <div>
                    <div class="aform-label" style="margin-bottom:0">Active</div>
                    <div class="aform-hint">Inactive coupons cannot be used at checkout.</div>
                </div>
            </div>

        </div>
    </div>

    <div style="display:flex;gap:var(--sp-3);margin-top:var(--sp-5)">
        <button type="submit" class="abtn abtn-blue">
            {{ isset($coupon) ? 'Update Coupon' : 'Create Coupon' }}
        </button>
        <a href="{{ route('admin.coupons.index') }}" class="abtn abtn-ghost">Cancel</a>
    </div>
</form>

@endsection

@push('scripts')
<script>
function handleTypeChange() {
    const type = document.getElementById('couponType').value;
    const vg   = document.getElementById('valueGroup');
    const pre  = document.getElementById('valuePrefix');
    const lbl  = document.getElementById('valueLabel');
    if (type === 'free_shipping') {
        vg.style.display = 'none';
    } else {
        vg.style.display = '';
        pre.textContent = type === 'percentage' ? '%' : '{{ $currencySymbol }}';
        lbl.innerHTML   = 'Value <span style="color:var(--danger)">*</span>';
    }
}
function generateCode() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let code = '';
    for (let i = 0; i < 8; i++) code += chars[Math.floor(Math.random() * chars.length)];
    document.getElementById('couponCode').value = code;
}
// Init on load
handleTypeChange();
</script>
@endpush
