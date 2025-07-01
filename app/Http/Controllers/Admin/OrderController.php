<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng (tạm thời dùng view trực tiếp)
     */
    public function index()
    {
        $orders = Order::with(['user', 'address'])->orderBy('created_at', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function show($id)
    {
        $order = Order::with([
            'user',
            'address',
            'orderDetails.product',
            'orderDetails.productVariant'
        ])->findOrFail($id);

        return view('admin.order.show', compact('order'));
    }

    /**
     * Xóa mềm đơn hàng
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();


        return redirect()->route('admin.orders.index')->with('success', 'Xóa đơn hàng thành công!');
    }

    /**
     * Khôi phục đơn hàng
     */
    public function restore($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->restore();

        return redirect()->route('admin.orders.index')->with('success', 'Khôi phục đơn hàng thành công!');
    }

    /**
     * Xóa vĩnh viễn
     */
    public function forceDelete($id)
    {
        $order = Order::onlyTrashed()->findOrFail($id);
        $order->forceDelete();

        return redirect()->route('admin.orders.index')->with('success', 'Xóa vĩnh viễn đơn hàng!');
    }
}
