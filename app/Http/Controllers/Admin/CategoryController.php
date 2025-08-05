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
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create($request->only('name', 'parent_id'));

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công');
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

    // chỉ lấy các danh mục cha cấp 1 khác chính nó
    $parents = Category::whereNull('parent_id')
        ->where('id', '!=', $category->id)
        ->get();

    return view('admin.categories.edit', compact('category', 'parents'));
}
    // Cập nhật danh mục với validation unique bỏ qua chính nó
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
        ]);

        $category->update($request->only('name', 'parent_id'));

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công');
    }

    // Xóa danh mục, kiểm tra nếu còn danh mục con thì báo lỗi
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        $hasChildren = Category::where('parent_id', $category->id)->exists();

        if ($hasChildren) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Không thể xóa danh mục vì còn danh mục con. Vui lòng xóa danh mục con trước.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Đã xoá danh mục');
    }
}
