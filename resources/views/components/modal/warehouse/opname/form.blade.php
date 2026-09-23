<form action="{{ route('opname.store') }}" method="post">
    @csrf
    <div class="modal fade" id="createOpname" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-label-primary py-3">
                    <h5 class="modal-title d-flex align-items-center mb-0 text-primary fw-bold">
                        <i class="mdi mdi-clipboard-plus-outline me-2 fs-4"></i> Buat Sesi Stock Opname
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="periode" name="periode" required>
                                    <option value="" disabled selected>Pilih Quarter</option>
                                    <option value="1" {{ ceil(date('n') / 3) == 1 ? 'selected' : '' }}>Quarter I (Jan - Mar)</option>
                                    <option value="2" {{ ceil(date('n') / 3) == 2 ? 'selected' : '' }}>Quarter II (Apr - Jun)</option>
                                    <option value="3" {{ ceil(date('n') / 3) == 3 ? 'selected' : '' }}>Quarter III (Jul - Sep)</option>
                                    <option value="4" {{ ceil(date('n') / 3) == 4 ? 'selected' : '' }}>Quarter IV (Okt - Des)</option>
                                </select>
                                <label for="periode">Periode Caturwulan <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="number" class="form-control" id="year" name="year" value="{{ date('Y') }}" min="2020" max="2030" required>
                                <label for="year">Tahun Periode <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                                <label for="date">Tanggal Pelaksanaan Audit <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control" name="note" id="note" placeholder="Tuliskan catatan bila ada..." style="height: 90px;"></textarea>
                                <label for="note">Catatan / Keterangan Sesi (Opsional)</label>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="mdi mdi-information-outline me-1"></i> Setelah sesi dibuat, Anda dapat langsung menambahkan rincian stok fisik per item barang.
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Buat &amp; Mulai Opname
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
