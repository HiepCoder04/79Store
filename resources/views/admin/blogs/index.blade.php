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

    .badge-status {
        background-color: #28a745;
        color: #fff;
        font-weight: 500;
        font-size: 0.875rem;
        padding: 6px 14px;
        border-radius: 999px;
        display: inline-block;
    }

    .badge-inactive {
        background-color: #6c757d;
        color: #fff;
        font-weight: 500;
        font-size: 0.875rem;
        padding: 6px 14px;
        border-radius: 999px;
        display: inline-block;
    }

    .dropdown .btn {
        border-radius: 8px;
        padding: 6px 12px;
    }

    .avatar-sm {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .btn-add {
        border-radius: 10px;
        font-weight: 500;
    }

    .title-icon {
        color: #198754;
        font-size: 1.3rem;
        margin-right: 8px;
    }
     .filter-box{border-radius:12px;padding:12px;background:#fff}
  .filter-box label{font-size:.9rem;color:#6b7280;margin-bottom:.35rem}
  .filter-field.form-control,
  .filter-field.form-select{border:1.5px solid #d1d5db;border-radius:10px}
  .filter-field:focus{border-color:#e91e63;box-shadow:0 0 0 .2rem rgba(233,30,99,.12);outline:0}
</style>

<div class="container table-container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0"><i class="bi bi-file-text title-icon"></i>Danh sách bài viết</h5>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-success btn-add">
      <i class="bi bi-plus-circle me-1"></i> Thêm bài viết
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  <form method="GET" action="{{ route('admin.blogs.index') }}" class="card mb-4 p-3">
  <div class="row g-3 align-items-end">

    <div class="col-md-4">
      <div class="filter-box">
        <label class="form-label">Tiêu đề</label>
        <input type="text" name="title" class="form-control filter-field"
               placeholder="Nhập tiêu đề…" value="{{ request('title') }}">
      </div>
    </div>

    <div class="col-md-4">
      <div class="filter-box">
        <label class="form-label">Danh mục blog</label>
        <select name="category_blog_id" class="form-select filter-field">
          <option value="">-- Tất cả --</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @selected(request('category_blog_id')==$cat->id)>
              {{ $cat->name }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="col-md-4">
      <div class="filter-box">
        <label class="form-label">Trạng thái</label>
        <select name="is_active" class="form-select filter-field">
          <option value="">-- Tất cả --</option>
          <option value="1" @selected(request('is_active')==='1')>Hoạt động</option>
          <option value="0" @selected(request('is_active')==='0')>Tắt</option>
        </select>
      </div>
    </div>

    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Lọc</button>
      <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">Xoá lọc</a>
    </div>
  </div>
</form>

  <div class="table-responsive">
    <table class="table custom-table table-bordered align-middle mb-0">
      <thead>
        <tr>
          <th>STT</th>
          <th>Hình ảnh</th>
          <th>Tiêu đề</th>
          <th>Danh mục</th>
          <th>Ngày tạo</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @foreach($blogs as $blog)
        <tr>
          <td>{{ $blog->id }}</td>
          <td>
            <img src="{{ asset($blog->img ?: 'assets/img/no-image.jpg') }}" alt="{{ $blog->title }}" class="avatar-sm">
          </td>
          <td class="text-start">
            <strong>{{ $blog->title }}</strong><br>
            <small class="text-muted">{{ Str::limit($blog->slug, 30) }}</small>
          </td>
          <td>{{ optional($blog->category)->name ?? '—' }}</td>
          <td>{{ $blog->created_at->format('d/m/Y') }}</td>
          <td>
            @if($blog->is_active)
              <span class="badge-status"><i class="bi bi-check-circle-fill me-1"></i>Kích hoạt</span>
            @else
              <span class="badge-inactive"><i class="bi bi-x-circle-fill me-1"></i>Ẩn</span>
            @endif
          </td>
          <td>
            <div class="dropdown">
              <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="{{ route('admin.blogs.edit', $blog) }}">
                    <i class="bi bi-pencil-square me-1"></i> Sửa
                  </a>
                </li>
                <li>
                  <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" onsubmit="return confirm('Xóa bài viết này?')">
                    @csrf @method('DELETE')
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

  @if(method_exists($blogs, 'links'))
    <div class="mt-3">
      {{ $blogs->links() }}
    </div>
  @endif
</div>
@endsection
