/**
 * Prospect Real-time Alert Modal System (mirip alert darurat SUO)
 * Menampilkan alert pop-up modal di tengah layar + nada chime saat ada Prospect baru:
 * 1. Role Admin & Sales Manager: Saat Support membuat Prospect baru (type: 'prospect_created').
 * 2. Role Sales: Saat Prospect didelegasikan/ditugaskan (type: 'prospect_assigned').
 */
$(function () {
    var pollUrl = window.prospectNotifUnreadUrl;
    var readUrlTemplate = window.prospectNotifReadUrlTemplate; // mengandung "__ID__"

    if (!pollUrl) return;

    var audioCtx = null;
    var isModalOpen = false;
    var activeToastIds = new Set();

    // Inisialisasi & unlock Web Audio Context saat interaksi pertama user
    function unlockAudioContext() {
        try {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            if (Ctx && !audioCtx) {
                audioCtx = new Ctx();
            }
            if (audioCtx && audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
        } catch (e) {}
    }
    document.addEventListener('click', unlockAudioContext, { passive: true });
    document.addEventListener('keydown', unlockAudioContext, { passive: true });
    document.addEventListener('touchstart', unlockAudioContext, { passive: true });
    document.addEventListener('pointerdown', unlockAudioContext, { passive: true });
    document.addEventListener('mousemove', unlockAudioContext, { once: true, passive: true });
    window.addEventListener('focus', unlockAudioContext, { passive: true });

    // Bunyikan nada chime ramah elegan (two-tone harmonious chime)
    function playProspectAlarm() {
        try {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            if (!audioCtx) audioCtx = new Ctx();

            function runTones() {
                var now = audioCtx.currentTime;

                function playBeep(freq, start, duration) {
                    var osc = audioCtx.createOscillator();
                    var gain = audioCtx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, start);
                    gain.gain.setValueAtTime(0, start);
                    gain.gain.linearRampToValueAtTime(0.3, start + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.001, start + duration);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start(start);
                    osc.stop(start + duration + 0.02);
                }

                // Nada pembuka harmonis
                playBeep(659.25, now, 0.18);          // E5
                playBeep(880.00, now + 0.16, 0.22);  // A5
                playBeep(1318.51, now + 0.36, 0.32); // E6
            }

            if (audioCtx.state === 'suspended') {
                audioCtx.resume().then(runTones).catch(function () {});
            } else {
                runTones();
            }
        } catch (e) {
            console.warn('[ProspectAlert] Audio failed:', e);
        }
    }

    // Bersihkan dismissal permanen lama dari sessionStorage jika ada
    try {
        for (var k in sessionStorage) {
            if (k && k.indexOf('dismissed_prospect_urgent_') === 0) {
                sessionStorage.removeItem(k);
            }
        }
    } catch (e) {}

    function isDismissed(notifId, stage) {
        try {
            var key = 'snoozed_prospect_urgent_' + notifId + (stage ? '_' + stage : '');
            var until = parseInt(localStorage.getItem(key) || sessionStorage.getItem(key), 10);
            if (until && Date.now() < until) {
                return true; // Masih dalam masa tunda (snooze)
            }
            // Global snooze check
            var globalUntil = parseInt(localStorage.getItem('snoozed_prospect_global_until'), 10);
            if (globalUntil && Date.now() < globalUntil) {
                return true;
            }
            return false;
        } catch (e) {
            return false;
        }
    }

    function setDismissed(notifId, stage, snoozeMs) {
        try {
            var key = 'snoozed_prospect_urgent_' + notifId + (stage ? '_' + stage : '');
            var duration = snoozeMs || 60000; // default snooze 60 detik
            var expireAt = Date.now() + duration;
            localStorage.setItem(key, expireAt);
            sessionStorage.setItem(key, expireAt);
        } catch (e) {}
    }

    function setGlobalSnooze(snoozeMs) {
        try {
            localStorage.setItem('snoozed_prospect_global_until', Date.now() + snoozeMs);
        } catch (e) {}
    }

    function escapeHtml(str) {
        return $('<div>').text(str == null ? '' : str).html();
    }

    function markRead(id) {
        if (!readUrlTemplate) return;
        $.post(readUrlTemplate.replace('__ID__', id), { _token: window.csrfToken });
    }

    // Suntikkan CSS Alert Modal Prospect jika belum ada
    function injectStyles() {
        if ($('#prospectUrgentModalStyles').length) return;
        var css = `
            @keyframes prospectPingDot {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
                70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
            }
            @keyframes prospectPingDotSuccess {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
                70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
            }
            @keyframes prospectShimmer {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
            #prospectUrgentModal {
                z-index: 1095 !important;
            }
            #prospectUrgentModal .modal-dialog {
                max-width: 550px;
                margin: 1.75rem auto;
            }
            #prospectUrgentModal .modal-content {
                border-radius: 20px !important;
                border: 0 !important;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.28) !important;
                overflow: hidden;
                background: #ffffff;
            }
            html.dark-style #prospectUrgentModal .modal-content {
                background: #2b2c40 !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7) !important;
                color: #e4e6f0;
            }
            #prospectUrgentModal .prospect-top-stripe {
                height: 5px;
                background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 50%, #38bdf8 100%);
                background-size: 200% 100%;
                animation: prospectShimmer 3s ease-in-out infinite;
                width: 100%;
            }
            #prospectUrgentModal.modal-assigned .prospect-top-stripe {
                background: linear-gradient(90deg, #10b981 0%, #06b6d4 50%, #3b82f6 100%);
                background-size: 200% 100%;
                animation: prospectShimmer 3s ease-in-out infinite;
                width: 100%;
            }
            .prospect-hero-halo {
                width: 76px;
                height: 76px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(99, 102, 241, 0.22) 0%, rgba(99, 102, 241, 0.04) 70%, transparent 100%);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            #prospectUrgentModal.modal-assigned .prospect-hero-halo {
                background: radial-gradient(circle, rgba(16, 185, 129, 0.22) 0%, rgba(16, 185, 129, 0.04) 70%, transparent 100%);
            }
            .prospect-hero-inner {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 6px 18px rgba(99, 102, 241, 0.4);
            }
            #prospectUrgentModal.modal-assigned .prospect-hero-inner {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
            }
            #prospectUrgentModal .prospect-detail-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                overflow: hidden;
            }
            html.dark-style #prospectUrgentModal .prospect-detail-card {
                background: #32344d;
                border-color: #3f4262;
            }
            #prospectUrgentModal .prospect-card-header {
                background: #ffffff;
                border-bottom: 1px solid #edf0f5;
                padding: 11px 16px;
            }
            html.dark-style #prospectUrgentModal .prospect-card-header {
                background: #2b2c40;
                border-bottom-color: #3f4262;
            }
            #prospectUrgentModal .prospect-card-body {
                padding: 12px 16px;
            }
            #prospectUrgentModal .prospect-info-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 7px 0;
                border-bottom: 1px dashed #e2e8f0;
            }
            html.dark-style #prospectUrgentModal .prospect-info-row {
                border-bottom-color: #434665;
            }
            #prospectUrgentModal .prospect-info-row:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }
            #prospectUrgentModal .prospect-info-label {
                font-size: 0.815rem;
                color: #64748b;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            html.dark-style #prospectUrgentModal .prospect-info-label {
                color: #a1a4b8;
            }
            #prospectUrgentModal .prospect-info-val {
                font-size: 0.865rem;
                font-weight: 600;
                color: #1e293b;
                text-align: right;
                max-width: 65%;
            }
            html.dark-style #prospectUrgentModal .prospect-info-val {
                color: #f1f5f9;
            }
            #prospectUrgentModal .prospect-guide-card {
                background: #eff6ff;
                border: 1px solid #dbeafe;
                border-radius: 12px;
                padding: 10px 14px;
            }
            html.dark-style #prospectUrgentModal .prospect-guide-card {
                background: #1e293b;
                border-color: #334155;
            }
            #prospectUrgentModal.modal-assigned .prospect-guide-card {
                background: #ecfdf5;
                border-color: #d1fae5;
            }
            html.dark-style #prospectUrgentModal.modal-assigned .prospect-guide-card {
                background: #064e3b;
                border-color: #047857;
            }
            #prospectUrgentModal .btn-action-prospect {
                background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                border: none;
                color: #ffffff;
                font-weight: 600;
                font-size: 0.9rem;
                padding: 10px 20px;
                border-radius: 12px;
                box-shadow: 0 4px 14px rgba(99, 102, 241, 0.38);
                transition: all 0.2s ease;
            }
            #prospectUrgentModal .btn-action-prospect:hover {
                background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
                color: #ffffff;
                box-shadow: 0 6px 18px rgba(99, 102, 241, 0.48);
                transform: translateY(-1px);
            }
            #prospectUrgentModal.modal-assigned .btn-action-prospect {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                box-shadow: 0 4px 14px rgba(16, 185, 129, 0.38);
            }
            #prospectUrgentModal.modal-assigned .btn-action-prospect:hover {
                background: linear-gradient(135deg, #059669 0%, #047857 100%);
                box-shadow: 0 6px 18px rgba(16, 185, 129, 0.48);
            }
            #prospectUrgentModal .btn-dismiss-prospect {
                background: #f1f5f9;
                color: #475569;
                border: 1px solid #e2e8f0;
                font-weight: 600;
                font-size: 0.88rem;
                padding: 10px 18px;
                border-radius: 12px;
                transition: all 0.15s ease;
            }
            #prospectUrgentModal .btn-dismiss-prospect:hover {
                background: #e2e8f0;
                color: #1e293b;
            }
            html.dark-style #prospectUrgentModal .btn-dismiss-prospect {
                background: #334155;
                color: #e2e8f0;
                border-color: #475569;
            }
            html.dark-style #prospectUrgentModal .btn-dismiss-prospect:hover {
                background: #475569;
                color: #ffffff;
            }
        `;
        $('<style id="prospectUrgentModalStyles">' + css + '</style>').appendTo('head');
    }

    function showProspectModal(item) {
        injectStyles();

        // Jangan buka jika user sudah berada di halaman detail prospek tersebut
        if (item.id && window.location.pathname.indexOf('/prospect/' + item.id) !== -1) {
            return;
        }

        // Jangan buka modal baru jika modal SUO atau modal Prospect sedang tampil
        if ($('#suoUrgentModal.show').length || $('#prospectUrgentModal.show').length || isModalOpen) {
            return;
        }

        $('#prospectUrgentModal').remove();
        $('.modal-backdrop').remove();

        var isAssigned = (item.type === 'prospect_assigned');
        var modalThemeClass = isAssigned ? 'modal-assigned' : '';
        var badgeText = isAssigned ? 'PROSPEK DITUGASKAN' : 'PROSPEK BARU';
        var stageBadge = item.stage_badge || (isAssigned ? 'Ditugaskan ke Anda' : 'Baru Dibuat');
        var stageTitle = item.stage_title || (isAssigned ? 'Anda Mendapat Penugasan Prospect Baru!' : 'Ada Prospect Baru Masuk!');
        var stageDesc = item.stage_desc || (isAssigned
            ? 'Prospek baru telah didelegasikan kepada Anda. Segera lakukan follow-up ke pelanggan untuk proses penawaran.'
            : 'Tim Support baru saja menginput data prospek baru yang belum ditugaskan ke Sales. Segera tentukan dan tugaskan Sales penanggung jawab.');
        var actionLabel = item.action_label || (isAssigned ? 'Buka Prospek' : 'Tugaskan ke Sales');
        var heroIcon = isAssigned ? 'mdi-account-arrow-right-outline' : 'mdi-account-star-outline';

        var avatarHtml = '';
        if (item.support_image) {
            avatarHtml = '<img src="' + escapeHtml(item.support_image) + '" class="rounded-circle me-1" style="width:22px;height:22px;object-fit:cover;" alt="' + escapeHtml(item.support_name || 'Support') + '">';
        } else {
            avatarHtml = '<span class="avatar-initial rounded-circle ' + (isAssigned ? 'bg-label-success' : 'bg-label-primary') + ' me-1" style="width:22px;height:22px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;">' + (item.support_name ? item.support_name.charAt(0) : 'S') + '</span>';
        }

        var picContactHtml = escapeHtml(item.pic_name || '-');
        if (item.pic_phone) {
            picContactHtml += ' <span class="text-muted fw-normal small">(' + escapeHtml(item.pic_phone) + ')</span>';
        }

        var kebutuhanHtml = item.kebutuhan ? escapeHtml(item.kebutuhan) : (item.category ? escapeHtml(item.category) : '-');

        var modalHtml = `
            <div class="modal fade ${modalThemeClass}" id="prospectUrgentModal" tabindex="-1" aria-labelledby="prospectUrgentModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0">
                        <!-- Top Accent Gradient Line -->
                        <div class="prospect-top-stripe"></div>

                        <div class="modal-body p-4 pt-3">
                            <!-- Top Status Badge & Close Button -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge rounded-pill px-3 py-1 text-uppercase fw-bold" style="background: ${isAssigned ? 'rgba(16, 185, 129, 0.12)' : 'rgba(99, 102, 241, 0.12)'}; color: ${isAssigned ? '#059669' : '#4f46e5'}; font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <i class="mdi ${isAssigned ? 'mdi-account-check-outline' : 'mdi-fire'} me-1"></i> ${badgeText} &bull; ${stageBadge}
                                </span>
                                <button type="button" class="btn-close btn-prospect-dismiss" data-notif-id="${item.id}" data-stage="${item.stage || ''}" aria-label="Close"></button>
                            </div>

                            <!-- Hero Icon Halo & Centered Titles -->
                            <div class="text-center mb-3">
                                <div class="prospect-hero-halo mx-auto mb-2">
                                    <div class="prospect-hero-inner">
                                        <i class="mdi ${heroIcon} fs-2"></i>
                                    </div>
                                </div>

                                <h4 class="modal-title fw-bold text-dark mb-1" id="prospectUrgentModalLabel" style="letter-spacing: -0.3px;">
                                    ${stageTitle}
                                </h4>
                                <p class="text-muted small mb-0 px-2" style="line-height: 1.55;">
                                    ${stageDesc}
                                </p>
                            </div>

                            <!-- Card 1: Structured Detail Card -->
                            <div class="prospect-detail-card mb-3">
                                <div class="prospect-card-header d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2 text-truncate pe-2">
                                        <span class="badge rounded-circle p-2 ${isAssigned ? 'bg-label-success' : 'bg-label-primary'} flex-shrink-0" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="mdi mdi-domain fs-6"></i>
                                        </span>
                                        <span class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;" title="${escapeHtml(item.company)}">
                                            ${escapeHtml(item.company)}
                                        </span>
                                    </div>
                                    <span class="text-muted flex-shrink-0" style="font-size: 0.76rem;">
                                        <i class="mdi mdi-clock-outline me-1"></i>${escapeHtml(item.created_at_time || item.created_at)}
                                    </span>
                                </div>
                                <div class="prospect-card-body">
                                    <div class="prospect-info-row">
                                        <span class="prospect-info-label">
                                            <i class="mdi mdi-account-outline text-secondary fs-6"></i> PIC Customer
                                        </span>
                                        <span class="prospect-info-val">${picContactHtml}</span>
                                    </div>
                                    <div class="prospect-info-row">
                                        <span class="prospect-info-label">
                                            <i class="mdi mdi-headset text-info fs-6"></i> Diinput Oleh
                                        </span>
                                        <span class="prospect-info-val d-inline-flex align-items-center justify-content-end">
                                            ${avatarHtml}
                                            <span class="text-truncate">${escapeHtml(item.support_name || 'Support')}</span>
                                        </span>
                                    </div>
                                    <div class="prospect-info-row">
                                        <span class="prospect-info-label">
                                            <i class="mdi mdi-clipboard-text-outline text-warning fs-6"></i> Kebutuhan
                                        </span>
                                        <span class="prospect-info-val text-truncate" title="${kebutuhanHtml}">${kebutuhanHtml}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 2: Guidance Notice -->
                            <div class="prospect-guide-card d-flex align-items-center gap-2 mb-3">
                                <i class="mdi ${isAssigned ? 'mdi-check-decagram text-success' : 'mdi-information text-primary'} fs-5 flex-shrink-0"></i>
                                <div class="small ${isAssigned ? 'text-success' : 'text-primary'}" style="font-size: 0.8rem; line-height: 1.45;">
                                    ${isAssigned ? 'Segera lakukan kontak awal dan tindak lanjut penawaran untuk meningkatkan peluang dealing.' : 'Segera tentukan Sales penanggung jawab agar prospek pelanggan dapat segera dihubungi.'}
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center gap-2 pt-1 flex-wrap">
                                <!-- Dropdown Snooze Fleksibel -->
                                <div class="dropdown">
                                    <button class="btn btn-dismiss-prospect dropdown-toggle d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Tunda pop-up alert ini">
                                        <i class="mdi mdi-clock-alert-outline fs-6 text-primary"></i>
                                        <span>Snooze</span>
                                    </button>
                                    <ul class="dropdown-menu shadow-sm dropdown-menu-start" style="border-radius: 12px; font-size: 0.85rem; min-width: 210px;">
                                        <li><h6 class="dropdown-header text-muted font-11 text-uppercase mb-0">Tunda Alert Pop-up</h6></li>
                                        <li><a class="dropdown-item btn-snooze-duration d-flex align-items-center" href="javascript:void(0);" data-ms="900000" data-label="15 Menit" data-notif-id="${item.id}" data-stage="${item.stage || ''}"><i class="mdi mdi-clock-fast text-primary me-2"></i>15 Menit</a></li>
                                        <li><a class="dropdown-item btn-snooze-duration d-flex align-items-center" href="javascript:void(0);" data-ms="3600000" data-label="1 Jam" data-notif-id="${item.id}" data-stage="${item.stage || ''}"><i class="mdi mdi-clock-outline text-info me-2"></i>1 Jam</a></li>
                                        <li><a class="dropdown-item btn-snooze-duration d-flex align-items-center" href="javascript:void(0);" data-ms="14400000" data-label="4 Jam" data-notif-id="${item.id}" data-stage="${item.stage || ''}"><i class="mdi mdi-timer-sand text-warning me-2"></i>4 Jam</a></li>
                                        <li><a class="dropdown-item btn-snooze-duration d-flex align-items-center" href="javascript:void(0);" data-ms="86400000" data-label="1 Hari Penuh" data-notif-id="${item.id}" data-stage="${item.stage || ''}"><i class="mdi mdi-calendar-today text-secondary me-2"></i>1 Hari (Sampai Besok)</a></li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li><a class="dropdown-item btn-snooze-duration text-danger d-flex align-items-center" href="javascript:void(0);" data-ms="604800000" data-label="7 Hari" data-notif-id="${item.id}" data-stage="${item.stage || ''}"><i class="mdi mdi-bell-off-outline me-2"></i>Nonaktifkan 7 Hari</a></li>
                                    </ul>
                                </div>

                                <button type="button" class="btn btn-dismiss-prospect btn-prospect-dismiss flex-shrink-0" data-notif-id="${item.id}" data-stage="${item.stage || ''}">
                                    Nanti Dulu
                                </button>
                                <a href="${item.url}" class="btn btn-action-prospect btn-prospect-action flex-grow-1 text-center d-inline-flex align-items-center justify-content-center gap-1" data-notif-id="${item.id}" data-stage="${item.stage || ''}">
                                    <span>${actionLabel}</span>
                                    <i class="mdi mdi-arrow-right fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modalHtml);

        var modalEl = document.getElementById('prospectUrgentModal');
        var opened = false;

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                var bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
                bsModal.show();
                opened = true;
            } catch (e) {
                console.warn('[ProspectAlert] bootstrap.Modal error:', e);
            }
        }
        if (!opened && typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            try {
                $('#prospectUrgentModal').modal({ backdrop: 'static', keyboard: false, show: true });
                opened = true;
            } catch (e) {
                console.warn('[ProspectAlert] jQuery.modal error:', e);
            }
        }

        if (opened) {
            isModalOpen = true;
            playProspectAlarm();
        }
    }

    // Listener saat modal ditutup (hidden)
    $(document).on('hidden.bs.modal', '#prospectUrgentModal', function () {
        isModalOpen = false;
        $('#prospectUrgentModal').remove();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');
    });

    // Klik tombol Buka Prospek -> snooze & tandai dibaca
    $(document).on('click', '.btn-prospect-action', function () {
        var notifId = $(this).data('notif-id');
        var stage = $(this).data('stage');
        if (notifId) {
            setDismissed(notifId, stage, 120000); // Tunda 2 menit saat navigasi
            markRead(notifId);
        }
        isModalOpen = false;
    });

    // Klik tombol Pilihan Snooze Durasi (15 menit, 1 jam, 4 jam, 1 hari, 7 hari)
    $(document).on('click', '.btn-snooze-duration', function () {
        var ms = parseInt($(this).data('ms'), 10) || 3600000;
        var notifId = $(this).data('notif-id');
        var stage = $(this).data('stage');
        var label = $(this).data('label') || 'beberapa saat';

        if (notifId) {
            setDismissed(notifId, stage, ms);
        }
        // Set global snooze juga agar tidak muncul prospek lain yang unassigned dalam durasi ini
        setGlobalSnooze(ms);

        var modalEl = document.getElementById('prospectUrgentModal');
        if (modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();
            }
            if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                $('#prospectUrgentModal').modal('hide');
            }
            setTimeout(function () {
                $('#prospectUrgentModal').remove();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('overflow', '');
            }, 350);
        }
        isModalOpen = false;

        // Feedback toast mini jika library toastr atau alert tersedia
        if (typeof toastr !== 'undefined') {
            toastr.info('Alert pop-up prospek berhasil di-snooze selama ' + label + '.');
        }
    });

    // Klik tombol Nanti Dulu -> tutup modal & snooze sementara (60 detik)
    $(document).on('click', '.btn-prospect-dismiss', function () {
        var notifId = $(this).data('notif-id');
        var stage = $(this).data('stage');
        if (notifId) {
            setDismissed(notifId, stage, 60000); // Tunda 60 detik
        }

        var modalEl = document.getElementById('prospectUrgentModal');
        if (modalEl) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) bsModal.hide();
            }
            if (typeof $ !== 'undefined' && $.fn && $.fn.modal) {
                $('#prospectUrgentModal').modal('hide');
            }
            setTimeout(function () {
                $('#prospectUrgentModal').remove();
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css('overflow', '');
            }, 350);
        }
        isModalOpen = false;
    });

    // Helper untuk test manual langsung di browser console
    window.showTestProspectModal = function (type) {
        showProspectModal({
            id: 'test_' + Date.now(),
            type: type || 'prospect_created',
            company: 'PT Demo Test Modal Mandiri',
            pic_name: 'Bapak Hendra Wijaya',
            pic_phone: '0812-3456-7890',
            support_name: 'Sandhy (Support)',
            kebutuhan: 'Pengetesan Pop-up Modal Alert Prospect Baru (Air Compressor ELGi Screw 50HP)',
            url: window.location.href,
            created_at: 'Baru saja',
            created_at_time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
        });
    };

    // Klik item notifikasi di dropdown lonceng navbar — tandai dibaca
    $(document).on('click', '.prospect-notif-item', function () {
        var id = $(this).data('prospect-notif-id');
        if (id) {
            markRead(id);
        }
    });

    function updateNavbarDropdown(items) {
        var $list = $('#staticNotifList');
        if (!$list.length) return;

        var unreadItems = items.filter(function (i) { return !i.is_read; });
        var createdCount = unreadItems.filter(function (i) { return i.type === 'prospect_created'; }).length;
        var assignedCount = unreadItems.filter(function (i) { return i.type === 'prospect_assigned'; }).length;

        // Update badge counters di header lonceng
        var $createdBadge = $('#prospectCreatedCountBadge');
        if (createdCount > 0) {
            if (!$createdBadge.length) {
                $('.dropdown-header').find('h6').after('<span id="prospectCreatedCountBadge" class="badge rounded-pill bg-primary me-1">' + createdCount + ' Prospect Baru</span>');
            } else {
                $createdBadge.text(createdCount + ' Prospect Baru').removeClass('d-none');
            }
        } else if ($createdBadge.length) {
            $createdBadge.addClass('d-none');
        }

        var $assignedBadge = $('#prospectAssignedCountBadge');
        if (assignedCount > 0) {
            if (!$assignedBadge.length) {
                $('.dropdown-header').find('h6').after('<span id="prospectAssignedCountBadge" class="badge rounded-pill bg-success me-1">' + assignedCount + ' Ditugaskan</span>');
            } else {
                $assignedBadge.text(assignedCount + ' Ditugaskan').removeClass('d-none');
            }
        } else if ($assignedBadge.length) {
            $assignedBadge.addClass('d-none');
        }

        // Sisipkan item baru ke daftar lonceng jika belum ada di DOM
        unreadItems.forEach(function (item) {
            var existing = $list.find('.prospect-notif-item[data-prospect-notif-id="' + item.id + '"]');
            if (!existing.length) {
                var isCreated = (item.type === 'prospect_created');
                var isAssigned = (item.type === 'prospect_assigned');

                var cardTypeClass = isAssigned ? 'notif-card-prospect-assigned' : 'notif-card-prospect-new';
                var iconColor = isAssigned ? 'bg-success text-white' : 'bg-primary text-white';
                var iconClass = isAssigned ? 'mdi-account-arrow-right-outline' : 'mdi-account-star-outline';
                var badgeColor = isAssigned ? 'bg-label-success' : 'bg-label-primary';
                var badgeText = isAssigned ? 'Ditugaskan' : 'Prospect Baru';
                var dotClass = isAssigned ? 'dot-success' : '';
                var descHtml = '';

                if (isAssigned) {
                    var catTextAssigned = item.category ? '<strong class="text-success">' + escapeHtml(item.category) + '</strong> • ' : '';
                    descHtml = 'Kategori: ' + catTextAssigned + escapeHtml(item.kebutuhan);
                } else {
                    var byText = item.support_name ? '<span class="fw-semibold text-dark">Oleh: ' + escapeHtml(item.support_name) + '</span> • ' : '';
                    var catTextCreated = item.category ? '<strong class="text-primary">' + escapeHtml(item.category) + '</strong> • ' : '';
                    descHtml = byText + 'Kategori: ' + catTextCreated + escapeHtml(item.kebutuhan);
                }

                var avatarHtml = '<div class="notif-card-avatar ' + iconColor + '"><i class="mdi ' + iconClass + '"></i></div>';

                var itemHtml = $(
                    '<a href="' + item.url + '" class="notif-card ' + cardTypeClass + ' prospect-notif-item" ' +
                    'data-prospect-notif-id="' + item.id + '">' +
                        '<div class="notif-card-inner">' +
                            avatarHtml +
                            '<div class="notif-card-content">' +
                                '<div class="notif-card-meta">' +
                                    '<span class="badge ' + badgeColor + ' notif-badge-pill">' + badgeText + '</span>' +
                                    '<span class="notif-time-ago">' +
                                        '<i class="mdi mdi-clock-outline fs-7"></i> ' + escapeHtml(item.created_at) +
                                    '</span>' +
                                '</div>' +
                                '<h6 class="notif-card-title">' + escapeHtml(item.company) + '</h6>' +
                                '<p class="notif-card-desc">' + descHtml + '</p>' +
                            '</div>' +
                            '<span class="notif-unread-dot ' + dotClass + '"></span>' +
                        '</div>' +
                    '</a>'
                );

                itemHtml.on('click', function () {
                    markRead(item.id);
                });

                $list.prepend(itemHtml);
            }
        });
    }

    function poll() {
        $.getJSON(pollUrl).done(function (res) {
            var items = res.items || [];
            var unreadCount = items.filter(function (i) { return !i.is_read; }).length;
            if (unreadCount > 0) {
                $('#navbarBellDot').removeClass('d-none');
            }
            updateNavbarDropdown(items);
        }).fail(function (xhr, status, err) {
            console.warn('[ProspectNotif] Poll failed:', status, err);
        });
    }

    var urgentCheckUrl = window.prospectUrgentCheckUrl;

    function pollUrgentProspect() {
        if (!urgentCheckUrl) return;
        if (isModalOpen) return;
        // Jika modal SUO sedang aktif di layar, jangan tumpuk modal prospect
        if ($('#suoUrgentModal.show').length || $('#prospectUrgentModal.show').length) return;

        $.getJSON(urgentCheckUrl).done(function (res) {
            if (res && res.has_urgent && res.prospect) {
                var p = res.prospect;
                var stageKey = p.stage || 'urgent';
                if (!isDismissed(p.id, stageKey)) {
                    showProspectModal({
                        id: p.id,
                        type: p.type || 'prospect_created',
                        stage: stageKey,
                        company: p.company,
                        pic_name: p.pic_name,
                        pic_phone: p.pic_phone,
                        pic_position: p.pic_position,
                        support_name: p.support_name,
                        support_image: p.support_image,
                        category: p.category,
                        kebutuhan: p.kebutuhan,
                        stage_badge: p.stage_badge,
                        stage_title: p.stage_title,
                        stage_desc: p.stage_desc,
                        action_label: p.action_label,
                        url: p.action_url,
                        created_at: p.created_at,
                        created_at_time: p.created_at_time
                    });
                }
            }
        }).fail(function (xhr, status, err) {
            console.warn('[ProspectUrgentCheck] Poll failed:', status, err);
        });
    }

    // Polling awal dan berulang tiap 5 detik
    poll();
    setInterval(poll, 5000);

    if (urgentCheckUrl) {
        pollUrgentProspect();
        setInterval(pollUrgentProspect, 5000);
    }

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            poll();
            if (urgentCheckUrl) pollUrgentProspect();
        }
    });
});
