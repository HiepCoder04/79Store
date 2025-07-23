@extends('admin.layouts.dashboard')

@section('content')
@php use Carbon\Carbon; @endphp

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0 text-primary">Danh sách Voucher</h4>
        <a href="{{ route('admin.vouchers.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Thêm voucher
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>Mã</th>
                    <th>Phần trăm giảm</th>
                    <th>Bắt đầu</th>
                    <th>Kết thúc</th>
                    <th>Giảm tối đa</th>
                    <th>HĐ tối thiểu</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vouchers as $v)
                <tr class="text-center">
                    <td>{{ $v->code }}</td>
                    <td>{{ $v->discount_percent }}%</td>
                    <td>{{ Carbon::parse($v->start_date)->format('d-m-Y') }}</td>
                    <td>{{ Carbon::parse($v->end_date)->format('d-m-Y') }}</td>
                    <td>{{ number_format($v->max_discount, 0, ',', '.') }}đ</td>
                    <td>{{ number_format($v->min_order_amount, 0, ',', '.') }}đ</td>
                    <td>
                        @if($v->is_active)
                        <span class="badge bg-success">Hoạt động</span>
                        @else
                        <span class="badge bg-secondary">Ngừng</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <a href="{{ route('admin.vouchers.edit', $v->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i> Sửa
                            </a>
                            <a href="{{ route('admin.vouchers.users', $v->id) }}"
                                class="btn btn-sm btn-info text-white">
                                <i class="bi bi-people"></i> Người đã dùng
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Không có voucher nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection