{{-- resources/views/admin/page/order/index.blade.php --}}
@extends('admin.layouts.dashboard')
@section('title', 'Danh sách đơn hàng')

@section('content')

<style>
.table tr th {
    padding-left: 0;
    padding-right: 0;
    color: #333;
}

.table thead tr th:last-child,
.table tbody tr td:last-child {
    min-width: 130px;
    width: 130px;
}
</style>

<div class="card">
    <div class="card-body">
        <h2 class="mb-3">Danh sách đơn hàng</h2>
        <table class="table">
            <thead>
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
                    <td>{{ $order->user->phone ?? '---' }}</td>
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
                        @if($order->payment_status == 'paid' || 
                            ($order->payment_method == 'vnpay' && $order->status != 'cancelled') ||
                            ($order->payment_method == 'cod' && $order->status == 'delivered'))
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
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">Chi tiết</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection