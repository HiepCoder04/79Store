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
        <table class="table custom-table table-bordered align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Quyền</th>
                    <th>Ngày sinh</th>
                    <th>Xác minh</th>
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
                        @if($user->email_verified_at)
                        <span class="badge-status badge-active">✔ Xác minh</span>
                        @else
                        <span class="badge-status badge-banned">✘ Chưa xác minh</span>
                        @endif
                    </td>
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

                            {{-- Phân quyền --}}
                            <!-- <button type="button" class="btn btn-sm btn-outline-primary btn-role" data-bs-toggle="modal" data-bs-target="#modal-{{ $user->id }}">
                                Phân quyền
                            </button> -->

                            {{-- Modal phân quyền --}}
                            <div class="modal fade" id="modal-{{ $user->id }}" tabindex="-1"
                                aria-labelledby="modalLabel{{ $user->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form action="{{ route('update-role') }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="id_user" value="{{ $user->id }}">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel{{ $user->id }}">Chọn quyền cho
                                                    user</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Đóng"></button>
                                            </div>
                                            <div class="modal-body">
                                                <select name="role" class="form-control">
                                                    <option value="1" {{ $user->role == 'staff' ? 'selected' : '' }}>
                                                        Staff</option>
                                                    <option value="2" {{ $user->role == 'customer' ? 'selected' : '' }}>
                                                        Customer</option>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Huỷ</button>
                                                <button type="submit" class="btn btn-primary">Lưu</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

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