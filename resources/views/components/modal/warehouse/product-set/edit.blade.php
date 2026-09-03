<div class="modal fade" id="editProductSet" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('product-set.update', $productSet->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-pencil-outline text-primary fs-4"></i>
                        Edit Product Set
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="edit_commodity" class="form-control" name="commodity"
                                    placeholder="Nama Commodity" value="{{ old('commodity', $product->commodity) }}" required>
                                <label for="edit_commodity">Commodity / SKU Name *</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="form-label text-dark fw-semibold mb-1" for="edit_category_select">
                                    Kategori Product Set *
                                </label>
                                <select id="edit_category_select" class="select2-category form-select" name="category" required>
                                    @php
                                        $currentCat = $product->category && $product->category !== '-' ? $product->category : 'Non Bearing Kit';
                                        $allCats = $categories ?? ['Bearing Kit', 'Non Bearing Kit', 'Seal Kit', 'Gasket Kit', 'Valve Kit', 'Filter Kit', 'Overhaul Kit'];
                                        if (!in_array($currentCat, $allCats)) {
                                            $allCats[] = $currentCat;
                                        }
                                    @endphp
                                    @foreach ($allCats as $cat)
                                        <option value="{{ $cat }}" {{ $currentCat == $cat ? 'selected' : '' }}>
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
                                <input type="text" id="edit_detail_desc" class="form-control" name="detail_desc"
                                    placeholder="Deskripsi singkat"
                                    value="{{ old('detail_desc', $product->detail_desc) }}">
                                <label for="edit_detail_desc">Short Description</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="edit_unit" class="form-control" name="unit"
                                    placeholder="Set"
                                    value="{{ old('unit', $product->unit ?? 'Set') }}">
                                <label for="edit_unit">Satuan (Unit)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <textarea id="edit_description" class="form-control h-px-100" name="description" placeholder="Deskripsi lengkap..."
                                    cols="30" rows="5">{{ old('description', $product->description) }}</textarea>
                                <label for="edit_description">Description</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="mdi mdi-check"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
