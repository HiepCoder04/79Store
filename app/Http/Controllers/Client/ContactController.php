<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function showForm()
    {
        return view('client.contact');
    }

    public function submitForm(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email',
            'message' => 'required|string|max:1000',
        ]);

        Contact::create($validated); // chỉ lưu vào DB

        return redirect()->back()->with('success', 'Gửi liên hệ thành công!');
    }
}
