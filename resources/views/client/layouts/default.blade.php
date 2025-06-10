<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
<meta name="description" content="">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Title -->
<title>79Store</title>

<!-- Favicon -->
<link rel="icon" href="{{ asset('assets/img/core-img/favicon.ico') }}">

<!-- Core Stylesheet -->
<link rel="stylesheet" href="{{ asset('assets/style.css') }}">

</head>

<body>
    <!-- Preloader -->
    <div class="preloader d-flex align-items-center justify-content-center">
        <div class="preloader-circle"></div>
        <div class="preloader-img">
            <img src="img/core-img/leaf.png" alt="">
        </div>
    </div>

    <!-- ##### Header Area Start ##### -->
    @include('client.layouts.header')
    <!-- ##### Header Area End ##### -->
    @include('client.layouts.banner')
    @yield('content')
    
    @include('client.layouts.footer')
    <!-- ##### Footer Area End ##### -->

    <!-- ##### All Javascript Files ##### -->
    <!-- jQuery-2.2.4 js -->
    <!-- jQuery -->
    <!-- jQuery -->
<script src="{{ asset('assets/js/jquery/jquery-2.2.4.min.js') }}"></script>

<!-- Popper js -->
<script src="{{ asset('assets/js/bootstrap/popper.min.js') }}"></script>

<!-- Bootstrap js -->
<script src="{{ asset('assets/js/bootstrap/bootstrap.min.js') }}"></script>

<!-- All Plugins js -->
<script src="{{ asset('assets/js/plugins/plugins.js') }}"></script>

<!-- Active js -->
<script src="{{ asset('assets/js/active.js') }}"></script>


</body>

</html>