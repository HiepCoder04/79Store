<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants', 'galleries']);

        // Kiểm tra có filter theo trạng thái không
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->whereNull('deleted_at');
                    break;
                case 'deleted':
                    $query->onlyTrashed();
                    break;
                case 'all':
                    $query->withTrashed();
                    break;
            }
        } else {
            // Mặc định chỉ hiển thị sản phẩm chưa bị xóa
            $query->whereNull('deleted_at');
        }

        $products = $query->latest()->paginate(10);
        
        // Thống kê số lượng
        $stats = [
            'total' => Product::withTrashed()->count(),
            'active' => Product::count(),
            'deleted' => Product::onlyTrashed()->count()
        ];

        return view('admin.products.index', compact('products', 'stats'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required|string|max:1500',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'required|array|min:1',
            'variants.*.pot' => 'nullable|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'is_active' => true,
            ]);

            foreach ($validated['variants'] as $variant) {
                $isValid = is_numeric($variant['price'] ?? null) &&
                          is_numeric($variant['stock_quantity'] ?? null);

                Log::debug($isValid ? '✅ Biến thể OK' : '❌ Bị loại', $variant);

                if ($isValid) {
                    $product->variants()->create([
                        'variant_name' => $product->name . ' - ' . ($variant['pot'] ?? 'Không rõ'),
                        'pot' => $variant['pot'] ?? null,
                        'price' => $variant['price'],
                        'stock_quantity' => $variant['stock_quantity']
                    ]);
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $path = $image->store('products', 'public');

                        if (!empty($path)) {
                            $product->galleries()->create([
                                'image' => '/storage/' . $path
                            ]);
                        } else {
                            Log::warning('Không thể lưu ảnh, $path rỗng.');
                        }
                    } else {
                        Log::warning('File ảnh không hợp lệ.');
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được thêm thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('❌ Lỗi khi thêm sản phẩm: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Thêm sản phẩm thất bại: ' . $e->getMessage());
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required|string|max:1500',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'nullable|array',
            'variants.*.pot' => 'nullable|max:50',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'category_id' => $request->category_id,
            ]);

            // Cập nhật hoặc thêm mới biến thể
            if ($request->filled('variants')) {
                foreach ($request->variants as $variant) {
                    if (
                        isset($variant['price'], $variant['stock_quantity']) &&
                        is_numeric($variant['price']) &&
                        is_numeric($variant['stock_quantity'])
                    ) {
                        if (!empty($variant['id'])) {
                            // Update variant
                            $product->variants()->where('id', $variant['id'])->update([
                                'pot' => $variant['pot'] ?? null,
                                'price' => $variant['price'],
                                'stock_quantity' => $variant['stock_quantity'],
                            ]);
                        } else {
                            // Create new variant
                            $product->variants()->create([
                                'variant_name' => $product->name . ' - ' . ($variant['pot'] ?? 'Không rõ'),
                                'pot' => $variant['pot'] ?? null,
                                'price' => $variant['price'],
                                'stock_quantity' => $variant['stock_quantity'],
                            ]);
                        }
                    }
                }
            }

            // Xóa ảnh đã chọn
            if ($request->filled('delete_images')) {
                foreach ($request->delete_images as $galleryId) {
                    $gallery = $product->galleries()->find($galleryId);
                    if ($gallery) {
                        // Xóa file vật lý
                        $imagePath = public_path($gallery->image);
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }

                        $gallery->delete();
                    }
                }
            }

            // Thêm ảnh mới nếu có
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $product->galleries()->create(['image' => '/storage/' . $path]);
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi cập nhật sản phẩm.')->withInput();
        }
    }

    public function destroy(Product $product)
    {
        try {
            // Kiểm tra ràng buộc dữ liệu trước khi xóa mềm
            $hasActiveOrders = DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->whereIn('order_details.product_variant_id', $product->variants->pluck('id'))
                ->whereIn('orders.status', ['pending', 'confirmed', 'shipping'])
                ->exists();

            if ($hasActiveOrders) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa sản phẩm này vì đang có đơn hàng chưa hoàn thành.'
                ], 400);
            }

            // Thực hiện xóa mềm
            $product->delete();

            // Ghi log
            Log::info('Sản phẩm đã được xóa mềm', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'deleted_by' => auth()->user()->id ?? 'system',
                'deleted_at' => now()
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được xóa thành công!'
                ]);
            }

            return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa thành công!');

        } catch (\Throwable $e) {
            Log::error('Lỗi khi xóa sản phẩm', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->user()->id ?? 'system'
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xóa sản phẩm: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Lỗi khi xóa sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Khôi phục sản phẩm đã bị xóa mềm
     */
    public function restore(Product $product)
    {
        try {
            // Product đã được tự động resolve từ route model binding
            $product->restore();

            Log::info('Sản phẩm đã được khôi phục', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'restored_by' => auth()->user()->id ?? 'system',
                'restored_at' => now()
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được khôi phục thành công!'
                ]);
            }

            return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được khôi phục thành công!');

        } catch (\Throwable $e) {
            Log::error('Lỗi khi khôi phục sản phẩm', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->user()->id ?? 'system'
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi khôi phục sản phẩm: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Lỗi khi khôi phục sản phẩm: ' . $e->getMessage());
        }
    }

    /**
     * Xóa vĩnh viễn sản phẩm
     */
    public function forceDelete(Product $product)
    {
        try {
            // Kiểm tra ràng buộc dữ liệu
            $hasOrders = DB::table('order_details')
                ->whereIn('product_variant_id', $product->variants->pluck('id'))
                ->exists();

            if ($hasOrders) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa vĩnh viễn sản phẩm này vì đã có đơn hàng liên quan.'
                ], 400);
            }

            DB::beginTransaction();

            // Xóa ảnh vật lý
            foreach ($product->galleries as $gallery) {
                $imagePath = public_path($gallery->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $gallery->forceDelete();
            }

            // Xóa các biến thể
            $product->variants()->forceDelete();

            // Lưu thông tin trước khi xóa
            $productName = $product->name;
            $productId = $product->id;

            // Xóa vĩnh viễn sản phẩm
            $product->forceDelete();

            DB::commit();

            Log::info('Sản phẩm đã được xóa vĩnh viễn', [
                'product_id' => $productId,
                'product_name' => $productName,
                'force_deleted_by' => auth()->user()->id ?? 'system',
                'force_deleted_at' => now()
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sản phẩm đã được xóa vĩnh viễn!'
                ]);
            }

            return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa vĩnh viễn!');

        } catch (\Throwable $e) {
            DB::rollBack();
            
            Log::error('Lỗi khi xóa vĩnh viễn sản phẩm', [
                'product_id' => $product->id ?? 'unknown',
                'error' => $e->getMessage(),
                'user_id' => auth()->user()->id ?? 'system'
            ]);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xóa vĩnh viễn sản phẩm: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Lỗi khi xóa vĩnh viễn sản phẩm: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load('variants', 'galleries', 'category');
        return view('admin.products.detail', compact('product'));
    }

    public function thongke()
    {
        return view('admin.thongke.thongke');
    }
}