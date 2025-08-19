<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cancellation;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusMail;
use App\Models\ProductVariant;
use App\Models\Pot;

class CancellationController extends Controller
{
    public function index()
    {
        $cancellations = Cancellation::with(['order', 'user'])->latest()->paginate(10);
        return view('admin.cancellations.index', compact('cancellations'));
    }

    public function show(Cancellation $cancellation)
    {
        $cancellation->loadMissing('order.orderDetails.productVariant.product');
        return view('admin.cancellations.show', compact('cancellation'));
    }

    public function approve(Cancellation $cancellation, Request $request)
    {
        $cancellation->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
        ]);
        // Cập nhật trạng thái đơn hàng
        $order = $cancellation->order;

        // Chuyển đơn sang trạng thái hủy
        $order->update(['status' => 'cancelled']);
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
        // Gửi mail xác nhận hủy
        Mail::to($cancellation->user->email)->send(
            new OrderStatusMail(
                $cancellation->order,
                "Yêu cầu hủy đơn hàng của bạn đã được **chấp nhận**.\n\nGhi chú: " . $request->admin_note
            )
        );

        return redirect()->route('admin.cancellations.index')->with('success', 'Đã chấp nhận hủy đơn.');
    }

    public function reject(Cancellation $cancellation, Request $request)
    {
        $cancellation->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
        ]);
        $order = $cancellation->order;

        // Trả lại trạng thái "đã xác nhận"
        if ($order->status === 'cancel_requested') {
            $order->update(['status' => 'confirmed']);
        }
        // Gửi mail từ chối hủy
        Mail::to($cancellation->user->email)->send(
            new OrderStatusMail(
                $cancellation->order,
                "Yêu cầu hủy đơn hàng của bạn đã bị **từ chối**.\n\nLý do: " . $request->admin_note
            )
        );

        return redirect()->route('admin.cancellations.index')->with('success', 'Đã từ chối yêu cầu hủy.');
    }
}
