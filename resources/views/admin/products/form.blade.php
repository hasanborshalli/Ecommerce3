@extends('admin.layout')

@php $editing = isset($product); @endphp
@section('title', $editing ? 'Edit Product' : 'New Product')
@section('page_title', $editing ? 'Edit Product' : 'New Product')
@section('breadcrumb')
    <a href="{{ route('admin.products.index') }}">Products</a> ›
    {{ $editing ? $product->name : 'New' }}
@endsection

@section('content')

<form method="POST"
      action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}"
      enctype="multipart/form-data"
      id="productForm">
    @csrf
    @if($editing) @method('PUT') @endif

    @if($errors->any())
    <div style="background:var(--danger-bg);border:1px solid var(--danger-border);border-radius:var(--radius-lg);padding:var(--sp-4) var(--sp-5);margin-bottom:var(--sp-5);display:flex;gap:var(--sp-3);align-items:flex-start">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2" style="flex-shrink:0;margin-top:2px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
            <div style="font-size:var(--text-sm);font-weight:var(--weight-semibold);color:var(--danger);margin-bottom:var(--sp-1)">Please fix the following errors:</div>
            <ul style="margin:0;padding-left:var(--sp-4);font-size:var(--text-sm);color:var(--danger)">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 340px;gap:var(--sp-5);align-items:start">

        {{-- ── Left column ──────────────────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

            {{-- Basic info --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Product Information</span></div>
                <div class="admin-card-body">

                    <div class="aform-group">
                        <label class="aform-label" for="name">Product Name <span class="req">*</span></label>
                        <input type="text" id="name" name="name"
                               class="aform-control{{ $errors->has('name') ? ' error' : '' }}"
                               value="{{ old('name', $product->name ?? '') }}"
                               required oninput="autoSlug(this.value)">
                        @error('name') <span class="aform-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="aform-row-2">
                        <div class="aform-group">
                            <label class="aform-label" for="slug">Slug (URL)</label>
                            <input type="text" id="slug" name="slug"
                                   class="aform-control{{ $errors->has('slug') ? ' error' : '' }}"
                                   value="{{ old('slug', $product->slug ?? '') }}"
                                   placeholder="auto-generated">
                            @error('slug') <span class="aform-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="aform-group">
                            <label class="aform-label" for="sku">SKU</label>
                            <input type="text" id="sku" name="sku"
                                   class="aform-control{{ $errors->has('sku') ? ' error' : '' }}"
                                   value="{{ old('sku', $product->sku ?? '') }}"
                                   placeholder="e.g. VLT-0001">
                            @error('sku') <span class="aform-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="aform-group">
                        <label class="aform-label" for="short_description">Short Description</label>
                        <textarea id="short_description" name="short_description"
                                  class="aform-control{{ $errors->has('short_description') ? ' error' : '' }}" rows="2"
                                  placeholder="One-line tagline shown on product cards">{{ old('short_description', $product->short_description ?? '') }}</textarea>
                        @error('short_description') <span class="aform-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="aform-group">
                        <label class="aform-label" for="description">Full Description</label>
                        <textarea id="description" name="description"
                                  class="aform-control{{ $errors->has('description') ? ' error' : '' }}" rows="6"
                                  placeholder="Detailed description — HTML allowed">{{ old('description', $product->description ?? '') }}</textarea>
                        @error('description') <span class="aform-error">{{ $message }}</span> @enderror
                    </div>

                </div>
            </div>

            {{-- Pricing --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Pricing</span></div>
                <div class="admin-card-body">

                    <div class="aform-row-3">
                        <div class="aform-group">
                            <label class="aform-label" for="price">Selling Price <span class="req">*</span></label>
                            <input type="number" id="price" name="price" step="0.01" min="0"
                                   class="aform-control{{ $errors->has('price') ? ' error' : '' }}"
                                   value="{{ old('price', $product->price ?? '') }}"
                                   required oninput="calcMargin()">
                            @error('price') <span class="aform-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="aform-group">
                            <label class="aform-label" for="sale_price">Sale Price</label>
                            <input type="number" id="sale_price" name="sale_price" step="0.01" min="0"
                                   class="aform-control{{ $errors->has('sale_price') ? ' error' : '' }}"
                                   value="{{ old('sale_price', $product->sale_price ?? '') }}"
                                   placeholder="Leave blank if not on sale">
                            @error('sale_price') <span class="aform-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="aform-group">
                            <label class="aform-label" for="cost_price">
                                Cost Price <span class="req">*</span>
                                <span style="font-size:10px;color:var(--admin-muted);font-weight:normal">(your cost)</span>
                            </label>
                            <input type="number" id="cost_price" name="cost_price" step="0.01" min="0"
                                   class="aform-control{{ $errors->has('cost_price') ? ' error' : '' }}"
                                   value="{{ old('cost_price', $product->cost_price ?? '') }}"
                                   required oninput="calcMargin()">
                            @error('cost_price') <span class="aform-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Live margin preview --}}
                    <div id="marginPreview" style="display:none;background:var(--admin-bg);border-radius:var(--radius);padding:var(--sp-3) var(--sp-4);font-size:var(--text-sm);display:flex;gap:var(--sp-6)">
                        <span>Profit: <strong id="profitVal" style="color:var(--success)">—</strong></span>
                        <span>Margin: <strong id="marginVal" style="color:var(--success)">—</strong></span>
                    </div>

                </div>
            </div>

            {{-- Variants --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title">Variants</span>
                    <span style="font-size:var(--text-xs);color:var(--admin-muted)">e.g. Size, Color</span>
                </div>
                <div class="admin-card-body">
                    <input type="hidden" name="variants_json" id="variantsJson"
                           value="{{ old('variants_json', $editing ? json_encode($product->variants) : '') }}">

                    <div id="variantGroups" style="display:flex;flex-direction:column;gap:var(--sp-4)"></div>

                    <button type="button" class="abtn abtn-outline abtn-sm" onclick="addVariantGroup()"
                            style="margin-top:var(--sp-3)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Add Option Group
                    </button>
                </div>
            </div>

            {{-- SEO --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">SEO</span></div>
                <div class="admin-card-body">
                    <div class="aform-group">
                        <label class="aform-label" for="meta_title">Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title"
                               class="aform-control{{ $errors->has('meta_title') ? ' error' : '' }}"
                               value="{{ old('meta_title', $product->meta_title ?? '') }}">
                        @error('meta_title') <span class="aform-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="aform-group">
                        <label class="aform-label" for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" class="aform-control" rows="2">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Right column ──────────────────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

            {{-- Actions --}}
            <div class="admin-card">
                <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                    <button type="submit" class="abtn abtn-amber abtn-full abtn-lg">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ $editing ? 'Save Changes' : 'Create Product' }}
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="abtn abtn-outline abtn-full">Cancel</a>
                    @if($editing)
                    <a href="{{ route('product.show', $product->slug) }}" target="_blank"
                       class="abtn abtn-ghost abtn-full" style="font-size:var(--text-xs)">
                        View on store ↗
                    </a>
                    @endif
                </div>
            </div>

            {{-- Category + status --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Organisation</span></div>
                <div class="admin-card-body">
                    <div class="aform-group">
                        <label class="aform-label" for="category_id">Category <span class="req">*</span></label>
                        <select id="category_id" name="category_id" class="aform-control{{ $errors->has('category_id') ? ' error' : '' }}" required>
                            <option value="">Select category</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                    {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="aform-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="aform-group">
                        <label class="aform-label" for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" class="aform-control" min="0"
                               value="{{ old('sort_order', $product->sort_order ?? 0) }}">
                    </div>
                </div>
            </div>

            {{-- Flags --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Visibility & Flags</span></div>
                <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                    @php
                    $flags = [
                        ['is_active',              'Active (visible in store)',     true],
                        ['is_featured',            'Featured (on homepage)',         false],
                        ['is_new',                 'Mark as New Arrival',            false],
                        ['is_on_sale',             'On Sale (show sale price)',       false],
                        ['show_when_out_of_stock', 'Show when out of stock',          true],
                    ];
                    @endphp
                    @foreach($flags as [$name, $label, $default])
                    <label class="toggle-wrap">
                        <div class="toggle">
                            <input type="hidden" name="{{ $name }}" value="0">
                            <input type="checkbox" name="{{ $name }}" value="1"
                                   {{ old($name, $editing ? ($product->$name ? '1' : '0') : ($default ? '1' : '0')) == '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </div>
                        <span class="toggle-label">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Stock --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Inventory</span></div>
                <div class="admin-card-body">
                    <div class="aform-group">
                        <label class="aform-label" for="stock">Stock Quantity <span class="req">*</span></label>
                        <input type="number" id="stock" name="stock"
                               class="aform-control{{ $errors->has('stock') ? ' error' : '' }}" min="0"
                               value="{{ old('stock', $product->stock ?? 0) }}" required>
                        @error('stock') <span class="aform-error">{{ $message }}</span> @enderror
                        <span class="aform-hint">Use Purchase Orders to add stock and track costs.</span>
                    </div>
                    <div class="aform-group">
                        <label class="aform-label" for="low_stock_threshold">Low Stock Alert Below</label>
                        <input type="number" id="low_stock_threshold" name="low_stock_threshold"
                               class="aform-control{{ $errors->has('low_stock_threshold') ? ' error' : '' }}" min="0"
                               value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}">
                        @error('low_stock_threshold') <span class="aform-error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Main image --}}
            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Main Image</span></div>
                <div class="admin-card-body">
                    @if($editing && $product->main_image)
                    <div style="margin-bottom:var(--sp-3)">
                        <img src="{{ Storage::url($product->main_image) }}" id="mainImgPreview"
                             style="width:100%;max-height:180px;object-fit:contain;border-radius:var(--radius);border:1px solid var(--admin-border)">
                    </div>
                    @else
                    <div id="mainImgPreviewWrap" style="display:none;margin-bottom:var(--sp-3)">
                        <img id="mainImgPreview" src=""
                             style="width:100%;max-height:180px;object-fit:contain;border-radius:var(--radius);border:1px solid var(--admin-border)">
                    </div>
                    @endif
                    <label class="image-upload-area" for="main_image" style="position:relative">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--amber)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        <span style="font-size:var(--text-sm);font-weight:var(--weight-medium);color:var(--admin-text)">
                            {{ $editing && $product->main_image ? 'Replace image' : 'Upload image' }}
                        </span>
                        <span style="font-size:var(--text-xs);color:var(--admin-muted)">PNG, JPG up to 4MB</span>
                        <input type="file" id="main_image" name="main_image"
                               accept="image/*" style="display:none"
                               onchange="previewMain(this)">
                        @error('main_image') <span class="aform-error">{{ $message }}</span> @enderror
                    </label>
                </div>
            </div>

            {{-- Gallery --}}
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="admin-card-title">Gallery</span>
                    <span style="font-size:var(--text-xs);color:var(--admin-muted)">Drag to reorder</span>
                </div>
                <div class="admin-card-body">
                    {{-- Hidden inputs track kept existing images (in order) --}}
                    <div id="keepInputs"></div>

                    {{-- Thumb grid — existing + new side by side, all sortable --}}
                    <div id="galleryThumbs" style="display:flex;flex-wrap:wrap;gap:var(--sp-3);margin-bottom:var(--sp-4);min-height:80px">
                        @if($editing && $product->gallery)
                            @foreach($product->gallery as $img)
                            <div class="gallery-thumb existing" data-path="{{ $img }}"
                                 style="position:relative;width:80px;height:80px;cursor:grab;flex-shrink:0">
                                <img src="{{ Storage::url($img) }}" alt="Gallery"
                                     style="width:80px;height:80px;object-fit:contain;border-radius:var(--radius);border:2px solid var(--admin-border);background:#f8f8f8">
                                <span style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.45);border-radius:0 0 var(--radius) var(--radius);font-size:9px;color:#fff;text-align:center;padding:1px 0">Saved</span>
                                <button type="button" onclick="removeThumb(this)"
                                        style="position:absolute;top:-6px;right:-6px;width:20px;height:20px;border-radius:999px;background:var(--danger);color:white;border:none;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;line-height:1;box-shadow:0 1px 4px rgba(0,0,0,0.2)">×</button>
                            </div>
                            @endforeach
                        @endif
                    </div>

                    <label class="image-upload-area" for="gallery_input" style="position:relative;margin-top:var(--sp-2)">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--amber)" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <span style="font-size:var(--text-sm);font-weight:var(--weight-medium);color:var(--admin-text)">Add gallery images</span>
                        <span style="font-size:var(--text-xs);color:var(--admin-muted)">Multiple files — drag thumbs above to reorder</span>
                        <input type="file" id="gallery_input" name="gallery_new[]"
                               accept="image/*" multiple style="display:none"
                               onchange="addGalleryPreviews(this)">
                        @error('gallery_new.*') <span class="aform-error">{{ $message }}</span> @enderror
                    </label>
                </div>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
// ── Slug auto-generation ─────────────────────────────────
let slugEdited = {{ $editing ? 'true' : 'false' }};
function autoSlug(val) {
    if (slugEdited) return;
    document.getElementById('slug').value = val.toLowerCase()
        .replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-');
}
document.getElementById('slug')?.addEventListener('input', () => { slugEdited = true; });

// ── Margin calculator ────────────────────────────────────
function calcMargin() {
    const price = parseFloat(document.getElementById('price')?.value) || 0;
    const cost  = parseFloat(document.getElementById('cost_price')?.value) || 0;
    const wrap  = document.getElementById('marginPreview');
    if (!wrap) return;
    if (price > 0 && cost > 0) {
        wrap.style.display = 'flex';
        const profit = price - cost;
        const margin = ((profit / price) * 100).toFixed(1);
        document.getElementById('profitVal').textContent = '{{ $currencySymbol }}' + profit.toFixed(2);
        document.getElementById('marginVal').textContent = margin + '%';
        const colour = profit >= 0 ? 'var(--success)' : 'var(--danger)';
        document.getElementById('profitVal').style.color = colour;
        document.getElementById('marginVal').style.color = colour;
    } else {
        wrap.style.display = 'none';
    }
}
calcMargin();

// ── Main image preview ────────────────────────────────────
function previewMain(input) {
    const preview = document.getElementById('mainImgPreview');
    const wrap    = document.getElementById('mainImgPreviewWrap');
    if (!input.files[0] || !preview) return;
    const reader = new FileReader();
    reader.onload = e => {
        preview.src = e.target.result;
        preview.style.display = 'block';
        if (wrap) wrap.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}

// ── Gallery management (sortable, preview, delete) ───────

// DataTransfer object to accumulate new files across multiple selections
const galleryDT = typeof DataTransfer !== 'undefined' ? new DataTransfer() : null;

function addGalleryPreviews(input) {
    const thumbs = document.getElementById('galleryThumbs');
    Array.from(input.files).forEach(file => {
        // Accumulate in DataTransfer so multi-selection works across picks
        if (galleryDT) galleryDT.items.add(file);
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'gallery-thumb new-file';
            div.dataset.filename = file.name;
            div.style.cssText = 'position:relative;width:80px;height:80px;cursor:grab;flex-shrink:0';
            div.innerHTML = `
                <img src="${e.target.result}"
                     style="width:80px;height:80px;object-fit:contain;border-radius:var(--radius);border:2px solid var(--amber);background:#f8f8f8">
                <span style="position:absolute;bottom:0;left:0;right:0;background:rgba(37,99,235,0.7);border-radius:0 0 var(--radius) var(--radius);font-size:9px;color:#fff;text-align:center;padding:1px 0">New</span>
                <button type="button" onclick="removeNewThumb(this, '${escHtml(file.name)}')"
                        style="position:absolute;top:-6px;right:-6px;width:20px;height:20px;border-radius:999px;background:var(--danger);color:white;border:none;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;line-height:1;box-shadow:0 1px 4px rgba(0,0,0,0.2)">×</button>
            `;
            thumbs.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
    // Write accumulated files back to the input so they submit with the form
    if (galleryDT) {
        try { input.files = galleryDT.files; } catch(e) {}
    }
    // Do NOT clear input.value — that would lose the file list
}

function removeNewThumb(btn, filename) {
    btn.closest('.gallery-thumb').remove();
    // Rebuild galleryDT without the removed file
    if (galleryDT) {
        // DataTransfer items can't be removed by name directly in all browsers
        // Rebuild from remaining new-file thumbs that still have a filename match
        const remaining = new Set(
            Array.from(document.querySelectorAll('#galleryThumbs .gallery-thumb.new-file'))
                 .map(d => d.dataset.filename)
        );
        // Clear and re-add only kept files
        const kept = [];
        for (let i = 0; i < galleryDT.items.length; i++) {
            const f = galleryDT.items[i].getAsFile();
            if (f && remaining.has(f.name)) kept.push(f);
        }
        while (galleryDT.items.length) galleryDT.items.remove(0);
        kept.forEach(f => galleryDT.items.add(f));
        // Reassign to file input
        const inp = document.getElementById('gallery_input');
        if (inp) { try { inp.files = galleryDT.files; } catch(e) {} }
    }
    syncGalleryHidden();
}

function removeThumb(btn) {
    const div = btn.closest('.gallery-thumb');
    div.remove();
    syncGalleryHidden();
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Sync hidden inputs based on current thumb order
function syncGalleryHidden() {
    const keepWrap = document.getElementById('keepInputs');
    keepWrap.innerHTML = '';

    document.querySelectorAll('#galleryThumbs .gallery-thumb.existing').forEach(div => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = 'gallery_keep[]';
        inp.value = div.dataset.path;
        keepWrap.appendChild(inp);
    });
}

// Drag-to-sort for gallery thumbs
(function() {
    const container = document.getElementById('galleryThumbs');
    let dragging = null;

    container.addEventListener('dragstart', e => {
        dragging = e.target.closest('.gallery-thumb');
        if (dragging) { dragging.style.opacity = '0.5'; e.dataTransfer.effectAllowed = 'move'; }
    });
    container.addEventListener('dragend', e => {
        if (dragging) { dragging.style.opacity = '1'; dragging = null; }
        syncGalleryHidden();
    });
    container.addEventListener('dragover', e => {
        e.preventDefault();
        const over = e.target.closest('.gallery-thumb');
        if (!over || over === dragging) return;
        const rect = over.getBoundingClientRect();
        const mid  = rect.left + rect.width / 2;
        if (e.clientX < mid) container.insertBefore(dragging, over);
        else container.insertBefore(dragging, over.nextSibling);
    });

    // Make thumbs draggable
    const observer = new MutationObserver(() => {
        container.querySelectorAll('.gallery-thumb').forEach(t => { t.draggable = true; });
    });
    observer.observe(container, { childList: true, subtree: true });
    container.querySelectorAll('.gallery-thumb').forEach(t => { t.draggable = true; });
})();

// Init hidden keep inputs from existing thumbs on load
syncGalleryHidden();

// ── Variant builder ───────────────────────────────────────
let variants = {};

// Load existing variants on edit
(function() {
    const raw = document.getElementById('variantsJson')?.value;
    if (!raw) return;
    try {
        const parsed = JSON.parse(raw);
        if (parsed && typeof parsed === 'object') {
            variants = parsed;
            Object.entries(variants).forEach(([name, values]) => {
                renderVariantGroup(name, values);
            });
        }
    } catch(e) {}
})();

function addVariantGroup() {
    const name = prompt('Option name (e.g. Size, Color):');
    if (!name?.trim()) return;
    if (variants[name]) { alert('Option already exists'); return; }
    variants[name] = [];
    renderVariantGroup(name, []);
    syncVariants();
}

function renderVariantGroup(name, values) {
    const container = document.getElementById('variantGroups');
    const id = 'vg-' + name.replace(/\s+/g, '-').toLowerCase();
    const div = document.createElement('div');
    div.id = id;
    div.style.cssText = 'background:var(--admin-bg);border-radius:var(--radius);padding:var(--sp-3)';
    div.innerHTML = `
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--sp-2)">
            <span style="font-size:var(--text-sm);font-weight:var(--weight-semibold)">${name}</span>
            <button type="button" onclick="removeVariantGroup('${name}','${id}')"
                    style="background:none;border:none;cursor:pointer;color:var(--danger);font-size:var(--text-xs)">Remove</button>
        </div>
        <div id="${id}-tags" style="display:flex;flex-wrap:wrap;gap:var(--sp-1);margin-bottom:var(--sp-2)">
            ${values.map(v => `<span data-val="${v}" style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:var(--ink);color:white;border-radius:999px;font-size:12px;">${v}<button type="button" onclick="removeTag('${name}',this)" style="background:none;border:none;color:rgba(255,255,255,0.7);cursor:pointer;font-size:14px;line-height:1;padding:0">×</button></span>`).join('')}
        </div>
        <div style="display:flex;gap:var(--sp-2)">
            <input type="text" id="${id}-input" placeholder="Add value, press Enter"
                   style="flex:1;height:32px;padding:0 10px;border:1.5px solid var(--admin-border);border-radius:var(--radius);font-size:var(--text-sm);outline:none;background:white"
                   onkeydown="if(event.key==='Enter'){event.preventDefault();addTag('${name}','${id}');}">
            <button type="button" onclick="addTag('${name}','${id}')"
                    class="abtn abtn-primary abtn-sm">Add</button>
        </div>
    `;
    container.appendChild(div);
}

function addTag(groupName, groupId) {
    const input = document.getElementById(groupId + '-input');
    const val   = input.value.trim();
    if (!val) return;
    if (!variants[groupName]) variants[groupName] = [];
    if (variants[groupName].includes(val)) { input.value=''; return; }
    variants[groupName].push(val);
    // Append tag span
    const tags = document.getElementById(groupId + '-tags');
    const span = document.createElement('span');
    span.dataset.val = val;
    span.style.cssText = 'display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:var(--ink);color:white;border-radius:999px;font-size:12px;';
    span.innerHTML = `${val}<button type="button" onclick="removeTag('${groupName}',this)" style="background:none;border:none;color:rgba(255,255,255,0.7);cursor:pointer;font-size:14px;line-height:1;padding:0">×</button>`;
    tags.appendChild(span);
    input.value = '';
    syncVariants();
}

function removeTag(groupName, btn) {
    const span = btn.closest('span');
    const val  = span.dataset.val;
    if (variants[groupName]) {
        variants[groupName] = variants[groupName].filter(v => v !== val);
    }
    span.remove();
    syncVariants();
}

function removeVariantGroup(name, id) {
    delete variants[name];
    document.getElementById(id)?.remove();
    syncVariants();
}

function syncVariants() {
    document.getElementById('variantsJson').value = JSON.stringify(variants);
}
</script>
@endpush
