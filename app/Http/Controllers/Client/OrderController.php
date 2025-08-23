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
                // ✅ XỬ LÝ CÁC TRẠNG THÁI TRẢ HÀNG MỚI
                if (in_array($request->status, ['delivered_with_returns', 'delivered_fully_returned', 'delivered_partial_returned'])) {
                    $query->where('status', 'delivered')
                          ->whereHas('returnRequests', function ($returnQuery) use ($request) {
                              $returnQuery->whereIn('status', ['refunded', 'exchanged']);
                              
                              // Lọc theo loại trả hàng
                              if ($request->status === 'delivered_fully_returned') {
                                  // Logic sẽ được xử lý trong eager loading
                              } elseif ($request->status === 'delivered_partial_returned') {
                                  // Logic sẽ được xử lý trong eager loading
                              }
                          });
                } else {
                    $query->where('status', $request->status); // chỉ lọc theo status
                }
            })
            ->with(['returnRequests' => function ($query) {
                $query->whereIn('status', ['refunded', 'exchanged']);
            }])
            ->latest()
            ->paginate(6) // ✅ THÊM PHÂN TRANG (6 đơn hàng mỗi trang)
            ->appends($request->query());

        // ✅ LỌC THÊM SAU KHI LOAD DỮ LIỆU
        if (in_array($request->status, ['delivered_fully_returned', 'delivered_partial_returned'])) {
            $filteredCollection = $orders->getCollection()->filter(function ($order) use ($request) {
                if (!$order) return false; // ✅ Kiểm tra order không null
                
                $returnedQty = $order->total_returned_quantity ?? 0;
                $totalQty = $order->total_items_quantity ?? 0;
                
                if ($request->status === 'delivered_fully_returned') {
                    return $returnedQty >= $totalQty && $returnedQty > 0;
                } elseif ($request->status === 'delivered_partial_returned') {
                    return $returnedQty > 0 && $returnedQty < $totalQty;
                }
                
                return true;
            })->values(); // ✅ Reset keys để tránh lỗi index

            // Update the collection in the paginator
            $orders->setCollection($filteredCollection);
        }

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
