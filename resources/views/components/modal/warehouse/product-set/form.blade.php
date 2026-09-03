<div class="modal fade" id="createProduct" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('product-set.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                @if (@$product)
                    @method('patch')
                @endif

                <div class="modal-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-package-variant-plus fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="createProductTitle">
                                Tambah Product Set Baru
                            </h5>
                            <small class="text-muted">Buat paket bundle produk baru untuk manajemen stok terpadu.</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger py-2 px-3 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="commodity" class="form-control" name="commodity"
                                    placeholder="Contoh: SET-FILTER-01" value="{{ old('commodity', @$product->commodity ?? '') }}" required>
                                <label for="commodity">Commodity / SKU Name *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label text-dark fw-semibold mb-1" for="category_select">
                                    Kategori Product Set *
                                </label>
                                <select id="category_select" class="select2-category form-select" name="category" required>
                                    @php
                                        $allCats = $categories ?? ['Bearing Kit', 'Non Bearing Kit', 'Seal Kit', 'Gasket Kit', 'Valve Kit', 'Filter Kit', 'Overhaul Kit'];
                                    @endphp
                                    @foreach ($allCats as $cat)
                                        <option value="{{ $cat }}" {{ (old('category', @$product->category) == $cat || $cat === 'Non Bearing Kit') ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    <i class="mdi mdi-plus-circle-outline text-primary me-0.5"></i>
                                    Pilih opsi atau <strong>ketik nama kategori baru</strong> lalu tekan <code>Enter</code>.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="detail_desc" class="form-control" name="detail_desc"
                                    placeholder="Deskripsi singkat"
                                    value="{{ old('detail_desc', @$product->detail_desc ?? '') }}">
                                <label for="detail_desc">Short Description</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="unit" class="form-control" name="unit"
                                    placeholder="Set"
                                    value="{{ old('unit', @$product->unit ?? 'Set') }}">
                                <label for="unit">Satuan (Unit)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <textarea id="description" class="form-control h-px-100" name="description" placeholder="Deskripsi lengkap mengenai paket bundle ini..."
                                    cols="30" rows="4">{{ old('description', @$product->description ?? '') }}</textarea>
                                <label for="description">Deskripsi Lengkap (Description)</label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-0 d-flex align-items-center gap-2" style="font-size: 12.5px;">
                        <i class="mdi mdi-information-outline fs-5 text-info flex-shrink-0"></i>
                        <span>Setelah bundle dibuat, Anda dapat langsung menambahkan komponen-komponen (*replacement*) penyusunnya pada halaman detail bundle.</span>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-3 px-4">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                        <i class="mdi mdi-check"></i>
                        <span>Simpan Product Set</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
