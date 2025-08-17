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
    <input type="number" name="order_id" value="{{ request('order_id') }}" class="form-control" placeholder="Order ID">
  </div>
  <div class="col-auto">
    <button class="btn btn-primary">Lọc</button>
  </div>
</form>

<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th><th>Order</th><th>User</th><th>SP</th><th>SL</th><th>Trạng thái</th><th>Ngày</th><th></th>
    </tr>
  </thead>
  <tbody>
    @foreach($items as $it)
      <tr>
        <td>{{ $it->id }}</td>
        <td>#{{ $it->order_id }}</td>
        <td>{{ $it->user->name ?? 'User' }}</td>
        <td>{{ $it->product->name ?? 'Sản phẩm' }}</td>
        <td>{{ $it->quantity }}</td>
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
