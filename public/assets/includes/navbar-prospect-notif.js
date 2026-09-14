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

    function isDismissed(notifId) {
        try {
            var key = 'dismissed_prospect_urgent_' + notifId;
            return sessionStorage.getItem(key) === '1';
        } catch (e) {
            return false;
        }
    }

    function setDismissed(notifId) {
        try {
            var key = 'dismissed_prospect_urgent_' + notifId;
            sessionStorage.setItem(key, '1');
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
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.7); }
                70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(105, 108, 255, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(105, 108, 255, 0); }
            }
            @keyframes prospectPingDotSuccess {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 199, 111, 0.7); }
                70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(40, 199, 111, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 199, 111, 0); }
            }
            @keyframes prospectShimmer {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
            #prospectUrgentModal .modal-dialog {
                max-width: 540px;
            }
            #prospectUrgentModal .modal-content {
                border-radius: 18px !important;
                border: 1px solid rgba(105, 108, 255, 0.25) !important;
                box-shadow: 0 24px 48px -12px rgba(105, 108, 255, 0.28), 0 4px 16px rgba(0, 0, 0, 0.06) !important;
                overflow: hidden;
                background: #ffffff;
            }
            html.dark-style #prospectUrgentModal .modal-content {
                background: #2b2c40 !important;
                border-color: rgba(105, 108, 255, 0.4) !important;
                box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.6) !important;
                color: #e4e6f0;
            }
            #prospectUrgentModal .prospect-top-stripe {
                height: 5px;
                background: linear-gradient(90deg, #696cff, #8a8dff, #38bdf8, #696cff);
                background-size: 200% 100%;
                animation: prospectShimmer 3s ease-in-out infinite;
            }
            #prospectUrgentModal.modal-assigned .prospect-top-stripe {
                background: linear-gradient(90deg, #28c76f, #48da89, #71dd37, #28c76f);
                background-size: 200% 100%;
                animation: prospectShimmer 3s ease-in-out infinite;
            }
            #prospectUrgentModal .prospect-badge-alert {
                background: #eef2ff;
                color: #696cff;
                font-weight: 700;
                font-size: 0.76rem;
                letter-spacing: 0.5px;
                padding: 4px 11px;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border: 1px solid rgba(105, 108, 255, 0.25);
            }
            #prospectUrgentModal .prospect-badge-alert.prospect-badge-assigned {
                background: #e8fadf;
                color: #28c76f;
                border-color: rgba(40, 199, 111, 0.25);
            }
            #prospectUrgentModal .prospect-pulse-dot {
                width: 8px;
                height: 8px;
                background-color: #696cff;
                border-radius: 50%;
                display: inline-block;
                animation: prospectPingDot 1.4s infinite;
            }
            #prospectUrgentModal .prospect-pulse-dot-success {
                width: 8px;
                height: 8px;
                background-color: #28c76f;
                border-radius: 50%;
                display: inline-block;
                animation: prospectPingDotSuccess 1.4s infinite;
            }
            #prospectUrgentModal .prospect-info-card {
                background: #f8f9fc;
                border: 1px solid #edf0f5;
                border-radius: 12px;
                padding: 14px 16px;
            }
            html.dark-style #prospectUrgentModal .prospect-info-card {
                background: #32344d;
                border-color: #3b3e5b;
            }
            #prospectUrgentModal .prospect-info-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px dashed #e4e7ed;
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
                color: #6c757d;
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
                color: #2b303a;
                text-align: right;
                max-width: 65%;
            }
            html.dark-style #prospectUrgentModal .prospect-info-val {
                color: #e4e6f0;
            }
            #prospectUrgentModal .prospect-notice-bar {
                background: #f0f4ff;
                border: 1px solid #d5e0ff;
                border-radius: 10px;
                padding: 10px 12px;
                font-size: 0.81rem;
                color: #3b4cb8;
                display: flex;
                align-items: flex-start;
                gap: 8px;
            }
            html.dark-style #prospectUrgentModal .prospect-notice-bar {
                background: #2a2e4e;
                border-color: #3d4576;
                color: #a8b6ff;
            }
            #prospectUrgentModal.modal-assigned .prospect-notice-bar {
                background: #eefaf2;
                border-color: #c7eed5;
                color: #1a7842;
            }
            html.dark-style #prospectUrgentModal.modal-assigned .prospect-notice-bar {
                background: #1e3a2b;
                border-color: #2a583e;
                color: #8be0ab;
            }
            #prospectUrgentModal .btn-action-prospect {
                background: linear-gradient(135deg, #696cff, #5053e6);
                border: none;
                color: #ffffff;
                font-weight: 600;
                font-size: 0.88rem;
                padding: 9px 18px;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
                transition: all 0.2s ease;
            }
            #prospectUrgentModal .btn-action-prospect:hover {
                background: linear-gradient(135deg, #5b5ee6, #4346d8);
                color: #ffffff;
                box-shadow: 0 6px 16px rgba(105, 108, 255, 0.45);
                transform: translateY(-1px);
            }
            #prospectUrgentModal.modal-assigned .btn-action-prospect {
                background: linear-gradient(135deg, #28c76f, #1f9d57);
                box-shadow: 0 4px 12px rgba(40, 199, 111, 0.35);
            }
            #prospectUrgentModal.modal-assigned .btn-action-prospect:hover {
                background: linear-gradient(135deg, #22b663, #198649);
                box-shadow: 0 6px 16px rgba(40, 199, 111, 0.45);
            }
            #prospectUrgentModal .btn-dismiss-prospect {
                background: #f1f3f6;
                color: #5c6370;
                border: none;
                font-weight: 500;
                font-size: 0.88rem;
                padding: 9px 16px;
                border-radius: 10px;
                transition: all 0.15s ease;
            }
            #prospectUrgentModal .btn-dismiss-prospect:hover {
                background: #e4e7ed;
                color: #2b303a;
            }
            html.dark-style #prospectUrgentModal .btn-dismiss-prospect {
                background: #3b3e5b;
                color: #c7c9d9;
            }
            html.dark-style #prospectUrgentModal .btn-dismiss-prospect:hover {
                background: #464a6e;
                color: #ffffff;
            }
        `;
        $('<style id="prospectUrgentModalStyles">' + css + '</style>').appendTo('head');
    }

    function showProspectModal(item) {
        injectStyles();

        // Jangan buka modal baru jika modal SUO atau modal Prospect sedang tampil
        if ($('#suoUrgentModal.show').length || $('#prospectUrgentModal.show').length) {
            return;
        }

        $('#prospectUrgentModal').remove();

        var isAssigned = (item.type === 'prospect_assigned');
        var modalThemeClass = isAssigned ? 'modal-assigned' : '';
        var badgeText = isAssigned ? 'PROSPECT DITUGASKAN' : 'PROSPECT BARU';
        var badgeClass = isAssigned ? 'prospect-badge-assigned' : '';
        var pulseDotHtml = isAssigned ? '<span class="prospect-pulse-dot-success"></span>' : '<span class="prospect-pulse-dot"></span>';
        var stageBadge = item.stage_badge || (isAssigned ? 'Ditugaskan ke Anda' : 'Baru Dibuat');
        var stageTitle = item.stage_title || (isAssigned ? 'Anda Mendapat Penugasan Prospect Baru!' : 'Ada Prospect Baru Masuk!');
        var stageDesc = item.stage_desc || (isAssigned
            ? 'Prospek baru telah didelegasikan kepada Anda. Segera lakukan follow-up ke pelanggan untuk proses penawaran.'
            : 'Tim Support baru saja menginput data prospek baru yang belum ditugaskan ke Sales. Segera tentukan dan tugaskan Sales penanggung jawab.');
        var actionLabel = item.action_label || (isAssigned ? 'Buka Prospek' : 'Tugaskan ke Sales');

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
                    <div class="modal-content">
                        <!-- Top Accent Line -->
                        <div class="prospect-top-stripe"></div>

                        <!-- Modal Header -->
                        <div class="p-4 pb-2 d-flex align-items-start justify-content-between border-0">
                            <div class="d-flex align-items-center gap-2">
                                <div class="prospect-badge-alert ${badgeClass}">
                                    ${pulseDotHtml}
                                    <span>${badgeText}</span>
                                </div>
                                <span class="badge ${isAssigned ? 'bg-label-success' : 'bg-label-primary'} rounded-pill fw-semibold" style="font-size: 0.74rem;">
                                    ${stageBadge}
                                </span>
                            </div>
                            <span class="text-muted" style="font-size: 0.78rem;">
                                <i class="mdi mdi-clock-outline me-1"></i>${escapeHtml(item.created_at_time || item.created_at)}
                            </span>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body px-4 pt-1 pb-3">
                            <div class="mb-3">
                                <h5 class="fw-bold text-dark mb-1" id="prospectUrgentModalLabel">${stageTitle}</h5>
                                <p class="text-muted small mb-0" style="line-height: 1.45;">${stageDesc}</p>
                            </div>

                            <!-- Structured Info Card -->
                            <div class="prospect-info-card mb-3">
                                <div class="prospect-info-row">
                                    <span class="prospect-info-label">
                                        <i class="mdi mdi-domain ${isAssigned ? 'text-success' : 'text-primary'} fs-6"></i> Perusahaan / Customer
                                    </span>
                                    <span class="prospect-info-val text-truncate fw-bold ${isAssigned ? 'text-success' : 'text-primary'}" title="${escapeHtml(item.company)}">
                                        ${escapeHtml(item.company)}
                                    </span>
                                </div>
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

                            <!-- Notice Alert -->
                            <div class="prospect-notice-bar">
                                <i class="mdi mdi-information-outline fs-5 flex-shrink-0" style="margin-top: -1px;"></i>
                                <div>Segera respon dan tindak lanjuti prospek ini untuk meningkatkan peluang dealing & kepuasan pelanggan.</div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="p-3 px-4 bg-light border-top d-flex align-items-center justify-content-between">
                            <button type="button" class="btn-dismiss-prospect btn-prospect-dismiss" data-notif-id="${item.id}">
                                Nanti Dulu
                            </button>
                            <a href="${item.url}" class="btn-action-prospect d-inline-flex align-items-center gap-1 btn-prospect-action" data-notif-id="${item.id}">
                                <span>${actionLabel}</span>
                                <i class="mdi mdi-arrow-right fs-5"></i>
                            </a>
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
                var bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
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

    // Klik tombol Buka Prospek -> tandai dibaca & simpan dismissal
    $(document).on('click', '.btn-prospect-action', function () {
        var notifId = $(this).data('notif-id');
        if (notifId) {
            setDismissed(notifId);
            markRead(notifId);
        }
        isModalOpen = false;
    });

    // Klik tombol Nanti Dulu -> tutup modal & simpan dismissal agar tidak pop-up berulang kali di sesi ini
    $(document).on('click', '.btn-prospect-dismiss', function () {
        var notifId = $(this).data('notif-id');
        if (notifId) {
            setDismissed(notifId);
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
                                </div>' +
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

    function checkAndShowProspectModal(items) {
        if (isModalOpen) return;

        // Cari notifikasi prospect belum dibaca dan belum di-dismiss
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            if (item.is_read) continue;
            if (item.type !== 'prospect_created' && item.type !== 'prospect_assigned') continue;
            if (isDismissed(item.id)) continue;

            // Tampilkan modal untuk prospect pertama yang ditemukan
            showProspectModal(item);
            break;
        }
    }

    function poll() {
        $.getJSON(pollUrl).done(function (res) {
            var items = res.items || [];
            var unreadCount = items.filter(function (i) { return !i.is_read; }).length;
            if (unreadCount > 0) {
                $('#navbarBellDot').removeClass('d-none');
            }
            checkAndShowProspectModal(items);
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
                if (!isDismissed(p.id)) {
                    showProspectModal({
                        id: p.id,
                        type: 'prospect_created',
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
