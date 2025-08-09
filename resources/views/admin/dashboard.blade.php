@extends('admin.layouts.dashboard')

@section('content')
<div class="container py-4">

    <h2 class="mb-4">
        📊 Thống kê đơn hàng
    </h2>

    {{-- Form lọc theo ngày --}}
    <form method="GET" class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Từ ngày</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">Đến ngày</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-danger w-100">
                Lọc dữ liệu
            </button>
        </div>
    </form>

    {{-- Thống kê tổng quan --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Doanh thu</h5>
                    <p class="card-text fs-5 fw-bold">{{ number_format($doanhThu, 0, ',', '.') }} đ</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text- bg-pink">
                <div class="card-body">
                    <h5 class="card-title">Đơn chờ xử lý</h5>
                    <p class="card-text fs-5 fw-bold">{{ $donHangChoXuLy }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Đơn đã giao</h5>
                    <p class="card-text fs-5 fw-bold">{{ $donHangDaGiao }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Đơn đã hủy</h5>
                    <p class="card-text fs-5 fw-bold">{{ $donHangDaHuy }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white" style="background-color:#6f42c1;">
                <div class="card-body">
                    <h5 class="card-title">Đơn đã trả</h5>
                    <p class="card-text fs-5 fw-bold">{{ $donHangDaTra }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Biểu đồ --}}
    <div class="row">
        <div class="col-md-6">
            <canvas id="chartDoanhThu"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="chartSoDonHang"></canvas>
        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const doanhThuData = @json($doanhThus);
    const soDonHangData = @json($soDonHangTheoNgay);

    const ctx1 = document.getElementById('chartDoanhThu').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: doanhThuData.map(item => item.date),
            datasets: [{
                label: 'Doanh thu theo ngày',
                data: doanhThuData.map(item => item.total),
                borderColor: 'blue',
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: value => value.toLocaleString() + ' đ' }
                }
            }
        }
    });

    const ctx2 = document.getElementById('chartSoDonHang').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: soDonHangData.map(item => item.date),
            datasets: [{
                label: 'Số lượng đơn hàng',
                data: soDonHangData.map(item => item.total),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
