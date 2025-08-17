<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cancellation;
use Illuminate\Support\Facades\Auth;

class CancellationController extends Controller
{
    public function store(Request $request, $orderId)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'Bạn cần nhập lý do hủy đơn hàng.',
        ]);

        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Kiểm tra trạng thái đơn hàng, chỉ được hủy khi pending/confirmed
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return redirect()->back()->with('error', 'Đơn hàng đã được xử lý, không thể hủy.');
        }

        // Tạo yêu cầu hủy
        Cancellation::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'status' => 'pending', // admin sẽ duyệt sau
        ]);

        return redirect()->route('client.orders.show', $order->id)
            ->with('success', 'Yêu cầu hủy đơn hàng đã được gửi. Vui lòng chờ xác nhận.');
    }

    /**
     * Admin duyệt/ từ chối yêu cầu hủy
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
        ]);

        $cancellation = Cancellation::findOrFail($id);
        $cancellation->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
        ]);

        // Nếu admin duyệt thì cập nhật trạng thái đơn hàng => cancelled
        if ($request->status === 'approved') {
            $cancellation->order->update(['status' => 'cancelled']);
        }

        return redirect()->back()->with('success', 'Cập nhật yêu cầu hủy thành công.');
    }
}

