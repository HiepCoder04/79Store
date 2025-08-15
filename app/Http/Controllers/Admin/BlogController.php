<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class BlogController extends Controller
{
    public function index(Request $request)
    {
    $q = Blog::with('categoryBlog'); // quan hệ belongsTo

    // Tiêu đề
    if ($request->filled('title')) {
        $q->where('title', 'like', '%'.$request->title.'%');
    }

    // Danh mục
    if ($request->filled('category_blog_id')) {
        $q->where('category_blog_id', $request->category_blog_id);
    }

    // Trạng thái (0/1)
    if ($request->filled('is_active') && in_array($request->is_active, ['0','1'], true)) {
        $q->where('is_active', (int)$request->is_active);
    }

    $blogs = $q->latest()
        ->paginate(15)
        ->appends($request->query());

    $categories = BlogCategory::orderBy('name')->get(['id','name']); // load cho combobox

    return view('admin.blogs.index', compact('blogs','categories'));

    }

    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required',
                'category_id' => 'nullable|exists:category_blogs,id',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        
            // Chuẩn bị dữ liệu
            $data = $request->except(['_token', 'img']);
            
            // Tạo slug và đảm bảo tính duy nhất
            $originalSlug = Str::slug($request->title);
            $slug = $originalSlug;
            $counter = 1;
            
            while (Blog::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $data['slug'] = $slug;
            
            // Xử lý is_active
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            
            // Ánh xạ category_id
            if (isset($data['category_id'])) {
                $data['category_blog_id'] = $data['category_id'];
                unset($data['category_id']);
            } else {
                // Đảm bảo category_blog_id có giá trị NULL nếu không có category_id
                $data['category_blog_id'] = null;
            }
            
            // Xử lý nội dung
            if (!empty($data['content'])) {
                $content = $data['content'];
                if (!preg_match('/<p\b[^>]*>/', $content)) {
                    $paragraphs = preg_split('/\R+/', $content);
                    $wrapped_content = '';
                    foreach ($paragraphs as $para) {
                        if (trim($para) !== '') {
                            $wrapped_content .= "<p>" . trim($para) . "</p>\n";
                        }
                    }
                    $data['content'] = $wrapped_content;
                }
            }
            
            // Xử lý upload ảnh
            if ($request->hasFile('img')) {
                $imageName = time() . '.' . $request->img->extension();
                $path = public_path('uploads/blogs');
                
                // Kiểm tra và tạo thư mục nếu không tồn tại
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                
                $request->img->move($path, $imageName);
                $data['img'] = 'uploads/blogs/' . $imageName;
            }
        
            // Log data trước khi tạo
            Log::info('Blog data before create:', $data);
            
            // Tạo bài viết
            $blog = Blog::create($data);
            Log::info('Blog created with ID: ' . $blog->id);
            
            return redirect()->route('admin.blogs.index')->with('success', 'Blog đã được tạo thành công');
        } catch (Exception $e) {
            Log::error('Error creating blog: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Lỗi khi tạo bài viết: ' . $e->getMessage());
        }
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::all();
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required',
                'category_id' => 'nullable|exists:category_blogs,id',
                'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $data = $request->except(['_token', '_method', 'img', 'remove_image']);
            
            // Tạo slug và đảm bảo tính duy nhất nếu tiêu đề thay đổi
            if ($blog->title != $request->title) {
                $originalSlug = Str::slug($request->title);
                $slug = $originalSlug;
                $counter = 1;
                
                while (Blog::where('slug', $slug)->where('id', '!=', $blog->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
                $data['slug'] = $slug;
            }
            
            // Xử lý is_active
            $data['is_active'] = $request->has('is_active') ? 1 : 0;
            
            // Ánh xạ category_id
            if (isset($data['category_id'])) {
                $data['category_blog_id'] = $data['category_id'];
                unset($data['category_id']);
            } else {
                $data['category_blog_id'] = null;
            }

            // Xử lý nội dung
            if (!empty($data['content'])) {
                $content = $data['content'];
                if (!preg_match('/<p\b[^>]*>/', $content)) {
                    $paragraphs = preg_split('/\R+/', $content);
                    $wrapped_content = '';
                    foreach ($paragraphs as $para) {
                        if (trim($para) !== '') {
                            $wrapped_content .= "<p>" . trim($para) . "</p>\n";
                        }
                    }
                    $data['content'] = $wrapped_content;
                }
            }

            // Xử lý upload ảnh mới
            if ($request->hasFile('img')) {
                // Xóa ảnh cũ nếu có
                if ($blog->img && file_exists(public_path($blog->img))) {
                    unlink(public_path($blog->img));
                }
                
                $imageName = time() . '.' . $request->img->extension();
                $path = public_path('uploads/blogs');
                
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                
                $request->img->move($path, $imageName);
                $data['img'] = 'uploads/blogs/' . $imageName;
            }
            
            // Xử lý xóa ảnh
            if ($request->has('remove_image') && $request->remove_image) {
                if ($blog->img && file_exists(public_path($blog->img))) {
                    unlink(public_path($blog->img));
                }
                $data['img'] = null;
            }

            Log::info('Blog data before update:', $data);
            $blog->update($data);

            return redirect()->route('admin.blogs.index')->with('success', 'Blog đã được cập nhật thành công');
        } catch (Exception $e) {
            Log::error('Error updating blog: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Lỗi khi cập nhật bài viết: ' . $e->getMessage());
        }
    }

    public function destroy(Blog $blog)
    {
        // Xóa ảnh khi xóa blog
        if ($blog->img && file_exists(public_path($blog->img))) {
            unlink(public_path($blog->img));
        }
        
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog đã được xóa thành công');
    }
}