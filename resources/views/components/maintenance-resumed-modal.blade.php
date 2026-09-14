@php
    $maintServiceDetails = \App\Services\MaintenanceService::getDetails();
    $lastDeactTs = (int) ($maintServiceDetails['last_deactivated_timestamp'] ?? 0);
    $lastDeactAt = $maintServiceDetails['last_deactivated_at'] ?? null;
    $isMaintActive = (bool) ($maintServiceDetails['is_active'] ?? false);
@endphp

@if (!$isMaintActive)
    <!-- Maintenance Resumed / System Updated Notification Modal -->
    <div class="modal fade" id="maintResumedModal" tabindex="-1" aria-labelledby="maintResumedModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <!-- Accent Line -->
                <div style="height: 5px; background: linear-gradient(90deg, #10b981 0%, #06b6d4 50%, #3b82f6 100%); width: 100%;"></div>

                <div class="modal-body p-4 p-md-4 pt-3">
                    <!-- Top Status Badge -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge rounded-pill px-3 py-1 text-uppercase fw-bold" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.75rem; letter-spacing: 0.5px;">
                            <i class="mdi mdi-check-decagram-outline me-1"></i> Pembaruan Sistem Selesai
                        </span>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Icon & Hero Animation -->
                    <div class="text-center mb-3">
                        <div class="maint-success-halo mx-auto mb-3">
                            <div class="maint-success-inner">
                                <i class="mdi mdi-rocket-launch-outline fs-2"></i>
                            </div>
                        </div>

                        <h4 class="modal-title fw-bold text-dark mb-2" id="maintResumedModalLabel" style="letter-spacing: -0.3px;">
                            Terima Kasih Telah Menunggu!
                        </h4>
                        <p class="text-muted small mb-0 px-2" style="line-height: 1.6;">
                            Sistem telah berhasil diperbarui ke versi terbaru dan kini telah kembali normal dengan penambahan beberapa fitur baru serta peningkatan performa aplikasi.
                        </p>
                    </div>

                    <!-- Card 1: Hard Refresh Guide -->
                    <div class="card border-0 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0 !important; border-radius: 14px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <span class="badge rounded-circle p-2 bg-label-primary flex-shrink-0" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="mdi mdi-refresh fs-5"></i>
                                </span>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                        Halaman atau Tombol Belum Berubah / Muncul?
                                    </div>
                                    <div class="text-muted small mt-1" style="font-size: 0.8rem; line-height: 1.5;">
                                        Browser Anda mungkin masih menyimpan cache lama. Silakan lakukan <strong>Hard Refresh</strong>:
                                    </div>
                                </div>
                            </div>

                            <div class="p-2 rounded mt-2 text-center" style="background: rgba(241, 245, 249, 0.9); border: 1px dashed #cbd5e1;">
                                <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 py-1">
                                    <span class="small fw-semibold text-secondary">Windows / Linux:</span>
                                    <kbd class="px-2 py-1 bg-dark text-white rounded small shadow-sm">Ctrl</kbd>
                                    <span class="text-muted fw-bold">+</span>
                                    <kbd class="px-2 py-1 bg-dark text-white rounded small shadow-sm">Shift</kbd>
                                    <span class="text-muted fw-bold">+</span>
                                    <kbd class="px-2 py-1 bg-dark text-white rounded small shadow-sm">R</kbd>
                                    <span class="text-muted small mx-1">atau</span>
                                    <kbd class="px-2 py-1 bg-dark text-white rounded small shadow-sm">Ctrl</kbd>
                                    <span class="text-muted fw-bold">+</span>
                                    <kbd class="px-2 py-1 bg-dark text-white rounded small shadow-sm">F5</kbd>
                                </div>
                                <div class="border-top my-1 pt-1 d-flex align-items-center justify-content-center flex-wrap gap-2">
                                    <span class="small fw-semibold text-secondary">Mac OS:</span>
                                    <kbd class="px-2 py-1 bg-dark text-white rounded small shadow-sm">⌘ Cmd</kbd>
                                    <span class="text-muted fw-bold">+</span>
                                    <kbd class="px-2 py-1 bg-dark text-white rounded small shadow-sm">Shift</kbd>
                                    <span class="text-muted fw-bold">+</span>
                                    <kbd class="px-2 py-1 bg-dark text-white rounded small shadow-sm">R</kbd>
                                </div>
                            </div>

                            <div class="mt-2 text-end">
                                <button type="button" class="btn btn-outline-primary btn-sm py-1 px-3" onclick="window.doHardRefresh(this)" style="font-size: 0.78rem; border-radius: 8px;">
                                    <i class="mdi mdi-autorenew me-1"></i> Hard Refresh Sekarang
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Helpdesk Ticket Guide -->
                    <div class="card border-0 mb-3" style="background: #fffbeb; border: 1px solid #fef3c7 !important; border-radius: 14px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start gap-2">
                                <span class="badge rounded-circle p-2 bg-label-warning flex-shrink-0" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="mdi mdi-lifebuoy fs-5"></i>
                                </span>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark" style="font-size: 0.88rem;">
                                        Masih Mengalami Kendala atau Menemukan Error?
                                    </div>
                                    <div class="text-muted small mt-1" style="font-size: 0.8rem; line-height: 1.5;">
                                        Apabila masih terdapat error setelah refresh, Anda dapat langsung membuat laporan tiket melalui <strong>Helpdesk</strong> agar segera ditindaklanjuti.
                                    </div>
                                    <div class="mt-2">
                                        <a href="{{ route('helpdesk.index') }}" class="btn btn-warning text-dark btn-sm py-1 px-3 fw-semibold shadow-sm" style="font-size: 0.78rem; border-radius: 8px;">
                                            <i class="mdi mdi-ticket-confirmation-outline me-1"></i> Buat Tiket Helpdesk
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="d-grid mt-2">
                        <button type="button" class="btn btn-success py-2 fw-semibold" data-bs-dismiss="modal" style="border-radius: 12px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Saya Mengerti & Lanjutkan Bekerja
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .maint-success-halo {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.22) 0%, rgba(16, 185, 129, 0.05) 70%, transparent 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .maint-success-inner {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.38);
        }
    </style>

    <script>
        (function() {
            const lastDeactTs = @json($lastDeactTs);
            const storageKey = 'maint_resumed_ack_ts';

            window.openMaintenanceResumedModal = function() {
                if (window.bootstrap && document.getElementById('maintResumedModal')) {
                    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('maintResumedModal'));
                    modal.show();
                }
            };

            window.doHardRefresh = function(btn) {
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Merefresh...';
                }
                // Save ack timestamp before reloading
                try {
                    localStorage.setItem(storageKey, String(lastDeactTs || Date.now()));
                    sessionStorage.removeItem('maint_just_resumed');
                } catch (e) {}

                // Force hard reload (cache bypass)
                window.location.reload(true);
            };

            function initResumedModal() {
                const urlParams = new URLSearchParams(window.location.search);
                const hasUrlParam = urlParams.has('maintenance_resumed') || urlParams.has('maint_updated');
                let hasSessionFlag = false;

                try {
                    hasSessionFlag = sessionStorage.getItem('maint_just_resumed') === '1';
                } catch (e) {}

                // Check timestamp acknowledge
                let ackTs = 0;
                try {
                    ackTs = parseInt(localStorage.getItem(storageKey) || '0', 10);
                } catch (e) {}

                const isNewDeactivation = lastDeactTs > 0 && lastDeactTs > ackTs && (Date.now() / 1000 - lastDeactTs) < (48 * 3600);

                if (hasUrlParam || hasSessionFlag || isNewDeactivation) {
                    setTimeout(function() {
                        window.openMaintenanceResumedModal();
                    }, 600);

                    // Clean URL parameter without reloading
                    if (hasUrlParam) {
                        urlParams.delete('maintenance_resumed');
                        urlParams.delete('maint_updated');
                        const newQuery = urlParams.toString();
                        const newUrl = window.location.pathname + (newQuery ? '?' + newQuery : '') + window.location.hash;
                        window.history.replaceState({}, document.title, newUrl);
                    }
                }

                // When modal is hidden, acknowledge so it doesn't pop up again
                const modalEl = document.getElementById('maintResumedModal');
                if (modalEl) {
                    modalEl.addEventListener('hidden.bs.modal', function () {
                        try {
                            localStorage.setItem(storageKey, String(lastDeactTs || Date.now()));
                            sessionStorage.removeItem('maint_just_resumed');
                        } catch (e) {}
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initResumedModal);
            } else {
                initResumedModal();
            }
        })();
    </script>
@endif
