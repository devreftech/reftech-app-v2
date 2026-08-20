$(function () {
    var $dot = $('#navbarBellDot');
    var $icon = $('#navbarBellIcon');
    var $countBadge = $('#paymentNotifCountBadge');
    var $list = $('#paymentNotifList');
    var pollUrl = window.paymentNotifUnreadUrl;
    var readUrlTemplate = window.paymentNotifReadUrlTemplate; // contains "__ID__" placeholder

    if (!pollUrl || !$list.length) return;

    var lastCount = $list.find('.payment-notif-unread').length;
    var audioCtx = null;

    function playBeep() {
        try {
            if (!audioCtx) {
                var Ctx = window.AudioContext || window.webkitAudioContext;
                audioCtx = new Ctx();
            }
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }
            var now = audioCtx.currentTime;
            [880, 1174.66].forEach(function (freq, i) {
                var osc = audioCtx.createOscillator();
                var gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                gain.gain.setValueAtTime(0.0001, now + i * 0.15);
                gain.gain.linearRampToValueAtTime(0.15, now + i * 0.15 + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + i * 0.15 + 0.18);
                osc.connect(gain).connect(audioCtx.destination);
                osc.start(now + i * 0.15);
                osc.stop(now + i * 0.15 + 0.2);
            });
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
            renderItems(res.items || []);

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
