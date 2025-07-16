<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>79Store - Gửi mã OTP</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Nucleo + FontAwesome -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <!-- Material Dashboard CSS -->
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e1e2f, #3a3a55);
            color: #fff;
        }

        .page-header {
            background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .page-header .mask {
            background: rgba(0, 0, 0, 0.6);
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            color: #fff;
        }

        .card-header {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .card-header h4 {
            font-weight: 700;
        }

        .form-label {
            font-weight: 500;
            color: #ddd;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.25);
            box-shadow: none;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            border: none;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #ff4b2b, #ff416c);
        }

        .alert {
            font-size: 0.9rem;
        }
    </style>
</head>

<body class="bg-gray-200">
    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100">
            <span class="mask bg-gradient-dark opacity-6"></span>
            <div class="container my-auto">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-7">
                        <div class="card z-index-0">
                            <div class="card-header text-center py-4">
                                <h4 class="text-white mb-0">Yêu cầu mã OTP</h4>
                            </div>
                            <div class="card-body px-4 py-4">
                                @if(session('status'))
                                    <div class="alert alert-success">{{ session('status') }}</div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('otp.request') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Địa chỉ Email</label>
                                        <input type="email" id="email" name="email" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mt-3">Gửi mã OTP</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer position-absolute bottom-2 py-2 w-100 text-white text-center">
                <div class="container">
                    ©
                    <script>document.write(new Date().getFullYear())</script> 79Store. Powered by Creative Tim.
                </div>
            </footer>
        </div>
    </main>

    <!-- Core JS Files -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>

</html>