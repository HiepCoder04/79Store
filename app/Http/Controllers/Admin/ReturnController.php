<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RefundReturnRequest;
use App\Models\ReturnTransaction;   
use Illuminate\Support\Facades\DB;  
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ReturnRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusMail;
use Illuminate\Support\Facades\Log;

class ReturnController extends Controller
{
    public function index(Request $request) {
    $q = ReturnRequest::with(['order','user','product','variant','pot'])
        ->latest();

    // ✅ THÊM LỌC THEO ORDER_ID
    if ($request->filled('order_id')) {
        $q->where('order_id', $request->order_id);
    }

    if ($request->filled('status'))   $q->where('status', $request->status);
    if ($request->filled('order_id')) $q->where('order_id', $request->order_id);
    $items = $q->paginate(20);
    return view('admin.returns.index', compact('items'));
}

public function show($id) {
    $item = ReturnRequest::with(['transactions','order','orderDetail','product','variant','pot','user'])
        ->findOrFail($id);
    
    // ✅ Tính toán giá trị hoàn tiền đề xuất ĐÚNG
    $suggestedAmount = 0;
    if ($item->orderDetail) {
        $productPrice = $item->orderDetail->product_price ?? 0;
        $potPrice = $item->orderDetail->pot_price ?? 0;
        
        // Tính riêng từng loại
        $plantRefund = $productPrice * ($item->plant_quantity ?? 0);
        $potRefund = $potPrice * ($item->pot_quantity ?? 0);
        $suggestedAmount = $plantRefund + $potRefund;
    }
    
    return view('admin.returns.show', compact('item', 'suggestedAmount'));
}

public function approve($id, Request $request) {
    $request->validate([
        'admin_note' => 'nullable|string|max:500',
    ], [
        'admin_note.max' => 'Ghi chú không quá 500 ký tự.',
    ]);

    $item = ReturnRequest::findOrFail($id);
    if ($item->status !== 'pending') {
        return back()->withErrors(['status' => 'Chỉ duyệt yêu cầu ở trạng thái pending.']);
    }
    $item->status = 'approved';
    $item->admin_note = $request->input('admin_note');
    $item->save();
    if ($item->user && $item->user->email) {
        $statusText = "Yêu cầu trả hàng của bạn đã được duyệt và chúng tôi đã hoàn tiền vào tài khoản của bạn";
        try {
            Mail::to($item->user->email)->send(new OrderStatusMail($item->order, $statusText));
        } catch (\Exception $ex) {
            Log::error('Lỗi gửi mail duyệt trả hàng: ' . $ex->getMessage());
        }
    }
    return back()->with('success', 'Đã duyệt yêu cầu.');
}

public function reject($id, Request $request) {
    $request->validate([
        'admin_note' => 'required|string|min:5|max:500',
    ], [
        'admin_note.required' => 'Vui lòng nhập lý do từ chối.',
        'admin_note.min' => 'Lý do từ chối phải có ít nhất 5 ký tự.',
        'admin_note.max' => 'Lý do từ chối không quá 500 ký tự.',
    ]);

    $item = ReturnRequest::findOrFail($id);
    if (!in_array($item->status, ['pending','approved'])) {
        return back()->withErrors(['status' => 'Chỉ từ chối khi yêu cầu đang pending/approved.']);
    }
    $item->status = 'rejected';
    $item->admin_note = $request->input('admin_note');
    $item->resolved_at = now();
    $item->save();
    return back()->with('success', 'Đã từ chối yêu cầu.');
}

public function refund($id, Request $request)
{
    // ✅ Tăng giới hạn ảnh lên 5MB
    $request->validate([
        'amount' => 'required|numeric|min:1',
        'proof_images' => 'required|array|min:1',
        'proof_images.*' => 'required|image|mimes:jpg,jpeg,png|max:5120', // 5MB = 5120KB
        'note' => 'required|string|min:3|max:500',
    ], [
        'amount.required' => 'Vui lòng nhập số tiền hoàn.',
        'amount.numeric' => 'Số tiền phải là số.',
        'amount.min' => 'Số tiền phải lớn hơn 0.',
        'proof_images.required' => 'Vui lòng upload ảnh bằng chứng chuyển khoản.',
        'proof_images.array' => 'Ảnh bằng chứng không hợp lệ.',
        'proof_images.min' => 'Phải có ít nhất 1 ảnh bằng chứng.',
        'proof_images.*.required' => 'Ảnh bằng chứng là bắt buộc.',
        'proof_images.*.image' => 'File phải là hình ảnh.',
        'proof_images.*.mimes' => 'Ảnh phải có định dạng JPG, JPEG hoặc PNG.',
        'proof_images.*.max'   => 'Mỗi ảnh tối đa 5MB.',
        'note.required' => 'Vui lòng nhập ghi chú về việc hoàn tiền.',
        'note.min' => 'Ghi chú phải có ít nhất 3 ký tự.',
        'note.max' => 'Ghi chú không quá 500 ký tự.',
    ]);

    $item = ReturnRequest::with(['variant','pot','transactions'])->findOrFail($id);

    if ($item->status !== 'approved') {
        return back()->withErrors(['status' => 'Chỉ hoàn tiền khi yêu cầu đã được duyệt.']);
    }

    // Chặn refund nếu đã có transaction refund
    $alreadyRefunded = $item->transactions()->where('type','refund')->exists();
    if ($alreadyRefunded) {
        return back()->withErrors(['status' => 'Yêu cầu này đã được hoàn tiền trước đó.']);
    }

    DB::transaction(function () use ($item, $request) {
        // Xử lý upload hình ảnh bằng chứng
        $proofImages = [];
        if ($request->hasFile('proof_images')) {
            foreach (array_slice($request->file('proof_images'), 0, 5) as $img) {
                $proofImages[] = $img->store('return_proofs', 'public');
            }
        }

        // ✅ Lấy số điện thoại ưu tiên từ đơn hàng
        $contactPhone = $item->order->phone ?? $item->user->phone ?? null;

        // ✅ Tạo transaction với type = 'refund' (sẽ hiển thị là "Đã hoàn tiền")
        ReturnTransaction::create([
            'return_request_id'   => $item->id,
            'type'                => 'refund', // Được hiển thị thành "Đã hoàn tiền"
            'amount'              => (int) $request->amount,
            'note'                => $request->note . ($contactPhone ? " | SĐT: {$contactPhone}" : ''),
            'bank_name'           => $item->bank_name,
            'bank_account_name'   => $item->bank_account_name,
            'bank_account_number' => $item->bank_account_number,
            'proof_images'        => $proofImages,
            'processed_at'        => now(),
        ]);

        // Cộng kho (dùng increment để an toàn concurrent)
        if ($item->variant && $item->plant_quantity > 0) {
            $item->variant()->increment('stock_quantity', (int) $item->plant_quantity);
        }
        if ($item->pot && $item->pot_quantity > 0) {
            $item->pot()->increment('quantity', (int) $item->pot_quantity);
        }
        $item->update([
            'status'      => 'refunded',
            'resolved_at' => now(),
        ]);
    });

    return back()->with('success', 'Đã hoàn tiền & cập nhật tồn kho.');
}

public function exchange($id, Request $request) {
    $item = ReturnRequest::findOrFail($id);
    if ($item->status !== 'approved') {
        return back()->withErrors(['status' => 'Chỉ đổi hàng khi yêu cầu đã được duyệt.']);
    }

    DB::transaction(function () use ($item, $request) {
        // ✅ Tạo transaction với type = 'exchange' (sẽ hiển thị là "Đã đổi hàng")
        ReturnTransaction::create([
            'return_request_id' => $item->id,
            'type'              => 'exchange', // Được hiển thị thành "Đã đổi hàng"
            'amount'            => 0,
            'note'              => $request->input('note'),
            'processed_at'      => now(),
        ]);

        $item->status = 'exchanged';
        $item->resolved_at = now();
        $item->save();
    });
    if ($item->user && $item->user->email) {
        $statusText = "Yêu cầu trả hàng của bạn đã bị từ chối. Lý do: " . $item->admin_note;
        try {
            Mail::to($item->user->email)->send(new OrderStatusMail($item->order, $statusText));
        } catch (\Exception $ex) {
            Log::error('Lỗi gửi mail từ chối trả hàng: ' . $ex->getMessage());
        }
    }
    return back()->with('success', 'Đã xử lý đổi hàng.');
}



}
