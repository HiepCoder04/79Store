<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class OrderController extends Controller
{
    //
    public function index()
    {
        $orders = Order::with('orderDetails.productVariant.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('client.users.order', compact('orders'));
    }
    public function show($id)
    {
        $order = Order::with([
            'orderDetails.productVariant.product.galleries'
        ])->where('user_id', auth()->id())->findOrFail($id);

        return view('client.users.order-detail', compact('order'));
    }
    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này.');
        }

        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')->with('error', 'Chỉ có thể hủy đơn hàng đang chờ thanh toán.');
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('orders.index')->with('success', 'Đơn hàng đã được hủy.');
    }
}
