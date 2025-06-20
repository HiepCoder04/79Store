@extends('client.layouts.default')

@section('title', 'Giỏ hàng')

@php use Illuminate\Support\Str; @endphp

@section('content')
<!-- ##### Breadcrumb Area Start ##### -->
<div class="breadcrumb-area">
    <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center" style="background-image: url({{ asset('assets/img/bg-img/24.jpg') }});">
        <h2>Thanh Toán</h2>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-white py-2 px-3 rounded">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Thanh Toán</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- ##### Breadcrumb Area End ##### -->

<!-- ##### Checkout Area Start ##### -->
<div class="checkout_area mb-100">
    <div class="container">
        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <div class="row g-4">
                <!-- Left: Delivery Info -->
                 <div class="col-lg-7">
                    @include('client.users.customer_info')
                </div>


                <!-- Right: Order Summary -->
                <div class="col-lg-5">
                    <div class="p-4 border rounded shadow-sm bg-white">
                         <h5 class="fw-bold mb-4">Tóm Tắt Đơn Hàng</h5>
                        @php $total = 0; @endphp
                        @foreach($cart->items as $item)
                            @php
                                $product = $item->productVariant->product;
                                $image = $product->galleries->first()->image ?? 'assets/img/bg-img/default.jpg';
                                $imageUrl = Str::startsWith($image, 'http') ? $image : asset($image);
                                $subtotal = $item->productVariant->price * $item->quantity;
                                $total += $subtotal;
                            @endphp
                            <div class="d-flex mb-3 align-items-center pb-2 border-bottom">
                                <div class="flex-shrink-0">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="rounded-2 border " width="150" height="100">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-truncate" style="max-width: 200px; margin-left: 10px">{{ $product->name }}</h6>
                                    <small style="margin-left: 10px">Chậu: {{ $item->productVariant->pot }} | SL: {{ $item->quantity }}</small>
                                </div>
                            </div>
                        @endforeach

                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Vận Chuyển</span>
                            <strong class="text-success">Miễn Phí</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tổng Phụ</span>
                            <strong>{{ number_format($total, 0, ',', '.') }}đ</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <span class="fw-bold">Tổng Cộng</span>
                            <span class="fw-bold">{{ number_format($total, 0, ',', '.') }}đ</span>
                        </div>

                        <button type="submit" class="btn btn-dark mt-4 w-100">
                             Đặt Hàng
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- ##### Checkout Area End ##### -->
@endsection