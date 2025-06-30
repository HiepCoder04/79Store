<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

@php
    if (session()->has('success')) {
        $typeSession = 'success';
        $typeClass = 'success';
    } elseif (session()->has('error')) {
        $typeSession = 'error';
        $typeClass = 'danger';
    } elseif (session()->has('warning')) {
        $typeSession = 'warning';
        $typeClass = 'warning';
    } else {
        $typeSession = null;
        $typeClass = null;
    }

    $message = $typeSession ? session($typeSession) : null;
@endphp

@if ($typeClass && $message)
    <div id="alertBox" class="custom-alert alert-{{ $typeClass }}" role="alert">
        <div class="custom-alert-content">
            <div class="progress-circle-wrapper {{ $typeClass }}">
                <svg class="progress-circle" viewBox="0 0 36 36" width="30" height="30" aria-hidden="true">
                    <path class="circle-bg"
                          d="M18 2.0845
                             a 15.9155 15.9155 0 0 1 0 31.831
                             a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    <path class="circle"
                          stroke-dasharray="100, 100"
                          d="M18 2.0845
                             a 15.9155 15.9155 0 0 1 0 31.831
                             a 15.9155 15.9155 0 0 1 0 -31.831"/>
                </svg>

                <i class="
                    @if($typeClass === 'success') bi bi-check-lg
                    @elseif($typeClass === 'danger') bi bi-x-lg
                    @elseif($typeClass === 'warning') bi bi-exclamation-lg
                    @endif
                icon-inside-circle"></i>
            </div>
            @php
                $mauchu='text-'.$typeClass
            @endphp

            <span class="{{$mauchu}}">{{ $message }}</span>

            <button class="custom-alert-close" onclick="closeAlert()">×</button>
        </div>
    </div>

    <style>
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 30%;
            background-color: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            transition: transform 0.4s ease, opacity 0.4s ease;
            font-weight: 500;
            color: #333;
        }

        .custom-alert-content {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }

        .custom-alert-close {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
            color: #666;
            line-height: 1;
        }

        /* Wrapper vòng tròn và icon */
        .progress-circle-wrapper {
            position: relative;
            width: 30px;
height: 30px;
            flex-shrink: 0;
        }

        .progress-circle {
            transform: rotate(-90deg);
        }

        .circle-bg {
            fill: none;
            stroke: #eee;
            stroke-width: 4;
        }

        .circle {
            fill: none;
            stroke-width: 4;
            stroke-linecap: round;
            stroke-dashoffset: 0;
            animation: progressCircle 4s linear forwards;
        }

        /* Icon Bootstrap inside circle */
        .icon-inside-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18px;
            color: currentColor;
            pointer-events: none;
            user-select: none;
        }

        /* Màu sắc theo loại thông báo */
        .progress-circle-wrapper.success {
            color: #198754;
        }
        .progress-circle-wrapper.success .circle {
            stroke: #198754;
        }

        .progress-circle-wrapper.danger {
            color: #dc3545;
        }
        .progress-circle-wrapper.danger .circle {
            stroke: #dc3545;
        }

        .progress-circle-wrapper.warning {
            color: #ffc107;
        }
        .progress-circle-wrapper.warning .circle {
            stroke: #ffc107;
        }

        @keyframes progressCircle {
            from {
                stroke-dasharray: 100, 100;
                stroke-dashoffset: 0;
            }
            to {
                stroke-dasharray: 100, 100;
                stroke-dashoffset: 100;
            }
        }
    </style>

    <script>
        function closeAlert() {
            const alert = document.getElementById('alertBox');
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(100%)';
                setTimeout(() => alert.remove(), 400);
            }
        }

        // Tự động đóng sau 4 giây
        setTimeout(() => closeAlert(), 4000);
    </script>
@endif
