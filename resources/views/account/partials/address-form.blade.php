{{--
    Partial: account/partials/address-form
    Usage:   @include('account.partials.address-form', ['addr' => $address_or_null])
    $addr    — CustomerAddress model or null (add mode)
--}}
<div style="display:flex;flex-direction:column;gap:var(--sp-4)">

    <div class="form-row-2">
        <div class="form-group">
            <label class="form-label">Label</label>
            <input type="text" name="label"
                   class="form-control"
                   value="{{ old('label', $addr?->label) }}"
                   placeholder="Home, Work…">
        </div>
        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name"
                   class="form-control"
                   value="{{ old('full_name', $addr?->full_name ?? session('customer_name')) }}"
                   placeholder="Name on delivery">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Address Line 1 <span class="req">*</span></label>
        <input type="text" name="address_line1"
               class="form-control{{ $errors->has('address_line1') ? ' error' : '' }}"
               value="{{ old('address_line1', $addr?->address_line1) }}"
               placeholder="Street address" required>
        @error('address_line1') <span class="form-error">{{ $message }}</span> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Address Line 2</label>
        <input type="text" name="address_line2"
               class="form-control"
               value="{{ old('address_line2', $addr?->address_line2) }}"
               placeholder="Apartment, floor, suite…">
    </div>

    <div class="form-row-2">
        <div class="form-group">
            <label class="form-label">City <span class="req">*</span></label>
            <input type="text" name="city"
                   class="form-control{{ $errors->has('city') ? ' error' : '' }}"
                   value="{{ old('city', $addr?->city) }}"
                   placeholder="City" required>
            @error('city') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
            <label class="form-label">State / Region</label>
            <input type="text" name="state"
                   class="form-control"
                   value="{{ old('state', $addr?->state) }}"
                   placeholder="State">
        </div>
    </div>

    <div class="form-row-2">
        <div class="form-group">
            <label class="form-label">Postal Code</label>
            <input type="text" name="postal_code"
                   class="form-control"
                   value="{{ old('postal_code', $addr?->postal_code) }}"
                   placeholder="ZIP / Postal code">
        </div>
        <div class="form-group">
            <label class="form-label">Country</label>
            <input type="text" name="country"
                   class="form-control"
                   value="{{ old('country', $addr?->country) }}"
                   placeholder="Country">
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Phone</label>
        <input type="tel" name="phone"
               class="form-control"
               value="{{ old('phone', $addr?->phone) }}"
               placeholder="+1 555 000 0000">
    </div>

    <div style="display:flex;align-items:center;gap:var(--sp-2)">
        <input type="checkbox" id="is_default{{ $addr?->id }}" name="is_default" value="1"
               style="width:16px;height:16px;accent-color:var(--amber)"
               {{ old('is_default', $addr?->is_default) ? 'checked' : '' }}>
        <label for="is_default{{ $addr?->id }}" style="font-size:var(--text-sm);color:var(--text-secondary);cursor:pointer">
            Set as default address
        </label>
    </div>

</div>
