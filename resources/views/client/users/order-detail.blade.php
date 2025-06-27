@extends('client.layouts.default')
@section('title', 'Chi tiết đơn hàng')
@php use Illuminate\Support\Str; @endphp

@section('content')
<!-- Banner đầu trang -->
<section class="breadcrumb-area">
    <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
         style="background-image: url('{{ asset('assets/img/bg-img/24.jpg') }}'); height: 250px;">
        <h2 class="text-white">Chi tiết đơn hàng</h2>
    </div>
</section>

<div class="container py-5">
    <div class="card mb-4 shadow-sm border">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <div>
                <h5 class="mb-0">🧾 Mã đơn: <strong>#ORD-{{ $order->id }}</strong></h5>
                <small class="text-muted">📅 Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</small>
            </div>
            @php
                $statusMap = [
                    'pending' => ['label' => 'Chờ thanh toán', 'class' => 'warning'],
                    'confirmed' => ['label' => 'Chờ xác nhận', 'class' => 'info'],
                    'shipping' => ['label' => 'Đang giao hàng', 'class' => 'primary'],
                    'delivered' => ['label' => 'Đã nhận hàng', 'class' => 'success'],
                    'cancelled' => ['label' => 'Đã hủy', 'class' => 'danger'],
                    'returned' => ['label' => 'Trả hàng', 'class' => 'secondary'],
                ];
                $steps = ['pending', 'confirmed', 'shipping', 'delivered'];
                $currentIndex = array_search($order->status, $steps);
                $status = $statusMap[$order->status] ?? ['label' => 'Không xác định', 'class' => 'dark'];
            @endphp
            <span class="badge bg-{{ $status['class'] }} py-2 px-3">{{ $status['label'] }}</span>
        </div>

        <div class="card-body">
            <p class="mb-1">🧍 <strong>Người nhận:</strong> {{ $order->name ?? $order->user->name }}</p>
            <p class="mb-1">☎️ <strong>Điện thoại:</strong> {{ $order->phone }}</p>
            <p class="mb-3">📍 <strong>Địa chỉ:</strong> {{ $order->address->address ?? 'Không có địa chỉ' }}</p>

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

            <div class="mb-4">
                <h5 class="fw-bold mb-3">🛍️ Sản phẩm đã mua</h5>
                @foreach($order->orderDetails as $detail)
                    @php
                        $product = $detail->productVariant->product;
                        $image = $product->galleries->first()->image ?? 'assets/img/bg-img/default.jpg';
                        $imageUrl = Str::startsWith($image, 'http') ? $image : asset($image);
                        $total = $detail->price * $detail->quantity;
                    @endphp
                    <div class="row align-items-center border-bottom pb-3 mb-3">
                        <div class="col-auto">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="rounded border" width="72" height="72">
                        </div>
                        <div class="col">
                            <h6 class="mb-1">{{ $product->name }}</h6>
                            <div class="text-muted small">Chậu: {{ $detail->productVariant->pot }}</div>
                            <div class="text-muted small">Số lượng: {{ $detail->quantity }}</div>
                        </div>
                        <div class="col text-end">
                            <div>{{ number_format($detail->price, 0, ',', '.') }}đ</div>
                            <div class="fw-bold">{{ number_format($total, 0, ',', '.') }}đ</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-end">
                <div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <strong>{{ number_format($order->total_before_discount, 0, ',', '.') }}đ</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Giảm giá:</span>
                        <strong>-{{ number_format($order->discount_amount, 0, ',', '.') }}đ</strong>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2">
                        <span class="fw-bold">Tổng cộng:</span>
                        <span class="fw-bold text-danger">{{ number_format($order->total_after_discount, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại đơn hàng
                </a>
                @if($order->status === 'pending')
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline-block ms-2">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này không?')">
                            <i class="fa fa-times-circle me-1"></i> Hủy đơn hàng
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

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
</style>
@endsection
