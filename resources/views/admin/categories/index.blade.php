@extends('admin.layouts.dashboard')

@section('title', 'Danh sách danh mục | 79Store')

@section('content')
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
</div>
@endif

<div class="card mt-4 shadow border-0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-primary">📂 Danh sách danh mục</h5>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle"></i> Thêm danh mục
        </a>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="text-uppercase text-muted small fw-semibold">ID</th>
                    <th scope="col" class="text-uppercase text-muted small fw-semibold">Tên</th>
                    <th scope="col" class="text-uppercase text-muted small fw-semibold">Danh mục cha</th>
                    <th scope="col" class="text-uppercase text-muted small fw-semibold text-center">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td>{{ $cat->name }}</td>
                    <td>{{ $cat->parent->name ?? '-' }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.categories.edit', $cat) }}"
                            class="btn btn-outline-primary btn-sm me-1">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </a>
                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
                            class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Bạn chắc chắn muốn xoá danh mục này?')"
                                class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash"></i> Xoá
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Không có danh mục nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection