<?php
// filepath: d:\DUANTOTNGHIEP\79Store\app\Http\Controllers\Client\HomeController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Banner;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
    public function indexBlog()
    {
        // Lấy các bài viết blog mới nhất
        $latest_blogs = Blog::where('is_active', 1)
            ->latest()
            ->take(3)
            ->get();

        // Các dữ liệu khác nếu cần

        return view('client.home', compact('latest_blogs'));
    }


    public function index()
    {
        // Lấy banner như cũ (giữ nguyên)
        $banners = Banner::where('is_active', 1)->latest()->get();

        // Lấy 8 sản phẩm mới nhất cho phần "New Arrivals"
        $products = Product::with('galleries', 'variants')
            ->where('is_active', 1)
            ->latest()
            ->take(8)
            ->get();

        $vouchers = Voucher::where('is_active', 1)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->get();

        //lay sp ban chay
        $bestSellersIds = Product::join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('order_details', 'product_variants.id', '=', 'order_details.product_variant_id')
            ->whereNull('products.deleted_at')
            ->select('products.id')
            ->groupBy('products.id')
            ->orderByDesc(DB::raw('SUM(order_details.quantity)'))
            ->take(8)
            ->pluck('id');

        // Lấy sản phẩm với quan hệ
        $bestSellers = Product::with('galleries', 'variants')
            ->where('is_active', 1)
            ->whereIn('id', $bestSellersIds)
            ->get();

        $userVouchers = Auth::check()
            ? Auth::user()->vouchers->pluck('voucher_id')
            : collect();

        return view('client.home', compact('banners', 'products', 'vouchers', 'userVouchers', 'bestSellers'));
        
    }
    public function about()
    {
         $banners = Banner::where('is_active', 1)->latest()->get();
        return view('client.users.about-detail',compact('banners'));
    }
}
