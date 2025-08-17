@extends('admin.layouts.dashboard')

@section('title', 'Chi tiết yêu cầu hủy đơn hàng | 79Store')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Chi tiết yêu cầu hủy đơn hàng</h5>
            <a href="{{ route('admin.cancellations.index') }}" class="btn btn-outline-secondary btn-sm">
                ← Quay lại danh sách
            </a>
        </div>

        <div class="card-body">
            <!-- Thông tin chung -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted">Thông tin đơn hàng</h6>
                    <p><strong>Mã đơn:</strong> {{ $cancellation->order->order_code }}</p>
                    <p><strong>Khách hàng:</strong> {{ $cancellation->user->name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $cancellation->order->phone }}</p>
                    <p><strong>Ngày đặt:</strong> {{ $cancellation->order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-muted">Thông tin yêu cầu hủy</h6>
                    <p><strong>Ngày yêu cầu:</strong> {{ $cancellation->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Lý do:</strong> {{ $cancellation->reason }}</p>
                    <p>
                        <strong>Trạng thái:</strong>
                        @php
                            $statusMap = [
                                'pending' => ['class' => 'bg-warning text-dark', 'label' => 'Chờ duyệt'],
                                'approved' => ['class' => 'bg-success', 'label' => 'Đã duyệt'],
                                'rejected' => ['class' => 'bg-danger', 'label' => 'Đã từ chối'],
                            ];
                            $status = $statusMap[$cancellation->status] ?? ['class' => 'bg-secondary', 'label' => 'Không xác định'];
                        @endphp
                        <span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                    </p>
                    @if ($cancellation->admin_note)
                        <p><strong>Ghi chú quản trị:</strong> {{ $cancellation->admin_note }}</p>
                    @endif
                </div>
            </div>

            <!-- Sản phẩm trong đơn -->
            <div class="mb-4">
                <h6 class="fw-bold text-muted">Sản phẩm trong đơn</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Chiều cao</th>
                                <th>Loại chậu</th>
                                <th>Giá cây</th>
                                <th>Giá chậu</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cancellation->order->orderDetails as $index => $detail)
                                <tr>
                                    <td>{{ $detail->product->name }}</td>
                                    <td>{{ $detail->product_height }} cm</td>
                                    <td>{{ $detail->product_pot ?? 'Không có' }}</td>
                                    <td>{{ number_format($detail->product_price ?? 0, 0, ',', '.') }} đ</td>
                                    <td>{{ number_format($detail->pot_price ?? 0, 0, ',', '.') }} đ</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ number_format($detail->price * $detail->quantity, 0, ',', '.') }} đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Nút hành động -->
            @if ($cancellation->status === 'pending')
                <div class="d-flex justify-content-end gap-2">
                    <!-- Nút duyệt -->
                    <form action="{{ route('admin.cancellations.approve', $cancellation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success">Duyệt</button>
                    </form>

                    <!-- Nút mở modal từ chối -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        Từ chối
                    </button>
                </div>

                <!-- Modal nhập lý do từ chối -->
                <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('admin.cancellations.reject', $cancellation->id) }}" method="POST" class="modal-content">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Nhập lý do từ chối</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <textarea name="admin_note" class="form-control" rows="3" placeholder="Nhập lý do từ chối..." required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-danger">Xác nhận từ chối</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
