<?php
// filepath: d:\DUANTOTNGHIEP\79Store\app\Http\Controllers\Client\HomeController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy các bài viết blog mới nhất
        $latest_blogs = Blog::where('is_active', 1)
                           ->latest()
                           ->take(3)
                           ->get();
        
        // Các dữ liệu khác nếu cần
        
        return view('client.home', compact('latest_blogs'));
    }
}