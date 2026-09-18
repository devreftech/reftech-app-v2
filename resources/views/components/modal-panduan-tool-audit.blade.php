{{-- MODAL PANDUAN & KETENTUAN AUDIT TOOLS --}}
<div class="modal fade" id="modalPanduanAudit" tabindex="-1" aria-labelledby="modalPanduanAuditLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-label-primary border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-book-open-page-variant-outline fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-primary mb-0" id="modalPanduanAuditLabel">
                            Panduan &amp; Ketentuan Self-Audit Tools
                        </h6>
                        <small class="text-muted font-11">Pedoman teknisi dalam pemeriksaan fisik mandiri &amp; pelaporan kondisi aset</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-3 p-md-4">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                            <i class="mdi mdi-clipboard-list-outline text-primary me-2"></i>Alur Proses Self-Audit Tools
                        </h6>
                        
                        <div class="timeline ps-3" style="border-left: 2px solid #e7e7e8;">
                            <div class="timeline-item pb-3 position-relative ps-3">
                                <span class="badge bg-primary rounded-circle position-absolute top-0 start-0 translate-middle-x" style="width: 22px; height: 22px; padding: 0; line-height: 22px; text-align: center;">1</span>
                                <h6 class="fw-bold text-dark mb-1 font-13">Pemberitahuan Periode Audit</h6>
                                <p class="small text-muted mb-0">
                                    Admin membuka periode audit (atau dibuka otomatis tiap akhir kuartal). Notifikasi muncul di banner atas, lonceng notifikasi, dan halaman audit Anda.
                                </p>
                            </div>

                            <div class="timeline-item pb-3 position-relative ps-3">
                                <span class="badge bg-primary rounded-circle position-absolute top-0 start-0 translate-middle-x" style="width: 22px; height: 22px; padding: 0; line-height: 22px; text-align: center;">2</span>
                                <h6 class="fw-bold text-dark mb-1 font-13">Pemeriksaan Fisik Mandiri</h6>
                                <p class="small text-muted mb-0">
                                    Periksa seluruh fisik alat kerja Anda satu per satu. Pilih kondisi fisik terkini secara akurat (<strong>Ada</strong>, <strong>Rusak</strong>, atau <strong>Hilang</strong>).
                                </p>
                            </div>

                            <div class="timeline-item pb-3 position-relative ps-3">
                                <span class="badge bg-primary rounded-circle position-absolute top-0 start-0 translate-middle-x" style="width: 22px; height: 22px; padding: 0; line-height: 22px; text-align: center;">3</span>
                                <h6 class="fw-bold text-dark mb-1 font-13">Unggah Foto Kondisi Fisik</h6>
                                <p class="small text-muted mb-0">
                                    Ambil foto fisik terbaru dari masing-masing alat (otomatis tersimpan ke server). Jika alat dalam kondisi rusak atau hilang, sertakan penjelasan detail kerusakannya.
                                </p>
                            </div>

                            <div class="timeline-item pb-3 position-relative ps-3">
                                <span class="badge bg-primary rounded-circle position-absolute top-0 start-0 translate-middle-x" style="width: 22px; height: 22px; padding: 0; line-height: 22px; text-align: center;">4</span>
                                <h6 class="fw-bold text-dark mb-1 font-13">Submit Audit Tools</h6>
                                <p class="small text-muted mb-0">
                                    Setelah semua alat telah diperiksa dan fotonya lengkap, klik tombol <strong>Submit Audit Tools</strong> untuk mengirim laporan akhir ke Admin Warehouse.
                                </p>
                            </div>

                            <div class="timeline-item position-relative ps-3">
                                <span class="badge bg-success rounded-circle position-absolute top-0 start-0 translate-middle-x" style="width: 22px; height: 22px; padding: 0; line-height: 22px; text-align: center;">5</span>
                                <h6 class="fw-bold text-dark mb-1 font-13">Verifikasi Admin Warehouse</h6>
                                <p class="small text-muted mb-0">
                                    Admin meninjau laporan Anda. Jika disetujui, status menjadi <strong>Terverifikasi</strong>. Jika ada foto/keterangan yang belum jelas, status menjadi <strong>Revisi</strong> dan dapat Anda perbaiki.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card bg-label-info border-0 shadow-none mb-3">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-info mb-2">
                                    <i class="mdi mdi-lightbulb-on-outline me-1"></i>Ketentuan &amp; Tips Audit
                                </h6>
                                <ul class="small text-heading ps-3 mb-0" style="font-size: 12.5px;">
                                    <li class="mb-2"><strong>Waktu Pelaporan:</strong> Self-audit dibuka selama rentang jadwal audit yang tertera pada kartu periode.</li>
                                    <li class="mb-2"><strong>Kejelasan Foto:</strong> Pastikan foto alat diambil dengan pencahayaan cukup dan menunjukkan kondisi fisik aktual.</li>
                                    <li class="mb-2"><strong>Alat Rusak / Hilang:</strong> Wajib menyertakan penjelasan kronologi atau bagian alat yang mengalami kendala.</li>
                                    <li><strong>Disiplin Aset:</strong> Menjaga dan merawat peralatan kerja merupakan bentuk profesionalisme dan tanggung jawab teknisi.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card bg-label-warning border-0 shadow-none">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="mdi mdi-cloud-check text-warning fs-4"></i>
                                    <h6 class="fw-bold text-warning mb-0">Fitur Auto-Save Aktif</h6>
                                </div>
                                <p class="small text-heading mb-0" style="font-size: 12px;">
                                    Setiap pilihan kondisi dan foto yang diunggah langsung tersimpan secara <em>real-time</em> di server. Anda dapat melanjutkan pengisian kapan saja sebelum melakukan submit.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top py-2 px-3 justify-content-between">
                <span class="small text-muted font-11">
                    <i class="mdi mdi-shield-check-outline text-success me-1"></i> Self-Audit Tools Reftech App
                </span>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4" data-bs-dismiss="modal">
                    Saya Mengerti
                </button>
            </div>
        </div>
    </div>
</div>
