<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Tạm thời không có logic vì đã xóa bảng 'orders'
    public function index()
    {
        return view('admin.orders.index', ['orders' => []]); // Trả về mảng rỗng
    }

    public function show($id)
    {
        abort(404, 'Không tìm thấy đơn hàng');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.orders.index')->with('error', 'Không thể xóa vì bảng orders đã bị xóa');
    }

    public function restore($id)
    {
        return redirect()->route('admin.orders.index')->with('error', 'Không thể khôi phục vì bảng orders đã bị xóa');
    }

    public function forceDelete($id)
    {
        return redirect()->route('admin.orders.index')->with('error', 'Không thể xóa vĩnh viễn vì bảng orders đã bị xóa');
    }
}
