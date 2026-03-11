<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::visible()->with('category');

        // Filter by category slug
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $query->where('category_id', $category->id);
        } else {
            $category = null;
        }

        // Filter by flags
        if ($request->filter === 'new')  $query->where('is_new', true);
        if ($request->filter === 'sale') $query->where('is_on_sale', true);

        // Sort
        switch ($request->sort) {
            case 'price_asc':  $query->orderBy('price', 'asc');  break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'newest':     $query->orderBy('created_at', 'desc'); break;
            case 'name':       $query->orderBy('name', 'asc'); break;
            case 'top_rated':  $query->orderBy('review_avg', 'desc')->orderBy('review_count', 'desc'); break;
            default:           $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
        }

        $products   = $query->paginate(12)->withQueryString();
        $categories = Category::active()->orderBy('sort_order')->get();

        return view('shop', compact('products', 'category', 'categories'));
    }

    public function show(Product $product)
    {
        // 404 if not visible
        if (!$product->is_active && !$product->show_when_out_of_stock) {
            abort(404);
        }

        $product->load('category');

        // Approved reviews for this product
        $reviews = Review::approved()
            ->where('product_id', $product->id)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->get();

        // Has the current customer already reviewed?
        $customerHasReviewed = false;
        if (session('customer_id')) {
            $customerHasReviewed = Review::where('product_id', $product->id)
                ->where('customer_id', session('customer_id'))
                ->exists();
        }

        // Related products (same category, exclude current)
        $related = Product::visible()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('category')
            ->take(4)
            ->get();

        return view('product', compact(
            'product', 'reviews', 'customerHasReviewed', 'related'
        ));
    }
}
