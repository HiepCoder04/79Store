@extends('admin.layouts.dashboard')

@section('title')
@parent
QUẢN LÍ TÀI KHOẢN
@endsection

@section('content')
<style>
.table-container {
    background: #fff;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    margin-top: 30px;
}

.custom-table thead {
    background-color: #e2e3e5;
    color: #000;
    font-weight: 600;
}

.custom-table th,
.custom-table td {
    text-align: center;
    vertical-align: middle;
    padding: 14px;
}

.badge-status {
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 500;
}

.badge-active {
    background-color: #28a745;
    color: #fff;
}

.badge-banned {
    background-color: #dc3545;
    color: #fff;
}

.dropdown .btn {
    border-radius: 8px;
    padding: 6px 12px;
}

.btn-role {
    padding: 4px 10px;
}

.modal .form-control {
    border-radius: 10px;
}

.table-actions {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}
</style>

<div class="container table-container">
    <div class="mb-4">
        <h5 class="mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>QUẢN LÍ TÀI KHOẢN</h5>
        <p class="text-muted">Danh sách tài khoản người dùng</p>
    </div>

    <div class="table-responsive">
        <form method="GET" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm tên, email, SĐT...">
    </div>

    <div class="col-md-2">
        <select name="role" class="form-select">
            <option value="">-- Quyền --</option>
            <option value="admin" {{ request('role')=='admin' ? 'selected' : '' }}>Admin</option>
            <option value="customer" {{ request('role')=='customer' ? 'selected' : '' }}>Customer</option>
        </select>
    </div>

    <div class="col-md-2">
        <select name="is_ban" class="form-select">
            <option value="">-- Trạng thái --</option>
            <option value="0" {{ request('is_ban')==='0' ? 'selected' : '' }}>Hoạt động</option>
            <option value="1" {{ request('is_ban')==='1' ? 'selected' : '' }}>Bị cấm</option>
        </select>
    </div>

    <div class="col-md-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Lọc</button>
        <a href="{{ route('admin.users.list') }}" class="btn btn-secondary">Xóa lọc</a>
    </div>
</form>
        <table class="table custom-table table-bordered align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Quyền</th>
                    <th>Ngày sinh</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td class="text-start">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>{{ $user->date_of_birth ? $user->date_of_birth->format('d/m/Y') : '—' }}</td>
                    <td>
                        @if($user->is_ban)
                        <span class="badge-status badge-banned">Đã bị cấm</span>
                        @else
                        <span class="badge-status badge-active">Hoạt động</span>
                        @endif
                    </td>
                    <td>
                        @if($user->role != 'admin')
                        <div class="table-actions">
                            {{-- Ban / Unban --}}
                            <form action="{{ route($user->is_ban ? 'unban-user' : 'ban-user') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="id_user" value="{{ $user->id }}">
                                <button class="btn btn-sm {{ $user->is_ban ? 'btn-success' : 'btn-danger' }}">
                                    {{ $user->is_ban ? 'Mở cấm' : 'Cấm' }}
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">Không có tài khoản nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection