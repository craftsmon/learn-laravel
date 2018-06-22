<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        if (request()->category) {
            $products = Product::with('categories')->whereHas('categories', function ($query) {
                $query->where('slug', request()->category);
            })->get();
            $categories = Category::all();
            $categoryName = $categories->where('slug', request()->category)->first()->name;
        }
        else {
            $products = Product::inRandomOrder()->take(10)->get();
            $categories = Category::all();
            $categoryName = 'Featured';
        }

        if (request()->sort == 'low_high') {
            $products = $products->sortBy('price');
        }
        else if (request()->sort == 'high_low') {
            $products = $products->sortByDesc('price');
        }

        return view('shop')->with([
            'products' => $products,
            'categories' => $categories,
            'categoryName' => $categoryName,
        ]);
    }

    /**
     * Display the specified resource
     * 
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function show ($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $mightAlsoLike = Product::where('slug', '!=', $slug)->mightAlsoLike()->get();

        return view('product')->with([
            'product' => $product,
            'mightAlsoLike' => $mightAlsoLike,
        ]);
    }
}
