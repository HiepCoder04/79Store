@if (session('success') || session('error'))
<div id="alertToast"
    class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} alert-dismissible fade show shadow"
    role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 250px;">
    {{ session('success') ?? session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>

    <!-- Thanh tiến trình -->
    <div id="progressBar" class="position-absolute bottom-0 start-0 bg-white bg-opacity-50"
        style="height: 4px; width: 100%;"></div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const alertBox = document.getElementById('alertToast');
    const progressBar = document.getElementById('progressBar');

    if (alertBox) {
        let width = 100;
        const interval = setInterval(() => {
            width -= 2.5;
            progressBar.style.width = width + '%';
        }, 100);

        setTimeout(() => {
            const toast = bootstrap.Alert.getOrCreateInstance(alertBox);
            toast.close();
            clearInterval(interval);
        }, 4000);

    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>