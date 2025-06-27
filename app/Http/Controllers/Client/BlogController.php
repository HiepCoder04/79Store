<?php
// filepath: d:\DUANTOTNGHIEP\79Store\app\Http\Controllers\Client\BlogController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
public function index()
{
    $blogs = Blog::where('is_active', 1)
              ->with('category')
              ->latest()
              ->paginate(6);
              
    $recent_posts = Blog::where('is_active', 1)
                    ->latest()
                    ->take(4)
                    ->get();
                    
    $categories = BlogCategory::withCount('blogs')->get();
    
    // Nếu có liên kết đến sản phẩm, thêm dòng này
    // $featured_products = \App\Models\Product::where('is_featured', 1)
    //                     ->take(3)
    //                     ->get();
    
    return view('client.blogs.index', compact('blogs', 'recent_posts', 'categories'));
}
    
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)
                ->where('is_active', 1)
                ->firstOrFail();
                
        $recent_posts = Blog::where('is_active', 1)
                        ->where('id', '!=', $blog->id)
                        ->latest()
                        ->take(4)
                        ->get();
        
        return view('client.blogs.show', compact('blog', 'recent_posts'));
    }
    
    // Sửa lại hàm category để hỗ trợ tham số tùy chọn
    public function category($slug = null)
    {
        // Nếu không có slug, hiển thị tất cả danh mục hoặc chuyển hướng
        if (!$slug) {
            // Có thể chuyển hướng đến trang danh sách blog
            return redirect()->route('client.blogs.index');
            
            // Hoặc hiển thị trang danh mục
            // $categories = BlogCategory::withCount('blogs')->get();
            // return view('client.blogs.categories', compact('categories'));
        }
        
        // Tìm danh mục theo slug
        $category = BlogCategory::where('slug', $slug)->firstOrFail();
        
        // Lấy các bài viết thuộc danh mục này
        $blogs = Blog::where('is_active', 1)
                ->where('category_blog_id', $category->id)
                ->latest()
                ->paginate(6);
                
        // Lấy các bài viết gần đây
        $recent_posts = Blog::where('is_active', 1)
                        ->latest()
                        ->take(4)
                        ->get();
                        
        // Lấy tất cả danh mục
        $categories = BlogCategory::withCount('blogs')->get();
        
        // Trả về view với dữ liệu
        return view('client.blogs.index', compact('blogs', 'recent_posts', 'categories', 'category'));
    }

    public function uploadImage(Request $req)
{
    $req->validate(['upload' => 'required|image|max:2048']);
    $path = $req->file('upload')->store('blog-images','public');
    return response()->json([
      "url" => asset("storage/{$path}")
    ]);
}
}