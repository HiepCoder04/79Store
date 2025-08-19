@extends('admin.layouts.dashboard')

@section('content')
@php use Carbon\Carbon; @endphp

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
  .custom-table th, .custom-table td {
    text-align: center;
    vertical-align: middle;
    padding: 12px;
  }
  .badge-status {
    padding: 6px 12px;
    font-size: 0.8rem;
    border-radius: 999px;
    font-weight: 500;
    display: inline-block;
  }
  .badge-active { background-color: #28a745; color: white; }
  .badge-inactive { background-color: #6c757d; color: white; }
  .btn-add { border-radius: 10px; font-weight: 500; }
</style>


<div class="container table-container">
  <div class="d-flex justify-content-between align-items-center mb-4">

    <h5 class="mb-0"><i class="bi bi-ticket-perforated-fill text-danger me-2"></i>Danh sách voucher</h5>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary btn-add">
      <i class="bi bi-plus-circle me-1"></i> Thêm voucher
    </a>
  </div>


  {{-- Form lọc --}}
  <form method="GET" class="mb-4">
    <div class="row g-3">
      <div class="col-md-3">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm mã">
      </div>
      <div class="col-md-2">
        <select name="is_active" class="form-select">
          <option value="">-- Trạng thái --</option>
          <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt động</option>
          <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Ngừng</option>
        </select>
      </div>
      <div class="col-md-2">
        <select name="status" class="form-select">
          <option value="">-- Thời gian --</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang diễn ra</option>
          <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Hết hạn</option>
        </select>
      </div>
      <div class="col-md-2">
        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" placeholder="Từ ngày">
      </div>
      <div class="col-md-2">
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" placeholder="Đến ngày">
      </div>
      <div class="col-md-1 d-grid">
        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Lọc</button>
      </div>
    </div>
  </form>

  <div class="table-responsive">

    <table class="table custom-table table-bordered align-middle mb-0">
      <thead>
        <tr>
          <th>Mã</th>
          <th>% Giảm giá</th>
          <th>Bắt đầu</th>
          <th>Kết thúc</th>
          <th>Giảm tối đa</th>
          <th>Đơn tối thiểu</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @forelse($vouchers as $v)
          <tr>
            <td>{{ $v->code }}</td>
            <td>{{ $v->discount_percent }}%</td>
            <td>{{ Carbon::parse($v->start_date)->format('d/m/Y') }}</td>
            <td>{{ Carbon::parse($v->end_date)->format('d/m/Y') }}</td>
            <td>{{ number_format($v->max_discount, 0, ',', '.') }}đ</td>
            <td>{{ number_format($v->min_order_amount, 0, ',', '.') }}đ</td>
            <td>
              <span class="badge-status {{ $v->is_active ? 'badge-active' : 'badge-inactive' }}">
                {{ $v->is_active ? 'Hoạt động' : 'Ngừng' }}
              </span>
            </td>
            <td>
              <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="{{ route('admin.vouchers.edit', $v->id) }}" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil-square"></i> Sửa
                </a>
                <a href="{{ route('admin.vouchers.users', $v->id) }}" class="btn btn-sm btn-secondary">
                  <i class="bi bi-person-check"></i> Người dùng
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted">Không có voucher nào.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
      {{ $vouchers->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection
