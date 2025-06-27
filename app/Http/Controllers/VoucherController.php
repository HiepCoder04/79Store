<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string'
        ]);

        $user = auth()->user();
        $code = $request->voucher_code;

        $voucher = Voucher::where('code', $code)->first();

        // 1. Không tồn tại
        if (!$voucher) {
            return back()->with('error', 'Mã giảm giá không tồn tại.');
        }

        // 2. Kiểm tra thời hạn
        $now = now();
        if ($now->lt($voucher->start_date) || $now->gt($voucher->end_date)) {
            return back()->with('error', 'Mã giảm giá đã hết hạn.');
        }

        // 3. Không hoạt động
        if (!$voucher->is_active) {
            return back()->with('error', 'Mã giảm giá không hợp lệ.');
        }

        // 4. Kiểm tra đã dùng chưa
        $used = DB::table('user_vouchers')
            ->where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->where('is_used', true)
            ->exists();

        if ($used) {
            return back()->with('error', 'Bạn đã sử dụng mã này rồi.');
        }

        //Lưu voucher ID vào session để dùng cho bước thanh toán
        session()->put('applied_voucher', $voucher->id);

        return back()->with('success', 'Áp dụng mã giảm giá thành công!');
    }
}

