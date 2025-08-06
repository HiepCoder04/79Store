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
                                            <td>{{ $item->pot?->name ?? 'Không có chậu' }}</td>

                                            <td>
                                                {{ $variant->height ? $variant->height . ' cm' : 'Không rõ' }}
                                            </td>
                                            <td>
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                                    class="form-control quantity-input text-center" style="width: 80px;">
                                            </td>
                                            {{-- update gia = gia sp bien the + gia chau --}}
                                           @php
                                            $pot = $item->pot;
                                            $potPrice = $pot?->price ?? 0;
                                            $unitPrice = $variant->price + $potPrice;
                                            @endphp

                                            <td class="unit-price" data-price="{{ $unitPrice }}">
                                            {{ number_format($unitPrice, 0, ',', '.') }}đ
                                            </td>
                                            <td class="item-subtotal">
                                            {{ number_format($unitPrice * $item->quantity, 0, ',', '.') }}đ
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
                    <div id="voucher-message"></div>
                    <form id="apply-voucher-form" action="{{ route('apply.voucher') }}" method="post">
                        @csrf
                        <div class="input-group">
                            <input type="text" id="voucher_code_input" name="voucher_code" class="form-control" placeholder="Nhập mã giảm giá">
                            <input type="hidden" id="voucher-selected-items" name="selected">
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
    // Format tiền
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
    }

    // Tính lại tổng tiền khi chọn sản phẩm hoặc thay đổi số lượng
    function updateSelectedTotal(discountInfo = null) {
        let subtotal = 0;

        document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
            const row = checkbox.closest('tr');
            const price = parseInt(row.querySelector('.unit-price').dataset.price);
            const qty = parseInt(row.querySelector('.quantity-input').value);
            subtotal += price * qty;
        });

        document.getElementById('subtotal-value').textContent = formatCurrency(subtotal);

        let discount = 0;
        if (discountInfo !== null) {
            const percent = parseFloat(discountInfo.discount_percent);
            const maxDiscount = parseFloat(discountInfo.max_discount || 0);
            const minOrderAmount = parseFloat(discountInfo.min_order_amount || 0);

            if (subtotal >= minOrderAmount) {
                discount = Math.floor(subtotal * percent / 100);
                discount = Math.min(discount, maxDiscount);
            }

            const discountContainer = document.getElementById('discount-value');
            if (discountContainer) {
                discountContainer.textContent = '-' + formatCurrency(discount);
            } else {
                const discountRow = document.createElement('div');
                discountRow.classList.add('discount', 'd-flex', 'justify-content-between');
                discountRow.innerHTML = `<h5>Mã giảm:</h5><h5 id="discount-value" class="text-danger">-${formatCurrency(discount)}</h5>`;
                document.querySelector('.cart-totals-area').insertBefore(discountRow, document.querySelector('.total'));
            }
        }

        const finalTotal = subtotal - discount;
        document.getElementById('total-value').textContent = formatCurrency(finalTotal);
    }

    // Cập nhật khi số lượng thay đổi
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
                    updateSelectedTotal(); // cập nhật lại tổng
                } catch (error) {
                    console.error(error);
                    alert('Lỗi cập nhật giỏ hàng: ' + error.message);
                }
            });
        });
    });

    // Checkbox chọn sản phẩm
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('check-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const quantityInputs = document.querySelectorAll('.quantity-input');

        checkboxes.forEach(cb => cb.addEventListener('change', () => updateSelectedTotal()));
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

        updateSelectedTotal();
    });

    // Áp dụng mã giảm giá bằng AJAX
    document.addEventListener('DOMContentLoaded', function () {
        const applyForm = document.getElementById('apply-voucher-form');
        const voucherInput = document.getElementById('voucher_code_input');
        const voucherMessage = document.getElementById('voucher-message');

        applyForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

            if (selectedIds.length === 0) {
                voucherMessage.innerHTML = '<div class="alert alert-danger">Vui lòng chọn ít nhất 1 sản phẩm để áp dụng mã!</div>';
                return;
            }

            const code = voucherInput.value.trim();
            if (!code) {
                voucherMessage.innerHTML = '<div class="alert alert-danger">Vui lòng nhập mã giảm giá.</div>';
                return;
            }

            try {
                const res = await fetch(applyForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        voucher_code: code,
                        selected: selectedIds
                    })
                });

                const data = await res.json();

                if (data.success) {
                    voucherMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    updateSelectedTotal({
                        discount_percent: data.discount_percent,
                        max_discount: data.max_discount,
                        min_order_amount: data.min_order_amount
                    });
                } else {
                    voucherMessage.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            } catch (err) {
                voucherMessage.innerHTML = `<div class="alert alert-danger">Có lỗi xảy ra!</div>`;
                console.error(err);
            }
        });
    });

    // Đến trang thanh toán
    document.addEventListener('DOMContentLoaded', function () {
        const checkoutBtn = document.getElementById('go-to-checkout');

        checkoutBtn.addEventListener('click', function () {
            const checkedItems = document.querySelectorAll('.item-checkbox:checked');

            if (checkedItems.length === 0) {
                alert('Vui lòng chọn ít nhất 1 sản phẩm để tiếp tục thanh toán!');
                return;
            }

            const selectedIds = Array.from(checkedItems).map(cb => cb.value);
            document.getElementById('selected-items-input').value = selectedIds.join(',');

            document.getElementById('checkout-form-selected').submit();
        });
    });
    // Lấy danh sách mã giảm giá
    function renderVoucherSuggestions(vouchers) {
    const input = document.getElementById('voucher_code_input');
    let suggestionBox = document.getElementById('voucher-suggestion-box');

    if (suggestionBox) suggestionBox.remove();

    suggestionBox = document.createElement('div');
    suggestionBox.id = "voucher-suggestion-box";
    suggestionBox.style.top = "100%";  // đẩy xuống ngay dưới input
suggestionBox.style.marginTop = "10px"; // thêm khoảng cách
    suggestionBox.style.zIndex = 999;
    suggestionBox.style.width = "100%";
    suggestionBox.style.background = "#fff";
    suggestionBox.style.border = "1px solid #ccc";
    suggestionBox.style.boxShadow = "0 2px 6px rgba(0,0,0,0.1)";
    suggestionBox.innerHTML = vouchers.map(v => `
        <div class="voucher-item p-2 border-bottom" style="cursor: pointer" data-code="${v.code}">
            <strong>${v.code}</strong> - ${parseInt(v.discount_percent)}%<br>

            HSD: ${new Date(v.end_date).toLocaleDateString('vi-VN')}<br>
            Giá giảm tối đa: ${parseInt(v.max_discount)}đ | Giá hóa đơn tối thiểu: ${parseInt(v.min_order_amount)}đ

        </div>
    `).join('');

    input.parentElement.style.position = "relative";
    input.parentElement.appendChild(suggestionBox);

    document.querySelectorAll('.voucher-item').forEach(item => {
        item.addEventListener('click', function () {
            input.value = this.dataset.code;
            suggestionBox.remove();
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('voucher_code_input');

    input.addEventListener('focus', function () {
        fetch('/vouchers/suggestions')
  .then(response => response.json())
  .then(data => {
    // Nếu response là object có dạng { data: [...] }
    const vouchers = Array.isArray(data) ? data : data.data;

    if (!Array.isArray(vouchers)) {
      throw new Error("Invalid vouchers format");
    }

    renderVoucherSuggestions(vouchers);

  })
  .catch(error => {
    console.error("LỖI API voucher:", error);
  });


    document.addEventListener('click', function (e) {
        const box = document.getElementById('voucher-suggestion-box');
        if (box && !box.contains(e.target) && e.target !== input) {
            box.remove();
        }
    });
});
})
</script>
@endpush


