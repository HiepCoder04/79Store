<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class AdminStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');

        // Query cơ bản
        $query = Order::query();

        // Nếu có lọc theo ngày
        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $orders = $query->get();

        // Tính tổng số đơn và doanh thu
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total');

        return view('admin.statistics.index', compact('orders', 'totalOrders', 'totalRevenue', 'start', 'end'));
    }
}

