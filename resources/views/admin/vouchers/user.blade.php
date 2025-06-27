@extends('admin.layouts.dashboard')

@section('content')
<div class="container">
    <h3>Người dùng đã sử dụng mã: {{ $voucher->code }}</h3>
<table class="table">
  <thead><tr><th>Tên</th><th>Email</th><th>Thời gian dùng</th></tr></thead>
  <tbody>
    @forelse($users as $u)
      <tr>
        <td>{{ $u->name }}</td>
        <td>{{ $u->email }}</td>
        <td>{{ $u->used_at }}</td>
      </tr>
    @empty
      <tr><td colspan="3">Chưa có ai sử dụng mã này.</td></tr>
    @endforelse
  </tbody>
</table>

</div>
@endsection
