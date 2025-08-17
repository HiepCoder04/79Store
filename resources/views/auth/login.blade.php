<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>79Store - Đăng nhập</title>

    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>

@include('client.layouts.thongbao')

<body class="bg-gray-200">
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg blur border-radius-xl top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
                    <div class="container-fluid ps-2 pe-0">
                        <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3" href="/">
                            79Store
                        </a>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>
        </div>
    </div>

    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100"
            style="background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?auto=format&fit=crop&w=1950&q=80');">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto">
                <div class="row">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Đăng nhập</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('auth.loginPost') }}" class="text-start">
                                    @csrf

                                    {{-- Hiển thị lỗi validate (tiếng Việt) --}}
                                    @if ($errors->any())
                                        <div class="alert alert-danger text-sm">
                                            <ul class="mb-0 ps-3">
                                                @foreach ($errors->unique() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @elseif (session('error'))
                                        <div class="alert alert-danger text-sm">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    {{-- Email --}}
                                    <div class="input-group input-group-outline my-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}"
                                            class="form-control @error('email') is-invalid border-danger text-danger @enderror"
                                            required>
                                    </div>
                                    <!-- @error('email')
                                        <div class="text-danger text-sm ms-1">{{ $message }}</div>
                                    @enderror -->

                                    {{-- Mật khẩu --}}
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Mật khẩu</label>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid border-danger text-danger @enderror"
                                            required>
                                    </div>
                                    <!-- @error('password')
                                        <div class="text-danger text-sm ms-1">{{ $message }}</div>
                                    @enderror -->

                                    {{-- Ghi nhớ --}}
                                    <div class="form-check form-switch d-flex align-items-center mb-3">
                                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                        <label class="form-check-label mb-0 ms-3" for="rememberMe">Nhớ tài khoản</label>
                                    </div>

                                    {{-- Nút đăng nhập --}}
                                    <div class="text-center">
                                        <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">
                                            Đăng nhập
                                        </button>
                                    </div>

                                    <p class="mt-4 text-sm text-center">
                                        Bạn chưa có tài khoản?
                                        <a href="{{ route('auth.register') }}" class="text-primary text-gradient font-weight-bold">Đăng ký</a>
                                    </p>
                                    <br>
                                    <a href="{{ route('otp.request.form') }}">Quên mật khẩu?</a>

                                    {{-- Đăng nhập Google --}}
                                    <div class="text-center mt-3 mb-2">
                                        <a href="{{ route('auth.google') }}" class="btn btn-outline-dark w-100">
                                            <i class="fa fa-google me-2"></i> Đăng nhập với Google
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer position-absolute bottom-2 py-2 w-100">
                <div class="container">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-12 col-md-6 my-auto">
                            <div class="copyright text-center text-sm text-white text-lg-start">
                                © <script>document.write(new Date().getFullYear())</script>,
                                made with <i class="fa fa-heart" aria-hidden="true"></i> by
                                <a href="https://www.creative-tim.com" class="font-weight-bold text-white" target="_blank">Creative Tim</a>
                                for a better web.
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                                <li class="nav-item">
                                    <a href="https://www.creative-tim.com" class="nav-link text-white" target="_blank">Creative Tim</a>
                                </li>
                                <li class="nav-item">
                                    <a href="https://www.creative-tim.com/presentation" class="nav-link text-white" target="_blank">About Us</a>
                                </li>
                                <li class="nav-item">
                                    <a href="https://www.creative-tim.com/blog" class="nav-link text-white" target="_blank">Blog</a>
                                </li>
                                <li class="nav-item">
                                    <a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-white" target="_blank">License</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <!-- Core JS -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), { damping: '0.5' });
        }
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>
