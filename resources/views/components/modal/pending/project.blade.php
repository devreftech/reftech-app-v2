<form action="{{ route('pending-po.projectEdit', $pending->id) }}" method="post" enctype="multipart/form-data">
    @method('PATCH')
    @csrf
    <div class="modal fade" id="replacementEdit" tabindex="-1" aria-labelledby="replacementEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-label-primary py-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary text-white font-11 rounded-pill font-monospace fw-bold">
                                {{ $pending->no_pending ?? 'SO' }}
                            </span>
                            <span class="badge bg-label-secondary font-11">{{ $pending->quote?->pic?->client?->company ?? $pending->unitQuotation?->client?->company ?? '-' }}</span>
                        </div>
                        <h5 class="modal-title fw-bold text-heading mb-0" id="replacementEditLabel">
                            <i class="mdi mdi-list-status me-1 text-primary"></i> Update Status Barang Proyek &amp; Alokasi Gudang
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert bg-label-info border border-info py-2 px-3 mb-3 rounded-3 font-12">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Sesuaikan item equivalent, <strong>Status Pengecekan</strong>, dan kuantitas alokasi <strong>BDG / BKS</strong> untuk masing-masing bagian pekerjaan proyek.
                    </div>

                    <div class="table-responsive border rounded-3 bg-white">
                        <table class="table table-sm table-hover align-middle mb-0 font-12">
                            <thead class="table-light font-11 text-uppercase text-muted">
                                <tr>
                                    <th style="width: 35px;" class="text-center">#</th>
                                    <th style="min-width: 260px;">Item &amp; Equivalent</th>
                                    <th style="width: 80px;" class="text-center">Qty Order</th>
                                    <th style="width: 140px;" class="text-center">Status Barang</th>
                                    <th style="width: 95px;" class="text-center">Alokasi BDG</th>
                                    <th style="width: 95px;" class="text-center">Alokasi BKS</th>
                                    <th style="min-width: 160px;">Catatan / Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $abjad = 64; @endphp
                                @foreach ($subQuote as $subJudul)
                                    @php
                                        $no = 1;
                                        $abjad++;
                                    @endphp
                                    <tr class="table-secondary">
                                        <td class="text-center fw-bold text-primary">{{ chr($abjad) }}</td>
                                        <td colspan="6" class="fw-bold text-dark py-2">
                                            <i class="mdi mdi-folder-outline me-1 text-primary"></i>{{ $subJudul->subtitle }}
                                        </td>
                                    </tr>
                                    @foreach ($subJudul->detail as $product)
                                        @php
                                            $currEq = $product->pending[0]->equivalent ?? null;
                                            $bdgStock = $currEq?->product?->stock ?? 0;
                                            $bksStock = $currEq?->product?->warehouse_stock ?? 0;
                                            $currStatus = @$product->pending[0]->status ?? 1;
                                        @endphp
                                        <tr>
                                            <td class="text-center text-muted font-11">{{ $no }}</td>
                                            <td>
                                                <div class="mb-1">
                                                    <select class="form-select form-select-sm select2-equivalent-ajax font-12"
                                                        data-allow-clear="true" name="equivalent[]" style="width: 100%;">
                                                        <option value="0">-- Pilih Equivalent --</option>
                                                        @if ($currEq)
                                                            <option value="{{ $currEq->id }}" selected>
                                                                {{ $currEq->brand }} {{ $currEq->pn }} - {{ $currEq->product?->go == 'Replacement' ? 'R' : 'G' }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mt-1" style="font-size: 10.5px;">
                                                    <span class="text-muted"><i class="mdi mdi-warehouse me-0.5"></i>Stok Fisik: BDG: <strong class="text-dark">{{ $bdgStock }}</strong> | BKS: <strong class="text-dark">{{ $bksStock }}</strong></span>
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold text-dark font-12">
                                                {{ $product->qty }} <span class="font-11 text-muted fw-normal">{{ $product->info_qty ?? 'pcs' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <select class="form-select form-select-sm font-11 fw-semibold" name="status[]">
                                                    <option value="1" {{ $currStatus == '1' ? 'selected' : '' }}>On Check</option>
                                                    <option value="2" {{ $currStatus == '2' ? 'selected' : '' }}>Ready Stock</option>
                                                    <option value="3" {{ $currStatus == '3' ? 'selected' : '' }}>Kurang</option>
                                                    <option value="4" {{ $currStatus == '4' ? 'selected' : '' }}>Pre-Order</option>
                                                    <option value="5" {{ $currStatus == '5' ? 'selected' : '' }}>Delivery Process</option>
                                                    <option value="6" {{ $currStatus == '6' ? 'selected' : '' }}>Done</option>
                                                    <option value="7" {{ $currStatus == '7' ? 'selected' : '' }}>Cancel</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" class="form-control form-control-sm text-center fw-bold font-12" name="bdg[]" value="{{ (int)@$product->pending[0]->bdg }}" min="0">
                                            </td>
                                            <td class="text-center">
                                                <input type="number" class="form-control form-control-sm text-center fw-bold font-12" name="bks[]" value="{{ (int)@$product->pending[0]->bks }}" min="0">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm font-11" name="note[]" value="{{ @$product->pending[0]->note }}" placeholder="Catatan item...">
                                            </td>
                                        </tr>
                                        @php $no++; @endphp
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3 d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                        <i class="mdi mdi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
