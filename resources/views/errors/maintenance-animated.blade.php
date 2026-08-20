<!DOCTYPE html>
<html lang="id" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Pemeliharaan Sistem Dinamis — Reftech Industrial</title>
    <meta name="description" content="Sistem Reftech sedang dalam pemeliharaan berkala" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/materialdesignicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 50%, #2563eb 100%);
            --accent-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
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

        /* ======================================================== */
        /* ANIMATED BACKGROUND MESH & DRIFTING FLOATING ORBS        */
        /* ======================================================== */
        .bg-canvas-wrap {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.65;
            animation: floatOrb 18s ease-in-out infinite alternate;
        }

        .orb-1 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.35) 0%, rgba(96, 165, 250, 0.05) 70%);
            top: -10%;
            left: 15%;
            animation-duration: 16s;
        }

        .orb-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.25) 0%, rgba(251, 191, 36, 0.05) 70%);
            bottom: -5%;
            right: 10%;
            animation-duration: 20s;
            animation-delay: -5s;
        }

        .orb-3 {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.2) 0%, rgba(192, 132, 252, 0.05) 70%);
            top: 40%;
            right: 25%;
            animation-duration: 22s;
            animation-delay: -8s;
        }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(60px, 40px) scale(1.12); }
            100% { transform: translate(-40px, 80px) scale(0.95); }
        }

        /* Floating particles */
        .particle {
            position: absolute;
            background: rgba(59, 130, 246, 0.4);
            border-radius: 50%;
            pointer-events: none;
            animation: particleFly 12s linear infinite;
        }

        @keyframes particleFly {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            20% { opacity: 0.7; }
            80% { opacity: 0.7; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        /* ======================================================== */
        /* MAIN CARD WITH GLASS BORDER & SUBTLE HOVER FLOAT         */
        /* ======================================================== */
        .animated-card {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 32px;
            box-shadow: 
                0 25px 60px -15px rgba(15, 23, 42, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.8) inset,
                0 10px 30px -10px rgba(37, 99, 235, 0.08);
            padding: 3.5rem 3rem;
            max-width: 680px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 10;
            animation: cardAppear 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes cardAppear {
            0% { opacity: 0; transform: translateY(30px) scale(0.97); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @media (max-width: 576px) {
            .animated-card {
                padding: 2.25rem 1.5rem;
                border-radius: 24px;
            }
        }

        /* ======================================================== */
        /* ANIMATED HERO ICON & ORBITING PARTICLES                  */
        /* ======================================================== */
        .hero-scene {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 0 auto 1.75rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .radar-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid rgba(59, 130, 246, 0.2);
            animation: radarPulse 3s cubic-bezier(0.25, 1, 0.5, 1) infinite;
        }

        .radar-ring:nth-child(2) {
            animation-delay: 1s;
        }

        .radar-ring:nth-child(3) {
            animation-delay: 2s;
        }

        @keyframes radarPulse {
            0% { transform: scale(0.6); opacity: 0.8; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .hero-center-box {
            width: 86px;
            height: 86px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #bfdbfe;
            border-radius: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 28px -6px rgba(37, 99, 235, 0.25);
            position: relative;
            z-index: 2;
            animation: heroBounce 4s ease-in-out infinite;
        }

        @keyframes heroBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .hero-center-box i {
            font-size: 42px;
            color: #2563eb;
            animation: spinGear 16s linear infinite;
        }

        @keyframes spinGear {
            100% { transform: rotate(360deg); }
        }

        /* Orbiting Satellite Tool Icons */
        .orbit-track {
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            animation: orbitRotate 10s linear infinite;
            z-index: 3;
            pointer-events: none;
        }

        .orbit-tool {
            position: absolute;
            width: 32px;
            height: 32px;
            background: #ffffff;
            border: 1px solid #bfdbfe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.18);
            font-size: 15px;
            color: #2563eb;
            animation: counterRotate 10s linear infinite;
        }

        .orbit-tool-1 { top: -8px; left: 44px; }
        .orbit-tool-2 { bottom: -8px; left: 44px; color: #d97706; border-color: #fde68a; }

        @keyframes orbitRotate {
            100% { transform: rotate(360deg); }
        }

        @keyframes counterRotate {
            100% { transform: rotate(-360deg); }
        }

        /* ======================================================== */
        /* SHIMMER STATUS PILL                                      */
        /* ======================================================== */
        .shimmer-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(90deg, #fef3c7 0%, #fffbeb 50%, #fef3c7 100%);
            background-size: 200% 100%;
            animation: shimmerMove 3s infinite linear;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 7px 18px;
            border-radius: 9999px;
            font-size: 0.825rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.12);
        }

        @keyframes shimmerMove {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .beacon-dot {
            width: 9px;
            height: 9px;
            background-color: #d97706;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.8);
            animation: beaconGlow 1.6s infinite;
        }

        @keyframes beaconGlow {
            0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(217, 119, 6, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 9px rgba(217, 119, 6, 0); }
            100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(217, 119, 6, 0); }
        }

        /* ======================================================== */
        /* TITLES & TYPOGRAPHY                                      */
        /* ======================================================== */
        .animated-title {
            font-size: 2rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 0.75rem;
            letter-spacing: -0.04em;
            line-height: 1.2;
        }

        .animated-message {
            font-size: 1.025rem;
            color: #475569;
            line-height: 1.65;
            margin-bottom: 2rem;
            max-width: 540px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ======================================================== */
        /* ANIMATED COUNTDOWN CARDS WITH HOVER FLOAT                */
        /* ======================================================== */
        .countdown-dynamic-wrap {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 1.6rem 1.25rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .countdown-dynamic-wrap::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #60a5fa, #3b82f6);
            background-size: 200% 100%;
            animation: shimmerMove 3s infinite linear;
        }

        .dynamic-cd-grid {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-top: 0.85rem;
        }

        .dynamic-cd-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 0.85rem 1.1rem;
            min-width: 86px;
            box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: default;
        }

        .dynamic-cd-card:hover {
            transform: translateY(-4px);
            border-color: #93c5fd;
            box-shadow: 0 12px 24px -4px rgba(37, 99, 235, 0.15);
        }

        .dynamic-cd-number {
            font-size: 2rem;
            font-weight: 900;
            color: #1d4ed8;
            font-family: 'Inter', monospace;
            line-height: 1.05;
            letter-spacing: -0.02em;
        }

        .dynamic-cd-unit {
            font-size: 0.725rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-top: 5px;
        }

        /* ======================================================== */
        /* BUTTONS WITH SMOOTH HOVER & PULSE                        */
        /* ======================================================== */
        .btn-animated-refresh {
            background: var(--primary-gradient);
            color: #ffffff;
            border: none;
            padding: 0.85rem 2.25rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 10px 25px -4px rgba(37, 99, 235, 0.4);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn-animated-refresh:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 16px 32px -4px rgba(37, 99, 235, 0.5);
            color: #ffffff;
        }

        .btn-animated-refresh:active {
            transform: translateY(0) scale(0.98);
        }

        .footer-autoreload {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .spin-slow {
            animation: spinGear 8s linear infinite;
        }
    </style>
</head>

<body>
    <!-- Background Canvas with Floating Drifting Orbs -->
    <div class="bg-canvas-wrap">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <!-- Flying Light Particles -->
        <div class="particle" style="width: 8px; height: 8px; left: 15%; animation-delay: 0s;"></div>
        <div class="particle" style="width: 12px; height: 12px; left: 35%; animation-delay: 3s;"></div>
        <div class="particle" style="width: 6px; height: 6px; left: 65%; animation-delay: 6s;"></div>
        <div class="particle" style="width: 10px; height: 10px; left: 85%; animation-delay: 2s;"></div>
    </div>

    <!-- Main Animated Maintenance Card -->
    <div class="animated-card">
        <!-- Status Shimmer Badge -->
        <div class="shimmer-badge">
            <span class="beacon-dot"></span>
            <span>PEMELIHARAAN SISTEM BERJALAN</span>
        </div>

        <!-- Animated Hero Scene with Orbiting Tools -->
        <div class="hero-scene">
            <div class="radar-ring"></div>
            <div class="radar-ring"></div>
            <div class="radar-ring"></div>

            <div class="hero-center-box">
                <i class="mdi mdi-cog-sync-outline"></i>
            </div>

            <!-- Orbiting tools track -->
            <div class="orbit-track">
                <div class="orbit-tool orbit-tool-1">
                    <i class="mdi mdi-wrench"></i>
                </div>
                <div class="orbit-tool orbit-tool-2">
                    <i class="mdi mdi-database-sync"></i>
                </div>
            </div>
        </div>

        <!-- Title -->
        <h1 class="animated-title">
            Sedang Dalam Pembaruan Sistem
        </h1>

        <!-- Message -->
        <p class="animated-message">
            {{ $details['message'] ?? 'Kami sedang melakukan pemeliharaan & sinkronisasi berkala dari Staging ke Production untuk meningkatkan kecepatan serta kenyamanan akses Anda.' }}
        </p>

        <!-- Countdown Block -->
        @if (!empty($details['end_time']))
            <div class="countdown-dynamic-wrap">
                <div class="d-flex align-items-center justify-content-center gap-2 text-muted small fw-bold">
                    <i class="mdi mdi-clock-fast text-primary fs-5"></i>
                    <span>ESTIMASI WAKTU SELESAI:</span>
                </div>

                <div class="dynamic-cd-grid">
                    <div class="dynamic-cd-card">
                        <span class="dynamic-cd-number" id="cd-hours">00</span>
                        <span class="dynamic-cd-unit">Jam</span>
                    </div>
                    <div class="dynamic-cd-card">
                        <span class="dynamic-cd-number" id="cd-minutes">00</span>
                        <span class="dynamic-cd-unit">Menit</span>
                    </div>
                    <div class="dynamic-cd-card">
                        <span class="dynamic-cd-number" id="cd-seconds">00</span>
                        <span class="dynamic-cd-unit">Detik</span>
                    </div>
                </div>

                <div class="mt-3 text-primary small fw-bold">
                    <i class="mdi mdi-target me-1"></i>Target Penyelesaian: {{ $details['end_time'] }}
                </div>
            </div>
        @else
            <!-- Stylish Notice Box (Matches background perfectly) -->
            <div class="p-3 px-4 rounded-4 mb-4 text-center d-flex align-items-center justify-content-center gap-3"
                style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1px solid #dbeafe; box-shadow: 0 6px 16px -2px rgba(37, 99, 235, 0.08);">
                <div class="avatar avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                    <i class="mdi mdi-progress-wrench text-white fs-6"></i>
                </div>
                <span class="small fw-semibold text-start" style="color: #1e293b; line-height: 1.45;">
                    Proses pemeliharaan sedang berlangsung dan sistem akan aktif kembali dalam waktu dekat.
                </span>
            </div>
        @endif

        <!-- Action Button -->
        <div>
            <button type="button" class="btn-animated-refresh" onclick="checkStatusManual()">
                <i class="mdi mdi-refresh fs-5" id="refresh-icon"></i>
                <span>Cek Status Sistem</span>
            </button>
        </div>

        <p class="footer-autoreload">
            <i class="mdi mdi-autorenew spin-slow text-primary"></i>
            <span>Halaman ini akan otomatis memuat ulang saat pemeliharaan selesai.</span>
        </p>

        <!-- Developer Shortcut -->
        <div class="mt-4 pt-3 border-top" style="border-color: #f1f5f9 !important;">
            <small class="text-muted">
                Akses Khusus: 
                <a href="{{ route('login') }}" class="text-decoration-none fw-semibold text-primary">
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
