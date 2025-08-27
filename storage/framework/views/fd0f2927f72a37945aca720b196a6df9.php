<section class="alazea-blog-area section-padding-100-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Section Heading -->
                <div class="section-heading text-center">
                    <h2>TIN TỨC MỚI NHẤT</h2>
                    <p style="font-size: 20px; font-weight: 600;">
                        Các bài viết chia sẻ kiến thức mới nhất từ chúng tôi.
                    </p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <?php if(isset($latest_blogs) && $latest_blogs->count() > 0): ?>
                <?php $__currentLoopData = $latest_blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <!-- Single Blog Post Area -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="single-blog-post mb-100">
                            <div class="post-thumbnail mb-30">
                                <a href="<?php echo e(route('client.blogs.show', $blog->slug)); ?>">
                                    <?php if($blog->img): ?>
                                        <img src="<?php echo e(asset($blog->img)); ?>" alt="<?php echo e($blog->title); ?>" loading="lazy">
                                    <?php else: ?>
                                        <img src="<?php echo e(asset('client/img/bg-img/6.jpg')); ?>" alt="<?php echo e($blog->title); ?>">
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="post-content">
                                <a href="<?php echo e(route('client.blogs.show', $blog->slug)); ?>" class="post-title">
                                    <h5><?php echo e(Str::limit($blog->title, 60)); ?></h5>
                                </a>
                                <div class="post-meta">
                                    <a href="#"><i class="fa fa-clock-o" aria-hidden="true"></i>
                                        <?php echo e($blog->created_at->format('d/m/Y')); ?></a>
                                    <?php if($blog->category): ?>
                                        
                                        <a
                                            href="<?php echo e(route('client.blogs.category', ['slug' => $blog->category->slug])); ?>">
                                            <i class="fa fa-folder" aria-hidden="true"></i> <?php echo e($blog->category->name); ?>

                                        </a>
                                    <?php endif; ?>
                                </div>
                                <p class="post-excerpt">
                                    <?php echo e(Str::limit(strip_tags($blog->content), 100)); ?>

                                    <a href="<?php echo e(route('client.blogs.show', $blog->slug)); ?>" class="text-success">Đọc
                                        thêm</a>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Chưa có bài viết nào được đăng.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-12 text-center mb-100">
                <a href="<?php echo e(route('client.blogs.index')); ?>" class="btn alazea-btn" title="Xem tất cả tin tức">Xem tất
                    cả bài viết</a>

            </div>
        </div>
    </div>
</section>
<!-- ##### Blog Area End ##### -->
<?php /**PATH C:\laragon\www\79Store\79Store\resources\views/client/layouts/blog.blade.php ENDPATH**/ ?>