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
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Đã hoàn hàng</option>
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
                <span class="badge bg-{{ $status['class'] }} py-2 px-3">{{ $status['label'] }}</span>
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
    }
</style>

@endsection
