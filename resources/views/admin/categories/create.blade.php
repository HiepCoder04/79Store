@extends('admin.layouts.dashboard')

@section('title', 'Thêm danh mục | 79Store')

@section('content')
<div class="card shadow-sm mt-4 border-0" style="background-color: #f9fafb;">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #1e293b;">
        <h5 class="mb-0 text-white">🗂️ Thêm danh mục mới</h5>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm">← Quay lại danh sách</a>
    </div>

    <div class="card-body">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold text-dark">Tên danh mục <span class="text-danger">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="form-control shadow-sm border border-gray-300 @error('name') is-invalid @enderror" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-dark">Danh mục cha</label>
                <select name="parent_id"
                    class="form-select shadow-sm border border-gray-300 @error('parent_id') is-invalid @enderror">
                    <option value="">-- Không chọn --</option>
                    @foreach ($parents as $p)
                    <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                    @endforeach
                </select>
                @error('parent_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn" style="background-color: #2563eb; color: white;">
                    <i class="bi bi-check-circle"></i> Lưu
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection