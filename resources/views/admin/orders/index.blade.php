@extends('admin.layouts.dashboard')
@section('title', 'Danh sách đơn hàng')

@section('content')
@php use Carbon\Carbon; @endphp

<style>
  body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', sans-serif;
  }

  .table-container {
    background: #ffffff;
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 0 20px rgba(0,0,0,0.08);
    margin: 40px auto;
    max-width: 1200px;
  }

  .table thead {
    background-color: #e2e3e5;
    color: white;
  }

  .table tbody tr:hover {
    background-color: #f1f1f1;
    cursor: pointer;
  }

  .table th,
  .table td {
    vertical-align: middle !important;
    text-align: center;
    padding: 12px;
  }

  .table td:first-child,
  .table th:first-child {
    text-align: center;
  }

  .btn-action {
    border-radius: 8px;
    font-size: 0.85rem;
    padding: 6px 12px;
  }

  .dropdown-menu a {
    display: flex;
    align-items: center;
    gap: 8px;
  }
</style>

<div class="container table-container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Danh sách đơn hàng</h4>
    <!-- Thêm các nút lọc nếu cần -->
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-bordered align-middle">
      <thead class="text-center">
        <tr>
          <th>#ID</th>
          <th>Khách hàng</th>
          <th>SĐT</th>
          <th>Ngày đặt</th>
          <th>Phương thức</th>
          <th>Thanh toán</th>
          <th>Trạng thái</th>
          <th>Tổng tiền</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($orders as $order)
        <tr>
          <td>{{ $order->id }}</td>
          <td>{{ $order->user->name ?? 'N/A' }}</td>
          <td>{{ $order->user->phone ?? '---' }}</td> b
          <td>{{ $order->created_at->format('d/m/Y') }}</td>
          <td>
            @if($order->payment_method == 'cod')
              <span class="badge bg-secondary">COD</span>
            @elseif($order->payment_method == 'vnpay')
              <span class="badge bg-primary">VNPAY</span>
            @else
              <span class="badge bg-info">{{ strtoupper($order->payment_method) }}</span>
            @endif
          </td>
          <td>
            @if($order->payment_status == 'paid' || ($order->payment_method == 'vnpay' && $order->status != 'cancelled'))
              <span class="badge bg-success">Đã thanh toán</span>
            @elseif($order->payment_status == 'pending')
              <span class="badge bg-warning">Chờ thanh toán</span>
            @elseif($order->payment_status == 'failed')
              <span class="badge bg-danger">Thất bại</span>
            @else
              <span class="badge bg-secondary">Chưa thanh toán</span>
            @endif
          </td>
          <td>
            @switch($order->status)
              @case('pending')
                <span class="badge bg-warning">Chờ xử lý</span>
                @break
              @case('confirmed')
                <span class="badge bg-info">Đang xử lý</span>
                @break
              @case('shipping')
                <span class="badge bg-primary">Đang giao</span>
                @break
              @case('delivered')
                <span class="badge bg-success">Hoàn tất</span>
                @break
              @case('cancelled')
                <span class="badge bg-danger">Đã huỷ</span>
                @break
              @case('returned')
                <span class="badge bg-secondary">Trả hàng</span>
                @break
              @default
                <span class="badge bg-light">{{ $order->status }}</span>
            @endswitch
          </td>
          <td>{{ number_format($order->total_after_discount, 0, ',', '.') }} đ</td>
          <td>
            <div class="dropdown">
              <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="{{ route('admin.orders.show', $order->id) }}">
                    <i class="bi bi-eye"></i> Xem chi tiết
                  </a>
                </li>
                <li>
                  <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" onsubmit="return confirm('Xóa đơn hàng này?')">
                    @csrf
                    @method('DELETE')
                    <button class="dropdown-item text-danger" type="submit">
                      <i class="bi bi-trash"></i> Xóa
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
