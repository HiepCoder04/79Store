@extends('client.layouts.default')

@section('title', 'Hoàn thành đơn hàng!')
@php use Illuminate\Support\Str; use Carbon\Carbon; @endphp

@section('content')
<section class="breadcrumb-area">
    <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
         style="background-image: url('{{ asset('assets/img/bg-img/24.jpg') }}'); height: 250px;">
        <h2 class="text-white">Đơn hàng của tôi</h2>
    </div>
</section>
<style>
    .order-success {
        max-width: 600px;
        margin: auto;
        padding: 2rem;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
        text-align: center;
        position: relative;
        z-index: 10;
    }

    .order-success img.product-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: .5rem;
        border: 1px solid #ccc;
        margin: 0 10px;
    }

    .confetti-canvas {
        position: fixed;
        top: 0; left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 0;
        pointer-events: none;
    }

    .order-info p {
        margin: 0.4rem 0;
        font-size: 1rem;
    }
</style>

<canvas id="confetti-canvas" class="confetti-canvas"></canvas>

<div class="container py-5">
    <div class="order-success bg-white">
        <h1 class="text-success mb-2" style="font-weight: 800;">🎉 Hoàn thành!</h1>
        <h4 class="text-muted">Cảm ơn bạn! <span class="text-dark">Đơn hàng của bạn đã được thanh toán thành công.</span></h4>

        {{-- Hiển thị sản phẩm (giả lập 2 ảnh nếu không có) --}}
        <div class="d-flex justify-content-center my-4">
            @php
                $items = session('order_items', []);
                // Lấy thông tin từ session thay vì request
                $orderId = session('order_id') ?? 'N/A';
                $orderTotal = session('order_total') ?? 0;
                $vnpayInfo = session('vnpay_transaction_info', []);
            @endphp

            @forelse ($items as $img)
                <img src="{{ asset($img) }}"
                     onerror="this.onerror=null;this.src='{{ asset('assets/img/default.jpg') }}';"
                     class="product-img border rounded shadow"
                     style="width: 120px; height: 120px; object-fit: cover;">
            @empty
                <img src="{{ asset('assets/img/bg-img/default.jpg') }}" class="product-img border rounded shadow" style="width: 120px; height: 120px; object-fit: cover;">
            @endforelse
        </div>

        {{-- Thông tin đơn hàng --}}
        <div class="order-info text-start bg-light p-4 rounded shadow-sm">
            <p><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</p>
            <p><strong>Ngày:</strong> {{ Carbon::now()->translatedFormat('l, d F Y') }}</p>
            <p><strong>Tổng cộng:</strong> <span class="text-success fw-bold">{{ number_format($order->total_after_discount, 0, ',', '.') }}đ</span></p>
            <p><strong>Phương thức thanh toán:</strong> <span class="text-primary">VNPAY</span></p>
            <p><strong>Trạng thái thanh toán:</strong> <span class="text-success">Đã thanh toán</span></p>
            
            @php
                $vnpayInfo = session('vnpay_transaction_info', []);
            @endphp
            @if(!empty($vnpayInfo))
                @if(isset($vnpayInfo['txn_ref']) && $vnpayInfo['txn_ref'])
                    <p><strong>Mã tham chiếu:</strong> <span class="text-muted">{{ $vnpayInfo['txn_ref'] }}</span></p>
                @endif
                @if(isset($vnpayInfo['transaction_no']) && $vnpayInfo['transaction_no'])
                    <p><strong>Mã giao dịch VNPay:</strong> <span class="text-muted">{{ $vnpayInfo['transaction_no'] }}</span></p>
                @endif
            @endif
        </div>

        {{-- Nút điều hướng --}}
        <div class="mt-4 d-flex justify-content-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-success px-4">🏠 Tiếp tục mua sắm</a>
            <a href="{{ route('client.orders.index') }}" class="btn btn-outline-dark px-4">📦 Xem đơn hàng</a>
        </div>
    </div>
</div>

{{-- Confetti script --}}
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    const canvas = document.getElementById('confetti-canvas');
    const myConfetti = confetti.create(canvas, { resize: true });
    const duration = 3000;
    const end = Date.now() + duration;

    (function frame() {
        myConfetti({ particleCount: 4, angle: 60, spread: 55, origin: { x: 0 } });
        myConfetti({ particleCount: 4, angle: 120, spread: 55, origin: { x: 1 } });
        if (Date.now() < end) requestAnimationFrame(frame);
    })();
</script>
@endsection

