<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Sistem Sedang Dalam Pemeliharaan (Maintenance) — Reftech</title>

    <meta name="description" content="Reftech System Maintenance Mode" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e2e8f0;
            overflow-x: hidden;
        }

        .maintenance-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            padding: 3rem 2.5rem;
            max-width: 680px;
            width: 90%;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .pulsing-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            padding: 6px 18px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .pulsing-dot {
            width: 10px;
            height: 10px;
            background-color: #ef4444;
            border-radius: 50%;
            animation: pulse-dot 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-dot {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.4;
                transform: scale(1.3);
            }
        }

        .countdown-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.25rem;
            min-width: 90px;
        }

        .countdown-number {
            font-size: 2.25rem;
            font-weight: 800;
            color: #60a5fa;
            line-height: 1;
        }

        .countdown-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-top: 4px;
            font-weight: 600;
        }

        .glow-bg {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="glow-bg"></div>

    <div class="maintenance-card my-4">
        <!-- Logo -->
        <div class="mb-4">
            <img src="{{ asset('assets/img/favicon/logo-reftech.png') }}" alt="Reftech" height="48"
                style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));"
                onerror="this.src='{{ asset('assets/img/favicon/logo-putih-app.png') }}'">
        </div>

        <!-- Status Badge -->
        <div class="pulsing-badge">
            <span class="pulsing-dot"></span>
            <span>SYSTEM MAINTENANCE IN PROGRESS</span>
        </div>

        <!-- Heading -->
        <h2 class="fw-bold mb-3 text-white">Sistem Sedang Dalam Pemeliharaan</h2>

        <!-- Message -->
        <p class="text-slate-300 fs-6 mb-4" style="color: #cbd5e1; line-height: 1.6;">
            {{ $details['message'] ?? 'Saat ini sistem sedang dalam proses pembaruan data dan fitur dari Staging ke Production untuk meningkatkan performa & kestabilan.' }}
        </p>

        @if (!empty($details['end_time']))
            <!-- Countdown Container -->
            <div class="mb-4">
                <div class="small text-muted mb-2" style="color: #94a3b8 !important;">
                    <i class="mdi mdi-clock-outline me-1"></i>Estimasi Selesai:
                    <strong class="text-info">{{ $details['end_time'] }}</strong>
                </div>
                <div class="d-flex justify-content-center gap-3" id="countdown-wrapper">
                    <div class="countdown-box">
                        <div class="countdown-number" id="cd-hours">00</div>
                        <div class="countdown-label">Jam</div>
                    </div>
                    <div class="countdown-box">
                        <div class="countdown-number" id="cd-minutes">00</div>
                        <div class="countdown-label">Menit</div>
                    </div>
                    <div class="countdown-box">
                        <div class="countdown-number" id="cd-seconds">00</div>
                        <div class="countdown-label">Detik</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Button -->
        <div class="d-flex justify-content-center align-items-center gap-3 pt-2">
            <button type="button" class="btn btn-primary px-4 py-2 rounded-pill" onclick="checkStatusManual()">
                <i class="mdi mdi-refresh me-1" id="refresh-icon"></i> Cek Status Sistem
            </button>
        </div>

        <p class="small text-muted mt-3 mb-0" style="color: #64748b !important;">
            <i class="mdi mdi-information-outline me-1"></i>Halaman ini akan otomatis memuat ulang saat pemeliharaan selesai.
        </p>

        <!-- Developer Secret / Bypass Link -->
        <div class="mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.08) !important;">
            <small style="color: #475569;">
                Akses Pengembang:
                <a href="{{ route('login') }}" class="text-muted text-decoration-none" style="color: #64748b !important;">
                    <i class="mdi mdi-shield-account-outline"></i> Login Developer
                </a>
            </small>
        </div>
    </div>

    <!-- Script for Live Countdown & Auto-Polling -->
    <script>
        const endTimeStr = @json($details['end_time'] ?? null);

        function parseEndTime(str) {
            if (!str || typeof str !== 'string') return null;
            str = str.trim();

            // 1. If format is full datetime: "2026-08-14 13:30:00" or "2026-08-14T13:30"
            if (/\d{4}[-/]\d{1,2}[-/]\d{1,2}/.test(str)) {
                let cleanStr = str.replace(/\s+WIB|\s+WITA|\s+WIT/i, '').replace(' ', 'T');
                let d = new Date(cleanStr);
                if (!isNaN(d.getTime())) return d;
            }

            // 2. If range like "13.00 - 13.30" or "13:00 - 13:30" -> take end time (13:30)
            if (str.includes('-') && !/\d{4}-\d{2}/.test(str)) {
                let parts = str.split('-');
                str = parts[parts.length - 1].trim();
            }

            // 3. Extract time like "13:30", "13.30", "13:30:00", or "13:30 WIB"
            let timeMatch = str.match(/(\d{1,2})[:.](\d{2})(?:[:.](\d{2}))?/);
            if (timeMatch) {
                let hours = parseInt(timeMatch[1], 10);
                let minutes = parseInt(timeMatch[2], 10);
                let seconds = timeMatch[3] ? parseInt(timeMatch[3], 10) : 0;

                let target = new Date();
                target.setHours(hours, minutes, seconds, 0);

                // If target time has already passed today by > 2 hours, assume next day
                if (target.getTime() < Date.now() - (2 * 3600000)) {
                    target.setDate(target.getDate() + 1);
                }
                return target;
            }

            // 4. Try native Date constructor
            let fallback = new Date(str);
            if (!isNaN(fallback.getTime())) return fallback;

            return null;
        }

        function updateCountdown() {
            if (!endTimeStr) return;

            const targetDate = parseEndTime(endTimeStr);
            if (!targetDate) return;

            const now = new Date();
            const diff = targetDate.getTime() - now.getTime();

            const hEl = document.getElementById('cd-hours');
            const mEl = document.getElementById('cd-minutes');
            const sEl = document.getElementById('cd-seconds');

            if (diff <= 0) {
                if (hEl) hEl.textContent = '00';
                if (mEl) mEl.textContent = '00';
                if (sEl) sEl.textContent = '00';
                return;
            }

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            if (hEl) hEl.textContent = String(hours).padStart(2, '0');
            if (mEl) mEl.textContent = String(minutes).padStart(2, '0');
            if (sEl) sEl.textContent = String(seconds).padStart(2, '0');
        }

        if (endTimeStr) {
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }

        // Auto polling every 15 seconds to check if maintenance has ended
        function checkStatus() {
            fetch('/api/maintenance/status')
                .then(res => res.json())
                .then(data => {
                    if (data && data.is_active === false) {
                        window.location.href = '/';
                    }
                })
                .catch(() => {});
        }

        setInterval(checkStatus, 15000);

        function checkStatusManual() {
            const icon = document.getElementById('refresh-icon');
            if (icon) icon.classList.add('mdi-spin');
            
            fetch('/api/maintenance/status')
                .then(res => res.json())
                .then(data => {
                    if (data && data.is_active === false) {
                        window.location.href = '/';
                    } else {
                        setTimeout(() => {
                            if (icon) icon.classList.remove('mdi-spin');
                            alert('Sistem masih dalam masa pemeliharaan. Estimasi selesai: ' + (data.end_time || 'segera') + '.');
                        }, 500);
                    }
                })
                .catch(() => {
                    setTimeout(() => {
                        if (icon) icon.classList.remove('mdi-spin');
                    }, 500);
                });
        }
    </script>
</body>

</html>
