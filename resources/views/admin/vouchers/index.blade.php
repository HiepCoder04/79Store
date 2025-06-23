@extends('admin.layouts.dashboard')

@section('content')
@php use Carbon\Carbon; @endphp
<div class="container">
    <h2>Danh sách voucher</h2>
    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary mb-3">Thêm voucher</a>
 <table class="table ">
  <thead>
    <tr>
      <th>Mã</th>
      <th>Phần trăm giảm giá</th>
      <th>Thời gian bắt đầu giảm</th>
      <th>Thời gian kết thúc</th>
      <th>giảm giá phần trăm</th>
      <th>giảm giá tối đa</th>
      <th>Giá trị hóa đơn tối thiểu</th>
      <th>Trạng thái</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    @foreach($vouchers as $v)
      <tr>
        <td>{{ $v->code }}</td>
        <td>{{ $v->discount_percent }}%</td>
<td>{{ Carbon::parse($v->start_date)->format('d-m-Y') }}</td>
<td>{{ Carbon::parse($v->end_date)->format('d-m-Y') }}</td>
        <td>{{$v->discount_percent}}</td>
        <td>{{$v->max_discount}}</td>
        <td>{{$v->min_order_amount}}</td>
        <td>{{ $v->is_active ? 'Hoạt động' : 'Ngừng' }}</td>
        
        <td>
          <a href="{{ route('admin.vouchers.edit', $v->id) }}" class="btn btn-warning">Sửa</a> |
          <a href="{{ route('admin.vouchers.users', $v->id) }}" class="btn btn-secondary">Người đã dùng</a>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

</div>
@endsection
