<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Controller
{
    public function product()
    {
        $products = Product::with('category','galleries')->latest()->paginate(9);

        return view('client.shop', compact('products'));
    }
    public function productDetail($id)
    {
        $product = Product::with('category', 'galleries','variants')->findOrFail($id);


        return view('client.shopDetail', compact('product'));
    }
}