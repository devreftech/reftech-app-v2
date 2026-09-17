<form action="{{ route('supplier.address.store', $supplier->id) }}" method="post">
    @csrf
    <div class="modal fade" id="createSupplierAddress" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                <i class="mdi mdi-map-marker-plus-outline font-22"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Tambah Alamat Supplier</h5>
                            <small class="text-muted font-12">Alamat operasional/gudang untuk {{ $supplier->supplier ?? 'Supplier' }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark font-13" for="addressNameCreate">
                            Label / Nama Alamat
                        </label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="mdi mdi-tag-outline"></i>
                            </span>
                            <input type="text" id="addressNameCreate" class="form-control border-start-0 ps-1" name="name"
                                placeholder="Contoh: Kantor Pusat / Gudang Bintaro / Pabrik Cikarang" value="{{ old('name') }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark font-13" for="addressTextCreate">
                            Alamat Lengkap <span class="text-danger">*</span>
                        </label>
                        <textarea id="addressTextCreate" class="form-control" name="address" rows="3"
                            placeholder="Jl. Raya No. 123, Kelurahan, Kecamatan, Kota, Kode Pos" required>{{ old('address') }}</textarea>
                    </div>

                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="isPrimaryCreate" {{ empty($addresses) || $addresses->isEmpty() ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark font-13" for="isPrimaryCreate">
                            Jadikan sebagai Alamat Utama
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-label-secondary px-3" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Alamat
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
