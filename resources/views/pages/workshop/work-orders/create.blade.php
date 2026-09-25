@extends('layouts.sales.app')
@section('title', 'Buat Work Order Spare Part Mesin - Reftech')

@push('after-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border-color: #dbdade;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select-machine-smart + .select2-container .select2-selection--single {
        height: 48px !important;
    }
    .select-machine-smart + .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 46px;
    }
    .select-machine-smart + .select2-container .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }
    .select2-dropdown {
        z-index: 9999 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3 flex-grow-1">
    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 fs-6">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('work-orders.index') }}">Work Order</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Buat Baru</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-wrench-plus text-primary"></i> Buat Pengajuan Work Order
            </h4>
            <p class="text-muted mb-0 small">
                Pengajuan pergantian spare part untuk mesin operasional / kompresor Fixed Asset.
            </p>
        </div>

        <div>
            <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary btn-sm shadow-xs">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading fw-bold mb-1"><i class="mdi mdi-alert-circle me-1"></i> Terjadi Kesalahan Input:</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('work-orders.store') }}" method="POST" id="formWorkOrder">
        @csrf

        <div class="row g-4">
            {{-- Kolom Kiri: Informasi Mesin & WO --}}
            <div class="col-lg-4 col-12">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="mdi mdi-cog-box text-primary"></i> 1. Unit Mesin (Asset)
                        </h6>
                    </div>
                    <div class="card-body pt-3">
                        {{-- Nomor WO (Auto) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase">Nomor Work Order</label>
                            <input type="text" class="form-control bg-light" value="{{ $suggestedNoWo }}" readonly>
                            <small class="text-muted" style="font-size: 11px;">Digenerate otomatis oleh sistem</small>
                        </div>

                        {{-- Pilih Mesin (Smart Selection kaya Smart Quotation) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase required">Pilih Unit Mesin</label>
                            <div class="form-floating form-floating-outline mb-1">
                                <select class="form-select select-machine-smart" name="id_fixed_asset" id="selectMachine" required style="width: 100%;">
                                    <option value="">---- Choose Machine Unit / Part Here ----</option>
                                    @foreach ($fixedAssets as $fa)
                                        @php
                                            $faBrand = $fa->unit_brand ?: ($fa->desc ?: 'Mesin');
                                            $faModel = $fa->unit_model ?: '-';
                                            $faSn = $fa->serial_number ?: '-';
                                            $faKondisi = $fa->kondisi ?: ($fa->status_unit ?? 'OK');
                                            $faLokasi = $fa->lokasi ?: 'Gudang Workshop';
                                        @endphp
                                        <option value="{{ $fa->id }}"
                                            data-code="{{ $fa->code }}"
                                            data-brand="{{ $faBrand }}"
                                            data-model="{{ $faModel }}"
                                            data-sn="{{ $faSn }}"
                                            data-kondisi="{{ $faKondisi }}"
                                            data-lokasi="{{ $faLokasi }}"
                                            data-desc="{{ $fa->desc }}"
                                            {{ old('id_fixed_asset') == $fa->id ? 'selected' : '' }}>
                                            {{ $fa->code }} — {{ $faBrand }} {{ $faModel }} (SN: {{ $faSn }}) || {{ $faLokasi }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="selectMachine">Unit Mesin (Fixed Asset)</label>
                            </div>
                            <small class="text-muted" style="font-size: 11px;">Cari berdasarkan Kode Aset (FA-...), Brand, Model, atau Serial Number Mesin</small>
                        </div>

                        {{-- Machine Preview Box --}}
                        <div class="p-3 rounded-3 bg-light border mb-3 d-none" id="machinePreviewBox">
                            <div class="d-flex align-items-center gap-2 mb-2 pb-2 border-bottom">
                                <i class="mdi mdi-engine text-primary fs-5"></i>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark" id="previewCode">-</h6>
                                    <small class="text-muted" id="previewDesc">-</small>
                                </div>
                            </div>
                            <div class="row g-2 small">
                                <div class="col-6">
                                    <span class="text-muted d-block" style="font-size: 11px;">Brand / Model:</span>
                                    <span class="fw-semibold text-dark" id="previewBrandModel">-</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block" style="font-size: 11px;">Serial Number:</span>
                                    <span class="fw-semibold text-dark" id="previewSn">-</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block" style="font-size: 11px;">Kondisi:</span>
                                    <span class="badge bg-label-info" id="previewKondisi">-</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block" style="font-size: 11px;">Lokasi:</span>
                                    <span class="fw-semibold text-dark" id="previewLocation">-</span>
                                </div>
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold small text-uppercase required">Tgl Pengajuan</label>
                                <input type="date" class="form-control" name="date" value="{{ old('date', now()->toDateString()) }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small text-uppercase">Est. Pekerjaan</label>
                                <input type="date" class="form-control" name="target_date" value="{{ old('target_date') }}">
                            </div>
                        </div>

                        {{-- Teknisi PIC --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase">Teknisi Pelaksana (PIC)</label>
                            <select class="form-select select2" name="id_user_technician">
                                <option value="">-- Pilih Teknisi (Opsional) --</option>
                                @foreach ($technicians as $tech)
                                    <option value="{{ $tech->id }}" {{ old('id_user_technician') == $tech->id ? 'selected' : '' }}>
                                        {{ $tech->name }} ({{ $tech->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Deskripsi Keluhan / Kebutuhan Servis --}}
                        <div class="mb-0">
                            <label class="form-label fw-semibold small text-uppercase required">Deskripsi Kebutuhan / Keluhan</label>
                            <textarea class="form-control" name="description" rows="4" placeholder="Jelaskan alasan pergantian spare part, kondisi kerusakan mesin, atau instruksi servis..." required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Pemilihan Spare Part (Input Manual oleh ServiceM) --}}
            <div class="col-lg-8 col-12">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                                <i class="mdi mdi-toolbox-outline text-primary"></i> 2. Daftar Spare Part yang Dibutuhkan
                            </h6>
                            <small class="text-muted">Tuliskan nama/deskripsi spare part yang dibutuhkan dan kuantitinya.</small>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm px-3 shadow-xs" id="btnAddRow" style="cursor: pointer; position: relative; z-index: 5;">
                            <i class="mdi mdi-plus me-1"></i> Tambah Item
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="itemsTable">
                                <thead class="table-light text-uppercase" style="font-size: 0.78rem;">
                                    <tr>
                                        <th style="min-width: 280px;">Nama / Deskripsi Spare Part <span class="text-danger">*</span></th>
                                        <th style="width: 120px;">Qty <span class="text-danger">*</span></th>
                                        <th style="width: 130px;">Satuan</th>
                                        <th style="min-width: 180px;">Catatan / Posisi Part</th>
                                        <th style="width: 40px;" class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsBody">
                                    {{-- Row template diinject via JavaScript --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Total Summary Box --}}
                        <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-semibold text-muted">Total Jenis Part:</span>
                                <span class="badge bg-label-primary fs-6 ms-1" id="totalItemsCount">0 Item</span>
                            </div>
                            <div class="text-end">
                                <span class="text-muted small d-block">Total Kuantiti:</span>
                                <h5 class="fw-bolder text-primary mb-0" id="totalQtyLabel">0 Unit</h5>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top py-3 d-flex justify-content-end gap-2">
                        <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSubmitWo">
                            <i class="mdi mdi-check-circle me-1"></i> Ajukan Work Order ke Gudang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    let rowIndex = 0;

    // Initialize Smart Select2 for Unit Mesin (Persis kaya selection part di Smart Quotation)
    $('#selectMachine').select2({
        width: '100%',
        placeholder: '---- Choose Machine Unit / Part Here ----',
        allowClear: true,
        templateResult: formatMachineOption,
        templateSelection: formatMachineSelection,
        escapeMarkup: function(m) { return m; }
    }).on('select2:select change', function(e) {
        let opt = $(this).find('option:selected');
        let code = opt.data('code') || '';
        let desc = opt.data('desc') || '';
        let brand = opt.data('brand') || '';
        let model = opt.data('model') || '';
        let sn = opt.data('sn') || '-';
        let kondisi = opt.data('kondisi') || 'OK';
        let lokasi = opt.data('lokasi') || 'Gudang Workshop';

        if (code || brand) {
            $('#previewCode').text(code || '-');
            $('#previewDesc').text(desc || '-');
            $('#previewBrandModel').text((brand || '-') + (model && model !== '-' ? ' / ' + model : ''));
            $('#previewSn').text(sn || '-');
            $('#previewKondisi').text(kondisi || 'OK');
            $('#previewLocation').text(lokasi || 'Gudang Workshop');
            $('#machinePreviewBox').removeClass('d-none');
        } else {
            $('#machinePreviewBox').addClass('d-none');
        }
    }).on('select2:clear', function() {
        $('#machinePreviewBox').addClass('d-none');
    });

    function formatMachineOption(m) {
        if (!m.id) {
            return m.text;
        }
        let $el = $(m.element);
        let code = $el.data('code') || '';
        let brand = $el.data('brand') || '';
        let model = $el.data('model') || '';
        let sn = $el.data('sn') || '-';
        let kondisi = $el.data('kondisi') || 'OK';
        let lokasi = $el.data('lokasi') || 'Gudang Workshop';

        let badgeClass = 'bg-label-info';
        if (kondisi === 'OK' || kondisi === 'Baru' || kondisi === 'Active') badgeClass = 'bg-label-success';
        if (kondisi === 'Rental') badgeClass = 'bg-label-primary';
        if (kondisi === 'Service' || kondisi === 'Breakdown' || kondisi === 'Rusak' || kondisi === 'Second') badgeClass = 'bg-label-warning';

        return $(
            '<div class="d-flex flex-column py-1 border-bottom">' +
                '<div class="d-flex align-items-center justify-content-between mb-1">' +
                    '<span class="fw-bold text-primary font-monospace" style="font-size: 0.88rem;">' +
                        '<i class="mdi mdi-engine me-1"></i>' + code +
                    '</span>' +
                    '<span class="badge ' + badgeClass + '" style="font-size: 10px;">' + kondisi + '</span>' +
                '</div>' +
                '<div class="small text-dark fw-semibold">' + brand + (model && model !== '-' ? ' ' + model : '') + '</div>' +
                '<div class="d-flex align-items-center gap-3 text-muted mt-1" style="font-size: 11px;">' +
                    '<span><i class="mdi mdi-barcode me-1 text-secondary"></i>SN: <strong>' + sn + '</strong></span>' +
                    '<span><i class="mdi mdi-map-marker-outline me-1 text-secondary"></i>' + lokasi + '</span>' +
                '</div>' +
            '</div>'
        );
    }

    function formatMachineSelection(m) {
        if (!m.id) {
            return m.text;
        }
        let $el = $(m.element);
        let code = $el.data('code') || '';
        let brand = $el.data('brand') || '';
        let model = $el.data('model') || '';
        let sn = $el.data('sn') || '-';

        if (!code && !brand) return m.text;
        return $(
            '<span><strong class="text-primary font-monospace">' + code + '</strong> — ' + brand + (model && model !== '-' ? ' ' + model : '') + ' <span class="text-muted">(SN: ' + sn + ')</span></span>'
        );
    }

    // Add Item Row (Manual Input)
    function addRow() {
        try {
            rowIndex++;
            let tr = `
                <tr id="row-${rowIndex}" class="item-row">
                    <td>
                        <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][item_name]" placeholder="Nama part, merk, nomor seri/part..." required>
                    </td>
                    <td>
                        <input type="number" step="1" min="1" class="form-control form-control-sm input-qty text-center" name="items[${rowIndex}][qty_requested]" value="1" required>
                    </td>
                    <td>
                        <select class="form-select form-select-sm" name="items[${rowIndex}][unit]">
                            <option value="Pcs" selected>Pcs</option>
                            <option value="Set">Set</option>
                            <option value="Unit">Unit</option>
                            <option value="Liter">Liter</option>
                            <option value="Roll">Roll</option>
                            <option value="Meter">Meter</option>
                            <option value="Pck">Pck</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="items[${rowIndex}][note]" placeholder="Posisi part / keterangan (opsional)...">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-row rounded-pill shadow-none" title="Hapus">
                            <i class="mdi mdi-delete-outline"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#itemsBody').append(tr);
            updateTotals();
        } catch (err) {
            console.error('Error adding new row:', err);
        }
    }

    function updateTotals() {
        let count = 0;
        let totalQty = 0;
        $('.item-row').each(function() {
            count++;
            let q = parseFloat($(this).find('.input-qty').val()) || 0;
            totalQty += q;
        });
        $('#totalItemsCount').text(count + ' Item');
        $('#totalQtyLabel').text(totalQty + ' Qty');
    }

    // Add initial row on load
    addRow();

    // Event listener for adding new row
    $(document).on('click', '#btnAddRow', function(e) {
        e.preventDefault();
        addRow();
    });

    // Update qty on input
    $(document).on('input', '.input-qty', function() {
        updateTotals();
    });

    // Remove row
    $(document).on('click', '.btn-remove-row', function(e) {
        e.preventDefault();
        if ($('.item-row').length <= 1) {
            Swal.fire('Info', 'Minimal harus ada 1 item spare part.', 'info');
            return;
        }
        $(this).closest('tr').remove();
        updateTotals();
    });

    // Check if machine preselected
    if ($('#selectMachine').val()) {
        $('#selectMachine').trigger('change');
    }
});
</script>
@endpush
