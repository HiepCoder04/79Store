@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Cập nhật đơn hàng #{{ $order->id }}</h2>

    <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Trạng thái đơn hàng</label>
            <select name="status" class="form-control" required>
                @foreach(['pending', 'confirmed', 'shipping', 'delivered', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Phương thức thanh toán</label>
            <input name="payment_method" class="form-control" value="{{ $order->payment_method }}">
        </div>

        <div class="mb-3">
            <label>Phương thức giao hàng</label>
            <input name="shipping_method" class="form-control" value="{{ $order->shipping_method }}">
        </div>

        <button type="submit" class="btn btn-success">Lưu thay đổi</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
