@php
    $maintDetails = \App\Services\MaintenanceService::getDetails();
@endphp

@if (!empty($maintDetails['is_planned']) && empty($maintDetails['is_active']))
    <div id="maint-planning-container" style="display: none;">
        <style>
            /* Pulse indicator animation */
            @keyframes maintPulseRing {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
            }
            @keyframes maintPulseGlow {
                0%, 100% { opacity: 1; filter: drop-shadow(0 0 6px rgba(245, 158, 11, 0.6)); }
                50% { opacity: 0.75; filter: drop-shadow(0 0 2px rgba(245, 158, 11, 0.2)); }
            }
            .maint-pulse-dot {
                width: 10px;
                height: 10px;
                background-color: #f59e0b;
                border-radius: 50%;
                display: inline-block;
                animation: maintPulseRing 2s infinite cubic-bezier(0.45, 0, 0.55, 1);
            }
            .maint-top-banner-bar {
                background: linear-gradient(90deg, #18181b 0%, #261c10 50%, #18181b 100%);
                border-bottom: 1px solid rgba(245, 158, 11, 0.3);
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
                position: relative;
                z-index: 9999;
                transition: all 0.3s ease;
            }
            .maint-top-banner-bar .maint-tag {
                background: rgba(245, 158, 11, 0.18);
                color: #fbbf24;
                border: 1px solid rgba(245, 158, 11, 0.35);
                font-size: 0.75rem;
                letter-spacing: 0.5px;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 20px;
                text-transform: uppercase;
                display: inline-flex;
                align-items: center;
            }
            .maint-banner-timer-pill {
                background: rgba(0, 0, 0, 0.45);
                border: 1px solid rgba(245, 158, 11, 0.4);
                color: #fbbf24;
                font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
                font-size: 0.95rem;
                font-weight: 700;
                padding: 4px 12px;
                border-radius: 8px;
                letter-spacing: 1px;
                display: inline-block;
                text-shadow: 0 0 8px rgba(245, 158, 11, 0.4);
            }
            .maint-btn-detail {
                background: rgba(245, 158, 11, 0.15);
                color: #fbbf24;
                border: 1px solid rgba(245, 158, 11, 0.4);
                font-size: 0.8rem;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                transition: all 0.2s ease;
            }
            .maint-btn-detail:hover {
                background: #f59e0b;
                color: #18181b;
                border-color: #f59e0b;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
            }

            /* Modal Styling */
            .maint-modal-dialog {
                max-width: 480px;
            }
            .maint-modal-content {
                border-radius: 20px;
                overflow: hidden;
                border: 1px solid rgba(245, 158, 11, 0.25);
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
                position: relative;
            }
            .maint-modal-accent-line {
                height: 4px;
                background: linear-gradient(90deg, #f59e0b 0%, #ea580c 50%, #f59e0b 100%);
                width: 100%;
            }
            .maint-icon-halo {
                width: 76px;
                height: 76px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(245, 158, 11, 0.2) 0%, rgba(245, 158, 11, 0.04) 70%, transparent 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px auto;
            }
            .maint-icon-inner {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 8px 18px rgba(217, 119, 6, 0.35);
            }
            .maint-info-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 12px;
                text-align: center;
                transition: transform 0.2s ease, border-color 0.2s ease;
            }
            .maint-info-card:hover {
                transform: translateY(-2px);
                border-color: #cbd5e1;
            }
            .maint-info-card.highlight {
                background: #fffbeb;
                border-color: #fde68a;
            }
            .dark-style .maint-info-card {
                background: rgba(255, 255, 255, 0.04);
                border-color: rgba(255, 255, 255, 0.08);
            }
            .dark-style .maint-info-card:hover {
                border-color: rgba(255, 255, 255, 0.16);
            }
            .dark-style .maint-info-card.highlight {
                background: rgba(245, 158, 11, 0.08);
                border-color: rgba(245, 158, 11, 0.25);
            }
            .maint-notice-box {
                background: #fffbeb;
                border: 1px solid #fef3c7;
                color: #92400e;
                border-radius: 12px;
                padding: 12px 14px;
            }
            .dark-style .maint-notice-box {
                background: rgba(245, 158, 11, 0.08);
                border-color: rgba(245, 158, 11, 0.2);
                color: #fde68a;
            }
            .maint-btn-primary {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                color: #ffffff;
                font-weight: 600;
                padding: 11px 20px;
                border-radius: 12px;
                border: none;
                box-shadow: 0 6px 18px rgba(217, 119, 6, 0.3);
                transition: all 0.2s ease;
            }
            .maint-btn-primary:hover {
                background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
                color: #ffffff;
                transform: translateY(-1px);
                box-shadow: 0 10px 22px rgba(217, 119, 6, 0.4);
            }
        </style>

        <!-- Top Floating Warning Banner -->
        <div id="maint-top-banner" class="maint-top-banner-bar w-100 py-2 px-3 text-white d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="maint-pulse-dot me-1"></span>
                <span class="maint-tag">
                    <i class="mdi mdi-alert-decagram-outline me-1"></i> Maintenance Alert
                </span>
                <span class="small text-white-50 d-none d-md-inline">|</span>
                <span class="small text-white">
                    Sistem akan memasuki masa pemeliharaan pukul 
                    <strong class="text-warning font-monospace">{{ $maintDetails['plan_start_time'] }}</strong>.
                    <span class="text-white-50 ms-1 d-none d-sm-inline">Harap segera simpan data Anda.</span>
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 ms-auto">
                <div class="d-flex align-items-center gap-2">
                    <span class="small text-white-50 d-none d-sm-inline">Sisa Waktu:</span>
                    <span class="maint-banner-timer-pill" id="maint-banner-timer">00:00:00</span>
                </div>
                <button type="button" class="btn maint-btn-detail btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#maintPlanningModal">
                    <i class="mdi mdi-information-outline me-1"></i> Detail
                </button>
            </div>
        </div>

        <!-- Warning Popup Modal -->
        <div class="modal fade" id="maintPlanningModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered maint-modal-dialog" role="document">
                <div class="modal-content maint-modal-content border-0">
                    <div class="maint-modal-accent-line"></div>
                    
                    <div class="modal-header border-0 pb-0 pt-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="badge rounded-pill px-3 py-1" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">
                            <i class="mdi mdi-calendar-clock me-1"></i> Jadwal Pemeliharaan
                        </span>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 pt-2 text-center">
                        <!-- Icon Avatar -->
                        <div class="maint-icon-halo">
                            <div class="maint-icon-inner">
                                <i class="mdi mdi-clock-alert-outline fs-3"></i>
                            </div>
                        </div>

                        <h4 class="fw-bold mb-1" style="letter-spacing: -0.3px;">Sistem Akan Segera Maintenance</h4>
                        <p class="text-muted mb-4 small" style="line-height: 1.6; max-width: 400px; margin-left: auto; margin-right: auto;">
                            {{ $maintDetails['plan_message'] ?? 'Sistem akan memasuki masa pemeliharaan rutin untuk peningkatan performa dan update sistem. Mohon segera simpan seluruh pekerjaan Anda.' }}
                        </p>

                        <!-- Schedule Cards Grid -->
                        <div class="row g-2 mb-3 text-start">
                            <div class="col-4">
                                <div class="maint-info-card h-100">
                                    <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">
                                        <i class="mdi mdi-clock-start text-warning me-1"></i> Mulai
                                    </div>
                                    <div class="fw-bold font-monospace text-dark text-nowrap" style="font-size: 0.9rem;">
                                        {{ $maintDetails['plan_start_time'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="maint-info-card h-100">
                                    <div class="text-muted small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 600;">
                                        <i class="mdi mdi-clock-check-outline text-primary me-1"></i> Estimasi
                                    </div>
                                    <div class="fw-semibold text-primary text-truncate" style="font-size: 0.85rem;" title="{{ $maintDetails['plan_end_time'] ?? 'Secepatnya' }}">
                                        {{ $maintDetails['plan_end_time'] ?? 'Secepatnya' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="maint-info-card highlight h-100">
                                    <div class="text-danger small mb-1" style="font-size: 0.72rem; text-transform: uppercase; font-weight: 700;">
                                        <i class="mdi mdi-timer-sand text-danger me-1"></i> Sisa Waktu
                                    </div>
                                    <div class="fw-bold text-danger font-monospace text-nowrap" style="font-size: 0.95rem;" id="maint-modal-timer">
                                        00:00:00
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Callout Box -->
                        <div class="maint-notice-box d-flex align-items-center gap-2 mb-4 text-start small">
                            <i class="mdi mdi-information-outline fs-4 flex-shrink-0"></i>
                            <div>
                                Pastikan Anda telah <strong>menyimpan seluruh input dan transaksi</strong> sebelum hitung mundur selesai.
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button type="button" class="btn maint-btn-primary w-100 py-2 d-flex align-items-center justify-content-center" data-bs-dismiss="modal">
                            <i class="mdi mdi-check-circle-outline me-2 fs-5"></i> Saya Mengerti & Lanjutkan Bekerja
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const planStartTimeStr = @json($maintDetails['plan_start_time']);
            const warnMinutes = @json((int)($maintDetails['plan_warn_minutes'] ?? 30));
            const autoActivate = @json((bool)($maintDetails['auto_activate'] ?? true));

            function parsePlanningTime(str) {
                if (!str) return null;
                str = str.trim();

                if (/\d{4}[-/]\d{1,2}[-/]\d{1,2}/.test(str)) {
                    let cleanStr = str.replace(/\s+WIB|\s+WITA|\s+WIT/i, '').replace(' ', 'T');
                    let d = new Date(cleanStr);
                    if (!isNaN(d.getTime())) return d;
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

            const targetStartTime = parsePlanningTime(planStartTimeStr);
            if (!targetStartTime) return;

            const warnThresholdMs = warnMinutes * 60 * 1000;

            let planningInterval = null;
            let isRedirecting = false;

            function checkPlanningWindow() {
                if (isRedirecting) return;

                const now = new Date();
                const diffMs = targetStartTime.getTime() - now.getTime();

                // If time has arrived or passed (diff <= 0)
                if (diffMs <= 0) {
                    if (planningInterval) clearInterval(planningInterval);
                    isRedirecting = true;

                    if (autoActivate) {
                        // Single smooth transition to maintenance page
                        window.location.href = '/maintenance';
                    }
                    return;
                }

                // If current time is within the warning window
                if (diffMs <= warnThresholdMs) {
                    const container = document.getElementById('maint-planning-container');
                    if (container && container.style.display === 'none') {
                        container.style.display = 'block';

                        // Show modal once per session
                        const modalDismissKey = 'maint_modal_seen_' + planStartTimeStr;
                        if (!sessionStorage.getItem(modalDismissKey)) {
                            setTimeout(function() {
                                if (window.bootstrap && document.getElementById('maintPlanningModal')) {
                                    const modal = new bootstrap.Modal(document.getElementById('maintPlanningModal'));
                                    modal.show();
                                    sessionStorage.setItem(modalDismissKey, '1');
                                }
                            }, 800);
                        }
                    }

                    // Update live countdown display
                    const hours = Math.floor(diffMs / (1000 * 60 * 60));
                    const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);

                    const timeFormatted = 
                        String(hours).padStart(2, '0') + ':' + 
                        String(minutes).padStart(2, '0') + ':' + 
                        String(seconds).padStart(2, '0');

                    const bannerTimer = document.getElementById('maint-banner-timer');
                    const modalTimer = document.getElementById('maint-modal-timer');

                    if (bannerTimer) bannerTimer.textContent = timeFormatted;
                    if (modalTimer) modalTimer.textContent = timeFormatted;
                }
            }

            checkPlanningWindow();
            planningInterval = setInterval(checkPlanningWindow, 1000);
        })();
    </script>
@endif
