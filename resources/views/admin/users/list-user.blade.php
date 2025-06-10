@extends('admin.layouts.dashboard')

@section('title')
@parent 
QUẢN LÍ TÀI KHOẢN
@endsection

@push('style')
@endpush
    
@section('content')
<div class="row">
    <div class="ms-3">
        <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
        <p class="mb-4">DANH SÁCH TÀI KHOẢN</p>
    </div>

    <div class="table-responsive px-4">
        <table class="table table-sm table-bordered table-hover align-middle small">
            <thead class="table-dark text-center">
                <tr>
                    <th style="width: 40px;">ID</th>
                    <th style="width: 140px;">Họ tên</th>
                    <th style="width: 180px;">Email</th>
                    <th style="width: 120px;">SĐT</th>
                    <th style="width: 90px;">Quyền</th>
                    <th style="width: 110px;">Ngày sinh</th>
                    <th style="width: 110px;">Xác minh</th>
                    <th style="width: 120px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td class="text-center">{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">{{ $user->phone }}</td>
                    <td class="text-center">{{ ucfirst($user->role) }}</td>
                    <td class="text-center">{{ $user->date_of_birth ? $user->date_of_birth->format('d/m/Y') : '---' }}</td>
                    <td class="text-center">
                        @if($user->email_verified_at)
                            <span class="badge bg-success">✔</span>
                        @else
                            <span class="badge bg-danger">✘</span>
                        @endif
                    </td>
                    <td class="text-center">
                     
                        <form action="" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Không có tài khoản nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Phân trang --}}
        <div class="d-flex justify-content-end">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection


@push('script')
    
@endpush