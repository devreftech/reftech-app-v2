@php
    $maintDetails = \App\Services\MaintenanceService::getDetails();
@endphp

@if (!empty($maintDetails['is_planned']) && empty($maintDetails['is_active']))
    <div id="maint-planning-container" style="display: none;">
        <!-- Bootstrap Alert Banner under Navbar -->
        <div class="container-fluid px-3 px-md-4 pt-3 pb-0">
            <div class="alert alert-warning alert-dismissible fade show border-0 shadow-xs mb-0 d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3" role="alert" style="border-left: 4px solid #ffab00 !important; background-color: rgba(255, 171, 0, 0.12);">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-warning text-dark text-uppercase font-11 fw-bold d-inline-flex align-items-center">
                        <i class="mdi mdi-alert-circle-outline me-1"></i> Maintenance Alert
                    </span>
                    <span class="font-13 text-heading">
                        Sistem akan memasuki masa pemeliharaan pada pukul 
                        <strong class="text-warning font-monospace">{{ $maintDetails['plan_start_time'] }}</strong>.
                        <span class="text-muted ms-1 d-none d-sm-inline">Harap segera simpan data transaksi Anda.</span>
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div class="d-flex align-items-center gap-1">
                        <span class="font-11 text-muted d-none d-md-inline">Sisa Waktu:</span>
                        <span class="badge bg-label-warning font-monospace font-13 fw-bold px-2 py-1" id="maint-banner-timer">00:00:00</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-warning waves-effect px-2 py-1 font-12" data-bs-toggle="modal" data-bs-target="#maintPlanningModal">
                        <i class="mdi mdi-information-outline me-1"></i> Detail
                    </button>
                    <button type="button" class="btn-close position-relative p-1 ms-1" data-bs-dismiss="alert" aria-label="Close" style="top: 0; right: 0;"></button>
                </div>
            </div>
        </div>

        <!-- Bootstrap Modal for Maintenance Details -->
        <div class="modal fade" id="maintPlanningModal" tabindex="-1" aria-labelledby="maintPlanningModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-light border-bottom py-3">
                        <h5 class="modal-title fw-bold text-warning d-flex align-items-center mb-0" id="maintPlanningModalLabel">
                            <i class="mdi mdi-clock-alert-outline me-2 fs-4"></i> Jadwal Pemeliharaan Sistem
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 text-center">
                        <!-- Avatar / Illustration using Materio style -->
                        <div class="avatar avatar-xl mx-auto mb-3" style="width: 64px; height: 64px;">
                            <span class="avatar-initial rounded-circle bg-label-warning">
                                <i class="mdi mdi-tools fs-1 text-warning"></i>
                            </span>
                        </div>

                        <h5 class="fw-bold mb-1 text-heading">Sistem Akan Segera Maintenance</h5>
                        <p class="text-muted small mb-4" style="line-height: 1.6; max-width: 420px; margin-left: auto; margin-right: auto;">
                            {{ $maintDetails['plan_message'] ?? 'Sistem akan memasuki masa pemeliharaan rutin untuk peningkatan performa dan update stabilitas. Mohon segera simpan seluruh data dan transaksi Anda.' }}
                        </p>

                        <!-- Schedule Cards Grid using Bootstrap Cards -->
                        <div class="row g-2 mb-3 text-start">
                            <div class="col-4">
                                <div class="card bg-label-secondary border-0 h-100 p-2 text-center">
                                    <div class="text-muted font-11 text-uppercase fw-semibold mb-1">
                                        <i class="mdi mdi-clock-start text-warning me-1"></i> Mulai
                                    </div>
                                    <div class="fw-bold font-monospace text-dark font-13 text-nowrap">
                                        {{ $maintDetails['plan_start_time'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card bg-label-primary border-0 h-100 p-2 text-center">
                                    <div class="text-muted font-11 text-uppercase fw-semibold mb-1">
                                        <i class="mdi mdi-clock-check-outline text-primary me-1"></i> Estimasi
                                    </div>
                                    <div class="fw-bold text-primary font-12 text-truncate" title="{{ $maintDetails['plan_end_time'] ?? 'Secepatnya' }}">
                                        {{ $maintDetails['plan_end_time'] ?? 'Secepatnya' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card bg-label-warning border-0 h-100 p-2 text-center">
                                    <div class="text-danger font-11 text-uppercase fw-bold mb-1">
                                        <i class="mdi mdi-timer-sand text-danger me-1"></i> Sisa Waktu
                                    </div>
                                    <div class="fw-bold text-danger font-monospace font-13 text-nowrap" id="maint-modal-timer">
                                        00:00:00
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Callout Box using Bootstrap Alert -->
                        <div class="alert alert-warning d-flex align-items-center gap-2 mb-4 text-start font-12 py-2 px-3">
                            <i class="mdi mdi-information-outline fs-4 flex-shrink-0 text-warning"></i>
                            <div>
                                Pastikan Anda telah <strong>menyimpan seluruh transaksi</strong> sebelum hitung mundur waktu maintenance berakhir.
                            </div>
                        </div>

                        <!-- Action Button using Bootstrap -->
                        <button type="button" class="btn btn-primary w-100 py-2 waves-effect waves-light d-flex align-items-center justify-content-center" data-bs-dismiss="modal">
                            <i class="mdi mdi-check-circle-outline me-2 fs-5"></i> Saya Mengerti &amp; Lanjutkan Bekerja
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
