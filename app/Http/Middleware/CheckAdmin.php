<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Chưa đăng nhập → redirect về login
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', 'Bạn cần đăng nhập.');
        }

        // Không phải admin → redirect về dashboard
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập.');
        }

        // Là admin → cho phép truy cập
        return $next($request);
    }
}