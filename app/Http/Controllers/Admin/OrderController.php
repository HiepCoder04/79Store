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
        $orders = Order::with('orderDetails')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    // Xem chi tiết 1 đơn hàng
    public function show($id)
    {
        $order = Order::with('orderDetails')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
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
        $order = Order::findOrFail($id);
        $order->update($request->only(['status', 'shipping_method', 'payment_method']));
        return redirect()->route('orders.index')->with('success', 'Cập nhật đơn hàng thành công.');
    }

    // (Tuỳ chọn) Xoá đơn hàng
    public function destroy($id)
    {
        Order::destroy($id);
        return redirect()->route('orders.index')->with('success', 'Xoá đơn hàng thành công.');
    }

}
