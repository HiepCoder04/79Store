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
        $vouchers = Voucher::latest()->paginate(10);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
{
    $data = $request->all();
 

    // Validate 
    $errors = [];

    if (empty($data['code'])) {
        $errors['code'] = 'Mã không được để trống.';
    } elseif (Voucher::where('code', $data['code'])->exists()) {
        $errors['code'] = 'Mã đã tồn tại.';
    }

    if (!empty($data['description']) && strlen($data['description']) > 1000) {
        $errors['description'] = 'Mô tả quá dài.';
    }

    if ( empty($data['event_type'] )){
        $errors['event_type'] = 'Loại sự kiện ko đc trống.';
    }
    

    if (empty($data['start_date'])) {
        $errors['start_date'] = 'Vui lòng nhập ngày bắt đầu.';
    } elseif ($data['start_date'] < date('Y-m-d')) {
        $errors['start_date'] = 'Ngày bắt đầu phải lớn hơn hoặc bằng hôm nay.';
    }

    if (empty($data['end_date'])) {
        $errors['end_date'] = 'Vui lòng nhập ngày kết thúc.';
    } elseif (!empty($data['start_date']) && $data['end_date'] < $data['start_date']) {
        $errors['end_date'] = 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.';
    }

    if (empty($data['discount_percent']) || !is_numeric($data['discount_percent'])) {
        $errors['discount_percent'] = 'Phần trăm giảm giá không hợp lệ.';
    }

    if (!empty($data['max_discount']) && (!is_numeric($data['max_discount']) || $data['max_discount'] < 0)) {
        $errors['max_discount'] = 'Giảm tối đa phải là số dương.';
    }

    if (!empty($data['min_order_amount']) && (!is_numeric($data['min_order_amount']) || $data['min_order_amount'] < 0)) {
        $errors['min_order_amount'] = 'Đơn tối thiểu phải là số dương.';
    }

    if (!isset($data['is_active']) || !in_array($data['is_active'], [0, 1, '0', '1'])) {
        $errors['is_active'] = 'Trạng thái không hợp lệ.';
    }

    
    if (!empty($errors)) {
        return back()->withErrors($errors)->withInput();
    }

    // Tạo mới
    Voucher::create([
        'code' => $data['code'],
        'description' => $data['description'] ?? null,
        'event_type' => $data['event_type'] ?? '',
        'discount_percent' => $data['discount_percent'],
        'start_date' => $data['start_date'],
        'end_date' => $data['end_date'],
        'max_discount' => $data['max_discount'] ?? null,
        'min_order_amount' => $data['min_order_amount'] ?? null,
        'is_active' => $data['is_active'] ?? 1,
    ]);

    return redirect()->route('admin.vouchers.index')->with('success', 'Thêm voucher thành công!');
}

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

public function update(Request $request, $id)
{
    $voucher = Voucher::findOrFail($id);
    $data = $request->all();
    $errors = [];

    // Validate
    if (empty($data['code'])) {
        $errors['code'] = 'Mã không được để trống.';
    } elseif (Voucher::where('code', $data['code'])->where('id', '!=', $id)->exists()) {
        $errors['code'] = 'Mã đã tồn tại.';
    }

    if (empty($data['description']) && strlen($data['description']) > 1000) {
        $errors['description'] = 'Mô tả quá dài.';
    }

    if (empty($data['event_type'])) {
        $errors['event_type'] = 'Loại sự kiện không được để trống.';
    }

    if (empty($data['start_date'])) {
        $errors['start_date'] = 'Vui lòng nhập ngày bắt đầu.';
    } elseif ($data['start_date'] < date('Y-m-d')) {
        $errors['start_date'] = 'Ngày bắt đầu phải lớn hơn hoặc bằng hôm nay.';
    }

    if (empty($data['end_date'])) {
        $errors['end_date'] = 'Vui lòng nhập ngày kết thúc.';
    } elseif (!empty($data['start_date']) && $data['end_date'] < $data['start_date']) {
        $errors['end_date'] = 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.';
    }

    if (empty($data['discount_percent']) || !is_numeric($data['discount_percent'])) {
        $errors['discount_percent'] = 'Phần trăm giảm giá không hợp lệ.';
    }

    if (!empty($data['max_discount']) && (!is_numeric($data['max_discount']) || $data['max_discount'] < 0)) {
        $errors['max_discount'] = 'Giảm tối đa phải là số dương.';
    }

    if (!empty($data['min_order_amount']) && (!is_numeric($data['min_order_amount']) || $data['min_order_amount'] < 0)) {
        $errors['min_order_amount'] = 'Đơn tối thiểu phải là số dương.';
    }

    if (!isset($data['is_active']) || !in_array($data['is_active'], [0, 1, '0', '1'])) {
        $errors['is_active'] = 'Trạng thái không hợp lệ.';
    }

    if (!empty($errors)) {
        return back()->withErrors($errors)->withInput();
    }

    // Cập nhật
    $voucher->update([
        'code' => $data['code'],
        'description' => $data['description'] ?? null,
        'event_type' => $data['event_type'] ?? null,
        'discount_percent' => $data['discount_percent'],
        'start_date' => $data['start_date'],
        'end_date' => $data['end_date'],
        'max_discount' => $data['max_discount'] ?? null,
        'min_order_amount' => $data['min_order_amount'] ?? null,
        'is_active' => $data['is_active'] ?? 1,
    ]);

    return redirect()->route('admin.vouchers.index')->with('success', 'Cập nhật voucher thành công!');
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

