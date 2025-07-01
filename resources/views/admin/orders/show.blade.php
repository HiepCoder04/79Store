{{-- resources/views/admin/page/order/show.blade.php --}}
@extends('admin.layouts.dashboard')
@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container">
    <h2>Chi tiết đơn hàng #{{ $order->id }}</h2>
    <p><strong>Khách hàng:</strong> {{ $order->user->name ?? 'N/A' }}</p>
    <p><strong>SĐT:</strong> {{ $order->phone }}</p>
    <p><strong>Địa chỉ:</strong> {{ $order->address->full_address ?? 'N/A' }}</p>
    <p><strong>Trạng thái:</strong> {{ $order->status }}</p>

    <hr>
    <h4>Sản phẩm</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Tên SP</th>
                <th>Biến thể</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Tổng</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderDetails as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->variant_name }}</td>
                <td>{{ number_format($item->price, 0, ',', '.') }} đ</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->total_price, 0, ',', '.') }} đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Tổng trước giảm:</strong> {{ number_format($order->total_before_discount, 0, ',', '.') }} đ</p>
    <p><strong>Giảm giá:</strong> -{{ number_format($order->discount_amount, 0, ',', '.') }} đ</p>
    <p><strong>Tổng thanh toán:</strong> {{ number_format($order->total_after_discount, 0, ',', '.') }} đ</p>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại</a>
</div>
@endsection
