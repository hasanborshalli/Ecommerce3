@extends('admin.layout')

@section('breadcrumb', 'Settings')

@section('content')

<div class="page-header">
    <h1 class="page-title">Store Settings</h1>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="layout-grid-2-equal">

        <div class="admin-main-column stack-20">

            <div class="card">
                <div class="card-header">
                    <span class="card-title">General</span>
                </div>
                <div class="card-body">
                    <div class="aform-group">
                        <label class="aform-label">Store Name</label>
                        <input type="text" name="site_name" class="aform-control" value="{{ $values['site_name'] }}">
                    </div>

                    <div class="aform-group">
                        <label class="aform-label">Tagline</label>
                        <input type="text" name="site_tagline" class="aform-control"
                            value="{{ $values['site_tagline'] }}">
                    </div>

                    <div class="aform-group">
                        <label class="aform-label">Store Logo</label>
                        <input type="file" name="site_logo" class="aform-control file-input-compact" accept="image/*">
                        <p class="aform-hint">Upload PNG or SVG for best results.</p>
                    </div>

                    <div class="aform-group">
                        <label class="aform-label">Footer About Text</label>
                        <textarea name="footer_about" class="aform-control"
                            rows="3">{{ $values['footer_about'] }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">Contact Information</span>
                </div>
                <div class="card-body">
                    <div class="aform-group">
                        <label class="aform-label">Email</label>
                        <input type="email" name="site_email" class="aform-control" value="{{ $values['site_email'] }}">
                    </div>

                    <div class="aform-group">
                        <label class="aform-label">Phone</label>
                        <input type="text" name="site_phone" class="aform-control" value="{{ $values['site_phone'] }}">
                    </div>

                    <div class="aform-group" style="margin-bottom:0;">
                        <label class="aform-label">Address</label>
                        <input type="text" name="site_address" class="aform-control"
                            value="{{ $values['site_address'] }}">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">Social Media</span>
                </div>
                <div class="card-body">
                    @foreach([
                    'social_instagram' => 'Instagram URL',
                    'social_facebook' => 'Facebook URL',
                    'social_tiktok' => 'TikTok URL',
                    'social_pinterest' => 'Pinterest URL'
                    ] as $key => $label)
                    <div class="aform-group" style="{{ $loop->last ? 'margin-bottom:0;' : '' }}">
                        <label class="aform-label">{{ $label }}</label>
                        <input type="url" name="{{ $key }}" class="aform-control" value="{{ $values[$key] }}"
                            placeholder="https://...">
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <div class="admin-side-column stack-20">

            <div class="card">
                <div class="card-header">
                    <span class="card-title">Currency & Shipping</span>
                </div>
                <div class="card-body">
                    <div class="aform-row-2">
                        <div class="aform-group">
                            <label class="aform-label">Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="aform-control"
                                value="{{ $values['currency_symbol'] }}" placeholder="$">
                        </div>

                        <div class="aform-group">
                            <label class="aform-label">Currency Code</label>
                            <input type="text" name="currency_code" class="aform-control"
                                value="{{ $values['currency_code'] }}" placeholder="USD">
                        </div>
                    </div>

                    <div class="aform-group">
                        <label class="aform-label">Shipping Cost</label>
                        <input type="number" name="shipping_cost" class="aform-control"
                            value="{{ $values['shipping_cost'] }}" step="0.01" min="0">
                    </div>

                    <div class="aform-group" style="margin-bottom:0;">
                        <label class="aform-label">Free Shipping Threshold</label>
                        <input type="number" name="free_shipping_over" class="aform-control"
                            value="{{ $values['free_shipping_over'] }}" step="0.01" min="0">
                        <p class="aform-hint">Orders above this amount get free shipping.</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span class="card-title">Default SEO</span>
                </div>
                <div class="card-body">
                    <div class="aform-group">
                        <label class="aform-label">Default Meta Title</label>
                        <input type="text" name="meta_title" class="aform-control" value="{{ $values['meta_title'] }}"
                            maxlength="160">
                    </div>

                    <div class="aform-group" style="margin-bottom:0;">
                        <label class="aform-label">Default Meta Description</label>
                        <textarea name="meta_description" class="aform-control" rows="3"
                            maxlength="320">{{ $values['meta_description'] }}</textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="abtn abtn-primary btn-full">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z" />
                    <polyline points="17 21 17 13 7 13 7 21" />
                </svg>
                Save All Settings
            </button>

        </div>
    </div>
</form>

@endsection