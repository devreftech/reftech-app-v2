<div class="modal fade" id="createItemReplacement" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <form action="{{ route('product-set.store_item', $productSet->id) }}" method="post">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-plus-box-outline text-primary fs-4"></i>
                        Tambah Komponen Bundle
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Pilih komponen / replacement item yang merupakan bagian penyusun dari <strong>{{ $product->commodity }}</strong>.
                    </p>

                    <div class="mb-3">
                        <label for="selectReplacement" class="form-label fw-semibold text-dark">Pilih Komponen (Replacement) *</label>
                        <select class="select2 form-select" id="selectReplacement" name="replacement" required data-placeholder="-- Cari atau Pilih Replacement --">
                            <option value=""></option>
                            @foreach ($replacement as $item)
                                @php
                                    $itemStock = ($item->stock ?? 0) + ($item->warehouse_stock ?? 0);
                                    $serials = $item->product->serial ?? collect();
                                    $brandInfo = $serials->count() > 0 ? ' [Brand: ' . $serials->pluck('brand')->unique()->implode(', ') . ']' : '';
                                @endphp
                                <option value="{{ $item->id }}">
                                    {{ $item->replacement }}{{ $brandInfo }} (Stok: {{ $itemStock }} {{ $item->product->unit ?? 'Pcs' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 12px;">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Stok bundle otomatis dihitung dari jumlah stok terendah di antara seluruh komponen penyusunnya.
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                        <i class="mdi mdi-check"></i>
                        <span>Simpan Komponen</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
