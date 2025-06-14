@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <h2>Chi tiết sản phẩm: {{ $product->name }}</h2>

    <div>
        <strong>Tên:</strong> {{ $product->name }}<br>
        <strong>Danh mục:</strong> {{ $product->category->name }}<br>
        <strong>Mô tả:</strong> {!! $product->description !!}<br>
        <strong>Trạng thái:</strong> {{ $product->is_active ? 'Hoạt động' : 'Không hoạt động' }}<br>
        <strong>Ngày tạo:</strong> {{ $product->created_at }}
    </div>

    <h3 class="mt-4">Biến thể sản phẩm</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kích thước</th>
                <th>Chậu</th>
                <th>Giá</th>
                <th>Số lượng tồn kho</th>
            </tr>
        </thead>
        <tbody>
            @foreach($product->variants as $variant)
                <tr>
                    <td>{{ $variant->size }}</td>
                    <td>{{ $variant->pot ?? 'Không có' }}</td>
                    <td>{{ $variant->price }}</td>
                    <td>{{ $variant->stock_quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 class="mt-4">Hình ảnh sản phẩm</h3>
    <div class="row">
        @foreach($product->galleries as $gallery)
            <div class="col-md-3">
                <img src="{{ $gallery->image }}" class="img-fluid img-thumbnail" alt="Product Image">
            </div>
        @endforeach
    </div>

    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mt-3">Quay lại danh sách</a>
</div>
@endsection
