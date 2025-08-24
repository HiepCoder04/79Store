@extends('admin.layouts.dashboard')

@section('title', 'Quản lý hủy đơn hàng | 79Store')

@section('content')
<div class="container-fluid py-4">
    <h3 class="mb-4"> Quản lý hủy đơn hàng</h3>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Mã đơn</th>
                        <th>Người yêu cầu</th>
                        <th>Lý do hủy</th>
                        <th>Trạng thái</th>
                        <th>Ngày yêu cầu</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cancellations as $cancel)
                        <tr>
                            <td>{{ $cancel->id }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $cancel->order_id) }}" target="_blank">
                                    {{ $cancel->order->order_code }}
                                </a>
                            </td>
                            <td>{{ $cancel->user->name }}</td>
                            <td>{{ Str::limit($cancel->reason, 50) }}</td>
                            <td>
                                @php
                                    $statusMap = [
                                        'pending' => ['class' => 'warning', 'label' => 'Chờ duyệt'],
                                        'approved' => ['class' => 'success', 'label' => 'Đã duyệt'],
                                        'rejected' => ['class' => 'danger', 'label' => 'Đã từ chối'],
                                    ];
                                    $status = $statusMap[$cancel->status] ?? ['class' => 'secondary', 'label' => 'Không xác định'];
                                @endphp
                                <span class="badge bg-{{ $status['class'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td>{{ $cancel->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.cancellations.show', $cancel->id) }}" class="btn btn-sm btn-outline-info">
                                    Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-muted">Không có yêu cầu hủy nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    @if($cancellations->total() > 0)
                        Hiển thị {{ $cancellations->firstItem() }} - {{ $cancellations->lastItem() }} 
                        trong tổng số {{ $cancellations->total() }} yêu cầu hủy đơn
                    @else
                        Không có yêu cầu hủy đơn nào
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $cancellations->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
