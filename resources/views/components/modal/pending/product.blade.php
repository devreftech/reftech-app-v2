<form action="{{ route('pending-po.productEdit', $pending->id) }}" method="post" enctype="multipart/form-data">
    @method('PATCH')
    @csrf
    <div class="modal fade" id="productEdit" tabindex="-1" aria-labelledby="productEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-label-primary py-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary text-white font-11 rounded-pill font-monospace fw-bold">
                                {{ $pending->no_pending ?? 'SO' }}
                            </span>
                            <span class="badge bg-label-secondary font-11">{{ $pending->quote?->pic?->client?->company ?? '-' }}</span>
                        </div>
                        <h5 class="modal-title fw-bold text-heading mb-0" id="productEditLabel">
                            <i class="mdi mdi-list-status me-1 text-primary"></i> Update Status Barang &amp; Alokasi Gudang
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert bg-label-info border border-info py-2 px-3 mb-3 rounded-3 font-12">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Sesuaikan <strong>Status Pengecekan</strong> dan kuantitas alokasi stok <strong>BDG / BKS</strong>. Sistem otomatis menghitung ketersediaan fisik stok barang.
                    </div>

                    <div class="table-responsive border rounded-3 bg-white">
                        <table class="table table-sm table-hover align-middle mb-0 font-12">
                            <thead class="table-light font-11 text-uppercase text-muted">
                                <tr>
                                    <th style="width: 35px;" class="text-center">#</th>
                                    <th style="min-width: 240px;">Item &amp; Spesifikasi</th>
                                    <th style="width: 80px;" class="text-center">Qty Order</th>
                                    <th style="width: 140px;" class="text-center">Status Barang</th>
                                    <th style="width: 95px;" class="text-center">Alokasi BDG</th>
                                    <th style="width: 95px;" class="text-center">Alokasi BKS</th>
                                    <th style="min-width: 160px;">Catatan / Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($detQuotation as $item)
                                    @php
                                        $bdgStock = $item->equivalent?->product?->stock ?? 0;
                                        $bksStock = $item->equivalent?->product?->warehouse_stock ?? 0;
                                        $totalStock = $bdgStock + $bksStock;
                                        
                                        $selectedStatus = $item->status;
                                        if ($item->status == '1' || is_null($item->status)) {
                                            $selectedStatus = $totalStock >= $item->qty ? '2' : '3';
                                        }

                                        $defaultBdg = $item->bdg;
                                        $defaultBks = $item->bks;
                                        if (($item->status == '1' || is_null($item->status)) && ($item->bdg == 0 && $item->bks == 0)) {
                                            if ($bdgStock >= $item->qty) {
                                                $defaultBdg = $item->qty;
                                                $defaultBks = 0;
                                            } elseif ($totalStock >= $item->qty) {
                                                $defaultBdg = $bdgStock;
                                                $defaultBks = $item->qty - $bdgStock;
                                            } else {
                                                $defaultBdg = $bdgStock;
                                                $defaultBks = $bksStock;
                                            }
                                        }
                                        $goLabel = $item->equivalent?->product?->go == 'Genuine' ? 'G' : 'R';
                                    @endphp
                                    <tr>
                                        <td class="text-center text-muted font-11">{{ $no }}</td>
                                        <td>
                                            <div class="fw-bold text-dark font-12 d-flex align-items-center flex-wrap gap-1">
                                                <span class="badge {{ $goLabel == 'G' ? 'bg-label-primary' : 'bg-label-info' }} font-10">{{ $goLabel }}</span>
                                                {{ $item->equivalent?->brand ?? '' }} {{ $item->equivalent?->pn ?? '' }}
                                            </div>
                                            @if($item->equivalent?->product?->commodity)
                                                <div class="text-muted font-11 text-truncate" style="max-width: 280px;" title="{{ $item->equivalent->product->commodity }}">{{ $item->equivalent->product->commodity }}</div>
                                            @endif
                                            <div class="d-flex align-items-center gap-2 mt-1" style="font-size: 10.5px;">
                                                <span class="text-muted"><i class="mdi mdi-warehouse me-0.5"></i>Stok Fisik: BDG: <strong class="text-dark">{{ $bdgStock }}</strong> | BKS: <strong class="text-dark">{{ $bksStock }}</strong></span>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold text-dark font-12">
                                            {{ $item->qty }} <span class="font-11 text-muted fw-normal">{{ $item->info_qty ?? 'pcs' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <select class="form-select form-select-sm font-11 fw-semibold" name="status[]">
                                                <option value="1" {{ $selectedStatus == '1' ? 'selected' : '' }}>On Check</option>
                                                <option value="2" {{ $selectedStatus == '2' ? 'selected' : '' }}>Ready Stock</option>
                                                <option value="3" {{ $selectedStatus == '3' ? 'selected' : '' }}>Kurang</option>
                                                <option value="4" {{ $selectedStatus == '4' ? 'selected' : '' }}>Pre-Order</option>
                                                <option value="5" {{ $selectedStatus == '5' ? 'selected' : '' }}>Delivery Process</option>
                                                <option value="6" {{ $selectedStatus == '6' ? 'selected' : '' }}>Done</option>
                                                <option value="7" {{ $selectedStatus == '7' ? 'selected' : '' }}>Cancel</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="form-control form-control-sm text-center fw-bold font-12" name="bdg[]" value="{{ (int)$defaultBdg }}" min="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="form-control form-control-sm text-center fw-bold font-12" name="bks[]" value="{{ (int)$defaultBks }}" min="0">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm font-11" name="note[]" value="{{ @$item->note }}" placeholder="Catatan item...">
                                        </td>
                                    </tr>
                                    @php $no++; @endphp
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
