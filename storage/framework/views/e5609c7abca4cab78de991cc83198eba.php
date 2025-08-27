<?php if($vouchers->isNotEmpty()): ?>
<section class="voucher-section py-4">
    <div class="container">
        <h4 class="mb-3">🎁 Ưu đãi cho bạn</h4>
        <div class="row">
            <?php $__currentLoopData = $vouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="voucher-box d-flex shadow-sm">
                        <div class="voucher-left p-3 text-white text-center">
                            <div class="badge bg-danger mb-2">Khách hàng mới</div>
                            <h5 class="fw-bold mb-0">FREE SHIP</h5>
                            <p class="mb-0">Giảm tối đa <?php echo e(number_format($voucher->max_discount)); ?>đ</p>
                        </div>
                        <div class="voucher-right p-3 flex-grow-1 position-relative">
                            <div class="badge bg-warning text-dark mb-1">⚡ Số lượng có hạn</div>
                            <div><strong>Giảm tới <?php echo e(number_format($voucher->max_discount)); ?>đ</strong></div>
                            <div>Đơn tối thiểu: <?php echo e(number_format($voucher->min_order_amount)); ?>đ</div>
                            <div class="progress my-1" style="height: 4px;">
                                <div class="progress-bar bg-danger" style="width: 60%"></div>
                            </div>
                            <div class="text-danger small">Đang hết nhanh • HSD: <?php echo e(\Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y')); ?></div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <a href="#" class="text-primary small">Điều kiện</a>
                                <?php if(Auth::check() && $userVouchers->contains($voucher->id)): ?>
                                    <button class="btn btn-outline-secondary btn-sm" disabled>Đã lưu</button>
                                <?php else: ?>
                                    <button class="btn btn-outline-success btn-sm save-voucher-btn" data-id="<?php echo e($voucher->id); ?>">Lưu</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>
<style>
.voucher-box {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid #ddd;
    display: flex;
    min-height: 130px;
}

.voucher-left {
    width: 120px;
    background-color: #00bfa5;
    display: flex;
    flex-direction: column;
    justify-content: center;
    border-right: 2px dashed #fff;
}

.voucher-right {
    font-size: 14px;
}

.progress {
    background-color: #eee;
    border-radius: 2px;
    overflow: hidden;
}
</style>

<?php $__env->startSection('page_scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.save-voucher-btn').forEach(button => {
        button.addEventListener('click', function () {
            const voucherId = this.dataset.id;
            const token = '<?php echo e(csrf_token()); ?>';
            const thisBtn = this;

            fetch(`/save-voucher/${voucherId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    thisBtn.innerText = 'Đã lưu';
                    thisBtn.classList.remove('btn-outline-success');
                    thisBtn.classList.add('btn-outline-secondary');
                    thisBtn.disabled = true;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert("Đã có lỗi xảy ra!");
                console.error(error);
            });
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php /**PATH C:\laragon\www\79Store\79Store\resources\views/client/layouts/vouchers.blade.php ENDPATH**/ ?>