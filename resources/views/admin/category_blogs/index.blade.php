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
   .filter-box{border-radius:12px;padding:12px;background:#fff}
  .filter-box label{font-size:.9rem;color:#6b7280;margin-bottom:.35rem}
  .filter-field.form-control{border:1.5px solid #d1d5db;border-radius:10px}
  .filter-field:focus{border-color:#e91e63;box-shadow:0 0 0 .2rem rgba(233,30,99,.12);outline:0}
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
<form method="GET" action="{{ route('admin.category_blogs.index') }}" class="card mb-4 p-3">
  <div class="row g-3 align-items-end">
    <div class="col-md-6">
      <div class="filter-box">
        <label class="form-label">Tên danh mục</label>
        <input type="text" name="q" class="form-control filter-field"
               placeholder="Nhập tên danh mục…" value="{{ request('q') }}">
      </div>
    </div>

    <div class="col-md-6 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Lọc</button>
      <a href="{{ route('admin.category_blogs.index') }}" class="btn btn-outline-secondary">Xoá lọc</a>
    </div>
  </div>
</form>


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
