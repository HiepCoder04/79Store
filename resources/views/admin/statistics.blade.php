@extends('admin.layouts.dashboard')

@section('content')
<h2 class="text-xl font-bold mb-4">📊 Thống kê đơn hàng</h2>

<div class="mb-6">
    <p><strong>Tổng số đơn:</strong> {{ $totalOrders }}</p>
    <p><strong>Số đơn đã thanh toán:</strong> {{ $paidOrdersCount }}</p>
    <p><strong>Tổng doanh thu:</strong> {{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
</div>

<h3 class="font-semibold mb-2">Đơn hàng theo trạng thái:</h3>
<ul class="list-disc list-inside mb-6">
    @foreach ($statusCounts as $item)
        <li>{{ $item->order_status }}: {{ $item->total }} đơn</li>
    @endforeach
</ul>

<h3 class="font-semibold mb-2">Biểu đồ đơn hàng 7 ngày gần nhất</h3>
<canvas id="ordersChart" height="100"></canvas>

@php
    $labels = $ordersByDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))->toArray();
    $dataCount = $ordersByDay->pluck('total')->toArray();
    $dataAmount = $ordersByDay->pluck('total_amount')->toArray();
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ordersChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Số đơn hàng',
                    data: @json($dataCount),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                },
                {
                    label: 'Doanh thu (VNĐ)',
                    data: @json($dataAmount),
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số đơn'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Doanh thu (VNĐ)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    }
                }
            }
        }
    });
</script>
@endsection
