@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <h2>Chỉnh sửa sản phẩm</h2>
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')  

        <div class="mb-3">
            <label for="name">Tên sản phẩm</label>
            <input type="text" name="name" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500" value="{{ $product->name }}" required>
        </div>
        <div class="mb-3">
            <label>Mô tả</label>
            <textarea rows="5" style="width: 100%;"  type="text" name="description" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500" required> {{ $product->description }}</textarea>
        </div>
        <div class="mb-3">
            <label for="category_id">Danh mục</label>
            <select name="category_id" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500" required>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>

        <h4>Biến thể sản phẩm</h4>
        <div id="variants">
            @foreach ($product->variants as $index => $variant)
            <div class="variant mb-3 border p-3 rounded">
                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

                <label>Chiều cao (height)</label>
                <input type="text" name="variants[{{ $index }}][height]" value="{{ $variant->height }}" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">

                <label>Giá</label>
                <input type="number" name="variants[{{ $index }}][price]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500" value="{{ $variant->price }}">

                <br>
                <label>Số lượng tồn</label>
                <input type="number" name="variants[{{ $index }}][stock_quantity]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500" value="{{ $variant->stock_quantity }}">
            </div>
            @endforeach
        </div>

        <button type="button" id="addVariant" class="btn btn-sm btn-secondary">+ Thêm biến thể</button>

        <h4 class="mt-4">Hình ảnh sản phẩm</h4>
        <div class="row mb-3">
            @foreach($product->galleries as $gallery)
            <div class="col-md-3">
                <img src="{{ asset($gallery->image) }}" class="img-thumbnail mb-2" style="height: 120px;">
                {{-- Có thể thêm nút xoá ảnh nếu cần --}}

                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $gallery->id }}" id="delete_{{ $gallery->id }}">
                    <label class="form-check-label" for="delete_{{ $gallery->id }}">Xoá ảnh</label>
                </div>
            </div>
            @endforeach
        </div>

        <input type="file" name="images[]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500" multiple>

        <button class="btn btn-primary mt-3">Cập nhật</button>
    </form>
</div>

<script>
    let variantIndex = {{ $product->variants->count() }};
    document.getElementById('addVariant').onclick = function () {
        const variantHtml = `
            <div class="variant mb-3 border p-3 rounded">
                
              

                <label>Chiều cao (height)</label>
                <input type="text" name="variants[${variantIndex}][height]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">

                <label>Giá</label>
                <input type="number" name="variants[${variantIndex}][price]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">

                <label>Số lượng tồn</label>
                <input type="number" name="variants[${variantIndex}][stock_quantity]" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
            </div>
        `;
        document.getElementById('variants').insertAdjacentHTML('beforeend', variantHtml);
        variantIndex++;
    };
</script>
@endsection
