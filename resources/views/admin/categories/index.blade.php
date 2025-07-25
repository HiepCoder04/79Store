@extends('admin.layouts.dashboard')

@section('title', 'Danh sách danh mục | 79Store')

@section('content')
<style>
    .table-container {
        background: #fff;
        padding: 30px;
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        margin-top: 30px;
    }

    .custom-table thead {
        background-color: #e2e3e5;
        color: #000;
        font-weight: 600;
    }

    .custom-table th,
    .custom-table td {
        text-align: center;
        vertical-align: middle;
        padding: 14px;
    }

    .btn-add {
        border-radius: 10px;
        font-weight: 500;
    }

    .dropdown .btn {
        border-radius: 8px;
        padding: 6px 12px;
    }

    .badge-parent {
        background-color: #0d6efd;
        color: #fff;
        font-size: 0.8rem;
        padding: 5px 10px;
        border-radius: 999px;
    }
</style>

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="container table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0"><i class="bi bi-list-task me-2 text-primary"></i>Danh sách danh mục</h5>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-add">
            <i class="bi bi-plus-circle me-1"></i> Thêm danh mục
        </a>
    </div>

    <div class="table-responsive">
        <table class="table custom-table table-bordered align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Danh mục cha</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td class="text-start">{{ $cat->name }}</td>
                        <td>
                            @if($cat->parent)
                                <span class="badge-parent">{{ $cat->parent->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.categories.edit', $cat) }}">
                                            <i class="bi bi-pencil-square me-1"></i> Sửa
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Xoá danh mục này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit">
                                                <i class="bi bi-trash me-1"></i> Xoá
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
