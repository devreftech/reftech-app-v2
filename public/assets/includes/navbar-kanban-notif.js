/**
 * Real-Time Notification & Audio Alert for Kanban Task Mentions
 * Reftech ERP System
 */
$(function () {
    var pollUrl = window.kanbanNotifUnreadUrl;
    if (!pollUrl) return;

    var audioCtx = null;
    var knownMentionIds = new Set();
    var isFirstLoad = true;
    var STORAGE_DISMISSED_KEY = 'dismissed_kanban_mention_toast_ids';

    // Unlock Web Audio Context on user interaction
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
                sessionStorage.setItem(STORAGE_DISMISSED_KEY, JSON.stringify(dismissed));
            }
        } catch (e) {}
    }

    // Play crisp modern 3-tone chime for Kanban Mention
    function playChime() {
        try {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) return;
            if (!audioCtx) audioCtx = new Ctx();

            var triggerTone = function () {
                var now = audioCtx.currentTime;

                // Tone 1: E5 (659.25 Hz)
                var osc1 = audioCtx.createOscillator();
                var gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, now);
                gain1.gain.setValueAtTime(0, now);
                gain1.gain.linearRampToValueAtTime(0.28, now + 0.02);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.36);

                // Tone 2: A5 (880.00 Hz)
                var osc2 = audioCtx.createOscillator();
                var gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880.00, now + 0.10);
                gain2.gain.setValueAtTime(0, now + 0.10);
                gain2.gain.linearRampToValueAtTime(0.32, now + 0.12);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.50);
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.start(now + 0.10);
                osc2.stop(now + 0.51);

                // Tone 3: C#6 (1108.73 Hz) - Bright resolution
                var osc3 = audioCtx.createOscillator();
                var gain3 = audioCtx.createGain();
                osc3.type = 'sine';
                osc3.frequency.setValueAtTime(1108.73, now + 0.22);
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
            console.warn('Kanban audio chime error:', e);
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
        if ($('#kanbanFloatingToastStyles').length) return;
        var css = `
            #kanbanFloatingToastContainer {
                position: fixed;
                top: 80px;
                right: 24px;
                z-index: 999995;
                display: flex;
                flex-direction: column;
                gap: 12px;
                max-width: 420px;
                width: calc(100vw - 36px);
                pointer-events: none;
            }
            .kanban-floating-toast {
                pointer-events: auto;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                border: 1px solid rgba(105, 108, 255, 0.35);
                border-left: 5px solid #696cff;
                border-radius: 14px;
                box-shadow: 0 14px 36px rgba(34, 48, 62, 0.24), 0 3px 8px rgba(0,0,0,0.08);
                overflow: hidden;
                transform: translateY(0);
                opacity: 1;
                transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
                animation: kanbanToastSlideIn 0.38s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            html.dark-style .kanban-floating-toast {
                background: rgba(43, 44, 64, 0.97);
                border-color: rgba(105, 108, 255, 0.45);
                border-left: 5px solid #696cff;
                box-shadow: 0 14px 36px rgba(0, 0, 0, 0.55);
                color: #e4e6f0;
            }
            .kanban-floating-toast.toast-hiding {
                opacity: 0;
                transform: translateX(110%);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            @keyframes kanbanToastSlideIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px) scale(0.95);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
            .kanban-floating-toast .toast-header-custom {
                padding: 10px 14px 8px 14px;
                display: flex;
                align-items: center;
                gap: 8px;
                border-bottom: 1px dashed rgba(0,0,0,0.07);
            }
            html.dark-style .kanban-floating-toast .toast-header-custom {
                border-bottom-color: rgba(255,255,255,0.08);
            }
            .kanban-floating-toast .toast-body-custom {
                padding: 12px 14px 14px 14px;
            }
            .kanban-toast-comment-preview {
                font-size: 0.8125rem;
                line-height: 1.45;
                color: #566a7f;
                background: rgba(105, 108, 255, 0.05);
                border-radius: 8px;
                padding: 8px 10px;
                margin-top: 6px;
                border-left: 3px solid rgba(105, 108, 255, 0.4);
            }
            html.dark-style .kanban-toast-comment-preview {
                color: #b4b7c9;
                background: rgba(105, 108, 255, 0.12);
            }
        `;
        $('head').append('<style id="kanbanFloatingToastStyles">' + css + '</style>');
    }

    function getOrCreateToastContainer() {
        var $container = $('#kanbanFloatingToastContainer');
        if (!$container.length) {
            $container = $('<div id="kanbanFloatingToastContainer"></div>');
            $('body').append($container);
        }
        return $container;
    }

    function showToast(mention) {
        var dismissed = getDismissedToastIds();
        if (dismissed.indexOf(Number(mention.comment_id)) !== -1) {
            return;
        }

        injectToastStyles();
        var $container = getOrCreateToastContainer();
        var toastId = 'kanban-toast-' + mention.comment_id;

        if ($('#' + toastId).length) return;

        var avatarUrl = mention.author_photo || '/assets/img/avatars/1.png';
        var commentText = $('<div>').html(mention.comment || '').text();
        if (commentText.length > 90) commentText = commentText.substring(0, 90) + '...';

        var toastHtml = `
            <div id="${toastId}" class="kanban-floating-toast" data-comment-id="${mention.comment_id}">
                <div class="toast-header-custom">
                    <span class="badge bg-label-primary px-2 py-1 rounded-pill" style="font-size: 11px;">
                        <i class="mdi mdi-at me-1"></i>Mention di Kanban
                    </span>
                    <span class="badge bg-label-secondary px-2 py-1 rounded-pill text-truncate" style="max-width: 140px; font-size: 10.5px;">
                        ${$('<div>').text(mention.board_name || 'Board').html()}
                    </span>
                    <button type="button" class="btn-close ms-auto btn-close-kanban-toast" aria-label="Close" style="font-size: 10px;"></button>
                </div>
                <div class="toast-body-custom">
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <img src="${avatarUrl}" alt="${$('<div>').text(mention.author_name).html()}" class="rounded-circle flex-shrink-0" style="width: 32px; height: 32px; object-fit: cover;" onerror="this.src='/assets/img/avatars/1.png'">
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 13px;">
                                ${$('<div>').text(mention.author_name).html()}
                                <span class="fw-normal text-muted" style="font-size: 11.5px;">menyebut Anda di:</span>
                            </div>
                            <div class="fw-semibold text-primary text-truncate" style="font-size: 12.5px;">
                                ${$('<div>').text(mention.task_title).html()}
                            </div>
                        </div>
                    </div>
                    <div class="kanban-toast-comment-preview">
                        "${$('<div>').text(commentText).html()}"
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <span class="text-muted" style="font-size: 11px;">
                            <i class="mdi mdi-clock-outline me-1"></i>Baru saja
                        </span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 btn-dismiss-kanban-toast" style="font-size: 11px;">
                                Tutup
                            </button>
                            <a href="${mention.go_url}" class="btn btn-xs btn-primary rounded-pill px-3 btn-open-kanban-task" style="font-size: 11px; font-weight: 600;" data-task-id="${mention.task_id}" data-board-id="${mention.board_id}" data-comment-id="${mention.comment_id}">
                                <i class="mdi mdi-open-in-new me-1"></i>Buka Task
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        var $toast = $(toastHtml);
        $container.append($toast);

        // Auto dismiss after 12s
        var timer = setTimeout(function () {
            dismissToast($toast, mention.comment_id);
        }, 12000);

        $toast.find('.btn-close-kanban-toast, .btn-dismiss-kanban-toast').on('click', function (e) {
            e.preventDefault();
            clearTimeout(timer);
            dismissToast($toast, mention.comment_id);
        });

        // Fast in-page modal open if user is currently on the same Kanban Board
        $toast.find('.btn-open-kanban-task').on('click', function (e) {
            var currentPath = window.location.pathname;
            var isSameBoard = currentPath.includes('/kanban/boards/' + mention.board_id);

            if (isSameBoard && typeof window.openTaskSidebar === 'function') {
                e.preventDefault();
                clearTimeout(timer);
                dismissToast($toast, mention.comment_id);

                // Mark as read via AJAX
                $.post('/notifications/kanban/' + mention.comment_id + '/read', {
                    _token: window.csrfToken || $('meta[name="csrf-token"]').attr('content')
                });

                window.openTaskSidebar(mention.task_id);
            }
        });
    }

    function dismissToast($toast, commentId) {
        addDismissedToastId(commentId);
        $toast.addClass('toast-hiding');
        setTimeout(function () {
            $toast.remove();
        }, 320);
    }

    // Polling function
    function pollKanbanMentions() {
        $.ajax({
            url: pollUrl,
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (!res || !res.success) return;

                var mentions = res.mentions || [];
                var unreadCount = res.unread_count || mentions.length;

                // Sync UI Badges
                var $bellDot = $('#navbarBellDot');
                var $kanbanBadge = $('#kanbanMentionCountBadge');
                var $totalBadge = $('#notifTotalBadge');

                if (unreadCount > 0) {
                    if ($bellDot.length) $bellDot.removeClass('d-none');
                    if ($kanbanBadge.length) {
                        $kanbanBadge.text(unreadCount + ' Mention Kanban').removeClass('d-none');
                    }
                }

                var newMentions = [];
                mentions.forEach(function (m) {
                    var cId = Number(m.comment_id);
                    if (!knownMentionIds.has(cId)) {
                        knownMentionIds.add(cId);
                        if (!isFirstLoad) {
                            newMentions.push(m);
                        }
                    }
                });

                // If new mentions arrived after initial load:
                if (!isFirstLoad && newMentions.length > 0) {
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
    pollKanbanMentions();

    // Regular interval: 10 seconds
    setInterval(pollKanbanMentions, 10000);
});
