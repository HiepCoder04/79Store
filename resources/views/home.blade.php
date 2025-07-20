@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white fs-5">
                        Xin chào, {{ Auth::user()->name }}
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <p class="mb-3">Bạn đã đăng nhập thành công vào hệ thống.</p>
                        <ul class="list-group list-group-flush mt-4">
                            <li class="list-group-item">Email: {{ Auth::user()->email }}</li>
                            <li class="list-group-item">Ngày tham gia: {{ Auth::user()->created_at->format('d/m/Y') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
