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

    .custom-table th,
    .custom-table td {
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

    .badge-active {
    background-color: #28a745;
    color: white;
    }

    .badge-inactive {
    background-color: #6c757d;
    color: white;
    }

    .btn-add {
    border-radius: 10px;
    font-weight: 500;
    }
    .filter-box{
    border-radius:12px;               /* bo góc */
    padding:12px;
    background:#fff;
  }
  .filter-box label{
    font-size:.9rem; color:#6b7280; margin-bottom:.35rem;
  }
  .filter-field.form-control,
  .filter-field.form-select{
    border:1.5px solid #d1d5db;      /* viền input rõ hơn */
    border-radius:10px;
  }
  .filter-field:focus{
    border-color:#e91e63;             /* màu viền khi focus (cùng tone nút Lọc) */
    box-shadow:0 0 0 .2rem rgba(233,30,99,.12);
    outline:0;
  }
  </style>

  <div class="container table-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0"><i class="bi bi-ticket-perforated-fill text-danger me-2"></i>Danh sách voucher</h5>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary btn-add">
      <i class="bi bi-plus-circle me-1"></i> Thêm voucher
    </a>
    </div>

    <div class="table-responsive">
    {{-- loc tim kiem --}}
    <form method="GET" action="{{ route('admin.vouchers.index') }}" class="card mb-4 p-3">
  <div class="row g-3 align-items-end">

    <div class="col-md-3">
      <div class="filter-box">
        <label class="form-label">Mã voucher</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-ticket-alt"></i></span>
          <input type="text" name="code" class="form-control filter-field" placeholder="Nhập mã..."
                 value="{{ request('code') }}">
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="filter-box">
        <label class="form-label">Ngày bắt đầu</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
          <input type="date" name="start_date" class="form-control filter-field"
                 value="{{ request('start_date') }}">
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="filter-box">
        <label class="form-label">Ngày kết thúc</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fa fa-calendar"></i></span>
          <input type="date" name="end_date" class="form-control filter-field"
                 value="{{ request('end_date') }}">
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="filter-box">
        <label class="form-label">Trạng thái</label>
        <select name="is_active" class="form-select filter-field">
          <option value="">-- Tất cả --</option>
          <option value="1" @selected(request('is_active')==='1')>Bật</option>
          <option value="0" @selected(request('is_active')==='0')>Tắt</option>
        </select>
      </div>
    </div>

    <div class="col-12 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Lọc</button>
      <a href="{{ route('admin.vouchers.index') }}" class="btn btn-outline-secondary">Xoá lọc</a>
    </div>
  </div>
</form>


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
      @foreach($vouchers as $v)
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
    @endforeach
      </tbody>
    </table>
    <div class="d-flex justify-content-center">
            {{ $vouchers->links('pagination::bootstrap-5') }}
        </div>
    </div>
  </div>
@endsection