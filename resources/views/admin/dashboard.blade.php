@extends('admin.layouts.dashboard')

@section('title', 'Dashboard - Thống kê')

@section('content')
<div class="container-fluid py-4">

    <h4 class="mb-4">Thống kê đơn hàng</h4>

    {{-- Bộ lọc ngày --}}
    <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Từ ngày</label>
            <input type="date" name="start_date" class="form-control" value="{{ $start }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Đến ngày</label>
            <input type="date" name="end_date" class="form-control" value="{{ $end }}">
        </div>
        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
    </form>

    {{-- Thẻ thống kê --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-gradient-primary text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Tổng đơn hàng</h5>
                    <h3>{{ $totalOrders }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-gradient-success text-white shadow">
                <div class="card-body">
                    <h5 class="card-title">Tổng doanh thu</h5>
                    <h3>{{ number_format($totalRevenue, 0, ',', '.') }}₫</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Bảng đơn hàng
    <div class="card">
        <div class="card-header"><strong>Danh sách đơn hàng</strong></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>Điện thoại</th>
                        <th>Tổng tiền</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user->name ?? 'N/A' }}</td>
                            <td>{{ $order->phone ?? '-' }}</td>
                            <td>{{ number_format($order->total, 0, ',', '.') }}₫</td>
                            <td>{{ $order->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Không có đơn hàng nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div> --}}

</div>
@endsection
