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

    .img-thumb {
        height: 60px;
        width: 100px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
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

    .dropdown .btn {
        border-radius: 8px;
        padding: 6px 12px;
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
  .filter-field.form-select{border:1.5px solid #d1d5db;border-radius:10px}
  .filter-field:focus{border-color:#e91e63;box-shadow:0 0 0 .2rem rgba(233,30,99,.12);outline:0}
</style>

<div class="container table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0"><i class="bi bi-bullseye title-icon"></i>Danh sách banner</h5>
        <a href="{{ route('admin.banners.create') }}" class="btn btn-success btn-add">
            <i class="bi bi-plus-circle me-1"></i> Thêm banner
        </a>
    </div>
    {{-- tim kiem --}}
    <form method="GET" action="{{ route('admin.banners.index') }}" class="card mb-4 p-3">
  <div class="row g-3 align-items-end">
    <div class="col-md-3">
      <div class="filter-box">
        <label class="form-label">Trạng thái</label>
        <select name="is_active" class="form-select filter-field">
          <option value="">-- Tất cả --</option>
          <option value="1" @selected(request('is_active')==='1')>Hoạt động</option>
          <option value="0" @selected(request('is_active')==='0')>Tắt</option>
        </select>
      </div>
    </div>

    <div class="col-md-3 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Lọc</button>
      <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">Xoá lọc</a>
    </div>
  </div>
</form>

    <table class="table custom-table table-bordered align-middle">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Link</th>
                <th>Mô tả</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($banners as $banner)
                <tr>
                    <td>
                        <img src="{{ asset($banner->image) }}" class="img-thumb" alt="banner">
                    </td>
                    <td class="text-start">{{ $banner->link }}</td>
                    <td class="text-start">{{ $banner->description ?: '—' }}</td>
                    <td>
                        <span class="badge-status">
                            <i class="bi bi-eye-fill me-1"></i> HIỂN THỊ
                        </span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.banners.edit', $banner) }}">
                                        <i class="bi bi-pencil-square me-1"></i> Sửa
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Xóa banner này?')">
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

{{-- Thêm phần pagination để đồng bộ --}}
<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        @if($banners->total() > 0)
            Hiển thị {{ $banners->firstItem() }} - {{ $banners->lastItem() }} 
            trong tổng số {{ $banners->total() }} banner
        @else
            Không có banner nào
        @endif
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $banners->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
</div>
@endsection
