<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('thongke');
        } elseif ($user->role === 'staff') {
            return redirect()->intended('/staff/dashboard');
        } elseif ($user->role === 'customer' || $user->role === 'guest') {
            return redirect()->route('home')->with('success','Đăng nhập thành công');
        } else {
            Auth::logout(); // tránh truy cập lạ
            return redirect()->route('login')->withErrors([
                'role' => 'Vai trò không hợp lệ.',
            ]);
        }
    }

    return back()->withErrors([
        'email' => 'Sai tài khoản hoặc mật khẩu',
    ])->withInput();
}

    // GET: /register
    public function register()
    {
        return view('auth.register');
    }

    // POST: /register
    public function registerPost(Request $request) {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
            'date_of_birth' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'phone'=> $request->phone,
            'password'=> Hash::make($request->password),
            'role' => 'customer',
            'date_of_birth' => $request->date_of_birth,
        ]);

        Auth::login($user);
        return redirect('auth/login')->with('success','Đăng kí thành công');
    }

    // POST: /logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success','Đăng xuất thành công');
    }
}