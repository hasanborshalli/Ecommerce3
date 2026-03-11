<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.form');
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        $data      = $this->prepareData($validated, $request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $this->validateCategory($request, $category->id);
        $data      = $this->prepareData($validated, $request);

        if ($request->hasFile('image')) {
            if ($category->image) Storage::disk('public')->delete($category->image);
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete — category has products. Reassign them first.');
        }
        if ($category->image) Storage::disk('public')->delete($category->image);
        $category->delete();
        Cache::forget('nav_categories');

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────

    private function validateCategory(Request $request, ?int $excludeId = null): array
    {
        return $request->validate([
            'name'             => 'required|string|max:100',
            'slug'             => 'nullable|string|max:150|unique:categories,slug' . ($excludeId ? ",{$excludeId}" : ''),
            'description'      => 'nullable|string|max:500',
            'image'            => 'nullable|image|max:3072',
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer|min:0',
            'meta_title'       => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
        ]);
    }

    private function prepareData(array $validated, Request $request): array
    {
        $data         = $validated;
        $data['slug'] = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        unset($data['image']);
        return $data;
    }
}
