@extends('layouts.app')

@section('title', 'Addresses — ' . $siteName)

@section('content')
<div class="container">
<div class="account-layout">

    @include('account.partials.nav', ['active' => 'addresses'])

    <div class="account-main">
        <div class="account-section">

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--sp-6)">
                <h1 class="account-section-title">Addresses</h1>
                <button type="button" class="btn btn-primary" onclick="toggleAddForm()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add Address
                </button>
            </div>

            @include('partials.flash')

            {{-- Add form (hidden by default) --}}
            <div id="addAddressForm" style="display:none;margin-bottom:var(--sp-6)">
                <div style="border:1px solid var(--border);border-radius:var(--radius-xl);padding:var(--sp-6)">
                    <h2 style="font-size:var(--text-base);font-weight:var(--weight-semibold);margin-bottom:var(--sp-5)">Add New Address</h2>
                    <form action="{{ route('account.addresses.store') }}" method="POST">
                        @csrf
                        @include('account.partials.address-form', ['addr' => null])
                        <div style="display:flex;gap:var(--sp-3);margin-top:var(--sp-5)">
                            <button type="submit" class="btn btn-primary">Save Address</button>
                            <button type="button" class="btn btn-outline" onclick="toggleAddForm()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Address cards --}}
            @if($addresses->count())
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:var(--sp-4)">
                @foreach($addresses as $addr)
                <div class="address-card{{ $addr->is_default ? ' is-default' : '' }}" id="addrCard{{ $addr->id }}">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:var(--sp-2)">
                        <div style="font-weight:var(--weight-semibold);font-size:var(--text-sm)">
                            {{ $addr->label ?: 'Address' }}
                        </div>
                        @if($addr->is_default)
                        <span class="address-default-badge">Default</span>
                        @endif
                    </div>

                    <div style="font-size:var(--text-sm);color:var(--text-secondary);line-height:var(--leading-relaxed)">
                        {{ $addr->full_name ?: session('customer_name') }}<br>
                        {{ $addr->address_line1 }}
                        @if($addr->address_line2) <br>{{ $addr->address_line2 }} @endif
                        <br>{{ $addr->city }}
                        @if($addr->state) , {{ $addr->state }} @endif
                        @if($addr->postal_code)  {{ $addr->postal_code }} @endif
                        @if($addr->country) <br>{{ $addr->country }} @endif
                    </div>

                    @if($addr->phone)
                    <div style="font-size:var(--text-xs);color:var(--text-muted);margin-top:var(--sp-2)">{{ $addr->phone }}</div>
                    @endif

                    <div style="display:flex;gap:var(--sp-2);margin-top:var(--sp-4);flex-wrap:wrap">
                        <button type="button" class="btn btn-sm btn-outline"
                                onclick="toggleEditForm({{ $addr->id }})">
                            Edit
                        </button>
                        @if(!$addr->is_default)
                        <form action="{{ route('account.addresses.default', $addr) }}" method="POST" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline">Set Default</button>
                        </form>
                        <form action="{{ route('account.addresses.destroy', $addr) }}" method="POST" style="display:inline"
                              onsubmit="return confirm('Remove this address?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-ghost" style="color:var(--danger)">Remove</button>
                        </form>
                        @endif
                    </div>

                    {{-- Inline edit form --}}
                    <div id="editForm{{ $addr->id }}" style="display:none;margin-top:var(--sp-5);border-top:1px solid var(--border);padding-top:var(--sp-5)">
                        <form action="{{ route('account.addresses.update', $addr) }}" method="POST">
                            @csrf @method('PATCH')
                            @include('account.partials.address-form', ['addr' => $addr])
                            <div style="display:flex;gap:var(--sp-3);margin-top:var(--sp-4)">
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                <button type="button" class="btn btn-outline btn-sm" onclick="toggleEditForm({{ $addr->id }})">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>

            @else
            <div style="text-align:center;padding:var(--sp-10) 0;color:var(--text-muted)">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" style="margin:0 auto var(--sp-4);display:block;opacity:0.35">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                <p style="font-size:var(--text-sm)">No saved addresses yet.</p>
                <button type="button" class="btn btn-primary" onclick="toggleAddForm()" style="margin-top:var(--sp-4)">Add Your First Address</button>
            </div>
            @endif

        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
function toggleAddForm() {
    const el = document.getElementById('addAddressForm');
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
    if (el.style.display === 'block') el.scrollIntoView({ behavior:'smooth', block:'nearest' });
}
function toggleEditForm(id) {
    const el = document.getElementById('editForm' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
// If there are validation errors, re-open the form
@if($errors->any())
document.getElementById('addAddressForm').style.display = 'block';
@endif
</script>
@endpush
