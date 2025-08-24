<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // Hiển thị danh sách danh mục kèm thông tin parent
    public function index(Request $request)
    {
        $query = Category::with('parent');
        
        // Tìm kiếm theo tên
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Lọc theo danh mục cha
        if ($request->filled('parent_id')) {
            if ($request->parent_id === 'root') {
                // Chỉ hiển thị danh mục gốc (không có parent)
                $query->whereNull('parent_id');
            } else {
                // Hiển thị danh mục con của parent_id cụ thể
                $query->where('parent_id', $request->parent_id);
            }
        }
        
        $categories = $query->orderBy('parent_id', 'asc')
                          ->orderBy('name', 'asc')
                          ->paginate(10)
                          ->appends($request->query());

        // Chỉ lấy danh mục cha (cấp 1) để làm filter
        $allParents = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'allParents'));
    }

    // Form thêm danh mục
    public function create()
    {
        // Chỉ lấy danh mục cha (cấp 1) để làm parent
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parents'));
    }

    // Lưu danh mục mới với validation chặt chẽ hơn
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'unique:categories,name',
                'regex:/^[a-zA-ZÀ-ỹ0-9\s\-\_\.]+$/u' // Chỉ cho phép chữ, số, dấu cách, gạch ngang, gạch dưới, chấm
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $parent = Category::find($value);
                        
                        // Kiểm tra parent phải là danh mục cấp 1 (không có parent)
                        if ($parent && $parent->parent_id !== null) {
                            $fail('Chỉ có thể chọn danh mục gốc làm danh mục cha. Hệ thống chỉ hỗ trợ 2 cấp danh mục.');
                        }
                        
                        // Kiểm tra parent có tồn tại không
                        if (!$parent) {
                            $fail('Danh mục cha không tồn tại.');
                        }
                    }
                }
            ]
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.min' => 'Tên danh mục phải có ít nhất 2 ký tự.',
            'name.max' => 'Tên danh mục không được quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.regex' => 'Tên danh mục chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: - _ .',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.',
        ]);

        // Tạo danh mục mới
        Category::create([
            'name' => trim($request->name),
            'parent_id' => $request->parent_id ?: null,
        ]);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Thêm danh mục thành công.');
    }

    // Redirect về danh sách (không dùng show chi tiết)
    public function show(string $id)
    {
        return redirect()->route('admin.categories.index');
    }

    // Form sửa danh mục
    public function edit(string $id)
    {
        $category = Category::with(['parent', 'children', 'products'])->findOrFail($id);
        
        // Lấy danh sách parent (loại trừ chính nó và các con của nó)
        $parents = Category::whereNull('parent_id')
                          ->where('id', '!=', $id)
                          ->whereNotIn('id', function($query) use ($id) {
                              // Loại trừ các danh mục con của category hiện tại
                              $query->select('parent_id')
                                    ->from('categories')
                                    ->where('parent_id', $id)
                                    ->whereNotNull('parent_id');
                          })
                          ->orderBy('name')
                          ->get();
        
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    // Cập nhật danh mục với validation nghiêm ngặt
    public function update(Request $request, string $id)
    {
        $category = Category::with(['children', 'products'])->findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'unique:categories,name,' . $id,
                'regex:/^[a-zA-ZÀ-ỹ0-9\s\-\_\.]+$/u'
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($id, $category) {
                    if ($value) {
                        // Không thể chọn chính nó làm parent
                        if ($value == $id) {
                            $fail('Không thể chọn chính danh mục này làm danh mục cha.');
                        }
                        
                        $parent = Category::find($value);
                        if ($parent) {
                            // Parent phải là danh mục gốc
                            if ($parent->parent_id !== null) {
                                $fail('Chỉ có thể chọn danh mục gốc làm danh mục cha.');
                            }
                            
                            // Không thể chọn con của mình làm parent (tránh vòng lặp)
                            if ($parent->parent_id == $id) {
                                $fail('Không thể chọn danh mục con làm danh mục cha.');
                            }
                        }
                        
                        // Nếu category hiện tại đang có children và muốn set parent_id
                        if ($category->hasChildren()) {
                            $fail('Không thể đặt danh mục cha cho danh mục đang có danh mục con. Hệ thống chỉ hỗ trợ 2 cấp.');
                        }
                    }
                }
            ]
        ], [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.min' => 'Tên danh mục phải có ít nhất 2 ký tự.',
            'name.max' => 'Tên danh mục không được quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.regex' => 'Tên danh mục chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: - _ .',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.',
        ]);

        $category->update([
            'name' => trim($request->name),
            'parent_id' => $request->parent_id ?: null,
        ]);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Cập nhật danh mục thành công.');
    }

    // Xóa danh mục với kiểm tra ràng buộc
    public function destroy(string $id)
    {
        $category = Category::with(['children', 'products'])->findOrFail($id);

        // Kiểm tra có danh mục con không
        if ($category->hasChildren()) {
            return back()->with('error', 'Không thể xóa danh mục này vì còn có danh mục con. Vui lòng xóa danh mục con trước.');
        }

        // Kiểm tra có sản phẩm không
        if ($category->products()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục này vì còn có sản phẩm. Vui lòng di chuyển sản phẩm sang danh mục khác trước.');
        }

        $category->delete();

        return back()->with('success', 'Xóa danh mục thành công.');
    }
}
