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
    public function listUser()
{
    $users = User::paginate(10); // hoặc ->latest()->paginate(10);
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


}