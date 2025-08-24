<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function listUser(Request $request)
    {
        $query = User::query()

        ->where('role', 'customer')
            ->search($request, ['name', 'email', 'phone']) // tìm kiếm

            

            ->filter($request, [
                'role'   => 'exact',
                'is_ban' => 'exact',
            ]);

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
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'reason'  => 'required|string|max:255',
        ]);

        $user = User::findOrFail($request->id_user);
        $user->is_ban = true;
        $user->ban_reason = $request->reason;
        $user->save();

        return back()->with('success', 'Đã cấm tài khoản: ' . $user->name . ' - Lý do: ' . $request->reason);
    }

    public function unbanUser(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->id_user);
        $user->is_ban = false;
        $user->ban_reason = null;
        $user->save();

        return back()->with('success', 'Đã mở cấm tài khoản: ' . $user->name);
    }

    public function UpdateRole(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'role'    => 'required|in:1,2',
        ]);

        $user = User::findOrFail($request->id_user);
        if ($request->role == 1) {
            $user->role = 'staff';
            $msg = 'User đã lên nhân viên';
        } else {
            $user->role = 'customer';
            $msg = 'User đã là khách hàng';
        }
        $user->save();

        return back()->with('success', $msg);
    }
}