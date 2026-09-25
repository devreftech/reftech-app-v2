<form action="{{ route('product.replacement.update', $detail->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <div class="modal fade" id="editReplacement-{{ $detail->id }}" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                {{-- Modal Header --}}
                <div class="modal-header bg-light py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                <i class="mdi mdi-pencil-outline fs-4"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                Edit Replacement: <span class="text-primary">{{ $detail->replacement }}</span>
                            </h5>
                            <small class="text-muted" style="font-size: 12px;">
                                Ubah kode SKU, harga modal (admin), dan konfigurasi opname
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-alert-circle-outline fs-5"></i>
                                <strong>Terjadi kesalahan input:</strong>
                            </div>
                            <ul class="mb-0 mt-2 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card border border-light-subtle shadow-none bg-body-tertiary rounded-3 p-3 mb-3">
                        <div class="row g-3">
                            <div class="col-md-{{ Auth::user()->role == 'Admin' ? '6' : '12' }}">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="replacement-{{ $detail->id }}" class="form-control bg-white"
                                        name="replacement" placeholder="Kode SKU" value="{{ old('replacement', $detail->replacement) }}" required>
                                    <label for="replacement-{{ $detail->id }}">Replacement (Kode SKU) <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            @if (Auth::user()->role == 'Admin')
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline input-group" data-price="1">
                                        <span class="input-group-text bg-light text-muted fw-semibold">Rp</span>
                                        <input type="text" class="form-control bg-white invoice-item-modal-label"
                                            id="modal-label-{{ $detail->id }}" data-id="{{ $detail->id }}" min="0"
                                            placeholder="Harga Modal" data-type="currency"
                                            pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                            value="{{ old('modal', @$detail->modal ? number_format($detail->modal, 0, ',', '.') : '') }}">
                                        <input class="form-control invoice-item-modal" type="number" name="modal"
                                            id="modal-{{ $detail->id }}"
                                            value="{{ old('modal', @$detail->modal ?? '') }}" hidden>
                                        <label for="modal-label-{{ $detail->id }}">Harga Modal (HPP)</label>
                                    </div>
                                </div>
                            @endif

                            <div class="col-12 mt-3">
                                <div class="form-check form-switch p-2 bg-white rounded-2 border d-flex align-items-center justify-content-between">
                                    <label class="form-check-label fw-semibold text-dark mb-0 ps-2" for="is_opname_edit_{{ $detail->id }}">
                                        <i class="mdi mdi-clipboard-check-outline text-success me-1"></i> Sertakan di Stock Opname
                                    </label>
                                    <input class="form-check-input ms-0" type="checkbox" name="is_opname" value="1"
                                        id="is_opname_edit_{{ $detail->id }}" {{ $detail->is_opname ? 'checked' : '' }}>
                                </div>
                                <div class="form-text text-muted mt-1" style="font-size: 11px;">
                                    <i class="mdi mdi-information-outline me-1"></i>Jika dinonaktifkan, part SKU ini tidak akan muncul pada daftar penghitungan fisik Stock Opname.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-light py-3 px-4 border-top d-flex justify-content-between align-items-center">
                    <small class="text-muted"><span class="text-danger">*</span> Wajib diisi</small>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary waves-effect rounded-pill px-3" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light rounded-pill px-4 shadow-sm">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
