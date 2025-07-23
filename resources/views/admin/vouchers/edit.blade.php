@extends('admin.layouts.dashboard')

@section('content')

<style>
.edit-voucher-container {
    max-width: 800px;
    margin: 30px auto;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 30px;
}

.edit-voucher-container h2 {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 25px;
    text-align: center;
}

label {
    font-weight: 500;
    margin-bottom: 6px;
    color: #34495e;
    display: block;
}

input,
textarea,
select {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 6px;
    padding: 10px 14px;
    width: 100%;
    font-size: 15px;
    color: #2c3e50;
    transition: border 0.2s ease-in-out;
}

input:focus,
textarea:focus,
select:focus {
    border-color: #007bff;
    outline: none;
    background-color: #ffffff;
    box-shadow: 0 0 0 0.1rem rgba(0, 123, 255, 0.15);
}

.text-danger {
    font-size: 13px;
    color: #e74c3c;
    margin-top: 4px;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    padding: 10px 24px;
    font-size: 16px;
    font-weight: 500;
    border-radius: 8px;
    transition: background-color 0.2s ease-in-out;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

@media (max-width: 576px) {
    .edit-voucher-container {
        padding: 20px;
    }

    .btn-success {
        width: 100%;
    }
}
</style>

<div class="edit-voucher-container">
    <h2>Chỉnh sửa Voucher</h2>
    <form method="POST" action="{{ route('admin.vouchers.update', $voucher->id) }}">
        @csrf
        @method('PUT')

        {{-- Mã voucher --}}
        <div class="mb-3">
            <label for="code">Mã</label>
            <input id="code" name="code" value="{{ old('code', $voucher->code) }}">
            @error('code') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Mô tả --}}
        <div class="mb-3">
            <label for="description">Mô tả</label>
            <textarea id="description" name="description">{{ old('description', $voucher->description) }}</textarea>
            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Loại sự kiện --}}
        <div class="mb-3">
            <label for="event_type">Loại sự kiện</label>
            <input id="event_type" name="event_type" value="{{ old('event_type', $voucher->event_type) }}">
            @error('event_type') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Phần trăm giảm --}}
        <div class="mb-3">
            <label for="discount_percent">Phần trăm giảm giá</label>
            <input type="number" id="discount_percent" name="discount_percent"
                value="{{ old('discount_percent', $voucher->discount_percent) }}">
            @error('discount_percent') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Ngày bắt đầu --}}
        <div class="mb-3">
            <label for="start_date">Ngày bắt đầu</label>
            <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $voucher->start_date) }}">
            @error('start_date') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Ngày kết thúc --}}
        <div class="mb-3">
            <label for="end_date">Ngày kết thúc</label>
            <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $voucher->end_date) }}">
            @error('end_date') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Giảm tối đa --}}
        <div class="mb-3">
            <label for="max_discount">Giảm tối đa</label>
            <input id="max_discount" name="max_discount" value="{{ old('max_discount', $voucher->max_discount) }}">
            @error('max_discount') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Giá trị đơn tối thiểu --}}
        <div class="mb-3">
            <label for="min_order_amount">Giá trị đơn tối thiểu</label>
            <input id="min_order_amount" name="min_order_amount"
                value="{{ old('min_order_amount', $voucher->min_order_amount) }}">
            @error('min_order_amount') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Trạng thái --}}
        <div class="mb-3">
            <label for="is_active">Trạng thái</label>
            <select id="is_active" name="is_active">
                <option value="1" @selected(old('is_active', $voucher->is_active) == 1)>Kích hoạt</option>
                <option value="0" @selected(old('is_active', $voucher->is_active) == 0)>Ngừng</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success mt-3">Lưu lại</button>
    </form>
</div>

@endsection