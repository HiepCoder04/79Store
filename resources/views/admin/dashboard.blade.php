@extends('admin.layouts.dashboard')

@section('content')
    <style>
        .stats-card {
            border-radius: 15px;
            padding: 20px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            height: 100%;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card h5 {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stats-card p {
            font-size: 1.5rem;
            margin: 0;
            font-weight: bold;
        }

        .chart-card {
            border-radius: 15px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .bg-purple {
            background-color: #6f42c1 !important;
        }

        .bg-pink {
            background-color: #d63384 !important;
        }
    </style>

    <div class="container py-4">

        <h2 class="mb-4 fw-bold ">
            Thống kê đơn hàng
        </h2>

        {{-- Form lọc theo ngày --}}
        <form method="GET" class="row g-3 align-items-end mb-4">
            <div class="col-md-3">
                <label for="start_date" class="form-label fw-semibold">Từ ngày</label>
                <input type="date" id="start_date" name="start_date" class="form-control shadow-sm"
                    value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label fw-semibold">Đến ngày</label>
                <input type="date" id="end_date" name="end_date" class="form-control shadow-sm"
                    value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-danger w-100 fw-bold shadow-sm">
                    <i class="fas fa-filter me-1"></i> Lọc dữ liệu
                </button>
            </div>
        </form>

        {{-- Thống kê nhanh --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stats-card bg-primary">
                    <h5>Doanh thu</h5>
                    <p>{{ number_format($doanhThu, 0, ',', '.') }} đ</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card bg-info">
                    <h5>Doanh thu thực tế</h5>
                    <small>Đã trừ tiền hàng return</small>
                    <p>{{ number_format($doanhThuThucTe, 0, ',', '.') }} đ</p>
                </div>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div class="stats-card bg-pink">
                        <h5>Chờ xác nhận</h5>
                        <p>{{ $donHangChoXuLy }}</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="text-decoration-none">
                    <div class="stats-card bg-warning">
                        <h5>Đã xác nhận</h5>
                        <p>{{ $donHangDaXuLy }}</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.orders.index', ['status' => 'shipping']) }}" class="text-decoration-none">
                    <div class="stats-card bg-secondary">
                        <h5>Đang giao</h5>
                        <p>{{ $donHangDangGiao}}</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="text-decoration-none">
                    <div class="stats-card bg-success">
                        <h5>Đã giao</h5>
                        <p>{{ $donHangDaGiao }}</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="text-decoration-none">
                    <div class="stats-card bg-danger">
                        <h5>Đã hủy</h5>
                        <p>{{ $donHangDaHuy }}</p>
                    </div>
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.orders.index', ['status' => 'returned']) }}" class="text-decoration-none">
                    <div class="stats-card bg-purple">
                        <h5>Đã trả</h5>
                        <p>{{ $donHangDaTra }}</p>
                    </div>
                </a>
            </div>

        </div>

        {{-- Biểu đồ --}}
        <div class="row g-4">
            <div class="col-md-6">
                <div class="chart-card">
                    <h5 class="mb-3 fw-semibold"> Doanh thu theo ngày</h5>
                    <canvas id="chartDoanhThu"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <h5 class="mb-3 fw-semibold"> Số lượng đơn hàng theo ngày</h5>
                    <canvas id="chartSoDonHang"></canvas>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="chart-card">
                    <h5 class="mb-3 fw-semibold"> Doanh thu 7 ngày gần nhất</h5>
                    <canvas id="chartWeeklyRevenue"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <h5 class="mb-3 fw-semibold"> Top 5 sản phẩm bán chạy</h5>
                    <div class="list-group">
                        @foreach($topProductsData['labels'] as $index => $productName)
                            <div
                                class="list-group-item d-flex justify-content-between align-items-center mb-2 shadow-sm rounded">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-3 fs-6">{{ $index + 1 }}</span>
                                    <div>
                                        <h6 class="mb-0">{{ $productName }}</h6>
                                    </div>
                                </div>
                                <span class="fw-bold text-success">{{ $topProductsData['totals'][$index] }} <i
                                        class="fas fa-shopping-cart"></i></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FontAwesome --}}
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const doanhThuData = @json($doanhThus);
        const soDonHangData = @json($soDonHangTheoNgay);
        const weeklyRevenueData = @json($weeklyRevenueData);

        if (document.getElementById('chartDoanhThu') && doanhThuData?.length > 0) {
            new Chart(document.getElementById('chartDoanhThu'), {
                type: 'line',
                data: {
                    labels: doanhThuData.map(item => item.date),
                    datasets: [{
                        label: 'Doanh thu (VND)',
                        data: doanhThuData.map(item => item.total),
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { responsive: true }
            });
        }

        if (document.getElementById('chartSoDonHang') && soDonHangData?.length > 0) {
            new Chart(document.getElementById('chartSoDonHang'), {
                type: 'bar',
                data: {
                    labels: soDonHangData.map(item => item.date),
                    datasets: [{
                        label: 'Số đơn hàng',
                        data: soDonHangData.map(item => item.total),
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: { responsive: true }
            });
        }

        if (document.getElementById('chartWeeklyRevenue') && weeklyRevenueData?.labels?.length > 0) {
            new Chart(document.getElementById('chartWeeklyRevenue'), {
                type: 'line',
                data: {
                    labels: weeklyRevenueData.labels,
                    datasets: [{
                        label: 'Doanh thu (VND)',
                        data: weeklyRevenueData.totals,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { responsive: true }
            });
        }
    </script>
@endsection