@extends('admin.layouts.dashboard')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header bg-gradient bg-primary text-white rounded-top-4">
                    <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Thêm Banner Mới</h4>
                </div>
                <div class="card-body p-4">

                    {{-- Thông báo thành công --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                        </div>
                    @endif

                    {{-- Thông báo lỗi --}}
                    @if (session('errors'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> Có lỗi xảy ra:
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach (session('errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        {{-- Ảnh banner --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">📷 Ảnh banner</label>
                            <input type="file" name="image" class="form-control" required>
                        </div>

                        {{-- Liên kết --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">🔗 Liên kết</label>
                            <input type="text" name="link" value="{{ old('link') }}" class="form-control" placeholder="https://..." required>
                        </div>

                        {{-- Mô tả --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">📝 Mô tả</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Nhập mô tả...">{{ old('description') }}</textarea>
                        </div>

                        {{-- Trạng thái --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">👁️ Trạng thái hiển thị</label>
                            <select name="is_active" class="form-select">
                                <option value="1" selected>Hiển thị</option>
                                <option value="0">Ẩn</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-save-fill me-1"></i> Lưu banner
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
