/**
 * Pop-up notifikasi "Prospect Baru" untuk Sales Manager / Admin.
 *
 * Versi ramping dari navbar-payment-notif.js: hanya floating toast (tanpa integrasi lonceng
 * navbar), polling AJAX tiap 7 detik, dedupe via localStorage, chime WebAudio.
 * Hanya menangani item bertipe `prospect_created`.
 */
$(function () {
    var pollUrl = window.prospectNotifUnreadUrl;
    var readUrlTemplate = window.prospectNotifReadUrlTemplate; // mengandung "__ID__"

    if (!pollUrl) return;

    var audioCtx = null;
    var activeToastIds = new Set();

    // Unlock Web Audio Context saat interaksi pertama user (kebijakan autoplay browser)
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

    // Key untuk menyimpan ID toast yang sudah ditutup user agar tidak berulang-ulang popup
    var STORAGE_DISMISSED_KEY = 'dismissed_prospect_toast_ids';
    function getDismissedToastIds() {
        try {
            var raw = localStorage.getItem(STORAGE_DISMISSED_KEY) || sessionStorage.getItem(STORAGE_DISMISSED_KEY) || '[]';
            return JSON.parse(raw).map(function (id) { return String(id); });
        } catch (e) {
            return [];
        }
    }
    function addDismissedToastId(id) {
        try {
            var strId = String(id);
            var dismissed = getDismissedToastIds();
            if (dismissed.indexOf(strId) === -1) {
                dismissed.push(strId);
                var str = JSON.stringify(dismissed);
                try { localStorage.setItem(STORAGE_DISMISSED_KEY, str); } catch (e) {}
                try { sessionStorage.setItem(STORAGE_DISMISSED_KEY, str); } catch (e) {}
            }
        } catch (e) {}
    }

    // Suntikkan CSS Floating Toast ke <head> jika belum ada
    function injectToastStyles() {
        if ($('#prospectFloatingToastStyles').length) return;
        var css = `
            #prospectFloatingToastContainer {
                position: fixed;
                bottom: 24px;
                right: 24px;
                z-index: 999990;
                display: flex;
                flex-direction: column-reverse;
                gap: 12px;
                max-width: 420px;
                width: calc(100vw - 36px);
                pointer-events: none;
            }
            .prospect-floating-toast {
                pointer-events: auto;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(105, 108, 255, 0.3);
                border-left: 5px solid #696cff;
                border-radius: 14px;
                box-shadow: 0 12px 32px rgba(34, 48, 62, 0.22), 0 2px 6px rgba(0,0,0,0.08);
                overflow: hidden;
                transform: translateY(0);
                opacity: 1;
                transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
                animation: prospectToastSlideIn 0.38s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            html.dark-style .prospect-floating-toast {
                background: rgba(43, 44, 64, 0.97);
                border-color: rgba(105, 108, 255, 0.45);
                border-left: 5px solid #696cff;
                box-shadow: 0 12px 32px rgba(0, 0, 0, 0.5);
                color: #e4e6f0;
            }
            .prospect-floating-toast.toast-hiding {
                opacity: 0;
                transform: translateX(110%);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            @keyframes prospectToastSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(28px) scale(0.96);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            .prospect-floating-toast .toast-header-custom {
                padding: 12px 16px 8px 16px;
                display: flex;
                align-items: center;
                gap: 10px;
                border-bottom: 1px dashed rgba(0,0,0,0.07);
            }
            html.dark-style .prospect-floating-toast .toast-header-custom {
                border-bottom-color: rgba(255,255,255,0.08);
            }
            .prospect-floating-toast .toast-body-custom {
                padding: 12px 16px;
            }
            .prospect-floating-toast .toast-footer-custom {
                padding: 8px 16px 12px 16px;
                display: flex;
                align-items: center;
                gap: 8px;
                background: rgba(0,0,0,0.015);
                border-top: 1px solid rgba(0,0,0,0.04);
            }
            html.dark-style .prospect-floating-toast .toast-footer-custom {
                background: rgba(255,255,255,0.02);
                border-top-color: rgba(255,255,255,0.06);
            }
            .prospect-toast-pulse {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: #696cff;
                display: inline-block;
                position: relative;
                box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.7);
                animation: prospectToastPulse 1.8s infinite;
            }
            @keyframes prospectToastPulse {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(105, 108, 255, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(105, 108, 255, 0); }
            }
            .prospect-floating-toast.prospect-toast-assigned {
                border-left: 5px solid #28c76f;
                border-color: rgba(40, 199, 111, 0.35);
            }
            .prospect-toast-pulse-success {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: #28c76f;
                display: inline-block;
                position: relative;
                box-shadow: 0 0 0 0 rgba(40, 199, 111, 0.7);
                animation: prospectToastPulseSuccess 1.8s infinite;
            }
            @keyframes prospectToastPulseSuccess {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 199, 111, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(40, 199, 111, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(40, 199, 111, 0); }
            }
            .prospect-floating-toast.prospect-toast-mention {
                border-left: 5px solid #ff9f43;
                border-color: rgba(255, 159, 67, 0.35);
            }
            .prospect-toast-pulse-warning {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: #ff9f43;
                display: inline-block;
                position: relative;
                box-shadow: 0 0 0 0 rgba(255, 159, 67, 0.7);
                animation: prospectToastPulseWarning 1.8s infinite;
            }
            @keyframes prospectToastPulseWarning {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 159, 67, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(255, 159, 67, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 159, 67, 0); }
            }
            .prospect-floating-toast.prospect-toast-comment {
                border-left: 5px solid #00cfe8;
                border-color: rgba(0, 207, 232, 0.35);
            }
            .prospect-toast-pulse-info {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: #00cfe8;
                display: inline-block;
                position: relative;
                box-shadow: 0 0 0 0 rgba(0, 207, 232, 0.7);
                animation: prospectToastPulseInfo 1.8s infinite;
            }
            @keyframes prospectToastPulseInfo {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 207, 232, 0.7); }
                70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(0, 207, 232, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(0, 207, 232, 0); }
            }
        `;
        $('<style id="prospectFloatingToastStyles">' + css + '</style>').appendTo('head');
    }

    function ensureToastContainer() {
        var $container = $('#prospectFloatingToastContainer');
        if (!$container.length) {
            $container = $('<div id="prospectFloatingToastContainer" aria-live="polite" aria-atomic="true"></div>');
            $('body').append($container);
        }
        return $container;
    }

    function executeChimeSound() {
        try {
            if (!audioCtx) return;
            var now = audioCtx.currentTime;

            // Tone 1 (D5 - 587.33 Hz)
            var osc1 = audioCtx.createOscillator();
            var gain1 = audioCtx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, now);
            gain1.gain.setValueAtTime(0, now);
            gain1.gain.linearRampToValueAtTime(0.28, now + 0.02);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.32);
            osc1.connect(gain1);
            gain1.connect(audioCtx.destination);
            osc1.start(now);
            osc1.stop(now + 0.33);

            // Tone 2 (A5 - 880 Hz)
            var osc2 = audioCtx.createOscillator();
            var gain2 = audioCtx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880.0, now + 0.11);
            gain2.gain.setValueAtTime(0, now + 0.11);
            gain2.gain.linearRampToValueAtTime(0.32, now + 0.13);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
            osc2.connect(gain2);
            gain2.connect(audioCtx.destination);
            osc2.start(now + 0.11);
            osc2.stop(now + 0.56);

            // Tone 3 (D6 - 1174.66 Hz)
            var osc3 = audioCtx.createOscillator();
            var gain3 = audioCtx.createGain();
            osc3.type = 'sine';
            osc3.frequency.setValueAtTime(1174.66, now + 0.22);
            gain3.gain.setValueAtTime(0, now + 0.22);
            gain3.gain.linearRampToValueAtTime(0.35, now + 0.24);
            gain3.gain.exponentialRampToValueAtTime(0.001, now + 0.75);
            osc3.connect(gain3);
            gain3.connect(audioCtx.destination);
            osc3.start(now + 0.22);
            osc3.stop(now + 0.76);
        } catch (e) {
            console.warn('Audio chime error:', e);
        }
    }

    function playBeep() {
        try {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            if (!audioCtx) {
                audioCtx = new Ctx();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume().then(function () {
                    executeChimeSound();
                }).catch(function () {});
            } else {
                executeChimeSound();
            }
        } catch (e) {
            // Autoplay policy atau browser tak mendukung — abaikan.
        }
    }

    function escapeHtml(str) {
        return $('<div>').text(str == null ? '' : str).html();
    }

    function markRead(id) {
        if (!readUrlTemplate) return;
        $.post(readUrlTemplate.replace('__ID__', id), { _token: window.csrfToken });
    }

    function dismissToast(toastId, notifId) {
        var $toast = $('#' + toastId);
        if ($toast.length) {
            $toast.addClass('toast-hiding');
            setTimeout(function () {
                $toast.remove();
                if (notifId) {
                    activeToastIds.delete(String(notifId));
                }
            }, 320);
        }
        if (notifId) {
            addDismissedToastId(notifId);
            // Tandai dibaca ke server agar tidak muncul lagi saat navigasi/reload halaman
            markRead(notifId);
        }
    }

    function renderToast($container, item) {
        var isAssigned = item.type === 'prospect_assigned';
        var isMention = item.type === 'comment_mention';
        var isComment = item.type === 'prospect_comment';
        var toastDomId = 'prospectToast_' + String(item.id).replace(/[^a-zA-Z0-9_]/g, '_');

        var titleText = 'Prospect Baru';
        var titleIcon = '<i class="mdi mdi-account-star-outline fs-5 text-primary"></i>';
        var pulseEl = '<span class="prospect-toast-pulse"></span>';
        var extraClass = '';
        var btnColor = 'btn-primary';
        var btnIcon = 'mdi-eye-outline';
        var btnLabel = 'Lihat Prospek';
        var bodyContent = '';

        if (isMention) {
            titleText = 'Kamu Di-mention di Prospek!';
            titleIcon = '<i class="mdi mdi-at fs-5 text-warning"></i>';
            pulseEl = '<span class="prospect-toast-pulse-warning"></span>';
            extraClass = ' prospect-toast-mention';
            btnColor = 'btn-warning text-dark';
            btnIcon = 'mdi-message-reply-text-outline';
            btnLabel = 'Lihat Mention';
            bodyContent =
                '<div class="fw-bold text-truncate my-1 fs-6" title="' + escapeHtml(item.company) + '">' +
                    '<i class="mdi mdi-office-building-outline text-muted me-1"></i>' + escapeHtml(item.company) +
                '</div>' +
                '<div class="p-2 rounded mt-1 border" style="background: rgba(255, 159, 67, 0.08); border-color: rgba(255, 159, 67, 0.25) !important;">' +
                    '<div class="small fw-semibold text-dark mb-1 d-flex align-items-center gap-1">' +
                        '<i class="mdi mdi-account-voice text-warning fs-6"></i> ' + escapeHtml(item.author_name) + ' me-mention Anda:' +
                    '</div>' +
                    '<div class="small text-secondary" style="font-style: italic; line-height: 1.4;">"' + escapeHtml(item.comment) + '"</div>' +
                '</div>';
        } else if (isComment) {
            titleText = 'Komentar Baru pada Prospek';
            titleIcon = '<i class="mdi mdi-comment-text-multiple-outline fs-5 text-info"></i>';
            pulseEl = '<span class="prospect-toast-pulse-info"></span>';
            extraClass = ' prospect-toast-comment';
            btnColor = 'btn-info';
            btnIcon = 'mdi-comment-eye-outline';
            btnLabel = 'Lihat Komentar';
            bodyContent =
                '<div class="fw-bold text-truncate my-1 fs-6" title="' + escapeHtml(item.company) + '">' +
                    '<i class="mdi mdi-office-building-outline text-muted me-1"></i>' + escapeHtml(item.company) +
                '</div>' +
                '<div class="p-2 rounded mt-1 border" style="background: rgba(0, 207, 232, 0.08); border-color: rgba(0, 207, 232, 0.25) !important;">' +
                    '<div class="small fw-semibold text-dark mb-1 d-flex align-items-center gap-1">' +
                        '<i class="mdi mdi-account-circle-outline text-info fs-6"></i> ' + escapeHtml(item.author_name) + ' berkomentar:' +
                    '</div>' +
                    '<div class="small text-secondary" style="font-style: italic; line-height: 1.4;">"' + escapeHtml(item.comment) + '"</div>' +
                '</div>';
        } else if (isAssigned) {
            titleText = 'Prospek Baru Ditugaskan!';
            titleIcon = '<i class="mdi mdi-account-arrow-right-outline fs-5 text-success"></i>';
            pulseEl = '<span class="prospect-toast-pulse-success"></span>';
            extraClass = ' prospect-toast-assigned';
            btnColor = 'btn-success';
            btnLabel = 'Lihat Prospek';
            var kebutuhan = item.kebutuhan ? escapeHtml(item.kebutuhan) : '-';
            var support = item.support_name ? escapeHtml(item.support_name) : 'Admin';
            bodyContent =
                '<div class="fw-bold text-truncate my-1 fs-6" title="' + escapeHtml(item.company) + '">' +
                    '<i class="mdi mdi-office-building-outline text-muted me-1"></i>' + escapeHtml(item.company) +
                '</div>' +
                (item.category ? '<div class="small mb-1"><span class="badge bg-label-success">' + escapeHtml(item.category) + '</span></div>' : '') +
                '<div class="small text-muted">' + kebutuhan + '</div>' +
                '<div class="small text-muted mt-1"><i class="mdi mdi-account-tie-outline"></i> Ditugaskan oleh: ' + support + '</div>';
        } else {
            var kebutuhan = item.kebutuhan ? escapeHtml(item.kebutuhan) : '-';
            var support = item.support_name ? escapeHtml(item.support_name) : 'Support';
            bodyContent =
                '<div class="fw-bold text-truncate my-1 fs-6" title="' + escapeHtml(item.company) + '">' +
                    '<i class="mdi mdi-office-building-outline text-muted me-1"></i>' + escapeHtml(item.company) +
                '</div>' +
                (item.category ? '<div class="small mb-1"><span class="badge bg-label-info">' + escapeHtml(item.category) + '</span></div>' : '') +
                '<div class="small text-muted">' + kebutuhan + '</div>' +
                '<div class="small text-muted mt-1"><i class="mdi mdi-account-tie-outline"></i> Dibuat oleh: ' + support + '</div>';
        }

        var toastHtml = $(
            '<div id="' + toastDomId + '" class="prospect-floating-toast' + extraClass + '" role="alert" aria-live="assertive" aria-atomic="true">' +
                '<div class="toast-header-custom">' +
                    pulseEl +
                    titleIcon +
                    '<strong class="me-auto fs-6 fw-bold' + (isAssigned ? ' text-success' : (isMention ? ' text-warning' : (isComment ? ' text-info' : ''))) + '">' + titleText + '</strong>' +
                    '<small class="text-muted">' + escapeHtml(item.created_at) + '</small>' +
                    '<button type="button" class="btn-close ms-2 fs-7 btn-toast-dismiss" data-toast-id="' + toastDomId + '" data-notif-id="' + item.id + '" aria-label="Close"></button>' +
                '</div>' +
                '<div class="toast-body-custom py-2">' +
                    bodyContent +
                '</div>' +
                '<div class="toast-footer-custom justify-content-between">' +
                    '<a href="' + item.url + '" class="btn btn-xs ' + btnColor + ' btn-toast-go d-inline-flex align-items-center gap-1 flex-grow-1" data-notif-id="' + item.id + '">' +
                        '<i class="mdi ' + btnIcon + '"></i> ' + btnLabel +
                    '</a>' +
                    '<button type="button" class="btn btn-xs btn-text-secondary btn-toast-dismiss" data-toast-id="' + toastDomId + '" data-notif-id="' + item.id + '">Nanti</button>' +
                '</div>' +
            '</div>'
        );

        $container.append(toastHtml);
    }

    function checkAndShowFloatingToasts(items) {
        injectToastStyles();
        var $container = ensureToastContainer();
        var dismissed = getDismissedToastIds();
        var hasNewToast = false;

        items.forEach(function (item) {
            var notifKey = String(item.id);
            if (item.is_read) return;
            var validTypes = ['prospect_created', 'prospect_assigned', 'prospect_comment', 'comment_mention'];
            if (validTypes.indexOf(item.type) === -1) return;
            if (dismissed.indexOf(notifKey) !== -1) return;
            if (activeToastIds.has(notifKey)) return;

            activeToastIds.add(notifKey);
            hasNewToast = true;
            renderToast($container, item);
        });

        if (hasNewToast) {
            playBeep();
        }
    }

    // Klik tombol dismiss (X / Nanti)
    $(document).on('click', '.prospect-floating-toast .btn-toast-dismiss', function (e) {
        e.preventDefault();
        dismissToast($(this).data('toast-id'), $(this).data('notif-id'));
    });

    // Klik "Lihat Prospek" — tandai dibaca sebelum pindah halaman
    $(document).on('click', '.prospect-floating-toast .btn-toast-go', function () {
        var notifId = $(this).data('notif-id');
        if (notifId) {
            addDismissedToastId(notifId);
            markRead(notifId);
        }
    });

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
                var isMention = (item.type === 'comment_mention');
                var isComment = (item.type === 'prospect_comment');

                var cardTypeClass = 'notif-card-prospect-new';
                var iconColor = 'bg-primary text-white';
                var iconClass = 'mdi-account-star-outline';
                var badgeColor = 'bg-label-primary';
                var badgeText = 'Prospect Baru';
                var dotClass = '';
                var descHtml = '';

                if (isMention) {
                    cardTypeClass = 'notif-card-mention';
                    iconColor = 'bg-warning text-white';
                    iconClass = 'mdi-at';
                    badgeColor = 'bg-label-warning';
                    badgeText = 'Mention Prospek';
                    dotClass = 'dot-warning';
                    descHtml = '<span class="fw-semibold text-dark">' + escapeHtml(item.author_name) + ' me-mention Anda:</span> "' + escapeHtml(item.comment) + '"';
                } else if (isComment) {
                    cardTypeClass = 'notif-card-comment';
                    iconColor = 'bg-info text-white';
                    iconClass = 'mdi-comment-text-multiple-outline';
                    badgeColor = 'bg-label-info';
                    badgeText = 'Komentar Prospek';
                    dotClass = 'dot-info';
                    descHtml = '<span class="fw-semibold text-dark">' + escapeHtml(item.author_name) + ' berkomentar:</span> "' + escapeHtml(item.comment) + '"';
                } else if (isAssigned) {
                    cardTypeClass = 'notif-card-prospect-assigned';
                    iconColor = 'bg-success text-white';
                    iconClass = 'mdi-account-arrow-right-outline';
                    badgeColor = 'bg-label-success';
                    badgeText = 'Ditugaskan';
                    dotClass = 'dot-success';
                    var catTextAssigned = item.category ? '<strong class="text-success">' + escapeHtml(item.category) + '</strong> • ' : '';
                    descHtml = 'Kategori: ' + catTextAssigned + escapeHtml(item.kebutuhan);
                } else {
                    var byText = item.support_name ? '<span class="fw-semibold text-dark">Oleh: ' + escapeHtml(item.support_name) + '</span> • ' : '';
                    var catTextCreated = item.category ? '<strong class="text-primary">' + escapeHtml(item.category) + '</strong> • ' : '';
                    descHtml = byText + 'Kategori: ' + catTextCreated + escapeHtml(item.kebutuhan);
                }

                var avatarHtml = '<div class="notif-card-avatar ' + iconColor + '"><i class="mdi ' + iconClass + '"></i></div>';
                if ((isMention || isComment) && item.author_image) {
                    avatarHtml = '<img src="' + escapeHtml(item.author_image) + '" alt="' + escapeHtml(item.author_name) + '" class="notif-card-avatar" style="object-fit: cover;">';
                }

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
            checkAndShowFloatingToasts(items);
            updateNavbarDropdown(items);
        }).fail(function (xhr, status, err) {
            console.warn('[ProspectNotif] Poll failed:', status, err);
        });
    }

    poll();
    setInterval(poll, 5000);

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') poll();
    });
});
