<form action="{{ @$product ? route('product.update', @$product->id) : route('product.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    @if (@$product)
        @method('patch')
    @endif

    <div class="modal fade" id="{{ @$product ? 'updateProduct-' . @$product->id : 'createProduct' }}" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                {{-- Modal Header --}}
                <div class="modal-header bg-light py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-circle {{ @$product ? 'bg-label-primary' : 'bg-label-success' }}">
                                <i class="mdi {{ @$product ? 'mdi-pencil-outline' : 'mdi-plus-box-outline' }} fs-4"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                @if (@$product)
                                    Update Product: <span class="text-primary">{{ @$product->commodity }}</span>
                                @else
                                    Tambah Produk Baru
                                @endif
                            </h5>
                            <small class="text-muted" style="font-size: 12px;">
                                {{ @$product ? 'Kelola rincian komoditas, spesifikasi, dan kebijakan pengadaan' : 'Lengkapi formulir untuk menambahkan master produk baru' }}
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-4">
                    @if (isset($errors) && $errors->any())
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

                    <input type="hidden" name="type" value="{{ old('type', @$product->type ?? 'Sparepart') }}">

                    {{-- Section 1: Informasi Utama & Spesifikasi --}}
                    <div class="card border border-light-subtle shadow-none bg-body-tertiary rounded-3 mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="mdi mdi-package-variant-closed text-primary fs-5"></i>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Informasi Utama Produk</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="commodity_{{ @$product->id ?? 'new' }}" class="form-control bg-white"
                                            name="commodity" placeholder="Contoh: W 123456"
                                            value="{{ old('commodity', @$product->commodity ?? '') }}" required>
                                        <label for="commodity_{{ @$product->id ?? 'new' }}">Commodity / Part Number <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="dimension_{{ @$product->id ?? 'new' }}" class="form-control bg-white"
                                            name="dimension" placeholder="Dimensi (cth: 10 x 20 x 30)"
                                            value="{{ old('dimension', @$product->dimension ?? '') }}" required>
                                        <label for="dimension_{{ @$product->id ?? 'new' }}">Dimension <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Kategori, Satuan & Pengadaan --}}
                    <div class="card border border-light-subtle shadow-none bg-body-tertiary rounded-3 mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="mdi mdi-tune-vertical-variant text-info fs-5"></i>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Kategori & Kebijakan Stok</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4 col-6">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select bg-white" id="category_{{ @$product->id ?? 'new' }}" name="category">
                                            <option disabled {{ !@$product->category ? 'selected' : '' }}>Pilih Kategori</option>
                                            <option value="Consumable Part" {{ old('category', @$product->category) == 'Consumable Part' ? 'selected' : '' }}>Consumable Part</option>
                                            <option value="Non Consumable Part" {{ old('category', @$product->category) == 'Non Consumable Part' ? 'selected' : '' }}>Non Consumable Part</option>
                                            <option value="Unit" {{ old('category', @$product->category) == 'Unit' ? 'selected' : '' }}>Unit</option>
                                            <option value="Jasa" {{ old('category', @$product->category) == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                                        </select>
                                        <label for="category_{{ @$product->id ?? 'new' }}">Kategori</label>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select bg-white" id="go_{{ @$product->id ?? 'new' }}" name="go">
                                            <option disabled {{ !@$product->go ? 'selected' : '' }}>Pilih Tipe</option>
                                            <option value="Genuine" {{ old('go', @$product->go) == 'Genuine' ? 'selected' : '' }}>Genuine</option>
                                            <option value="Replacement" {{ old('go', @$product->go) == 'Replacement' ? 'selected' : '' }}>Replacement</option>
                                        </select>
                                        <label for="go_{{ @$product->id ?? 'new' }}">Genuine / Replacement</label>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select bg-white" id="procurement_type_{{ @$product->id ?? 'new' }}" name="procurement_type">
                                            <option value="ready_stock" {{ old('procurement_type', @$product->procurement_type ?? 'ready_stock') == 'ready_stock' ? 'selected' : '' }}>
                                                Ready Stock (Wajib Pantau FSN)
                                            </option>
                                            <option value="by_order" {{ old('procurement_type', @$product->procurement_type) == 'by_order' ? 'selected' : '' }}>
                                                By Order / Indent (Sesuai PO)
                                            </option>
                                        </select>
                                        <label for="procurement_type_{{ @$product->id ?? 'new' }}">Tipe Pengadaan</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select bg-white" id="unit_{{ @$product->id ?? 'new' }}" name="unit">
                                            <option disabled {{ !@$product->unit ? 'selected' : '' }}>Pilih Satuan</option>
                                            <option value="Pcs" {{ old('unit', @$product->unit ?? 'Pcs') == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                            <option value="Set" {{ old('unit', @$product->unit) == 'Set' ? 'selected' : '' }}>Set</option>
                                            <option value="Pail" {{ old('unit', @$product->unit) == 'Pail' ? 'selected' : '' }}>Pail</option>
                                            <option value="Drum" {{ old('unit', @$product->unit) == 'Drum' ? 'selected' : '' }}>Drum</option>
                                            <option value="Unit" {{ old('unit', @$product->unit) == 'Unit' ? 'selected' : '' }}>Unit</option>
                                            <option value="Lot" {{ old('unit', @$product->unit) == 'Lot' ? 'selected' : '' }}>Lot</option>
                                        </select>
                                        <label for="unit_{{ @$product->id ?? 'new' }}">Satuan (Unit)</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-6">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" step="any" id="weight_{{ @$product->id ?? 'new' }}" class="form-control bg-white"
                                            name="weight" placeholder="0" value="{{ old('weight', @$product->weight ?? '') }}">
                                        <label for="weight_{{ @$product->id ?? 'new' }}">Berat (Gram / gr)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Deskripsi & Catatan --}}
                    <div class="card border border-light-subtle shadow-none bg-body-tertiary rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="mdi mdi-text-box-outline text-warning fs-5"></i>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">Deskripsi & Catatan</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="detail_desc_{{ @$product->id ?? 'new' }}" class="form-control bg-white"
                                            name="detail_desc" placeholder="Ringkasan singkat produk"
                                            value="{{ old('detail_desc', @$product->detail_desc ?? '') }}">
                                        <label for="detail_desc_{{ @$product->id ?? 'new' }}">Short Description</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating form-floating-outline">
                                        <textarea id="description_{{ @$product->id ?? 'new' }}" class="form-control bg-white" name="description"
                                            placeholder="Deskripsi spesifikasi lengkap" style="height: 90px;" required>{{ old('description', @$product->description ?? '') }}</textarea>
                                        <label for="description_{{ @$product->id ?? 'new' }}">Description Lengkap <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating form-floating-outline">
                                        <textarea id="note_{{ @$product->id ?? 'new' }}" class="form-control bg-white" name="note"
                                            placeholder="Catatan internal..." style="height: 70px;">{{ old('note', @$product->note ?? '') }}</textarea>
                                        <label for="note_{{ @$product->id ?? 'new' }}">Catatan (Note)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-light py-3 px-4 border-top d-flex justify-content-between align-items-center">
                    <div class="text-muted d-none d-sm-flex align-items-center gap-1" style="font-size: 12px;">
                        <span class="text-danger fw-bold">*</span>
                        <span>Kolom wajib diisi sebelum menyimpan.</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary waves-effect rounded-pill px-3" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light rounded-pill px-4 shadow-sm">
                            <i class="mdi mdi-check-circle-outline me-1"></i>
                            {{ @$product ? 'Simpan Perubahan' : 'Buat Produk' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
