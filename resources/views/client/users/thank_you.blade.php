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
        <h4 class="text-muted">Cảm ơn bạn! <span class="text-dark">Đơn hàng của bạn sẽ được chuẩn bị.</span></h4>

        {{-- Hiển thị sản phẩm (giả lập 2 ảnh nếu không có) --}}
        <div class="d-flex justify-content-center my-4">
            @php
        $items = session('order_items', []);
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
            <p><strong>Mã đơn hàng:</strong> <span class="text-dark">ORD-{{ session('order_id') }}</span></p>
            <p><strong>Ngày:</strong> {{ Carbon::now()->translatedFormat('l, d F Y') }}</p>
            <p><strong>Tổng cộng:</strong> {{ number_format(session('order_total') ?? 0, 0, ',', '.') }}đ</p>
        </div>

        {{-- Nút điều hướng --}}
        <div class="mt-4 d-flex justify-content-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-success px-4">🏠 Tiếp tục mua sắm</a>
            <a href="{{ route('client.orders.index') }}" class="btn btn-outline-dark px-4">📦 Xem đơn hàng</a>
        </div>
    </div>
</div>

{{-- Confetti --}}
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
