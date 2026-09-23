{{-- Interactive Modal: Buat Surat Jalan / Barang Keluar --}}
<div class="modal fade" id="modalCreateProductOut" tabindex="-1" aria-labelledby="modalCreateProductOutLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-label-warning py-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-warning text-dark font-11 rounded-pill font-monospace fw-bold">
                            <i class="mdi mdi-truck-delivery-outline me-1"></i>{{ $pending->no_pending ?? 'SO' }}
                        </span>
                        @if(($pending->status ?? 0) == 8)
                            <span class="badge bg-info text-white font-11 rounded-pill">Partial Delivery Sedang Berjalan</span>
                        @endif
                    </div>
                    <h5 class="modal-title fw-bold text-heading mb-0" id="modalCreateProductOutLabel">
                        <i class="mdi mdi-package-variant-closed me-1 text-warning"></i> Form Buat Surat Jalan (Barang Keluar)
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formModalProductOut" action="{{ route('pending-po.product_out-post', $pending->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    {{-- Previous Deliveries Info if any --}}
                    @if(!empty($allProductOuts) && count($allProductOuts) > 0)
                        <div class="alert bg-label-info border border-info py-2 px-3 mb-3 rounded-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <span class="font-12 fw-semibold text-info">
                                    <i class="mdi mdi-information-outline me-1"></i> Order ini telah memiliki <strong>{{ count($allProductOuts) }} Surat Jalan</strong> sebelumnya.
                                </span>
                                <span class="badge bg-info text-white font-10">Pengiriman Bertahap (Partial)</span>
                            </div>
                        </div>
                    @endif

                    {{-- Document Header Fields --}}
                    <div class="card border bg-light-subtle mb-3">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                {{-- No. BK --}}
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                                        No. Dokumen BK <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-sm fw-bold text-primary font-monospace"
                                        name="no_product_out" id="modal_no_product_out"
                                        value="{{ $nextNoProductOut ?? '' }}" required>
                                </div>

                                {{-- Tanggal --}}
                                <div class="col-6 col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                                        Tanggal Kirim <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control form-control-sm fw-semibold"
                                        name="date" id="modal_date"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>

                                {{-- Offline / Online --}}
                                <div class="col-6 col-md-4">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                                        Tipe Pengiriman <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-sm fw-semibold" name="vers" id="modal_vers" required>
                                        <option value="Offline" selected>Offline / Ekspedisi</option>
                                        <option value="Online">Online</option>
                                    </select>
                                </div>

                                {{-- Hidden invoice & PO --}}
                                <input type="hidden" name="invoice" value="{{ $invoiceNo ?? '-' }}">
                                <input type="hidden" name="po" value="{{ $poNo ?? '-' }}">
                                <input type="hidden" name="shipping" value="0">
                                <input type="hidden" name="total" value="0">

                                {{-- Detail Client / Tujuan --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">
                                        Tujuan / Alamat Pengiriman <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control form-control-sm font-12" name="detail_client" rows="2" required>{{ $detailClientFormatted ?? '' }}</textarea>
                                </div>

                                {{-- Catatan --}}
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-muted text-uppercase mb-1">Catatan Pengiriman</label>
                                    <textarea class="form-control form-control-sm font-12" name="note" rows="2" placeholder="Driver / No. Kendaraan / Catatan khusus...">-</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Item Selection Table --}}
                    <div class="mb-2">
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                            <label class="form-label fw-bold text-uppercase text-dark font-12 mb-0">
                                <i class="mdi mdi-checkbox-multiple-marked-outline me-1 text-primary"></i>Pilih Item yang Akan Dikeluarkan:
                            </label>
                            <span class="badge bg-label-primary font-11" id="modalSelectedCounter">0 item dipilih</span>
                        </div>

                        <div class="table-responsive border rounded-3 bg-white">
                            <table class="table table-sm table-hover align-middle mb-0 font-12">
                                <thead class="table-light font-11 text-uppercase text-muted">
                                    <tr>
                                        <th style="width: 42px;" class="text-center">
                                            <input type="checkbox" class="form-check-input" id="modalCheckAll" title="Pilih Semua">
                                        </th>
                                        <th style="width: 30px;" class="text-center">#</th>
                                        <th>Nama Barang &amp; Spesifikasi</th>
                                        <th style="width: 120px;" class="text-center">Gudang</th>
                                        <th style="width: 170px;" class="text-center">Status Pemenuhan</th>
                                        <th style="width: 130px;" class="text-center">Qty Kirim Sekarang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($modalItemsData ?? [] as $idx => $row)
                                        @php
                                            $isCompleted = ($row['qty_remaining'] <= 0);
                                        @endphp
                                        <tr class="modal-item-row {{ $isCompleted ? 'bg-light text-muted opacity-60' : '' }}" data-row="{{ $idx }}">
                                            {{-- Checkbox --}}
                                            <td class="text-center">
                                                <input type="checkbox"
                                                    class="form-check-input modal-row-check"
                                                    name="selected_items[]"
                                                    value="{{ $idx }}"
                                                    data-row="{{ $idx }}"
                                                    {{ $isCompleted ? 'disabled' : 'checked' }}>
                                            </td>

                                            {{-- No --}}
                                            <td class="text-center text-muted font-11">{{ $idx + 1 }}</td>

                                            {{-- Item Info & Replacement SKU Selector --}}
                                            <td>
                                                <div class="fw-bold text-dark font-12 d-flex align-items-center flex-wrap gap-1">
                                                    {{ $row['name'] }}
                                                    @if(!empty($row['pn']) && $row['pn'] !== '-')
                                                        <span class="badge bg-label-primary font-10">PN: {{ $row['pn'] }}</span>
                                                    @endif
                                                </div>
                                                @if(!empty($row['description']) && $row['description'] !== $row['name'])
                                                    <div class="text-muted font-11 text-truncate mb-1" style="max-width: 380px;" title="{{ $row['description'] }}">{{ $row['description'] }}</div>
                                                @endif

                                                @if(!$row['is_non_inventory'])
                                                    <div class="mt-2 pt-1 border-top">
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <span class="font-11 fw-semibold text-muted">
                                                                <i class="mdi mdi-swap-horizontal me-1 text-primary"></i>Replacement SKU (Fisik):
                                                            </span>
                                                            <span class="badge bg-label-secondary font-10" id="modal_rep_stock_{{ $idx }}">
                                                                BDG: <strong>{{ $row['stock_bdg'] ?? 0 }}</strong> | BKS: <strong>{{ $row['stock_bks'] ?? 0 }}</strong>
                                                            </span>
                                                        </div>
                                                        <select class="form-select form-select-sm select2-modal-replacement font-11"
                                                            name="replacement[{{ $idx }}]"
                                                            id="modal_replacement_{{ $idx }}"
                                                            data-row="{{ $idx }}"
                                                            style="width: 100%;"
                                                            {{ $isCompleted ? 'disabled' : '' }}>
                                                            @if ($row['default_rep_id'])
                                                                <option value="{{ $row['default_rep_id'] }}" selected>{{ $row['default_rep_text'] }}</option>
                                                            @else
                                                                <option value="">-- Pilih Replacement --</option>
                                                            @endif
                                                            @foreach($row['replacements'] ?? [] as $repItem)
                                                                @if($repItem->id != ($row['default_rep_id'] ?? 0))
                                                                    <option value="{{ $repItem->id }}"
                                                                        data-stock="{{ $repItem->stock }}"
                                                                        data-wh-stock="{{ $repItem->warehouse_stock }}"
                                                                        data-commodity="{{ $repItem->product?->commodity ?? '' }}">
                                                                        {{ $repItem->replacement }} | {{ $repItem->product?->commodity }} (BDG: {{ $repItem->stock }}, BKS: {{ $repItem->warehouse_stock }})
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @else
                                                    <div class="mt-1">
                                                        <span class="badge bg-label-warning font-10">
                                                            <i class="mdi mdi-information-outline me-1"></i>Non-Inventory / Ready Stock (Tanpa SKU Fisik)
                                                        </span>
                                                        <input type="hidden" name="replacement[{{ $idx }}]" value="0">
                                                    </div>
                                                @endif

                                                {{-- Hidden Inputs per item --}}
                                                <input type="hidden" name="equivalent[{{ $idx }}]" id="modal_equiv_{{ $idx }}" value="{{ $row['default_serial_id'] ?? 0 }}">
                                                <input type="hidden" name="price[{{ $idx }}]" value="{{ $row['price'] ?? 0 }}">
                                                <input type="hidden" name="amount[{{ $idx }}]" value="{{ $row['amount'] ?? 0 }}">
                                            </td>

                                            {{-- Warehouse --}}
                                            <td class="text-center">
                                                <select class="form-select form-select-sm font-11 py-1 modal-row-wh" name="warehouse[{{ $idx }}]" {{ $isCompleted ? 'disabled' : '' }}>
                                                    <option value="BDG" selected>BDG</option>
                                                    <option value="BKS">BKS</option>
                                                </select>
                                            </td>

                                            {{-- Fulfillment Status --}}
                                            <td class="text-center">
                                                <div class="font-11 text-muted">
                                                    Order: <strong>{{ $row['qty_ordered'] }}</strong> | Kirim: <strong class="text-success">{{ $row['qty_shipped'] }}</strong>
                                                </div>
                                                <div class="mt-0.5">
                                                    @if($isCompleted)
                                                        <span class="badge bg-label-success font-10"><i class="mdi mdi-check-circle-outline me-0.5"></i>Tuntas</span>
                                                    @else
                                                        <span class="badge bg-label-warning font-10">Sisa: <strong>{{ $row['qty_remaining'] }}</strong></span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Qty Kirim Input --}}
                                            <td class="text-center">
                                                @if(!$isCompleted)
                                                    <div class="input-group input-group-sm" style="max-width: 110px; margin: 0 auto;">
                                                        <input type="number"
                                                            class="form-control text-center fw-bold modal-row-qty"
                                                            name="qty[{{ $idx }}]"
                                                            id="modal_qty_{{ $idx }}"
                                                            data-row="{{ $idx }}"
                                                            min="1"
                                                            max="{{ $row['qty_remaining'] }}"
                                                            value="{{ (int)$row['qty_remaining'] }}"
                                                            required>
                                                    </div>
                                                @else
                                                    <span class="text-muted font-11 fst-italic">0 (Selesai)</span>
                                                    <input type="hidden" name="qty[{{ $idx }}]" value="0" disabled>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada rincian item pada Sales Order ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-2 px-3 d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold d-flex align-items-center gap-1 shadow-sm" id="btnSubmitModalProductOut">
                        <i class="mdi mdi-check-circle me-1"></i> Simpan Surat Jalan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('after-script')
<script>
$(document).ready(function() {
    function updateModalCounter() {
        var checkedCount = $('.modal-row-check:checked').length;
        $('#modalSelectedCounter').text(checkedCount + ' item dipilih');
        if (checkedCount === 0) {
            $('#btnSubmitModalProductOut').prop('disabled', true);
        } else {
            $('#btnSubmitModalProductOut').prop('disabled', false);
        }
    }

    function initModalReplacementSelect2() {
        if (typeof $.fn.select2 === 'undefined') return;

        $('.select2-modal-replacement').each(function() {
            var $select = $(this);
            if ($select.data('select2')) {
                $select.select2('destroy');
            }

            $select.select2({
                dropdownParent: $('#modalCreateProductOut'),
                width: '100%',
                placeholder: '-- Pilih Replacement / SKU --',
                allowClear: true,
                ajax: {
                    url: '/pending-po/replacements/search',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term || '' };
                    },
                    processResults: function (data) {
                        return {
                            results: data.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.replacement + ' | ' + item.commodity + ' (BDG: ' + item.stock + ', BKS: ' + item.warehouse_stock + ')',
                                    stock: item.stock,
                                    warehouse_stock: item.warehouse_stock,
                                    serial_id: item.serial_id
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            }).on('select2:select', function(e) {
                var row = $(this).data('row');
                var data = e.params.data;
                var stockBdg = (data.stock !== undefined) ? data.stock : ($(this).find(':selected').data('stock') || 0);
                var stockBks = (data.warehouse_stock !== undefined) ? data.warehouse_stock : ($(this).find(':selected').data('wh-stock') || 0);
                $('#modal_rep_stock_' + row).html('BDG: <strong>' + stockBdg + '</strong> | BKS: <strong>' + stockBks + '</strong>');
                if (data.serial_id) {
                    $('#modal_equiv_' + row).val(data.serial_id);
                }
            }).on('change', function() {
                var row = $(this).data('row');
                var $opt = $(this).find(':selected');
                if ($opt.length && $opt.data('stock') !== undefined) {
                    var stockBdg = $opt.data('stock') || 0;
                    var stockBks = $opt.data('wh-stock') || 0;
                    $('#modal_rep_stock_' + row).html('BDG: <strong>' + stockBdg + '</strong> | BKS: <strong>' + stockBks + '</strong>');
                }
            });
        });
    }

    // Toggle row check
    $(document).on('change', '.modal-row-check', function() {
        var rowId = $(this).data('row');
        var isChecked = $(this).is(':checked');
        var $qtyInput = $('#modal_qty_' + rowId);
        var $whSelect = $('select[name="warehouse[' + rowId + ']"]');
        var $repSelect = $('#modal_replacement_' + rowId);

        if (isChecked) {
            $qtyInput.prop('disabled', false);
            $whSelect.prop('disabled', false);
            $repSelect.prop('disabled', false);
        } else {
            $qtyInput.prop('disabled', true);
            $whSelect.prop('disabled', true);
            $repSelect.prop('disabled', true);
        }
        updateModalCounter();
    });

    // Check all
    $(document).on('change', '#modalCheckAll', function() {
        var isChecked = $(this).is(':checked');
        $('.modal-row-check:not(:disabled)').prop('checked', isChecked).trigger('change');
    });

    // Initial counter update & select2 when modal shown
    $('#modalCreateProductOut').on('shown.bs.modal', function() {
        updateModalCounter();
        initModalReplacementSelect2();
    });

    // Prevent submission if 0 items checked
    $('#formModalProductOut').on('submit', function(e) {
        var checkedCount = $('.modal-row-check:checked').length;
        if (checkedCount === 0) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Minimal 1 Item',
                    text: 'Harap centang setidaknya 1 item yang ingin dikeluarkan pada Surat Jalan ini.'
                });
            } else {
                alert('Harap centang setidaknya 1 item yang ingin dikeluarkan pada Surat Jalan ini.');
            }
            return false;
        }
    });
});
</script>
@endpush
