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

  /* ✅ THÊM STYLE CHO NHÃN PHỤ LINK */
  .badge.bg-secondary:hover {
    background-color: #495057 !important;
    transform: scale(1.05);
    transition: all 0.2s ease;
  }

  .badge.text-decoration-none:hover {
    text-decoration: underline !important;
  }
</style>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Danh sách đơn hàng</h2>
            <button class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#filterSection">
                <i class="fas fa-filter"></i> Bộ lọc nâng cao
            </button>
        </div>

        <!-- Thống kê nhanh -->
        @if(isset($stats))
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>{{ $stats['total_orders'] }}</h5>
                        <small>Tổng đơn hàng</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h6>{{ number_format($stats['total_revenue'], 0, ',', '.') }}đ</h6>
                        <small>Tổng doanh thu</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h5>{{ $stats['pending_orders'] }}</h5>
                        <small>Chờ xác nhận</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h5>{{ $stats['delivered_orders'] }}</h5>
                        <small>Đã giao</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h6>{{ number_format($stats['average_order_value'], 0, ',', '.') }}đ</h6>
                        <small>Giá trị đơn trung bình</small>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Bộ lọc nâng cao -->
        <div class="collapse @if(request()->hasAny(['status', 'date_filter', 'payment_method', 'payment_status', 'search', 'amount_filter'])) show @endif" id="filterSection">
            <div class="card card-body mb-4 bg-light">
                <form method="GET" action="{{ route('admin.orders.index') }}">
                    <div class="row g-3">
                        <!-- Lọc theo trạng thái đơn hàng -->
                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-list"></i> Trạng thái đơn hàng</label>
                            <select name="status" class="form-select">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="pending" @selected(request('status') === 'pending')>Chờ xác nhận</option>
                                <option value="confirmed" @selected(request('status') === 'confirmed')>Đã xác nhận</option>
                                <option value="shipping" @selected(request('status') === 'shipping')>Đang giao</option>
                                <option value="delivered" @selected(request('status') === 'delivered')>Hoàn tất</option>
                                <option value="cancelled" @selected(request('status') === 'cancelled')>Đã hủy</option>
                                <option value="returned" @selected(request('status') === 'returned')>Trả hàng</option>
                                {{-- ✅ THÊM CÁC OPTION LỌC TRẢ HÀNG --}}
                                <option value="delivered_with_returns" @selected(request('status') === 'delivered_with_returns')>Hoàn tất - Có trả hàng</option>
                                <option value="delivered_fully_returned" @selected(request('status') === 'delivered_fully_returned')>Hoàn tất - Hoàn trả hết</option>
                                <option value="delivered_partial_returned" @selected(request('status') === 'delivered_partial_returned')>Hoàn tất - Trả một phần</option>
                            </select>
                        </div>

                        <!-- Lọc theo thời gian -->
                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-calendar"></i> Thời gian</label>
                            <select name="date_filter" class="form-select" id="dateFilter">
                                <option value="">-- Tất cả thời gian --</option>
                                <option value="today" @selected(request('date_filter') === 'today')>Hôm nay</option>
                                <option value="yesterday" @selected(request('date_filter') === 'yesterday')>Hôm qua</option>
                                <option value="this_week" @selected(request('date_filter') === 'this_week')>Tuần này</option>
                                <option value="this_month" @selected(request('date_filter') === 'this_month')>Tháng này</option>
                                <option value="last_month" @selected(request('date_filter') === 'last_month')>Tháng trước</option>
                                <option value="custom" @selected(request('date_filter') === 'custom')>Tùy chọn</option>
                            </select>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-credit-card"></i> Phương thức thanh toán</label>
                            <select name="payment_method" class="form-select">
                                <option value="">-- Tất cả phương thức --</option>
                                <option value="cod" @selected(request('payment_method') === 'cod')>COD</option>
                                <option value="vnpay" @selected(request('payment_method') === 'vnpay')>VNPay</option>
                            </select>
                        </div>

                        <!-- Trạng thái thanh toán -->
                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-money-check"></i> Trạng thái thanh toán</label>
                            <select name="payment_status" class="form-select">
                                <option value="">-- Tất cả trạng thái --</option>
                                <option value="unpaid" @selected(request('payment_status') === 'unpaid')>Chưa thanh toán</option>
                                <option value="pending" @selected(request('payment_status') === 'pending')>Chờ thanh toán</option>
                                <option value="paid" @selected(request('payment_status') === 'paid')>Đã thanh toán</option>
                                <option value="failed" @selected(request('payment_status') === 'failed')>Thất bại</option>
                                <option value="refunded" @selected(request('payment_status') === 'refunded')>Đã hoàn tiền</option>
                            </select>
                        </div>

                        <!-- Tìm kiếm nâng cao -->
                        <div class="col-md-6">
                            <label class="form-label"><i class="fas fa-search"></i> Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" value="{{ request('search') }}" 
                                   placeholder="Mã đơn, tên khách hàng, email, SĐT, tên sản phẩm...">
                        </div>

                        <!-- Lọc theo giá trị đơn hàng -->
                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-dollar-sign"></i> Giá trị đơn hàng</label>
                            <select name="amount_filter" class="form-select" id="amountFilter">
                                <option value="">-- Tất cả giá trị --</option>
                                <option value="under_500k" @selected(request('amount_filter') === 'under_500k')>Dưới 500K</option>
                                <option value="500k_1m" @selected(request('amount_filter') === '500k_1m')>500K - 1M</option>
                                <option value="1m_2m" @selected(request('amount_filter') === '1m_2m')>1M - 2M</option>
                                <option value="2m_5m" @selected(request('amount_filter') === '2m_5m')>2M - 5M</option>
                                <option value="over_5m" @selected(request('amount_filter') === 'over_5m')>Trên 5M</option>
                                <option value="custom" @selected(request('amount_filter') === 'custom')>Tùy chọn</option>
                            </select>
                        </div>

                        <!-- Sắp xếp -->
                        <div class="col-md-3">
                            <label class="form-label"><i class="fas fa-sort"></i> Sắp xếp</label>
                            <select name="sort_by" class="form-select">
                                <option value="newest" @selected(request('sort_by') === 'newest')>Mới nhất</option>
                                <option value="oldest" @selected(request('sort_by') === 'oldest')>Cũ nhất</option>
                                <option value="highest_value" @selected(request('sort_by') === 'highest_value')>Giá trị cao nhất</option>
                                <option value="lowest_value" @selected(request('sort_by') === 'lowest_value')>Giá trị thấp nhất</option>
                                <option value="status_priority" @selected(request('sort_by') === 'status_priority')>Ưu tiên xử lý</option>
                            </select>
                        </div>
                    </div>

                    <!-- Phần tùy chọn thời gian -->
                    <div id="customDateRange" class="row g-3 mt-2 @if(request('date_filter') !== 'custom') d-none @endif">
                        <div class="col-md-3">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <!-- Phần tùy chọn giá trị -->
                    <div id="customAmountRange" class="row g-3 mt-2 @if(request('amount_filter') !== 'custom') d-none @endif">
                        <div class="col-md-3">
                            <label class="form-label">Từ (VNĐ)</label>
                            <input type="number" name="min_amount" class="form-control" value="{{ request('min_amount') }}" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Đến (VNĐ)</label>
                            <input type="number" name="max_amount" class="form-control" value="{{ request('max_amount') }}" min="0">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> Áp dụng bộ lọc
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i> Đặt lại
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Bảng đơn hàng -->
        <table class="table">
            <thead>
                <tr>
                    <th>Mã đơn hàng</th>
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
                    <td>{{ $order->order_code }}</td>
                    <td>{{ $order->name ?? 'N/A' }}</td>
                    <td>{{ $order->phone ?? '---' }}</td>
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
                        @if(
                            $order->payment_status == 'paid' ||
                            ($order->payment_method == 'vnpay' && $order->status != 'cancelled') ||
                            ($order->payment_method == 'cod' && $order->status == 'delivered')
                        )
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
                                <span class="badge bg-warning">Chờ xác nhận</span>
                                @break
                            @case('confirmed')
                                <span class="badge bg-info">Đã xác nhận</span>
                                @break
                            @case('shipping')
                                <span class="badge bg-primary">Đang giao</span>
                                @break
                            @case('delivered')
                                <span class="badge bg-success">Hoàn tất</span>
                                {{-- ✅ THÊM NHÃN PHỤ VỚI LINK CHO ĐƠN HÀNG ĐÃ GIAO --}}
                                @if($order->return_badge_text)
                                    <br>
                                    @php
                                        // Lấy yêu cầu trả hàng đầu tiên (hoặc mới nhất) để xem chi tiết
                                        $firstReturnRequest = $order->returnRequests()->whereIn('status', ['pending', 'approved', 'refunded', 'exchanged'])->latest()->first();
                                    @endphp
                                    @if($firstReturnRequest)
                                        <a href="{{ route('admin.returns.show', $firstReturnRequest->id) }}" 
                                           class="badge bg-secondary mt-1 text-decoration-none" 
                                           title="Xem chi tiết yêu cầu trả hàng">
                                            {{ $order->return_badge_text }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary mt-1">{{ $order->return_badge_text }}</span>
                                    @endif
                                @endif
                                @break
                            @case('cancel_requested')
                                <span class="badge bg-secondary">Yêu cầu hủy</span>
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
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">Chi tiết</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Phân trang -->
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Hiển thị {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} 
                trong tổng số {{ $orders->total() }} đơn hàng
            </div>
            {{ $orders->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hiển thị/ẩn phần tùy chọn thời gian
    document.getElementById('dateFilter').addEventListener('change', function() {
        const customRange = document.getElementById('customDateRange');
        if (this.value === 'custom') {
            customRange.classList.remove('d-none');
        } else {
            customRange.classList.add('d-none');
        }
    });

    // Hiển thị/ẩn phần tùy chọn giá trị
    document.getElementById('amountFilter').addEventListener('change', function() {
        const customRange = document.getElementById('customAmountRange');
        if (this.value === 'custom') {
            customRange.classList.remove('d-none');
        } else {
            customRange.classList.add('d-none');
        }
    });
});
</script>
@endpush
@endsection
