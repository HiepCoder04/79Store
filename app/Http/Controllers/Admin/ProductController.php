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
use App\Models\Pot;
use App\Models\ProductVariant;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Product::with(['category', 'galleries'])
    ->withMin('variants', 'price')
    ->withMax('variants', 'price')
    ->search($request, ['name', 'slug', 'description']);

        // Lọc danh mục
        if ($request->filled('category_id')) {
            $category = Category::find($request->category_id);
            if ($category) {
                if ($category->parent_id === null) {
                    // Nếu là danh mục cha → lấy tất cả id của nó + id con cháu
                    $ids = $this->getAllChildCategoryIds($category->id);
                    $ids[] = $category->id; // thêm chính nó
                    $query->whereIn('category_id', $ids);
                } else {
                    // Nếu là danh mục con → chỉ lấy sản phẩm của nó
                    $query->where('category_id', $category->id);
                }
            }
        }

        // Lọc trạng thái hoạt động (chấp nhận 0)
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter trạng thái xóa mềm
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
            $query->whereNull('deleted_at');
        }

        $products = $query->latest()->paginate(10)->appends($request->query());

        // Thống kê số lượng
        $stats = [
            'total' => Product::withTrashed()->count(),
            'active' => Product::where('is_active', 1)->whereNull('deleted_at')->count(),
            'deleted' => Product::onlyTrashed()->count()
        ];

        return view('admin.products.index', compact('products', 'stats', 'categories'));
    }
    private function getAllChildCategoryIds($parentId)
    {
        $category = Category::find($parentId);
        if ($category) {
            return $category->getAllChildrenIds();
        }
        return [];
    }

    public function create()
    {
        // Sắp xếp danh mục theo cấp độ và tên
        $categories = Category::with('parent')
                             ->orderBy('parent_id', 'asc')
                             ->orderBy('name', 'asc')
                             ->get();
        $pots = Pot::all();
        return view('admin.products.create', compact('categories', 'pots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'required|string|max:1500|min:10',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'required|array|min:1',
            'variants.*.height' => 'nullable|string|max:100',
            'variants.*.price' => 'required|numeric|min:1000|max:99999999',
            'variants.*.stock_quantity' => 'required|integer|min:0|max:9999',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'selected_pots' => 'nullable|array',
            'selected_pots.*' => 'exists:pots,id',
        ], [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.min' => 'Tên sản phẩm phải có ít nhất 3 ký tự.',
            'name.max' => 'Tên sản phẩm không được quá 255 ký tự.',
            'description.required' => 'Mô tả sản phẩm không được để trống.',
            'description.min' => 'Mô tả phải có ít nhất 10 ký tự.',
            'description.max' => 'Mô tả không được quá 1500 ký tự.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'variants.required' => 'Phải có ít nhất 1 biến thể.',
            'variants.min' => 'Phải có ít nhất 1 biến thể.',
            'variants.*.price.required' => 'Giá là bắt buộc.',
            'variants.*.price.min' => 'Giá phải từ 1.000 VNĐ trở lên.',
            'variants.*.price.max' => 'Giá không được quá 99.999.999 VNĐ.',
            'variants.*.stock_quantity.required' => 'Số lượng tồn là bắt buộc.',
            'variants.*.stock_quantity.min' => 'Số lượng tồn không được âm.',
            'variants.*.stock_quantity.max' => 'Số lượng tồn không được quá 9999.',
            'images.required' => 'Vui lòng chọn ít nhất 1 ảnh sản phẩm.',
            'images.min' => 'Phải có ít nhất 1 ảnh sản phẩm.',
            'images.max' => 'Chỉ được tải lên tối đa 10 ảnh.',
            'images.*.required' => 'Ảnh không được để trống.',
            'images.*.image' => 'File phải là ảnh.',
            'images.*.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'images.*.max' => 'Kích thước ảnh không được quá 5MB.',
        ]);

        DB::beginTransaction();

        try {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'is_active' => true,
            ]);

            foreach ($validated['variants'] as $variant) {
                $isValid = is_numeric($variant['price'] ?? null) &&
                    is_numeric($variant['stock_quantity'] ?? null);

                Log::debug($isValid ? '✅ Biến thể OK' : '❌ Bị loại', $variant);

                if ($isValid) {
                    $newVariant = $product->variants()->create([
                        'variant_name' => $product->name . ' - ' . ($variant['pot'] ?? 'Không rõ'),

                        'height' => $variant['height'] ?? null,
                        'price' => $variant['price'],
                        'stock_quantity' => $variant['stock_quantity']
                    ]);
                    $allPotIds = Pot::pluck('id')->toArray();
                    $newVariant->pots()->attach($allPotIds);
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
            // Xử lý chậu nếu có
            if ($request->filled('selected_pots')) {
                $selectedPotIds = $request->input('selected_pots');
                foreach ($product->variants as $variant) {
                    $variant->pots()->sync($selectedPotIds);
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
        // Sắp xếp danh mục theo cấp độ và tên
        $categories = Category::with('parent')
                             ->orderBy('parent_id', 'asc')
                             ->orderBy('name', 'asc')
                             ->get();
        $pots = Pot::all();
        $selectedPotIds = $product->variants->flatMap->pots->pluck('id')->unique();
        return view('admin.products.edit', compact('product', 'categories', 'pots', 'selectedPotIds'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'required|string|max:1500|min:10',
            'category_id' => 'required|exists:categories,id',
            'variants' => 'nullable|array',
            'variants.*.height' => 'nullable|string|max:100',
            'variants.*.price' => 'nullable|numeric|min:1000|max:99999999',
            'variants.*.stock_quantity' => 'nullable|integer|min:0|max:9999',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'selected_pots' => 'nullable|array',
            'selected_pots.*' => 'exists:pots,id',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:product_galleries,id',
        ], [
            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.min' => 'Tên sản phẩm phải có ít nhất 3 ký tự.',
            'name.max' => 'Tên sản phẩm không được quá 255 ký tự.',
            'description.required' => 'Mô tả sản phẩm không được để trống.',
            'description.min' => 'Mô tả phải có ít nhất 10 ký tự.',
            'description.max' => 'Mô tả không được quá 1500 ký tự.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'variants.*.price.min' => 'Giá phải từ 1.000 VNĐ trở lên.',
            'variants.*.price.max' => 'Giá không được quá 99.999.999 VNĐ.',
            'variants.*.stock_quantity.min' => 'Số lượng tồn không được âm.',
            'variants.*.stock_quantity.max' => 'Số lượng tồn không được quá 9999.',
            'images.max' => 'Chỉ được tải lên tối đa 10 ảnh.',
            'images.*.image' => 'File phải là ảnh.',
            'images.*.mimes' => 'Ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'images.*.max' => 'Kích thước ảnh không được quá 5MB.',
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


                                'height' => $variant['height'] ?? null,
                                'price' => $variant['price'],
                                'stock_quantity' => $variant['stock_quantity'],
                            ]);
                        } else {
                            // Create new variant
                            $newVariant = $product->variants()->create([
                                'variant_name' => $product->name,

                                'height' => $variant['height'] ?? null,
                                'price' => $variant['price'],
                                'stock_quantity' => $variant['stock_quantity'],
                            ]);

                            $allPotIds = Pot::pluck('id')->toArray();
                            $newVariant->pots()->attach($allPotIds);
                        }
                    }
                }
            }

            // Xóa ảnh đã chọn
            if ($request->filled('delete_images')) {
                foreach ($request->delete_images as $galleryId) {
                    $gallery = $product->galleries()->find($galleryId);
                    if ($gallery) {
                        // Optionally: xóa file vật lý luôn
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
            // Xử lý chậu nếu có
            // Gán lại các chậu cho tất cả biến thể nếu người dùng chọn lại
            if ($request->filled('selected_pots')) {
                $selectedPotIds = $request->input('selected_pots');
                foreach ($product->variants as $variant) {
                    $variant->pots()->sync($selectedPotIds);
                }
            } else {
                // Nếu không chọn gì thì xoá hết liên kết
                foreach ($product->variants as $variant) {
                    $variant->pots()->detach();
                }
            }


            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật sản phẩm.');
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
        $product->load('variants.pots', 'galleries', 'category');
        $allPots = $product->variants
        ->flatMap(fn ($v) => $v->pots ?? collect())
        ->unique('id')
        ->values();
        return view('admin.products.detail', compact('product', 'allPots'));
    }

    public function thongke()
    {
        return view('admin.thongke.thongke');
    }
    //xoa bien the cua product
    public function deleteVariant($id)
    {
        // Tìm biến thể theo ID
        $variant = ProductVariant::findOrFail($id);
        $productId = $variant->product_id;

        // Xoá liên kết với chậu nếu có
        $variant->pots()->detach();

        // Xoá chính biến thể
        $variant->delete();

        return redirect()->route('admin.products.edit', $productId)->with('success', 'Xoá biến thể thành công.');
    }
    public function toggleStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->is_active = $request->is_active;
        $product->save();

        return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
    }
}
