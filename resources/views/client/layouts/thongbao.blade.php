@if (session('success') || session('error'))
    <div id="alertToast"
        class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show shadow"
        role="alert"
        style="position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 250px; transition: opacity 0.5s;">
        
        {{ session('success') ?? session('error') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>

        <!-- Thanh tiến trình -->
        <div id="progressBar" class="position-absolute bottom-0 start-0 bg-white bg-opacity-50"
            style="height: 4px; width: 100%; transition: width 0.1s linear;"></div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('alertToast');
    const progressBar = document.getElementById('progressBar');

    if (alertBox && progressBar) {
        const duration = 4000; // tổng thời gian hiển thị (ms)
        const intervalTime = 50; // thời gian cập nhật tiến trình
        const steps = duration / intervalTime;
        let currentStep = 0;

        const interval = setInterval(() => {
            currentStep++;
            const percent = Math.max(0, 100 - (currentStep / steps) * 100);
            progressBar.style.width = percent + '%';

            if (currentStep >= steps) {
                clearInterval(interval);
                const toast = bootstrap.Alert.getOrCreateInstance(alertBox);
                toast.close();
            }
        }, intervalTime);
    }
});
</script>

<!-- Gắn ở layout hoặc trước </body> -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
