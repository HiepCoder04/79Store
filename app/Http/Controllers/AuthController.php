<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    // GET: /login
    public function login()
    {
        return view('auth.login');
    }

    // POST: /login
    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->remember)) {
            $user = Auth::user();

            if ($user->is_ban == false) {
                if ($user->role === 'admin' || $user->role === 'staff') {
                    return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!');
                } elseif ($user->role === 'customer' || $user->role === 'guest') {
                    return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
                }
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khóa.']);
            }
        }

        return back()->withErrors(['email' => 'Sai tài khoản hoặc mật khẩu.'])->withInput();
    }

    // GET: /register
    public function register()
    {
        return view('auth.register');
    }

    // POST: /register
    public function registerPost(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Họ và tên không được để trống.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'email.required' => 'Email không được để trống.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'is_ban' => false
        ]);

        return redirect()->route('auth.login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
    }

    // GET: /logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('auth.login')->with('success', 'Đăng xuất thành công!');
    }
}
