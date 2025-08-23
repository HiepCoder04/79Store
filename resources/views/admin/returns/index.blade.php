@extends('admin.layouts.dashboard')

@section('content')
<h4>Quản Lí Trả hàng</h4>

<form class="row g-2 mb-3" method="GET">
  <div class="col-auto">
    <select name="status" class="form-select" onchange="this.form.submit()">
      <option value="">-- Tất cả trạng thái --</option>
      <option value="pending" @selected(request('status')==='pending')>Chờ Duyệt</option>
      <option value="approved" @selected(request('status')==='approved')>Đã duyệt</option>
      <option value="rejected" @selected(request('status')==='rejected')>Bị từ chối</option>
      <option value="refunded" @selected(request('status')==='refunded')>Đã hoàn tiền</option>
      <option value="exchanged" @selected(request('status')==='exchanged')>Đã đổi trả</option>
    </select>
  </div>
  <div class="col-auto">
    <button class="btn btn-primary">Lọc</button>
  </div>
</form>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th><th>Mã đơn hàng</th><th>Tài khoản</th><th>SĐT</th><th>Sản phẩm</th><th>Số lượng trả</th><th>Giá trị ước tính</th><th>Trạng thái</th><th>Ngày</th><th></th>
    </tr>
  </thead>
  <tbody>
    @foreach($items as $it)
      @php
        // ✅ Tính đúng giá trị hoàn tiền ước tính
        $estimatedRefund = 0;
        if ($it->orderDetail) {
            $productPrice = $it->orderDetail->product_price ?? 0;
            $potPrice = $it->orderDetail->pot_price ?? 0;
            $plantRefund = $productPrice * ($it->plant_quantity ?? 0);
            $potRefund = $potPrice * ($it->pot_quantity ?? 0);
            $estimatedRefund = $plantRefund + $potRefund;
        }
        
        // ✅ Lấy số điện thoại ưu tiên từ đơn hàng
        $phone = $it->order->phone ?? $it->user->phone ?? null;
      @endphp
      <tr>
        <td>{{ $it->id }}</td>
        <td>{{ $it->order->order_code }}</td>
        <td>{{ $it->user->name ?? 'User' }}</td>
        <td>
          @if($phone)
            <a href="tel:{{ $phone }}" class="text-decoration-none">
              <i class="fas fa-phone-alt text-primary"></i> {{ $phone }}
            </a>
          @else
            <span class="text-muted">-</span>
          @endif
        </td>
        <td>{{ $it->product->name ?? 'Sản phẩm' }}</td>
        <td>
          {{-- ✅ Hiển thị chi tiết SL trả với tên cụ thể --}}
          @if($it->plant_quantity > 0 && $it->pot_quantity > 0)
            <div class="fw-bold text-success">🌱 {{ $it->plant_quantity }} × {{ $it->product->name ?? 'Cây' }}</div>
            <div class="fw-bold text-info">🪴 {{ $it->pot_quantity }} × {{ $it->orderDetail->product_pot ?? 'Chậu' }}</div>
            <small class="badge bg-light text-dark">Cả cây + chậu</small>
          @elseif($it->plant_quantity > 0)
            <div class="fw-bold text-success">
              🌱 {{ $it->plant_quantity }} × {{ $it->product->name ?? 'Cây' }}
            </div>
            <small class="badge bg-success">Chỉ cây</small>
          @elseif($it->pot_quantity > 0)
            <div class="fw-bold text-info">
              🪴 {{ $it->pot_quantity }} × {{ $it->orderDetail->product_pot ?? 'Chậu' }}
            </div>
            <small class="badge bg-info">Chỉ chậu</small>
          @else
            <div class="text-muted">
              ❓ {{ $it->quantity }} (không rõ)
            </div>
            <small class="badge bg-secondary">Dữ liệu cũ</small>
          @endif
        </td>
        <td>
          {{-- ✅ Hiển thị giá trị ước tính đúng --}}
          {{ number_format($estimatedRefund, 0, ',', '.') }}đ
        </td>
        <td>
          @switch($it->status)
            @case('pending')
              <span class="badge bg-warning">Chờ Duyệt</span>
              @break
            @case('approved')
              <span class="badge bg-info">Đã duyệt</span>
              @break
            @case('rejected')
              <span class="badge bg-secondary">Bị từ chối</span>
              @break
            @case('refunded')
              <span class="badge bg-success">Đã hoàn tiền</span>
              @break
            @case('exchanged')
              <span class="badge bg-primary">Đã đổi trả</span>
              @break
            @default
              <span class="badge bg-dark">{{ $it->status }}</span>
          @endswitch
        </td>
        <td>{{ $it->created_at->format('d/m/Y H:i') }}</td>
        <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.returns.show', $it->id) }}">Xem</a></td>
      </tr>
    @endforeach
  </tbody>
</table>

{{ $items->links() }}
@endsection
