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

        // Chỉ được hủy khi trạng thái là 'pending' hoặc 'processing'
    if (!in_array($order->status, ['pending', 'processing'])) {
        return redirect()->route('client.orders.index')
                         ->with('error', 'Đơn hàng đã được xử lý, không thể hủy.');
    }
        $order->update(['status' => 'cancelled']);

        return redirect()->route('client.orders.index')->with('success', 'Đơn hàng đã được hủy.');
    }
    public function reorder($id)
{
    $order = Order::with('orderDetails')->where('user_id', auth()->id())->findOrFail($id);

    $cart = \App\Models\Cart::firstOrCreate(['user_id' => auth()->id()]);

    foreach ($order->orderDetails as $detail) {
        $item = \App\Models\CartItem::firstOrNew([
            'cart_id' => $cart->id,
            'product_variant_id' => $detail->product_variant_id,
        ]);
        $item->quantity += $detail->quantity;
        $item->save();
    }

    return redirect()->route('cart.index')->with('success', 'Đã thêm lại sản phẩm vào giỏ hàng.');
}

public function returnOrder($id)
{
    $order = \App\Models\Order::where('user_id', auth()->id())
        ->where('status', 'delivered') // Chỉ đơn đã giao mới được trả hàng
        ->findOrFail($id);

    $order->status = 'returned';
    $order->save();

    return redirect()->route('client.orders.show', $order->id)->with('success', 'Yêu cầu trả hàng đã được ghi nhận.');
}


}
