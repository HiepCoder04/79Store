@extends('client.layouts.default')

@section('title', 'Giỏ hàng')

@php use Illuminate\Support\Str; @endphp

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb-area">
    <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
         style="background-image: url({{ asset('assets/img/bg-img/24.jpg') }});">
        <h2>Giỏ hàng</h2>
    </div>

    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Cart Section -->
<div class="cart-area section-padding-0-100 clearfix">
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($items->isEmpty())
            <div class="alert alert-info text-center">Giỏ hàng của bạn đang trống.</div>
        @else
        <div class="row">
            <div class="col-12">
                <div class="table-responsive shadow-sm rounded-3">
                    <table class="table table-hover table-bordered align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Chậu</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Thành tiền</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                @php
                                    $variant = $item->productVariant;
                                    $product = $variant->product;
                                    $gallery = $product->galleries->first();
                                    $image = $gallery->image ?? null;
                                    $imageUrl = $image
                                        ? (Str::startsWith($image, 'http') ? $image : asset(ltrim($image, '/')))
                                        : asset('assets/img/bg-img/default.jpg');
                                    $subtotal = $variant->price * $item->quantity;
                                @endphp
                                <tr>
                                    <td class="text-start">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" width="80" class="img-thumbnail">
                                            <strong>{{ $product->name }}</strong>
                                        </div>
                                    </td>
                                    <td>{{ $variant->pot }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('cart.update', $item->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}"
                                                   min="1" class="form-control text-center" style="width: 80px;" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td>{{ number_format($variant->price, 0, ',', '.') }}đ</td>
                                    <td>{{ number_format($subtotal, 0, ',', '.') }}đ</td>
                                    <td>
                                        <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Xoá">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Coupon & Totals -->
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="coupon-discount mt-70">
                    <h5>Mã giảm giá</h5>
                    <form action="" method="post">
                        @csrf
                        <input type="text" name="coupon-code" placeholder="Nhập mã giảm giá">
                        <button type="submit">Áp dụng</button>
                    </form>
                    @if(session('coupon_error'))
                        <div class="text-danger mt-2">{{ session('coupon_error') }}</div>
                    @endif
                    @if(session('coupon_success'))
                        <div class="text-success mt-2">{{ session('coupon_success') }}</div>
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="cart-totals-area mt-70">
                    <h5 class="title--">Tổng đơn hàng</h5>

                    <div class="subtotal d-flex justify-content-between">
                        <h5>Tạm tính</h5>
                        <h5>{{ number_format($total, 0, ',', '.') }}đ</h5>
                    </div>

                    <div class="shipping d-flex justify-content-between">
                        <h5>Phí vận chuyển</h5>
                        <h5 class="text-success">Miễn phí</h5>
                    </div>

                    <div class="total d-flex justify-content-between mt-3">
                        <h5>Tổng cộng</h5>
                        <h5>{{ number_format($total, 0, ',', '.') }}đ</h5>
                    </div>

                    <div class="checkout-btn mt-3">
                        <button class="btn alazea-btn w-100" >Thanh Toán</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection