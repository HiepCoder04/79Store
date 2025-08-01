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

  <div class="table-responsive">
    <table class="table custom-table table-bordered align-middle mb-0">
      <thead>
        <tr>
          <th>ID</th>
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
