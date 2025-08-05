@extends('admin.layouts.dashboard')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">🖼️ Chỉnh sửa Banner</h4>
        </div>

        <div class="card-body">

            {{-- Thông báo thành công --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Thông báo lỗi --}}
            @if (session('errors'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach (session('errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Ảnh hiện tại --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ảnh hiện tại:</label><br>
                    <img src="{{ asset($banner->image) }}" style="height: 100px;" class="rounded border shadow-sm">
                </div>

                {{-- Upload ảnh mới --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Chọn ảnh mới (bỏ qua nếu không đổi):</label>
                    <input type="file" name="image" class="form-control">
                </div>

                {{-- Liên kết --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Liên kết:</label>
                    <input type="text" name="link" value="{{ $banner->link }}" class="form-control">
                </div>

                {{-- Mô tả --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả:</label>
                    <textarea name="description" class="form-control" rows="3">{{ $banner->description }}</textarea>
                </div>

                {{-- Trạng thái --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Trạng thái:</label>
                    <select name="is_active" class="form-select">
                        <option value="1" {{ $banner->is_active ? 'selected' : '' }}>Hiển thị</option>
                        <option value="0" {{ !$banner->is_active ? 'selected' : '' }}>Ẩn</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
