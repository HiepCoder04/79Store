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
        $query = User::query()
            ->search($request, ['name', 'email', 'phone']) // tìm kiếm
            ->filter($request, [
                'role'   => 'exact', // lọc theo quyền
                'is_ban' => 'exact', // lọc theo trạng thái cấm
            ]);


        // Lọc trạng thái xác minh email (do không nằm trong trait filter)
        if ($request->filled('verified')) {
            if ($request->verified == 1) {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->verified == 0) {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->latest()->paginate(10)->appends($request->query());

        return view('admin.users.list-user', compact('users'));
    }

    public function banUser(Request $request)
    {
        $user_id = $request->id_user;

        $user = User::findOrFail($user_id);
        $user->is_ban = true;
        $user->save();
        return back()->with('success', 'Tài khoản đã bị cấm');
    }
    public function unbanUser(Request $request)
    {
        $user = User::findOrFail($request->id_user);

        $user->is_ban = false;
        $user->save();

        return back()->with('success', 'Tài khoản đã được mở cấm');
    }
    public function UpdateRole(Request $request)
    {
        $role = $request->role;
        $user_id = $request->id_user;

        $user = User::findOrFail($user_id);
        if ($role == 1) {
            $user->role = 'staff';
            $user->save();
            return back()->with('success', 'User đã lên nhân viên');
        } elseif ($role == 2) {
            $user->role = 'customer';
            $user->save();
            return back()->with('success', 'User đã là khách hàng');
        }
    }
}
