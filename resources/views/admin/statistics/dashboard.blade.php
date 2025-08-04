@extends('admin.layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">📊 Thống kê đơn hàng</h2>

    <form method="GET" action="{{ route('admin.statistics.dashboard') }}" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">-- Tất cả trạng thái --</option>
                @foreach (['pending', 'confirmed', 'shipping', 'delivered', 'cancelled', 'returned'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Lọc dữ liệu</button>
        </div>
    </form>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="alert alert-info">
                <strong>Tổng đơn hàng:</strong> {{ $totalOrders }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-success">
                <strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 0, ',', '.') }} VND
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->customer_name ?? 'N/A' }}</td>
                        <td>{{ number_format($order->total_after_discount, 0, ',', '.') }} VND</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Không có đơn hàng nào phù hợp.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
