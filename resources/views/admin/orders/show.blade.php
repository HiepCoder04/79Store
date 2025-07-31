{{-- resources/views/admin/page/order/show.blade.php --}}
@extends('admin.layouts.dashboard')
@section('title', 'Chi tiết đơn hàng')

<style>
.table tr th {
    padding-left: 0 !important;
    padding-right: 0 !important;
    color: #333;
}

.table thead tr th:last-child,
.table tbody tr td:last-child {
    min-width: 130px;
    width: 130px;
}

.total-container {
    background-color: #efefef;
    border-radius: 8px;
    color: #333;
}

.general-info-container {
    background-color: #efefef;
    border-radius: 8px;
    padding: 16px;
    justify-content: space-between;
    display: flex;
    align-items: stretch;
    /* Đảm bảo các item và line đều kéo dài theo chiều cao lớn nhất */
    color: #333;
}

.general-info-item {
    min-width: 25%;
}

.vertical-line {
    width: 1px;
    background-color: #d3d3d3;
    align-self: stretch;
    height: auto;
}

.status-form {
    color: #333;
}

.status-form .value form {
    height: max-content;
    width: auto;
    margin: 0;
}

.status-form .value form .form-select {
    width: 160px;
    height: 36px;
    box-sizing: border-box;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 8px;
}
</style>

@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@section('content')
<div class="card">
    <div class="card-body">
        <h2>Chi tiết đơn hàng #{{ $order->id }}</h2>
        <div class="general-info-container mb-3">
            <div class="general-info-item">
                <strong>Khách hàng: </strong>
                <div class="value">{{ $order->user->name ?? 'N/A' }}</div>
            </div>
            <div class="vertical-line"></div>
            <div class="general-info-item">
                <strong>SĐT: </strong>
                <div class="value">{{ $order->user->phone ?? '---' }}</div>
            </div>
            <div class="vertical-line"></div>
            <div class="general-info-item">
                <strong>Địa chỉ: </strong>
                <div class="value">{{ optional($order->user->addresses->first())->full_address ?? '---' }}</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12 d-flex align-items-center gap-3 status-form">
                <strong>Trạng thái: </strong>
                <div class="value">
                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="">
                        @csrf
                        @method('PUT')
                        <select name="status" onchange="this.form.submit()" class="form-select">
                            @foreach ([
                            'pending' => 'Chờ xử lý',
                            'confirmed' => 'Đang xử lý',
                            'shipping' => 'Đang giao',
                            'delivered' => 'Hoàn tất',
                            'cancelled' => 'Đã huỷ',
                            'returned' => 'Trả hàng'
                            ] as $value => $label)
                            <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <h4>Sản phẩm</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Tên SP</th>
                    <th>Biến thể</th>
                    <th>Số lượng</th>
                    <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails as $item)
                <tr>
                    <td>{{ $item->product_name ?? '---' }}</td>
                    <td>{{ $item->variant_name ?? '---' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row mb-3">
            <div class="col-12 d-flex justify-content-end">
                <div class="w-50 total-container p-3 gap-3 d-flex flex-column">
                    <div class="d-flex align-items-center justify-content-between">
                        <strong>Tổng trước giảm:</strong>
                        <div class="value">{{ number_format($totalBeforeDiscount, 0, ',', '.') }} đ</div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <strong>Giảm giá:</strong>
                        <div class="value">{{ number_format($discount, 0, ',', '.') }} đ</div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <strong>Tổng thanh toán:</strong>
                        <div class="value fw-bold">{{ number_format($total, 0, ',', '.') }} đ</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-dark ms-auto me-0">Quay lại</a>
            </div>
        </div>
    </div>
</div>
@endsection