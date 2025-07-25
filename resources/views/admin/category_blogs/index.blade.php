@extends('admin.layouts.dashboard')

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

  .badge-count {
    background-color: #0d6efd;
    color: #fff;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 500;
  }

  .dropdown .btn {
    border-radius: 8px;
    padding: 6px 12px;
  }
</style>

<div class="container table-container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0"><i class="bi bi-journal-text me-2 text-primary"></i>Danh sách danh mục blog</h5>
    <a href="{{ route('admin.category_blogs.create') }}" class="btn btn-info text-white btn-add">
      <i class="bi bi-plus-circle me-1"></i> Thêm danh mục
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="table-responsive">
    <table class="table custom-table table-bordered align-middle mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Tên danh mục</th>
          <th>Số lượng bài viết</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @foreach($categories as $index => $category)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td class="text-start">{{ $category->name }}</td>
            <td>
              <span class="badge-count">{{ $category->blogs_count }}</span>
            </td>
            <td>
              <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="{{ route('admin.category_blogs.edit', $category) }}">
                      <i class="bi bi-pencil-square me-1"></i> Sửa
                    </a>
                  </li>
                  <li>
                    <form action="{{ route('admin.category_blogs.destroy', $category) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                      @csrf
                      @method('DELETE')
                      <button class="dropdown-item text-danger" type="submit">
                        <i class="bi bi-trash me-1"></i> Xóa
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
