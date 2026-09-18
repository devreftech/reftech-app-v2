@extends('layouts.sales.app')
@section('title', 'Buat Surat Jalan Manual')

@section('content')
    <div class="d-flex justify-content-between align-items-center py-3 mb-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Accounting / <a href="{{ route('delivery.index') }}" class="text-muted">Delivery Order</a> /</span> Buat Surat Jalan Manual
            </h4>
            <p class="text-muted mb-0 small">Formulir pembuatan Surat Jalan / Delivery Order dari Invoice atau Standalone</p>
        </div>
        <div>
            <a href="{{ route('delivery.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 shadow-xs">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar DO
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-circle-outline me-2 fs-5"></i>
                <div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="formCreateManualDelivery" action="{{ route('delivery.store-manual-custom') }}" method="POST">
        @csrf

        {{-- Card 1: Sumber Data & Informasi Pengiriman --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-truck-delivery-outline text-primary fs-4"></i> Informasi Pengiriman &amp; Penerima
                </h5>
            </div>
            <div class="card-body p-4">
                {{-- Mode Switcher: Dari Invoice vs Manual Bebas --}}
                <div class="card bg-light border-0 shadow-none mb-4">
                    <div class="card-body p-3">
                        <label class="form-label fw-bold text-dark mb-2">Sumber Data Surat Jalan</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check custom-option custom-option-basic">
                                <label class="form-check-label custom-option-content py-2 px-3 rounded border bg-white cursor-pointer" for="sourceInvoice">
                                    <input name="source_type" class="form-check-input" type="radio" value="invoice" id="sourceInvoice" {{ old('source_type', 'invoice') === 'invoice' ? 'checked' : '' }}>
                                    <span class="custom-option-header pb-0">
                                        <span class="fw-bold text-dark"><i class="mdi mdi-file-document-outline text-primary me-1"></i> Dari Invoice yang Ada</span>
                                    </span>
                                    <span class="custom-option-body pt-1 small text-muted d-block">
                                        Pilih invoice tersimpan untuk mengisi customer &amp; alamat otomatis
                                    </span>
                                </label>
                            </div>
                            <div class="form-check custom-option custom-option-basic">
                                <label class="form-check-label custom-option-content py-2 px-3 rounded border bg-white cursor-pointer" for="sourceStandalone">
                                    <input name="source_type" class="form-check-input" type="radio" value="standalone" id="sourceStandalone" {{ old('source_type') === 'standalone' ? 'checked' : '' }}>
                                    <span class="custom-option-header pb-0">
                                        <span class="fw-bold text-dark"><i class="mdi mdi-pencil-box-outline text-primary me-1"></i> Manual Bebas (Standalone)</span>
                                    </span>
                                    <span class="custom-option-body pt-1 small text-muted d-block">
                                        Ketik customer, alamat, dan referensi tanpa terikat invoice
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    {{-- Row 1: Invoice Selector (Only active in invoice mode) --}}
                    <div class="col-md-12" id="invoiceSelectContainer">
                        <label class="form-label fw-bold">Pilih Invoice <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="selectInvoiceDo" name="id_invoice" style="width: 100%;">
                            <option value="">-- Cari &amp; Pilih No. Invoice / Customer --</option>
                            @foreach ($invoices as $inv)
                                @php
                                    $custName = $inv->quote->pic->client->company ?? ($inv->unitQuote->client->company ?? 'Unknown');
                                    $addrMain = $inv->quote->pic->client->address ?? ($inv->unitQuote->client->address ?? '');
                                    $addrSub  = $inv->quote->pic->client->subAddress ?? ($inv->unitQuote->client->subAddress ?? '');
                                    $flag     = $inv->flag ?: ($inv->unitQuote->client->info === 'Kojisha' ? 'Kojisha' : 'Reftech');
                                @endphp
                                <option value="{{ $inv->id }}"
                                    {{ old('id_invoice') == $inv->id ? 'selected' : '' }}
                                    data-customer="{{ $custName }}"
                                    data-address-main="{{ $addrMain }}"
                                    data-address-sub="{{ $addrSub }}"
                                    data-invoice-to="{{ $inv->invoiceTo ?? '1' }}"
                                    data-po="{{ $inv->no_po ?? '' }}"
                                    data-flag="{{ $flag }}">
                                    {{ $inv->no_invoice }} — {{ $custName }} (PO: {{ $inv->no_po ?: '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Row 2: Entitas, Jenis Pengiriman & Tanggal --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Entitas Perusahaan <span class="text-danger">*</span></label>
                        <select class="form-select" id="doEntity" name="entity" required>
                            <option value="Reftech" {{ old('entity', 'Reftech') === 'Reftech' ? 'selected' : '' }}>PT Reftech Jaya Optima</option>
                            <option value="Kojisha" {{ old('entity') === 'Kojisha' ? 'selected' : '' }}>PT Kojisha Innotiv Indonesia</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Jenis Pengiriman <span class="text-danger">*</span></label>
                        <select class="form-select" id="doType" name="type" required>
                            <option value="ekspedisi" {{ old('type', 'ekspedisi') === 'ekspedisi' ? 'selected' : '' }}>Ekspedisi / Logistik / Kurir</option>
                            <option value="teknisi" {{ old('type') === 'teknisi' ? 'selected' : '' }}>Teknisi / Hand Carry / Diantar Sendiri</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tanggal Surat Jalan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="doDate" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                    </div>

                    {{-- Row 3: Customer & PO --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Customer / Nama Perusahaan <span class="text-danger">*</span></label>
                        <div class="mb-1" id="clientSelectGroup" style="display: none;">
                            <select class="form-select" id="selectClientDo" style="width: 100%;">
                                <option value="">-- Ketik untuk cari customer dari database... --</option>
                            </select>
                        </div>
                        <input type="text" class="form-control" id="doCustomerName" name="customer_name" value="{{ old('customer_name') }}" placeholder="Masukkan nama PT / Perusahaan / Customer" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. PO Customer / Referensi</label>
                        <input type="text" class="form-control" id="doPoNumber" name="po_number" value="{{ old('po_number') }}" placeholder="Contoh: PO/2026/09/001">
                    </div>

                    {{-- Row 4: Alamat Pengiriman --}}
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">Alamat Pengiriman (Ship To) <span class="text-danger">*</span></label>
                            <div id="addressSwitcher" class="d-none">
                                <div class="btn-group btn-group-sm" role="group">
                                    <input type="radio" class="btn-check" name="dest_toggle" id="destMain" value="1" checked autocomplete="off">
                                    <label class="btn btn-outline-primary btn-xs" for="destMain">Alamat Utama</label>

                                    <input type="radio" class="btn-check" name="dest_toggle" id="destSub" value="2" autocomplete="off">
                                    <label class="btn btn-outline-primary btn-xs" for="destSub">Alamat Cabang/Pabrik</label>
                                </div>
                                <input type="hidden" name="destination" id="doDestination" value="{{ old('destination', '1') }}">
                            </div>
                        </div>
                        <textarea class="form-control" id="doAddress" name="address" rows="2" placeholder="Masukkan alamat lengkap tujuan pengiriman" required>{{ old('address') }}</textarea>
                    </div>

                    {{-- Row 5: Info Tambahan Pengiriman --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">No. Surat Jalan Manual</label>
                        <input type="text" class="form-control font-monospace" id="doNoDo" name="no_do" value="{{ old('no_do') }}" placeholder="Otomatis jika dikosongkan">
                        <div class="form-text small text-muted">Contoh: SJ/RT/{{ date('Y') }}/{{ date('m') }}/0001</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nama Supir / Kurir / Ekspedisi</label>
                        <input type="text" class="form-control" id="doDriverName" name="driver_name" value="{{ old('driver_name') }}" placeholder="Misal: JNE / Pak Budi / GoSend">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">No. Plat Kendaraan / No. Resi</label>
                        <input type="text" class="form-control" id="doVehicleNo" name="vehicle_no" value="{{ old('vehicle_no') }}" placeholder="Misal: B 1234 XYZ / Resi #12345">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Catatan Pengiriman</label>
                        <textarea class="form-control" id="doNote" name="note" rows="2" placeholder="Catatan khusus penerima / instruksi bongkar muat...">{{ old('note') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Daftar Item Barang yang Dikirim --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-format-list-bulleted text-primary fs-4"></i> Daftar Item Barang yang Dikirim
                </h5>
                <button type="button" class="btn btn-sm btn-primary shadow-xs d-flex align-items-center gap-1" id="btnAddDoItemRow">
                    <i class="mdi mdi-plus"></i> Tambah Baris Barang
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive border rounded mb-3">
                    <table class="table table-sm table-bordered align-middle mb-0" id="doItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 4%" class="text-center">No.</th>
                                <th style="width: 36%">Nama Barang / Produk <span class="text-danger">*</span></th>
                                <th style="width: 32%">Deskripsi / Spesifikasi / Part No.</th>
                                <th style="width: 12%" class="text-center">Qty <span class="text-danger">*</span></th>
                                <th style="width: 12%">Satuan</th>
                                <th style="width: 4%" class="text-center"><i class="mdi mdi-trash-can-outline"></i></th>
                            </tr>
                        </thead>
                        <tbody id="doItemsTableBody">
                            {{-- Initial rows populated via JS --}}
                        </tbody>
                    </table>
                </div>
                <div class="form-text text-muted small">
                    <i class="mdi mdi-information-outline me-1"></i>Pastikan minimal 1 baris item barang terisi dengan nama barang dan jumlah kuantitas.
                </div>
            </div>
        </div>

        {{-- Bottom Actions --}}
        <div class="card border-0 shadow-sm mb-5">
            <div class="card-body py-3 d-flex justify-content-between align-items-center">
                <a href="{{ route('delivery.index') }}" class="btn btn-label-secondary">
                    <i class="mdi mdi-close me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-4" id="btnSubmitDoManual">
                    <i class="mdi mdi-content-save-outline fs-5"></i> Simpan Surat Jalan
                </button>
            </div>
        </div>
    </form>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('script')
<script>
$(document).ready(function() {
    let doRowIndex = 0;
    let selectedInvData = null;

    function createDoItemRow(idx, data = {}) {
        return `
            <tr data-row-idx="${idx}">
                <td class="text-center fw-semibold row-number">${idx + 1}</td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="items[${idx}][product]" value="${data.product || ''}" placeholder="Nama barang / kompresor / sparepart" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="items[${idx}][desc]" value="${data.desc || ''}" placeholder="Keterangan / spesifikasi / SN / PN">
                </td>
                <td>
                    <input type="number" step="any" min="0.01" class="form-control form-control-sm text-center" name="items[${idx}][qty]" value="${data.qty || 1}" required>
                </td>
                <td>
                    <select class="form-select form-select-sm" name="items[${idx}][info_qty]">
                        <option value="Pcs" ${data.info_qty === 'Pcs' ? 'selected' : ''}>Pcs</option>
                        <option value="Unit" ${data.info_qty === 'Unit' ? 'selected' : ''}>Unit</option>
                        <option value="Set" ${data.info_qty === 'Set' ? 'selected' : ''}>Set</option>
                        <option value="Pail" ${data.info_qty === 'Pail' ? 'selected' : ''}>Pail</option>
                        <option value="Box" ${data.info_qty === 'Box' ? 'selected' : ''}>Box</option>
                        <option value="Roll" ${data.info_qty === 'Roll' ? 'selected' : ''}>Roll</option>
                        <option value="Lot" ${data.info_qty === 'Lot' ? 'selected' : ''}>Lot</option>
                        <option value="Meter" ${data.info_qty === 'Meter' ? 'selected' : ''}>Meter</option>
                        <option value="Drum" ${data.info_qty === 'Drum' ? 'selected' : ''}>Drum</option>
                        <option value="Can" ${data.info_qty === 'Can' ? 'selected' : ''}>Can</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-label-danger btn-remove-row p-1" title="Hapus baris">
                        <i class="mdi mdi-close"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function reindexDoRows() {
        $('#doItemsTableBody tr').each(function(i) {
            $(this).find('.row-number').text(i + 1);
            $(this).find('input, select').each(function() {
                const name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/items\[\d+\]/, `items[${i}]`));
                }
            });
        });
        doRowIndex = $('#doItemsTableBody tr').length;
    }

    function initDefaultRows() {
        $('#doItemsTableBody').empty();
        $('#doItemsTableBody').append(createDoItemRow(0));
        $('#doItemsTableBody').append(createDoItemRow(1));
        $('#doItemsTableBody').append(createDoItemRow(2));
        doRowIndex = 3;
    }

    // Add Row Click
    $('#btnAddDoItemRow').on('click', function() {
        $('#doItemsTableBody').append(createDoItemRow(doRowIndex));
        doRowIndex++;
        reindexDoRows();
    });

    // Remove Row Click
    $(document).on('click', '.btn-remove-row', function() {
        if ($('#doItemsTableBody tr').length <= 1) {
            alert('Minimal 1 item barang harus ada.');
            return;
        }
        $(this).closest('tr').remove();
        reindexDoRows();
    });

    // Toggle Source Mode (Invoice vs Standalone)
    $('input[name="source_type"]').on('change', function() {
        const mode = $(this).val();
        if (mode === 'invoice') {
            $('#invoiceSelectContainer').slideDown(150);
            $('#clientSelectGroup').hide();
            $('#selectInvoiceDo').prop('required', true);
            $('#addressSwitcher').removeClass('d-none');
            $('#selectInvoiceDo').trigger('change');
        } else {
            $('#invoiceSelectContainer').slideUp(150);
            $('#clientSelectGroup').show();
            $('#selectInvoiceDo').prop('required', false);
            $('#addressSwitcher').addClass('d-none');
        }
    });

    // Invoice selection change
    $('#selectInvoiceDo').on('change', function() {
        const $selected = $(this).find(':selected');
        if (!$selected.val()) {
            selectedInvData = null;
            return;
        }

        selectedInvData = {
            customer: $selected.data('customer') || '',
            addressMain: $selected.data('address-main') || '',
            addressSub: $selected.data('address-sub') || '',
            invoiceTo: String($selected.data('invoice-to') || '1'),
            po: $selected.data('po') || '',
            flag: $selected.data('flag') || 'Reftech'
        };

        $('#doCustomerName').val(selectedInvData.customer);
        $('#doPoNumber').val(selectedInvData.po);
        $('#doEntity').val(selectedInvData.flag);

        if (selectedInvData.invoiceTo === '2' && selectedInvData.addressSub) {
            $('#destSub').prop('checked', true);
            $('#doDestination').val('2');
            $('#doAddress').val(selectedInvData.addressSub);
        } else {
            $('#destMain').prop('checked', true);
            $('#doDestination').val('1');
            $('#doAddress').val(selectedInvData.addressMain);
        }
    });

    // Address radio toggle
    $('input[name="dest_toggle"]').on('change', function() {
        const val = $(this).val();
        $('#doDestination').val(val);
        if (selectedInvData) {
            if (val === '2' && selectedInvData.addressSub) {
                $('#doAddress').val(selectedInvData.addressSub);
            } else {
                $('#doAddress').val(selectedInvData.addressMain);
            }
        }
    });

    // Initialize Select2 for Invoice
    if ($.fn.select2) {
        $('#selectInvoiceDo').select2({
            placeholder: '-- Cari & Pilih No. Invoice / Customer --',
            allowClear: true
        });

        // Initialize Select2 AJAX for Client Search in Standalone Mode
        $('#selectClientDo').select2({
            placeholder: '-- Ketik untuk cari customer dari database... --',
            allowClear: true,
            ajax: {
                url: "{{ route('delivery.search-clients') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: data.pagination
                    };
                },
                cache: true
            },
            minimumInputLength: 1
        }).on('select2:select', function(e) {
            const data = e.params.data;
            if (!data) return;
            $('#doCustomerName').val(data.company || data.text || '');
            $('#doAddress').val(data.address || data.sub_address || '');
            if (data.entity) {
                $('#doEntity').val(data.entity);
            }
        });
    }

    // Trigger initial mode state
    $('input[name="source_type"]:checked').trigger('change');
    initDefaultRows();
});
</script>
@endpush
