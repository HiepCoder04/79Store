<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Map trạng thái sang tiếng Việt
        $statusLabels = [
            'pending'   => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping'  => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            'returned'  => 'Đã trả hàng',
        ];

        // --- Lọc dữ liệu theo ngày ---
        $query = Order::query();

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // --- Số liệu tổng quan ---
        $totalOrders      = (clone $query)->count();
        $totalRevenueRaw  = (clone $query)->sum('total_after_discount'); // (nếu bạn vẫn dùng ở đâu đó)
        $deliveredSum     = (clone $query)->where('status', 'delivered')->sum('total_after_discount');
        $returnedSum      = (clone $query)->where('status', 'returned')->sum('total_after_discount');

        // ✅ Doanh thu thực: ĐÃ GIAO - ĐÃ TRẢ
        $doanhThu = $deliveredSum - $returnedSum;

        $donHangChoXuLy = (clone $query)->where('status', 'pending')->count();
        $donHangDaGiao  = (clone $query)->where('status', 'delivered')->count();
        $donHangDaHuy   = (clone $query)->where('status', 'cancelled')->count();
        $donHangDaTra   = (clone $query)->where('status', 'returned')->count();

        // --- Biểu đồ Doanh thu theo ngày (NET: delivered - returned) ---
        $doanhThus = (clone $query)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw("
                    SUM(CASE WHEN status = 'delivered' THEN total_after_discount ELSE 0 END)
                    - SUM(CASE WHEN status = 'returned'  THEN total_after_discount ELSE 0 END)
                    AS total
                ")
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // --- Biểu đồ Số đơn hàng theo ngày ---
        $soDonHangTheoNgay = (clone $query)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // --- Doanh thu 7 ngày gần nhất (NET) ---
        $weeklyBase = Order::query()
            ->where('created_at', '>=', Carbon::now()->subDays(6));
        if ($request->start_date) $weeklyBase->whereDate('created_at', '>=', $request->start_date);
        if ($request->end_date)   $weeklyBase->whereDate('created_at', '<=', $request->end_date);

        $weeklyRevenue = $weeklyBase
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw("
                    SUM(CASE WHEN status = 'delivered' THEN total_after_discount ELSE 0 END)
                    - SUM(CASE WHEN status = 'returned'  THEN total_after_discount ELSE 0 END)
                    AS total
                ")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $weeklyRevenueData = [
            'labels' => $weeklyRevenue->pluck('date'),
            'totals' => $weeklyRevenue->pluck('total')
        ];

        // --- Top 5 sản phẩm bán chạy ---
        $topProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $topProductsData = [
            'labels' => $topProducts->pluck('name'),
            'totals' => $topProducts->pluck('total_sold')
        ];

        // --- Danh sách đơn hàng ---
        $orders = (clone $query)->orderBy('created_at', 'desc')->paginate(10);
        foreach ($orders as $order) {
            $order->status_vi = $statusLabels[$order->status] ?? $order->status;
        }

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenueRaw', // nếu không dùng ở view, bạn có thể bỏ
            'doanhThu',        // ✅ dùng thẻ Doanh thu này
            'donHangChoXuLy',
            'donHangDaGiao',
            'donHangDaHuy',
            'donHangDaTra',
            'doanhThus',
            'soDonHangTheoNgay',
            'weeklyRevenueData',
            'topProductsData',
            'orders',
            'statusLabels'
        ));
    }
}
