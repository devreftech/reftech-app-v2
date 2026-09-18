<form action="{{ route('purchase-request.store', $pending->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="purchaseReq" tabindex="-1" aria-labelledby="purchaseReqLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light py-3 border-bottom">
                    <div>
                        <h5 class="modal-title fw-bold text-primary mb-1 d-flex align-items-center" id="purchaseReqLabel">
                            <i class="mdi mdi-cart-plus me-2 fs-4"></i> Buat Purchase Request (PR)
                        </h5>
                        <div class="text-muted font-12">
                            <span class="fw-semibold text-dark">{{ $pending->quote->invoice[0]?->no_invoice ?? ($pending->quote->pic->client->company ?? '-') }}</span>
                            &bull; SO #{{ $pending->id }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-primary d-flex align-items-center py-2 px-3 mb-3">
                        <i class="mdi mdi-information-outline fs-5 me-2"></i>
                        <span class="font-12">
                            Masukkan <strong>Qty PR</strong> dan <strong>Catatan</strong> untuk item yang ingin dipesan ke supplier. Item dengan Qty 0 akan diabaikan secara otomatis.
                        </span>
                    </div>

                    <div class="card border shadow-none mb-0">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr class="font-12">
                                        <th style="width: 40px;" class="text-center">No</th>
                                        <th>Item &amp; Equivalent</th>
                                        <th style="width: 130px;" class="text-center">Stok &amp; Status</th>
                                        <th style="width: 90px;" class="text-center">Qty Order</th>
                                        <th style="width: 120px;" class="text-center">Qty PR</th>
                                        <th style="width: 250px;">Catatan PR</th>
                                    </tr>
                                </thead>
                                <tbody class="font-13">
                                    @php $no = 1; @endphp
                                    @foreach ($detQuotation as $item)
                                        @php
                                            $prod = $item->equivalent->product ?? null;
                                            $bdgStock = $prod->stock ?? 0;
                                            $bksStock = $prod->warehouse_stock ?? 0;
                                            $defaultQty = ($item->status == 3) ? (float)$item->qty : 0;
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-medium">{{ $no }}</td>
                                            <td style="max-width: 320px; white-space: normal;">
                                                <input type="hidden" name="id_equivalent[]" value="{{ $item->id_equivalent }}">
                                                @if ($item->id_equivalent == '0' || !$item->equivalent)
                                                    <span class="text-muted">-</span>
                                                @else
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <span class="fw-bold text-dark">{{ $item->equivalent->brand ?? '' }} {{ $item->equivalent->pn ?? '' }}</span>
                                                        @if ($prod && $prod->go)
                                                            <span class="badge {{ $prod->go == 'Genuine' ? 'bg-label-success' : 'bg-label-warning' }} font-10">{{ $prod->go }}</span>
                                                        @endif
                                                    </div>
                                                    @if ($prod && $prod->description)
                                                        <div class="text-muted font-11 mt-1">{{ $prod->description }}</div>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($item->status == 3)
                                                    <span class="badge bg-label-danger font-11 mb-1">Kurang</span>
                                                @elseif ($item->status == 2)
                                                    <span class="badge bg-label-success font-11 mb-1">Ready</span>
                                                @else
                                                    <span class="badge bg-label-secondary font-11 mb-1">On Check</span>
                                                @endif
                                                <div class="text-muted font-10">BDG: {{ $bdgStock }} | BKS: {{ $bksStock }}</div>
                                            </td>
                                            <td class="text-center fw-bold text-dark">
                                                {{ $item->qty }} {{ $item->info_qty ?? ($prod->unit ?? '') }}
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm text-center fw-bold"
                                                    name="qty[]" min="0" step="any"
                                                    value="{{ $defaultQty }}" placeholder="0">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="note[]" value="{{ $item->status == 3 ? 'Kebutuhan SO kurang stok' : '' }}"
                                                    placeholder="Catatan item...">
                                            </td>
                                        </tr>
                                        @php $no++; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Section Optional: Tambah Item Manual / Di Luar SO --}}
                    <div class="card border border-dashed shadow-none mt-3">
                        <div class="card-header bg-transparent py-2 px-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="fw-bold text-secondary font-12"><i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Item Manual / Di Luar List SO (Opsional)</span>
                            <button type="button" class="btn btn-xs btn-outline-primary btn-add-manual-pr-row">
                                <i class="mdi mdi-plus me-1"></i> Tambah Baris Manual
                            </button>
                        </div>
                        <div class="card-body p-3">
                            <div id="manualPrItemsContainer" class="d-flex flex-column gap-2">
                                <div class="manual-pr-row border rounded-3 p-2 bg-light bg-opacity-50">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-5">
                                            <label class="form-label font-11 mb-1">Cari Equivalent Master</label>
                                            <select class="form-select select2-equivalent-ajax" data-allow-clear="true" name="manual_id_equivalent[]" style="width:100%">
                                                <option value="0"> ---- Cari Part Number / Brand ---- </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label font-11 mb-1">Qty</label>
                                            <input type="number" class="form-control form-control-sm" name="manual_qty[]" min="0" step="any" placeholder="0">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label font-11 mb-1">Catatan</label>
                                            <input type="text" class="form-control form-control-sm" name="manual_note[]" placeholder="Catatan item manual...">
                                        </div>
                                        <div class="col-md-1 text-center pt-3">
                                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-manual-pr-row" title="Hapus Baris" style="display: none;">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 border-top">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="mdi mdi-cart-plus me-1"></i> Buat Purchase Request
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
