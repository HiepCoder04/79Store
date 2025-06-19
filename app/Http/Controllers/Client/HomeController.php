<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use App\Models\Product;
class HomeController extends Controller
{
     public function index()
    {
        // Lấy banner như cũ (giữ nguyên)
        $banners = Banner::where('is_active', 1)->latest()->get();

        // Lấy 8 sản phẩm mới nhất cho phần "New Arrivals"
        $products = Product::with('galleries', 'variants')
            ->latest()
            ->take(8)
            ->get();

        // Truyền cả 2 xuống view
        return view('client.home', compact('banners', 'products'));
    }

}
