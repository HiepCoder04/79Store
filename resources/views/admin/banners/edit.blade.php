@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <h2>Chỉnh sửa banner</h2>

    {{-- Thông báo thành công --}}
    @if (session('success'))
        <div class="alert alert-success text-white bg-green-500 p-3 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- Thông báo lỗi --}}
    @if (session('errors'))
        <div class="alert alert-danger text-white bg-red-500 p-3 rounded mb-3">
            <ul class="mb-0">
                @foreach (session('errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Ảnh hiện tại --}}
        <div class="mb-3">
            <label>Ảnh hiện tại</label><br>
            <img src="{{ asset($banner->image) }}" style="height: 100px;">
        </div>

        {{-- Upload ảnh mới --}}
        <div class="mb-3">
            <label>Chọn ảnh mới (bỏ qua nếu không đổi)</label>
            <input type="file" name="image" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-3">
            <label>Liên kết</label>
            <input type="text" name="link" value="{{ $banner->link }}" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">{{ $banner->description }}</textarea>
        </div>

        <div class="mb-3">
            <label>Trạng thái</label>
            <select name="is_active" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
                <option value="1" {{ $banner->is_active ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ !$banner->is_active ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
    </form>
        <a href="{{route('admin.banners.index')}}" class="btn btn-primary">quay lại</a>
</div>
@endsection
