<div class="modal fade" id="formHelpdesk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-lighter py-3 px-4">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="mdi mdi-ticket-plus-outline text-primary fs-4"></i>
                        <span>Buat Tiket Bantuan / Laporan Bug</span>
                    </h5>
                    <small class="text-muted">Tim developer akan segera meninjau dan menindaklanjuti kendala Anda.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('helpdesk.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="helpdeskTitle" class="form-control fw-semibold" name="title"
                                    placeholder="Contoh: Tombol print invoice tidak merespon saat diklik" required>
                                <label for="helpdeskTitle"><i class="mdi mdi-format-title me-1 text-muted"></i>Judul Singkat Kendala <span class="text-danger">*</span></label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="helpdeskUrl" class="form-control font-monospace" name="url_accessed"
                                    placeholder="Contoh: http://127.0.0.1:8000/suo/8 atau /suo/8">
                                <label for="helpdeskUrl"><i class="mdi mdi-link-variant me-1 text-muted"></i>Link / URL Halaman Terkait (Opsional)</label>
                            </div>
                            <div class="d-flex align-items-center gap-1 text-muted small mt-1 ms-1">
                                <i class="mdi mdi-information-outline fs-7"></i>
                                <span>Copy-paste alamat URL halaman browser tempat Anda mengalami error/kendala.</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control" name="description" id="helpdeskDescription" style="height: 140px"
                                    placeholder="Jelaskan langkah-langkah yang dilakukan dan pesan error yang muncul..." required></textarea>
                                <label for="helpdeskDescription"><i class="mdi mdi-text-box-outline me-1 text-muted"></i>Penjelasan Detail Kendala <span class="text-danger">*</span></label>
                            </div>
                            <div class="d-flex align-items-center gap-1 text-muted small mt-1 ms-1">
                                <i class="mdi mdi-lightbulb-outline fs-7 text-warning"></i>
                                <span>Tips: Sertakan kronologi kejadian atau pesan error agar perbaikan lebih cepat.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-lighter py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 waves-effect waves-light shadow-sm">
                        <i class="mdi mdi-send-outline me-1"></i>
                        <span>Kirim Tiket</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
