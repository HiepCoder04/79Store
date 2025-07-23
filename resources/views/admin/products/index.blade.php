@extends('admin.layouts.dashboard')

@section('content')
<style>
.container {
    background: #ffffff;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
}

h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
}

.table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fdfdfd;
}

.table thead {
    background-color: #f1f1f1;
}

.table th,
.table td {
    text-align: left;
    padding: 12px 16px;
    border: 1px solid #ddd;
}

.table tbody tr:hover {
    background-color: #f9f9f9;
}

.btn {
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 4px;
    margin-right: 4px;
}

.btn-info {
    background-color: #17a2b8;
    border: none;
    color: white;
}

.btn-warning {
    background-color: #ffc107;
    border: none;
    color: black;
}

.btn-danger {
    background-color: #dc3545;
    border: none;
    color: white;
}

.btn-primary {
    background-color: #007bff;
    border: none;
    color: white;
}

.btn:hover {
    opacity: 0.85;
    transition: 0.2s ease;
}
</style>

<div class="container">
    <h2>Danh sách sản phẩm</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Thêm sản phẩm</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? 'N/A' }}</td>
                <td>{{ $product->variants->first()->price ?? 'N/A' }}</td>
                <td>
                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">Chi tiết</a>
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                        style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection