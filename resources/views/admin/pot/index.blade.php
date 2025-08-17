@extends('admin.layouts.dashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    .table-container {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .table thead {
        background: #f8f9fa;
        font-weight: 600;
    }
    .table th, .table td {
        vertical-align: middle;
        text-align: center;
    }
    .btn-add {
        border-radius: 8px;
        font-weight: 500;
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="fa fa-leaf me-2"></i>Quản lý chậu</h4>
        <a href="{{ route('admin.pot.create') }}" class="btn btn-success shadow btn-add">
            <i class="fa fa-plus me-1"></i> Thêm chậu mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-container">
        @if ($pots->isEmpty())
            <div class="text-center text-muted py-4">Chưa có chậu nào.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên chậu</th>
                            <th>Giá</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pots as $index => $pot)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-start">{{ $pot->name }}</td>
                                <td>{{ number_format($pot->price, 0, ',', '.') }}đ</td>
                                <td>{{ $pot->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.pot.edit', $pot->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            Sửa
                                        </a>
                                        <form action="{{ route('admin.pot.destroy', $pot->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Bạn muốn xóa chậu này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
