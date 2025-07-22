<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('client.account.index', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('client.account.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'date_of_birth' => 'nullable|date',
        ]);

        $user = Auth::user();

        // Cập nhật thông tin cơ bản
        $user->name = $request->input('name');
        $user->phone = $request->input('phone');
        $user->date_of_birth = $request->input('date_of_birth');

        // Xử lý avatar
        if ($request->hasFile('avatar')) {
            // Xóa ảnh cũ nếu tồn tại
            if ($user->avatar && file_exists(public_path('img/avatars/' . $user->avatar))) {
                @unlink(public_path('img/avatars/' . $user->avatar));
            }

            $file = $request->file('avatar');
            $filename = uniqid('avatar_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/avatars'), $filename);

            $user->avatar = $filename;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'avatar_url' => $user->avatar
                ? asset('img/avatars/' . $user->avatar) . '?t=' . time()
                : null,
            'message' => 'Thông tin đã được cập nhật.',
        ]);
    }
}
