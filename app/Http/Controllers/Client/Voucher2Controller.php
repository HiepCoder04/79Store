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
    try {
        $vouchers = Voucher::where('is_active', 1)
            ->where('start_date', '<=', \Carbon\Carbon::now())
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
