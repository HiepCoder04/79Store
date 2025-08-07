@extends('admin.layouts.dashboard')

@section('title', 'Dashboard - Thống kê')

@section('content')
<div class="container-fluid py-4">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <body class="bg-light p-4">

        <div class="container">

            <h4 class="mb-4">Thống kê đơn hàng</h4>

            <!-- Tổng quan -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Doanh thu</h5>
                            <p class="card-text">{{ number_format($doanhThu) ?? 0}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Đơn hàng chờ xử lý</h5>
                            <p class="card-text">{{ number_format($donHangChoXuLy ?? 0) }}</p>

                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Đơn hàng đã giao</h5>
                            <p class="card-text">{{ number_format($donHangDaGiao ?? 0) }}</p>

                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Đơn hàng đã hủy</h5>
                            <p class="card-text">{{ number_format($donHangDaHuy ?? 0) }}</p>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="crChart"></canvas>
                </div>
            </div>

            <!-- Top sản phẩm -->


        </div>
        <script>
        // Truyền dữ liệu doanh thu từ PHP sang JS (dạng array of objects: {date, total})
        const doanhThu = @json($doanhThus);

        // Tách nhãn ngày và giá trị doanh thu từ dữ liệu
        const labels = doanhThu.map(item => item.date);
        const data = doanhThu.map(item => item.total);

        // Tính tổng doanh thu
        const tongDoanhThu = data.reduce((sum, val) => sum + val, 0);

        // Hiển thị tổng doanh thu ra HTML
        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("doanhThu").innerText = tongDoanhThu.toLocaleString('vi-VN') + " đ";
        });

        // Vẽ biểu đồ dạng đường (Line Chart) doanh thu theo ngày
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu theo ngày',
                    data: data,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0, 0, 255, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Doanh thu: ' + context.parsed.y.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });
        console.log("Doanh thu:", doanhThu);
        </script>
        <script>
        // Truyền dữ liệu từ PHP sang JS
        const donHangTheoNgay = @json($soDonHangTheoNgay); // [{date: '2025-08-01', total: 5}, ...]

        // Tách ngày và tổng số đơn hàng
        const labelsDonHang = donHangTheoNgay.map(item => item.date);
        const dataDonHang = donHangTheoNgay.map(item => item.total);

        // Debug log để kiểm tra dữ liệu truyền sang
        console.log("Labels:", labelsDonHang);
        console.log("Data:", dataDonHang);

        // Vẽ biểu đồ cột số lượng đơn hàng theo ngày
        new Chart(document.getElementById('crChart'), {
            type: 'bar',
            data: {
                labels: labelsDonHang,
                datasets: [{
                    label: 'Số lượng đơn hàng theo ngày',
                    data: dataDonHang,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)' // màu xanh dương
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0 // Không có số thập phân
                        }
                    }
                }
            }
        });
        </script>




    </body>

    </html>



</div>
@endsection