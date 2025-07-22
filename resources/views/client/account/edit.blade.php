@extends('client.layouts.default')

@section('content')
    <section class="my-5">
        <div class="container">
            <h4 class="mb-4">Chỉnh sửa thông tin cá nhân</h4>
            <form id="account-form" action="{{ route('client.account.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Ảnh đại diện -->
                    <div class="col-md-3 mb-4 text-center">
                        <img id="avatar-preview"
                            src="{{ $user->avatar ? asset('img/avatars/' . $user->avatar) : asset('assets/img/default-avatar.png') }}"
                            alt="Avatar" class="img-thumbnail"
                            style="width: 150px; height: 150px; object-fit: cover;">
                        <input type="file" name="avatar" class="form-control mt-2" accept="image/*"
                            onchange="previewAvatar(event)">
                    </div>

                    <div class="col-md-9">
                        <div class="form-group mb-3">
                            <label for="name">Họ và tên</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                        </div>

                        <div class="form-group mb-3">
                            <label for="date_of_birth">Ngày sinh</label>
                            <input type="date" name="date_of_birth" class="form-control"
                                value="{{ $user->date_of_birth }}">
                        </div>

                        <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                        <a href="{{ route('client.account.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Hiển thị ảnh preview khi chọn ảnh mới
        function previewAvatar(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('avatar-preview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        $('#account-form').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        if (response.avatar_url) {
                            $('#avatar-preview').attr('src', response.avatar_url);

                            // Cập nhật cả ảnh đại diện trên header nếu có
                            $('#header-avatar').attr('src', response.avatar_url);
                        }

                        alert('✅ Cập nhật thành công!');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let msg = '';
                        for (let field in errors) {
                            msg += '- ' + errors[field][0] + '\n';
                        }
                        alert('❌ Lỗi:\n' + msg);
                    } else {
                        alert('❌ Đã xảy ra lỗi, vui lòng thử lại!');
                    }
                }
            });
        });
    </script>
@endpush
