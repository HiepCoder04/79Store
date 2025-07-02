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
                    <th style="width: 110px;">Trạng thái</th>
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
                    <td class="text-center">{{ $user->date_of_birth ? $user->date_of_birth->format('d/m/Y') : '---' }}
                    </td>
                    <td class="text-center">
                        @if($user->email_verified_at)
                        <span class="badge bg-success">✔</span>
                        @else
                        <span class="badge bg-danger">✘</span>
                        @endif
                    </td>
                    <td>
                        @if($user-> is_ban == true)
                        <p> Đã bị cấm</p>
                        @else
                        Hoạt động
                        @endif
                    </td>
                    <td class="text-center row">

                        @if($user->role != 'admin')
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary  col-sm-6" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">
                            Phân quyền
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('update-role') }}" method="POST"> {{-- Form BẮT ĐẦU từ đây --}}
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" value="{{ $user->id }}" name="id_user">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Chọn quyền cho user này
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            {{-- Gửi user_id --}}
                                            Chọn quyền
                                            <select name="role" class="form-control">
                                                <option value="1" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff
                                                </option>
                                                <option value="2" {{ $user->role == 'customer' ? 'selected' : '' }}>
                                                    Customer
                                                </option>
                                            </select>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Lưu quyền</button>
                                            {{-- Submit form --}}
                                        </div>
                                    </div>
                                </form> {{-- Form KẾT THÚC tại đây --}}
                            </div>
                        </div>

                        @if($user->is_ban == false)
                        <form action="{{ route('ban-user') }}" method="post">
                            @csrf
                            @method('PUT')
                            <input type="hidden" value="{{ $user->id }}" name="id_user">
                            <button class="btn btn-sm btn-secondary  col-sm-6">Cấm</button>
                        </form>
                        @elseif($user->is_ban == true)
                        <form action="{{ route('unban-user') }}" method="post">
                            @csrf
                            @method('PUT')
                            <input type="hidden" value="{{ $user->id }}" name="id_user">
                            <button class="btn btn-sm btn-secondary  col-sm-6">Mở cấm</button>
                        </form>
                        @endif


                        @endif

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