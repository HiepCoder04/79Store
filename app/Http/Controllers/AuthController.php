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
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->is_ban == false) {
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'staff') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'customer' || $user->role === 'guest') {
                    return redirect()->route('home')->with('success', 'Đăng nhập thành công');
                } else {
                    Auth::logout(); // tránh truy cập lạ
                    return redirect()->route('auth.login')->withErrors([
                        'role' => 'Vai trò không hợp lệ.',
                    ]);
                }
            } else {
                return redirect()->route('auth.login')->withErrors([
                    'email' => 'Tài khoản của bạn đã bị cấm. Vui lòng liên hệ quản trị viên.'
                ])->withInput();
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
    public function registerPost(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
            'date_of_birth' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'date_of_birth' => $request->date_of_birth,
        ]);

        Auth::login($user);
        return redirect('auth/login')->with('success', 'Đăng kí thành công');
    }

    // POST: /logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('success', 'Đăng xuất thành công');
    }


    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'password' => bcrypt('google-login'), // placeholder
                    'email_verified_at' => now(),
                ]
            );

            Auth::login($user);

            return redirect()->route('home');
        } catch (\Exception $e) {
            return redirect()->route('auth.login')->with('error', 'Đăng nhập Google thất bại!');
        }
    }
}
