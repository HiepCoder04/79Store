@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <h2>Thêm chậu mới</h2>

    {{-- Hiển thị lỗi --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.pot.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Tên chậu</label>
            <input type="text" class="form-control bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500" id="name" name="name" >
        </div>

        <button type="submit" class="btn btn-success">Thêm</button>
        <a href="{{ route('admin.pot.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
