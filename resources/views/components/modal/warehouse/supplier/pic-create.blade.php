<form action="{{ route('supplier.pic.store', $supplier->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="createSupplierPic" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                <i class="mdi mdi-account-plus-outline font-22"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Tambah PIC Supplier</h5>
                            <small class="text-muted font-12">Kontak person untuk {{ $supplier->supplier ?? 'Supplier' }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    @if ($errors->any())
                        <div class="alert alert-danger py-2 px-3 mb-3 font-13">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark font-13" for="picNameCreate">
                            Nama PIC <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="mdi mdi-account-outline"></i>
                            </span>
                            <input type="text" id="picNameCreate" class="form-control border-start-0 ps-1" name="namePic"
                                placeholder="Contoh: Bpk. Hendra / Ibu Sarah" value="{{ old('namePic') }}" required autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark font-13" for="picPhoneCreate">
                            No. Telepon / WhatsApp
                        </label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="mdi mdi-phone-outline"></i>
                            </span>
                            <input type="text" id="picPhoneCreate" class="form-control border-start-0 ps-1" name="phonePic"
                                placeholder="08xxxxxxxxxx" value="{{ old('phonePic') }}" autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label fw-semibold text-dark font-13" for="picEmailCreate">
                            Email PIC
                        </label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-light border-end-0 text-muted">
                                <i class="mdi mdi-email-outline"></i>
                            </span>
                            <input type="email" id="picEmailCreate" class="form-control border-start-0 ps-1" name="emailPic"
                                placeholder="nama@supplier.com" value="{{ old('emailPic') }}" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-label-secondary px-3" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan PIC
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
