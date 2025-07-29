@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-3">Liên hệ đã xoá</h2>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Tên</th>
                <th>Email</th>
                <th>Thời gian xoá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($contacts as $contact)
                <tr>
                    <td>{{ $contact->name }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->deleted_at->format('H:i d/m/Y') }}</td>
                    <td>
                        <form action="{{ route('admin.contacts.restore', $contact->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm btn-success" onclick="return confirm('Khôi phục liên hệ này?')" type="submit">Khôi phục</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Không có liên hệ nào đã xoá.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Phân trang --}}
    <div>
        {{ $contacts->links() }}
    </div>

    <a href="{{ route('admin.contacts.index') }}" class="btn btn-link mt-3">← Quay lại danh sách chính</a>
</div>
@endsection
