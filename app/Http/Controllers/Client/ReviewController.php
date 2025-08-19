<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\OrderDetail;
class ReviewController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'order_detail_id' => 'required|exists:order_details,id',
        'rating'          => 'required|integer|min:1|max:5',
        'comment'         => 'nullable|string|max:2000',
        'image_path'      => 'nullable|image|max:2048',
    ]);

    $orderDetail = OrderDetail::with('order')->findOrFail($request->order_detail_id);

    // Check quyền
    if ($orderDetail->order->user_id !== auth()->id()) {
        return back()->with('error', 'Bạn không có quyền đánh giá sản phẩm này.');
    }

    // Check trạng thái
    if ($orderDetail->order->status !== 'delivered') {
        return back()->with('error', 'Chỉ được đánh giá sau khi đơn đã giao.');
    }

    // Check đã đánh giá chưa
    if (Review::where('order_detail_id', $orderDetail->id)
        ->where('user_id', auth()->id())
        ->exists()) {
        return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
    }

    // Upload ảnh
    $path = $request->hasFile('image_path')
        ? $request->file('image_path')->store('reviews', 'public')
        : null;

    Review::create([
        'product_id'      => $orderDetail->product_id,
        'user_id'         => auth()->id(),
        'order_detail_id' => $orderDetail->id,
        'rating'          => $request->rating,
        'comment'         => $request->comment,
        'image_path'      => $path,
    ]);
    if ($orderDetail->order->status !== 'delivered') {
    dd('Status hiện tại:', $orderDetail->order->status);
}
    return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
}

}
