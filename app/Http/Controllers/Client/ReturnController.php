<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Client\StoreReturnRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ReturnRequest;

class ReturnController extends Controller
{
    public function index(Order $order) {
    $this->authorize('own-order', $order);
    $requests = ReturnRequest::with(['orderDetail','product','variant','pot','transactions'])
        ->where('order_id', $order->id)
        ->latest()
        ->paginate(10);
    return view('client.orders.returns.index', compact('order','requests'));
}

public function store(StoreReturnRequest $request, Order $order) {
    $this->authorize('own-order', $order);

    if (empty($order->delivered_at)) {
        return back()->withErrors(['return' => 'Đơn chưa ở trạng thái đã giao.']);
    }
    $deadline = \Carbon\Carbon::parse($order->delivered_at)->addDays(7);
    if (now()->greaterThan($deadline)) {
        return back()->withErrors(['return' => 'Đã quá hạn 7 ngày kể từ khi giao.']);
    }

    $detail = OrderDetail::with(['product','variant'])->findOrFail($request->order_detail_id);
    if ($detail->order_id !== $order->id) abort(403);

    $qtyWant = (int) $request->quantity;

    // ✅ PHÂN TÍCH RETURN_ITEMS để tách plant_quantity và pot_quantity
    $returnItems = $request->input('return_items', []);
    $plantQuantity = 0;
    $potQuantity = 0;
    
    // Logic tách dựa vào UI frontend
    if (in_array('plant', $returnItems) && in_array('pot', $returnItems)) {
        // Trả cả cây và chậu → số lượng bằng nhau
        $plantQuantity = $qtyWant;
        $potQuantity = $qtyWant;
    } elseif (in_array('plant', $returnItems)) {
        // Chỉ trả cây
        $plantQuantity = $qtyWant;
        $potQuantity = 0;
    } elseif (in_array('pot', $returnItems)) {
        // Chỉ trả chậu
        $plantQuantity = 0;
        $potQuantity = $qtyWant;
    } else {
        return back()->withErrors(['return_items' => 'Vui lòng chọn ít nhất cây hoặc chậu để trả.']);
    }

    $images = [];
    if ($request->hasFile('images')) {
        foreach (array_slice($request->file('images'), 0, 5) as $img) {
            $images[] = $img->store('returns', 'public');
        }
    }

    ReturnRequest::create([
        'order_id'            => $order->id,
        'order_detail_id'     => $detail->id,
        'user_id'             => $order->user_id,
        'product_id'          => $detail->product_id,
        'product_variant_id'  => $detail->product_variant_id,
        'pot_id'              => $detail->pot_id ?? null,
        'quantity'            => $qtyWant, // Tổng số lượng
        'plant_quantity'      => $plantQuantity, // ✅ Số lượng cây
        'pot_quantity'        => $potQuantity,   // ✅ Số lượng chậu
        'reason'              => $request->reason,
        'images'              => $images,
        'status'              => 'pending',
        'bank_name'           => $request->bank_name,
        'bank_account_name'   => $request->bank_account_name,
        'bank_account_number' => $request->bank_account_number,
    ]);

    return redirect()->route('client.orders.returns.index', $order)
        ->with('success', 'Đã gửi yêu cầu trả hàng.');
}

}
