@extends('client.layouts.default')
@section('title', 'Đơn hàng của tôi')
@php use Illuminate\Support\Str; @endphp

@section('content')

<!-- Banner đầu trang -->
<section class="breadcrumb-area">
    <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
         style="background-image: url('{{ asset('assets/img/bg-img/24.jpg') }}'); height: 250px;">
        <h2 class="text-white">Đơn hàng của tôi</h2>
    </div>
</section>

<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">🛒 Lịch sử đơn hàng</h2>
    <form method="GET" class="d-flex flex-wrap gap-2">
        <div>
            <select name="status" class="form-select">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending"   {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                <option value="shipping"  {{ request('status') === 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Đã nhận hàng</option>
                <option value="cancel_requested" {{ request('status') === 'cancel_requested' ? 'selected' : '' }}>Yêu cầu hủy</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Đã hoàn hàng</option>
                {{-- ✅ THÊM CÁC OPTION LỌC TRẢ HÀNG --}}
                <option value="delivered_with_returns" {{ request('status') === 'delivered_with_returns' ? 'selected' : '' }}>Đã nhận - Có trả hàng</option>
                <option value="delivered_fully_returned" {{ request('status') === 'delivered_fully_returned' ? 'selected' : '' }}>Đã nhận - Hoàn trả hết</option>
                <option value="delivered_partial_returned" {{ request('status') === 'delivered_partial_returned' ? 'selected' : '' }}>Đã nhận - Trả một phần</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Lọc</button>
            <a href="{{ route('client.orders.index') }}" class="btn btn-secondary">Đặt lại</a>
        </div>
    </form>
</div>
    @forelse ($orders as $order)
        @php
              $statusMap = [
                'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'warning'],
                'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'info'],
                'shipping'  => ['label' => 'Đang giao hàng', 'class' => 'primary'],
                'delivered' => ['label' => 'Đã nhận hàng', 'class' => 'success'],
                'cancel_requested' => ['label' => 'Yêu cầu hủy', 'class' => 'secondary'],
                'cancelled' => ['label' => 'Đã hủy', 'class' => 'danger'],
                'returned'  => ['label' => 'Đã hoàn hàng', 'class' => 'secondary'], // đổi từ "Trả hàng" → "Đã hoàn hàng"
            ];

            $status = $statusMap[$order->status] ?? ['label' => 'Không xác định', 'class' => 'dark'];

            $steps = ['pending', 'confirmed', 'shipping', 'delivered'];
            $currentIndex = array_search($order->status, $steps);

        @endphp

        <div class="card mb-4 shadow-sm border">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <div>
                    <h5 class="mb-0">🧾 Mã đơn: <strong>{{ $order->order_code }}</strong></h5>
                    <small class="text-muted">📅 Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $status['class'] }} py-2 px-3">{{ $status['label'] }}</span>
                    {{-- ✅ THÊM NHÃN PHỤ VỚI LINK CHO ĐƠN HÀNG ĐÃ GIAO --}}
                    @if($order->status === 'delivered' && $order->return_badge_text)
                        <br>
                        <a href="{{ route('client.orders.returns.index', $order->id) }}" 
                           class="badge bg-secondary mt-1 px-2 py-1 text-decoration-none" 
                           title="Xem lịch sử trả hàng">
                            {{ $order->return_badge_text }}
                        </a>
                    @endif
                </div>
            </div>




            <div class="card-body">
                <p class="mb-1">🧍 <strong>Người nhận:</strong> {{ $order->name ?? $order->user->name }}</p>
                <p class="mb-1">☎️ <strong>Điện thoại:</strong> {{ $order->phone }}</p>
                <p class="mb-3">📍 <strong>Địa chỉ:</strong> {{ $order->address->address ?? 'Không có' }}</p>

                @if ($currentIndex !== false)
                    <div class="steps d-flex justify-content-between mb-4">
                        @foreach ($steps as $index => $step)
                            @php
                                $stepLabel = $statusMap[$step]['label'];
                                $isActive = $index <= $currentIndex;
                            @endphp
                            <div class="text-center flex-fill">
                                <div class="step-circle {{ $isActive ? 'step-active' : 'step-inactive' }}">
                                    {{ $index + 1 }}
                                </div>
                                <small class="step-label {{ $isActive ? 'text-success fw-bold' : 'text-muted' }}">
                                    {{ $stepLabel }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <strong>Tổng cộng:</strong>
                        <span class="text-danger fs-5">{{ number_format($order->total_after_discount, 0, ',', '.') }}đ</span>
                    </div>
                    <a href="{{ route('client.orders.show', $order->id) }}" class="btn btn-outline-dark btn-sm">📄 Xem chi tiết</a>
                </div>
            </div>
        </div>

    @empty
        <p class="text-muted">Bạn chưa có đơn hàng nào.</p>
    @endforelse

    {{-- ✅ THÊM PHÂN TRANG --}}
    @if($orders->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                <small class="text-muted">
                    Hiển thị {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} 
                    trong tổng số {{ $orders->total() }} đơn hàng
                </small>
            </div>
            <nav aria-label="Phân trang đơn hàng">
                {{ $orders->appends(request()->query())->links('pagination::bootstrap-4') }}
            </nav>
        </div>
    @endif
</div>

{{-- Style cho tiến trình --}}
<style>
    .steps {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .step-circle {
        width: 38px;
        height: 38px;
        line-height: 38px;
        border-radius: 50%;
        font-weight: bold;
        font-size: 16px;
        text-align: center;
        margin: 0 auto;
        transition: all 0.3s ease;
    }

    .step-label {
        display: block;
        font-size: 13px;
        margin-top: 4px;
    }

    .step-active {
        background-color: #28a745;
        color: white;
    }

    .step-inactive {
        background-color: #e9ecef;
        color: #6c757d;
    }

    /* ✅ THÊM STYLE CHO NHÃN PHỤ LINK */
    .badge.bg-secondary:hover {
        background-color: #495057 !important;
        transform: scale(1.05);
        transition: all 0.2s ease;
    }

    .badge.text-decoration-none:hover {
        text-decoration: underline !important;
    }

    /* ✅ THÊM STYLE CHO PHÂN TRANG */
    .pagination .page-link {
        color: #28a745;
        border-color: #28a745;
        border-radius: 0.5rem;
        margin: 0 2px;
    }

    .pagination .page-link:hover {
        color: #fff;
        background-color: #28a745;
        border-color: #28a745;
    }

    .pagination .page-item.active .page-link {
        background-color: #28a745;
        border-color: #28a745;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        border-color: #dee2e6;
    }

    @media (max-width: 576px) {
        .steps {
            flex-direction: column;
            gap: 12px;
        }

        .step-circle {
            width: 30px;
            height: 30px;
            font-size: 14px;
            line-height: 30px;
        }
        
        /* Mobile pagination */
        .pagination {
            font-size: 0.875rem;
        }
        
        .pagination .page-link {
            padding: 0.375rem 0.5rem;
        }
    }
</style>

@endsection
