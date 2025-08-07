{{-- resources/views/admin/page/order/show.blade.php --}}
@extends('admin.layouts.dashboard')
@section('title', 'Chi tiết đơn hàng')

<style>
.order-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.order-header h2 {
    margin: 0;
    font-weight: 600;
    font-size: 1.8rem;
}

.order-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    margin-top: 8px;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-confirmed { background: #dbeafe; color: #1e40af; }
.status-shipping { background: #fde68a; color: #d97706; }
.status-delivered { background: #d1fae5; color: #065f46; }
.status-cancelled { background: #fee2e2; color: #dc2626; }
.status-returned { background: #e5e7eb; color: #374151; }

.info-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    transition: all 0.3s ease;
}

.info-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.info-card-header {
    background: #f8fafc;
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-card-header i {
    color: #3b82f6;
    font-size: 1.2rem;
}

.info-card-header h5 {
    margin: 0;
    font-weight: 600;
    color: #1f2937;
    font-size: 1.1rem;
}

.info-card-body {
    padding: 20px;
}

.info-item {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
    align-items: start;
}

.info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.info-label {
    font-weight: 500;
    color: #6b7280;
    font-size: 0.9rem;
}

.info-value {
    color: #111827;
    font-weight: 400;
    word-break: break-word;
}

.status-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
    margin-bottom: 20px;
}

.status-form .form-select {
    width: 180px;
    height: 42px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    padding: 8px 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.status-form .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.products-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    margin-bottom: 20px;
}

.products-header {
    background: #f8fafc;
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
}

.products-header h4 {
    margin: 0;
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 8px;
}

.table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.table thead th {
    background: #f8fafc;
    color: #374151;
    font-weight: 600;
    padding: 16px 20px;
    border: none;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table tbody td {
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
    color: #111827;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: #f9fafb;
}

.table tbody tr:last-child td {
    border-bottom: none;
}

.total-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
    margin-bottom: 20px;
}

.total-card {
    padding: 24px;
    max-width: 400px;
    margin-left: auto;
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.total-row:last-child {
    border-bottom: 2px solid #e5e7eb;
    padding-top: 16px;
    margin-top: 8px;
    font-weight: 600;
    font-size: 1.1rem;
}

.total-row.no-border {
    border-bottom: none;
}

.total-label {
    color: #6b7280;
    font-weight: 500;
}

.total-value {
    color: #111827;
    font-weight: 600;
}

.action-section {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-back {
    background: #6b7280;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
}

.btn-back:hover {
    background: #4b5563;
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.alert {
    border-radius: 8px;
    border: none;
    padding: 16px 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.general-info-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .general-info-container {
        grid-template-columns: 1fr;
    }
    
    .info-item {
        grid-template-columns: 1fr;
        gap: 6px;
    }
    
    .total-card {
        max-width: 100%;
    }
}
</style>

@if (session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
</div>
@endif

@section('content')
<div class="order-header">
    <h2><i class="fas fa-receipt me-2"></i>Chi tiết đơn hàng #{{ $order->id }}</h2>
    <span class="order-badge status-{{ $order->status }}">
        @switch($order->status)
            @case('pending') Chờ xác nhận @break
            @case('confirmed') Đã xác nhận @break
            @case('shipping') Đang giao @break
            @case('delivered') Hoàn tất @break
            @case('cancelled') Đã huỷ @break
            @case('returned') Trả hàng @break
        @endswitch
    </span>
</div>

<div class="general-info-container">
    {{-- Thông tin người đặt hàng --}}
    <div class="info-card">
        <div class="info-card-header">
            <i class="fas fa-user"></i>
            <h5>Thông tin người đặt hàng</h5>
        </div>
        <div class="info-card-body">
            <div class="info-item">
                <span class="info-label">Tên tài khoản:</span>
                <span class="info-value">{{ $order->user->name ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $order->user->email ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Số điện thoại:</span>
                <span class="info-value">{{ $order->user->phone ?? '---' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Địa chỉ mặc định:</span>
                <span class="info-value">{{ optional($order->user->addresses->first())->full_address ?? '---' }}</span>
            </div>
        </div>
    </div>

    {{-- Thông tin người nhận hàng --}}
    <div class="info-card">
        <div class="info-card-header">
            <i class="fas fa-shipping-fast"></i>
            <h5>Thông tin người nhận hàng</h5>
        </div>
        <div class="info-card-body">
            <div class="info-item">
                <span class="info-label">Tên người nhận:</span>
                <span class="info-value">{{ $order->name ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Số điện thoại:</span>
                <span class="info-value">{{ $order->phone ?? '---' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Địa chỉ nhận hàng:</span>
                <span class="info-value">{{ optional($order->address)->full_address ?? '---' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Ghi chú:</span>
                <span class="info-value">{{ $order->note ?? '---' }}</span>
            </div>
        </div>
    </div>
</div>

<div class="status-section">
    <div class="d-flex align-items-center gap-3 status-form">
        <strong><i class="fas fa-tasks me-2"></i>Cập nhật trạng thái:</strong>
        <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
            @csrf
            @method('PUT')
            <select name="status" onchange="this.form.submit()" class="form-select">
                @foreach ($availableStatuses as $value => $label)
                    <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>


<div class="products-section">
    <div class="products-header">
        <h4><i class="fas fa-boxes me-2"></i>Sản phẩm đã đặt</h4>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Chiều cao</th>
                <th>Loại chậu</th>
                <th>Giá cây</th>
                <th>Giá chậu</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderDetails as $item)
            <tr>
                <td>{{ $item->product_name ?? '---' }}</td>
                <td>{{ $item->product_height ?? '---' }} cm</td>
                <td>{{ $item->product_pot ?? '---' }}</td>
                <td>{{ number_format($item->product_price ?? 0, 0, ',', '.') }} đ</td>
                <td>{{ number_format($item->pot_price ?? 0, 0, ',', '.') }} đ</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="total-section">
    <div class="total-card">
        <div class="total-row">
            <span class="total-label">Tổng trước giảm:</span>
            <span class="total-value">{{ number_format($totalBeforeDiscount, 0, ',', '.') }} đ</span>
        </div>
        <div class="total-row">
            <span class="total-label">Giảm giá:</span>
            <span class="total-value">{{ number_format($discount, 0, ',', '.') }} đ</span>
        </div>
        <div class="total-row">
            <span class="total-label">Tổng thanh toán:</span>
            <span class="total-value">{{ number_format($total, 0, ',', '.') }} đ</span>
        </div>
    </div>
</div>

<div class="action-section">
    <a href="{{ route('admin.orders.index') }}" class="btn-back">
        <i class="fas fa-arrow-left me-2"></i>Quay lại
    </a>
</div>
@endsection