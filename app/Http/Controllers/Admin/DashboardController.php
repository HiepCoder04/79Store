<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        // Query đơn hàng
        $query = Order::query();

        // Nếu lọc theo khoảng ngày
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $orders = $query->get();

        // Tính tổng số đơn và tổng doanh thu
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total');

        // Trả dữ liệu ra giao diện dashboard
        return view('admin.dashboard', compact(
            'orders', 'totalOrders', 'totalRevenue', 'start', 'end'
        ));
    }
}
