<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    // public function listUser(){
    //     return view('admin.users.list-user');
    // }
    public function listUser(Request $request)
{
    $q = User::query();

    // Tên người dùng
    if ($request->filled('name')) {
        $q->where('name', 'like', '%'.$request->name.'%');
    }

    // Email
    if ($request->filled('email')) {
        $q->where('email', 'like', '%'.$request->email.'%');
    }

    // Số điện thoại (tìm ở cột 'phone' hoặc 'phone_number' nếu bạn dùng tên đó)
    if ($request->filled('phone')) {
        $raw = $request->phone;
        $digits = preg_replace('/\D/', '', $raw); // loại ký tự không phải số
        $q->where(function ($w) use ($raw, $digits) {
            $w->where('phone', 'like', '%'.$raw.'%')
              ->orWhere('phone', 'like', '%'.$digits.'%')
              ->orWhere('phone_number', 'like', '%'.$raw.'%')
              ->orWhere('phone_number', 'like', '%'.$digits.'%');
        });
    }

    $users = $q->latest()
        ->paginate(20)
        ->appends($request->query()); // giữ tham số khi phân trang

    return view('admin.users.list-user', compact('users'));
}

public function banUser(Request $request){
        $user_id = $request->id_user;

        $user = User::findOrFail($user_id);
        $user->is_ban = true;
        $user->save();
        return back()->with('success','Tài khoản đã bị cấm');
        
}
public function unbanUser(Request $request)
{
    $user = User::findOrFail($request->id_user);

    $user->is_ban = false;
    $user->save();

    return back()->with('success', 'Tài khoản đã được mở cấm');
}
public function UpdateRole(Request $request){
    $role = $request->role;
    $user_id = $request->id_user;

    $user = User::findOrFail($user_id);
    if ($role== 1) {
        $user->role = 'staff';
        $user->save();
        return back()->with('success','User đã lên nhân viên');
    }elseif($role== 2){
        $user->role = 'customer';
        $user->save();
        return back()->with('success','User đã là khách hàng');
    }
   
}



}