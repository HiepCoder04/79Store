@extends('client.layouts.default')

@section('title', 'Giỏ hàng')

@php use Illuminate\Support\Str; @endphp

@section('content')
    <!-- ##### Breadcrumb Area Start ##### -->
    <div class="breadcrumb-area">
        <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
            style="background-image: url({{ asset('assets/img/bg-img/24.jpg') }});">
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
        <!-- Main checkout form -->
        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
            @csrf
             <input type="hidden" name="selected_ids" value="{{ request('selected') }}">
            <div class="row g-4">
                <div class="col-lg-7">
                    @include('client.users.customer_info')
                </div>

                <div class="col-lg-5">
                    <div class="p-4 border rounded shadow-sm bg-white">
                        <h5 class="fw-bold mb-4">Tóm Tắt Đơn Hàng</h5>

                        @php
                            $cartTotal = $cart->items->sum(fn($item) => $item->productVariant->price * $item->quantity);
                            $voucherId = session('applied_voucher');
                            $voucher = $voucherId ? \App\Models\Voucher::find($voucherId) : null;
                            $discount = 0;
                            if ($voucher && $voucher->is_active && now()->between($voucher->start_date, $voucher->end_date)) {
                                if ($cartTotal >= $voucher->min_order_amount) {
                                    $discount = $cartTotal * ($voucher->discount_percent / 100);
                                    if ($voucher->max_discount && $discount > $voucher->max_discount) {
                                        $discount = $voucher->max_discount;
                                    }
                                }
                            } else {
                                $voucher = null;
                                session()->forget('applied_voucher');
                            }
                            $finalTotal = $cartTotal - $discount;
                        @endphp

                        @foreach ($cart->items as $item)
                            @php
                                $product = $item->productVariant->product;
                                $gallery = $product->galleries->first();
                                $image = optional($gallery)->image;
                                $imageUrl = $image
                                    ? (Str::startsWith($image, ['http', '/']) ? $image : asset($image))
                                    : asset('assets/img/bg-img/default.jpg');
                            @endphp

                            <div class="d-flex mb-3 align-items-center pb-2 border-bottom">
                                <div class="flex-shrink-0">
                                    <img src="{{ $imageUrl }}"
                                         onerror="this.onerror=null;this.src='{{ asset('assets/img/default.jpg') }}';"
                                         alt="{{ $product->name }}"
                                         class="rounded-2 border"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-truncate" style="max-width: 200px;">
                                        {{ $product->name }}
                                    </h6>
                                    <small>Chậu: {{ $item->productVariant->pot }} | SL: {{ $item->quantity }}</small>
                                </div>
                            </div>
                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between">
                            <span>Vận Chuyển</span>
                            <strong class="text-success">Miễn Phí</strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Tạm tính</span>
                            <strong>{{ number_format($cartTotal, 0, ',', '.') }}đ</strong>
                        </div>

                        @if ($voucher)
                            <div class="d-flex justify-content-between text-danger">
                                <span>Mã giảm: {{ $voucher->code }}</span>
                                <strong>-{{ number_format($discount, 0, ',', '.') }}đ</strong>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mt-2">
                            <span class="fw-bold">Tổng cộng</span>
                            <span class="fw-bold">{{ number_format($finalTotal, 0, ',', '.') }}đ</span>
                        </div>

                        <input type="hidden" name="voucher_id" value="{{ $voucher?->id }}">
                        <input type="hidden" name="discount" value="{{ $discount }}">

                        <button type="button" id="place-order-btn" class="btn btn-dark mt-4 w-100">
                            Đặt Hàng
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- ✅ VNPAY FORM -->
        <form action="{{ url('/vnpay_payment') }}" method="POST" id="vnpay-form" style="display: none;">
            @csrf
            <input type="hidden" name="amount" value="{{ $finalTotal }}">
            <input type="hidden" name="redirect" value="1">
            <input type="hidden" name="voucher_id" value="{{ $voucher?->id }}">
            <input type="hidden" name="discount" value="{{ $discount }}">

            <!-- Thông tin chuyển từ form chính -->
            <input type="hidden" name="name" id="vnpay-name">
            <input type="hidden" name="phone" id="vnpay-phone">
            <input type="hidden" name="email" id="vnpay-email">
            <input type="hidden" name="address_id" id="vnpay-address_id">
            <input type="hidden" name="new_address" id="vnpay-new_address">
            <input type="hidden" name="set_default" id="vnpay-set_default">
            <input type="hidden" name="note" id="vnpay-note">
            <input type="hidden" name="payment_method" value="vnpay" id="vnpay-payment_method">
        </form>
    </div>
</div>
@endsection

@section('page_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const placeOrderBtn = document.getElementById('place-order-btn');
        const methodCod = document.getElementById('method_cod');
        const methodOnline = document.getElementById('method_online');
        const checkoutForm = document.getElementById('checkout-form');
        const vnpayForm = document.getElementById('vnpay-form');

        placeOrderBtn.addEventListener('click', function () {
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const email = document.getElementById('email').value;
            const newAddress = document.getElementById('new_address')?.value || '';
            const addressId = document.querySelector('input[name="address_id"]:checked')?.value || '';
            const note = document.getElementById('note')?.value || '';
            const setDefault = document.getElementById('set_default')?.checked ? '1' : '0';

            if (!name || !phone || !email || (!addressId && !newAddress)) {
                alert('Vui lòng điền đầy đủ thông tin và địa chỉ.');
                return;
            }

            if (methodOnline && methodOnline.checked) {
                // Gán vào form VNPAY
                document.getElementById('vnpay-name').value = name;
                document.getElementById('vnpay-phone').value = phone;
                document.getElementById('vnpay-email').value = email;
                document.getElementById('vnpay-new_address').value = newAddress;
                document.getElementById('vnpay-address_id').value = addressId;
                document.getElementById('vnpay-set_default').value = setDefault;
                document.getElementById('vnpay-note').value = note;

                vnpayForm.submit();
            } else {
                checkoutForm.submit();
            }
        });
    });
</script>
@endsection
