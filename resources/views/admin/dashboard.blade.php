@extends('admin.layouts.dashboard')

@section('content')
<style>
    .stats-card {
        border-radius: 15px;
        padding: 20px;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
        height: 100%;
    }
    .stats-card:hover {
        transform: translateY(-3px);
    }
    .stats-icon {
        font-size: 28px;
        opacity: 0.8;
        margin-bottom: 5px;
    }
    .chart-card {
        border-radius: 15px;
        padding: 20px;
        background: white;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        height: 100%;
    }
</style>

<div class="container py-4">

    <h2 class="mb-4 fw-bold">
        📊 Thống kê đơn hàng
    </h2>

    {{-- Form lọc theo ngày --}}
    <form method="GET" class="row g-3 align-items-end mb-4">
        <div class="col-md-3">
            <label for="start_date" class="form-label fw-semibold">Từ ngày</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label fw-semibold">Đến ngày</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-danger w-100 fw-bold">
                Lọc dữ liệu
            </button>
        </div>
    </form>

   {{-- Thống kê tổng quan --}}
<div class="row g-3 mb-4 justify-content-center text-center">
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stats-card" style="background: #0d6efd;">
            <div class="stats-icon"><i class="fas fa-coins"></i></div>
            <h6>Doanh thu</h6>
            <h4>{{ number_format($doanhThu, 0, ',', '.') }} đ</h4>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stats-card" style="background: #d63384;">
            <div class="stats-icon"><i class="fas fa-hourglass-half"></i></div>
            <h6>Chờ xử lý</h6>
            <h4>{{ $donHangChoXuLy }}</h4>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stats-card" style="background: #198754;">
            <div class="stats-icon"><i class="fas fa-truck"></i></div>
            <h6>Đã giao</h6>
            <h4>{{ $donHangDaGiao }}</h4>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stats-card" style="background: #dc3545;">
            <div class="stats-icon"><i class="fas fa-times-circle"></i></div>
            <h6>Đã hủy</h6>
            <h4>{{ $donHangDaHuy }}</h4>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stats-card" style="background: #6f42c1;">
            <div class="stats-icon"><i class="fas fa-undo-alt"></i></div>
            <h6>Đã trả</h6>
            <h4>{{ $donHangDaTra }}</h4>
        </div>
    </div>
</div>

    {{-- Biểu đồ --}}
    <div class="row g-4">
        <div class="col-md-6">
            <div class="chart-card">
                <h5 class="mb-3">📈 Doanh thu theo ngày</h5>
                <canvas id="chartDoanhThu"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-card">
                <h5 class="mb-3">📦 Số lượng đơn hàng theo ngày</h5>
                <canvas id="chartSoDonHang"></canvas>
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
</script>
@endsection
