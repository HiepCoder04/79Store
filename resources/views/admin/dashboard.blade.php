@extends('admin.layouts.dashboard')

@section('title', 'Dashboard - Thống kê')

@section('content')
    <div class="container mt-4">

        {{-- Bộ lọc đơn hàng --}}
        <h2 class="mb-4">📊 Thống kê đơn hàng</h2>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-2 mb-4">
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Tất cả trạng thái --</option>
                    @foreach ($statusLabels as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">Lọc dữ liệu</button>
            </div>
        </form>

        {{-- Thẻ thống kê tổng quan --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Doanh thu</h5>
                        <p class="card-text">{{ number_format($doanhThu ?? 0) }} đ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng chờ xử lý</h5>
                        <p class="card-text">{{ number_format($donHangChoXuLy ?? 0) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng đã giao</h5>
                        <p class="card-text">{{ number_format($donHangDaGiao ?? 0) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng đã hủy</h5>
                        <p class="card-text">{{ number_format($donHangDaHuy ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biểu đồ --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <canvas id="revenueChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="orderChart"></canvas>
            </div>
        </div>

        
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Doanh thu theo ngày
        const doanhThuData = @json($doanhThus);
        const revenueLabels = doanhThuData.map(item => item.date);
        const revenueValues = doanhThuData.map(item => item.total);

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: revenueLabels,
                datasets: [{
                    label: 'Doanh thu theo ngày',
                    data: revenueValues,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0, 0, 255, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Số lượng đơn hàng theo ngày
        const donHangData = @json($soDonHangTheoNgay);
        const orderLabels = donHangData.map(item => item.date);
        const orderValues = donHangData.map(item => item.total);

        new Chart(document.getElementById('orderChart'), {
            type: 'bar',
            data: {
                labels: orderLabels,
                datasets: [{
                    label: 'Số lượng đơn hàng theo ngày',
                    data: orderValues,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
@endsection