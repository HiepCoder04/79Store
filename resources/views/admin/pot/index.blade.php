@extends('admin.layouts.dashboard')

@section('content')

<style>
  .filter-box{border-radius:12px;padding:12px;background:#fff}
  .filter-box label{font-size:.9rem;color:#6b7280;margin-bottom:.35rem}
  .filter-field.form-control{border:1.5px solid #d1d5db;border-radius:10px}
  .filter-field:focus{border-color:#e91e63;box-shadow:0 0 0 .2rem rgba(233,30,99,.12);outline:0}
  .search h2 {
    font-size: 1.5rem;
    color: #333;
    margin: 20px;
  }
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-success">Quản lý chậu</h2>
        <a href="{{ route('admin.pot.create') }}" class="btn btn-success shadow">
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

    <div class="card border-0 shadow-sm">
    <div class="search">
    <h2>Tìm kiếm</h2>
    <form method="GET" action="{{ route('admin.pot.index') }}" class="card mb-4 p-3">
  <div class="row g-3 align-items-end">
    <div class="col-md-4">
      <div class="filter-box">
        <label class="form-label">Tên chậu</label>
        <input type="text" name="q" class="form-control filter-field"
               placeholder="Nhập tên chậu…" value="{{ request('q') }}">
      </div>
    </div>

    <div class="col-md-3">
      <div class="filter-box">
        <label class="form-label">Giá từ (VND)</label>
        <input type="number" name="price_min" min="0" step="1"
               class="form-control filter-field"
               value="{{ request('price_min') }}">
      </div>
    </div>

    <div class="col-md-3">
      <div class="filter-box">
        <label class="form-label">Đến (VND)</label>
        <input type="number" name="price_max" min="0" step="1"
               class="form-control filter-field"
               value="{{ request('price_max') }}">
      </div>
    </div>

    <div class="col-md-2 d-flex gap-2">
      <button type="submit" class="btn btn-primary w-100">Lọc</button>
      <a href="{{ route('admin.pot.index') }}" class="btn btn-outline-secondary w-100">Xoá lọc</a>
    </div>
  </div>
</form>
    </div>
        <div class="card-body">
            @if ($pots->isEmpty())
                <div class="text-center text-muted">Chưa có chậu nào.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>#</th>
                                <th>Tên chậu</th>
                                <th>Giá chậu</th>
                                <th>Ngày tạo</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pots as $index => $pot)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $pot->name }}</td>
                                    <td>{{ number_format($pot->price, 0, ',', '.') }} VND</td>
                                    <td class="text-center">{{ $pot->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.pot.edit', $pot->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form id="delete-{{$pot->id}}" action="{{ route('admin.pot.destroy', $pot->id) }}" onsubmit="return comfirm('Bạn muốn xóa chậu ???')" method="POST" class="d-inline delete-pot-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash-alt"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@