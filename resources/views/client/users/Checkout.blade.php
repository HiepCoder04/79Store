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

        @foreach ($cart->items as $item)
            @php
                $product = $item->productVariant->product;
                $gallery = $product->galleries->first();
                $image = optional($gallery)->image;

                $imageUrl = $image
                    ? (Str::startsWith($image, ['http', '/']) ? $image : asset($image))
                    : asset('assets/img/bg-img/default.jpg');

                $subtotal = $item->productVariant->price * $item->quantity;
                $total += $subtotal;
            @endphp

            <div class="d-flex mb-3 align-items-center pb-2 border-bottom">
                <div class="flex-shrink-0">
                    <img src="{{ $imageUrl }}"
                        onerror="this.onerror=null;this.src='{{ asset('assets/img/default.jpg') }}';"
                        alt="{{ $product->name }}" class="rounded-2 border" style="width: 60px; height: 60px; object-fit: cover;">
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="mb-1 text-truncate" style="max-width: 200px; margin-left: 10px">
                        {{ $product->name }}
                    </h6>
                    <small style="margin-left: 10px">
                        Chậu: {{ $item->productVariant->pot }} |
                        SL: {{ $item->quantity }}
                    </small>
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

        <button type="button" id="place-order-btn" class="btn btn-dark mt-4 w-100">
            Đặt Hàng
        </button>
    </div>
</div>
                </div>
            </form>

            <!-- VNPAY payment form - separate from the main checkout form -->
            <form action="{{ url('/vnpay_payment') }}" method="POST" id="vnpay-form" style="display: none;">
                @csrf
                <input type="hidden" name="amount" value="{{ $total }}">
                <input type="hidden" name="redirect" value="1">
                <!-- Hidden fields to carry over form data from main checkout form -->
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
    <!-- ##### Checkout Area End ##### -->

@endsection

@section('page_scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Checkout script loaded');

            // Form elements
            const placeOrderBtn = document.getElementById('place-order-btn');
            const checkoutForm = document.getElementById('checkout-form');
            const vnpayForm = document.getElementById('vnpay-form');
            const methodCod = document.getElementById('method_cod');
            const methodOnline = document.getElementById('method_online');

            // Address management elements
            const addNewAddressDiv = document.getElementById('add-new-address');
            const confirmNewAddressBtn = document.getElementById('confirm-new-address');
            const changeAddressBtn = document.querySelector('button.btn-outline-primary.me-2');
            const addAddressBtn = document.querySelector('button.btn-outline-success');
            const newAddressInput = document.getElementById('new_address');
            const addressAddedSuccess = document.getElementById('address-added-success');

            // Khôi phục địa chỉ từ localStorage nếu có (cho trường hợp trang bị refresh)
            const restoreTempAddress = () => {
                if (localStorage.getItem('tempNewAddress')) {
                    // Lấy giá trị đã lưu
                    const savedAddress = localStorage.getItem('tempNewAddress');
                    const isDefault = localStorage.getItem('tempSetDefault') === '1';

                    if (savedAddress && newAddressInput) {
                        // Điền giá trị vào form
                        newAddressInput.value = savedAddress;

                        // Hiển thị div thêm địa chỉ
                        addNewAddressDiv.classList.remove('d-none');

                        // Đặt checkbox nếu cần
                        if (document.getElementById('set_default')) {
                            document.getElementById('set_default').checked = isDefault;
                        }

                        // Đánh dấu địa chỉ đã được xác nhận
                        newAddressInput.classList.add('is-valid');
                        newAddressInput.classList.add('confirmed-address');
                        newAddressInput.setAttribute('readonly', true);
                        confirmNewAddressBtn.classList.add('d-none');

                        // Lấy container địa chỉ hiện tại
                        const addressDisplayContainer = document.querySelector('.bg-light.p-3.rounded.mb-3');

                        if (addressDisplayContainer) {
                            // Cập nhật hiển thị địa chỉ mới
                            const addressText = addressDisplayContainer.querySelector('.fa-location-dot')
                                .nextSibling;
                            if (addressText) {
                                addressText.textContent = " " + savedAddress;
                            }

                            // Đóng form thêm địa chỉ
                            const changeAddressDiv = document.getElementById('change-address');
                            if (changeAddressDiv) {
                                changeAddressDiv.classList.add('d-none');
                            }

                            // Bỏ chọn tất cả radio buttons địa chỉ
                            const addressRadios = document.querySelectorAll('input[name="address_id"]');
                            addressRadios.forEach(radio => radio.checked = false);
                        }
                    }
                }
            };

            // Chạy khôi phục ngay khi trang load
            restoreTempAddress();

            // Handle address UI interactions
            if (addAddressBtn) {
                addAddressBtn.addEventListener('click', function() {
                    // When "Thêm mới" button is clicked, show the add new address form
                    addNewAddressDiv.classList.remove('d-none');
                });
            }

            // Handle confirming new address
            if (confirmNewAddressBtn && newAddressInput) {
                confirmNewAddressBtn.addEventListener('click', function() {
                    const newAddress = newAddressInput.value.trim();
                    if (newAddress) {
                        // Lấy container địa chỉ hiện tại
                        const addressDisplayContainer = document.querySelector(
                            '.bg-light.p-3.rounded.mb-3');

                        if (addressDisplayContainer) {
                            // Cập nhật hiển thị địa chỉ mới
                            const addressText = addressDisplayContainer.querySelector('.fa-location-dot')
                                .nextSibling;
                            if (addressText) {
                                addressText.textContent = " " + newAddress;
                            }

                            // Đóng form thêm địa chỉ mới
                            addNewAddressDiv.classList.add('d-none');
                        }

                        // Bỏ chọn tất cả radio buttons địa chỉ nếu có
                        const addressRadios = document.querySelectorAll('input[name="address_id"]');
                        addressRadios.forEach(radio => radio.checked = false);

                        // Đảm bảo div chứa các địa chỉ đã chọn được ẩn
                        const changeAddressDiv = document.getElementById('change-address');
                        if (changeAddressDiv) {
                            changeAddressDiv.classList.add('d-none');
                        }

                        // Show success message
                        addressAddedSuccess.classList.remove('d-none');

                        // Highlight the input to show it's been accepted
                        newAddressInput.classList.add('is-valid');

                        // Đánh dấu để submit
                        newAddressInput.classList.add('confirmed-address');

                        // Hide the confirm button
                        confirmNewAddressBtn.classList.add('d-none');

                        // After 3 seconds, hide the success message
                        setTimeout(() => {
                            addressAddedSuccess.classList.add('d-none');
                        }, 3000);

                        // Lưu địa chỉ mới vào localStorage để có thể khôi phục nếu trang refresh
                        localStorage.setItem('tempNewAddress', newAddress);
                        localStorage.setItem('tempSetDefault', document.getElementById('set_default')
                            .checked ? '1' : '0');
                    } else {
                        // Show error if empty
                        newAddressInput.classList.add('is-invalid');
                        alert('Vui lòng nhập địa chỉ mới!');
                    }
                });

                // Remove invalid class when user types in the input
                newAddressInput.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            }

            // Tạo loading overlay
            const loadingOverlay = document.createElement('div');
            loadingOverlay.style.position = 'fixed';
            loadingOverlay.style.top = '0';
            loadingOverlay.style.left = '0';
            loadingOverlay.style.width = '100%';
            loadingOverlay.style.height = '100%';
            loadingOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
            loadingOverlay.style.display = 'none';
            loadingOverlay.style.justifyContent = 'center';
            loadingOverlay.style.alignItems = 'center';
            loadingOverlay.style.zIndex = '9999';

            const loadingContent = document.createElement('div');
            loadingContent.style.backgroundColor = 'white';
            loadingContent.style.padding = '20px';
            loadingContent.style.borderRadius = '5px';
            loadingContent.style.textAlign = 'center';

            const loadingText = document.createElement('p');
            loadingText.textContent = 'Đang chuyển hướng đến cổng thanh toán...';
            loadingText.style.marginBottom = '10px';

            const spinner = document.createElement('div');
            spinner.classList.add('spinner-border', 'text-primary');
            spinner.setAttribute('role', 'status');

            loadingContent.appendChild(spinner);
            loadingContent.appendChild(loadingText);
            loadingOverlay.appendChild(loadingContent);
            document.body.appendChild(loadingOverlay);

            // Hiển thị loading khi chuyển hướng
            const showLoading = () => {
                loadingOverlay.style.display = 'flex';
            };

            // Helper để hiển thị logs (debug)
            const logFormData = () => {
                const formData = {
                    name: document.getElementById('name').value,
                    phone: document.getElementById('phone').value,
                    email: document.getElementById('email').value,
                    payment_method: methodCod.checked ? 'cod' : 'vnpay',
                };

                // Address info
                if (document.getElementById('new_address') && document.getElementById('new_address').value
                    .trim()) {
                    formData.new_address = document.getElementById('new_address').value;
                    formData.set_default = document.getElementById('set_default').checked;
                    formData.using = 'new_address';
                } else {
                    const checkedAddress = document.querySelector('input[name="address_id"]:checked');
                    if (checkedAddress) {
                        formData.address_id = checkedAddress.value;
                        formData.using = 'existing_address';
                    } else {
                        formData.using = 'none';
                    }
                }

                console.log('Form data to be sent:', formData);
            };

            if (placeOrderBtn) {
                placeOrderBtn.addEventListener('click', function() {
                    // Kiểm tra dữ liệu nhập vào
                    const name = document.getElementById('name').value;
                    const phone = document.getElementById('phone').value;
                    const email = document.getElementById('email').value;

                    if (!name || !phone || !email) {
                        alert('Vui lòng điền đầy đủ thông tin cá nhân!');
                        return;
                    }

                    // Kiểm tra địa chỉ
                    const addressRadios = document.querySelectorAll('input[name="address_id"]');
                    const newAddress = document.getElementById('new_address');
                    let addressSelected = false;

                    if (addressRadios.length > 0) {
                        for (const radio of addressRadios) {
                            if (radio.checked) {
                                addressSelected = true;
                                break;
                            }
                        }
                    }

                    if (!addressSelected && (!newAddress || !newAddress.value)) {
                        alert('Vui lòng chọn hoặc nhập địa chỉ giao hàng!');
                        return;
                    }

                    // Validate if a new address is entered but not confirmed
                    if (newAddress && newAddress.value.trim() &&
                        !newAddress.hasAttribute('readonly') &&
                        !newAddress.classList.contains('confirmed-address') &&
                        document.getElementById('confirm-new-address') &&
                        !document.getElementById('confirm-new-address').classList.contains('d-none')) {
                        alert('Vui lòng xác nhận địa chỉ mới trước khi đặt hàng!');
                        newAddress.focus();
                        return;
                    }

                    // Check which payment method is selected
                    if (methodOnline && methodOnline.checked) {
                        // If VNPAY is selected, copy data from main form to VNPAY form
                        document.getElementById('vnpay-name').value = document.getElementById('name').value;
                        document.getElementById('vnpay-phone').value = document.getElementById('phone')
                            .value;
                        document.getElementById('vnpay-email').value = document.getElementById('email')
                            .value;

                        // Copy address data if applicable
                        if (addressRadios.length > 0) {
                            for (const radio of addressRadios) {
                                if (radio.checked) {
                                    document.getElementById('vnpay-address_id').value = radio.value;
                                    break;
                                }
                            }
                        }

                        // Copy new address data if applicable
                        if (newAddress && newAddress.value.trim()) {
                            document.getElementById('vnpay-new_address').value = newAddress.value;

                            // Log để debug
                            console.log('Using new address:', newAddress.value);
                        }

                        // Copy set_default checkbox if applicable
                        if (document.getElementById('set_default')) {
                            document.getElementById('vnpay-set_default').value = document.getElementById(
                                'set_default').checked ? 1 : 0;
                        }

                        // Copy note if applicable
                        if (document.getElementById('note')) {
                            document.getElementById('vnpay-note').value = document.getElementById('note')
                                .value;
                        }

                        // Log form data before submitting
                        logFormData();

                        // Hiển thị loading và submit form VNPAY
                        showLoading();
                        setTimeout(() => {
                            // Không xóa localStorage trong trường hợp VNPAY vì người dùng có thể quay lại
                            vnpayForm.submit();
                        }, 300); // Đợi một chút để hiển thị loading
                    } else {
                        // Log form data before submitting
                        logFormData();

                        // If COD is selected, submit the main checkout form
                        // Clear localStorage to avoid reusing temp address after successful submission
                        localStorage.removeItem('tempNewAddress');
                        localStorage.removeItem('tempSetDefault');
                        checkoutForm.submit();
                    }
                });
            } else {
                console.error('Place order button not found');
            }
        });
    </script>
@endsection
