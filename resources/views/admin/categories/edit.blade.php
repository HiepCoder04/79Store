@extends('admin.layouts.dashboard')

@section('title', 'Sửa danh mục | 79Store')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-pencil-square me-2 text-warning"></i>Chỉnh sửa danh mục
        </h4>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h6 class="mb-0">Thông tin danh mục: {{ $category->name }}</h6>
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

            <form method="POST" action="{{ route('admin.categories.update', $category) }}" id="categoryForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Tên danh mục <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $category->name) }}"
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
                                <option value="">📁 Là danh mục gốc (cấp 1)</option>
                                @foreach ($parents as $p)
                                    <option value="{{ $p->id }}" {{ old('parent_id', $category->parent_id) == $p->id ? 'selected' : '' }}>
                                        📂 {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                @if($category->children()->exists())
                                    <span class="text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Danh mục này đang có danh mục con, không thể đổi thành danh mục con.
                                    </span>
                                @else
                                    Để trống nếu muốn là danh mục gốc.
                                @endif
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
                    <button type="submit" class="btn btn-warning text-dark">
                        <i class="bi bi-check-circle me-1"></i>Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
