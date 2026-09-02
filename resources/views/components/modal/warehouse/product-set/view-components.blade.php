{{-- Modal Detail Komponen & Stok Product Set --}}
<div class="modal fade" id="modalViewComponents" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="mdi mdi-layers-outline fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="mvc_bundle_title">
                            Komponen Penyusun Bundle
                        </h5>
                        <small class="text-muted" id="mvc_bundle_subtitle">Rincian komponen dan ketersediaan stok.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 bg-light rounded-3 mb-3 border">
                    <div>
                        <span class="text-muted small fw-semibold">Stok Bundle Tersedia:</span>
                        <span class="fw-bolder fs-5 ms-1" id="mvc_total_stock_badge">-</span>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold">Total Komponen:</span>
                        <span class="badge bg-label-primary rounded-pill px-2.5 py-1 fw-bold ms-1" id="mvc_comp_count">-</span>
                    </div>
                </div>

                <div class="table-responsive border rounded-3 overflow-hidden">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="text-center py-2.5" style="width: 35px; font-size: 11px;">#</th>
                                <th class="py-2.5" style="font-size: 11px;">Komponen & Merk Kompatibel</th>
                                <th class="text-center py-2.5" style="width: 100px; font-size: 11px;">Office</th>
                                <th class="text-center py-2.5" style="width: 100px; font-size: 11px;">Warehouse</th>
                                <th class="text-center py-2.5" style="width: 120px; font-size: 11px;">Total Stok</th>
                            </tr>
                        </thead>
                        <tbody id="mvc_table_body">
                            {{-- Dynamically populated via JS --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-light border-top py-3 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="mvc_detail_btn" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                    <span>Buka Halaman Detail & HPP</span>
                    <i class="mdi mdi-arrow-right fs-6"></i>
                </a>
            </div>
        </div>
    </div>
</div>
