<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Title -->
    <title>79Store</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/img/core-img/favicon.ico') }}">

    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Trong <head> -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Trước </body> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>



</head>

<body>
    <!-- Preloader -->
    <div class="preloader d-flex align-items-center justify-content-center">
        <div class="preloader-circle"></div>
        <div class="preloader-img">
            <img src="{{ asset('assets/img/core-img/leaf.png') }}" alt="">
        </div>
    </div>

    <!-- ##### Header Area Start ##### -->
    @include('client.layouts.header')
    <!-- ##### Header Area End ##### -->

    @yield('content')

    <!-- ##### Footer Area ##### -->
    @include('client.layouts.footer')

    <!-- ##### Chatbot Widget ##### -->
    @include('client.layouts.chatbot')

    <!-- ##### All Javascript Files ##### -->
    <!-- jQuery -->
    <script src="{{ asset('assets/js/jquery/jquery-2.2.4.min.js') }}"></script>

    <!-- Popper -->
    <script src="{{ asset('assets/js/bootstrap/popper.min.js') }}"></script>

    <!-- Bootstrap -->
    <script src="{{ asset('assets/js/bootstrap/bootstrap.min.js') }}"></script>

    <!-- All Plugins -->
    <script src="{{ asset('assets/js/plugins/plugins.js') }}"></script>

    <!-- Active JS -->
    <script src="{{ asset('assets/js/active.js') }}"></script>

    <!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Page-specific scripts -->
    @yield('page_scripts')
    @stack('scripts')
<!-- Toastr hiển thị thông báo -->
<script>
    @foreach (['success', 'error', 'warning', 'info'] as $msg)
        @if(session()->has($msg))
            toastr.{{ $msg }}("{{ session($msg) }}");
        @endif
    @endforeach
</script>




</body>

</html>
