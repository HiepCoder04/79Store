@extends('admin.layouts.dashboard')

@section('content')
<div class="container mt-4">
    <h4 class="text-primary fw-bold mb-4">🎁 Thêm Voucher Mới</h4>

    <form method="POST" action="{{ route('admin.vouchers.store') }}" class="bg-white p-4 rounded shadow-sm border">
        @csrf
        @if(isset($voucher)) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Mã</label>
            <input name="code" value="{{ old('code', $voucher->code ?? '') }}" class="form-control"
                placeholder="Nhập mã voucher">
            @error('code')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3"
                placeholder="Mô tả chi tiết">{{ old('description', $voucher->description ?? '') }}</textarea>
            @error('description')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Loại sự kiện</label>
            <input name="event_type" value="{{ old('event_type', $voucher->event_type ?? '') }}" class="form-control"
                placeholder="Ví dụ: Black Friday">
            @error('event_type')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Phần trăm giảm giá (%)</label>
                <input type="number" name="discount_percent"
                    value="{{ old('discount_percent', $voucher->discount_percent ?? '') }}" class="form-control">
                @error('discount_percent')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Giảm tối đa (VNĐ)</label>
                <input name="max_discount" value="{{ old('max_discount', $voucher->max_discount ?? '') }}"
                    class="form-control">
                @error('max_discount')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Ngày bắt đầu</label>
                <input type="date" name="start_date" value="{{ old('start_date', $voucher->start_date ?? '') }}"
                    class="form-control">
                @error('start_date')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Ngày kết thúc</label>
                <input type="date" name="end_date" value="{{ old('end_date', $voucher->end_date ?? '') }}"
                    class="form-control">
                @error('end_date')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá trị hóa đơn tối thiểu (VNĐ)</label>
            <input name="min_order_amount" value="{{ old('min_order_amount', $voucher->min_order_amount ?? '') }}"
                class="form-control">
            @error('min_order_amount')
            <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Trạng thái</label>
            <select name="is_active" class="form-select">
                <option value="1" @selected(old('is_active', $voucher->is_active ?? 1) == 1)>Kích hoạt</option>
                <option value="0" @selected(old('is_active', $voucher->is_active ?? 1) == 0)>Ngừng</option>
            </select>
        </div>

        <button class="btn btn-primary px-4">
            <i class="bi bi-save"></i> Lưu voucher
        </button>
    </form>
</div>
@endsection