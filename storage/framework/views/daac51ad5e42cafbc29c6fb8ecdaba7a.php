<section class="new-arrivals-products-area bg-gray section-padding-100">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading text-center">
                    <h2>SẢN PHẨM MỚI</h2>
                    <p style="font-size: 20px; font-weight: 600;">
                        Chúng tôi liên tục cập nhật những sản phẩm mới nhất cho bạn.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-product-area mb-50 wow fadeInUp" data-wow-delay="<?php echo e(($index + 1) * 100); ?>ms">
                        <!-- Product Image -->
                        <div class="product-img ">
                            <a href="<?php echo e(route('shop-detail', $product->id)); ?>">
                                <?php
                                    $image = optional($product->galleries->first())->image;
                                    $imagePath = $image
                                        ? asset(ltrim($image, '/'))
                                        : asset('assets/img/bg-img/default.jpg');
                                ?>
                                <img src="<?php echo e($imagePath); ?>" alt="<?php echo e($product->name); ?>" class="img-fluid fixed-img">
                            </a>

                            <!-- Optional Tag -->
                            <div class="product-tag">
                                <a href="#">Mới</a>
                            </div>

                            

                        </div>

                        <!-- Product Info -->
                        <div class="product-info mt-15 text-center">
                            <a href="<?php echo e(route('shop-detail', $product->id)); ?>">
                                <p><?php echo e($product->name); ?></p>
                            </a>

                            <?php
                                $min = $product->variants->min('price');
                                $max = $product->variants->max('price');
                            ?>
                            <h6 class="text-success fw-bold">
                                <?php echo e(number_format($min, 0, ',', '.')); ?>đ
                                <?php if($min != $max): ?>
                                    – <?php echo e(number_format($max, 0, ',', '.')); ?>đ
                                <?php endif; ?>
                            </h6>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <div class="col-12 text-center">
                <a href="<?php echo e(route('shop')); ?>" class="btn alazea-btn">Xem tất cả</a>
            </div>
        </div>
    </div>
</section>

<section class="best-sellers-area section-padding-80-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-heading text-center">
                    <h2>Sản phẩm bán chạy</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <?php $__currentLoopData = $bestSellers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="single-product-area mb-50">
                        <div class="product-img">
                            <?php
                                $firstGallery = $product->galleries->first();
                                $imagePath = $firstGallery && $firstGallery->image
                                    ? asset(ltrim($firstGallery->image, '/'))
                                    : asset('assets/img/bg-img/default.jpg');
                            ?>
                            <img src="<?php echo e($imagePath); ?>" alt="<?php echo e($product->name); ?>">
                        </div>
                        <div class="product-info mt-15 text-center">
                            <a href="<?php echo e(route('shop-detail', $product->id)); ?>">
                                <p><?php echo e($product->name); ?></p>
                            </a>
                            <?php
                                $min = $product->variants->min('price');
                                $max = $product->variants->max('price');
                            ?>
                            <h6 class="text-success fw-bold">
                                <?php echo e(number_format($min, 0, ',', '.')); ?>đ
                                <?php if($min != $max): ?>
                                    – <?php echo e(number_format($max, 0, ',', '.')); ?>đ
                                <?php endif; ?>
                            </h6>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<style>
.best-sellers-area {
    padding-top: 20px; /* khoảng cách trên */
    padding-bottom: 20px; /* khoảng cách dưới */
    margin-top: 10px; /* cách phần trên ít nhất 10px */
    margin-bottom: 10px; /* cách phần dưới ít nhất 10px */
    background-color: #f9f9f9; /* nền nhẹ để tách biệt */
    border-radius: 8px;
}

.best-sellers-area .section-heading h2 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 25px;
    color: #333;
}

.best-sellers-area .single-product-area {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.best-sellers-area .single-product-area:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.best-sellers-area .product-img img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.best-sellers-area .product-info p {
    margin: 10px 0 5px;
    font-weight: 500;
    font-size: 16px;
    color: #555;
}

.best-sellers-area .product-info h6 {
    margin-bottom: 10px;
}

</style><?php /**PATH C:\laragon\www\79Store\79Store\resources\views/client/layouts/products.blade.php ENDPATH**/ ?>