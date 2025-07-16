<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Ban
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
          if (Auth::check() && Auth::user()->is_ban == 1) {
            Auth::logout(); // Đăng xuất
            return redirect()->route('auth.login')->with('error', 'Tài khoản của bạn đã bị khóa.');
        }
        return $next($request);
    }
}