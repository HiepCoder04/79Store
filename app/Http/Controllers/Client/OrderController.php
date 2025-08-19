<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Pot;
use App\Models\ProductVariant;
use App\Models\CartItem;
use App\Models\Cancellation;

class OrderController extends Controller
{
    //
    public function index(Request $request)
    {
        $orders = Order::where('user_id', auth()->id())
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status); // chỉ lọc theo status
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        return view('client.users.order', compact('orders'));
    }
    public function show($id)
    {
        $order = Order::with([
            'orderDetails.productVariant.product.galleries'
        ])->where('user_id', auth()->id())->findOrFail($id);

        return view('client.users.order-detail', compact('order'));
    }
    public function cancel(Request $request, Order $order)
    {
        // Chỉ chủ đơn hàng mới được hủy
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này.');
        }

        if ($order->status === 'pending') {
            // ❌ Không cần validate reason

            // Hủy ngay khi đơn còn ở trạng thái chờ xác nhận
            $order->update(['status' => 'cancelled']);

            // Cộng lại số lượng hàng vào kho
            foreach ($order->orderDetails as $detail) {
                $variant = ProductVariant::find($detail->product_variant_id);
                if ($variant) {
                    $variant->stock_quantity += $detail->quantity;
                    $variant->save();
                }

                if ($detail->product_pot) {
                    $pot = Pot::where('name', $detail->product_pot)->first();
                    if ($pot) {
                        $pot->quantity += $detail->quantity;
                        $pot->save();
                    }
                }
            }

            return redirect()->route('client.orders.index')
                ->with('success', 'Đơn hàng đã được hủy thành công.');
        } elseif ($order->status === 'confirmed') {
            // ✅ Phải nhập lý do
            $request->validate([
                'reason' => 'required|string|max:500',
            ]);

            // Tạo yêu cầu hủy
            Cancellation::create([
                'order_id' => $order->id,
                'user_id'  => auth()->id(),
                'reason'   => $request->reason,
                'status'   => 'pending',
            ]);

            // Cập nhật trạng thái đơn hàng sang "Yêu cầu hủy"
            $order->update(['status' => 'cancel_requested']);

            return redirect()->route('client.orders.index')
                ->with('success', 'Yêu cầu hủy đơn đã được gửi, vui lòng chờ xác nhận từ shop.');
        }

        // ❌ Không thể hủy khi đơn đã giao hoặc huỷ trước đó
        return redirect()->route('client.orders.index')
            ->with('error', 'Đơn hàng đã được giao hoặc không thể hủy.');
    }

    public function reorder($id)
    {
        $order = Order::with('orderDetails')->where('user_id', auth()->id())->findOrFail($id);

        $cart = \App\Models\Cart::firstOrCreate(['user_id' => auth()->id()]);

        foreach ($order->orderDetails as $detail) {
            $variant = ProductVariant::find($detail->product_variant_id);

            if (!$variant || $variant->stock_quantity <= 0) {
                continue; // Bỏ qua nếu hết hàng
            }

            $item = CartItem::firstOrNew([
                'cart_id' => $cart->id,
                'product_variant_id' => $detail->product_variant_id,
            ]);
            $item->quantity += min($detail->quantity, $variant->stock_quantity);
            $item->save();
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm lại sản phẩm vào giỏ hàng.');
    }

    public function returnOrder($id)
    {
        $order = \App\Models\Order::where('user_id', auth()->id())
            ->where('status', 'delivered') // Chỉ đơn đã giao mới được trả hàng
            ->findOrFail($id);
        foreach ($order->orderDetails as $detail) {
            $variant = ProductVariant::find($detail->product_variant_id);
            if ($variant) {
                $variant->stock_quantity += $detail->quantity;
                $variant->save();
            }

            if ($detail->product_pot) {
                $pot = Pot::where('name', $detail->product_pot)->first();
                if ($pot) {
                    $pot->quantity += $detail->quantity;
                    $pot->save();
                }
            }
        }
        $order->status = 'returned';
        $order->save();

        return redirect()->route('client.orders.show', $order->id)->with('success', 'Yêu cầu trả hàng đã được ghi nhận.');
    }
}
