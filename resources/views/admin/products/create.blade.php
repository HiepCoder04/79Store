@extends('admin.layouts.dashboard')

@section('content')
<style>
.container {
    background: #ffffff;
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    max-width: 900px;
}

h2,
h4 {
    color: #333;
    margin-bottom: 20px;
}

label {
    font-weight: 600;
    margin-bottom: 6px;
    display: block;
}

.form-control {
    background-color: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 10px 14px;
    width: 100%;
    color: #1e293b;
    margin-bottom: 16px;
}

.form-control:focus {
    border-color: #3b82f6;
    outline: none;
    background-color: #fff;
}

.variant {
    background-color: #f9fafb;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
}

.btn-success {
    background-color: #16a34a;
    color: #fff;
    border: none;
}

.btn-secondary {
    background-color: #64748b;
    color: white;
    border: none;
}

.btn-danger {
    background-color: #dc2626;
    color: white;
    border: none;
}

.btn:hover {
    opacity: 0.9;
}

.alert-danger {
    background-color: #f87171;
    color: white;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 20px;
}

#imagePreview img {
    width: 100%;
    border-radius: 6px;
}

.position-relative {
    position: relative;
}

.position-absolute {
    position: absolute;
}
</style>

<div class="container">
    <h2>Thêm sản phẩm mới</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="name">Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div>
            <label for="description">Mô tả</label>
            <textarea rows="5" name="description" class="form-control">{{ old('description') }}</textarea>
            @error('description')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div>
            <label for="category_id">Danh mục</label>
            <select name="category_id" class="form-control">
                <option value="">-- Chọn danh mục --</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
            @error('category_id')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <h4>Biến thể sản phẩm</h4>
        <div id="variants">
            <div class="variant mb-3 border rounded p-3 position-relative">
                <button type="button" class="btn btn-danger btn-sm remove-variant position-absolute"
                    style="right: 10px; top: 10px;">X</button>

                <label>Chậu</label>
                <input type="text" name="variants[0][pot]" class="form-control" value="{{ old('variants.0.pot') }}">
                @error('variants.0.pot')
                <small class="text-danger">{{ $message }}</small>
                @enderror

                <label>Giá</label>
                <input type="number" name="variants[0][price]" class="form-control"
                    value="{{ old('variants.0.price') }}">
                @error('variants.0.price')
                <small class="text-danger">{{ $message }}</small>
                @enderror

                <label>Số lượng tồn</label>
                <input type="number" name="variants[0][stock_quantity]" class="form-control"
                    value="{{ old('variants.0.stock_quantity') }}">
                @error('variants.0.stock_quantity')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <button type="button" id="addVariant" class="btn btn-secondary mb-4">+ Thêm biến thể</button>

        <h4>Hình ảnh sản phẩm</h4>
        <input type="file" name="images[]" id="imageInput" class="form-control mb-4" multiple>
        @error('images')
        <small class="text-danger">{{ $message }}</small>
        @enderror
        <div id="imagePreview" class="row"></div>

        <button class="btn btn-success mt-4">Lưu sản phẩm</button>
    </form>
</div>

<script>
let variantIndex = 1;

document.getElementById('addVariant').onclick = function() {
    const variantHTML = `
        <div class="variant mb-3 border rounded p-3 position-relative">
            <button type="button" class="btn btn-danger btn-sm remove-variant position-absolute" style="right: 10px; top: 10px;">X</button>

            <label>Chậu</label>
            <input type="text" name="variants[${variantIndex}][pot]" class="form-control">

            <label>Giá</label>
            <input type="number" name="variants[${variantIndex}][price]" class="form-control">

            <label>Số lượng tồn</label>
            <input type="number" name="variants[${variantIndex}][stock_quantity]" class="form-control">
        </div>
    `;
    document.getElementById('variants').insertAdjacentHTML('beforeend', variantHTML);
    variantIndex++;
};

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-variant')) {
        e.target.closest('.variant').remove();
    }
});

document.getElementById('imageInput').addEventListener('change', function() {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-2';
            col.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" />`;
            preview.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection