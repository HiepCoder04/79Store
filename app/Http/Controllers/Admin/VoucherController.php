<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->get();
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:vouchers,code',
            'title' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'discount_percent' => 'required|numeric|min:1|max:100',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
        ]);

        Voucher::create($request->all());
        return redirect()->route('admin.vouchers.index')->with('success', 'Thêm voucher thành công!');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'code' => 'required|unique:vouchers,code,' . $voucher->id,
            'title' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'discount_percent' => 'required|numeric|min:1|max:100',
            'max_discount' => 'nullable|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
        ]);

        $voucher->update($request->all());
        return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Đã xoá voucher.');
    }

    public function users(Voucher $voucher)
    {
        $users = DB::table('user_vouchers')
            ->join('users', 'users.id', '=', 'user_vouchers.user_id')
            ->select('users.name', 'users.email', 'user_vouchers.used_at')
            ->where('voucher_id', $voucher->id)
            ->where('is_used', true)
            ->get();

        return view('admin.vouchers.user', compact('voucher', 'users'));
    }
}

