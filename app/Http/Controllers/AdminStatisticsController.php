<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class AdminStatisticsController extends Controller
{
    public function Index()
{
    // Đếm số đơn theo trạng thái
    $statusCounts = Order::select('order_status', DB::raw('COUNT(*) as total'))
        ->groupBy('order_status')
        ->get();

    // Tổng doanh thu đã thanh toán
    $totalRevenue = Order::where('payment_status', 'paid')->sum('amount');

    // Tổng số đơn đã thanh toán
    $paidOrdersCount = Order::where('payment_status', 'paid')->count();

    // Tổng số đơn
    $totalOrders = Order::count();

    // Thống kê đơn hàng 7 ngày gần nhất
    $ordersByDay = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(amount) as total_amount')
        )
        ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date', 'asc')
        ->get();

    return view('admin.statistics', compact(
        'statusCounts',
        'totalRevenue',
        'paidOrdersCount',
        'totalOrders',
        'ordersByDay'
    ));
}
}
