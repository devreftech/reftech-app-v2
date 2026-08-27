@extends('layouts.sales.app')
@section('title', 'Create Service Reports')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <style>
        .form-section-card {
            border-radius: 16px;
            border: 1px solid rgba(229, 231, 235, 0.5);
            box-shadow: 0 4px 16px rgba(0,0,0,0.02);
            transition: box-shadow 0.2s ease-in-out;
        }

        /* Global .card:hover (demo.css) nge-lift semua card 2px + bayangannya
           dibesarin — cocok buat grid dashboard, tapi di form ini card-nya ditumpuk
           vertikal penuh selebar layar, jadi begitu hover/klik field di dalamnya,
           card ke-lift dan nabrak/tumpang-tindih sama card di bawahnya. Dimatikan
           efeknya khusus di sini. */
        .form-section-card:hover {
            transform: none !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.02) !important;
        }

        .select2-container--open {
            z-index: 9999 !important;
        }
        .select2-dropdown {
            z-index: 9999 !important;
        }
        .form-section-title {
            font-size: 1.05rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-section-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }
        .bg-icon-primary { background: rgba(99, 102, 241, 0.12); color: #6366f1; }
        .bg-icon-info { background: rgba(14, 165, 233, 0.12); color: #0ea5e9; }
        .bg-icon-success { background: rgba(16, 185, 129, 0.12); color: #10b981; }

        .service-no-badge {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            border-radius: 12px;
            padding: 0.6rem 1.2rem;
            font-family: monospace;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.05em;
        }
    </style>

    <!-- Modern Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="mdi mdi-file-document-edit-outline text-primary me-2"></i>{{ @$report ? 'Edit Service Report' : 'Create Service Report' }}
            </h2>
            <p class="text-muted mb-0 small">Lengkapi formulir laporan servis teknisi di bawah ini dengan lengkap & akurat.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex align-items-center gap-2">
            <span class="text-muted small fw-semibold">No. Service:</span>
            <div class="service-no-badge shadow-sm">
                {{ @$report ? $report->no_service : ($formattedNumberS . '-S/RJO-' . Auth::user()->code . '/' . $formattedMonthNow . '/' . \Carbon\Carbon::now()->year) }}
            </div>
        </div>
    </div>

    <form action="{{ @$report ? route('service-reports.update', @$report->id) : route('service-reports.store') }}"
        method="post" id="serviceReports" name="service-reports">
        @csrf
        @if (@$report)
            @method('PATCH')
        @endif
        <input type="hidden" name="no_service" value="{{ @$report ? $report->no_service : ($formattedNumberS . '-S/RJO-' . Auth::user()->code . '/' . $formattedMonthNow . '/' . \Carbon\Carbon::now()->year) }}">

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="d-flex align-items-center mb-2 fw-bold">
                    <i class="mdi mdi-alert-circle-outline me-2 mdi-20px"></i> Mohon koreksi inputan berikut:
                </div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Section 1: Customer & Machine Selection --}}
        <div class="card form-section-card mb-4">
            <div class="card-header bg-transparent border-bottom p-4">
                <div class="form-section-title">
                    <div class="form-section-icon bg-icon-primary">
                        <i class="mdi mdi-domain"></i>
                    </div>
                    <div>
                        <span>Pelanggan & Jenis Layanan</span>
                        <small class="text-muted d-block fw-normal" style="font-size: 0.8rem;">Pilih sales, klien, PIC penanggung jawab, dan jenis layanan.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                @if (isset($isInternalStock) && $isInternalStock)
                    <div class="alert alert-info border-0 mb-4 py-2" style="border-radius: 10px;">
                        <i class="mdi mdi-information-outline me-1"></i> Unit internal Reftech — Sales/Client/PIC dilewati, langsung menggunakan Machine yang sudah dipilih.
                    </div>
                    <input type="hidden" name="id_sales" value="">
                    <input type="hidden" name="client" value="">
                    <input type="hidden" name="id_pic" value="{{ $selectedPICId }}">
                    <input type="text" name="technician" value="{{ Auth::user()->id }}" hidden>
                @endif

                <div class="row g-3">
                    @if (!isset($isInternalStock) || !$isInternalStock)
                        <div class="col-12 col-md-3">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select invoice-item-sales" data-allow-clear="true"
                                    name="id_sales" id="selectSales" {{ @$reports ? 'disabled' : '' }}>
                                    <option selected disabled>----- Select Sales -----</option>
                                    @foreach ($sales as $sale)
                                        <option data-id="{{ $sale->id }}" value="{{ $sale->id }}"
                                            {{ (isset($selectedSalesId) && $selectedSalesId == $sale->id) || (@$report && isset($report->pic->client->sales) && $report->pic->client->sales->id == $sale->id) ? 'selected' : '' }}>
                                            {{ $sale->name }}</option>
                                    @endforeach
                                </select>
                                <label for="selectSales">Sales Representative</label>
                            </div>
                            <input type="text" name="technician" value="{{ Auth::user()->id }}" hidden>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-floating form-floating-outline">
                                <select id="client-dropdown" class="select2 form-select invoice-item-client" data-id="1"
                                    data-allow-clear="true" name="client" disabled>
                                    <option selected disabled> ---- Choose Client ---- </option>
                                    @if (@$report && isset($report->pic->client))
                                        <option data-id="{{ $report->pic->client->id }}" value="{{ $report->pic->client->id }}"
                                            selected>
                                            {{ $report->pic->client->company }}</option>
                                    @endif
                                </select>
                                <label for="client-dropdown">Client / Company</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="d-flex gap-2 align-items-start">
                                <div class="form-floating form-floating-outline flex-grow-1">
                                    <select id="pic-dropdown" class="select2 form-select invoice-item-pic" data-id="1"
                                        data-allow-clear="true" name="id_pic" disabled>
                                        <option selected disabled> ---- Choose PIC ---- </option>
                                        @if (@$report && isset($report->pic))
                                            <option data-id="{{ $report->pic->id }}" value="{{ $report->pic->id }}" selected>
                                                {{ $report->pic->name_pic }}</option>
                                        @endif
                                    </select>
                                    <label for="pic-dropdown">PIC Klien</label>
                                </div>
                                <button type="button" class="btn btn-icon btn-label-primary mt-1" id="btnQuickPic" title="Tambah PIC Baru">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="col-12 col-md-3">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="service-type-select" aria-label="Service Type" name="type">
                                <option selected="" disabled>---- Choose Service Type ----</option>
                                <option value="Visit" {{ @$report->type == 'Visit' ? 'Selected' : '' }}>Visit</option>
                                <option value="Service" {{ @$report->type == 'Service' ? 'Selected' : '' }}>Service</option>
                                <option value="General" {{ @$report->type == 'General' ? 'Selected' : '' }}>General Check</option>
                                <option value="Rental" {{ @$report->type == 'Rental' ? 'Selected' : '' }}>Rental</option>
                                <option value="Cleaning" {{ @$report->type == 'Cleaning' ? 'Selected' : '' }}>Cleaning</option>
                                <option value="Commissioning" {{ @$report->type == 'Commissioning' ? 'Selected' : '' }}>Commissioning</option>
                            </select>
                            <label for="service-type-select">Service Type</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Details & Operational Parameters --}}
        <div class="card form-section-card mb-4">
            <div class="card-header bg-transparent border-bottom p-4">
                <div class="form-section-title">
                    <div class="form-section-icon bg-icon-info">
                        <i class="mdi mdi-tune-vertical-variant"></i>
                    </div>
                    <div>
                        <span>Parameter & Jenis Pekerjaan</span>
                        <small class="text-muted d-block fw-normal" style="font-size: 0.8rem;">Pilih unit mesin, tentukan tanggal servis, jam kerja mesin (running/load), dan job description.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div class="d-flex gap-2 align-items-start">
                            <div class="form-floating form-floating-outline flex-grow-1">
                                <select id="machine-dropdown" class="form-select invoice-item-machine" data-id="1"
                                    data-allow-clear="true" name="machine"
                                    {{ isset($isInternalStock) && $isInternalStock ? '' : 'disabled' }}>
                                    <option selected disabled> ---- Choose Machine ---- </option>
                                    @if (isset($isInternalStock) && $isInternalStock && isset($machine))
                                        <option value="{{ $machine->id }}" data-unit-category="{{ optional(optional($machine->unit)->unit)->unit ?? '' }}"
                                            data-dummy="{{ $machine->id_unit ? 0 : 1 }}" selected>
                                            {{ optional($machine->unit)->brand ?? '-' }} {{ optional($machine->unit)->pn ?? '' }} ||
                                            {{ $machine->location }} - {{ $machine->tag }} - {{ $machine->serial }}
                                        </option>
                                    @elseif (@$report && isset($report->machine))
                                        <option data-id="{{ $report->machine->id }}" value="{{ $report->machine->id }}" data-unit-category="{{ optional(optional(optional($report->machine)->unit)->unit)->unit ?? '' }}"
                                            data-dummy="{{ $report->machine->id_unit ? 0 : 1 }}" selected>
                                            {{ optional($report->machine->unit)->brand ?? '-' }} {{ optional($report->machine->unit)->pn ?? '' }} ||
                                            {{ $report->machine->location }} - {{ $report->machine->tag }} -
                                            {{ $report->machine->serial }}
                                        </option>
                                    @endif
                                </select>
                                <label for="machine-dropdown">Unit Mesin</label>
                            </div>
                            <button type="button" class="btn btn-icon btn-label-primary mt-1" id="btnQuickMachine"
                                title="Tambah Mesin Baru (Dummy)">
                                <i class="mdi mdi-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-md-4" id="pm-level-container" style="display: none;">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="pm-level-select" name="pm_level">
                                <option value="" selected>---- Select PM Level ----</option>
                                <option value="PM1" {{ @$report->pm_level == 'PM1' ? 'Selected' : '' }}>PM1 (Minor Service)</option>
                                <option value="PM2" {{ @$report->pm_level == 'PM2' ? 'Selected' : '' }}>PM2 (Major Service)</option>
                                <option value="PM3" {{ @$report->pm_level == 'PM3' ? 'Selected' : '' }}>PM3</option>
                                <option value="PM4" {{ @$report->pm_level == 'PM4' ? 'Selected' : '' }}>PM4</option>
                                <option value="Troubleshooting" {{ @$report->pm_level == 'Troubleshooting' ? 'Selected' : '' }}>Troubleshooting / Repair</option>
                            </select>
                            <label for="pm-level-select">PM Level (Air Compressor Screw)</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="date" name="date" id="date"
                                value="{{ $report->date ?? now()->format('Y-m-d') }}">
                            <label for="date">Tanggal Pengerjaan</label>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="running" name="running"
                                placeholder="Running Hours..." value="{{ old('running', @$report->running ?? '') }}">
                            <label for="running">Running Hours (Jam)</label>
                        </div>
                    </div>

                    <div class="col-6 col-md-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="load" name="load"
                                placeholder="Load Hours..." value="{{ old('load', @$report->load ?? '') }}">
                            <label for="load">Load Hours (Jam)</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="jobdesc" name="jobdesc"
                                placeholder="Scope of Work..."
                                value="{{ old('jobdesc', @$report->jobdesc ?? '') }}">
                            <label for="jobdesc">Job Description (Ringkasan Tugas)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Detailed Notes & Recommendation --}}
        <div class="card form-section-card mb-4">
            <div class="card-header bg-transparent border-bottom p-4">
                <div class="form-section-title">
                    <div class="form-section-icon bg-icon-success">
                        <i class="mdi mdi-notebook-edit-outline"></i>
                    </div>
                    <div>
                        <span>Temuan, Catatan & Rekomendasi Teknisi</span>
                        <small class="text-muted d-block fw-normal" style="font-size: 0.8rem;">Tuliskan hasil temuan kondisi lapangan dan saran perbaikan untuk pelanggan.</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="description" name="desc" placeholder="Detail temuan..."
                                style="min-height: 110px;">{{ old('desc', @$report->desc ?? '') }}</textarea>
                            <label for="description">Detail Temuan & Keterangan Servis (Description)</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="recomendation" name="recomendation" placeholder="Rekomendasi teknisi..."
                                style="min-height: 110px;">{{ old('recomendation', @$report->recomendation ?? '') }}</textarea>
                            <label for="recomendation">Rekomendasi Perbaikan / Part Replacement</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Submit Actions Bar -->
        <div class="d-flex justify-content-end align-items-center gap-2 mb-5 p-3 bg-white border rounded-3 shadow-sm">
            <a href="{{ route('service-reports.index') }}" class="btn btn-outline-secondary px-4 wave-effect" style="border-radius: 10px;">
                <i class="mdi mdi-arrow-left me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary px-4 shadow-sm wave-effect" style="border-radius: 10px; font-weight: 600;">
                <i class="mdi mdi-content-save-outline me-1"></i> Save
            </button>
        </div>
    </form>

    {{-- Modal: Quick-add PIC buat Client yang UDAH dipilih, langsung dari form ini
         tanpa reload halaman. --}}
    <div class="modal fade" id="modalQuickPic" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah PIC Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="quickPicError"></div>
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Nama PIC</label>
                        <input type="text" class="form-control" id="quickPicName" placeholder="Nama penanggung jawab">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">No. HP (opsional)</label>
                            <input type="text" class="form-control" id="quickPicPhone">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">Email (opsional)</label>
                            <input type="email" class="form-control" id="quickPicEmail">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSaveQuickPic">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Quick-add Machine "Dummy" — input bebas, gak di-link ke katalog Unit. --}}
    <div class="modal fade" id="modalQuickMachine" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Mesin Baru <span class="badge bg-label-warning ms-1">Dummy</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info border-0 py-2 mb-3" style="border-radius: 10px;">
                        <i class="mdi mdi-information-outline me-1"></i> Input bebas, gak lewat katalog Unit — cuma buat unit di lapangan yang belum tercatat. Ditandai badge <span class="badge bg-label-warning">Dummy</span> di dropdown.
                    </div>
                    <div class="alert alert-danger d-none" id="quickMachineError"></div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">Brand</label>
                            <input type="text" class="form-control" id="quickMachineBrand" placeholder="KAESER">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">Model / Type</label>
                            <input type="text" class="form-control" id="quickMachineModel" placeholder="CSD 130">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Serial Number</label>
                        <input type="text" class="form-control" id="quickMachineSerial">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">Lokasi (opsional)</label>
                            <input type="text" class="form-control" id="quickMachineLocation">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">Tag (opsional)</label>
                            <input type="text" class="form-control" id="quickMachineTag">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSaveQuickMachine">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    @include('components.modal.machine.form-technician')
</div>
@endsection
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        #image-preview img {
            max-width: 150px;
            margin-left: 16px;
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        function initNumericInput() {
            var input = $('.input-numeric')
            for (var i = 0; i < input.length; i++) {
                input[i].addEventListener('input', function() {
                    // Hapus karakter selain angka
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        }
        $(document).ready(function() {
            var selectedMachineId = '{{ $selectedMachineId ?? $report->id_machine ?? '' }}';
            var selectedSalesId = '{{ $selectedSalesId ?? (isset($report->pic->client) ? $report->pic->client->id_sales : '') }}';
            var selectedClientId = '{{ $selectedClientId ?? (isset($report->pic) ? $report->pic->id_client : '') }}';
            var selectedPICId = '{{ $selectedPICId ?? $report->id_pic ?? '' }}';
            var isInternalStock = {{ isset($isInternalStock) && $isInternalStock ? 'true' : 'false' }};
            var csrfToken = '{{ csrf_token() }}';
            initNumericInput();

            // Unit Mesin dilepas dari auto-init select2 generic (lihat class-nya di
            // markup, gak ada "select2" lagi) biar bisa dikasih templateResult sendiri
            // buat nampilin badge "Dummy" di opsi yang id_unit-nya null.
            function machineOptionTemplate(item) {
                if (!item.id) return item.text;
                var isDummy = item.element && $(item.element).attr('data-dummy') == '1';
                if (!isDummy) return item.text;
                return $('<span>' + item.text + ' <span class="badge bg-label-warning">Dummy</span></span>');
            }
            function refreshMachineSelect2() {
                var $el = $('#machine-dropdown');
                if ($el.data('select2')) {
                    $el.select2('destroy');
                }
                $el.select2({
                    width: '100%',
                    templateResult: machineOptionTemplate,
                    templateSelection: machineOptionTemplate,
                });
            }
            refreshMachineSelect2();

            // Quick-add PIC — buat Client yang UDAH dipilih, biar teknisi gak perlu
            // buka halaman lain buat nambahin penanggung jawab baru.
            var modalQuickPic = new bootstrap.Modal(document.getElementById('modalQuickPic'));
            $('#btnQuickPic').on('click', function() {
                var currentClient = $('#client-dropdown').val();
                if (!currentClient) {
                    Swal.fire('Pilih Client dulu', 'Client / Company wajib dipilih sebelum menambah PIC baru.', 'warning');
                    return;
                }
                $('#quickPicError').addClass('d-none').text('');
                modalQuickPic.show();
            });
            $('#btnSaveQuickPic').on('click', function() {
                var $btn = $(this);
                var payload = {
                    id_client: $('#client-dropdown').val(),
                    name_pic: $('#quickPicName').val(),
                    phone_pic: $('#quickPicPhone').val(),
                    email_pic: $('#quickPicEmail').val(),
                    _token: csrfToken,
                };
                if (!payload.id_client || !payload.name_pic) {
                    $('#quickPicError').removeClass('d-none').text('Client dan Nama PIC wajib diisi.');
                    return;
                }
                $btn.prop('disabled', true);
                $.ajax({
                    url: '{{ route('service-reports.quick-pic') }}',
                    type: 'POST',
                    data: payload,
                    success: function(res) {
                        var opt = $('<option></option>').attr('value', res.id).text(res.name_pic);
                        $('#pic-dropdown').append(opt).prop('disabled', false)
                            .val(res.id).trigger('change');

                        $('#quickPicName, #quickPicPhone, #quickPicEmail').val('');
                        modalQuickPic.hide();
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.errors)
                            ? Object.values(xhr.responseJSON.errors).flat().join(' ')
                            : 'Gagal menyimpan PIC baru.';
                        $('#quickPicError').removeClass('d-none').text(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });

            // Quick-add Machine "Dummy" — butuh Client udah kepilih dulu.
            var modalQuickMachine = new bootstrap.Modal(document.getElementById('modalQuickMachine'));
            $('#btnQuickMachine').on('click', function() {
                if (!$('#client-dropdown').val()) {
                    Swal.fire('Pilih Client dulu', 'Client / Company wajib dipilih sebelum menambah mesin baru.', 'warning');
                    return;
                }
                $('#quickMachineError').addClass('d-none').text('');
                modalQuickMachine.show();
            });
            $('#btnSaveQuickMachine').on('click', function() {
                var $btn = $(this);
                var payload = {
                    id_client: $('#client-dropdown').val(),
                    brand: $('#quickMachineBrand').val(),
                    model: $('#quickMachineModel').val(),
                    serial: $('#quickMachineSerial').val(),
                    location: $('#quickMachineLocation').val(),
                    tag: $('#quickMachineTag').val(),
                    _token: csrfToken,
                };
                if (!payload.id_client || !payload.brand || !payload.model || !payload.serial) {
                    $('#quickMachineError').removeClass('d-none').text('Client, Brand, Model, dan Serial Number wajib diisi.');
                    return;
                }
                $btn.prop('disabled', true);
                $.ajax({
                    url: '{{ route('service-reports.quick-machine') }}',
                    type: 'POST',
                    data: payload,
                    success: function(res) {
                        var opt = $('<option></option>').attr('value', res.id)
                            .attr('data-unit-category', res.unit_category || '')
                            .attr('data-dummy', res.is_dummy || 0)
                            .text(res.text);
                        $('#machine-dropdown').append(opt).prop('disabled', false);
                        refreshMachineSelect2();
                        $('#machine-dropdown').val(res.id).trigger('change');

                        $('#quickMachineBrand, #quickMachineModel, #quickMachineSerial, #quickMachineLocation, #quickMachineTag').val('');
                        modalQuickMachine.hide();
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.errors)
                            ? Object.values(xhr.responseJSON.errors).flat().join(' ')
                            : 'Gagal menyimpan mesin baru.';
                        $('#quickMachineError').removeClass('d-none').text(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                    }
                });
            });
            $('#formFileMultiple').on('change', function() {
                var files = this.files;
                var dynamicInputsContainer = $('#dynamicInputsContainer');
                console.log(dynamicInputsContainer);

                dynamicInputsContainer.empty();

                for (var i = 0; i < files.length; i++) {
                    var dynamicInput =
                        '<input class="form-control mb-2" type="text" name="description[]" placeholder="Deskripsi untuk File ' +
                        (i +
                            1) + '">';
                    dynamicInputsContainer.append(dynamicInput);
                }

                if (files.length !== 3 && files.length !== 6 && files.length !== 9) {
                    alert('Gambar Wajib Kelipatan 3! 3/6/9 Maksimal 9');
                    this.value = ''; // Menghapus file yang tidak memenuhi syarat
                    dynamicInputsContainer.empty();
                }

                console.log(files);
                const previewContainer = document.getElementById('image-preview');
                previewContainer.innerHTML = '';

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imageContainer = document.createElement('div');
                        const imageElement = document.createElement('img');
                        const description = document.createElement('p');

                        imageContainer.className =
                            'image-container'; // Tambahkan kelas sesuai kebutuhan
                        imageElement.src = e.target.result;
                        description.textContent = 'Photo ' + (i + 1);

                        imageContainer.appendChild(imageElement);
                        imageContainer.appendChild(description);
                        previewContainer.appendChild(imageContainer);

                    };

                    reader.readAsDataURL(file);
                }
            });
            $('#selectSales').on('change', function() {
                var salesId = $(this).find(':selected').data('id');
                var Url = '/client/dropdown/' + salesId;

                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        var clientDropdown = $('#client-dropdown');
                        clientDropdown.empty();
                        clientDropdown.append(
                            '<option selected="" disabled> ---- Choose Client Here ---- </option>'
                        );

                        $.each(response, function(key, value) {
                            var option = $('<option></option>').attr('value', value.id)
                                .text(value.company);
                            clientDropdown.append(option);
                        });

                        clientDropdown.prop('disabled', false);

                        if (selectedClientId) {
                            clientDropdown.val(selectedClientId).trigger('change');
                        }
                    }
                });
            });

            $('#client-dropdown').on('change', function() {
                var clientId = $(this).find(':selected').val();
                var Url = '/pic/dropdown/' + clientId;

                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        var picDropdown = $('#pic-dropdown');
                        picDropdown.empty();
                        picDropdown.append(
                            '<option selected="" disabled> ---- Choose PIC Here ---- </option>'
                        );

                        $.each(response, function(key, value) {
                            var option = $('<option></option>').attr('value', value.id)
                                .text(value.name_pic);
                            picDropdown.append(option);
                        });

                        picDropdown.prop('disabled', false);

                        if (selectedPICId) {
                            picDropdown.val(selectedPICId).trigger('change');
                        }
                    }
                });
            });

            // Unit Mesin baru ke-load setelah Service Type dipilih. Kalau Service
            // Type = Rental, sumbernya BUKAN mesin milik client (PIC-nya diabaikan),
            // tapi daftar unit internal Reftech (Fixed Asset) — selain itu tetap
            // sesuai unit yang dimiliki client (perlu PIC dulu buat tau client-nya).
            function resetMachineDropdown(placeholder) {
                var machineDropdown = $('#machine-dropdown');
                machineDropdown.empty();
                machineDropdown.append('<option selected disabled>' + placeholder + '</option>');
                machineDropdown.prop('disabled', true);
                refreshMachineSelect2();
            }

            function populateMachineDropdown(url) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        var machineDropdown = $('#machine-dropdown');
                        machineDropdown.empty();
                        machineDropdown.append(
                            '<option selected="" disabled> ---- Choose Machine Here ---- </option>'
                        );

                        $.each(response, function(key, value) {
                            // Mesin "Dummy" (id_unit null) gak punya brand/model dari
                            // katalog — fallback ke machine.desc (yang diisi bebas pas
                            // quick-add) biar labelnya tetap kebaca.
                            var label = (value.brand || value.model)
                                ? ((value.brand || '') + " " + (value.model || '')).trim()
                                : (value.desc || '-');
                            var option = $('<option></option>').attr('value', value.id)
                                .attr('data-unit-category', value.unit_category || '')
                                .attr('data-dummy', value.is_dummy || 0)
                                .text(label +
                                    " || " + value.location + " - " + value.tag +
                                    " - " + value.serial);
                            machineDropdown.append(option);
                        });

                        machineDropdown.prop('disabled', false);
                        refreshMachineSelect2();

                        if (selectedMachineId) {
                            machineDropdown.val(selectedMachineId).trigger('change');
                        }
                    }
                });
            }

            function loadMachineDropdown() {
                if (isInternalStock) return; // Machine udah dikunci dari server

                var serviceType = $('#service-type-select').val();
                if (!serviceType) {
                    resetMachineDropdown('---- Pilih Service Type dulu ----');
                    return;
                }

                if (serviceType === 'Rental') {
                    populateMachineDropdown('/db/machine/internal-fleet');
                    return;
                }

                var picId = $('#pic-dropdown').val();
                if (!picId) {
                    resetMachineDropdown('---- Pilih PIC dulu ----');
                    return;
                }
                populateMachineDropdown('/machine/dropdown/' + picId);
            }

            $('#pic-dropdown').on('change', function() {
                loadMachineDropdown();
            });

            $('#service-type-select').on('change', function() {
                loadMachineDropdown();
            });

            function checkPmLevelVisibility() {
                var serviceType = $('#service-type-select').val();
                var selectedOption = $('#machine-dropdown').find(':selected');
                var unitCategory = selectedOption.data('unit-category') || '';

                // Handle pre-filled machine option or dynamically selected option
                if (serviceType === 'Service' && unitCategory.toUpperCase().includes('AIR COMPRESSOR SCREW')) {
                    $('#pm-level-container').slideDown(200);
                } else {
                    $('#pm-level-container').slideUp(200);
                }
            }

            $('#service-type-select, #machine-dropdown').on('change', function() {
                checkPmLevelVisibility();
            });

            // Initial check on load (edit mode / pre-selected)
            setTimeout(function() {
                checkPmLevelVisibility();
            }, 500);

            // Trigger change event to pre-select dependent dropdowns in order
            // (dilewati untuk unit internal Reftech — Machine sudah langsung dipilih dari server)
            if (selectedSalesId && !isInternalStock) {
                $('#selectSales').trigger('change');
            }

            $('#serviceReports').on('submit', function(e) {
                e.preventDefault();
                var form = this;

                Swal.fire({
                    title: 'Apakah kamu sudah benar dalam pembuatan service report ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                        cancelButton: 'btn btn-label-secondary waves-effect',
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

        });
    </script>
@endpush
