@php
    $maintDetails = \App\Services\MaintenanceService::getDetails();
@endphp

@if (!empty($maintDetails['is_planned']) && empty($maintDetails['is_active']))
    <div id="maint-planning-container" style="display: none;">
        <!-- Top Floating Warning Banner -->
        <div id="maint-top-banner" class="w-100 py-2 px-3 text-white fw-semibold d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm"
            style="background: linear-gradient(90deg, #b45309 0%, #d97706 50%, #b45309 100%); z-index: 99999; position: relative;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark fw-bold px-2 py-1">
                    <i class="mdi mdi-alert-circle-outline text-warning me-1"></i> PEMBERITAHUAN
                </span>
                <span class="small">
                    Sistem akan memasuki masa pemeliharaan (Maintenance) pukul 
                    <strong class="text-white text-decoration-underline">{{ $maintDetails['plan_start_time'] }}</strong>.
                    Harap segera simpan data Anda.
                </span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="small text-white opacity-75">Sisa Waktu Persiapan:</span>
                <span class="badge bg-dark text-warning fs-6 px-3 py-1 font-monospace" id="maint-banner-timer">00:00:00</span>
            </div>
        </div>

        <!-- Warning Popup Modal -->
        <div class="modal fade" id="maintPlanningModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header bg-warning text-white py-3">
                        <h5 class="modal-title text-white fw-bold d-flex align-items-center" id="maintPlanningModalTitle">
                            <i class="mdi mdi-alert-octagon-outline fs-3 me-2"></i> Peringatan Pemeliharaan Sistem
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="avatar avatar-xl mx-auto mb-3">
                            <span class="avatar-initial rounded-circle bg-label-warning">
                                <i class="mdi mdi-clock-alert-outline mdi-36px"></i>
                            </span>
                        </div>
                        <h5 class="fw-bold mb-2">Sistem Akan Segera Maintenance</h5>
                        <p class="text-muted mb-4 small" style="line-height: 1.6;">
                            {{ $maintDetails['plan_message'] ?? 'Sistem akan memasuki masa pemeliharaan untuk update staging ke production. Mohon segera selesaikan dan simpan pekerjaan atau transaksi Anda.' }}
                        </p>

                        <div class="p-3 bg-lighter rounded border mb-4 text-start">
                            <div class="row g-2 small">
                                <div class="col-5 text-muted">Jadwal Mulai:</div>
                                <div class="col-7 fw-bold text-dark">{{ $maintDetails['plan_start_time'] }}</div>

                                <div class="col-5 text-muted">Estimasi Selesai:</div>
                                <div class="col-7 fw-semibold text-primary">{{ $maintDetails['plan_end_time'] ?? 'Selesai secepatnya' }}</div>

                                <div class="col-5 text-muted">Hitung Mundur:</div>
                                <div class="col-7 fw-bold text-danger font-monospace fs-6" id="maint-modal-timer">--:--:--</div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-warning w-100 py-2 rounded-pill fw-bold text-dark shadow-sm" data-bs-dismiss="modal">
                            <i class="mdi mdi-check me-1"></i> Saya Mengerti & Segera Simpan Pekerjaan
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
