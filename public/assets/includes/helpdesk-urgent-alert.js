/**
 * Helpdesk Ticket Real-time Alert Modal System untuk Developer & Admin
 * Menampilkan alert pop-up modal di tengah layar + nada chime saat ada Tiket Bantuan baru dari User:
 */
$(function () {
    var checkUrl = window.helpdeskUrgentCheckUrl;
    if (!checkUrl) return;

    var audioCtx = null;
    var isModalOpen = false;
    var pollTimer = null;

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

    // Bunyikan nada notifikasi tiket baru (3-tone alert crystal harmonic chime)
    function playHelpdeskAlarm() {
        try {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            if (!audioCtx) audioCtx = new Ctx();

            function runTones() {
                var now = audioCtx.currentTime;

                function playBeep(freq, start, duration, type) {
                    var osc = audioCtx.createOscillator();
                    var gain = audioCtx.createGain();
                    osc.type = type || 'sine';
                    osc.frequency.setValueAtTime(freq, start);
                    gain.gain.setValueAtTime(0, start);
                    gain.gain.linearRampToValueAtTime(0.32, start + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.001, start + duration);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start(start);
                    osc.stop(start + duration + 0.02);
                }

                // Melodi nada notifikasi modern: F5 -> A5 -> C6
                playBeep(698.46, now, 0.18);          // F5
                playBeep(880.00, now + 0.14, 0.20);  // A5
                playBeep(1046.50, now + 0.32, 0.35); // C6
            }

            if (audioCtx.state === 'suspended') {
                audioCtx.resume().then(runTones).catch(function () {});
            } else {
                runTones();
            }
        } catch (e) {
            console.warn('[HelpdeskUrgentAlert] Audio failed:', e);
        }
    }

    function isDismissed(ticketId) {
        try {
            var key = 'snoozed_helpdesk_urgent_' + ticketId;
            var until = parseInt(localStorage.getItem(key) || sessionStorage.getItem(key), 10);
            if (until && Date.now() < until) {
                return true; // Masih dalam masa snooze
            }
            var globalUntil = parseInt(localStorage.getItem('snoozed_helpdesk_global_until'), 10);
            if (globalUntil && Date.now() < globalUntil) {
                return true;
            }
            return false;
        } catch (e) {
            return false;
        }
    }

    function setDismissed(ticketId, snoozeMs) {
        try {
            var key = 'snoozed_helpdesk_urgent_' + ticketId;
            var duration = snoozeMs || 900000; // default snooze 15 menit
            var expireAt = Date.now() + duration;
            localStorage.setItem(key, expireAt);
            sessionStorage.setItem(key, expireAt);
        } catch (e) {}
    }

    function setGlobalSnooze(snoozeMs) {
        try {
            localStorage.setItem('snoozed_helpdesk_global_until', Date.now() + snoozeMs);
        } catch (e) {}
    }

    function escapeHtml(str) {
        return $('<div>').text(str == null ? '' : str).html();
    }

    // Injeksi CSS Alert Modal Helpdesk
    function injectStyles() {
        if ($('#helpdeskUrgentModalStyles').length) return;
        var css = `
            @keyframes helpdeskPingDot {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
                70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
            }
            @keyframes helpdeskShimmer {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
            #helpdeskUrgentModal {
                z-index: 1098 !important;
            }
            #helpdeskUrgentModal .modal-dialog {
                max-width: 540px;
                margin: 1.75rem auto;
            }
            #helpdeskUrgentModal .modal-content {
                border-radius: 20px !important;
                border: 0 !important;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35) !important;
                overflow: hidden;
                background: #ffffff;
            }
            html.dark-style #helpdeskUrgentModal .modal-content {
                background: #2b2c40 !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                box-shadow: 0 25px 60px rgba(0, 0, 0, 0.75) !important;
                color: #e4e6f0;
            }
            #helpdeskUrgentModal .helpdesk-top-stripe {
                height: 5px;
                background: linear-gradient(90deg, #f59e0b 0%, #ec4899 50%, #6366f1 100%);
                background-size: 200% 100%;
                animation: helpdeskShimmer 3s ease-in-out infinite;
                width: 100%;
            }
            .helpdesk-hero-halo {
                width: 74px;
                height: 74px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(245, 158, 11, 0.22) 0%, rgba(245, 158, 11, 0.04) 70%, transparent 100%);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .helpdesk-hero-inner {
                width: 52px;
                height: 52px;
                border-radius: 50%;
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 6px 18px rgba(245, 158, 11, 0.45);
            }
            #helpdeskUrgentModal .helpdesk-detail-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 14px;
                overflow: hidden;
            }
            html.dark-style #helpdeskUrgentModal .helpdesk-detail-card {
                background: #32344d;
                border-color: #3f4262;
            }
            #helpdeskUrgentModal .helpdesk-card-header {
                background: #ffffff;
                border-bottom: 1px solid #edf0f5;
                padding: 11px 16px;
            }
            html.dark-style #helpdeskUrgentModal .helpdesk-card-header {
                background: #2b2c40;
                border-bottom-color: #3f4262;
            }
            #helpdeskUrgentModal .helpdesk-card-body {
                padding: 12px 16px;
            }
            #helpdeskUrgentModal .helpdesk-info-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 7px 0;
                border-bottom: 1px dashed #e2e8f0;
            }
            html.dark-style #helpdeskUrgentModal .helpdesk-info-row {
                border-bottom-color: #434665;
            }
            #helpdeskUrgentModal .helpdesk-info-row:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }
            #helpdeskUrgentModal .helpdesk-info-label {
                font-size: 0.815rem;
                color: #64748b;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            html.dark-style #helpdeskUrgentModal .helpdesk-info-label {
                color: #a1a4b8;
            }
            #helpdeskUrgentModal .helpdesk-info-val {
                font-size: 0.865rem;
                font-weight: 600;
                color: #1e293b;
                text-align: right;
                max-width: 65%;
            }
            html.dark-style #helpdeskUrgentModal .helpdesk-info-val {
                color: #f1f5f9;
            }
            #helpdeskUrgentModal .helpdesk-desc-box {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                padding: 10px 12px;
                font-size: 0.84rem;
                color: #334155;
                line-height: 1.5;
                max-height: 90px;
                overflow-y: auto;
            }
            html.dark-style #helpdeskUrgentModal .helpdesk-desc-box {
                background: #242538;
                border-color: #383a54;
                color: #cbd5e1;
            }
            #helpdeskUrgentModal .btn-action-helpdesk {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                border: none;
                color: #ffffff;
                font-weight: 600;
                font-size: 0.9rem;
                padding: 10px 20px;
                border-radius: 12px;
                box-shadow: 0 4px 14px rgba(245, 158, 11, 0.38);
                transition: all 0.2s ease;
            }
            #helpdeskUrgentModal .btn-action-helpdesk:hover {
                background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
                color: #ffffff;
                box-shadow: 0 6px 18px rgba(245, 158, 11, 0.48);
                transform: translateY(-1px);
            }
            #helpdeskUrgentModal .btn-dismiss-helpdesk {
                background: #f1f5f9;
                color: #475569;
                border: 1px solid #e2e8f0;
                font-weight: 600;
                font-size: 0.88rem;
                padding: 10px 18px;
                border-radius: 12px;
                transition: all 0.15s ease;
            }
            #helpdeskUrgentModal .btn-dismiss-helpdesk:hover {
                background: #e2e8f0;
                color: #1e293b;
            }
            html.dark-style #helpdeskUrgentModal .btn-dismiss-helpdesk {
                background: #334155;
                color: #e2e8f0;
                border-color: #475569;
            }
            html.dark-style #helpdeskUrgentModal .btn-dismiss-helpdesk:hover {
                background: #475569;
                color: #ffffff;
            }
        `;
        $('<style id="helpdeskUrgentModalStyles">' + css + '</style>').appendTo('head');
    }

    function showHelpdeskModal(ticket) {
        injectStyles();

        // Hindari menumpuk jika modal darurat lain sedang aktif
        if ($('#helpdeskUrgentModal.show').length || $('#prospectUrgentModal.show').length || $('#suoUrgentModal.show').length || isModalOpen) {
            return;
        }

        $('#helpdeskUrgentModal').remove();
        $('.modal-backdrop').remove();

        var avatarHtml = '';
        if (ticket.requester_image) {
            avatarHtml = '<img src="' + escapeHtml(ticket.requester_image) + '" class="rounded-circle me-1" style="width:22px;height:22px;object-fit:cover;" alt="' + escapeHtml(ticket.requester_name) + '">';
        } else {
            avatarHtml = '<span class="avatar-initial rounded-circle bg-label-warning me-1" style="width:22px;height:22px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;">' + (ticket.requester_name ? ticket.requester_name.charAt(0) : 'U') + '</span>';
        }

        var urlDisplayHtml = '';
        if (ticket.url_accessed) {
            urlDisplayHtml = `
                <div class="helpdesk-info-row">
                    <span class="helpdesk-info-label">
                        <i class="mdi mdi-link-variant text-primary fs-6"></i> Halaman Terkait
                    </span>
                    <span class="helpdesk-info-val text-truncate" title="${escapeHtml(ticket.url_accessed)}">
                        <a href="${escapeHtml(ticket.url_accessed)}" target="_blank" class="text-primary text-decoration-underline font-12">
                            ${escapeHtml(ticket.url_accessed.replace(/^https?:\/\/[^\/]+/, '')) || '/'}
                        </a>
                    </span>
                </div>
            `;
        }

        var modalHtml = `
            <div class="modal fade" id="helpdeskUrgentModal" tabindex="-1" aria-labelledby="helpdeskUrgentModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0">
                        <!-- Top Shimmer Line -->
                        <div class="helpdesk-top-stripe"></div>

                        <div class="modal-body p-4 pt-3">
                            <!-- Top Status Badge & Close Button -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge rounded-pill px-3 py-1 text-uppercase fw-bold" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <i class="mdi mdi-lifebuoy me-1"></i> TIKET HELPDESK BARU &bull; PERLU RESPON
                                </span>
                                <button type="button" class="btn-close btn-helpdesk-dismiss" data-ticket-id="${ticket.id}" aria-label="Close"></button>
                            </div>

                            <!-- Hero Icon & Title -->
                            <div class="text-center mb-3">
                                <div class="helpdesk-hero-halo mx-auto mb-2">
                                    <div class="helpdesk-hero-inner">
                                        <i class="mdi mdi-ticket-account fs-2"></i>
                                    </div>
                                </div>

                                <h4 class="modal-title fw-bold text-dark mb-1" id="helpdeskUrgentModalLabel" style="letter-spacing: -0.3px;">
                                    Laporan Kendala User Baru!
                                </h4>
                                <p class="text-muted small mb-0 px-2" style="line-height: 1.55;">
                                    User telah mengirimkan tiket kendala baru ke Helpdesk. Mohon segera dicek dan ditindaklanjuti.
                                </p>
                            </div>

                            <!-- Structured Detail Card -->
                            <div class="helpdesk-detail-card mb-3">
                                <div class="helpdesk-card-header d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2 text-truncate pe-2">
                                        <span class="badge rounded-pill bg-label-warning flex-shrink-0 font-12 fw-bold">
                                            ${escapeHtml(ticket.no_ticket)}
                                        </span>
                                        <span class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;" title="${escapeHtml(ticket.title)}">
                                            ${escapeHtml(ticket.title)}
                                        </span>
                                    </div>
                                    <span class="text-muted flex-shrink-0 font-11">
                                        <i class="mdi mdi-clock-outline me-1"></i>${escapeHtml(ticket.created_at_time || ticket.created_at)}
                                    </span>
                                </div>
                                <div class="helpdesk-card-body">
                                    <div class="helpdesk-info-row">
                                        <span class="helpdesk-info-label">
                                            <i class="mdi mdi-account-outline text-secondary fs-6"></i> Pelapor
                                        </span>
                                        <span class="helpdesk-info-val d-inline-flex align-items-center justify-content-end">
                                            ${avatarHtml}
                                            <span class="text-truncate">${escapeHtml(ticket.requester_name)} (${escapeHtml(ticket.requester_role)})</span>
                                        </span>
                                    </div>
                                    ${urlDisplayHtml}
                                    <div class="mt-2 pt-1">
                                        <span class="helpdesk-info-label mb-1">
                                            <i class="mdi mdi-text-box-outline text-warning fs-6"></i> Deskripsi Masalah:
                                        </span>
                                        <div class="helpdesk-desc-box mt-1">
                                            ${escapeHtml(ticket.description || 'Tidak ada deskripsi rinci.')}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex align-items-center gap-2 pt-1 flex-wrap">
                                <!-- Dropdown Snooze -->
                                <div class="dropdown">
                                    <button class="btn btn-dismiss-helpdesk dropdown-toggle d-inline-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Tunda pop-up alert ini">
                                        <i class="mdi mdi-clock-alert-outline fs-6 text-warning"></i>
                                        <span>Snooze</span>
                                    </button>
                                    <ul class="dropdown-menu shadow-sm dropdown-menu-start" style="border-radius: 12px; font-size: 0.85rem; min-width: 210px;">
                                        <li><h6 class="dropdown-header text-muted font-11 text-uppercase mb-0">Tunda Alert Pop-up</h6></li>
                                        <li><a class="dropdown-item btn-snooze-helpdesk d-flex align-items-center" href="javascript:void(0);" data-ms="900000" data-ticket-id="${ticket.id}"><i class="mdi mdi-clock-fast text-warning me-2"></i>15 Menit</a></li>
                                        <li><a class="dropdown-item btn-snooze-helpdesk d-flex align-items-center" href="javascript:void(0);" data-ms="3600000" data-ticket-id="${ticket.id}"><i class="mdi mdi-clock-outline text-info me-2"></i>1 Jam</a></li>
                                        <li><a class="dropdown-item btn-snooze-helpdesk d-flex align-items-center" href="javascript:void(0);" data-ms="14400000" data-ticket-id="${ticket.id}"><i class="mdi mdi-timer-sand text-secondary me-2"></i>4 Jam</a></li>
                                        <li><a class="dropdown-item btn-snooze-helpdesk d-flex align-items-center" href="javascript:void(0);" data-ms="86400000" data-ticket-id="${ticket.id}"><i class="mdi mdi-calendar-today text-primary me-2"></i>1 Hari Penuh</a></li>
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li><a class="dropdown-item btn-snooze-helpdesk text-danger d-flex align-items-center" href="javascript:void(0);" data-ms="604800000" data-ticket-id="${ticket.id}"><i class="mdi mdi-bell-off-outline me-2"></i>Nonaktifkan 7 Hari</a></li>
                                    </ul>
                                </div>

                                <button type="button" class="btn btn-dismiss-helpdesk btn-helpdesk-dismiss flex-shrink-0" data-ticket-id="${ticket.id}">
                                    Nanti Dulu
                                </button>
                                <a href="${escapeHtml(ticket.action_url || '/helpdesk')}" class="btn btn-action-helpdesk btn-helpdesk-action flex-grow-1 text-center d-inline-flex align-items-center justify-content-center gap-1" data-ticket-id="${ticket.id}">
                                    <span>Buka di Helpdesk</span>
                                    <i class="mdi mdi-arrow-right fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modalHtml);

        var modalEl = document.getElementById('helpdeskUrgentModal');
        var opened = false;

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                var bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
                bsModal.show();
                opened = true;
            } catch (e) {
                console.warn('[HelpdeskUrgentAlert] bootstrap.Modal error:', e);
            }
        }
        if (!opened && typeof $ !== 'undefined' && $.fn && $.fn.modal) {
            try {
                $('#helpdeskUrgentModal').modal({ backdrop: 'static', keyboard: false, show: true });
                opened = true;
            } catch (e) {
                console.warn('[HelpdeskUrgentAlert] jQuery.modal error:', e);
            }
        }

        if (opened) {
            isModalOpen = true;
            playHelpdeskAlarm();
        }
    }

    // Event listener saat modal ditutup
    $(document).on('hidden.bs.modal', '#helpdeskUrgentModal', function () {
        isModalOpen = false;
        $('#helpdeskUrgentModal').remove();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');
    });

    // Event listener tombol Nanti Dulu & Close
    $(document).on('click', '.btn-helpdesk-dismiss', function () {
        var ticketId = $(this).data('ticket-id');
        if (ticketId) {
            setDismissed(ticketId, 900000); // 15 menit
        }
        closeHelpdeskModal();
    });

    // Event listener tombol Snooze durasi
    $(document).on('click', '.btn-snooze-helpdesk', function () {
        var ms = parseInt($(this).data('ms'), 10) || 900000;
        var ticketId = $(this).data('ticket-id');
        if (ticketId) {
            setDismissed(ticketId, ms);
        }
        closeHelpdeskModal();
    });

    // Event listener tombol Buka di Helpdesk
    $(document).on('click', '.btn-helpdesk-action', function () {
        var ticketId = $(this).data('ticket-id');
        if (ticketId) {
            setDismissed(ticketId, 300000); // Tunda 5 menit agar tidak langsung pop up lagi saat load page
        }
        closeHelpdeskModal();
    });

    function closeHelpdeskModal() {
        var modalEl = document.getElementById('helpdeskUrgentModal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                var inst = bootstrap.Modal.getInstance(modalEl);
                if (inst) inst.hide();
            } catch (e) {}
        }
        if (typeof $ !== 'undefined' && $('#helpdeskUrgentModal').length) {
            $('#helpdeskUrgentModal').modal('hide');
        }
        setTimeout(function () {
            isModalOpen = false;
            $('#helpdeskUrgentModal').remove();
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css('overflow', '');
        }, 300);
    }

    // Fungsi Polling Server
    function checkUrgentTickets() {
        if (isModalOpen || $('#helpdeskUrgentModal.show').length || $('#prospectUrgentModal.show').length || $('#suoUrgentModal.show').length) {
            return;
        }

        $.ajax({
            url: checkUrl,
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res && res.has_urgent && res.ticket) {
                    var ticket = res.ticket;
                    if (!isDismissed(ticket.id)) {
                        showHelpdeskModal(ticket);
                    }
                }
            },
            error: function (xhr, status, err) {
                // Silent fail
            }
        });
    }

    // Start polling loop
    setTimeout(checkUrgentTickets, 3000);
    pollTimer = setInterval(checkUrgentTickets, 10000); // Polling setiap 10 detik
});
