<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatisticsController extends Controller
{
    public function index()
    {
        // Đếm số đơn theo trạng thái
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        // Tổng doanh thu các đơn đã thanh toán (VD: chỉ tính confirmed/completed)
        $totalRevenue = Order::whereIn('status', ['confirmed', 'completed'])->sum('total');

        // Tổng số đơn đã thanh toán
        $paidOrdersCount = Order::whereIn('status', ['confirmed', 'completed'])->count();

        // Tổng số đơn
        $totalOrders = Order::count();

        // Thống kê theo ngày (7 ngày gần nhất)
        $ordersByDay = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue')
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
