@extends('client.layouts.default')

@section('title', 'Thông tin người dùng')

@section('content')
<div class="breadcrumb-area">
        <div class="top-breadcrumb-area bg-img bg-overlay d-flex align-items-center justify-content-center"
            style="background-image: url({{ asset('assets/img/bg-img/24.jpg') }});">
            <h2>Thông tin tài khoản</h2>
        </div>

        <div class="container py-4">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fa fa-home"></i> Trang chủ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thông tin tài khoản</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
<div class="container mt-5 mb-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            {{-- Hiển thị lỗi --}}
            <div id="form-errors" class="alert alert-danger mb-4 d-none">
                <ul class="mb-0" id="form-errors-list"></ul>
            </div>

            {{-- Hiển thị thông báo thành công --}}
            <div id="form-success" class="alert alert-success mb-4 d-none">
                ✅ Cập nhật thành công!
            </div>

            <form id="account-form" action="{{ route('client.account.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Avatar --}}
                <div class="mb-4 text-center">
                    <img id="avatar-preview"
                         src="{{ auth()->user()->avatar ? asset('img/avatars/' . auth()->user()->avatar) : asset('img/default-avatar.png') }}"
                         alt="Avatar" class="rounded-circle shadow-sm header-avatar-img"
                         width="120" height="120" style="object-fit: cover;">
                    <div class="mt-2 w-50 mx-auto">
                        <input type="file" name="avatar" class="form-control" accept="image/*" onchange="previewAvatar(event)">
                    </div>
                </div>

                {{-- Họ tên --}}
                <div class="form-group mb-3">
                    <label for="name" class="form-label fw-bold">Họ và tên</label>
                    <input type="text" name="name" id="name" class="form-control"
                           value="{{ old('name', auth()->user()->name) }}" required>
                </div>

                {{-- Email --}}
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Email (không thay đổi)</label>
                    <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                </div>

                {{-- Số điện thoại --}}
                <div class="form-group mb-3">
                    <label for="phone" class="form-label fw-bold">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                           value="{{ old('phone', auth()->user()->phone) }}">
                </div>

                {{-- Ngày sinh --}}
                <div class="form-group mb-4">
                    <label for="date_of_birth" class="form-label fw-bold">Ngày sinh</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control"
                           value="{{ old('date_of_birth', auth()->user()->date_of_birth ? \Carbon\Carbon::parse(auth()->user()->date_of_birth)->format('Y-m-d') : '') }}">
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewAvatar(event) {
        const reader = new FileReader();
        reader.onload = function(){
            document.getElementById('avatar-preview').src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    $('#account-form').on('submit', function(e) {
        e.preventDefault();

        $('#form-errors').addClass('d-none');
        $('#form-success').addClass('d-none');
        $('#form-errors-list').empty();

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
                    $('#form-success').removeClass('d-none');
                    if (response.avatar_url) {
                        $('.header-avatar-img').attr('src', response.avatar_url);
                        $('#avatar-preview').attr('src', response.avatar_url);
                    }
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $('#form-errors-list').append(`<li>${errors[field][0]}</li>`);
                    }
                    $('#form-errors').removeClass('d-none');
                } else {
                    alert('Đã xảy ra lỗi, vui lòng thử lại sau!');
                }
            }
        });
    });
</script>
@endpush
