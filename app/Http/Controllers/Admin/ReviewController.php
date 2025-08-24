<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with(['user', 'product'])
        ->search($request, ['comment']) // dùng traits để search theo comment
        ->filter($request, [
            'rating' => 'exact', // lọc số sao
        ])
        // lọc theo tên user
        ->when($request->filled('user_name'), function($q) use ($request) {
            $q->whereHas('user', function($sub) use ($request) {
                $sub->where('name', 'LIKE', '%' . $request->user_name . '%');
            });
        })
        // lọc theo tên sản phẩm
        ->when($request->filled('product_name'), function($q) use ($request) {
            $q->whereHas('product', function($sub) use ($request) {
                $sub->where('name', 'LIKE', '%' . $request->product_name . '%');
            });
        })
        ->orderByDesc('created_at')
        ->paginate(10)
        ->appends($request->query());

    return view('admin.reviews.index', compact('reviews'));
    }

    public function reply(Request $request, Review $review)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:2000'
        ]);

        $review->update([
            'admin_reply' => $request->admin_reply
        ]);

        return back()->with('success', 'Đã phản hồi đánh giá!');
    }

    public function destroy(Review $review)
    {
        // Xóa ảnh nếu có
        if ($review->image_path && Storage::disk('public')->exists($review->image_path)) {
            Storage::disk('public')->delete($review->image_path);
        }

        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá!');
    }
}
