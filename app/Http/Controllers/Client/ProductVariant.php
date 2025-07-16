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
        $keyword = $request->input('keyword');

        // Tạo query ban đầu với quan hệ
        $query = Product::with('category', 'galleries');

        // Lọc theo từ khóa trong tên sản phẩm
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // Lọc theo danh mục nếu có
        if (!empty($selectedCategories)) {
            $query->whereIn('category_id', $selectedCategories);
        }

        // Nếu sắp xếp theo giá cao -> thấp
        $query = Product::query()->with('galleries');

        if ($request->has('sort')) {
            $sort = $request->sort;

            if (in_array('high-low', $sort)) {
                $query->withMax('variants', 'price')->orderBy('variants_max_price', 'desc');
            } elseif (in_array('low-high', $sort)) {
                $query->withMin('variants', 'price')->orderBy('variants_min_price', 'asc');
            }
        }
        $products = $query->paginate(9)->withQueryString();

        // Phân trang, sắp xếp mới nhất và giữ query trên URL
        $products = $query->latest()->paginate(9)->appends($request->query());

        $categories = Category::all();

        // Truyền cả biến keyword về view nếu muốn hiển thị lại từ khóa đã nhập
        return view('client.shop', compact('products', 'categories', 'selectedCategories', 'keyword'));
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