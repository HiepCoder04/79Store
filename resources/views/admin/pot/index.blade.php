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

            {{-- Thêm phần pagination để đồng bộ --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    @if($pots->total() > 0)
                        Hiển thị {{ $pots->firstItem() }} - {{ $pots->lastItem() }} 
                        trong tổng số {{ $pots->total() }} chậu
                    @else
                        Không có chậu nào
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $pots->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection
