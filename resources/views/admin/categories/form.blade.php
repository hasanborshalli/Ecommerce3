@extends('admin.layout')

@php $editing = isset($category); @endphp
@section('title', $editing ? 'Edit Category' : 'New Category')
@section('page_title', $editing ? 'Edit Category' : 'New Category')
@section('breadcrumb')
    <a href="{{ route('admin.categories.index') }}">Categories</a> ›
    {{ $editing ? $category->name : 'New' }}
@endsection

@section('content')

<form method="POST"
      action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if($editing) @method('PUT') @endif

    <div style="display:grid;grid-template-columns:1fr 320px;gap:var(--sp-5);align-items:start">

        {{-- ── Left --}}
        <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Category Details</span></div>
                <div class="admin-card-body">

                    <div class="aform-group">
                        <label class="aform-label" for="name">Name <span class="req">*</span></label>
                        <input type="text" id="name" name="name"
                               class="aform-control{{ $errors->has('name') ? ' error' : '' }}"
                               value="{{ old('name', $category->name ?? '') }}"
                               required oninput="autoSlug(this.value)">
                        @error('name') <span class="aform-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="aform-group">
                        <label class="aform-label" for="slug">Slug</label>
                        <input type="text" id="slug" name="slug"
                               class="aform-control"
                               value="{{ old('slug', $category->slug ?? '') }}"
                               placeholder="auto-generated">
                    </div>

                    <div class="aform-group">
                        <label class="aform-label" for="description">Description</label>
                        <textarea id="description" name="description"
                                  class="aform-control" rows="3"
                                  placeholder="Optional — shown on category/SEO pages">{{ old('description', $category->description ?? '') }}</textarea>
                    </div>

                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">SEO</span></div>
                <div class="admin-card-body">
                    <div class="aform-group">
                        <label class="aform-label" for="meta_title">Meta Title</label>
                        <input type="text" id="meta_title" name="meta_title" class="aform-control"
                               value="{{ old('meta_title', $category->meta_title ?? '') }}">
                    </div>
                    <div class="aform-group">
                        <label class="aform-label" for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description"
                                  class="aform-control" rows="2">{{ old('meta_description', $category->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Right --}}
        <div style="display:flex;flex-direction:column;gap:var(--sp-5)">

            <div class="admin-card">
                <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-3)">
                    <button type="submit" class="abtn abtn-amber abtn-full abtn-lg">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        {{ $editing ? 'Save Changes' : 'Create Category' }}
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="abtn abtn-outline abtn-full">Cancel</a>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Settings</span></div>
                <div class="admin-card-body" style="display:flex;flex-direction:column;gap:var(--sp-4)">
                    <label class="toggle-wrap">
                        <div class="toggle">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $editing ? ($category->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </div>
                        <span class="toggle-label">Active (visible in nav & shop)</span>
                    </label>
                    <div class="aform-group">
                        <label class="aform-label" for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order"
                               class="aform-control" min="0"
                               value="{{ old('sort_order', $category->sort_order ?? 0) }}">
                        <span class="aform-hint">Lower numbers appear first.</span>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-card-header"><span class="admin-card-title">Image</span></div>
                <div class="admin-card-body">

                    {{-- Current image preview --}}
                    @if($editing && $category->image)
                    <div id="imgPreviewWrap" style="margin-bottom:var(--sp-3)">
                        <img src="{{ Storage::url($category->image) }}" id="catImgPreview"
                             style="width:100%;max-height:160px;object-fit:cover;border-radius:var(--radius);border:1px solid var(--admin-border)">
                    </div>
                    @else
                    <div id="imgPreviewWrap" style="display:none;margin-bottom:var(--sp-3)">
                        <img id="catImgPreview" src=""
                             style="width:100%;max-height:160px;object-fit:cover;border-radius:var(--radius);border:1px solid var(--admin-border)">
                    </div>
                    @endif

                    <label class="image-upload-area" for="image" style="position:relative">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--amber)" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                        <span style="font-size:var(--text-sm);font-weight:var(--weight-medium);color:var(--admin-text)">
                            {{ $editing && $category->image ? 'Replace image' : 'Upload image' }}
                        </span>
                        <span style="font-size:var(--text-xs);color:var(--admin-muted)">PNG, JPG up to 3MB</span>
                        <input type="file" id="image" name="image" accept="image/*"
                               style="display:none" onchange="previewCatImg(this)">
                    </label>

                </div>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
let slugEdited = {{ $editing ? 'true' : 'false' }};
function autoSlug(val) {
    if (slugEdited) return;
    document.getElementById('slug').value = val.toLowerCase()
        .replace(/[^a-z0-9\s-]/g,'').replace(/\s+/g,'-').replace(/-+/g,'-');
}
document.getElementById('slug')?.addEventListener('input', () => { slugEdited = true; });

function previewCatImg(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('catImgPreview').src = e.target.result;
        document.getElementById('imgPreviewWrap').style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
