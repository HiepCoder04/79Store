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
 
$soDonHangTheoNgay =DB::table('orders')
    ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
    ->where('status', 'delivered') // có thể bỏ nếu muốn tính mọi trạng thái
    ->groupByRaw('DATE(created_at)')
    ->orderBy('date', 'asc')
    ->get();

   $doanhThus = DB::table('order_details')
    ->join('orders', 'order_details.order_id', '=', 'orders.id')
    ->where('orders.status', 'delivered') // hoặc điều kiện khác
    ->selectRaw('DATE(orders.created_at) as date, SUM(order_details.price * order_details.quantity) as total')
    ->groupByRaw('DATE(orders.created_at)')
    ->orderBy('date', 'asc')
    ->get();


        $doanhThu = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.status', 'delivered')
            ->select(DB::raw('SUM(order_details.price * order_details.quantity) as total'))
            ->value('total');   
        $donHangChoXuLy =Order::where('status', 'pending')->count();
        $donHangDaGiao = Order::where('status', 'delivered')->count();
        $donHangDaHuy = Order::where('status', 'cancelled')->count();

        return view('admin.dashboard', compact('doanhThus','soDonHangTheoNgay','doanhThu', 'donHangChoXuLy', 'donHangDaGiao', 'donHangDaHuy'));
    }
}