@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Danh sách liên hệ</h1>

    {{-- Form tìm kiếm --}}
<form action="{{ route('admin.contacts.index') }}" method="GET" class="mb-3 d-flex" style="gap: 8px;">
    <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên hoặc email" value="{{ request('keyword') }}">

    <select name="is_read" class="form-select" style="max-width: 150px;">
        <option value="">-- Tất cả --</option>
        <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Đã đọc</option>
        <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Chưa đọc</option>
    </select>

    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
</form>


    {{-- Bảng danh sách liên hệ --}}
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Tên</th>
                <th>Email</th>
                <th>Ngày gửi</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contacts as $contact)
                <tr>
                    <td>{{ $contact->name }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->created_at->format('H:i d/m/Y') }}</td>
                    <td>
                        @if ($contact->is_read)
                            <span class="badge bg-success">Đã đọc</span>
                        @else
                            <span class="badge bg-secondary">Chưa đọc</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-sm btn-info">Xem</a>

                        <form action="{{ route('admin.contacts.destroy', $contact->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Bạn có chắc muốn xoá liên hệ này?')" class="btn btn-sm btn-danger">Xoá</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Không có liên hệ nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Phân trang --}}
    <div>
        {{ $contacts->withQueryString()->links() }}
    </div>

    {{-- Link xem liên hệ đã xoá --}}
    <a href="{{ route('admin.contacts.trashed') }}" class="btn btn-link mt-3">Xem liên hệ đã xoá</a>
</div>
@endsection
