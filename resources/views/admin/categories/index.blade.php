@extends('admin.layouts.dashboard')

@section('title', 'Danh sách danh mục | 79Store')

@section('content')
<style>
    .table-container {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-top: 25px;
    }

    .table thead {
        background-color: #f8f9fa;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .table th, .table td {
        vertical-align: middle;
        padding: 12px 15px;
    }

    .table-hover tbody tr:hover {
        background-color: #f5f7fa;
        transition: all 0.2s ease;
    }

    .btn-add {
        border-radius: 8px;
        font-weight: 500;
    }

    .badge-parent {
        background-color: #0d6efd;
        color: #fff;
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 999px;
    }

    .badge-none {
        background: #adb5bd;
        color: #fff;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
    }

    .action-btn {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .action-btn a, .action-btn button {
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 0.85rem;
    }

    .alert {
        border-radius: 8px;
        margin-bottom: 15px;
    }
</style>

@if (session('success'))
    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
@endif

<div class="container table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-list-task me-2 text-primary"></i> Quản lý danh mục
        </h5>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-add">
            <i class="bi bi-plus-circle me-1"></i> Thêm danh mục
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px">ID</th>
                    <th class="text-start">Tên danh mục</th>
                    <th>Danh mục cha</th>
                    <th style="width: 150px">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td class="text-start fw-medium">{{ $cat->name }}</td>
                        <td>
                            @if($cat->parent)
                                <span class="badge-parent">{{ $cat->parent->name }}</span>
                            @else
                                <span class="badge-none">Không có</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-btn">
                                <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm">
                                    <button type="submit" class="btn btn-sm btn-danger ">
                                        Sửa
                                    </button>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $cat) }}" 
                                    method="POST" 
                                    style="display:inline" 
                                    onsubmit="return confirm('Xoá danh mục này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger mt-1">
                                        Xoá
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4"></i> <br> Chưa có danh mục nào
                        </td>
                    </tr>
                @endforelse
                
            </tbody>
        </table>
    </div>
</div>
@endsection
