<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Voucher2Controller extends Controller
{
    public function getSuggestions()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $vouchers = Voucher::whereHas('userVouchers', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
                ->where('is_active', 1)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->inRandomOrder()
                ->limit(5)
                ->get(['code', 'end_date', 'discount_percent', 'max_discount', 'min_order_amount']);

            return response()->json($vouchers);
        } catch (\Throwable $e) {
            \Log::error('Lỗi khi lấy voucher gợi ý: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
