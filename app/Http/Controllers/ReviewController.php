<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a new customer review (pending admin approval).
     */
    public function store(Request $request, Product $product)
    {
        // Must be signed in
        if (!session('customer_id')) {
            return redirect()->route('account.login')
                ->with('error', 'Please sign in to leave a review.');
        }

        $customerId = (int) session('customer_id');

        // One review per customer per product
        if (Review::where('product_id', $product->id)
                  ->where('customer_id', $customerId)
                  ->exists()) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:120',
            'body'   => 'nullable|string|max:1500',
        ]);

        Review::create([
            'product_id'   => $product->id,
            'customer_id'  => $customerId,
            'author_name'  => session('customer_name'),
            'author_email' => session('customer_email'),
            'rating'       => $validated['rating'],
            'title'        => $validated['title'] ?? null,
            'body'         => $validated['body']  ?? null,
            'status'       => 'pending',
        ]);

        return back()->with('review_success', true);
    }
}
