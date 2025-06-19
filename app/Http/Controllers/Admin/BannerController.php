<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $errors = [];
    
        if (!$request->hasFile('image')) {
            $errors[] = 'Ảnh banner là bắt buộc.';
        } elseif (!$request->file('image')->isValid()) {
            $errors[] = 'Tệp ảnh không hợp lệ.';
        }
    
        if ($request->link && !filter_var($request->link, FILTER_VALIDATE_URL)) {
            $errors[] = 'Liên kết không đúng định dạng URL.';
        }
    
        if (count($errors)) {
            return back()->withInput()->with('errors', $errors);
        }
    
        // Upload ảnh
        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/banners'), $filename);
    
        Banner::create([
            'image' => 'uploads/banners/' . $filename,
            'link' => $request->link,
            'description' => $request->description,
            'is_active' => $request->is_active ?? 0
        ]);
    
        return back()->with('success', 'Thêm banner thành công!');
    }


    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $errors = [];
    
        // Validate file nếu có file
        if ($request->hasFile('image')) {
            if (!$request->file('image')->isValid()) {
                $errors[] = 'Tệp ảnh không hợp lệ.';
            }
        }
    
        if ($request->link && !filter_var($request->link, FILTER_VALIDATE_URL)) {
            $errors[] = 'Liên kết không đúng định dạng URL.';
        }
    
        if (count($errors)) {
            return back()->withInput()->with('errors', $errors);
        }
    
        // Nếu có ảnh mới thì lưu ảnh
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/banners'), $filename);
            $banner->image = 'uploads/banners/' . $filename;
        }
    
        $banner->link = $request->link;
        $banner->description = $request->description;
        $banner->is_active = $request->is_active ?? 0;
        $banner->save();
    
        return back()->with('success', 'Cập nhật banner thành công!');
    }
    

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return back()->with('success', 'Đã xóa banner!');
    }
}
