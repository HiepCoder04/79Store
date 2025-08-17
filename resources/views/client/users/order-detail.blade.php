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
                    <h5 class="mb-0">🧾 Mã đơn: <strong>{{ $order->order_code }}</strong></h5>
                    <small class="text-muted">📅 Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</small>
                </div>
                @php
                    $statusMap = [
                        'pending' => ['label' => 'Chờ xác nhận', 'class' => 'warning'],
                        'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'info'],
                        'shipping' => ['label' => 'Đang giao hàng', 'class' => 'primary'],
                        'delivered' => ['label' => 'Đã nhận hàng', 'class' => 'success'],
                        'cancel_requested' => ['label' => 'Yêu cầu hủy', 'class' => 'secondary'],
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

                {{-- Thanh tiến trình --}}
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

                {{-- Danh sách sản phẩm --}}
                <div class="mb-4">
                    <h5 class="fw-bold mb-3">🛍️ Sản phẩm đã mua</h5>
                    <div class="list-group">
                        @foreach ($order->orderDetails as $detail)
                            @php
                                $product = $detail->productVariant->product;
                                $image = optional($product->galleries->first())->image;
                                $imageUrl = $image
                                    ? (Str::startsWith($image, ['http', '/']) ? $image : asset($image))
                                    : asset('assets/img/bg-img/default.jpg');

                                $potPrice = 0;
                                $potName = null;
                                if ($detail->product_pot && strtolower($detail->product_pot) !== 'không có chậu') {
                                    $potName = $detail->product_pot;
                                    $potModel = \App\Models\Pot::where('name', $potName)->first();
                                    $potPrice = $potModel?->price ?? 0;
                                }
                                $priceCay = $detail->price;
                            @endphp

                            <div class="list-group-item border rounded mb-3 shadow-sm">
                                <div class="row g-3 align-items-center">
                                    <!-- Ảnh sản phẩm -->
                                    <div class="col-md-2 col-4">
                                        <img src="{{ $imageUrl }}"
                                            onerror="this.onerror=null;this.src='{{ asset('assets/img/default.jpg') }}';"
                                            alt="{{ $product->name }}"
                                            class="rounded border w-100" style="aspect-ratio: 1/1; object-fit: cover;">
                                    </div>

                                    <!-- Thông tin sản phẩm -->
                                    <div class="col-md-6 col-8">
                                        <h6 class="mb-1 fw-bold">{{ $product->name }}</h6>
                                        <div class="small text-muted">Chiều cao: {{ $detail->product_height }} cm</div>
                                        @if ($potName)
                                            <div class="small text-muted">Chậu: {{ $potName }}</div>
                                        @endif
                                        <div class="small text-muted">Số lượng: {{ $detail->quantity }}</div>
                                    </div>

                                    <!-- Giá -->
                                    <div class="col-md-4 text-end">
                                        <div class="small">Giá cây: 
                                            <strong>{{ number_format($detail->product_price ?? 0, 0, ',', '.') }}đ</strong>
                                        </div>
                                        @if ($detail->pot_price > 0)
                                            <div class="small">Giá chậu: 
                                                <strong>{{ number_format($detail->pot_price, 0, ',', '.') }}đ</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tổng cộng --}}
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
                </div>

                {{-- Nút hành động --}}
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <!-- Nút quay lại -->
                    <a href="{{ route('client.orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại đơn hàng
                    </a>

                    <!-- Hủy đơn hàng -->
                    @if (in_array($order->status, ['pending','confirmed']))
                        <button type="button" class="btn btn-outline-danger" 
                                data-bs-toggle="modal" data-bs-target="#cancelModal-{{ $order->id }}">
                            <i class="fa fa-times-circle me-1"></i> Hủy đơn hàng
                        </button>

                        <!-- Modal hủy -->
                        <div class="modal fade" id="cancelModal-{{ $order->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Hủy đơn hàng #{{ $order->order_code }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($order->status === 'pending')
                                                <p>Bạn có chắc chắn muốn hủy đơn hàng này không?</p>
                                            @else
                                                <div class="mb-3">
                                                    <label class="form-label">Lý do hủy</label>
                                                    <textarea name="reason" class="form-control" rows="3" required></textarea>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Mua lại -->
                    @if ($order->status === 'cancelled')
                        <form method="POST" action="{{ route('client.orders.reorder', $order->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fa fa-shopping-cart me-1"></i> Mua lại đơn hàng
                            </button>
                        </form>
                    @endif

                    <!-- Hoàn hàng -->
                    @if ($order->status === 'delivered')
                        {{-- Nút + modal TẠO YÊU CẦU TRẢ HÀNG THEO DÒNG HÀNG --}}
                        @include('client.orders.partials.return_button')

                        {{-- Link xem lịch sử yêu cầu trả hàng --}}
                        <a class="btn btn-link" href="{{ route('client.orders.returns.index', $order) }}">
                            Lịch sử trả hàng
                        </a>
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
