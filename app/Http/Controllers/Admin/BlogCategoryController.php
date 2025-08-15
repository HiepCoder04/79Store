<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = BlogCategory::query();

    // Lọc theo tên danh mục
    if ($request->filled('q')) {
        $q->where('name', 'like', '%'.$request->q.'%'); // nếu cột khác, đổi 'name' cho khớp
    }

    $categories = $q->latest()
        ->paginate(15)
        ->appends($request->query()); // giữ tham số khi phân trang

    return view('admin.category_blogs.index', compact('categories'));
    }

    public function create()
    {
        // folder: resources/views/admin/category_blogs/create.blade.php
        return view('admin.category_blogs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // bảng thực tế là category_blogs, cột name
            'name' => 'required|string|max:255|unique:category_blogs,name',
        ]);

        BlogCategory::create($request->only('name'));

        // route resource name: admin.category_blogs.index
        return redirect()
            ->route('admin.category_blogs.index')
            ->with('success', 'Danh mục blog đã được tạo thành công');
    }

    public function edit(BlogCategory $category_blog)
    {
        return view('admin.category_blogs.edit', ['blogCategory' => $category_blog]);
    }

    public function update(Request $request, BlogCategory $category_blog)
    {
        $request->validate([
            'name' => "required|string|max:255|unique:category_blogs,name,{$category_blog->id}",
        ]);

        $category_blog->update($request->only('name'));

        return redirect()
            ->route('admin.category_blogs.index')
            ->with('success', 'Danh mục blog đã được cập nhật thành công');
    }

    public function destroy(BlogCategory $category_blog)
    {
        if ($category_blog->blogs()->count() > 0) {
            return redirect()
                ->route('admin.category_blogs.index')
                ->with('error', 'Không thể xóa danh mục này vì đang có bài viết thuộc danh mục');
        }

        $category_blog->delete();

        return redirect()
            ->route('admin.category_blogs.index')
            ->with('success', 'Danh mục blog đã được xóa thành công');
    }
}
