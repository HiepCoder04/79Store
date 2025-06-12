@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <h2>Danh sách banner</h2>
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary mb-3">Thêm banner</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Link</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($banners as $banner)
                <tr>
                    <td><img src="{{ asset($banner->image) }}" style="height: 80px" /></td>
                    <td>{{ $banner->link }}</td>
                    <td>{{ $banner->description }}</td>
                    <td>{{ $banner->is_active ? 'Hiển thị' : 'Ẩn' }}</td>
                    <td>
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa banner này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
