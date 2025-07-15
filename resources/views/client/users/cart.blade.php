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
                                        @endphp
                                        <tr data-item-id="{{ $item->id }}">
                                            <td class="text-start">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" width="80" class="img-thumbnail">
                                                    <strong>{{ $product->name }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $variant->pot }}</td>
                                            <td>
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                                    class="form-control quantity-input text-center" style="width: 80px;">
                                            </td>
                                            <td class="unit-price" data-price="{{ $variant->price }}">{{ number_format($variant->price, 0, ',', '.') }}đ</td>
                                            <td class="item-subtotal">{{ number_format($variant->price * $item->quantity, 0, ',', '.') }}đ</td>
                                            <td>
                                                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="Xoá"><i class="fa fa-times"></i></button>
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
                            <form action="{{ route('apply.voucher') }}" method="post">
                                @csrf
                                <input type="text" name="voucher_code" placeholder="Nhập mã giảm giá">
                                <button type="submit" class="btn btn-primary">Áp dụng</button>
                            </form>
                            @if (session('success'))
                                <div class="alert alert-success mt-2">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="cart-totals-area mt-70">
                            <h5 class="title--">Tổng đơn hàng</h5>

                            <div class="subtotal d-flex justify-content-between">
                                <h5>Tạm tính</h5>
                                <h5 id="subtotal-value">{{ number_format($cartTotal, 0, ',', '.') }}đ</h5>
                            </div>

                            <div class="shipping d-flex justify-content-between">
                                <h5>Phí vận chuyển</h5>
                                <h5 class="text-success">Miễn phí</h5>
                            </div>

                            <div class="total d-flex justify-content-between mt-3">
                                <h5>Tổng cộng</h5>
                                <h5 id="total-value">{{ number_format($finalTotal, 0, ',', '.') }}đ</h5>
                            </div>

                            <div class="checkout-btn mt-3">
                                <a href="{{ route('checkout.index') }}" class="btn alazea-btn w-100">Đến Trang Thanh Toán</a>
                            </div>

                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', async function () {
                const row = this.closest('tr');
                const itemId = row.dataset.itemId;
                const quantity = parseInt(this.value);

                try {
                    const res = await fetch(`/cart/${itemId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ quantity })
                    });

                    if (!res.ok) throw new Error('Cập nhật thất bại');

                    const data = await res.json();
                    row.querySelector('.item-subtotal').textContent = data.itemSubtotalFormatted;
                    document.querySelector('#subtotal-value').textContent = data.cartTotalFormatted;
                    document.querySelector('#total-value').textContent = data.finalTotalFormatted;
                } catch (error) {
                    console.error(error);
                    alert('Lỗi cập nhật giỏ hàng: ' + error.message);
                }
            });
        });
    });
</script>
@endpush
