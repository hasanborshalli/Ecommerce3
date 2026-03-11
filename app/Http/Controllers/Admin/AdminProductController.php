<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku',  'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            match ($request->status) {
                'active'       => $query->where('is_active', true),
                'inactive'     => $query->where('is_active', false),
                'out_of_stock' => $query->where('stock', '<=', 0),
                'low_stock'    => $query->where('stock', '>', 0)->whereColumn('stock', '<=', 'low_stock_threshold'),
                default        => null,
            };
        }

        $products   = $query->orderBy('sort_order')->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $categories = Category::active()->orderBy('sort_order')->get();

        $counts = [
            'all'       => Product::count(),
            'active'    => Product::where('is_active', true)->count(),
            'low_stock' => Product::lowStock()->count(),
            'out'       => Product::where('is_active', true)->where('stock', '<=', 0)->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'counts'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        return view('admin.products.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);
        $data      = $this->prepareData($validated, $request);
        $data      = $this->handleImages($data, $request, null);

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        return view('admin.products.form', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product->id);
        $data      = $this->prepareData($validated, $request);
        $data      = $this->handleImages($data, $request, $product);

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Nullify any purchase order items referencing this product
        // (product_id is nullable so PO history is preserved)
        PurchaseOrderItem::where('product_id', $product->id)
            ->update(['product_id' => null]);

        // Delete images from storage
        if ($product->main_image) {
            Storage::disk('public')->delete($product->main_image);
        }
        if ($product->gallery) {
            foreach ($product->gallery as $img) {
                Storage::disk('public')->delete($img);
            }
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────

    private function validateProduct(Request $request, ?int $excludeId = null): array
    {
        return $request->validate([
            'category_id'            => 'required|exists:categories,id',
            'name'                   => 'required|string|max:200',
            'slug'                   => 'nullable|string|max:200|unique:products,slug' . ($excludeId ? ",{$excludeId}" : ''),
            'sku'                    => 'nullable|string|max:100|unique:products,sku' . ($excludeId ? ",{$excludeId}" : ''),
            'short_description'      => 'nullable|string|max:500',
            'description'            => 'nullable|string',
            'price'                  => 'required|numeric|min:0',
            'sale_price'             => 'nullable|numeric|min:0',
            'cost_price'             => 'required|numeric|min:0',
            'stock'                  => 'required|integer|min:0',
            'low_stock_threshold'    => 'required|integer|min:0',
            'main_image'             => 'nullable|image|max:4096',
            'gallery_new.*'          => 'nullable|image|max:4096',
            'gallery_keep.*'         => 'nullable|string',
            'variants_json'          => 'nullable|string',
            'is_active'              => 'nullable|boolean',
            'is_featured'            => 'nullable|boolean',
            'is_new'                 => 'nullable|boolean',
            'is_on_sale'             => 'nullable|boolean',
            'show_when_out_of_stock' => 'nullable|boolean',
            'meta_title'             => 'nullable|string|max:200',
            'meta_description'       => 'nullable|string|max:500',
            'sort_order'             => 'nullable|integer|min:0',
        ]);
    }

    private function prepareData(array $validated, Request $request): array
    {
        $data = $validated;

        // Auto-slug
        $data['slug'] = !empty($data['slug'])
            ? Str::slug($data['slug'])
            : Str::slug($data['name']);

        // Checkboxes
        $data['is_active']              = $request->boolean('is_active');
        $data['is_featured']            = $request->boolean('is_featured');
        $data['is_new']                 = $request->boolean('is_new');
        $data['is_on_sale']             = $request->boolean('is_on_sale');
        $data['show_when_out_of_stock'] = $request->boolean('show_when_out_of_stock');

        // Clear sale price if not on sale
        if (!$data['is_on_sale']) {
            $data['sale_price'] = null;
        }

        // Variants JSON
        if (!empty($validated['variants_json'])) {
            $decoded = json_decode($validated['variants_json'], true);
            $data['variants'] = is_array($decoded) ? $decoded : null;
        } else {
            $data['variants'] = null;
        }
        unset($data['variants_json'], $data['gallery_keep'], $data['gallery_new']);

        return $data;
    }

    private function handleImages(array $data, Request $request, ?Product $product): array
    {
        // Main image
        if ($request->hasFile('main_image')) {
            if ($product?->main_image) {
                Storage::disk('public')->delete($product->main_image);
            }
            $data['main_image'] = $request->file('main_image')
                ->store('products', 'public');
        }

        // Gallery: kept = array of existing paths (in user's sorted order)
        // gallery_keep[] comes as multiple hidden inputs (one per existing thumb, in DOM order)
        $kept = $request->input('gallery_keep', []);
        if (!is_array($kept)) {
            $kept = $kept ? [$kept] : [];
        }

        // Delete removed gallery images (those no longer in kept list)
        if ($product && $product->gallery) {
            foreach ($product->gallery as $img) {
                if (!in_array($img, $kept)) {
                    Storage::disk('public')->delete($img);
                }
            }
        }

        // Upload new gallery images (name changed to gallery_new[] in form)
        $newUploads = [];
        if ($request->hasFile('gallery_new')) {
            foreach ($request->file('gallery_new') as $file) {
                $newUploads[] = $file->store('products/gallery', 'public');
            }
        }

        $data['gallery'] = array_merge($kept, $newUploads) ?: null;

        return $data;
    }
}
