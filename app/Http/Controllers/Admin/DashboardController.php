<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $statusLabels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'delivered' => 'Đã giao',
            'cancelled' => 'Đã hủy',
            'returned' => 'Đã trả hàng',
        ];

        // ======== Lọc ngày cho Orders ========
        $ordersQ = Order::query();
        if ($request->filled('start_date')) {
            $ordersQ->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $ordersQ->whereDate('created_at', '<=', $request->end_date);
        }

        // ======== Đếm theo trạng thái ========
        $donHangChoXuLy = (clone $ordersQ)->where('status', 'pending')->count();
        $donHangDaXuLy = (clone $ordersQ)->where('status', 'confirmed')->count();
        $donHangDangGiao = (clone $ordersQ)->where('status', 'shipping')->count();
        $donHangDaGiao = (clone $ordersQ)->where('status', 'delivered')->count();
        $donHangDaHuy = (clone $ordersQ)->where('status', 'cancelled')->count();

        // Đã trả: lấy từ return_requests
        $returnReqQ = DB::table('return_requests');
        if ($request->filled('start_date')) {
            $returnReqQ->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $returnReqQ->whereDate('created_at', '<=', $request->end_date);
        }
        // Nếu có cột trạng thái cho request, thêm điều kiện tại đây (ví dụ ->where('status','approved'))
        $donHangDaTra = $returnReqQ->count();

        // ======== Doanh thu gộp (delivered) ========
        $doanhThu = (clone $ordersQ)
            ->where('status', 'delivered')
            ->sum('total_after_discount');

        // ======== Tiền hoàn (return_transactions.amount) ========
        $refundQ = DB::table('return_transactions')->where('type', 'refund');
        if ($request->filled('start_date')) {
            $refundQ->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $refundQ->whereDate('created_at', '<=', $request->end_date);
        }
        $doanhThuBiTru = $refundQ->sum('amount');

        // ======== Doanh thu thực tế ========
        $doanhThuThucTe = ($doanhThu ?? 0) - ($doanhThuBiTru ?? 0);

        // ======== Doanh thu theo ngày (đã trừ hoàn) ========
        $deliveredByDate = (clone $ordersQ)
            ->where('status', 'delivered')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_after_discount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $refundsByDateQ = DB::table('return_transactions')
            ->where('type', 'refund');
        if ($request->filled('start_date')) {
            $refundsByDateQ->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $refundsByDateQ->whereDate('created_at', '<=', $request->end_date);
        }
        $refundsByDate = $refundsByDateQ
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->pluck('total', 'date');

        $doanhThus = $deliveredByDate->map(function ($row) use ($refundsByDate) {
            $refund = $refundsByDate[$row->date] ?? 0;
            $row->total = ($row->total ?? 0) - $refund;
            return $row;
        });

        // ======== Số đơn theo ngày ========
        $soDonHangTheoNgay = (clone $ordersQ)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ======== Doanh thu 7 ngày gần nhất (đã trừ hoàn) ========
        $from = Carbon::today()->subDays(6)->toDateString();

        $weeklyDelivered = Order::where('status', 'delivered')
            ->whereDate('created_at', '>=', $from)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_after_discount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $weeklyRefunds = DB::table('return_transactions')
            ->where('type', 'refund')
            ->whereDate('created_at', '>=', $from)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->pluck('total', 'date');

        $labels = [];
        $totals = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i)->toDateString();
            $labels[] = $d;
            $del = optional($weeklyDelivered->firstWhere('date', $d))->total ?? 0;
            $ref = $weeklyRefunds[$d] ?? 0;
            $totals[] = $del - $ref;
        }
        $weeklyRevenueData = ['labels' => $labels, 'totals' => $totals];

        // ======== Top 5 sản phẩm bán chạy (đơn delivered) ========
        $topProducts = DB::table('order_details')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('orders.created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('orders.created_at', '<=', $request->end_date))
            ->select('products.name', DB::raw('SUM(order_details.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $topProductsData = [
            'labels' => $topProducts->pluck('name'),
            'totals' => $topProducts->pluck('total_sold'),
        ];

        return view('admin.dashboard', compact(
            'statusLabels',
            'donHangChoXuLy',
            'donHangDaXuLy',
            'donHangDangGiao',
            'donHangDaGiao',
            'donHangDaHuy',
            'donHangDaTra',
            'doanhThu',          // Doanh thu gộp (delivered)
            'doanhThuBiTru',     // Tổng tiền hoàn từ return_transactions
            'doanhThuThucTe',    // Doanh thu thực tế = doanhThu - doanhThuBiTru
            'doanhThus',
            'soDonHangTheoNgay',
            'weeklyRevenueData',
            'topProductsData'
        ));
    }
}
