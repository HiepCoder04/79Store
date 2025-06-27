@extends('admin.layouts.dashboard')

@section('content')

<div class="container">
    <h2>edit voucher</h2>
    <form method="POST" action="{{ route('admin.vouchers.update',$voucher->id) }}">
    @csrf
    @if(isset($voucher)) @method('PUT') @endif

    <div class="col-12 mb-2">
    <label>Mã</label>
    <input name="code" value="{{ old('code', $voucher->code ?? '') }}" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
    @error('code')
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
    </div>
    
    <div class="col-12 mb-2">
    <label>Mô tả</label>
    <textarea name="description" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">{{ old('description', $voucher->description ?? '') }}</textarea>
    @error('description')
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
    </div>

    <div class="col-12 mb-2">
    <label>loại sự kiện</label>
    <input name="event_type" value="{{ old('event_type', $voucher->event_type ?? '') }}" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
    @error('event_type')
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
    </div>

    <div class="col-12 mb-2">
    <label>Phần trăm giảm giá</label>
    <input type="number" name="discount_percent" value="{{ old('discount_percent', $voucher->discount_percent ?? '') }}" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
    @error('discount_percent')
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
    </div>

    <div class="col-12 mb-2">
    <label>Ngày bắt đầu</label>
    <input type="date" name="start_date" value="{{ old('start_date', $voucher->start_date ?? '') }}" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
    @error('start_date')
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
    </div>

    <div class="col-12 mb-2">
    <label>Ngày kết thúc</label>
    <input type="date" name="end_date" value="{{ old('end_date', $voucher->end_date ?? '') }}" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
    @error('end_date')
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
    </div>

    <div class="col-12 mb-2">
    <label>Giảm tối đa</label>
    <input name="max_discount" value="{{ old('max_discount', $voucher->max_discount ?? '') }}" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
    @error('max_discount')
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
    </div>

    <div class="col-12 mb-2">
    <label>Đơn tối thiểu</label>
    <input name="min_order_amount" value="{{ old('min_order_amount', $voucher->min_order_amount ?? '') }}" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
    @error('min_order_amount')
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
    </div>

    <div class="col-12 mb-2">
    <label>Trạng thái</label>
    <select name="is_active" class="bg-gray-100 border border-gray-300 rounded px-4 py-2 w-full text-gray-700 focus:outline-none focus:border-blue-500">
        <option value="1" @selected(old('is_active', $voucher->is_active ?? 1) == 1)>Kích hoạt</option>
        <option value="0" @selected(old('is_active', $voucher->is_active ?? 1) == 0)>Ngừng</option>
    </select>
    </div>

    <button class="btn btn-success mt-3">Lưu</button>
</form>

</div>
@endsection
