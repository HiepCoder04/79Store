<?php

// app/Http/Controllers/ForgotPasswordOtpController.php
namespace App\Http\Controllers;

use App\Mail\PasswordOtpMail;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ForgotPasswordOtpController extends Controller
{
    public function showEmailForm()
    {
        return view('auth.otp-request');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $otp = rand(100000, 999999);

        PasswordOtp::where('email', $request->email)->delete(); // Xoá mã cũ

        PasswordOtp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($request->email)->send(new PasswordOtpMail($otp));

        return redirect()->route('otp.verify.form')->with('email', $request->email);
    }

    public function showVerifyForm()
    {
        return view('auth.otp-verify');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
        ]);

        $otpRecord = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Mã OTP không đúng hoặc đã hết hạn.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        PasswordOtp::where('email', $request->email)->delete();

        return redirect()->route('auth.login')->with('success', 'Đặt lại mật khẩu thành công');
    }
}
