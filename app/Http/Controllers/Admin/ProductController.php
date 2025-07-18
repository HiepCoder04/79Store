<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
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
            'variants.*.height' => 'nullable|string|max:100',
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
                $isValid =
                 
                    is_numeric($variant['price'] ?? null) &&
                    is_numeric($variant['stock_quantity'] ?? null);


                \Log::debug($isValid ? '✅ Biến thể OK' : '❌ Bị loại', $variant);

                if ($isValid) {

                    $product->variants()->create([
                        'variant_name' => $product->name . ' - ' . ($variant['pot'] ?? 'Không rõ'),
                        'pot' => $variant['pot'] ?? null,
                        'height' => $variant['height'] ?? null,
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
                            \Log::warning('Không thể lưu ảnh, $path rỗng.');
                        }
                    } else {
                        \Log::warning(' File ảnh không hợp lệ.');
                    }
                }
            }


            DB::commit();

            return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được thêm thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('❌ Lỗi khi thêm sản phẩm: ' . $e->getMessage());
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
           'variants.*.height' => 'nullable|string|max:100',
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
                                'height' => $variant['height'] ?? null,
                                'price' => $variant['price'],
                                'stock_quantity' => $variant['stock_quantity'],
                            ]);
                        } else {
                            // Create new variant
                            $product->variants()->create([
                                    'variant_name' => $product->name . ' - ' . ($variant['pot'] ?? 'Không rõ'),
                                'pot' => $variant['pot'] ?? null,
                                'height' => $variant['height'] ?? null,
                                'price' => $variant['price'],
                                'stock_quantity' => $variant['stock_quantity'],
                            ]);
                        }
                    }
                }
            }
            //xóa ảnh đã chọn
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

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
            return back()->with('error', 'Đã xảy ra lỗi khi cập nhật sản phẩm.')->withInput();
        }
    }


    public function destroy(Product $product)
{
    try {
        // Xoá ảnh vật lý nếu cần
        foreach ($product->galleries as $gallery) {
            $imagePath = public_path($gallery->image);
            if (file_exists($imagePath)) {
                unlink($imagePath); // Xoá file
            }
            $gallery->delete(); // Xoá record DB
        }

        // Xoá các biến thể sản phẩm
        $product->variants()->delete();

        // Xoá chính sản phẩm
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa!');
    } catch (\Throwable $e) {
        \Log::error('Lỗi khi xoá sản phẩm: ' . $e->getMessage());
        return back()->with('error', 'Lỗi khi xóa sản phẩm!');
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
