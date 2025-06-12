@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <h2>Thêm banner</h2>

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

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Ảnh banner</label>
            <input type="file" name="image" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-3">
            <label>Liên kết</label>
            <input type="text" name="link" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500" value="{{ old('link') }}">
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea name="description" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Trạng thái</label>
            <select name="is_active" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
                <option value="1">Hiển thị</option>
                <option value="0">Ẩn</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Lưu</button>
    </form>
    <a href="{{route('admin.banners.index')}}" class="btn btn-primary">quay lại</a>
</div>
@endsection
