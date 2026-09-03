$(function () {
    var $dot = $('#navbarBellDot');
    var $icon = $('#navbarBellIcon');
    var $countBadge = $('#paymentNotifCountBadge');
    var $list = $('#paymentNotifList');
    var pollUrl = window.paymentNotifUnreadUrl;
    var readUrlTemplate = window.paymentNotifReadUrlTemplate; // contains "__ID__" placeholder
    var userRole = window.currentUserRole || '';

    if (!pollUrl || !$list.length) return;

    var lastCount = $list.find('.payment-notif-unread').length;
    var audioCtx = null;
    var activeToastIds = new Set();

    // Unlock Web Audio Context upon first user interaction to comply with browser autoplay policies
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

    // Key untuk menyimpan ID toast yang sudah ditutup user di session ini agar tidak berulang-ulang popup
    var STORAGE_DISMISSED_KEY = 'dismissed_invoice_toast_ids';
    function getDismissedToastIds() {
        try {
            return JSON.parse(sessionStorage.getItem(STORAGE_DISMISSED_KEY) || '[]');
        } catch (e) {
            return [];
        }
    }

    function addDismissedToastId(id) {
        try {
            var dismissed = getDismissedToastIds();
            if (dismissed.indexOf(id) === -1) {
                dismissed.push(id);
                sessionStorage.setItem(STORAGE_DISMISSED_KEY, JSON.stringify(dismissed));
            }
        } catch (e) {}
    }

    // Suntikkan CSS untuk Floating Toast Notification ke <head> jika belum ada
    function injectToastStyles() {
        if ($('#invoiceFloatingToastStyles').length) return;
        var css = `
            #invoiceFloatingToastContainer {
                position: fixed;
                bottom: 100px;
                right: 24px;
                z-index: 999990;
                display: flex;
                flex-direction: column-reverse;
                gap: 12px;
                max-width: 410px;
                width: calc(100vw - 36px);
                pointer-events: none;
            }
            .invoice-floating-toast {
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
                animation: invoiceToastSlideIn 0.38s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            html.dark-style .invoice-floating-toast {
                background: rgba(43, 44, 64, 0.97);
                border-color: rgba(105, 108, 255, 0.45);
                border-left: 5px solid #696cff;
                box-shadow: 0 12px 32px rgba(0, 0, 0, 0.5);
                color: #e4e6f0;
            }
            .invoice-floating-toast.toast-hiding {
                opacity: 0;
                transform: translateX(110%);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            @keyframes invoiceToastSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(28px) scale(0.96);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            .invoice-floating-toast .toast-header-custom {
                padding: 12px 16px 8px 16px;
                display: flex;
                align-items: center;
                gap: 10px;
                border-bottom: 1px dashed rgba(0,0,0,0.07);
            }
            html.dark-style .invoice-floating-toast .toast-header-custom {
                border-bottom-color: rgba(255,255,255,0.08);
            }
            .invoice-floating-toast .toast-body-custom {
                padding: 12px 16px;
            }
            .invoice-floating-toast .toast-footer-custom {
                padding: 8px 16px 12px 16px;
                display: flex;
                align-items: center;
                gap: 8px;
                background: rgba(0,0,0,0.015);
                border-top: 1px solid rgba(0,0,0,0.04);
            }
            html.dark-style .invoice-floating-toast .toast-footer-custom {
                background: rgba(255,255,255,0.02);
                border-top-color: rgba(255,255,255,0.06);
            }
            .invoice-toast-pulse {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: #696cff;
                display: inline-block;
                position: relative;
                box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.7);
                animation: toastPulse 1.8s infinite;
            }
            @keyframes toastPulse {
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
            .invoice-toast-amount-box {
                background: rgba(105, 108, 255, 0.08);
                border: 1px solid rgba(105, 108, 255, 0.18);
                border-radius: 8px;
                padding: 8px 12px;
            }
            html.dark-style .invoice-toast-amount-box {
                background: rgba(105, 108, 255, 0.15);
                border-color: rgba(105, 108, 255, 0.3);
            }
        `;
        $('<style id="invoiceFloatingToastStyles">' + css + '</style>').appendTo('head');
    }

    function ensureToastContainer() {
        var $container = $('#invoiceFloatingToastContainer');
        if (!$container.length) {
            $container = $('<div id="invoiceFloatingToastContainer" aria-live="polite" aria-atomic="true"></div>');
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
            osc2.frequency.setValueAtTime(880.00, now + 0.12);
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
            // Autoplay policy or unsupported browser — ignore silently.
        }
    }

    function shakeBell() {
        $icon.removeClass('bell-shake');
        // Force reflow so the animation can restart if it's already mid-run.
        void $icon[0].offsetWidth;
        $icon.addClass('bell-shake');
        setTimeout(function () { $icon.removeClass('bell-shake'); }, 1700);
    }

    function escapeHtml(str) {
        return $('<div>').text(str == null ? '' : str).html();
    }

    function renderItems(items) {
        var html = items.map(function (item) {
            var amount = 'Rp ' + Number(item.amount || 0).toLocaleString('id-ID');
            var isInvoiceRequested = item.type === 'invoice_requested';
            var isInvoiceApproved = item.type === 'invoice_approved';
            var icon = isInvoiceRequested ? 'mdi-file-document-outline' : (isInvoiceApproved ? 'mdi-check-decagram-outline' : 'mdi-cash-multiple');
            var badgeClass = isInvoiceRequested ? 'bg-label-primary' : (isInvoiceApproved ? 'bg-label-info' : 'bg-label-success');
            var message = isInvoiceRequested
                ? 'Invoice senilai ' + amount + ' menunggu diterbitkan (' + escapeHtml(item.company) + ')'
                : isInvoiceApproved
                    ? 'Invoice senilai ' + amount + ' sudah di-acc Accounting (' + escapeHtml(item.company) + ')'
                    : 'Payment ' + amount + ' ditambahkan (' + escapeHtml(item.company) + ')';
            var unread = !item.is_read;
            return (
                '<a href="' + item.url + '" class="payment-notif-item' + (unread ? ' payment-notif-unread' : '') + '"' +
                    ' data-notif-id="' + item.id + '" data-read="' + (unread ? '0' : '1') + '">' +
                    '<li class="list-group-item list-group-item-action dropdown-notifications-item' + (unread ? ' bg-label-secondary' : '') + '">' +
                        '<div class="d-flex gap-2">' +
                            '<div class="flex-shrink-0"><div class="avatar me-1">' +
                                '<span class="avatar-initial rounded-circle ' + badgeClass + '"><i class="mdi ' + icon + '"></i></span>' +
                            '</div></div>' +
                            '<div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">' +
                                '<h6 class="mb-1 text-truncate">' + escapeHtml(item.no_quote) + '</h6>' +
                                '<small class="text-truncate text-body">' + message + '</small>' +
                            '</div>' +
                            '<div class="flex-shrink-0 dropdown-notifications-actions d-flex flex-column align-items-end gap-1">' +
                                '<small class="text-muted">' + escapeHtml(item.created_at) + '</small>' +
                                (unread ? '<span class="badge badge-dot bg-danger"></span>' : '') +
                            '</div>' +
                        '</div>' +
                    '</li>' +
                '</a>'
            );
        }).join('');
        $list.html(html);
    }

    function markRead(id) {
        if (!readUrlTemplate) return;
        $.post(readUrlTemplate.replace('__ID__', id), { _token: window.csrfToken });
    }

    // Dismiss floating toast
    function dismissToast(toastId, notifId) {
        var $toast = $('#' + toastId);
        if ($toast.length) {
            $toast.addClass('toast-hiding');
            setTimeout(function () {
                $toast.remove();
                activeToastIds.delete(notifId);
            }, 320);
        }
        if (notifId) {
            addDismissedToastId(notifId);
        }
    }

    // Tampilkan Popup Toast untuk Invoice Requested (Accounting & Admin) atau Invoice Approved (Sales)
    function checkAndShowFloatingToasts(items) {
        injectToastStyles();
        var $container = ensureToastContainer();
        var dismissed = getDismissedToastIds();
        var hasNewToast = false;

        items.forEach(function (item) {
            if (item.is_read) return;
            if (dismissed.indexOf(item.id) !== -1) return;
            if (activeToastIds.has(item.id)) return;

            var isAccountingAdmin = (userRole === 'Accounting' || userRole === 'Admin');
            var isSales = (userRole === 'Sales');

            var shouldShow = false;
            var toastTitle = '';
            var toastIcon = '';

            if (isAccountingAdmin && item.type === 'invoice_requested') {
                shouldShow = true;
                toastTitle = 'Pengajuan Invoice Baru';
                toastIcon = 'mdi-file-document-alert-outline';
            } else if (isSales && item.type === 'invoice_approved') {
                shouldShow = true;
                toastTitle = 'Invoice Berhasil Di-ACC';
                toastIcon = 'mdi-check-decagram-outline';
            }

            if (!shouldShow) return;

            activeToastIds.add(item.id);
            hasNewToast = true;
            var toastDomId = 'invoiceToast_' + item.id;
            var amountFormatted = 'Rp ' + Number(item.amount || 0).toLocaleString('id-ID');
            var invoiceTypeBadge = item.invoice_type
                ? (item.invoice_type + (item.invoice_percent ? ' ' + item.invoice_percent + '%' : ''))
                : 'Invoice';

            var poButtonHtml = '';
            if (item.po_url) {
                poButtonHtml = '<a href="' + escapeHtml(item.po_url) + '" target="_blank" class="btn btn-xs btn-outline-secondary d-inline-flex align-items-center gap-1">' +
                    '<i class="mdi mdi-file-pdf-box fs-6 text-danger"></i> Lihat PO' +
                '</a>';
            }

            var primaryActionBtn = '';
            if (isAccountingAdmin) {
                primaryActionBtn = '<a href="' + escapeHtml(item.url) + '" class="btn btn-xs btn-primary btn-toast-acc d-inline-flex align-items-center gap-1 flex-grow-1" data-notif-id="' + item.id + '">' +
                    '<i class="mdi mdi-check-circle-outline"></i> ACC & Terbitkan' +
                '</a>';
            } else {
                primaryActionBtn = '<a href="' + escapeHtml(item.url) + '" class="btn btn-xs btn-primary d-inline-flex align-items-center gap-1 flex-grow-1" data-notif-id="' + item.id + '">' +
                    '<i class="mdi mdi-eye-outline"></i> Lihat Invoice' +
                '</a>';
            }

            var toastHtml = $(
                '<div id="' + toastDomId + '" class="invoice-floating-toast" role="alert" aria-live="assertive" aria-atomic="true">' +
                    '<div class="toast-header-custom">' +
                        '<span class="invoice-toast-pulse"></span>' +
                        '<i class="mdi ' + toastIcon + ' fs-5 text-primary"></i>' +
                        '<strong class="me-auto fs-6 fw-bold">' + toastTitle + '</strong>' +
                        '<small class="text-muted">' + escapeHtml(item.created_at) + '</small>' +
                        '<button type="button" class="btn-close ms-2 fs-7 btn-toast-dismiss" data-toast-id="' + toastDomId + '" data-notif-id="' + item.id + '" aria-label="Close"></button>' +
                    '</div>' +
                    '<div class="toast-body-custom py-2">' +
                        '<div class="d-flex align-items-center justify-content-between mb-1">' +
                            '<span class="badge bg-label-primary fw-semibold">' + escapeHtml(item.no_quote) + '</span>' +
                            '<span class="badge bg-label-info">' + escapeHtml(invoiceTypeBadge) + '</span>' +
                        '</div>' +
                        '<div class="fw-bold text-truncate my-1 fs-6" title="' + escapeHtml(item.company) + '">' +
                            '<i class="mdi mdi-office-building-outline text-muted me-1"></i>' + escapeHtml(item.company) +
                        '</div>' +
                        '<div class="small text-muted d-flex flex-wrap gap-2 mb-2">' +
                            (item.po_number ? '<span><i class="mdi mdi-receipt-text-outline text-muted"></i> PO: <b>' + escapeHtml(item.po_number) + '</b></span>' : '') +
                            (item.sales_name ? '<span><i class="mdi mdi-account-tie-outline text-muted"></i> Sales: ' + escapeHtml(item.sales_name) + '</span>' : '') +
                        '</div>' +
                        '<div class="invoice-toast-amount-box d-flex justify-content-between align-items-center">' +
                            '<span class="small fw-semibold text-muted">Nominal Invoice:</span>' +
                            '<span class="fw-bolder text-primary fs-6">' + amountFormatted + '</span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="toast-footer-custom justify-content-between">' +
                        poButtonHtml +
                        primaryActionBtn +
                        '<button type="button" class="btn btn-xs btn-text-secondary btn-toast-dismiss" data-toast-id="' + toastDomId + '" data-notif-id="' + item.id + '">Nanti</button>' +
                    '</div>' +
                '</div>'
            );

            $container.append(toastHtml);
        });

        if (hasNewToast) {
            playBeep();
            shakeBell();
        }
    }

    // Event listener: Klik tombol dismiss (X / Nanti)
    $(document).on('click', '.btn-toast-dismiss', function (e) {
        e.preventDefault();
        var toastId = $(this).data('toast-id');
        var notifId = $(this).data('notif-id');
        dismissToast(toastId, notifId);
    });

    // Event listener: Klik ACC / View dari toast
    $(document).on('click', '.btn-toast-acc', function (e) {
        var notifId = $(this).data('notif-id');
        if (notifId) {
            markRead(notifId);
        }
    });

    // Klik cuma menghilangkan penanda merah (unread) itemnya — item tetap
    // ada di listing, tidak ikut hilang/ke-remove dari daftar.
    $(document).on('click', '.payment-notif-item', function () {
        var $item = $(this);
        if ($item.attr('data-read') === '1') return; // sudah dibaca, tidak perlu request lagi
        markRead($item.data('notif-id'));

        $item.attr('data-read', '1').removeClass('payment-notif-unread');
        $item.find('li').removeClass('bg-label-secondary');
        $item.find('.badge-dot').remove();

        if (lastCount > 0) lastCount -= 1;
        var remaining = $list.find('.payment-notif-unread').length;
        if (remaining > 0) {
            $countBadge.text(remaining + ' Notifikasi');
        } else {
            $dot.addClass('d-none');
            $countBadge.addClass('d-none');
        }
    });

    function poll() {
        $.getJSON(pollUrl).done(function (res) {
            var count = res.count || 0;
            var items = res.items || [];
            renderItems(items);

            if (count > 0) {
                $dot.removeClass('d-none');
                $countBadge.text(count + ' Notifikasi').removeClass('d-none');
            } else {
                $dot.addClass('d-none');
                $countBadge.addClass('d-none');
            }

            if (count > lastCount) {
                shakeBell();
                playBeep();
            }
            lastCount = count;

            // Trigger floating popup toast jika ada pengajuan invoice yang belum di-acc/dibaca
            checkAndShowFloatingToasts(items);
        });
    }

    // Poll right away so a notification created while the page was open elsewhere
    // shows up as soon as this tab gets focus/reload, then keep polling for near-realtime updates.
    poll();
    setInterval(poll, 7000);

    // Poll immediately when the tab regains focus/visibility, instead of waiting
    // up to 7s — covers the common case of switching back from another tab.
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') poll();
    });
});
