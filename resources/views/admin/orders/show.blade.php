    {{-- resources/views/admin/page/order/show.blade.php --}}
    @extends('admin.layouts.dashboard')
    @section('title', 'Chi tiết đơn hàng')

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @section('content')
        <div class="container">
            <h2>Chi tiết đơn hàng #{{ $order->id }}</h2>
            <p><strong>Khách hàng:</strong> {{ $order->user->name ?? 'N/A' }}</p>
            <p><strong>SĐT:</strong> {{ $order->user->phone ?? '---' }}</p>
            <p><strong>Địa chỉ:</strong> {{ optional($order->user->addresses->first())->full_address ?? '---' }}</p>

            <strong>Trạng thái:</strong>
            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="d-inline-block">
                @csrf
                @method('PUT')
                <select name="status" onchange="this.form.submit()" class="form-select d-inline-block w-auto">
                    @foreach ([
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đang xử lý',
            'shipping' => 'Đang giao',
            'delivered' => 'Hoàn tất',
            'cancelled' => 'Đã huỷ',
            'returned' => 'Trả hàng'
        ] as $value => $label)
                        <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </form>
            </p>

            <hr>
            <h4>Sản phẩm</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Tên SP</th>
                        <th>Biến thể</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderDetails as $item)
                        <tr>
                            <td>{{ $item->productVariant->product->name ?? '---' }}</td>
                            <td>{{ $item->productVariant->pot  ?? '---' }}</td>
                            <td>{{ number_format($item->unit_price, 0, ',', '.') }} đ</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p><strong>Tổng trước giảm:</strong> {{ number_format($totalBeforeDiscount, 0, ',', '.') }} đ</p>
            <p><strong>Giảm giá:</strong> -{{ number_format($discount, 0, ',', '.') }} đ</p>
            <p><strong>Tổng thanh toán:</strong> {{ number_format($total, 0, ',', '.') }} đ</p>


            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    @endsection
