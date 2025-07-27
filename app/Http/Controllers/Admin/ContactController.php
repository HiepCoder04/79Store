<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class ContactController extends Controller
{
    // Danh sách liên hệ
    public function index(Request $request)
    {
        $query = Contact::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                  ->orWhere('email', 'like', "%$keyword%");
            });
        }

        // Lọc theo trạng thái đã đọc / chưa đọc
        if ($request->filled('is_read')) {
            $query->where('is_read', $request->is_read);
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.contacts.index', compact('contacts'));
    }

    // Chi tiết liên hệ + đánh dấu đã đọc
    public function show($id)
    {
        $contact = Contact::findOrFail($id);

        // Tự động đánh dấu đã đọc nếu chưa
        if (!$contact->is_read) {
            $contact->is_read = true;
            $contact->save();
        }

        return view('admin.contacts.show', compact('contact'));
    }

    // Cập nhật ghi chú nội bộ
    public function updateNote(Request $request, $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:1000',
        ]);

        $contact = Contact::findOrFail($id);
        $contact->note = $request->note;
        $contact->save();

        return redirect()->back()->with('success', 'Ghi chú đã được cập nhật.');
    }

    // Đánh dấu đã đọc (nếu muốn làm thủ công từ danh sách)
    public function markAsRead($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->is_read = true;
        $contact->save();

        return redirect()->back()->with('success', 'Liên hệ đã được đánh dấu là đã đọc.');
    }

    // Xoá liên hệ (soft delete)
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return redirect()->back()->with('success', 'Đã xoá liên hệ thành công.');
    }
    public function trashed()
{
    $contacts = Contact::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);
    return view('admin.contacts.trashed', compact('contacts'));
}

public function restore($id)
{
    $contact = Contact::withTrashed()->findOrFail($id);
    $contact->restore();

    return redirect()->route('admin.contacts.trashed')->with('success', 'Đã khôi phục liên hệ.');
}

public function sendReply(Request $request, $id)
{
    $request->validate([
        'reply_message' => 'required|string|max:2000',
    ]);

    $contact = Contact::findOrFail($id);

    $email = $contact->email;
    $subject = 'Phản hồi liên hệ từ website';
    $message = $request->input('reply_message');

    // Gửi email
    Mail::raw($message, function ($mail) use ($email, $subject) {
        $mail->to($email)
            ->subject($subject);
    });

    return redirect()->back()->with('success', 'Đã gửi email phản hồi thành công!');
}


}
