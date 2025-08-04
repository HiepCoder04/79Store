<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class AdminStatisticsController extends Controller
{
    public function dashboard(Request $request)
{
    $query = Order::query();

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    $totalOrders = $query->count();
    $totalRevenue = $query->sum('total_after_discount');

    $orders = $query->latest()->paginate(10);

    return view('admin.statistics.dashboard', compact('totalOrders', 'totalRevenue', 'orders'));
}

}
