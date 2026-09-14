@extends('layouts.sales.app')
@section('title', 'Buat Transfer Gudang Baru')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }
        .form-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            background: #ffffff;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        }
        .route-preview-box {
            background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%);
            border-radius: 12px;
            border: 1px dashed #c9d2e3;
            padding: 16px;
        }
        .item-row {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #e7eaf0;
            padding: 14px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }
        .item-row:hover {
            border-color: #696cff;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.08);
        }
        .select2-container--default .select2-selection--single {
            height: 44px;
            border: 1px solid #d9dee3;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 44px;
            padding-left: 12px;
            font-size: 0.875rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y px-4">
        {{-- Page Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mb-1 font-12">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('change-warehouse.index') }}">Transfer Gudang</a></li>
                        <li class="breadcrumb-item active fw-semibold">Buat Pengiriman Baru</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="mdi mdi-dolly text-primary font-26"></i>
                    Formulir Transfer Stok Antar Gudang
                </h4>
                <p class="text-muted mb-0 font-13">
                    Catat perpindahan fisik stok sparepart antara Gudang Bekasi (BKS) dan Gudang Bandung (BDG).
                </p>
            </div>
            <div>
                <a href="{{ route('change-warehouse.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-xs">
                    <i class="mdi mdi-arrow-left me-1"></i>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        {{-- Errors Alert --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="mdi mdi-alert-circle-outline font-20"></i>
                    <strong>Mohon periksa kembali formulir Anda:</strong>
                </div>
                <ul class="mb-0 font-12 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('change-warehouse.store') }}" method="POST" id="changeWarehouseForm">
            @csrf
            <div class="row g-4">
                {{-- Left Column: Transfer Information --}}
                <div class="col-xl-4 col-lg-5 col-12">
                    <div class="card form-card shadow-sm h-100">
                        <div class="card-header border-bottom py-3">
                            <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="mdi mdi-information-outline text-primary font-18"></i>
                                Informasi &amp; Rute Mutasi
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            {{-- Title --}}
                            <div class="mb-3">
                                <label for="title-input" class="form-label font-12 fw-bold text-uppercase text-muted">
                                    Judul / Keperluan Transfer <span class="text-danger">*</span>
                                </label>
                                <input class="form-control form-control-lg font-13" type="text" id="title-input" name="title"
                                    placeholder="Contoh: Transfer Sparepart Proyek AC..." value="{{ old('title') }}" required>
                                <small class="text-muted font-11">Deskripsi singkat keperluan mutasi barang.</small>
                            </div>

                            {{-- Warehouse Route Destination --}}
                            <div class="mb-3">
                                <label for="info-dropdown" class="form-label font-12 fw-bold text-uppercase text-muted">
                                    Gudang Tujuan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg font-13" id="info-dropdown" name="info" required>
                                    <option value="" disabled {{ old('info') ? '' : 'selected' }}>-- Pilih Gudang Tujuan --</option>
                                    <option value="BDG" {{ old('info') == 'BDG' ? 'selected' : '' }}>Gudang Bandung (BDG)</option>
                                    <option value="BKS" {{ old('info') == 'BKS' ? 'selected' : '' }}>Gudang Bekasi (BKS)</option>
                                </select>
                            </div>

                            {{-- Interactive Visual Route Box --}}
                            <div class="route-preview-box mb-3 text-center">
                                <span class="text-muted font-11 fw-bold text-uppercase d-block mb-2">Simulasi Alur Pengiriman</span>
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <div class="p-2 rounded bg-white shadow-xs text-center border" style="min-width: 90px;">
                                        <span class="font-10 text-muted d-block">Asal</span>
                                        <strong class="font-13 text-primary" id="routeOriginText">
                                            {{ old('info') == 'BDG' ? 'Bekasi (BKS)' : (old('info') == 'BKS' ? 'Bandung (BDG)' : '-') }}
                                        </strong>
                                    </div>
                                    <i class="mdi mdi-arrow-right-bold text-muted font-20"></i>
                                    <div class="p-2 rounded bg-white shadow-xs text-center border" style="min-width: 90px;">
                                        <span class="font-10 text-muted d-block">Tujuan</span>
                                        <strong class="font-13 text-success" id="routeDestinationText">
                                            {{ old('info') == 'BDG' ? 'Bandung (BDG)' : (old('info') == 'BKS' ? 'Bekasi (BKS)' : '-') }}
                                        </strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Courier / Expedition --}}
                            <div class="mb-3">
                                <label for="kurir-input" class="form-label font-12 fw-bold text-uppercase text-muted">
                                    Kurir / Ekspedisi Pengantar
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-truck-fast-outline text-muted"></i></span>
                                    <input class="form-control font-13" type="text" id="kurir-input" name="kurir"
                                        placeholder="Contoh: Lalamove / Driver Kantor / JNE Cargo" value="{{ old('kurir') }}">
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="mb-2">
                                <label for="note-input" class="form-label font-12 fw-bold text-uppercase text-muted">
                                    Catatan Pengiriman (Opsional)
                                </label>
                                <textarea class="form-control font-13" id="note-input" name="note" rows="3"
                                    placeholder="Contoh: Barang fisik telah dicek tersegel baik...">{{ old('note') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Sparepart Item Repeater --}}
                <div class="col-xl-8 col-lg-7 col-12">
                    <div class="card form-card shadow-sm h-100 d-flex flex-column">
                        <div class="card-header border-bottom py-3 d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                                    <i class="mdi mdi-package-variant-closed text-primary font-18"></i>
                                    Daftar Sparepart yang Dipindahkan
                                </h6>
                                <small class="text-muted font-11">Pilih sparepart dari master inventori dan tentukan kuantitas yang dikirim.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary d-flex align-items-center gap-1 waves-effect" id="addItemBtn">
                                <i class="mdi mdi-plus"></i>
                                <span>Tambah Item</span>
                            </button>
                        </div>

                        <div class="card-body p-4 flex-grow-1">
                            <div id="itemsContainer">
                                {{-- Initial Row --}}
                                <div class="item-row" id="row-1">
                                    <div class="row align-items-center g-3">
                                        <div class="col-12 col-md-8">
                                            <label class="form-label font-11 fw-bold text-uppercase text-muted mb-1">Pilih Sparepart / Commodity</label>
                                            <select class="select2-item form-select" name="replacement[]" required data-placeholder="-- Cari sparepart / part number / commodity --">
                                                <option value=""></option>
                                                @foreach ($detProduct as $products)
                                                    @php
                                                        $commodity = optional($products->product)->commodity ?? 'Sparepart';
                                                        $desc = optional($products->product)->detail_desc ?? '';
                                                        $partNo = $products->replacement ?? '-';
                                                        $type = (optional($products->product)->go == 'Genuine') ? 'G' : 'R';
                                                        $stockBdg = (int) ($products->stock ?? 0);
                                                        $stockBks = (int) ($products->warehouse_stock ?? 0);
                                                    @endphp
                                                    <option value="{{ $products->id }}">
                                                        {{ $commodity }} ({{ $desc }}) || Part: {{ $partNo }} [{{ $type }}] — (Stok BDG: {{ $stockBdg }} | BKS: {{ $stockBks }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-8 col-md-3">
                                            <label class="form-label font-11 fw-bold text-uppercase text-muted mb-1">Kuantitas (Qty)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control text-center item-qty-input fw-bold" name="qty[]" min="1" value="1" required>
                                                <span class="input-group-text bg-light font-11">pcs</span>
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-1 text-end pt-3 pt-md-0">
                                            <button type="button" class="btn btn-icon btn-label-danger btn-remove-row" title="Hapus Baris">
                                                <i class="mdi mdi-trash-can-outline font-16"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Summary & Submit --}}
                        <div class="card-footer border-top p-3 bg-light d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="px-3 py-2 bg-white rounded-3 border shadow-xs">
                                    <span class="font-11 text-muted d-block">Total Jenis Item:</span>
                                    <strong class="font-14 text-heading" id="totalVariantsText">1 jenis</strong>
                                </div>
                                <div class="px-3 py-2 bg-white rounded-3 border shadow-xs">
                                    <span class="font-11 text-muted d-block">Total Kuantitas Fisik:</span>
                                    <strong class="font-14 text-primary" id="totalQtyText">1 pcs</strong>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('change-warehouse.index') }}" class="btn btn-outline-secondary px-3">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-1 shadow waves-effect waves-light" id="submitTransferBtn">
                                    <i class="mdi mdi-check-circle-outline font-16"></i>
                                    <span>Simpan &amp; Kirim Transfer</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Template for Javascript cloning --}}
    <template id="rowTemplate">
        <div class="item-row">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-8">
                    <label class="form-label font-11 fw-bold text-uppercase text-muted mb-1">Pilih Sparepart / Commodity</label>
                    <select class="select2-item form-select" name="replacement[]" required data-placeholder="-- Cari sparepart / part number / commodity --">
                        <option value=""></option>
                        @foreach ($detProduct as $products)
                            @php
                                $commodity = optional($products->product)->commodity ?? 'Sparepart';
                                $desc = optional($products->product)->detail_desc ?? '';
                                $partNo = $products->replacement ?? '-';
                                $type = (optional($products->product)->go == 'Genuine') ? 'G' : 'R';
                                $stockBdg = (int) ($products->stock ?? 0);
                                $stockBks = (int) ($products->warehouse_stock ?? 0);
                            @endphp
                            <option value="{{ $products->id }}">
                                {{ $commodity }} ({{ $desc }}) || Part: {{ $partNo }} [{{ $type }}] — (Stok BDG: {{ $stockBdg }} | BKS: {{ $stockBks }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-8 col-md-3">
                    <label class="form-label font-11 fw-bold text-uppercase text-muted mb-1">Kuantitas (Qty)</label>
                    <div class="input-group">
                        <input type="number" class="form-control text-center item-qty-input fw-bold" name="qty[]" min="1" value="1" required>
                        <span class="input-group-text bg-light font-11">pcs</span>
                    </div>
                </div>
                <div class="col-4 col-md-1 text-end pt-3 pt-md-0">
                    <button type="button" class="btn btn-icon btn-label-danger btn-remove-row" title="Hapus Baris">
                        <i class="mdi mdi-trash-can-outline font-16"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            // Function to initialize Select2
            function initSelect2(element) {
                $(element).select2({
                    placeholder: $(element).data('placeholder') || '-- Pilih Sparepart --',
                    allowClear: true,
                    width: '100%'
                });
            }

            // Init initial row Select2
            initSelect2('.select2-item');

            // Route Destination change simulator
            $('#info-dropdown').on('change', function() {
                var destination = $(this).val();
                if (destination === 'BDG') {
                    $('#routeOriginText').text('Bekasi (BKS)').removeClass('text-success').addClass('text-primary');
                    $('#routeDestinationText').text('Bandung (BDG)').removeClass('text-primary').addClass('text-success');
                } else if (destination === 'BKS') {
                    $('#routeOriginText').text('Bandung (BDG)').removeClass('text-success').addClass('text-primary');
                    $('#routeDestinationText').text('Bekasi (BKS)').removeClass('text-primary').addClass('text-success');
                }
            });

            // Calculate Totals
            function calculateTotals() {
                var totalVariants = $('.item-row').length;
                var totalQty = 0;

                $('.item-qty-input').each(function() {
                    var val = parseInt($(this).val()) || 0;
                    totalQty += val;
                });

                $('#totalVariantsText').text(totalVariants + ' jenis');
                $('#totalQtyText').text(totalQty + ' pcs');
            }

            $(document).on('input change', '.item-qty-input', function() {
                calculateTotals();
            });

            // Add Row
            $('#addItemBtn').on('click', function() {
                var template = document.getElementById('rowTemplate');
                var clone = template.content.cloneNode(true);
                $('#itemsContainer').append(clone);

                // Init Select2 on the new row
                var newSelect = $('#itemsContainer .item-row:last-child .select2-item');
                initSelect2(newSelect);

                calculateTotals();
            });

            // Remove Row
            $(document).on('click', '.btn-remove-row', function() {
                if ($('.item-row').length > 1) {
                    $(this).closest('.item-row').remove();
                    calculateTotals();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Transfer gudang minimal harus memiliki 1 barang!',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                }
            });

            // Form Submit Confirmation
            $('#changeWarehouseForm').on('submit', function(e) {
                var form = this;
                var dest = $('#info-dropdown').val();
                if (!dest) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Gudang Tujuan Belum Dipilih',
                        text: 'Silakan pilih gudang tujuan transfer terlebih dahulu.',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    return false;
                }

                var emptySelect = false;
                $('.select2-item').each(function() {
                    if (!$(this).val()) {
                        emptySelect = true;
                    }
                });

                if (emptySelect) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Item Belum Dipilih',
                        text: 'Pastikan seluruh baris barang telah dipilih dengan benar.',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
