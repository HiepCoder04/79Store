<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Log;

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
        'category_id' => 'required|exists:categories,id',
        'variants' => 'required|array|min:1',
        'variants.*.size' => 'nullable|max:50',
        'variants.*.pot' => 'nullable|max:50',
        'variants.*.price' => 'nullable|numeric|min:0',
        'variants.*.stock_quantity' => 'nullable|integer|min:0',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    DB::beginTransaction();

    try {
        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'category_id' => $validated['category_id'],
            'is_active' => true,
        ]);

        foreach ($validated['variants'] as $variant) {
            $isValid =
    array_key_exists('size', $variant) &&
    filled($variant['size']) && // dùng Laravel helper để check KHÔNG RỖNG
    is_numeric($variant['price'] ?? null) &&
    is_numeric($variant['stock_quantity'] ?? null);


            \Log::debug($isValid ? '✅ Biến thể OK' : '❌ Bị loại', $variant);

            if ($isValid) {
                
                $product->variants()->create([
                    'size' => $variant['size'] ?? '',
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
        'category_id' => 'required|exists:categories,id',
        'variants' => 'nullable|array',
        'variants.*.size' => 'nullable|string|max:50',
        'variants.*.pot' => 'nullable|string|max:50',
        'variants.*.price' => 'nullable|numeric|min:0',
        'variants.*.stock_quantity' => 'nullable|integer|min:0',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    DB::beginTransaction();
    try {
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category_id' => $request->category_id,
        ]);

        // Cập nhật hoặc thêm mới biến thể
        if ($request->filled('variants')) {
            foreach ($request->variants as $variant) {
                if (
                    !empty($variant['size']) &&
                    isset($variant['price'], $variant['stock_quantity']) &&
                    is_numeric($variant['price']) &&
                    is_numeric($variant['stock_quantity'])
                ) {
                    if (!empty($variant['id'])) {
                        // Update variant
                        $product->variants()->where('id', $variant['id'])->update([
                            'size' => $variant['size'],
                            'pot' => $variant['pot'] ?? null,
                            'price' => $variant['price'],
                            'stock_quantity' => $variant['stock_quantity'],
                        ]);
                    } else {
                        // Create new variant
                        $product->variants()->create([
                            'size' => $variant['size'],
                            'pot' => $variant['pot'] ?? null,
                            'price' => $variant['price'],
                            'stock_quantity' => $variant['stock_quantity'],
                        ]);
                    }
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
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa!');
    }

    public function show(Product $product)
    {
        $product->load('variants', 'galleries', 'category');
        return view('admin.products.detail', compact('product'));
    }
}
