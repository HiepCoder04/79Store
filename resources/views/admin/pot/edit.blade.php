@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <h2>Chỉnh sửa chậu</h2>

    <form action="{{ route('admin.pot.update', $pot->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Tên chậu</label>
            <input type="text" class="form-control bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500" id="name" name="name" value="{{ $pot->name }}">
        </div>
        <div class="form-group">
            <label for="price">Giá (VNĐ)</label>
            <input type="number" step="0.01" name="price" class="form-control bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500" value="{{ old('price', $pot->price) }}" required>
        </div>
        <div class="form-group">
            <label for="quantity">Số lượng</label>
        <input type="number" name="quantity" class="form-control bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500"
               value="{{ old('quantity', $pot->quantity) }}" required>
        </div>


        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.pot.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
