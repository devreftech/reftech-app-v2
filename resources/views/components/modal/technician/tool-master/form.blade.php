<form action="{{ route('tool-master.store') }}" method="post" enctype="multipart/form-data" id="formToolMaster">
    @csrf
    <input type="hidden" name="_method" id="toolMasterMethod" value="post">
    <div class="modal animate__animated animate__fadeIn" id="toolMasterModal" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="toolMasterModalLabel">Create New Master Tools</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-2 mb-3">
                        <div class="col-md-8 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="nama_tools" class="form-control" name="nama_tools"
                                    placeholder="Contoh: Obeng Plus (+) 6 inch">
                                <label for="nama_tools">Nama Tools</label>
                            </div>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="kategori" name="kategori">
                                    <option value="" selected>-- Pilih Kategori --</option>
                                    <option value="Hand Tools">Hand Tools</option>
                                    <option value="Measuring Tools">Measuring Tools</option>
                                    <option value="Electrical Instruments">Electrical Instruments</option>
                                    <option value="Diagnostic Instruments">Diagnostic Instruments</option>
                                    <option value="Pressure Instruments">Pressure Instruments</option>
                                    <option value="Refrigeration Tools">Refrigeration Tools</option>
                                    <option value="Torque Tools">Torque Tools</option>
                                    <option value="Lifting Tools">Lifting Tools</option>
                                    <option value="Safety Equipment">Safety Equipment</option>
                                    <option value="Workshop Equipment">Workshop Equipment</option>
                                    <option value="Lubrication Tools">Lubrication Tools</option>
                                    <option value="Cleaning Tools">Cleaning Tools</option>
                                    <option value="Overhaul Tools">Overhaul Tools</option>
                                </select>
                                <label for="kategori">Kategori</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="spesifikasi" class="form-control" name="spesifikasi"
                                    placeholder="Brand/spesifikasi rekomendasi">
                                <label for="spesifikasi">Spesifikasi</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="link_pembelian" class="form-control" name="link_pembelian"
                                    placeholder="https://...">
                                <label for="link_pembelian">Link Pembelian (Referensi)</label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="number" id="harga_referensi" class="form-control" name="harga_referensi"
                                    placeholder="0">
                                <label for="harga_referensi">Harga Referensi</label>
                            </div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="status_aktif" name="status_aktif">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                                <label for="status_aktif">Status</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-8 mb-2">
                            <label class="form-label" for="foto_referensi">Foto Referensi</label>
                            <input type="file" id="foto_referensi" class="form-control" name="foto_referensi"
                                accept="image/*">
                            <small class="text-muted">Foto contoh produk yang benar, buat acuan beli.</small>
                        </div>
                        <div class="col-md-4 mb-2" id="fotoReferensiPreviewWrapper" style="display: none;">
                            <label class="form-label">Foto Saat Ini</label>
                            <div>
                                <img id="fotoReferensiPreview" src="" alt="Foto Referensi"
                                    style="max-width: 100%; max-height: 100px; border-radius: 6px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
