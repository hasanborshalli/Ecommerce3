@extends('admin.layout')

@section('title', 'Settings')
@section('page_title', 'Settings')
@section('breadcrumb') Admin › Settings @endsection

@section('content')

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf

    <div style="display:grid;grid-template-columns:1fr 320px;gap:var(--sp-5);align-items:start">

        {{-- ── Left: settings sections ──────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

            {{-- Store info --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Store Information</span></div>
                <div class="admin-card-body">
                    <div class="aform-row-2">
                        <div class="aform-group">
                            <label class="aform-label" for="site_name">Store Name <span class="req">*</span></label>
                            <input type="text" id="site_name" name="site_name" class="aform-control"
                                   value="{{ old('site_name', $settings['site_name'] ?? '') }}" required>
                        </div>
                        <div class="aform-group">
                            <label class="aform-label" for="site_tagline">Tagline</label>
                            <input type="text" id="site_tagline" name="site_tagline" class="aform-control"
                                   value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}"
                                   placeholder="e.g. Built to Last. Designed to Impress.">
                            <span class="aform-hint">Short phrase shown under your store name in the browser tab, SEO meta tags, and footer. Keep it under 10 words.</span>
                        </div>
                    </div>
                    <div class="aform-row-2">
                        <div class="aform-group">
                            <label class="aform-label" for="site_email">Contact Email</label>
                            <input type="email" id="site_email" name="site_email" class="aform-control"
                                   value="{{ old('site_email', $settings['site_email'] ?? '') }}">
                        </div>
                        <div class="aform-group">
                            <label class="aform-label" for="site_phone">Phone</label>
                            <input type="text" id="site_phone" name="site_phone" class="aform-control"
                                   value="{{ old('site_phone', $settings['site_phone'] ?? '') }}">
                        </div>
                    </div>
                    <div class="aform-group">
                        <label class="aform-label" for="site_address">Address</label>
                        <input type="text" id="site_address" name="site_address" class="aform-control"
                               value="{{ old('site_address', $settings['site_address'] ?? '') }}">
                    </div>
                    <div class="aform-group">
                        <label class="aform-label" for="footer_about">Footer About Text</label>
                        <textarea id="footer_about" name="footer_about" class="aform-control" rows="2"
                                  placeholder="Short brand description in footer">{{ old('footer_about', $settings['footer_about'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Commerce --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Commerce</span></div>
                <div class="admin-card-body">
                    <div class="aform-row-3">
                        <div class="aform-group">
                            <label class="aform-label" for="currency_symbol">Currency Symbol</label>
                            <input type="text" id="currency_symbol" name="currency_symbol"
                                   class="aform-control" maxlength="5"
                                   value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '$') }}">
                        </div>
                        <div class="aform-group">
                            <label class="aform-label" for="free_shipping_over">Free Shipping Over</label>
                            <input type="number" id="free_shipping_over" name="free_shipping_over"
                                   class="aform-control" step="0.01" min="0"
                                   value="{{ old('free_shipping_over', $settings['free_shipping_over'] ?? '150') }}">
                        </div>
                        <div class="aform-group">
                            <label class="aform-label" for="shipping_cost">Shipping Cost</label>
                            <input type="number" id="shipping_cost" name="shipping_cost"
                                   class="aform-control" step="0.01" min="0"
                                   value="{{ old('shipping_cost', $settings['shipping_cost'] ?? '9') }}">
                        </div>
                    </div>
                    <div class="aform-hint" style="margin-top:var(--sp-1)">
                        Set shipping cost to 0 if you want flat-rate free shipping for all orders.
                    </div>
                </div>
            </div>

            {{-- Announcement bar --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Announcement Bar</span></div>
                <div class="admin-card-body">
                    <div class="aform-group">
                        <label class="aform-label" for="announcement_text">Message</label>
                        <input type="text" id="announcement_text" name="announcement_text" class="aform-control"
                               value="{{ old('announcement_text', $settings['announcement_text'] ?? '') }}"
                               placeholder="e.g. Free shipping on orders over $150">
                        <span class="aform-hint">Leave blank to show the default free-shipping message.</span>
                    </div>
                    <div class="aform-row-2">
                        <div class="aform-group">
                            <label class="aform-label" for="announcement_link">Link URL</label>
                            <input type="text" id="announcement_link" name="announcement_link" class="aform-control"
                                   value="{{ old('announcement_link', $settings['announcement_link'] ?? '') }}"
                                   placeholder="https://…">
                        </div>
                        <div class="aform-group">
                            <label class="aform-label" for="announcement_link_text">Link Text</label>
                            <input type="text" id="announcement_link_text" name="announcement_link_text" class="aform-control"
                                   value="{{ old('announcement_link_text', $settings['announcement_link_text'] ?? '') }}"
                                   placeholder="e.g. Shop now">
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">SEO</span></div>
                <div class="admin-card-body">
                    <div class="aform-group">
                        <label class="aform-label" for="meta_description">Default Meta Description</label>
                        <textarea id="meta_description" name="meta_description" class="aform-control" rows="2"
                                  placeholder="Default description for pages without their own meta">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Social --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Social Media</span></div>
                <div class="admin-card-body">
                    @foreach([
                        ['social_instagram', 'Instagram URL', 'https://instagram.com/…'],
                        ['social_facebook',  'Facebook URL',  'https://facebook.com/…'],
                        ['social_twitter',   'Twitter / X URL','https://x.com/…'],
                    ] as [$name, $label, $placeholder])
                    <div class="aform-group">
                        <label class="aform-label" for="{{ $name }}">{{ $label }}</label>
                        <input type="url" id="{{ $name }}" name="{{ $name }}" class="aform-control"
                               value="{{ old($name, $settings[$name] ?? '') }}"
                               placeholder="{{ $placeholder }}">
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── Right: actions + logo ────────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

            <div class="admin-card">
                <div class="admin-card-body">
                    <button type="submit" class="abtn abtn-amber abtn-full abtn-lg">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save Settings
                    </button>
                </div>
            </div>

            {{-- Logo --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Store Logo</span></div>
                <div class="admin-card-body">
                    @if(!empty($settings['site_logo']))
                    <div id="logoPreviewWrap" style="margin-bottom:var(--sp-3);padding:var(--sp-3);background:var(--ink);border-radius:var(--radius);text-align:center">
                        <img src="{{ Storage::url($settings['site_logo']) }}" id="logoPreview"
                             style="max-height:60px;max-width:180px;object-fit:contain">
                    </div>
                    @else
                    <div id="logoPreviewWrap" style="display:none;margin-bottom:var(--sp-3)">
                        <img id="logoPreview" src=""
                             style="max-height:60px;max-width:100%;object-fit:contain;border:1px solid var(--admin-border);border-radius:var(--radius);padding:var(--sp-2)">
                    </div>
                    @endif

                    <label class="image-upload-area" for="site_logo" style="position:relative">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--amber)" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <span style="font-size:var(--text-sm);font-weight:var(--weight-medium)">
                            {{ !empty($settings['site_logo']) ? 'Replace logo' : 'Upload logo' }}
                        </span>
                        <span style="font-size:var(--text-xs);color:var(--admin-muted)">PNG with transparency recommended</span>
                        <input type="file" id="site_logo" name="site_logo" accept="image/*"
                               style="display:none" onchange="previewLogo(this)">
                    </label>

                    <div class="aform-hint" style="margin-top:var(--sp-2)">
                        Leave blank to use the text store name in the nav.
                    </div>
                </div>
            </div>

            {{-- Admin credentials reminder --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Admin Access</span></div>
                <div class="admin-card-body">
                    <p style="font-size:var(--text-sm);color:var(--admin-muted);line-height:var(--leading-relaxed)">
                        Admin credentials are set via environment variables in your <code style="background:var(--admin-bg);padding:1px 5px;border-radius:3px;font-size:var(--text-xs)">.env</code> file:
                    </p>
                    <div style="background:var(--admin-bg);border-radius:var(--radius);padding:var(--sp-3);margin-top:var(--sp-3);font-size:var(--text-xs);font-family:var(--font-mono);line-height:2">
                        ADMIN_EMAIL=admin@example.com<br>
                        ADMIN_PASSWORD=your_password
                    </div>
                </div>
            </div>

        </div>
    </div>

</form>

@endsection

@push('scripts')
<script>
function previewLogo(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('logoPreview').src = e.target.result;
        document.getElementById('logoPreviewWrap').style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
