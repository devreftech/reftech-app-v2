<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Pemeliharaan Sistem — Reftech Industrial</title>
    <meta name="description" content="Sistem Reftech sedang dalam pemeliharaan berkala" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            --accent-color: #3b82f6;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(245, 158, 11, 0.06) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(99, 102, 241, 0.05) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            margin: 0;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Ambient floating shapes */
        .ambient-shape-1 {
            position: absolute;
            top: -100px;
            right: -100px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        .ambient-shape-2 {
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        /* Maintenance Main Card */
        .light-maintenance-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 28px;
            box-shadow: 0 20px 50px -12px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(15, 23, 42, 0.02);
            padding: 3.5rem 3rem;
            max-width: 660px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 10;
            transition: transform 0.3s ease;
        }

        @media (max-width: 576px) {
            .light-maintenance-card {
                padding: 2.5rem 1.5rem;
                border-radius: 20px;
            }
        }

        /* Brand & Badge */
        .brand-logo-wrap {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.75rem;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fef3c7;
            border: 1px solid #fde68a;
            color: #b45309;
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 0.825rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .pulsing-dot-amber {
            width: 8px;
            height: 8px;
            background-color: #d97706;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7);
            animation: pulseAmber 1.8s infinite;
        }

        @keyframes pulseAmber {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(217, 119, 6, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
        }

        /* Animated Illustration Icon */
        .hero-icon-box {
            width: 90px;
            height: 90px;
            border-radius: 24px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #bfdbfe;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            position: relative;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.15);
        }

        .hero-icon-box i {
            font-size: 44px;
            color: #2563eb;
            animation: rotateSlow 20s linear infinite;
        }

        @keyframes rotateSlow {
            100% { transform: rotate(360deg); }
        }

        /* Titles & Headings */
        .main-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.75rem;
            letter-spacing: -0.03em;
        }

        .lead-message {
            font-size: 1rem;
            color: #475569;
            line-height: 1.65;
            margin-bottom: 2rem;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Countdown Cards (Light & Crisp) */
        .light-countdown-wrap {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 1.5rem 1.25rem;
            margin-bottom: 2rem;
        }

        .light-countdown-grid {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 0.75rem;
        }

        .light-cd-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 0.75rem 1rem;
            min-width: 80px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .light-cd-val {
            font-size: 1.85rem;
            font-weight: 800;
            color: #1e40af;
            font-family: 'Inter', monospace;
            line-height: 1.1;
        }

        .light-cd-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            margin-top: 4px;
        }

        /* Action Buttons */
        .btn-refresh-primary {
            background: var(--primary-gradient);
            color: #ffffff;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-refresh-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(37, 99, 235, 0.4);
            color: #ffffff;
        }

        .btn-refresh-primary:active {
            transform: translateY(0);
        }

        .footer-note {
            font-size: 0.825rem;
            color: #94a3b8;
            margin-top: 1.25rem;
        }

        .dev-link {
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }

        .dev-link:hover {
            color: #2563eb;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Ambient Decor -->
    <div class="ambient-shape-1"></div>
    <div class="ambient-shape-2"></div>

    <div class="light-maintenance-card">
        <!-- Status Pill -->
        <div class="brand-badge mb-4">
            <span class="pulsing-dot-amber"></span>
            <span>PEMELIHARAAN SISTEM BERKALA</span>
        </div>

        <!-- Hero Icon -->
        <div>
            <div class="hero-icon-box">
                <i class="mdi mdi-server-network-outline"></i>
            </div>
        </div>

        <!-- Main Title -->
        <h1 class="main-title">
            Kami Sedang Melakukan Pembaruan
        </h1>

        <!-- Subtitle / Message -->
        <p class="lead-message">
            {{ $details['message'] ?? 'Sistem Reftech sedang dalam proses pemeliharaan & sinkronisasi berkala untuk meningkatkan performa dan keandalan operasional Anda.' }}
        </p>

        <!-- Countdown Block -->
        @if (!empty($details['end_time']))
            <div class="light-countdown-wrap">
                <div class="d-flex align-items-center justify-content-center gap-1 text-muted small fw-semibold">
                    <i class="mdi mdi-clock-outline text-primary"></i>
                    <span>ESTIMASI SISTEM KEMBALI NORMAL DALAM:</span>
                </div>

                <div class="light-countdown-grid">
                    <div class="light-cd-card">
                        <span class="light-cd-val" id="cd-hours">00</span>
                        <span class="light-cd-label">Jam</span>
                    </div>
                    <div class="light-cd-card">
                        <span class="light-cd-val" id="cd-minutes">00</span>
                        <span class="light-cd-label">Menit</span>
                    </div>
                    <div class="light-cd-card">
                        <span class="light-cd-val" id="cd-seconds">00</span>
                        <span class="light-cd-label">Detik</span>
                    </div>
                </div>

                <div class="mt-2 text-primary small fw-bold">
                    Target Penyelesaian: {{ $details['end_time'] }}
                </div>
            </div>
        @else
            <div class="p-3 px-4 rounded-4 mb-4 text-center d-flex align-items-center justify-content-center gap-2"
                style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1px solid #dbeafe; box-shadow: 0 4px 14px -2px rgba(37, 99, 235, 0.06);">
                <div class="avatar avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px;">
                    <i class="mdi mdi-progress-wrench text-white" style="font-size: 15px;"></i>
                </div>
                <span class="small fw-semibold text-start" style="color: #1e293b; line-height: 1.45;">
                    Proses pemeliharaan sedang berlangsung dan sistem akan aktif kembali dalam waktu dekat.
                </span>
            </div>
        @endif

        <!-- Action Button -->
        <div>
            <button type="button" class="btn-refresh-primary" onclick="checkStatusManual()">
                <i class="mdi mdi-refresh fs-5" id="refresh-icon"></i>
                <span>Cek Status Sistem</span>
            </button>
        </div>

        <p class="footer-note mb-0">
            <i class="mdi mdi-autorenew me-1"></i>Halaman ini akan otomatis memuat ulang saat pemeliharaan selesai.
        </p>

        <!-- Developer Shortcut -->
        <div class="mt-4 pt-3 border-top" style="border-color: #f1f5f9 !important;">
            <small class="text-muted">
                Khusus Pengembang: 
                <a href="{{ route('login') }}" class="dev-link fw-semibold">
                    <i class="mdi mdi-shield-account-outline me-1"></i>Login Developer
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
