<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\Pot;

class ProductVariant extends Controller
{
    public function product(Request $request)
    {
        $selectedCategories = (array) $request->input('category', []);
        $keyword            = trim((string) $request->input('keyword', ''));
        $sorts              = (array) $request->input('sort', []);
        $perPage            = (int) $request->input('per_page', 9);

        // Base query: join variants để có min/max price cho sort/hiển thị
        $query = Product::query()
            ->with(['category', 'galleries', 'variants'])
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->where('products.is_active', 1)
            ->select(
                'products.id',
                'products.name',
                'products.slug',
                'products.category_id',
                'products.description',
                'products.created_at',
                'products.updated_at',
                DB::raw('MIN(product_variants.price) as min_price'),
                DB::raw('MAX(product_variants.price) as max_price')
            )
            ->groupBy(
                'products.id',
                'products.name',
                'products.slug',
                'products.category_id',
                'products.description',
                'products.created_at',
                'products.updated_at'
            );

        // Lọc theo từ khóa
        if ($keyword !== '') {
            $query->where('products.name', 'like', "%{$keyword}%");
        }

        // Lọc theo danh mục
        if (!empty($selectedCategories)) {
            $query->whereIn('products.category_id', $selectedCategories);
        }

        // Sắp xếp
        if (in_array('new', $sorts, true)) {
            $query->orderBy('products.created_at', 'desc');
        }
        if (in_array('a-z', $sorts, true)) {
            $query->orderBy('products.name', 'asc');
        }
        if (in_array('z-a', $sorts, true)) {
            $query->orderBy('products.name', 'desc');
        }
        if (in_array('low-high', $sorts, true)) {
            $query->orderBy('min_price', 'asc');
        }
        if (in_array('high-low', $sorts, true)) {
            $query->orderBy('min_price', 'desc');
        }

        // Phân trang + giữ toàn bộ query trên URL
        $products = $query->paginate($perPage)->appends($request->query());

        $categories = Category::all();

        return view('client.shop', [
            'products'           => $products,
            'categories'         => $categories,
            'selectedCategories' => $selectedCategories,
            'keyword'            => $keyword,
        ]);
    }
    public function productDetail($id)
    {
        $product = Product::with([
            'category',
            'galleries',
            'variants.pots',
            'reviews.user'          // lấy danh sách chậu riêng theo product
        ])->findOrFail($id);


        $comments = Comment::with('user', 'product')
            ->where('product_id', $id)
            ->whereNull('parent_id')
            ->latest()
            ->get();

        $averageRating = $product->reviews()->avg('rating') ?? 0;
        $reviewCount   = $product->reviews()->count();


        // Thêm đoạn này để truyền biến variants cho JavaScript xử lý
        $variants = $product->variants->map(function ($v) {
            return [
                'id' => $v->id,
                'height' => $v->height,
                'price' => $v->price,
                'stock_quantity' => $v->stock_quantity,
                'pots' => $v->pots->pluck('id')->toArray(),
            ];
        });
        $availablePotIds = Pot::where('quantity', '>', 0)->pluck('id')->toArray();

        // Lọc các biến thể có liên kết với chậu còn hàng
        $variants = $product->variants->map(function ($v) use ($availablePotIds) {
            return [
                'id' => $v->id,
                'height' => $v->height,
                'price' => $v->price,
                'stock_quantity' => $v->stock_quantity,
                'pots' => $v->pots->whereIn('id', $availablePotIds)->pluck('id')->toArray(),
            ];
        });

        // Lấy danh sách pots để truyền sang view
        $allPots = Pot::whereIn('id', $availablePotIds)->get()->keyBy('id');

        // Lấy tất cả pot thực sự được dùng trong variant
        $linkedPotIds = $variants->pluck('pots')->flatten()->unique();

        // Lấy pot thực sự hiển thị: có liên kết & còn hàng
        $potsToShow = $linkedPotIds->map(fn($id) => $allPots[$id] ?? null)->filter();

         return view('client.shopDetail', compact(
            'product',
            'comments',
            'variants',
            'allPots',
            'potsToShow',
            'averageRating',
            'reviewCount'
        ));
        
    }
}
