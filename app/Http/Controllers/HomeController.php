<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $heroSlides  = HeroSlide::active()->get();
        $featured    = Product::featured()->with('category')->take(8)->get();
        $newArrivals = Product::newArrivals()->with('category')->take(4)->get();
        $onSale      = Product::onSale()->with('category')->take(4)->get();
        $categories  = Category::active()->orderBy('sort_order')->take(6)->get();

        return view('home', compact(
            'heroSlides', 'featured', 'newArrivals', 'onSale', 'categories'
        ));
    }

    public function about()
    {
        return view('about');
    }
}
