@extends('client.layouts.default')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">Liên hệ với chúng tôi</h2>

    @if(session('success'))
        <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form method="POST" action="{{ route('client.contact.submit') }}">
        @csrf
        <div class="mb-3">
            <label for="name">Họ tên</label>
            <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
        </div>
        <div class="mb-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" required value="{{ old('email') }}">
        </div>
        <div class="mb-3">
            <label for="message">Nội dung</label>
            <textarea class="form-control" name="message" rows="5" required>{{ old('message') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi liên hệ</button>
    </form>
</div>
@endsection
