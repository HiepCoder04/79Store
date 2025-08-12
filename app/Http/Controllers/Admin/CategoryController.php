<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // Hiển thị danh sách danh mục kèm thông tin parent
    public function index()
    {
        $categories = Category::with('parent')->get();
        return view('admin.categories.index', compact('categories'));
    }

    // Form thêm danh mục
    public function create()
    {
        $parents = Category::whereNull('parent_id')->get(); // chỉ lấy cấp 1
        return view('admin.categories.create', compact('parents'));
    }

    // Lưu danh mục mới với validation unique cho name
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $parent = Category::find($value);
                        if (!$parent || $parent->parent_id !== null) {
                            $fail('Danh mục cha phải là cấp 1.');
                        }
                    }
                }
            ]
        ]);

        Category::create($request->only('name', 'parent_id'));

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công.');
    }

    // Redirect về danh sách (không dùng show chi tiết)
    public function show(string $id)
    {
        return redirect()->route('admin.categories.index');
    }

    // Form sửa danh mục (loại trừ chính nó khỏi danh sách parent)
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        $parents = Category::whereNull('parent_id')->where('id', '!=', $id)->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }
    // Cập nhật danh mục với validation unique bỏ qua chính nó
    public function update(Request $request, string $id)
    {
         $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'parent_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value) {
                        if ($value == $id) {
                            $fail('Không thể chọn chính nó làm cha.');
                        }
                        $parent = Category::find($value);
                        if (!$parent || $parent->parent_id !== null) {
                            $fail('Danh mục cha phải là cấp 1.');
                        }
                    }
                }
            ]
        ]);

        $category->update($request->only('name', 'parent_id'));

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }


    // Xóa danh mục, kiểm tra nếu còn danh mục con thì báo lỗi
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        // Nếu là cha và có con thì không cho xóa
        if (Category::where('parent_id', $category->id)->exists()) {
            return back()->with('error', 'Không thể xóa danh mục cha khi còn danh mục con.');
        }

        $category->delete();

        return back()->with('success', 'Xóa danh mục thành công.');
    }
}
