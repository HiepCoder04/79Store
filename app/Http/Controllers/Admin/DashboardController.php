<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use DB;
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Map trạng thái sang tiếng Việt
        $statusLabels = [
            'pending'   => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipping'  => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            'returned'  => 'Đã trả hàng'
        ];

        // --- Lọc dữ liệu ---
        $query = Order::query();

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        // --- Số liệu tổng quan ---
        $totalOrders = $query->count();
        $totalRevenue = $query->sum('total_after_discount');

        $doanhThu = Order::where('status', 'delivered')->sum('total_after_discount');
        $donHangChoXuLy = Order::where('status', 'pending')->count();
        $donHangDaGiao = Order::where('status', 'delivered')->count();
        $donHangDaHuy = Order::where('status', 'cancelled')->count();

        // --- Dữ liệu biểu đồ ---
        $doanhThus = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_after_discount) as total')
            )
            ->where('status', 'delivered')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $soDonHangTheoNgay = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // --- Danh sách đơn hàng ---
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        // Thay trạng thái tiếng Việt trong danh sách đơn hàng
        foreach ($orders as $order) {
            $order->status_vi = $statusLabels[$order->status] ?? $order->status;
        }

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'doanhThu',
            'donHangChoXuLy',
            'donHangDaGiao',
            'donHangDaHuy',
            'doanhThus',
            'soDonHangTheoNgay',
            'orders',
            'statusLabels'
        ));
    }
}