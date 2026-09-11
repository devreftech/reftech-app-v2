/**
 * SUO (Sales Urgent Order) Real-time Urgent Alert System
 * Menampilkan alert darurat (modal pop-up + alarm suara) saat ada order mendesak:
 * 1. Role Logistic: Saat sales baru mengajukan SUO (status: 'submitted').
 * 2. Role Accounting: Saat gudang telah konfirmasi ketersediaan stok (status: 'confirmed'),
 *    hanya mentrigger accounting yang bertugas menangani sales yang bersangkutan.
 */
$(function () {
    var checkUrl = window.suoUrgentCheckUrl;
    if (!checkUrl) return;

    var audioCtx = null;
    var isModalOpen = false;

    // Inisialisasi & unlock Web Audio Context
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

    // Bunyikan nada alarm darurat (two-tone elegant urgent chime)
    function playUrgentAlarm() {
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
                    gain.gain.linearRampToValueAtTime(0.35, start + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.001, start + duration);
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.start(start);
                    osc.stop(start + duration + 0.02);
                }

                // Siklus 1: Dua nada harmonis
                playBeep(880, now, 0.16);         // A5
                playBeep(1174.66, now + 0.18, 0.22); // D6

                // Siklus 2: Pengulangan setelah jeda singkat
                playBeep(880, now + 0.48, 0.16);
                playBeep(1174.66, now + 0.66, 0.28);
            }

            if (audioCtx.state === 'suspended') {
                audioCtx.resume().then(runTones).catch(function () {});
            } else {
                runTones();
            }
        } catch (e) {
            console.warn('[SuoUrgentAlert] Audio alarm failed:', e);
        }
    }

    function isDismissed(suoId, stage) {
        try {
            var key = 'dismissed_suo_urgent_' + suoId + '_' + stage;
            return sessionStorage.getItem(key) === '1';
        } catch (e) {
            return false;
        }
    }

    function setDismissed(suoId, stage) {
        try {
            var key = 'dismissed_suo_urgent_' + suoId + '_' + stage;
            sessionStorage.setItem(key, '1');
        } catch (e) {}
    }

    // Suntikkan CSS Alert Modal Darurat jika belum ada
    function injectStyles() {
        if ($('#suoUrgentAlertStyles').length) return;
        var css = `
            @keyframes suoPingDot {
                0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 84, 85, 0.7); }
                70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(234, 84, 85, 0); }
                100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(234, 84, 85, 0); }
            }
            @keyframes suoShimmer {
                0% { background-position: -200% 0; }
                100% { background-position: 200% 0; }
            }
            #suoUrgentModal .modal-dialog {
                max-width: 560px;
            }
            #suoUrgentModal .modal-content {
                border-radius: 18px !important;
                border: 1px solid rgba(234, 84, 85, 0.25) !important;
                box-shadow: 0 24px 48px -12px rgba(234, 84, 85, 0.22), 0 4px 16px rgba(0, 0, 0, 0.06) !important;
                overflow: hidden;
                background: #ffffff;
            }
            #suoUrgentModal .suo-top-stripe {
                height: 4px;
                background: linear-gradient(90deg, #ea5455, #ff6b6b, #ea5455);
                background-size: 200% 100%;
                animation: suoShimmer 3s ease-in-out infinite;
            }
            #suoUrgentModal .suo-badge-urgent {
                background: #ffebe8;
                color: #ea5455;
                font-weight: 700;
                font-size: 0.76rem;
                letter-spacing: 0.5px;
                padding: 4px 10px;
                border-radius: 20px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                border: 1px solid rgba(234, 84, 85, 0.2);
            }
            #suoUrgentModal .suo-pulse-dot {
                width: 8px;
                height: 8px;
                background-color: #ea5455;
                border-radius: 50%;
                display: inline-block;
                animation: suoPingDot 1.4s infinite;
            }
            #suoUrgentModal .suo-info-card {
                background: #f8f9fc;
                border: 1px solid #edf0f5;
                border-radius: 12px;
                padding: 14px 16px;
            }
            #suoUrgentModal .suo-info-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 7px 0;
                border-bottom: 1px dashed #e4e7ed;
            }
            #suoUrgentModal .suo-info-row:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }
            #suoUrgentModal .suo-info-label {
                font-size: 0.815rem;
                color: #6c757d;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            #suoUrgentModal .suo-info-val {
                font-size: 0.865rem;
                font-weight: 600;
                color: #2b303a;
                text-align: right;
                max-width: 65%;
            }
            #suoUrgentModal .suo-item-chip {
                background: #ffffff;
                border: 1px solid #e2e6ea;
                border-radius: 8px;
                padding: 6px 10px;
                font-size: 0.8rem;
                color: #333;
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 5px;
            }
            #suoUrgentModal .suo-item-chip:last-child {
                margin-bottom: 0;
            }
            #suoUrgentModal .suo-item-qty {
                background: #eef2f7;
                color: #495057;
                font-weight: 700;
                font-size: 0.74rem;
                padding: 2px 7px;
                border-radius: 6px;
                margin-left: 8px;
                white-space: nowrap;
            }
            #suoUrgentModal .suo-notice-bar {
                background: #fff8eb;
                border: 1px solid #ffe1a6;
                border-radius: 10px;
                padding: 10px 12px;
                font-size: 0.81rem;
                color: #8c5303;
                display: flex;
                align-items: flex-start;
                gap: 8px;
            }
            #suoUrgentModal .btn-action-urgent {
                background: linear-gradient(135deg, #ea5455, #e03233);
                border: none;
                color: #ffffff;
                font-weight: 600;
                font-size: 0.88rem;
                padding: 9px 18px;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(234, 84, 85, 0.35);
                transition: all 0.2s ease;
            }
            #suoUrgentModal .btn-action-urgent:hover {
                background: linear-gradient(135deg, #df4445, #d02425);
                color: #ffffff;
                box-shadow: 0 6px 16px rgba(234, 84, 85, 0.45);
                transform: translateY(-1px);
            }
            #suoUrgentModal .btn-dismiss-urgent {
                background: #f1f3f6;
                color: #5c6370;
                border: none;
                font-weight: 500;
                font-size: 0.88rem;
                padding: 9px 16px;
                border-radius: 10px;
                transition: all 0.15s ease;
            }
            #suoUrgentModal .btn-dismiss-urgent:hover {
                background: #e4e7ed;
                color: #2b303a;
            }
        `;
        $('<style id="suoUrgentAlertStyles">' + css + '</style>').appendTo('head');
    }

    function showUrgentModal(suo) {
        injectStyles();

        // Hapus modal lama jika ada
        $('#suoUrgentModal').remove();

        var avatarHtml = '';
        if (suo.sales_image) {
            avatarHtml = '<img src="' + suo.sales_image + '" class="rounded-circle me-1" style="width:24px;height:24px;object-fit:cover;" alt="' + suo.sales_name + '">';
        } else {
            avatarHtml = '<span class="avatar-initial rounded-circle bg-label-primary me-1" style="width:24px;height:24px;font-size:11px;display:inline-flex;align-items:center;justify-content:center;font-weight:bold;">' + (suo.sales_name ? suo.sales_name.charAt(0) : 'S') + '</span>';
        }

        // Render daftar item terstruktur jika tersedia
        var itemsListHtml = '';
        if (suo.items_list && suo.items_list.length > 0) {
            var itemsToShow = suo.items_list.slice(0, 3);
            itemsListHtml = '<div class="suo-items-container">';
            itemsToShow.forEach(function (item) {
                itemsListHtml += `
                    <div class="suo-item-chip">
                        <span class="text-truncate fw-medium" title="${item.item_name}">${item.item_name}</span>
                        <span class="suo-item-qty">${item.qty} ${item.unit}</span>
                    </div>
                `;
            });
            if (suo.items_list.length > 3) {
                itemsListHtml += `<div class="text-muted text-center pt-1" style="font-size: 0.76rem;">+${suo.items_list.length - 3} item lainnya (lihat di formulir SUO)</div>`;
            }
            itemsListHtml += '</div>';
        } else {
            itemsListHtml = `<div class="fw-semibold text-dark small text-end">${suo.items}</div>`;
        }

        var modalHtml = `
            <div class="modal fade" id="suoUrgentModal" tabindex="-1" aria-labelledby="suoUrgentModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <!-- Top Accent Line -->
                        <div class="suo-top-stripe"></div>

                        <!-- Modal Header -->
                        <div class="p-4 pb-2 d-flex align-items-start justify-content-between border-0">
                            <div class="d-flex align-items-center gap-2">
                                <div class="suo-badge-urgent">
                                    <span class="suo-pulse-dot"></span>
                                    <span>URGENT ORDER</span>
                                </div>
                                <span class="badge bg-label-secondary text-dark rounded-pill fw-semibold" style="font-size: 0.74rem;">
                                    ${suo.stage_badge}
                                </span>
                            </div>
                            <span class="text-muted" style="font-size: 0.78rem;">
                                <i class="mdi mdi-clock-outline me-1"></i>${suo.created_at}
                            </span>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body px-4 pt-1 pb-3">
                            <div class="mb-3">
                                <h5 class="fw-bold text-dark mb-1" id="suoUrgentModalLabel">${suo.stage_title}</h5>
                                <p class="text-muted small mb-0" style="line-height: 1.45;">${suo.stage_desc}</p>
                            </div>

                            <!-- Structured Order Card -->
                            <div class="suo-info-card mb-3">
                                <div class="suo-info-row">
                                    <span class="suo-info-label">
                                        <i class="mdi mdi-file-document-outline text-primary fs-6"></i> No. SUO
                                    </span>
                                    <span class="suo-info-val text-primary font-monospace fw-bold">${suo.no_suo}</span>
                                </div>
                                <div class="suo-info-row">
                                    <span class="suo-info-label">
                                        <i class="mdi mdi-domain text-secondary fs-6"></i> Perusahaan / Customer
                                    </span>
                                    <span class="suo-info-val text-truncate" title="${suo.company}">${suo.company}</span>
                                </div>
                                <div class="suo-info-row">
                                    <span class="suo-info-label">
                                        <i class="mdi mdi-account-outline text-secondary fs-6"></i> PIC
                                    </span>
                                    <span class="suo-info-val text-muted">${suo.pic || '-'}</span>
                                </div>
                                <div class="suo-info-row">
                                    <span class="suo-info-label">
                                        <i class="mdi mdi-account-tie-outline text-info fs-6"></i> Sales Pemohon
                                    </span>
                                    <span class="suo-info-val d-inline-flex align-items-center justify-content-end">
                                        ${avatarHtml}
                                        <span class="text-truncate">${suo.sales_name}</span>
                                    </span>
                                </div>
                                <div class="pt-2">
                                    <div class="suo-info-label mb-2">
                                        <i class="mdi mdi-cube-outline text-warning fs-6"></i> Daftar Barang
                                    </div>
                                    ${itemsListHtml}
                                </div>
                            </div>

                            <!-- Notice Alert -->
                            <div class="suo-notice-bar">
                                <i class="mdi mdi-information-outline fs-5 flex-shrink-0" style="color: #f59e0b; margin-top: -1px;"></i>
                                <div>Mohon segera ditindaklanjuti agar pesanan mendesak ini dapat diproses dan dikirim tanpa hambatan.</div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="p-3 px-4 bg-light border-top d-flex align-items-center justify-content-between">
                            <button type="button" class="btn-dismiss-urgent btn-suo-dismiss" data-suo-id="${suo.id}" data-stage="${suo.stage}">
                                Nanti Dulu
                            </button>
                            <a href="${suo.action_url}" class="btn-action-urgent d-inline-flex align-items-center gap-1 btn-suo-action" data-suo-id="${suo.id}" data-stage="${suo.stage}">
                                <span>${suo.action_label}</span>
                                <i class="mdi mdi-arrow-right fs-5"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modalHtml);

        var modalEl = document.getElementById('suoUrgentModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var bsModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
            bsModal.show();
            isModalOpen = true;

            playUrgentAlarm();
        }
    }

    // Klik tombol action -> simpan dismissal agar saat halaman dibuka modal tidak muncul lagi seketika
    $(document).on('click', '.btn-suo-action', function () {
        var suoId = $(this).data('suo-id');
        var stage = $(this).data('stage');
        setDismissed(suoId, stage);
        isModalOpen = false;
    });

    // Klik tombol dismiss (Nanti Dulu)
    $(document).on('click', '.btn-suo-dismiss', function () {
        var suoId = $(this).data('suo-id');
        var stage = $(this).data('stage');
        setDismissed(suoId, stage);

        var modalEl = document.getElementById('suoUrgentModal');
        if (modalEl) {
            var bsModal = bootstrap.Modal.getInstance(modalEl);
            if (bsModal) bsModal.hide();
            setTimeout(function () { $('#suoUrgentModal').remove(); }, 350);
        }
        isModalOpen = false;
    });

    function pollUrgent() {
        if (isModalOpen) return;

        $.getJSON(checkUrl).done(function (res) {
            if (res && res.has_urgent && res.suo) {
                if (!isDismissed(res.suo.id, res.suo.stage)) {
                    showUrgentModal(res.suo);
                }
            }
        }).fail(function (xhr, status, err) {
            console.warn('[SuoUrgentAlert] Check failed:', status, err);
        });
    }

    // Mulai polling segera, lalu tiap 5 detik
    pollUrgent();
    setInterval(pollUrgent, 5000);

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            pollUrgent();
        }
    });
});
