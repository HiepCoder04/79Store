<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UserVoucher;
use Illuminate\Http\JsonResponse;
class VoucherController extends Controller
{
public function apply(Request $request): JsonResponse
{
    $request->validate([
        'voucher_code' => 'required|string',
        'selected' => 'required|array',
    ]);

    $user = auth()->user();
    $code = $request->voucher_code;
    $selectedItemIds = $request->input('selected', []);

    $voucher = Voucher::where('code', $code)->first();

    if (!$voucher) {
        return response()->json(['success' => false, 'message' => 'Mã giảm giá không tồn tại.']);
    }

    $now = now();
    if ($now->lt($voucher->start_date) || $now->gt($voucher->end_date)) {
        return response()->json(['success' => false, 'message' => 'Mã giảm giá đã hết hạn.']);
    }

    if (!$voucher->is_active) {
        return response()->json(['success' => false, 'message' => 'Mã giảm giá không hợp lệ.']);
    }

    $used = DB::table('user_vouchers')
        ->where('user_id', $user->id)
        ->where('voucher_id', $voucher->id)
        ->where('is_used', true)
        ->exists();

    if ($used) {
        return response()->json(['success' => false, 'message' => 'Bạn đã sử dụng mã này rồi.']);
    }

    $selectedItems = \App\Models\CartItem::with('productVariant')
        ->whereIn('id', $selectedItemIds)
        ->whereHas('cart', fn($q) => $q->where('user_id', $user->id))
        ->get();

    $subtotal = $selectedItems->sum(function ($item) {
        return $item->productVariant->price * $item->quantity;
    });

    if ($subtotal < $voucher->min_order_amount) {
        return response()->json(['success' => false, 'message' => 'Đơn hàng chưa đạt ' . number_format($voucher->min_order_amount, 0, ',', '.') . 'đ để áp dụng mã ' . $voucher->code]);
    }

    session()->put('applied_voucher', $voucher->id);

    return response()->json([
    'success' => true,
    'message' => 'Áp dụng mã giảm giá thành công!',
    'voucher_id' => $voucher->id,
    'discount_percent' => $voucher->discount_percent,
    'max_discount' => $voucher->max_discount,
    'min_order_amount' => $voucher->min_order_amount,
]);
}


public function save($id)
{
    $user = Auth::user();

    $voucher = Voucher::find($id);
    if (!$voucher) {
        return response()->json(['success' => false, 'message' => 'Mã không tồn tại']);
    }

    $exists = UserVoucher::where('user_id', $user->id)->where('voucher_id', $id)->exists();
    if ($exists) {
        return response()->json(['success' => false, 'message' => 'Bạn đã lưu mã này rồi']);
    }

    UserVoucher::create([
        'user_id' => $user->id,
        'voucher_id' => $id,
    ]);

    return response()->json(['success' => true, 'message' => 'Lưu mã thành công']);
}
}

