@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Chi tiết liên hệ</h2>

    <div class="mb-3">
        <p><strong>Tên:</strong> {{ $contact->name }}</p>
        <p><strong>Email:</strong> {{ $contact->email }}</p>
        <p><strong>Nội dung:</strong> {{ $contact->message }}</p>
        <p><strong>Trạng thái:</strong>
            @if ($contact->is_read)
                <span class="text-success">Đã đọc</span>
            @else
                <span class="text-danger">Chưa đọc</span>
            @endif
        </p>
        <p><strong>Ngày gửi:</strong> {{ $contact->created_at->format('H:i d/m/Y') }}</p>
    </div>

    <hr>

    <h4>Ghi chú nội bộ</h4>
    <form action="{{ route('admin.contacts.updateNote', $contact->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <textarea name="note" class="form-control" rows="4" placeholder="Nhập ghi chú...">{{ old('note', $contact->note) }}</textarea>
            @error('note')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Lưu ghi chú</button>
    </form>

    <h5>Phản hồi nhanh</h5>
<form action="{{ route('admin.contacts.reply', $contact->id) }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="reply_message" class="form-label">Nội dung phản hồi</label>
        <textarea name="reply_message" class="form-control" rows="4" required>{{ old('reply_message') }}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Gửi email phản hồi</button>
</form>


    <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary mt-3">← Quay lại danh sách</a>
</div>
@endsection
