<div class="modal fade" id="detailHelpdesk" tabindex="-1" aria-hidden="true" data-id="">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-lighter py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="mdi mdi-ticket-confirmation-outline mdi-18px"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span>Detail Tiket:</span>
                            <span class="font-monospace text-primary" id="detailHelpdeskNoTicket"></span>
                        </h5>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="detailHelpdeskStatus"></span>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-4">
                {{-- Meta Information Card --}}
                <div class="card border rounded-3 shadow-none bg-light mb-3">
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-4">
                                <span class="text-muted small d-block mb-1"><i class="mdi mdi-account-outline me-1"></i>Pelapor / User</span>
                                <div class="fw-semibold text-dark" id="detailHelpdeskRequester">-</div>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <span class="text-muted small d-block mb-1"><i class="mdi mdi-calendar-clock-outline me-1"></i>Waktu Laporan</span>
                                <div class="fw-semibold text-dark" id="detailHelpdeskDate">-</div>
                            </div>
                            <div class="col-12 col-md-4" id="detailHelpdeskUrlWrapper">
                                <span class="text-muted small d-block mb-1"><i class="mdi mdi-link-variant me-1"></i>Halaman Terkait</span>
                                <div class="text-truncate">
                                    <a href="#" id="detailHelpdeskUrl" target="_blank" class="fw-semibold text-primary text-decoration-underline d-inline-flex align-items-center gap-1">
                                        <span id="detailHelpdeskUrlText" class="text-truncate" style="max-width: 180px;"></span>
                                        <i class="mdi mdi-open-in-new fs-6"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ticket Title --}}
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-1">Judul Kendala</label>
                    <div class="p-2 px-3 border rounded-2 bg-white fw-bold text-dark fs-6" id="detailHelpdeskTitle">-</div>
                </div>

                {{-- Ticket Description --}}
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase mb-1">Rincian Deskripsi / Log Error</label>
                    <div class="p-3 border rounded-3 bg-white" style="max-height: 280px; overflow-y: auto;">
                        <pre class="mb-0 text-dark" id="detailHelpdeskDescription"
                            style="font-size: 13.5px; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; white-space: pre-wrap; word-break: break-word; line-height: 1.5;"></pre>
                    </div>
                </div>

                {{-- Resolution Note (if Resolved) --}}
                <div class="d-none mb-0" id="detailHelpdeskResolutionWrapper">
                    <div class="card border border-success bg-label-success shadow-none mb-0">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-success mb-2 d-flex align-items-center gap-1">
                                <i class="mdi mdi-check-circle-outline fs-5"></i>
                                <span>Keterangan Penyelesaian (Resolution Note)</span>
                            </h6>
                            <pre class="mb-0 text-dark" id="detailHelpdeskResolutionNote"
                                style="font-size: 13.5px; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; white-space: pre-wrap; word-break: break-word; line-height: 1.5;"></pre>
                        </div>
                    </div>
                </div>
            </div>

            @if (Auth::user()->role == 'Admin')
                <div class="modal-footer bg-lighter py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Tutup</button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-warning d-flex align-items-center gap-1 waves-effect waves-light button-helpdesk-status" data-status="In Progress">
                            <i class="mdi mdi-progress-wrench me-1"></i>
                            <span>Proses Tiket</span>
                        </button>
                        <button type="button" class="btn btn-success d-flex align-items-center gap-1 waves-effect waves-light button-helpdesk-status" data-status="Resolved">
                            <i class="mdi mdi-check-decagram-outline me-1"></i>
                            <span>Selesaikan Tiket</span>
                        </button>
                    </div>
                </div>
            @else
                <div class="modal-footer bg-lighter py-3 px-4">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Tutup</button>
                </div>
            @endif
        </div>
    </div>
</div>
