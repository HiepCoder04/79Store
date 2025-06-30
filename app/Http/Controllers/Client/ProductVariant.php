<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Controller
{
    public function product(Request $request)
    {
        $selectedCategories = $request->input('category', []);

        $products = Product::with('category', 'galleries')
            ->when(!empty($selectedCategories), function ($query) use ($selectedCategories) {
                $query->whereIn('category_id', $selectedCategories);
            })
            ->latest()
            ->paginate(9);

        $categories = Category::all();

        return view('client.shop', compact('products', 'categories', 'selectedCategories'));
    }
    public function productDetail($id)
    {
        $product = Product::with('category', 'galleries', 'variants')->findOrFail($id);

        $comments = Comment::with('user', 'product')
            ->where('product_id', $id)
            ->whereNull('parent_id') 
            ->latest()              
            ->get();
        return view('client.shopDetail', compact('product', 'comments'));
    }
}