@extends('layouts.sales.app')
@section('title', 'Form Barang Keluar - Sales Order')
@section('content')
@php
    $isLogistic = auth()->user()?->hasRole('Logistic') || auth()->user()?->hasRole('logistic');
@endphp
<form action="{{ route('pending-po.product_out-post', $id) }}" method="post" enctype="multipart/form-data">
    @csrf

    {{-- Alerts --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Delivery History Alert if partial deliveries exist --}}
    @if(!empty($allProductOuts) && count($allProductOuts) > 0)
        <div class="alert bg-label-info border border-info alert-dismissible mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-truck-check-outline fs-4 text-info mt-n1"></i>
                <div class="w-100">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                        <h6 class="fw-bold mb-0 text-info">Riwayat Pengiriman Sebelumnya (Partial Delivery)</h6>
                        <span class="badge bg-info text-white">{{ count($allProductOuts) }} Surat Jalan Telah Dibuat</span>
                    </div>
                    <p class="small mb-2">Sales Order ini telah memiliki catatan pengeluaran barang sebelumnya. Input di bawah ini telah disesuaikan dengan <strong>sisa barang</strong> yang belum terkirim.</p>
                    <div class="table-responsive bg-white rounded-2 border">
                        <table class="table table-sm table-borderless font-12 mb-0">
                            <thead class="table-light font-11 text-uppercase text-muted">
                                <tr>
                                    <th class="ps-2">No. Surat Jalan (BK)</th>
                                    <th>Tanggal</th>
                                    <th>Driver / Kendaraan</th>
                                    <th class="text-center">Jml Item</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allProductOuts as $pastPo)
                                    <tr class="border-bottom">
                                        <td class="ps-2 fw-bold font-monospace text-primary">{{ $pastPo->code_product_out }}</td>
                                        <td>{{ $pastPo->date_out ? \Carbon\Carbon::parse($pastPo->date_out)->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $pastPo->driver ?? '-' }} {{ $pastPo->vehicle_number ? '(' . $pastPo->vehicle_number . ')' : '' }}</td>
                                        <td class="text-center"><span class="badge bg-label-primary">{{ $pastPo->detailProductOut->count() }} item</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Header Info --}}
    <div class="row g-3 mb-4">
        {{-- No. Dokumen BK --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <label class="form-label fw-semibold text-muted small text-uppercase">No. Dokumen Barang Keluar</label>
                    <input type="text" class="form-control form-control-lg fw-bold text-primary"
                        name="no_product_out" id="no_product_out"
                        value="{{ old('no_product_out', $nextNoProductOut) }}"
                        placeholder="No. BK otomatis">
                    <small class="text-muted">Edit jika perlu mengganti penomoran</small>
                </div>
            </div>
        </div>

        {{-- No. Invoice --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <label class="form-label fw-semibold text-muted small text-uppercase">No. Invoice</label>
                    <input type="text" class="form-control form-control-lg fw-bold"
                        name="invoice" id="invoice"
                        value="{{ old('invoice', $invoiceNo) }}"
                        placeholder="No. Invoice">
                </div>
            </div>
        </div>

        {{-- No. PO --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <label class="form-label fw-semibold text-muted small text-uppercase">No. PO</label>
                    <input type="text" class="form-control form-control-lg fw-bold"
                        name="po" id="po"
                        value="{{ old('po', $poNo) }}"
                        placeholder="No. PO">
                </div>
            </div>
        </div>
    </div>

    {{-- Main Form Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="mb-0 fw-bold">
                <i class="mdi mdi-package-variant-closed me-2 text-primary"></i>Form Barang Keluar &mdash; Sales Order
            </h5>
        </div>
        <div class="card-body">

            {{-- Detail Client & Metadata --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-lg-6">
                    <label class="form-label fw-semibold">Detail Client / Tujuan Pengiriman</label>
                    <textarea class="form-control" rows="4" name="detail_client"
                        placeholder="Detail client...">{{ old('detail_client', $detailClientFormatted) }}</textarea>
                </div>
                <div class="col-6 col-lg-3">
                    <label class="form-label fw-semibold">Offline / Online</label>
                    <select class="form-select" id="vers" name="vers">
                        <option disabled {{ old('vers') ? '' : 'selected' }}>-- Pilih --</option>
                        <option value="Offline" {{ old('vers') == 'Offline' ? 'selected' : '' }}>Offline</option>
                        <option value="Online" {{ old('vers') == 'Online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>
                <div class="col-6 col-lg-3">
                    <label class="form-label fw-semibold">Tanggal Transaksi</label>
                    <input class="form-control" type="date" id="date" name="date"
                        value="{{ old('date', now()->format('Y-m-d')) }}">
                </div>
            </div>

            <hr>
            <h6 class="fw-bold mb-3">
                <i class="mdi mdi-format-list-bulleted me-1"></i>Daftar Item &amp; Alokasi Barang Keluar
            </h6>

            <div id="items-container">
            @foreach ($itemsData as $idx => $row)
            @php
                $item     = $row['dPending'];
                $equiv    = $row['equiv'];
                $reps     = $row['replacements'];
                $defRepId = $row['default_rep_id'];
                $no       = $idx + 1;
            @endphp
            <div class="item-row border rounded-3 p-3 mb-3 position-relative" id="item-row-{{ $no }}" data-row="{{ $no }}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-label-primary fs-6 fw-bold">Item #{{ $no }}</span>
                        <span class="badge bg-label-secondary font-11">Total Order: <strong>{{ $row['qty_ordered'] ?? $row['qty'] }}</strong></span>
                        @if(($row['qty_shipped'] ?? 0) > 0)
                            <span class="badge bg-label-info font-11">Sudah Terkirim: <strong>{{ $row['qty_shipped'] }}</strong></span>
                            <span class="badge bg-label-warning font-11">Sisa Belum Kirim: <strong>{{ $row['qty_remaining'] ?? $row['qty'] }}</strong></span>
                        @endif
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-item"
                        data-row="{{ $no }}" title="Hapus item ini">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                </div>

                <div class="row g-3">
                    {{-- Nama Item / SKU --}}
                    <div class="col-12 col-md-5">
                        <label class="form-label fw-semibold small">Nama Item / SKU</label>
                        @if (!$row['is_non_inventory'])
                            <select class="form-select select2-replacement mb-2"
                                id="replacement-{{ $no }}"
                                name="replacement[]"
                                data-row="{{ $no }}">
                                @if ($defRepId)
                                    <option value="{{ $defRepId }}" selected>{{ $row['default_rep_text'] }}</option>
                                @else
                                    <option value="">-- Pilih Replacement --</option>
                                @endif
                            </select>
                            <input type="hidden" name="equivalent[]" value="{{ $row['default_serial_id'] }}">
                            @if ($equiv)
                                <small class="text-muted">
                                    <i class="mdi mdi-barcode me-1"></i>{{ $equiv->pn ?? '-' }} &mdash; {{ $equiv->product?->commodity ?? '-' }}
                                </small>
                            @endif
                        @else
                            <input type="text" class="form-control mb-2" value="{{ $row['name'] }}" disabled>
                            <input type="hidden" name="replacement[]" value="0">
                            <input type="hidden" name="equivalent[]" value="0">
                            <small class="text-warning"><i class="mdi mdi-information-outline me-1"></i>Non-Inventory / Ready Stock</small>
                        @endif
                    </div>

                    {{-- Qty --}}
                    <div class="col-6 col-md-1">
                        <label class="form-label fw-semibold small">Qty Kirim</label>
                        <input type="number" class="form-control item-qty"
                            id="qty-{{ $no }}" name="qty[]"
                            data-row="{{ $no }}"
                            value="{{ (int) $row['qty'] }}"
                            min="1"
                            max="{{ $row['qty_remaining'] ?? $row['qty'] }}">
                        <small class="text-muted item-stock-info" id="stock-info-{{ $no }}">
                            BDG: {{ $row['default_stock'] }} / BKS: {{ $row['default_wh_stock'] }}
                        </small>
                    </div>

                    {{-- Warehouse --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">Gudang</label>
                        <select class="form-select item-warehouse" name="warehouse[]"
                            id="warehouse-{{ $no }}" data-row="{{ $no }}">
                            <option value="BDG" selected>BDG</option>
                            <option value="BKS">BKS</option>
                        </select>
                    </div>

                    {{-- Harga Satuan --}}
                    @if (!$isLogistic)
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">Harga Satuan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control item-price-label"
                                id="price-label-{{ $no }}" data-row="{{ $no }}"
                                value="{{ number_format($row['price'], 0, ',', '.') }}">
                        </div>
                        <input type="hidden" class="item-price" id="price-{{ $no }}" name="price[]" value="{{ $row['price'] }}">
                    </div>
                    @else
                        <input type="hidden" name="price[]" value="{{ $row['price'] }}">
                    @endif

                    {{-- Subtotal --}}
                    @if (!$isLogistic)
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold small">Subtotal</label>
                        <p class="form-control-plaintext fw-bold text-success mb-0" id="amount-label-{{ $no }}">
                            Rp {{ number_format($row['amount'], 0, ',', '.') }}
                        </p>
                        <input type="hidden" class="item-amount" id="amount-{{ $no }}" name="amount[]" value="{{ $row['amount'] }}">
                    </div>
                    @else
                        <input type="hidden" class="item-amount" id="amount-{{ $no }}" name="amount[]" value="{{ $row['amount'] }}">
                    @endif
                </div>
            </div>
            @endforeach
            </div>

            <hr>

            {{-- Footer --}}
            <div class="row justify-content-end g-3 mb-3">
                <div class="col-12 col-lg-6">
                    <label class="form-label fw-semibold">Catatan / Note</label>
                    <textarea class="form-control" rows="3" name="note"
                        placeholder="Tulis catatan pengiriman...">{{ old('note', '-') }}</textarea>
                </div>
                <div class="col-12 col-lg-6">
                    <label class="form-label fw-semibold">Ongkir / Biaya Pengiriman</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" id="shipping-label"
                            value="{{ number_format($shipping, 0, ',', '.') }}">
                        <input type="hidden" id="shipping" name="shipping" value="{{ $shipping }}">
                    </div>
                    @if (!$isLogistic)
                    <div class="card bg-label-primary border-0">
                        <div class="card-body d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold">Total Keseluruhan</span>
                            <span class="fw-bold fs-5" id="grand-total-label">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @endif
                    <input type="hidden" id="total" name="total" value="{{ $grandTotal }}">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <a href="{{ route('pending-po.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                    <i class="mdi mdi-arrow-left me-1"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="mdi mdi-check-circle me-1"></i>Simpan Barang Keluar
                </button>
            </div>

        </div>
    </div>
</form>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script>
    $(function () {

        function fmtNum(n) {
            return n.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // ─── Select2 AJAX replacement ────────────────────────────────────
        function initReplacementSelect2(el) {
            $(el).select2({
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
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0,
            });

            $(el).on('select2:select', function (e) {
                var row = $(this).data('row');
                var d   = e.params.data;
                $('#stock-info-' + row).text('BDG: ' + (d.stock || 0) + ' / BKS: ' + (d.warehouse_stock || 0));
            });
        }

        $('.select2-replacement').each(function () {
            initReplacementSelect2(this);
        });

        // ─── Shipping ────────────────────────────────────────────────────
        $('#shipping-label').on('keyup', function () {
            var val = fmtNum($(this).val());
            $(this).val(val);
            $('#shipping').val(parseFloat(val.replace(/\./g, '')) || 0);
            recalcTotal();
        });

        // ─── Price ──────────────────────────────────────────────────────
        $(document).on('keyup', '.item-price-label', function () {
            var row = $(this).data('row');
            var val = fmtNum($(this).val());
            $(this).val(val);
            $('#price-' + row).val(parseFloat(val.replace(/\./g, '')) || 0);
            recalcRow(row);
        });

        // ─── Qty ─────────────────────────────────────────────────────────
        $(document).on('change keyup', '.item-qty', function () {
            recalcRow($(this).data('row'));
        });

        function recalcRow(row) {
            var qty    = parseFloat($('#qty-' + row).val()) || 0;
            var price  = parseFloat($('#price-' + row).val()) || 0;
            var amount = qty * price;
            $('#amount-' + row).val(amount);
            $('#amount-label-' + row).text('Rp ' + amount.toLocaleString('id-ID'));
            recalcTotal();
        }

        function recalcTotal() {
            var total = 0;
            $('.item-amount').each(function () { total += parseFloat($(this).val()) || 0; });
            var shipping = parseFloat($('#shipping').val()) || 0;
            var grand    = total + shipping;
            $('#total').val(grand);
            $('#grand-total-label').text('Rp ' + grand.toLocaleString('id-ID'));
        }

        // ─── Delete row ──────────────────────────────────────────────────
        $(document).on('click', '.btn-delete-item', function () {
            var row = $(this).data('row');
            if (confirm('Hapus item #' + row + ' dari daftar?')) {
                $('#item-row-' + row).fadeOut(300, function () {
                    $(this).remove();
                    recalcTotal();
                });
            }
        });
    });
    </script>
@endpush

