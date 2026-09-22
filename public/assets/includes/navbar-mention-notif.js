/**
 * Real-Time Floating Toast Notification & Audio Alert for Mentions
 * (Smart Quotation, Prospect, and Purchase Request)
 * Reftech ERP System
 */
$(function () {
    var pollUrl = window.mentionNotifUnreadUrl;
    if (!pollUrl) return;

    var audioCtx = null;
    var knownMentionIds = new Set();
    var isFirstLoad = true;
    var STORAGE_DISMISSED_KEY = 'dismissed_module_mention_toast_ids';

    // Unlock Web Audio Context on first user interaction
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
    ['click', 'keydown', 'touchstart', 'pointerdown'].forEach(function (evt) {
        document.addEventListener(evt, unlockAudioContext, { passive: true, once: false });
    });

    function getDismissedToastIds() {
        try {
            var raw = sessionStorage.getItem(STORAGE_DISMISSED_KEY) || '[]';
            return JSON.parse(raw);
        } catch (e) {
            return [];
        }
    }

    function addDismissedToastId(id) {
        try {
            if (!id) return;
            var strId = String(id);
            var dismissed = getDismissedToastIds();
            if (dismissed.indexOf(strId) === -1) {
                dismissed.push(strId);
                sessionStorage.setItem(STORAGE_DISMISSED_KEY, JSON.stringify(dismissed));
            }
        } catch (e) {}
    }

    // Play elegant modern 3-tone harmonic chime for Mentions
    function playChime() {
        try {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            if (!audioCtx) audioCtx = new Ctx();

            var triggerTone = function () {
                var now = audioCtx.currentTime;

                // Tone 1: F#5 (739.99 Hz)
                var osc1 = audioCtx.createOscillator();
                var gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(739.99, now);
                gain1.gain.setValueAtTime(0, now);
                gain1.gain.linearRampToValueAtTime(0.28, now + 0.02);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.36);

                // Tone 2: A#5 (932.33 Hz)
                var osc2 = audioCtx.createOscillator();
                var gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(932.33, now + 0.10);
                gain2.gain.setValueAtTime(0, now + 0.10);
                gain2.gain.linearRampToValueAtTime(0.32, now + 0.12);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.50);
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.start(now + 0.10);
                osc2.stop(now + 0.51);

                // Tone 3: D#6 (1244.51 Hz) - Radiant resolution
                var osc3 = audioCtx.createOscillator();
                var gain3 = audioCtx.createGain();
                osc3.type = 'sine';
                osc3.frequency.setValueAtTime(1244.51, now + 0.22);
                gain3.gain.setValueAtTime(0, now + 0.22);
                gain3.gain.linearRampToValueAtTime(0.38, now + 0.24);
                gain3.gain.exponentialRampToValueAtTime(0.001, now + 0.85);
                osc3.connect(gain3);
                gain3.connect(audioCtx.destination);
                osc3.start(now + 0.22);
                osc3.stop(now + 0.86);
            };

            if (audioCtx.state === 'suspended') {
                audioCtx.resume().then(triggerTone).catch(function () {});
            } else {
                triggerTone();
            }
        } catch (e) {
            console.warn('Mention audio chime error:', e);
        }
    }

    function shakeBell() {
        var $icon = $('#navbarBellIcon');
        if (!$icon.length) return;
        $icon.removeClass('bell-shake');
        void $icon[0].offsetWidth; // reflow
        $icon.addClass('bell-shake');
        setTimeout(function () { $icon.removeClass('bell-shake'); }, 1700);
    }

    function injectToastStyles() {
        if ($('#mentionFloatingToastStyles').length) return;
        var css = `
            #mentionFloatingToastContainer {
                position: fixed;
                bottom: 95px;
                right: 24px;
                z-index: 999995;
                display: flex;
                flex-direction: column-reverse;
                gap: 12px;
                max-width: 420px;
                width: calc(100vw - 36px);
                pointer-events: none;
            }
            .mention-floating-toast {
                pointer-events: auto;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                border-radius: 14px;
                box-shadow: 0 14px 36px rgba(34, 48, 62, 0.24), 0 3px 8px rgba(0,0,0,0.08);
                overflow: hidden;
                transform: translateY(0);
                opacity: 1;
                transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
                animation: mentionToastSlideUp 0.38s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            .mention-floating-toast.toast-sq {
                border: 1px solid rgba(105, 108, 255, 0.35);
                border-left: 5px solid #696cff;
            }
            .mention-floating-toast.toast-prospect {
                border: 1px solid rgba(255, 171, 0, 0.4);
                border-left: 5px solid #ffab00;
            }
            .mention-floating-toast.toast-pr {
                border: 1px solid rgba(3, 195, 236, 0.4);
                border-left: 5px solid #03c3ec;
            }
            .mention-floating-toast.toast-kanban {
                border: 1px solid rgba(113, 221, 55, 0.4);
                border-left: 5px solid #71dd37;
            }
            html.dark-style .mention-floating-toast {
                background: rgba(43, 44, 64, 0.98);
                box-shadow: 0 14px 36px rgba(0, 0, 0, 0.55);
                color: #e4e6f0;
            }
            html.dark-style .mention-floating-toast.toast-sq {
                border-color: rgba(105, 108, 255, 0.5);
                border-left: 5px solid #696cff;
            }
            html.dark-style .mention-floating-toast.toast-prospect {
                border-color: rgba(255, 171, 0, 0.5);
                border-left: 5px solid #ffab00;
            }
            html.dark-style .mention-floating-toast.toast-pr {
                border-color: rgba(3, 195, 236, 0.5);
                border-left: 5px solid #03c3ec;
            }
            html.dark-style .mention-floating-toast.toast-kanban {
                border-color: rgba(113, 221, 55, 0.5);
                border-left: 5px solid #71dd37;
            }
            .mention-floating-toast.toast-hiding {
                opacity: 0;
                transform: translateX(110%);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            @keyframes mentionToastSlideUp {
                from {
                    opacity: 0;
                    transform: translateY(28px) scale(0.96);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            .mention-floating-toast .toast-header-custom {
                padding: 10px 14px 8px 14px;
                display: flex;
                align-items: center;
                gap: 8px;
                border-bottom: 1px dashed rgba(0,0,0,0.07);
            }
            html.dark-style .mention-floating-toast .toast-header-custom {
                border-bottom-color: rgba(255,255,255,0.08);
            }
            .mention-floating-toast .toast-body-custom {
                padding: 12px 14px 14px 14px;
            }
            .mention-toast-comment-preview {
                font-size: 0.8125rem;
                line-height: 1.45;
                color: #566a7f;
                border-radius: 8px;
                padding: 8px 10px;
                margin-top: 6px;
                font-style: italic;
            }
            .toast-sq .mention-toast-comment-preview {
                background: rgba(105, 108, 255, 0.06);
                border-left: 3px solid rgba(105, 108, 255, 0.5);
            }
            .toast-prospect .mention-toast-comment-preview {
                background: rgba(255, 171, 0, 0.08);
                border-left: 3px solid rgba(255, 171, 0, 0.6);
            }
            .toast-pr .mention-toast-comment-preview {
                background: rgba(3, 195, 236, 0.08);
                border-left: 3px solid rgba(3, 195, 236, 0.6);
            }
            .toast-kanban .mention-toast-comment-preview {
                background: rgba(113, 221, 55, 0.08);
                border-left: 3px solid rgba(113, 221, 55, 0.6);
            }
            html.dark-style .toast-sq .mention-toast-comment-preview {
                color: #b4b7c9;
                background: rgba(105, 108, 255, 0.15);
            }
            html.dark-style .toast-prospect .mention-toast-comment-preview {
                color: #dcd9a8;
                background: rgba(255, 171, 0, 0.15);
            }
            html.dark-style .toast-pr .mention-toast-comment-preview {
                color: #a8dce8;
                background: rgba(3, 195, 236, 0.15);
            }
            html.dark-style .toast-kanban .mention-toast-comment-preview {
                color: #b7e89e;
                background: rgba(113, 221, 55, 0.15);
            }
        `;
        $('head').append('<style id="mentionFloatingToastStyles">' + css + '</style>');
    }

    function getOrCreateToastContainer() {
        var $container = $('#mentionFloatingToastContainer');
        if (!$container.length) {
            $container = $('<div id="mentionFloatingToastContainer"></div>');
            $('body').append($container);
        }
        return $container;
    }

    function showToast(mention) {
        var dismissed = getDismissedToastIds();
        if (dismissed.indexOf(String(mention.id)) !== -1) {
            return;
        }

        injectToastStyles();
        var $container = getOrCreateToastContainer();
        var domToastId = 'mention-toast-' + mention.id;

        if ($('#' + domToastId).length) return;

        var avatarUrl = mention.author_avatar || '/assets/img/avatars/1.png';
        var commentText = $('<div>').html(mention.comment || '').text();
        if (commentText.length > 100) commentText = commentText.substring(0, 100) + '...';

        var badgeClass = 'bg-label-primary';
        var toastTypeClass = 'toast-sq';
        var iconClass = 'mdi-file-document-edit-outline';
        var actionLabel = 'Buka Quotation';

        if (mention.type === 'prospect') {
            badgeClass = 'bg-label-warning';
            toastTypeClass = 'toast-prospect';
            iconClass = 'mdi-account-search-outline';
            actionLabel = 'Buka Prospect';
        } else if (mention.type === 'purchase_request') {
            badgeClass = 'bg-label-info';
            toastTypeClass = 'toast-pr';
            iconClass = 'mdi-cart-arrow-down';
            actionLabel = 'Buka PR';
        } else if (mention.type === 'kanban') {
            badgeClass = 'bg-label-success';
            toastTypeClass = 'toast-kanban';
            iconClass = 'mdi-view-dashboard-outline';
            actionLabel = 'Buka Kanban';
        }

        var toastHtml = `
            <div id="${domToastId}" class="mention-floating-toast ${toastTypeClass}" data-mention-id="${mention.id}">
                <div class="toast-header-custom">
                    <span class="badge ${badgeClass} px-2 py-1 rounded-pill" style="font-size: 11px;">
                        <i class="mdi ${iconClass} me-1"></i>Mention di ${mention.module_name}
                    </span>
                    <span class="badge bg-label-secondary px-2 py-1 rounded-pill text-truncate ms-1" style="max-width: 140px; font-size: 10px;">
                        ${$('<div>').text(mention.target_title || '').html()}
                    </span>
                    <button type="button" class="btn-close ms-auto btn-close-mention-toast" aria-label="Close" style="font-size: 10px;"></button>
                </div>
                <div class="toast-body-custom">
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <img src="${avatarUrl}" alt="${$('<div>').text(mention.author_name).html()}" class="rounded-circle flex-shrink-0" style="width: 34px; height: 34px; object-fit: cover;" onerror="this.src='/assets/img/avatars/1.png'">
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 13px;">
                                ${$('<div>').text(mention.author_name).html()}
                                <span class="fw-normal text-muted" style="font-size: 11.5px;">menyebut Anda:</span>
                            </div>
                            <div class="fw-semibold text-primary text-truncate" style="font-size: 12.5px;">
                                ${$('<div>').text(mention.target_title).html()}
                            </div>
                        </div>
                    </div>
                    <div class="mention-toast-comment-preview">
                        "${$('<div>').text(commentText).html()}"
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <span class="text-muted" style="font-size: 11px;">
                            <i class="mdi mdi-clock-outline me-1"></i>${mention.created_at || 'Baru saja'}
                        </span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 btn-dismiss-mention-toast" style="font-size: 11px;">
                                Tutup
                            </button>
                            <a href="${mention.go_url}" class="btn btn-xs btn-primary rounded-pill px-3 btn-open-mention-target" style="font-size: 11px; font-weight: 600;">
                                <i class="mdi mdi-open-in-new me-1"></i>${actionLabel}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        var $toast = $(toastHtml);
        $container.append($toast);

        // Persistent notification: stays on screen until dismissed or clicked
        $toast.find('.btn-close-mention-toast, .btn-dismiss-mention-toast').on('click', function (e) {
            e.preventDefault();
            dismissToast($toast, mention.id);
        });

        $toast.find('.btn-open-mention-target').on('click', function (e) {
            addDismissedToastId(mention.id);
        });
    }

    function dismissToast($toast, mentionId) {
        addDismissedToastId(mentionId);
        $toast.addClass('toast-hiding');
        setTimeout(function () {
            $toast.remove();
        }, 320);
    }

    // Polling function
    function pollMentions() {
        $.ajax({
            url: pollUrl,
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (!res || !res.success) return;

                var mentions = res.mentions || [];
                var unreadCount = res.unread_count || mentions.length;

                // Sync Navbar Bell Indicator
                var $bellDot = $('#navbarBellDot');
                if (unreadCount > 0 && $bellDot.length) {
                    $bellDot.removeClass('d-none');
                }

                var newMentions = [];
                mentions.forEach(function (m) {
                    var mId = String(m.id);
                    if (!knownMentionIds.has(mId)) {
                        knownMentionIds.add(mId);
                        newMentions.push(m);
                    }
                });

                // Display toasts:
                // 1. On first load: display any existing unread mentions (persistent until clicked/dismissed)
                // 2. On subsequent polls: if new mentions arrived, play chime, shake bell, and show toast
                if (isFirstLoad) {
                    mentions.forEach(function (m) {
                        showToast(m);
                    });
                } else if (newMentions.length > 0) {
                    playChime();
                    shakeBell();
                    newMentions.forEach(function (m) {
                        showToast(m);
                    });
                }

                isFirstLoad = false;
            },
            error: function () {
                // Silently ignore network interruptions
            }
        });
    }

    // Initial check
    pollMentions();

    // Regular interval: 10 seconds
    setInterval(pollMentions, 10000);
});
