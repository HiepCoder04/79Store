<div class="p-4 border rounded shadow-sm bg-white">
    <h5 class="fw-bold mb-4"><i class="fa fa-user me-2 text-primary"></i> Thông tin người nhận</h5>

    <div class="row g-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Họ và tên</label>
            <input type="text" name="name" id="name" class="form-control"
                value="{{ old('name', $user->name ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label for="phone" class="form-label">Số điện thoại</label>
            <input type="tel" name="phone" id="phone" class="form-control"
                value="{{ old('phone', $user->phone ?? '') }}" required>
        </div>
        <div class="col-12">
            <label for="email" class="form-label">Gmail (Người đặt)</label>
            <input type="email" name="email" id="email" class="form-control"
                value="{{ old('email', $user->email ?? '') }}" required disabled>
        </div>
    </div>

    <hr class="my-4">

    <h5 class="fw-bold mb-3"><i class="fa fa-map-marker-alt me-2 text-danger"></i>Địa chỉ nhận hàng</h5>

    @if ($addresses->isNotEmpty())
        @php $default = $addresses->first(); @endphp
        <div class="bg-light p-3 rounded mb-3">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <i class="fa fa-user text-secondary"></i>
<span id="selected-address-text" class="ms-2 fw-semibold text-dark">
    {{ optional($addresses->first())->address ?? 'Chưa chọn địa chỉ' }}
</span><strong>{{ $default->name }}</strong><br>
                    <i class="fa fa-phone-alt text-secondary"></i> <span>{{ $default->phone }}</span><br>
                    <i class="fa fa-location-dot text-secondary"></i>
                    {{ $default->address->address ?? '' }}
                </div>
                <div class="text-end">
                    <button type="button" id="change-address-btn"
                        class="btn btn-sm btn-outline-primary me-2">Thay đổi</button>
                    <button type="button" class="btn btn-sm btn-outline-success"
                        onclick="document.getElementById('add-new-address').classList.toggle('d-none')">+ Thêm mới</button>
                </div>
            </div>
        </div>

        <div id="change-address" class="d-none mb-3">
            <div class="mb-3">
                <div class="fw-bold mb-2">Chọn từ địa chỉ đã lưu:</div>
                @foreach($addresses as $order)
                    @if($order->address) {{-- Chỉ hiển thị nếu có địa chỉ --}}
                    <div class="form-check mb-2">
                       <input class="form-check-input" type="radio" name="address_id"
       id="address_{{ $order->id }}"
       value="{{ $order->id }}"
       data-address="{{ $order->address }}"
       {{ $loop->first ? 'checked' : '' }}>
                        <label class="form-check-label" for="address_{{ $order->address_id }}">
                            <i class="fa fa-location-dot me-1 text-muted"></i>
                            {{ $order->address }} -
                            <span class="text-muted">{{ $order->phone }}</span>
                        </label>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <div id="add-new-address" class="{{ $addresses->isNotEmpty() ? 'd-none' : '' }}">
        <div class="mb-3">
            <label for="new_address" class="form-label">Nhập địa chỉ cụ thể</label>
            <input type="text" class="form-control" name="new_address" id="new_address">
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="set_default" id="set_default">
            <label class="form-check-label" for="set_default">Đặt làm mặc định</label>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <button type="button" id="confirm-new-address" class="btn btn-sm btn-outline-primary">Xác nhận địa chỉ</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="cancel-new-address">Hủy</button>
        </div>
    </div>

    <div id="address-added-success" class="alert alert-success mt-2 d-none">
        <span>Địa chỉ mới đã được thêm và sẽ được sử dụng cho đơn hàng này.</span>
    </div>

    <div class="mb-4 mt-4">
        <label for="note" class="form-label">Ghi chú đơn hàng</label>
        <textarea class="form-control" name="note" id="note" rows="3" placeholder="Ghi chú giao hàng..."></textarea>
    </div>

    <h5 class="fw-bold mb-3"><i class="fa fa-wallet me-2 text-warning"></i>Phương thức thanh toán</h5>
    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="payment_method" id="method_cod" value="cod" checked>
        <label class="form-check-label" for="method_cod">
            <i class="fa fa-money-bill-alt me-1"></i> Thanh toán khi nhận hàng
        </label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="payment_method" id="method_online" value="vnpay">
        <label class="form-check-label" for="method_online">
            <i class="fa fa-credit-card me-1"></i> Thanh toán trước (VNPAY)
        </label>
    </div>
</div>
