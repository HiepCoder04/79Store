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
}
