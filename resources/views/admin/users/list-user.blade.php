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
            <h5 class="mb-0">QUẢN LÍ TÀI KHOẢN</h5>
        </div>

        <div class="table-responsive">


            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Tìm tên, email, SĐT...">
                </div>

                <div class="col-md-2">
                    <select name="role" class="form-select">
                        <option value="">-- Quyền --</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="is_ban" class="form-select">
                        <option value="">-- Trạng thái --</option>
                        <option value="0" {{ request('is_ban') === '0' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="1" {{ request('is_ban') === '1' ? 'selected' : '' }}>Bị cấm</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Lọc</button>
                    <a href="{{ route('admin.users.list') }}" class="btn btn-secondary">Xóa lọc</a>
                </div>
            </form>

            {{-- Thông báo --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>SĐT</th>
                        <th>Quyền</th>
                        <th>Trạng thái</th>
                        <th>Lý do cấm</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                @if($user->is_ban)
                                    <span class="badge bg-danger">Đã cấm</span>
                                @else
                                    <span class="badge bg-success">Hoạt động</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_ban && $user->ban_reason)
                                    <span title="{{ $user->ban_reason }}">
                                        {{ \Illuminate\Support\Str::limit($user->ban_reason, 20) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(!$user->is_ban)
                                    <!-- Nút Ban (mở modal nhập lý do) -->
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#banModal"
                                        data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                        Cấm
                                    </button>
                                @else
                                    <!-- Nút Unban -->
                                    <form action="{{ route('admin.users.unban') }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="id_user" value="{{ $user->id }}">
                                        <button type="submit" class="btn btn-sm btn-success"
                                            onclick="return confirm('Mở cấm user {{ $user->name }}?')">
                                            Mở cấm
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        <!-- Modal nhập lý do cấm -->
        <div class="modal fade" id="banModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form action="{{ route('admin.users.ban') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id_user" id="banUserId">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Cấm người dùng</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p id="banUserName"></p>
                            <div class="mb-3">
                                <label for="reason" class="form-label">Lý do cấm</label>
                                <textarea name="reason" id="reason" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">Xác nhận</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var banModal = document.getElementById('banModal');
                banModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var userId = button.getAttribute('data-user-id');
                    var userName = button.getAttribute('data-user-name');

                    document.getElementById('banUserId').value = userId;
                    document.getElementById('banUserName').innerText = "Bạn có chắc chắn muốn cấm user: " + userName + " ?";
                });
            });
        </script>

        <div class="d-flex justify-content-end mt-3">
            {{ $users->links() }}
        </div>
    </div>
@endsection