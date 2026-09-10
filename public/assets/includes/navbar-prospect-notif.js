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

    // Key untuk menyimpan ID toast yang sudah ditutup user agar tidak berulang-ulang popup
    var STORAGE_DISMISSED_KEY = 'dismissed_prospect_toast_ids';
    function getDismissedToastIds() {
        try {
            var raw = localStorage.getItem(STORAGE_DISMISSED_KEY) || sessionStorage.getItem(STORAGE_DISMISSED_KEY) || '[]';
            return JSON.parse(raw).map(function (id) { return Number(id); });
        } catch (e) {
            return [];
        }
    }
    function addDismissedToastId(id) {
        try {
            var numId = Number(id);
            if (isNaN(numId)) return;
            var dismissed = getDismissedToastIds();
            if (dismissed.indexOf(numId) === -1) {
                dismissed.push(numId);
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
                max-width: 410px;
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
                0% {
                    transform: scale(0.95);
                    box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.7);
                }
                70% {
                    transform: scale(1);
                    box-shadow: 0 0 0 8px rgba(105, 108, 255, 0);
                }
                100% {
                    transform: scale(0.95);
                    box-shadow: 0 0 0 0 rgba(105, 108, 255, 0);
                }
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

            // Tone 1 (E5 - 659.25 Hz)
            var osc1 = audioCtx.createOscillator();
            var gain1 = audioCtx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(659.25, now);
            gain1.gain.setValueAtTime(0, now);
            gain1.gain.linearRampToValueAtTime(0.3, now + 0.02);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc1.connect(gain1);
            gain1.connect(audioCtx.destination);
            osc1.start(now);
            osc1.stop(now + 0.36);

            // Tone 2 (A5 - 880 Hz)
            var osc2 = audioCtx.createOscillator();
            var gain2 = audioCtx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880.0, now + 0.12);
            gain2.gain.setValueAtTime(0, now + 0.12);
            gain2.gain.linearRampToValueAtTime(0.35, now + 0.14);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.65);
            osc2.connect(gain2);
            gain2.connect(audioCtx.destination);
            osc2.start(now + 0.12);
            osc2.stop(now + 0.66);
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
                    activeToastIds.delete(Number(notifId));
                    activeToastIds.delete(notifId);
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
        var toastDomId = 'prospectToast_' + item.id;
        var kebutuhan = item.kebutuhan ? escapeHtml(item.kebutuhan) : '-';
        var support = item.support_name ? escapeHtml(item.support_name) : 'Support';

        var toastHtml = $(
            '<div id="' + toastDomId + '" class="prospect-floating-toast" role="alert" aria-live="assertive" aria-atomic="true">' +
                '<div class="toast-header-custom">' +
                    '<span class="prospect-toast-pulse"></span>' +
                    '<i class="mdi mdi-account-star-outline fs-5 text-primary"></i>' +
                    '<strong class="me-auto fs-6 fw-bold">Prospect Baru</strong>' +
                    '<small class="text-muted">' + escapeHtml(item.created_at) + '</small>' +
                    '<button type="button" class="btn-close ms-2 fs-7 btn-toast-dismiss" data-toast-id="' + toastDomId + '" data-notif-id="' + item.id + '" aria-label="Close"></button>' +
                '</div>' +
                '<div class="toast-body-custom py-2">' +
                    '<div class="fw-bold text-truncate my-1 fs-6" title="' + escapeHtml(item.company) + '">' +
                        '<i class="mdi mdi-office-building-outline text-muted me-1"></i>' + escapeHtml(item.company) +
                    '</div>' +
                    (item.category ? '<div class="small mb-1"><span class="badge bg-label-info">' + escapeHtml(item.category) + '</span></div>' : '') +
                    '<div class="small text-muted">' + kebutuhan + '</div>' +
                    '<div class="small text-muted mt-1"><i class="mdi mdi-account-tie-outline"></i> Dibuat oleh: ' + support + '</div>' +
                '</div>' +
                '<div class="toast-footer-custom justify-content-between">' +
                    '<a href="' + item.url + '" class="btn btn-xs btn-primary btn-toast-go d-inline-flex align-items-center gap-1 flex-grow-1" data-notif-id="' + item.id + '">' +
                        '<i class="mdi mdi-eye-outline"></i> Lihat Prospek' +
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
            var numId = Number(item.id);
            if (item.is_read) return;
            if (item.type !== 'prospect_created') return;
            if (dismissed.indexOf(numId) !== -1) return;
            if (activeToastIds.has(numId) || activeToastIds.has(item.id)) return;

            activeToastIds.add(numId);
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

    function poll() {
        $.getJSON(pollUrl).done(function (res) {
            checkAndShowFloatingToasts(res.items || []);
        });
    }

    poll();
    setInterval(poll, 7000);

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') poll();
    });
});
