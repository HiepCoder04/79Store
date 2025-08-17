@extends('admin.layouts.dashboard')

@section('content')
<div class="container py-4">
    <div class="card-header text-white mb-3">
            <h4 class="mb-0">{{ isset($voucher) ? 'Chỉnh sửa Voucher' : 'Thêm Voucher' }}</h4>
        </div>
    <div class="card shadow-sm border-0">
        
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vouchers.store') }}">
                @csrf
                @if(isset($voucher)) @method('PUT') @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Mã voucher</label>
                        <input type="text" name="code" 
                            value="{{ old('code', $voucher->code ?? '') }}" 
                            class="form-control @error('code') is-invalid @enderror" 
                            placeholder="Nhập mã voucher">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Loại sự kiện</label>
                        <input type="text" name="event_type" 
                            value="{{ old('event_type', $voucher->event_type ?? '') }}" 
                            class="form-control @error('event_type') is-invalid @enderror" 
                            placeholder="Ví dụ: Sinh nhật, Khai trương...">
                        @error('event_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea name="description" rows="3" 
                            class="form-control @error('description') is-invalid @enderror" 
                            placeholder="Mô tả chi tiết voucher">{{ old('description', $voucher->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Phần trăm giảm giá (%)</label>
                        <input type="number" name="discount_percent" 
                            value="{{ old('discount_percent', $voucher->discount_percent ?? '') }}" 
                            class="form-control @error('discount_percent') is-invalid @enderror" 
                            placeholder="0-100">
                        @error('discount_percent')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Giảm tối đa</label>
                        <input type="number" name="max_discount" 
                            value="{{ old('max_discount', $voucher->max_discount ?? '') }}" 
                            class="form-control @error('max_discount') is-invalid @enderror" 
                            placeholder="VNĐ">
                        @error('max_discount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Đơn tối thiểu</label>
                        <input type="number" name="min_order_amount" 
                            value="{{ old('min_order_amount', $voucher->min_order_amount ?? '') }}" 
                            class="form-control @error('min_order_amount') is-invalid @enderror" 
                            placeholder="VNĐ">
                        @error('min_order_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ngày bắt đầu</label>
                        <input type="date" name="start_date" 
                            value="{{ old('start_date', $voucher->start_date ?? '') }}" 
                            class="form-control @error('start_date') is-invalid @enderror">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Ngày kết thúc</label>
                        <input type="date" name="end_date" 
                            value="{{ old('end_date', $voucher->end_date ?? '') }}" 
                            class="form-control @error('end_date') is-invalid @enderror">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Trạng thái</label>
                        <select name="is_active" class="form-select">
                            <option value="1" @selected(old('is_active', $voucher->is_active ?? 1) == 1)>Kích hoạt</option>
                            <option value="0" @selected(old('is_active', $voucher->is_active ?? 1) == 0)>Ngừng</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Quay lại</a>
                    <button class="btn btn-success px-4">{{ isset($voucher) ? 'Cập nhật' : 'Lưu' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
