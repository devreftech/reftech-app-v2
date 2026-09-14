@extends('layouts.sales.app')
@section('title', 'Tambah Koreksi Persediaan (Inventory Adjustment)')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        .form-section-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #566a7f;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed #d9dee3;
        }
        .repeater-card {
            background: #fafbfc;
            border: 1px solid #edf0f2;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
            position: relative;
            transition: all 0.2s ease;
        }
        .repeater-card:hover {
            border-color: #696cff;
            background: #fff;
            box-shadow: 0 4px 14px rgba(105, 108, 255, 0.08);
        }
        .btn-remove-item {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
        .summary-card-sticky {
            position: sticky;
            top: 85px;
        }
        .say-amount-box {
            background: #f4f5fa;
            border-left: 4px solid var(--bs-primary);
            border-radius: 8px;
            padding: 12px 16px;
        }
        .stock-info-pill {
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 4px;
            background: #eef2ff;
            color: #435ebe;
            display: inline-block;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / <a href="{{ route('expense-inventory.index') }}" class="text-muted">Inventory Adjustment</a> /</span> Tambah Koreksi Persediaan
            </h4>
            <p class="text-muted mb-0">Catat penyesuaian nilai dan stok fisik persediaan barang/spare part secara terintegrasi.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('expense-inventory.index') }}" class="btn btn-outline-secondary waves-effect">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" form="inventoryAdjForm" class="btn btn-primary waves-effect waves-light shadow-sm">
                <i class="mdi mdi-content-save-check me-1"></i> Simpan Penyesuaian
            </button>
        </div>
    </div>

    <!-- Main Form -->
    <form id="inventoryAdjForm" action="{{ route('expense-inventory.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Left Column: Inputs & Repeater -->
            <div class="col-xl-8 col-lg-7 col-12">
                <!-- Section 1: Dokumen Koreksi & Akun Terkait -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-section-title">
                            <i class="mdi mdi-clipboard-text-outline text-primary fs-5"></i>
                            <span>1. Dokumen Berita Acara & Akun Akuntansi</span>
                        </div>

                        <div class="row g-3">
                            <!-- Tanggal Koreksi -->
                            <div class="col-md-6 col-12">
                                <label for="adj_date" class="form-label fw-semibold">Tanggal Penyesuaian <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" id="adj_date" name="date" 
                                       value="{{ old('date', date('Y-m-d')) }}" required>
                            </div>

                            <!-- No. Invoice / Berita Acara -->
                            <div class="col-md-6 col-12">
                                <label for="no-voucher-input" class="form-label fw-semibold">No. Berita Acara / No. Dokumen</label>
                                <input class="form-control font-monospace" type="text" placeholder="Contoh: ADJ/2026/09/XXX"
                                    id="no-voucher-input" name="no_invoice" value="{{ old('no_invoice') }}">
                                <small class="text-muted">Nomor dokumen stock opname atau memo koreksi fisik.</small>
                            </div>

                            <!-- Akun Akuntansi (COA) -->
                            <div class="col-md-6 col-12">
                                <label for="account-select" class="form-label fw-semibold">Akun Beban / Selisih Persediaan (COA) <span class="text-danger">*</span></label>
                                <select id="account-select" class="select2 form-select" name="account" required>
                                    <option value="">-- Pilih Akun Penyesuaian --</option>
                                    @foreach ($account as $acc)
                                        <option value="{{ $acc->id }}" data-memo="{{ $acc->category }}"
                                            {{ old('account') == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->code }} - {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Akun penampung beban selisih persediaan (Laba/Rugi).</small>
                            </div>

                            <!-- Memo / Alasan Penyesuaian -->
                            <div class="col-md-6 col-12">
                                <label for="memo-input" class="form-label fw-semibold">Alasan Penyesuaian (Memo) <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" placeholder="Contoh: Selisih Stock Opname Q3 / Barang Rusak..."
                                    id="memo-input" name="detail" value="{{ old('detail') }}" required>
                                <small class="text-muted">Ringkasan keterangan penyebab penyesuaian.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Daftar Item Barang Yang Disesuaikan (Repeater) -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-package-variant-closed text-primary fs-5"></i>
                            <h5 class="card-title mb-0 fw-bold">2. Daftar Barang Penyesuaian (Inventory Items)</h5>
                        </div>
                        <span class="badge bg-label-primary px-3 py-1" id="items-count-badge">1 Item Barang</span>
                    </div>
                    <div class="card-body pt-4">
                        <div class="form-invoice-repeater source-item">
                            <div data-repeater-list="group-a" id="repeater-container">
                                <div data-repeater-item class="repeater-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-label-secondary font-monospace row-number-badge">Item #1</span>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-item" data-repeater-delete="" title="Hapus baris ini">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </div>

                                    <!-- Baris 1: Pemilihan Produk -->
                                    <div class="row g-3 mb-3">
                                        <!-- Komoditas / Equivalent -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label small fw-semibold">Pilih Komoditas / Part Number <span class="text-danger">*</span></label>
                                            <select class="form-select select2 invoice-item-equivalent" name="equivalent[]" required>
                                                <option value="">-- Pilih Komoditas / PN --</option>
                                                @foreach ($product as $prod)
                                                    <option value="{{ $prod->id }}" data-commodity="{{ $prod->id_product }}">
                                                        {{ $prod->pn }} | {{ $prod->product->commodity ?? '-' }} ({{ ($prod->product->go ?? '') == 'Genuine' ? 'G' : 'R' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Replacement / Spesifikasi Part -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label small fw-semibold">Pilih Replacement / Stok Fisik <span class="text-danger">*</span></label>
                                            <select class="form-select select2 invoice-item-replacement" name="replacement[]" disabled required>
                                                <option value="">-- Pilih Komoditas Dahulu --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Baris 2: Detail Qty, Gudang, Harga Modal, Subtotal -->
                                    <div class="row g-3">
                                        <!-- Gudang -->
                                        <div class="col-md-3 col-6">
                                            <label class="form-label small fw-semibold">Gudang <span class="text-danger">*</span></label>
                                            <select class="form-select invoice-item-warehouse" name="warehouse[]" required>
                                                <option value="BDG">Gudang BDG</option>
                                                <option value="BKS">Gudang BKS</option>
                                            </select>
                                        </div>

                                        <!-- Qty -->
                                        <div class="col-md-3 col-6">
                                            <label class="form-label small fw-semibold">Qty Kurang <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control invoice-item-qty" name="qty[]" 
                                                   min="1" placeholder="1" required disabled>
                                            <div class="mt-1">
                                                <span class="stock-info-pill info-max-label">Maks: -</span>
                                            </div>
                                        </div>

                                        <!-- Harga Modal Satuan (Formatted) -->
                                        <div class="col-md-3 col-12">
                                            <label class="form-label small fw-semibold">Harga Modal Satuan</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text small">Rp</span>
                                                <input type="text" class="form-control invoice-item-price-label bg-light" 
                                                       placeholder="0" readonly>
                                            </div>
                                            <input type="hidden" class="invoice-item-price" name="price[]" value="0">
                                        </div>

                                        <!-- Subtotal Nilai Penyesuaian -->
                                        <div class="col-md-3 col-12">
                                            <label class="form-label small fw-semibold">Nilai Penyesuaian</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text small fw-bold">Rp</span>
                                                <input type="text" class="form-control fw-bold text-primary bg-light invoice-item-amount-display" 
                                                       placeholder="0" readonly>
                                            </div>
                                            <input type="hidden" class="invoice-item-amount" name="amount[]" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary waves-effect mt-2 btn-add-row" data-repeater-create="">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Barang Penyesuaian
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Live Summary & Actions -->
            <div class="col-xl-4 col-lg-5 col-12">
                <div class="summary-card-sticky">
                    <!-- Summary Card -->
                    <div class="card shadow-sm mb-4 border-start border-4 border-primary">
                        <div class="card-header border-bottom py-3">
                            <h6 class="card-title mb-0 fw-bold">
                                <i class="mdi mdi-calculator text-primary me-2"></i>Total Nilai Penyesuaian
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Nilai Koreksi</span>
                            <h2 class="fw-bold text-primary mb-3" id="display-total">Rp 0</h2>
                            <input type="hidden" name="total" id="hidden-total" value="0">

                            <!-- Terbilang Box -->
                            <div class="say-amount-box mb-3">
                                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Terbilang (Say Amount)</span>
                                <div class="fw-bold text-dark fst-italic invoice-item-say-total" style="font-size: 0.9rem;">
                                    # Nol Rupiah #
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-2 waves-effect waves-light shadow-sm">
                                <i class="mdi mdi-content-save-check me-1"></i> Simpan Penyesuaian
                            </button>
                            <a href="{{ route('expense-inventory.index') }}" class="btn btn-outline-secondary w-100 waves-effect">
                                Batal
                            </a>
                        </div>
                    </div>

                    <!-- Information Guide Card -->
                    <div class="card shadow-sm border-0 bg-light">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-2 text-primary">
                                <i class="mdi mdi-information-outline fs-5"></i>
                                <h6 class="fw-bold mb-0 text-primary">Dampak Koreksi Persediaan</h6>
                            </div>
                            <small class="text-muted d-block">
                                Jumlah kuantitas yang disesuaikan akan secara otomatis mengurangi saldo fisik persediaan di gudang terpilih (BDG/BKS) serta membukukan beban penyesuaian persediaan ke buku besar.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/includes/repeater/jquery-repeater-invoice.js') }}"></script>
    <script>
        $(document).ready(function() {
            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function terbilang(n) {
                const angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
                n = parseInt(n, 10);
                if (isNaN(n) || n === 0) return "";
                if (n < 12) return angka[n];
                if (n < 20) return terbilang(n - 10) + " belas";
                if (n < 100) return terbilang(Math.floor(n / 10)) + " puluh " + terbilang(n % 10);
                if (n < 200) return "seratus " + terbilang(n - 100);
                if (n < 1000) return terbilang(Math.floor(n / 100)) + " ratus " + terbilang(n % 100);
                if (n < 2000) return "seribu " + terbilang(n - 1000);
                if (n < 1000000) return terbilang(Math.floor(n / 1000)) + " ribu " + terbilang(n % 1000);
                if (n < 1000000000) return terbilang(Math.floor(n / 1000000)) + " juta " + terbilang(n % 1000000);
                if (n < 1000000000000) return terbilang(Math.floor(n / 1000000000)) + " miliar " + terbilang(n % 1000000000);
                return "";
            }

            function initSelect2(context) {
                context = context || $(document);
                context.find('.select2').each(function() {
                    if (!$(this).hasClass("select2-hidden-accessible")) {
                        $(this).select2({
                            width: '100%',
                            allowClear: true
                        });
                    }
                });
            }
            initSelect2();

            // Update row counters
            function updateRowCounters() {
                var count = $('.repeater-card').length;
                $('#items-count-badge').text(count + (count === 1 ? ' Item Barang' : ' Item Barang'));
                $('.repeater-card').each(function(idx) {
                    $(this).find('.row-number-badge').text('Item #' + (idx + 1));
                });
            }

            // Calculate totals
            function updateCalculations() {
                var grandTotal = 0;
                $('.invoice-item-amount').each(function() {
                    var val = parseFloat($(this).val()) || 0;
                    grandTotal += val;
                });

                $('#display-total').text('Rp ' + formatNumber(grandTotal.toString()));
                $('#hidden-total').val(grandTotal);

                var tb = terbilang(grandTotal);
                if (tb) {
                    $('.invoice-item-say-total').text('# ' + tb.charAt(0).toUpperCase() + tb.slice(1) + ' Rupiah #');
                } else {
                    $('.invoice-item-say-total').text('# Nol Rupiah #');
                }
            }

            // Recalculate row amount (Qty * Price)
            function calculateRowAmount(card) {
                var qty = parseFloat(card.find('.invoice-item-qty').val()) || 0;
                var price = parseFloat(card.find('.invoice-item-price').val()) || 0;
                var rowAmount = qty * price;

                card.find('.invoice-item-amount').val(rowAmount);
                card.find('.invoice-item-amount-display').val(rowAmount ? formatNumber(rowAmount.toString()) : '0');
                updateCalculations();
            }

            // When Commodity changes: fetch replacements via AJAX
            $(document).on('change', '.invoice-item-equivalent', function() {
                var card = $(this).closest('.repeater-card');
                var commodity = $(this).find(':selected').data('commodity');
                var replacementDropdown = card.find('.invoice-item-replacement');

                replacementDropdown.empty().append('<option value="">Memuat replacement...</option>').prop('disabled', true);
                card.find('.invoice-item-qty').val('').prop('disabled', true);
                card.find('.info-max-label').text('Maks: -');
                card.find('.invoice-item-price-label').val('0');
                card.find('.invoice-item-price').val('0');
                calculateRowAmount(card);

                if (!commodity) {
                    replacementDropdown.empty().append('<option value="">-- Pilih Komoditas Dahulu --</option>');
                    return;
                }

                $.ajax({
                    url: '/product-in/replacement/' + commodity,
                    type: 'GET',
                    success: function(response) {
                        replacementDropdown.empty().append('<option value="">-- Pilih Replacement --</option>');
                        if (response && response.length > 0) {
                            $.each(response, function(k, v) {
                                replacementDropdown.append(
                                    '<option value="' + v.id + '" data-modal="' + (v.modal || 0) + '" data-stock="' + (v.stock || 0) + '" data-wstock="' + (v.warehouse_stock || 0) + '">' +
                                    (v.replacement || '-') + ' (BDG: ' + (v.stock || 0) + ' | BKS: ' + (v.warehouse_stock || 0) + ')' +
                                    '</option>'
                                );
                            });
                            replacementDropdown.prop('disabled', false);
                            replacementDropdown.trigger('change.select2');
                        } else {
                            replacementDropdown.append('<option value="">Tidak ada data replacement</option>');
                        }
                    },
                    error: function() {
                        replacementDropdown.empty().append('<option value="">Gagal memuat replacement</option>');
                    }
                });
            });

            // When Replacement changes: set modal price and max stock
            $(document).on('change', '.invoice-item-replacement', function() {
                var card = $(this).closest('.repeater-card');
                var selected = $(this).find(':selected');
                var replacementId = selected.val();

                if (!replacementId) return;

                var warehouse = card.find('.invoice-item-warehouse').val();
                var stockBdg = parseFloat(selected.data('stock')) || 0;
                var stockBks = parseFloat(selected.data('wstock')) || 0;
                var modal = parseFloat(selected.data('modal')) || 0;

                var activeStock = warehouse === 'BDG' ? stockBdg : stockBks;

                card.find('.info-max-label').text('Maks Stok (' + warehouse + '): ' + activeStock + ' unit');
                card.find('.invoice-item-price').val(modal);
                card.find('.invoice-item-price-label').val(modal ? formatNumber(modal.toString()) : '0');

                var qtyInput = card.find('.invoice-item-qty');
                if (activeStock > 0) {
                    qtyInput.prop('disabled', false).attr('max', activeStock).val(1);
                } else {
                    qtyInput.prop('disabled', false).attr('max', activeStock).val(0);
                }

                calculateRowAmount(card);
            });

            // When Warehouse changes: update max stock hint
            $(document).on('change', '.invoice-item-warehouse', function() {
                var card = $(this).closest('.repeater-card');
                var replacementSelect = card.find('.invoice-item-replacement option:selected');
                if (replacementSelect.val()) {
                    var warehouse = $(this).val();
                    var stockBdg = parseFloat(replacementSelect.data('stock')) || 0;
                    var stockBks = parseFloat(replacementSelect.data('wstock')) || 0;
                    var activeStock = warehouse === 'BDG' ? stockBdg : stockBks;

                    card.find('.info-max-label').text('Maks Stok (' + warehouse + '): ' + activeStock + ' unit');
                    card.find('.invoice-item-qty').attr('max', activeStock);
                }
            });

            // When Qty changes
            $(document).on('input keyup change', '.invoice-item-qty', function() {
                var card = $(this).closest('.repeater-card');
                calculateRowAmount(card);
            });

            // Initialize jQuery Repeater
            $('.form-invoice-repeater').repeater({
                show: function() {
                    $(this).slideDown();
                    $(this).find('.select2-container').remove();
                    $(this).find('.select2').removeClass('select2-hidden-accessible');
                    initSelect2($(this));
                    updateRowCounters();
                    updateCalculations();
                },
                hide: function(deleteElement) {
                    if ($('.repeater-card').length > 1) {
                        $(this).slideUp(deleteElement);
                        setTimeout(function() {
                            updateRowCounters();
                            updateCalculations();
                        }, 400);
                    } else {
                        alert('Minimal harus ada satu baris barang penyesuaian.');
                    }
                }
            });

            updateRowCounters();
            updateCalculations();
        });
    </script>
@endpush
