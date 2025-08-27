<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">


    <!-- Title -->
    <title>79Store</title>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(asset('assets/img/core-img/favicon.ico')); ?>">

    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/style.css')); ?>">
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
            <img src="<?php echo e(asset('assets/img/core-img/leaf.png')); ?>" alt="">
        </div>
    </div>

    <!-- ##### Header Area Start ##### -->
    <?php echo $__env->make('client.layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <!-- ##### Header Area End ##### -->

    <?php echo $__env->yieldContent('content'); ?>

    <!-- ##### Footer Area ##### -->
    <?php echo $__env->make('client.layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- ##### Chatbot Widget ##### -->
    <?php echo $__env->make('client.layouts.chatbot', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- ##### All Javascript Files ##### -->
    <!-- jQuery -->
    <script src="<?php echo e(asset('assets/js/jquery/jquery-2.2.4.min.js')); ?>"></script>

    <!-- Popper -->
    <script src="<?php echo e(asset('assets/js/bootstrap/popper.min.js')); ?>"></script>

    <!-- Bootstrap -->
    <script src="<?php echo e(asset('assets/js/bootstrap/bootstrap.min.js')); ?>"></script>

    <!-- All Plugins -->
    <script src="<?php echo e(asset('assets/js/plugins/plugins.js')); ?>"></script>

    <!-- Active JS -->
    <script src="<?php echo e(asset('assets/js/active.js')); ?>"></script>

    <!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Page-specific scripts -->
    <?php echo $__env->yieldContent('page_scripts'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
<!-- Toastr hiển thị thông báo -->
<script>
    <?php $__currentLoopData = ['success', 'error', 'warning', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(session()->has($msg)): ?>
            toastr.<?php echo e($msg); ?>("<?php echo e(session($msg)); ?>");
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</script>




</body>

</html>
<?php /**PATH C:\laragon\www\79Store\79Store\resources\views/client/layouts/default.blade.php ENDPATH**/ ?>