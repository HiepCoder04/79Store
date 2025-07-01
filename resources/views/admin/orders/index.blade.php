{{-- resources/views/admin/page/order/index.blade.php --}}
@extends('admin.layouts.dashboard')
@section('title', 'Danh sách đơn hàng')

@section('content')
<div class="container">
    <h2>Danh sách đơn hàng</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Khách hàng</th>
                <th>SĐT</th>
                <th>Ngày đặt</th>
                <th>Trạng thái</th>
                <th>Tổng tiền</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
                <td>{{ $order->phone }}</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ number_format($order->total_after_discount, 0, ',', '.') }} đ</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">Chi tiết</a>
                    <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa đơn hàng này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
