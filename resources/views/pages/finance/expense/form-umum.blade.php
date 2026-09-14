@extends('layouts.sales.app')
@section('title', 'Tambah Jurnal Umum (Non-Kas)')

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
            margin-bottom: 1rem;
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
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / <a href="{{ route('expense-umum.index') }}" class="text-muted">Jurnal Umum</a> /</span> Buat Jurnal Umum Baru
            </h4>
            <p class="text-muted mb-0">Catat transaksi memorial atau penyesuaian alokasi pos akun non-kas secara terstruktur.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('expense-umum.index') }}" class="btn btn-outline-secondary waves-effect">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <button type="submit" form="journalForm" class="btn btn-primary waves-effect waves-light shadow-sm">
                <i class="mdi mdi-content-save-check me-1"></i> Simpan Jurnal Umum
            </button>
        </div>
    </div>

    <!-- Main Form -->
    <form id="journalForm" action="{{ route('expense.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Left Column: Inputs & Repeater -->
            <div class="col-xl-8 col-lg-7 col-12">
                <!-- Section 1: Detail Jurnal Transaksi -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-section-title">
                            <i class="mdi mdi-file-document-edit-outline text-primary fs-5"></i>
                            <span>1. Informasi & Referensi Jurnal</span>
                        </div>

                        <div class="row g-3">
                            <!-- Tanggal Jurnal -->
                            <div class="col-md-6 col-12">
                                <label for="journal_date" class="form-label fw-semibold">Tanggal Transaksi Jurnal <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" id="journal_date" name="date" 
                                       value="{{ old('date', date('Y-m-d')) }}" required>
                            </div>

                            <!-- No. Referensi / Bukti Memorial -->
                            <div class="col-md-6 col-12">
                                <label for="no-voucher-input" class="form-label fw-semibold">No. Bukti Memorial / No. Invoice</label>
                                <input class="form-control font-monospace" type="text" placeholder="Contoh: MEM/2026/09/001"
                                    id="no-voucher-input" name="no_invoice" value="{{ old('no_invoice') }}">
                                <small class="text-muted">Nomor surat bukti transaksi atau dokumen rujukan internal.</small>
                            </div>

                            <!-- No. Cek / Referensi Dokumen -->
                            <div class="col-md-6 col-12">
                                <label for="no-cheque-input" class="form-label fw-semibold">No. Dokumen Terkait (Opsional)</label>
                                <input class="form-control" type="text" placeholder="Contoh: REF-999"
                                    id="no-cheque-input" name="no_cheque" value="{{ old('no_cheque') }}">
                            </div>

                            <!-- Keterangan / Memo Jurnal -->
                            <div class="col-md-6 col-12">
                                <label for="memo-input" class="form-label fw-semibold">Keterangan / Memo Jurnal <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" placeholder="Contoh: Penyesuaian Saldo Awal / Alokasi Biaya Antar Divisi..." 
                                    id="memo-input" name="detail" value="{{ old('detail') }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Baris Alokasi Akun Jurnal (Repeater) -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-format-list-bulleted text-primary fs-5"></i>
                            <h5 class="card-title mb-0 fw-bold">2. Alokasi Baris Akun (Journal Items)</h5>
                        </div>
                        <span class="badge bg-label-primary px-3 py-1" id="items-count-badge">1 Baris Alokasi</span>
                    </div>
                    <div class="card-body pt-4">
                        <div class="form-invoice-repeater source-item">
                            <div data-repeater-list="group-a" id="repeater-container">
                                <div data-repeater-item class="repeater-card">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-label-secondary font-monospace row-number-badge">Baris #1</span>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-remove-item" data-repeater-delete="" title="Hapus baris ini">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </div>

                                    <div class="row g-3">
                                        <!-- Akun -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label small fw-semibold">Akun (Chart of Accounts) <span class="text-danger">*</span></label>
                                            <select class="form-select select2 invoice-item-account" name="account[]" required>
                                                <option value="">-- Pilih Akun --</option>
                                                @foreach ($account as $acc)
                                                    <option value="{{ $acc->id }}" data-memo="{{ $acc->category }}">
                                                        {{ $acc->code }} - {{ $acc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Kategori / Memo Otomatis -->
                                        <div class="col-md-6 col-12">
                                            <label class="form-label small fw-semibold">Klasifikasi Akun</label>
                                            <input type="text" class="form-control invoice-item-memo-label bg-light" 
                                                   placeholder="Otomatis mengikuti akun terpilih" readonly>
                                            <input type="hidden" class="invoice-item-memo" name="memo[]">
                                        </div>

                                        <!-- Nominal Transaksi (Formatted) -->
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Nominal Alokasi (Rp) <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text fw-bold">Rp</span>
                                                <input type="text" class="form-control form-control-lg fw-bold invoice-item-amount-label text-dark" 
                                                       placeholder="0" name="harga" required>
                                            </div>
                                            <input type="hidden" class="invoice-item-amount" name="amount[]" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-outline-primary waves-effect mt-2 btn-add-row" data-repeater-create="">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Baris Akun
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
                                <i class="mdi mdi-calculator text-primary me-2"></i>Total Nilai Jurnal
                            </h6>
                        </div>
                        <div class="card-body pt-3">
                            <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Total Akumulasi</span>
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
                                <i class="mdi mdi-content-save-check me-1"></i> Simpan Jurnal Umum
                            </button>
                            <a href="{{ route('expense-umum.index') }}" class="btn btn-outline-secondary w-100 waves-effect">
                                Batal
                            </a>
                        </div>
                    </div>

                    <!-- Information Guide Card -->
                    <div class="card shadow-sm border-0 bg-light">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-2 text-primary">
                                <i class="mdi mdi-information-outline fs-5"></i>
                                <h6 class="fw-bold mb-0 text-primary">Informasi Jurnal Umum</h6>
                            </div>
                            <small class="text-muted d-block">
                                Modul ini digunakan untuk mencatat pengalokasian pos keuangan atau transaksi memorial tanpa langsung mendebit/mengkredit rekening kas fisik bank.
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

            // Update row numbers & items count
            function updateRowCounters() {
                var count = $('.repeater-card').length;
                $('#items-count-badge').text(count + (count === 1 ? ' Baris Alokasi' : ' Baris Alokasi'));
                $('.repeater-card').each(function(idx) {
                    $(this).find('.row-number-badge').text('Baris #' + (idx + 1));
                });
            }

            // Calculate total & terbilang
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

            // Sync account category memo
            $(document).on('change', '.invoice-item-account', function() {
                var memo = $(this).find(':selected').data('memo') || '';
                var card = $(this).closest('.repeater-card');
                card.find('.invoice-item-memo-label').val(memo);
                card.find('.invoice-item-memo').val(memo);
            });

            // Amount input formatting
            $(document).on('input keyup change', '.invoice-item-amount-label', function() {
                var input = $(this);
                var val = input.val().replace(/\D/g, "");
                var num = parseFloat(val) || 0;
                
                input.val(val ? formatNumber(val) : "");
                input.closest('.repeater-card').find('.invoice-item-amount').val(num);
                updateCalculations();
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
                        alert('Minimal harus ada satu baris alokasi akun.');
                    }
                }
            });

            updateRowCounters();
            updateCalculations();
        });
    </script>
@endpush
