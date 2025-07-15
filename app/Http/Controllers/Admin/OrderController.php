<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index()
    {
        $orders = Order::with(['orderDetails', 'user'])->latest()->paginate(10);

        foreach ($orders as $order) {
            $order->total_amount = $order->orderDetails->sum(function ($item) {
                return $item->quantity * $item->price;
            });
        }

        return view('admin.orders.index', compact('orders'));
    }

    // Xem chi tiết 1 đơn hàng
    public function show($id)
    {
        $order = Order::with([
            'user.addresses',
            'orderDetails.productVariant.product'
        ])->findOrFail($id);

        // Tổng tiền trước giảm
        $totalBeforeDiscount = 0;
        foreach ($order->orderDetails as $item) {
            $totalBeforeDiscount += $item->price * $item->quantity;
        }

        $discount = $order->discount ?? 0;
        $total = $totalBeforeDiscount - $discount;

        return view('admin.orders.show', compact('order', 'totalBeforeDiscount', 'discount', 'total'));
    }

    // Form chỉnh sửa đơn hàng
    public function edit($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    // Cập nhật đơn hàng
    public function update(Request $request, $id)
    {
         $request->validate([
        'status' => 'required|in:pending,confirmed,shipping,delivered,cancelled',
        'payment_method' => 'nullable|string|max:255',
        'shipping_method' => 'nullable|string|max:255',
    ]);
        $order = Order::findOrFail($id);
        dd($request->all());
        $order->update($request->only(['status', 'shipping_method', 'payment_method']));
        return redirect()->route('orders.index')->with('success', 'Cập nhật đơn hàng thành công.');
    }

    // (Tuỳ chọn) Xoá đơn hàng
    public function destroy($id)
    {
        Order::destroy($id);
        return redirect()->route('orders.index')->with('success', 'Xoá đơn hàng thành công.');
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->input('status');
        $order->save();

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }
}
