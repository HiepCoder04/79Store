@extends('admin.layouts.dashboard')

@section('title', 'Thêm danh mục | 79Store')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-plus-circle me-2 text-primary"></i>Thêm danh mục mới
        </h4>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0">Thông tin danh mục</h6>
        </div>
        
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <h6 class="alert-heading">Có lỗi xảy ra:</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.categories.store') }}" id="categoryForm">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Tên danh mục <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}"
                                   placeholder="Nhập tên danh mục..."
                                   maxlength="255"
                                   required>
                            <div class="form-text">
                                Từ 2-255 ký tự. Chỉ chứa chữ, số, khoảng trắng và các ký tự: - _ .
                            </div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Danh mục cha</label>
                            <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">📁 Tạo danh mục gốc (cấp 1)</option>
                                @forelse ($parents as $p)
                                    <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>
                                        📂 {{ $p->name }}
                                    </option>
                                @empty
                                    <option value="" disabled>Chưa có danh mục gốc nào</option>
                                @endforelse
                            </select>
                            <div class="form-text">
                                Để trống nếu muốn tạo danh mục gốc. Hệ thống chỉ hỗ trợ 2 cấp danh mục.
                            </div>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>


                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Hủy
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Tạo danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('categoryForm');
    const nameInput = document.querySelector('input[name="name"]');
    
    // Validate tên danh mục real-time
    nameInput.addEventListener('input', function() {
        const value = this.value.trim();
        const regex = /^[a-zA-ZÀ-ỹ0-9\s\-\_\.]+$/u;
        
        if (value.length > 0 && !regex.test(value)) {
            this.classList.add('is-invalid');
            this.nextElementSibling.textContent = 'Tên danh mục chỉ được chứa chữ cái, số, khoảng trắng và các ký tự: - _ .';
        } else if (value.length > 0 && value.length < 2) {
            this.classList.add('is-invalid');
            this.nextElementSibling.textContent = 'Tên danh mục phải có ít nhất 2 ký tự.';
        } else {
            this.classList.remove('is-invalid');
            this.nextElementSibling.textContent = 'Từ 2-255 ký tự. Chỉ chứa chữ, số, khoảng trắng và các ký tự: - _ .';
        }
    });
});
</script>
@endsection
