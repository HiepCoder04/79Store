@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <h2>Thêm sản phẩm mới</h2>

   
@if(session('error'))
    <div class="alert alert-danger text-white-50">{{ session('error') }}</div>
@endif
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label>Tên sản phẩm</label>
            <input type="text" name="name" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-3">
            <label>Mô tả</label>
            <textarea  rows="5" style="width: 100%;" type="text" name="description" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500"></textarea>
        </div>
        
        <div class="mb-3">
            <label>Danh mục</label>
            <select name="category_id" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
                <option value="">-- Chọn danh mục --</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <h4 class="mt-4">Biến thể sản phẩm</h4>
        <div id="variants">
            <div class="variant mb-3 border rounded p-3 position-relative">
                <button type="button" class="btn btn-danger btn-sm remove-variant position-absolute" style="right: 10px; top: 10px;">X</button>
                <label>Chậu</label>
                <input type="text" name="variants[0][pot]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
                
                <label for="height">Chiều cao (height)</label>
                <input type="text" name="variants[0][height]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">

                <label>Giá</label>
                <input type="number" name="variants[0][price]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">

                <br>
                <label>Số lượng tồn</label>
                <input type="number" name="variants[0][stock_quantity]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
            </div>
        </div>
        <button type="button" id="addVariant" class="btn btn-secondary mb-4">+ Thêm biến thể</button>

        <h4>Hình ảnh sản phẩm</h4>
        <input type="file" name="images[]" id="imageInput" class="btn btn-secondary mb-4" multiple>
        <div id="imagePreview" class="row"></div>

        <button class="btn btn-success mt-4">Lưu sản phẩm</button>
    </form>
</div>

{{-- Script --}}
<script>
    let variantIndex = 1;

    document.getElementById('addVariant').onclick = function () {
        const variantHTML = `
            <div class="variant mb-3 border rounded p-3 position-relative">
                <button type="button" class="btn btn-danger btn-sm remove-variant position-absolute" style="right: 10px; top: 10px;">X</button>

               
                <label>Chậu</label>
                <input type="text" name="variants[${variantIndex}][pot]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">

                 <label>Chiều cao (height)</label>
                <input type="text" name="variants[${variantIndex}][height]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">

                <label>Giá</label>
                <input type="number" name="variants[${variantIndex}][price]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">

                <label>Số lượng tồn</label>
                <input type="number" name="variants[${variantIndex}][stock_quantity]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
            </div>
        `;
        document.getElementById('variants').insertAdjacentHTML('beforeend', variantHTML);
        variantIndex++;
    };

    // Xóa biến thể
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant').remove();
        }
    });

    // Preview hình ảnh
    document.getElementById('imageInput').addEventListener('change', function () {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function (e) {
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
