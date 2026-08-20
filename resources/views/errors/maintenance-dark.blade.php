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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
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
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }
            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        .countdown-container {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin: 2rem 0;
        }

        .countdown-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            min-width: 85px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .countdown-number {
            font-size: 2rem;
            font-weight: 700;
            color: #38bdf8;
            font-family: monospace;
            line-height: 1;
        }

        .countdown-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            margin-top: 6px;
            font-weight: 600;
        }

        .bg-glow {
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, rgba(0, 0, 0, 0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            pointer-events: none;
        }

        .gear-spin {
            animation: spin 15s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="bg-glow"></div>

    <div class="maintenance-card">
        <!-- Badge -->
        <div class="pulsing-badge">
            <span class="pulsing-dot"></span>
            MODE PEMELIHARAAN SISTEM
        </div>

        <!-- Logo / Icon -->
        <div class="mb-4">
            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle"
                style="background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2);">
                <i class="mdi mdi-cog-sync-outline mdi-48px text-info gear-spin"></i>
            </div>
        </div>

        <!-- Title -->
        <h2 class="fw-bold text-white mb-2" style="letter-spacing: -0.02em;">
            Sistem Sedang Diperbarui
        </h2>

        <!-- Message -->
        <p class="text-slate-300 fs-6 mb-4" style="color: #cbd5e1; line-height: 1.6;">
            {{ $details['message'] ?? 'Kami sedang melakukan pemeliharaan dan peningkatan performa sistem dari Staging ke Production agar layanan berjalan lebih optimal.' }}
        </p>

        <!-- Countdown -->
        @if (!empty($details['end_time']))
            <div class="mb-2">
                <small class="text-muted text-uppercase" style="letter-spacing: 0.08em; color: #94a3b8 !important;">
                    Estimasi Selesai Dalam:
                </small>
                <div class="countdown-container">
                    <div class="countdown-box">
                        <span class="countdown-number" id="cd-hours">00</span>
                        <span class="countdown-label">Jam</span>
                    </div>
                    <div class="countdown-box">
                        <span class="countdown-number" id="cd-minutes">00</span>
                        <span class="countdown-label">Menit</span>
                    </div>
                    <div class="countdown-box">
                        <span class="countdown-number" id="cd-seconds">00</span>
                        <span class="countdown-label">Detik</span>
                    </div>
                </div>
                <small class="text-info fw-semibold">
                    <i class="mdi mdi-clock-outline me-1"></i>Target: {{ $details['end_time'] }}
                </small>
            </div>
        @else
            <div class="p-3 px-4 rounded-4 mb-4 text-center d-flex align-items-center justify-content-center gap-2"
                style="background: rgba(15, 23, 42, 0.65); border: 1px solid rgba(56, 189, 248, 0.2); box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.05);">
                <div class="avatar avatar-xs rounded-circle bg-label-info d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px;">
                    <i class="mdi mdi-progress-wrench text-info" style="font-size: 15px;"></i>
                </div>
                <span class="small fw-semibold text-start" style="color: #cbd5e1; line-height: 1.45;">
                    Proses pemeliharaan sedang berlangsung dan sistem akan aktif kembali dalam waktu dekat.
                </span>
            </div>
        @endif

        <!-- Action / Reload -->
        <div class="d-flex justify-content-center align-items-center gap-3 pt-2">
            <button type="button" class="btn btn-primary px-4 py-2 rounded-pill" onclick="checkStatusManual()">
                <i class="mdi mdi-refresh me-1" id="refresh-icon"></i> Cek Status Sistem
            </button>
        </div>

        <p class="small text-muted mt-3 mb-0" style="color: #64748b !important;">
            <i class="mdi mdi-information-outline me-1"></i>Halaman ini akan otomatis memuat ulang saat pemeliharaan selesai.
        </p>

        <!-- Developer Login Link -->
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

            if (/\d{4}[-/]\d{1,2}[-/]\d{1,2}/.test(str)) {
                let cleanStr = str.replace(/\s+WIB|\s+WITA|\s+WIT/i, '').replace(' ', 'T');
                let d = new Date(cleanStr);
                if (!isNaN(d.getTime())) return d;
            }

            if (str.includes('-') && !/\d{4}-\d{2}/.test(str)) {
                let parts = str.split('-');
                str = parts[parts.length - 1].trim();
            }

            let timeMatch = str.match(/(\d{1,2})[:.](\d{2})(?:[:.](\d{2}))?/);
            if (timeMatch) {
                let hours = parseInt(timeMatch[1], 10);
                let minutes = parseInt(timeMatch[2], 10);
                let seconds = timeMatch[3] ? parseInt(timeMatch[3], 10) : 0;

                let target = new Date();
                target.setHours(hours, minutes, seconds, 0);

                if (target.getTime() < Date.now() - (2 * 3600000)) {
                    target.setDate(target.getDate() + 1);
                }
                return target;
            }

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
                    if (icon) icon.classList.remove('mdi-spin');
                    if (data && data.is_active === false) {
                        window.location.href = '/';
                    } else {
                        alert('Sistem saat ini masih dalam proses pemeliharaan. Mohon tunggu beberapa saat.');
                    }
                })
                .catch(() => {
                    if (icon) icon.classList.remove('mdi-spin');
                    alert('Gagal memeriksa status. Sistem kemungkinan masih dalam proses restart.');
                });
        }
    </script>

    <!-- Floating Background Music Player -->
    @include('components.maintenance-audio', ['details' => $details])
</body>

</html>
