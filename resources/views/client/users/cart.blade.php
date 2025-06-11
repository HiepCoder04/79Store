@extends('client.layouts.default')

@section('title', 'Giỏ hàng')

@section('content')
<section class="about-us-area section-padding-100-0">
    <div class="container">
        <!-- Top Breadcrumb Area -->
        <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center" style="background-image: url({{ asset('img/bg-img/24.jpg') }});">
            <h2>Giỏ hàng</h2>
        </div>

        <!-- Breadcrumb -->
        <div class="row mt-3">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home')}}"><i class="fa fa-home"></i> Trang chủ</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Cart Area Start -->
        <div class="cart-area section-padding-0-100 clearfix">
            <div class="container">
                @if ($items->isEmpty())
                    <p class="text-center mt-5">Giỏ hàng của bạn đang trống.</p>
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="cart-table clearfix">
                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Số lượng</th>
                                            <th>Giá</th>
                                            <th>Thành tiền</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            @php
                                                $product = $item->productVariant->product;
                                                $variant = $item->productVariant;
                                                $gallery = $product->galleries->first(); // nếu có quan hệ
                                            @endphp
                                            <tr>
                                                <td class="cart_product_img">
                                                    <a href="#">
                                                        <img src="{{ asset('storage/' . ($gallery->image ?? 'img/default.jpg')) }}" alt="{{ $product->name }}" width="80">
                                                    </a>
                                                    <h5>{{ $product->name }}</h5>
                                                    <small>Loại: {{ $variant->size }} - {{ $variant->pot }}</small>
                                                </td>
                                                <td class="qty">
                                                    <form method="POST" action="{{ route('cart.update') }}">
                                                        @csrf
                                                        <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                                                        <div class="quantity">
                                                            <input type="number" class="qty-text" name="quantity" value="{{ $item->quantity }}" min="1">
                                                            <button type="submit" class="btn btn-sm btn-success mt-2">Cập nhật</button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td class="price">
                                                    <span>{{ number_format($variant->price, 0, ',', '.') }}đ</span>
                                                </td>
                                                <td class="total_price">
                                                    <span>{{ number_format($variant->price * $item->quantity, 0, ',', '.') }}đ</span>
                                                </td>
                                                <td class="action">
                                                    <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"><i class="icon_close"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Coupon & Total -->
                    <div class="row">
                        <!-- Coupon -->
                        <div class="col-12 col-lg-6">
                            <div class="coupon-discount mt-70">
                                <h5>NHẬP MÃ GIẢM GIÁ</h5>
                                <form action="#" method="post">
                                    <input type="text" name="coupon-code" placeholder="Nhập mã khuyến mãi">
                                    <button type="submit">ÁP DỤNG</button>
                                </form>
                            </div>
                        </div>

                        <!-- Tổng tiền -->
                        <div class="col-12 col-lg-6">
                            <div class="cart-totals-area mt-70">
                                <h5>Tổng giỏ hàng</h5>
                                <div class="subtotal d-flex justify-content-between">
                                    <h5>Tạm tính</h5>
                                    <h5>{{ number_format($total, 0, ',', '.') }}đ</h5>
                                </div>
                                <div class="shipping d-flex justify-content-between">
                                    <h5>Phí vận chuyển</h5>
                                    <h5>Miễn phí</h5>
                                </div>
                                <div class="total d-flex justify-content-between">
                                    <h5>Tổng cộng</h5>
                                    <h5>{{ number_format($total, 0, ',', '.') }}đ</h5>
                                </div>
                                <div class="checkout-btn mt-3">
                                    <a href="#" class="btn alazea-btn w-100">TIẾN HÀNH THANH TOÁN</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
