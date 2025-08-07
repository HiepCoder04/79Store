<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @extends('layouts.app') {{-- hoặc admin layout --}}

@section('content')
<div class="container mt-5">
    <h3>Thống kê đơn hàng</h3>

    {{-- Form lọc --}}
    <form action="{{ route('admin.thongke') }}" method="GET" class="row g-3 my-4">
        <div class="col-md-4">
            <label for="start_date" class="form-label">Từ ngày</label>
            <input type="date" name="start_date" value="{{ $start }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label">Đến ngày</label>
            <input type="date" name="end_date" value="{{ $end }}" class="form-control">
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>
    </form>

    {{-- Thống kê --}}
    <div class="mb-4">
        <strong>Tổng đơn:</strong> {{ $totalOrders }} <br>
        <strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 0, ',', '.') }} VND
    </div>

    {{-- Bảng đơn hàng --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người đặt</th>
                <th>Ngày tạo</th>
                <th>Trạng thái</th>
                <th>Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name ?? '---' }}</td>
                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ number_format($order->total, 0, ',', '.') }} VND</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

    
</body>
</html> -->