{{-- Modal Pengajuan Retur Penjualan oleh Sales (Kanban Style UI) --}}
<div class="modal fade return-kanban-modal" id="modalRequestReturn" tabindex="-1" aria-labelledby="modalRequestReturnLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            {{-- Modal Header (Kanban Style) --}}
            <div class="modal-header flex-column align-items-start pb-3 bg-light border-bottom position-relative px-4 pt-3">
                <div class="d-flex align-items-center w-100 justify-content-between mb-2">
                    {{-- Top Left Badge & Status --}}
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="badge bg-warning text-dark font-11 rounded-pill font-monospace fw-bold px-2.5 py-1">
                            <i class="mdi mdi-file-document-outline me-1"></i>{{ $quoteNo ?? ($quote->no_quote ?? 'Quotation') }}
                        </span>
                        <span class="badge bg-label-secondary font-11 rounded-pill px-2.5 py-1">
                            <i class="mdi mdi-shield-refresh-outline me-1"></i>Sales Return Request
                        </span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="d-flex align-items-center justify-content-between w-100 flex-wrap gap-2">
                    <div>
                        <h4 class="modal-title fw-bold text-heading mb-0" id="modalRequestReturnLabel" style="font-size: 18px; letter-spacing: -0.2px;">
                            <i class="mdi mdi-keyboard-return me-1 text-warning"></i> Form Pengajuan Retur Penjualan
                        </h4>
                        <small class="text-muted font-12">Pilih item barang yang ingin diretur, tentukan alasan dan solusi penanganan.</small>
                    </div>
                    <div>
                        <span class="badge bg-label-primary px-3 py-1.5 rounded-pill font-12 fw-semibold" id="returnSelectedCounter">
                            <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>0 item dipilih
                        </span>
                    </div>
                </div>
            </div>

            <form id="formRequestReturn" action="{{ $formAction }}" method="POST">
                @csrf
                <div class="modal-body p-4 custom-modal-scroll" style="background-color: #f8fafc; max-height: calc(85vh - 140px); overflow-y: auto;">
                    
                    {{-- Section 1: Overview Info Cards (Kanban Info Block Style) --}}
                    <div class="card task-card-elevated border-0 shadow-xs mb-3">
                        <div class="card-body p-3.5">
                            <div class="d-flex align-items-center justify-content-between pb-2 mb-2.5 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-xs bg-label-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                        <i class="mdi mdi-information-outline" style="font-size: 16px;"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-heading font-13 text-uppercase">Informasi Dokumen &amp; Klien</h6>
                                </div>
                                <span class="badge bg-label-info font-11 rounded-pill">Quotation Ref</span>
                            </div>

                            <div class="row g-2.5">
                                <div class="col-12 col-md-4">
                                    <div class="task-info-block h-100">
                                        <span class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px;">No. Dokumen / Quotation</span>
                                        <strong class="text-primary font-monospace d-block font-14">{{ $quoteNo ?? ($quote->no_quote ?? '-') }}</strong>
                                        <small class="text-muted font-11">ID Referensi Sistem</small>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="task-info-block h-100">
                                        <span class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px;">Customer / Perusahaan</span>
                                        <strong class="text-heading d-block font-14 text-truncate" title="{{ $clientName ?? '-' }}">{{ $clientName ?? '-' }}</strong>
                                        <small class="text-muted font-11">Pihak Pembeli</small>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="task-info-block h-100">
                                        <span class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px;">Sales Pengaju</span>
                                        <strong class="text-heading d-block font-14">
                                            <i class="mdi mdi-account-check-outline text-success me-1"></i>{{ Auth::user()->name ?? 'Sales' }}
                                        </strong>
                                        <small class="text-muted font-11">{{ now()->format('d M Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Item Selection Table (Scrollable Container) --}}
                    <div class="card task-card-elevated border-0 shadow-xs mb-3">
                        <div class="card-header bg-white py-3 px-3.5 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs bg-label-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                    <i class="mdi mdi-package-variant-closed" style="font-size: 16px;"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-heading font-13 text-uppercase">1. Pilih Item Barang Retur</h6>
                                    <small class="text-muted font-11">Centang barang yang diajukan dan sesuaikan kuantiti retur</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check form-check-sm mb-0">
                                    <input class="form-check-input" type="checkbox" id="returnCheckAll">
                                    <label class="form-check-label font-12 fw-semibold cursor-pointer text-dark" for="returnCheckAll">
                                        Pilih Semua Item
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            {{-- Scrollable Table Area --}}
                            <div class="table-responsive custom-modal-scroll" style="max-height: 280px; overflow-y: auto;">
                                <table class="table table-sm table-hover align-middle mb-0 font-12" id="tableReturnItems">
                                    <thead class="table-light font-11 text-uppercase text-muted sticky-top shadow-xs" style="z-index: 2; top: 0;">
                                        <tr>
                                            <th style="width: 46px;" class="text-center bg-light">Pilih</th>
                                            <th style="width: 35px;" class="text-center bg-light">#</th>
                                            <th class="bg-light">Nama Item &amp; Spesifikasi</th>
                                            <th style="width: 95px;" class="text-center bg-light">Qty Awal</th>
                                            <th style="width: 130px;" class="text-center bg-light">Qty Retur</th>
                                            <th style="width: 140px;" class="text-end bg-light">Harga Satuan</th>
                                            <th style="width: 150px;" class="text-end bg-light pe-3">Subtotal Retur</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($returnItems ?? [] as $idx => $item)
                                            @php
                                                $itemId = $item['id'] ?? $item->id;
                                                $itemName = $item['name'] ?? ($item->label ?? ($item->name ?? ($item->product?->commodity ?? 'Item #' . ($idx + 1))));
                                                $itemPn = $item['pn'] ?? ($item->pn ?? ($item->serialProduct?->pn ?? ''));
                                                $itemDesc = $item['description'] ?? ($item->description ?? ($item->desc ?? ''));
                                                $qtyOrder = (float)($item['qty'] ?? ($item->qty ?? 1));
                                                $price = (float)($item['price'] ?? ($item->price ?? 0));
                                                $replacementId = $item['id_replacement'] ?? ($item->id_replacement ?? 0);
                                            @endphp
                                            <tr class="return-item-row" data-row="{{ $idx }}" style="transition: background-color 0.15s ease;">
                                                {{-- Checkbox --}}
                                                <td class="text-center">
                                                    <input type="checkbox"
                                                        class="form-check-input return-row-check"
                                                        name="selected_items[]"
                                                        value="{{ $idx }}"
                                                        data-row="{{ $idx }}"
                                                        style="cursor: pointer; width: 18px; height: 18px;">
                                                </td>

                                                {{-- No --}}
                                                <td class="text-center text-muted font-11">{{ $idx + 1 }}</td>

                                                {{-- Item Info --}}
                                                <td>
                                                    <div class="fw-bold text-dark font-12 d-flex align-items-center flex-wrap gap-1">
                                                        {{ $itemName }}
                                                        @if(!empty($itemPn) && $itemPn !== '-')
                                                            <span class="badge bg-label-secondary font-10 px-1.5 py-0.5">PN: {{ $itemPn }}</span>
                                                        @endif
                                                    </div>
                                                    @if(!empty($itemDesc) && $itemDesc !== $itemName)
                                                        <div class="text-muted font-11 text-truncate" style="max-width: 380px;" title="{{ $itemDesc }}">
                                                            {{ $itemDesc }}
                                                        </div>
                                                    @endif

                                                    {{-- Hidden inputs --}}
                                                    <input type="hidden" name="item_id[{{ $idx }}]" value="{{ $itemId }}">
                                                    <input type="hidden" name="item_name[{{ $idx }}]" value="{{ $itemName }}">
                                                    <input type="hidden" name="id_replacement[{{ $idx }}]" value="{{ $replacementId }}">
                                                    <input type="hidden" name="price[{{ $idx }}]" class="return-item-price" id="return_price_{{ $idx }}" value="{{ $price }}">
                                                    <input type="hidden" name="amount[{{ $idx }}]" class="return-item-amount" id="return_amount_{{ $idx }}" value="{{ $price }}">
                                                </td>

                                                {{-- Qty Order --}}
                                                <td class="text-center font-12 fw-semibold text-muted">
                                                    <span class="badge bg-label-secondary">{{ (int)$qtyOrder }}</span>
                                                </td>

                                                {{-- Qty Retur Input --}}
                                                <td class="text-center">
                                                    <div class="input-group input-group-sm" style="max-width: 110px; margin: 0 auto;">
                                                        <input type="number"
                                                            class="form-control text-center fw-bold return-row-qty"
                                                            name="qty[{{ $idx }}]"
                                                            id="return_qty_{{ $idx }}"
                                                            data-row="{{ $idx }}"
                                                            data-max="{{ (int)$qtyOrder }}"
                                                            min="1"
                                                            max="{{ (int)$qtyOrder }}"
                                                            value="1"
                                                            disabled
                                                            required
                                                            style="border-radius: 6px;">
                                                    </div>
                                                    <small class="text-muted font-10 d-block mt-0.5">Maks: {{ (int)$qtyOrder }} unit</small>
                                                </td>

                                                {{-- Harga Satuan --}}
                                                <td class="text-end font-monospace text-dark font-12">
                                                    Rp {{ number_format($price, 0, ',', '.') }}
                                                </td>

                                                {{-- Subtotal Retur --}}
                                                <td class="text-end font-monospace fw-bold text-danger font-12 pe-3" id="return_subtotal_label_{{ $idx }}">
                                                    Rp {{ number_format($price, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada rincian item pada penawaran ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Reason & Notes Card --}}
                    <div class="card task-card-elevated border-0 shadow-xs mb-3">
                        <div class="card-header bg-white py-2.5 px-3.5 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs bg-label-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                    <i class="mdi mdi-alert-circle-outline" style="font-size: 16px;"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-heading font-13 text-uppercase">2. Alasan Pengajuan Retur</h6>
                            </div>
                        </div>
                        <div class="card-body p-3.5">
                            <div class="row g-3">
                                <div class="col-12 col-md-5">
                                    <label class="form-label fw-bold font-12 text-dark mb-1">
                                        Kategori Alasan <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text bg-light border-end-0"><i class="mdi mdi-tag-outline text-muted"></i></span>
                                        <select class="form-select form-select-sm" name="reason_category" id="return_reason_category" required>
                                            <option value="" selected disabled>-- Pilih Kategori Alasan --</option>
                                            <option value="damaged">Barang Rusak / Cacat Pabrik / Malfungsi</option>
                                            <option value="wrong_spec">Salah Tipe / Spesifikasi Tidak Sesuai</option>
                                            <option value="excess">Kelebihan Kirim / Dobel Order</option>
                                            <option value="canceled">Permintaan Klien / Batal Order</option>
                                            <option value="other">Alasan Lainnya</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-7">
                                    <label class="form-label fw-bold font-12 text-dark mb-1">
                                        Keterangan / Detail Alasan <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control form-control-sm font-12"
                                        name="reason_note"
                                        id="return_reason_note"
                                        rows="2"
                                        placeholder="Jelaskan kronologi, kondisi kerusakan, atau catatan pengajuan retur..." required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Resolution Options (Interactive Cards) --}}
                    <div class="card task-card-elevated border-0 shadow-xs mb-2">
                        <div class="card-header bg-white py-2.5 px-3.5 border-bottom">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-xs bg-label-info rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                    <i class="mdi mdi-lightbulb-on-outline" style="font-size: 16px;"></i>
                                </div>
                                <h6 class="fw-bold mb-0 text-heading font-13 text-uppercase">3. Solusi Penanganan yang Diajukan</h6>
                            </div>
                        </div>
                        <div class="card-body p-3.5">
                            <div class="row g-2.5 mb-2">
                                {{-- Option 1: Replacement --}}
                                <div class="col-12 col-md-4">
                                    <label class="card h-100 cursor-pointer return-resolution-card border-primary bg-label-primary shadow-xs p-3" for="res_replacement" style="cursor: pointer; border-radius: 10px; transition: all 0.2s ease;">
                                        <div class="d-flex align-items-start gap-2.5">
                                            <input type="radio" class="form-check-input mt-1" name="resolution" id="res_replacement" value="replacement" checked>
                                            <div>
                                                <div class="fw-bold text-dark font-13 d-flex align-items-center gap-1">
                                                    <i class="mdi mdi-swap-horizontal text-primary fs-5"></i> Ganti Barang
                                                </div>
                                                <small class="text-muted d-block mt-1 font-11">Kirim barang/unit pengganti baru ke customer (Replacement).</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                {{-- Option 2: Refund --}}
                                <div class="col-12 col-md-4">
                                    <label class="card h-100 cursor-pointer return-resolution-card border bg-white p-3" for="res_refund" style="cursor: pointer; border-radius: 10px; transition: all 0.2s ease;">
                                        <div class="d-flex align-items-start gap-2.5">
                                            <input type="radio" class="form-check-input mt-1" name="resolution" id="res_refund" value="refund">
                                            <div>
                                                <div class="fw-bold text-dark font-13 d-flex align-items-center gap-1">
                                                    <i class="mdi mdi-cash-refund text-success fs-5"></i> Refund Dana
                                                </div>
                                                <small class="text-muted d-block mt-1 font-11">Pengembalian uang / transfer balik ke rekening customer.</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                {{-- Option 3: Deposit / Credit Note --}}
                                <div class="col-12 col-md-4">
                                    <label class="card h-100 cursor-pointer return-resolution-card border bg-white p-3" for="res_deposit" style="cursor: pointer; border-radius: 10px; transition: all 0.2s ease;">
                                        <div class="d-flex align-items-start gap-2.5">
                                            <input type="radio" class="form-check-input mt-1" name="resolution" id="res_deposit" value="deposit">
                                            <div>
                                                <div class="fw-bold text-dark font-13 d-flex align-items-center gap-1">
                                                    <i class="mdi mdi-credit-card-plus-outline text-info fs-5"></i> Potong Tagihan
                                                </div>
                                                <small class="text-muted d-block mt-1 font-11">Simpan deposit / kurangi tagihan invoice berikutnya.</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Dynamic Bank Information if Refund selected --}}
                            <div id="refundBankInfoBox" class="p-3 bg-white rounded-3 border mt-3 d-none shadow-xs" style="border-radius: 10px;">
                                <div class="d-flex align-items-center gap-2 mb-2 pb-1 border-bottom">
                                    <i class="mdi mdi-bank-outline text-success fs-5"></i>
                                    <h6 class="fw-bold font-12 text-dark mb-0">Informasi Rekening Tujuan Refund:</h6>
                                </div>
                                <div class="row g-2.5">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label font-11 fw-semibold mb-1">Nama Bank</label>
                                        <input type="text" class="form-control form-control-sm" name="bank_name" placeholder="BCA / Mandiri / BRI / dll">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label font-11 fw-semibold mb-1">Nomor Rekening</label>
                                        <input type="text" class="form-control form-control-sm font-monospace" name="bank_account" placeholder="1234567890">
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label class="form-label font-11 fw-semibold mb-1">Atas Nama Rekening</label>
                                        <input type="text" class="form-control form-control-sm" name="bank_holder" placeholder="PT / Nama Klien">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Modal Footer (Sticky Bottom) --}}
                <div class="modal-footer bg-white border-top py-2.5 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-11 d-block fw-semibold text-uppercase">Total Estimasi Nilai Retur:</span>
                        <span class="fw-bold fs-5 text-danger font-monospace" id="returnGrandTotalLabel">Rp 0</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-warning text-dark fw-bold px-3 d-flex align-items-center gap-1.5 shadow-sm" id="btnSubmitRequestReturn" disabled>
                            <i class="mdi mdi-send-check me-1"></i> Kirim Pengajuan Retur
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Scoped Kanban Modal Scroll & Card styling */
.return-kanban-modal .task-card-elevated {
    background-color: #ffffff !important;
    border-radius: 12px !important;
    border: 1px solid #edf0f4 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
    transition: box-shadow 0.2s ease;
}
.return-kanban-modal .task-card-elevated:hover {
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06) !important;
}
.return-kanban-modal .task-info-block {
    background-color: #f8fafc;
    border: 1px solid #edf0f4;
    border-radius: 10px;
    padding: 9px 12px;
    transition: all 0.15s ease;
}
.return-kanban-modal .task-info-block:hover {
    border-color: #cbd5e1;
    background-color: #f1f5f9;
}
.return-kanban-modal .return-resolution-card {
    border: 1.5px solid #e2e8f0 !important;
}
.return-kanban-modal .return-resolution-card:hover {
    border-color: #696cff !important;
}
.return-kanban-modal .custom-modal-scroll::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.return-kanban-modal .custom-modal-scroll::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 10px;
}
.return-kanban-modal .custom-modal-scroll::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}
.return-kanban-modal .custom-modal-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.return-item-row.is-selected {
    background-color: #f0f7ff !important;
}
</style>

@push('after-script')
<script>
$(document).ready(function() {
    function recalcReturnTotal() {
        var grandTotal = 0;
        var checkedCount = $('.return-row-check:checked').length;

        $('.return-row-check:checked').each(function() {
            var row = $(this).data('row');
            var qty = parseFloat($('#return_qty_' + row).val()) || 0;
            var price = parseFloat($('#return_price_' + row).val()) || 0;
            var subtotal = qty * price;

            $('#return_amount_' + row).val(subtotal);
            $('#return_subtotal_label_' + row).text('Rp ' + subtotal.toLocaleString('id-ID'));
            grandTotal += subtotal;
        });

        $('#returnGrandTotalLabel').text('Rp ' + grandTotal.toLocaleString('id-ID'));
        $('#returnSelectedCounter').html('<i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>' + checkedCount + ' item dipilih');

        if (checkedCount === 0) {
            $('#btnSubmitRequestReturn').prop('disabled', true);
        } else {
            $('#btnSubmitRequestReturn').prop('disabled', false);
        }
    }

    // Toggle row checkbox
    $(document).on('change', '.return-row-check', function() {
        var rowId = $(this).data('row');
        var isChecked = $(this).is(':checked');
        var $qtyInput = $('#return_qty_' + rowId);
        var $row = $(this).closest('.return-item-row');

        if (isChecked) {
            $qtyInput.prop('disabled', false);
            $row.addClass('is-selected');
        } else {
            $qtyInput.prop('disabled', true);
            $row.removeClass('is-selected');
        }
        recalcReturnTotal();
    });

    // Check all
    $(document).on('change', '#returnCheckAll', function() {
        var isChecked = $(this).is(':checked');
        $('.return-row-check').prop('checked', isChecked).trigger('change');
    });

    // Qty change
    $(document).on('input change', '.return-row-qty', function() {
        var max = parseInt($(this).data('max')) || 1;
        var val = parseInt($(this).val()) || 1;
        if (val > max) {
            $(this).val(max);
        } else if (val < 1) {
            $(this).val(1);
        }
        recalcReturnTotal();
    });

    // Toggle Resolution Cards & Bank Box
    $('input[name="resolution"]').on('change', function() {
        var val = $(this).val();
        $('.return-resolution-card').removeClass('border-primary bg-label-primary shadow-xs').addClass('border bg-white');
        $(this).closest('.return-resolution-card').removeClass('border bg-white').addClass('border-primary bg-label-primary shadow-xs');

        if (val === 'refund') {
            $('#refundBankInfoBox').removeClass('d-none');
        } else {
            $('#refundBankInfoBox').addClass('d-none');
        }
    });

    // Initial check on modal shown
    $('#modalRequestReturn').on('shown.bs.modal', function() {
        recalcReturnTotal();
    });

    // Validate on submit
    $('#formRequestReturn').on('submit', function(e) {
        var checkedCount = $('.return-row-check:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Minimal 1 Item',
                    text: 'Harap centang setidaknya 1 item yang ingin diajukan retur.'
                });
            } else {
                alert('Harap centang setidaknya 1 item yang ingin diajukan retur.');
            }
            return false;
        }
    });
});
</script>
@endpush
