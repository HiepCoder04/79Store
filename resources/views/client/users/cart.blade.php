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
            @if (session('error'))
                                <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                            @endif

            @if ($items->isEmpty())
                <div class="alert alert-info text-center">Giỏ hàng của bạn đang trống.</div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive shadow-sm rounded-3">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-light text-center align-middle">
                                    <tr>
                                        <th><input type="checkbox" id="check-all"></th>
                                        <th>Sản phẩm</th>
                                        <th>Chậu</th>
                                        <th>Chiều cao</th>
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
                                            <td>
                                                <input type="checkbox" name="selected_items[]" value="{{ $item->id }}" class="item-checkbox">
                                            </td>
                                            <td class="text-start">
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" width="80" class="img-thumbnail">
                                                    <strong>{{ $product->name }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $variant->pot }}</td>
                                            <td>
                                                {{ $variant->height ? $variant->height . ' cm' : 'Không rõ' }}
                                            </td>
                                            <td>
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                                    class="form-control quantity-input text-center" style="width: 80px;">
                                            </td>
                                            <td class="unit-price" data-price="{{ $variant->price }}">
                                                {{ number_format($variant->price, 0, ',', '.') }}đ
                                            </td>
                                            <td class="item-subtotal">
                                                {{ number_format($variant->price * $item->quantity, 0, ',', '.') }}đ
                                            </td>
                                            <td>
                                                <form method="POST" action="{{ route('cart.remove', $item->id) }}">
                                                    @csrf @method('DELETE')
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
                            <form action="{{ route('apply.voucher') }}" method="post">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="voucher_code" class="form-control" placeholder="Nhập mã giảm giá" aria-label="Voucher Code">
                                    <button class="btn btn-primary" type="submit">Áp dụng</button>
                                </div>
                            </form>
                            
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="cart-totals-area mt-70">
                            <h5 class="title--">Tổng đơn hàng</h5>

                            <div class="subtotal d-flex justify-content-between">
                                <h5>Tạm tính</h5>
                                <h5 id="subtotal-value">{{ number_format($cartTotal, 0, ',', '.') }}đ</h5> {{-- sẽ được cập nhật lại bằng JS --}}
                            </div>


                            <div class="shipping d-flex justify-content-between">
                                <h5>Phí vận chuyển</h5>
                                <h5 class="text-success">Miễn phí</h5>
                            </div>

                            @if ($voucher)
                                <div class="discount d-flex justify-content-between">
                                    <h5>Mã giảm: {{ $voucher->code }}</h5>
                                    <h5 class="text-danger">-{{ number_format($discount, 0, ',', '.') }}đ</h5>
                                </div>
                            @endif

                            <div class="total d-flex justify-content-between mt-3">
                                <h5>Tổng cộng</h5>
                                <h5 id="total-value">{{ number_format($finalTotal, 0, ',', '.') }}đ</h5>
                            </div>

                            <form id="checkout-form-selected" action="{{ route('checkout.index') }}" method="GET">
                                <input type="hidden" name="selected" id="selected-items-input">
                                <button type="button" id="go-to-checkout" class="btn alazea-btn w-100">Đến Trang Thanh Toán</button>
                            </form>

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

     function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }

    function updateSelectedTotal() {
        let subtotal = 0;

        document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
            const row = checkbox.closest('tr');
            const price = parseInt(row.querySelector('.unit-price').dataset.price);
            const qty = parseInt(row.querySelector('.quantity-input').value);
            subtotal += price * qty;
        });

        document.getElementById('subtotal-value').textContent = formatCurrency(subtotal);

        let discount = 0;
        @if ($voucher)
            const percent = {{ $voucher->discount_percent }};
            const maxDiscount = {{ $voucher->max_discount ?? 0 }};
            const minOrderAmount = {{ $voucher->min_order_amount }};

            if (subtotal >= minOrderAmount) {
                discount = Math.floor(subtotal * percent / 100);
                if (maxDiscount && discount > maxDiscount) {
                    discount = maxDiscount;
                }
            }
            document.getElementById('discount-value').textContent = '-' + formatCurrency(discount);
        @endif

        const finalTotal = subtotal - discount;
        document.getElementById('total-value').textContent = formatCurrency(finalTotal);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const quantityInputs = document.querySelectorAll('.quantity-input');

        checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedTotal));
        quantityInputs.forEach(qty => {
            qty.addEventListener('change', function () {
                const row = qty.closest('tr');
                const checkbox = row.querySelector('.item-checkbox');
                if (checkbox.checked) {
                    updateSelectedTotal();
                }
            });
        });

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = checkAll.checked);
                updateSelectedTotal();
            });
        }

        updateSelectedTotal(); // Gọi khi load
    });

     document.addEventListener('DOMContentLoaded', function () {
    const checkoutBtn = document.getElementById('go-to-checkout');

    checkoutBtn.addEventListener('click', function () {
        const checkedItems = document.querySelectorAll('.item-checkbox:checked');

        if (checkedItems.length === 0) {
            alert('Vui lòng chọn ít nhất 1 sản phẩm để tiếp tục thanh toán!');
            return;
        }

        // Lấy ID các sản phẩm đã chọn
        const selectedIds = Array.from(checkedItems).map(cb => cb.value);
        document.getElementById('selected-items-input').value = selectedIds.join(',');

        // Submit form
        document.getElementById('checkout-form-selected').submit();
    });
});
</script>
@endpush
