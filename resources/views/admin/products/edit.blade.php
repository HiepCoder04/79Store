@extends('admin.layouts.dashboard')

@section('content')
<div class="container py-4 px-3 bg-white rounded shadow">
    <h2 class="text-xl font-semibold mb-4">Chỉnh sửa sản phẩm</h2>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Tên sản phẩm --}}
        <div class="mb-4">
            <label for="name" class="form-label fw-bold">Tên sản phẩm</label>
            <input type="text" name="name" value="{{ $product->name }}"
                class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500"
                required>
        </div>

        {{-- Mô tả --}}
        <div class="mb-4">
            <label class="form-label fw-bold">Mô tả</label>
            <textarea name="description" rows="5" required
                class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">{{ trim($product->description) }}</textarea>
        </div>

        {{-- Danh mục --}}
        <div class="mb-4">
            <label for="category_id" class="form-label fw-bold">Danh mục</label>
            <select name="category_id" required
                class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Biến thể --}}
        <h4 class="text-lg font-medium mt-5 mb-3 border-bottom pb-2">Biến thể sản phẩm</h4>
        <div id="variants">
            @foreach ($product->variants as $index => $variant)
            <div class="variant mb-4 border border-gray-300 p-3 rounded">
                <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

                <div class="mb-2">
                    <label class="form-label">Chậu</label>
                    <input type="text" name="variants[{{ $index }}][pot]"
                        class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full" value="{{ $variant->pot }}">
                </div>

                <div class="mb-2">
                    <label class="form-label">Giá</label>
                    <input type="number" name="variants[{{ $index }}][price]"
                        class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full"
                        value="{{ $variant->price }}">
                </div>

                <div>
                    <label class="form-label">Số lượng tồn</label>
                    <input type="number" name="variants[{{ $index }}][stock_quantity]"
                        class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full"
                        value="{{ $variant->stock_quantity }}">
                </div>
            </div>
            @endforeach
        </div>

        <button type="button" id="addVariant" class="btn btn-outline-primary btn-sm mb-3">+ Thêm biến thể</button>

        {{-- Hình ảnh --}}
        <h4 class="text-lg font-medium mt-5 mb-3 border-bottom pb-2">Hình ảnh sản phẩm</h4>
        <div class="row g-3 mb-4">
            @foreach($product->galleries as $gallery)
            <div class="col-md-3">
                <img src="{{ asset($gallery->image) }}" class="img-thumbnail mb-2"
                    style="height: 120px; object-fit: cover;">
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="delete_images[]" value="{{ $gallery->id }}"
                        id="delete_{{ $gallery->id }}">
                    <label class="form-check-label" for="delete_{{ $gallery->id }}">Xoá ảnh</label>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mb-4">
            <label class="form-label">Tải ảnh mới</label>
            <input type="file" name="images[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-success">💾 Cập nhật</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary ms-2">← Quay lại danh sách</a>
    </form>
</div>

{{-- JS thêm biến thể --}}
<script>
let variantIndex = {
    {
        $product - > variants - > count()
    }
};
document.getElementById('addVariant').onclick = function() {
    const variantHtml = `
            <div class="variant mb-4 border border-gray-300 p-3 rounded">
                <div class="mb-2">
                    <label>Chậu</label>
                    <input type="text" name="variants[\${variantIndex}][pot]"
                        class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full">
                </div>
                <div class="mb-2">
                    <label>Giá</label>
                    <input type="number" name="variants[\${variantIndex}][price]"
                        class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full">
                </div>
                <div>
                    <label>Số lượng tồn</label>
                    <input type="number" name="variants[\${variantIndex}][stock_quantity]"
                        class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full">
                </div>
            </div>
        `;
    document.getElementById('variants').insertAdjacentHTML('beforeend', variantHtml);
    variantIndex++;
};
</script>
@endsection