@extends('layouts.sales.app')
@section('title', 'Detail Pembayaran')
@section('content')

@php
    $clientCompany = $isUnitQuotation ? $quote->client->company : $quote->pic->client->company;
    $clientNpwp = $isUnitQuotation ? $quote->client->npwp : $quote->pic->client->npwp;
    $clientAddress = $isUnitQuotation ? $quote->client->address : $quote->pic->client->address;
    $clientInfo = $isUnitQuotation ? $quote->client->info : $quote->pic->client->info;

    if ($payment->level == 0) {
        if ($payment->file == null) {
            $statusColor = 'danger'; $statusIcon = 'mdi-clock-outline'; $statusText = 'Menunggu Pembayaran';
        } else {
            $statusColor = 'warning'; $statusIcon = 'mdi-eye-check-outline'; $statusText = 'Menunggu Verifikasi';
        }
    } else {
        $statusColor = 'success'; $statusIcon = 'mdi-check-circle-outline'; $statusText = 'Terverifikasi';
    }

    $netAmount = $payment->amount - ($payment->pph ?? 0) - ($payment->cost ?? 0);
@endphp

{{-- Breadcrumb --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-cash-check text-primary"></i> Detail Pembayaran
            <span class="badge bg-label-{{ $statusColor }} rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                <i class="mdi {{ $statusIcon }}"></i> {{ $statusText }}
            </span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 13px;">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment_index.payment') }}">Payment Received</a></li>
                <li class="breadcrumb-item active">#RCPT-{{ $payment->id }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('payment_index.payment') }}" class="btn btn-sm btn-label-secondary rounded-pill px-3">
            <i class="mdi mdi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

{{-- Top Metric Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="mdi mdi-receipt-text-outline fs-4"></i></span>
                </div>
                <div class="overflow-hidden">
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Receipt No.</small>
                    <span class="fw-bold text-dark text-truncate d-block" style="font-size: 15px;">#RCPT-{{ $payment->id }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-info"><i class="mdi mdi-calendar-check-outline fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Tanggal Pembayaran</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;">{{ Carbon\Carbon::parse($payment->date)->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-success"><i class="mdi mdi-cash-multiple fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Nominal Pembayaran</small>
                    <span class="fw-bold text-success" style="font-size: 15px;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-secondary"><i class="mdi mdi-tag-outline fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Tipe / Tag</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;">{{ $payment->type }} {{ $payment->percent }}%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- LEFT COLUMN --}}
    <div class="col-xl-8">
        {{-- Customer Info --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-domain text-primary"></i> Informasi Customer
                </h6>
                {{-- Company Logo --}}
                @if ($clientInfo == 'Reftech')
                    <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Logo" style="height: 28px;">
                @else
                    <img src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt="Logo" style="height: 28px;">
                @endif
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="avatar avatar-lg flex-shrink-0">
                        <span class="avatar-initial rounded bg-primary text-white fw-bold" style="font-size: 18px;">
                            {{ strtoupper(substr($clientCompany ?? 'C', 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold text-dark mb-1">{{ $clientCompany }}</h5>
                        <p class="text-muted mb-2" style="font-size: 13px;">
                            <i class="mdi mdi-map-marker-outline me-1"></i> {{ $clientAddress }}
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            @if ($clientNpwp)
                                <span class="badge bg-label-info rounded-pill px-3 py-1">
                                    <i class="mdi mdi-card-account-details-outline me-1"></i> NPWP: {{ $clientNpwp }}
                                </span>
                            @else
                                <span class="badge bg-label-danger rounded-pill px-3 py-1">
                                    <i class="mdi mdi-alert-circle-outline me-1"></i> NPWP Belum Diisi
                                </span>
                            @endif
                            <a href="{{ $isUnitQuotation ? route('invoice.show_unit', $invoice->id) : route('invoice.show', $invoice->id) }}" target="_blank" class="badge bg-label-primary rounded-pill px-3 py-1 text-decoration-none">
                                <i class="mdi mdi-file-document-outline me-1"></i> Invoice: {{ $invoice->no_invoice }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Detail Table --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-bank-transfer text-primary"></i> Detail Transaksi Pembayaran
                </h6>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-xs btn-label-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editDate">
                        <i class="mdi mdi-calendar-edit me-1"></i> Edit Tanggal
                    </button>
                    <button type="button" class="btn btn-xs btn-label-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addPPH">
                        <i class="mdi mdi-percent me-1"></i> {{ $payment->pph > 0 ? 'Edit' : 'Add' }} PPH
                    </button>
                    <button type="button" class="btn btn-xs btn-label-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addCost">
                        <i class="mdi mdi-currency-usd me-1"></i> {{ $payment->cost > 0 ? 'Edit' : 'Add' }} Cost
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr style="font-size: 12px;">
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3">Tanggal</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3">Metode</th>
                                @if ($payment->pph > 0)
                                    <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">PPH</th>
                                @endif
                                @if ($payment->cost > 0)
                                    <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">Cost</th>
                                @endif
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">Nominal</th>
                                @if ($payment->pph > 0 || $payment->cost > 0)
                                    <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">Nett</th>
                                @endif
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-center">Tag</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-center">Bukti Transfer</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="font-size: 13px;">
                                <td class="align-middle px-3 text-dark">
                                    {{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}
                                </td>
                                <td class="align-middle px-3">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="mdi mdi-bank-transfer text-primary"></i> {{ $payment->method }}
                                    </span>
                                </td>
                                @if ($payment->pph > 0)
                                    <td class="align-middle px-3 text-end text-muted">Rp {{ number_format($payment->pph, 0, ',', '.') }}</td>
                                @endif
                                @if ($payment->cost > 0)
                                    <td class="align-middle px-3 text-end text-muted">Rp {{ number_format($payment->cost, 0, ',', '.') }}</td>
                                @endif
                                <td class="align-middle px-3 text-end fw-semibold text-dark">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                @if ($payment->pph > 0 || $payment->cost > 0)
                                    <td class="align-middle px-3 text-end fw-bold text-primary">Rp {{ number_format($netAmount, 0, ',', '.') }}</td>
                                @endif
                                <td class="align-middle px-3 text-center">
                                    <span class="badge bg-label-secondary rounded-pill px-2.5">{{ $payment->type }} {{ $payment->percent }}%</span>
                                </td>
                                <td class="align-middle px-3 text-center">
                                    @if ($payment->file)
                                        <a href="{{ route('view_payment.payment', $payment->id) }}" target="_blank" class="btn btn-xs btn-label-primary rounded-pill px-3">
                                            <i class="mdi mdi-eye-outline me-1"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted" style="font-size: 12px;">Belum diupload</span>
                                    @endif
                                </td>
                                <td class="align-middle px-3 text-center">
                                    @if ($payment->level == 0)
                                        <button type="button" class="btn btn-xs btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#confirmPayment">
                                            <i class="mdi mdi-check me-1"></i> Konfirmasi
                                        </button>
                                    @else
                                        <a href="#" class="btn btn-xs btn-label-danger rounded-pill px-3 unconfirm-payment" data-id="{{ $payment->id }}">
                                            <i class="mdi mdi-close me-1"></i> Batal Konfirmasi
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Note Card --}}
        @if ($payment->note)
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-note-text-outline text-primary"></i> Catatan Pembayaran
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.6; white-space: pre-line;">{{ $payment->note }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="col-xl-4">
        {{-- Payment Status Card --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-shield-check-outline text-primary"></i> Status Pembayaran
                </h6>
            </div>
            <div class="card-body p-4 text-center">
                <div class="avatar avatar-lg mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-{{ $statusColor }}">
                        <i class="mdi {{ $statusIcon }} fs-3"></i>
                    </span>
                </div>
                <h5 class="fw-bold text-{{ $statusColor }} mb-1">{{ $statusText }}</h5>
                <small class="text-muted">Diperbarui {{ $payment->updated_at ? $payment->updated_at->diffForHumans() : '-' }}</small>
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-calculator-variant-outline text-primary"></i> Rincian Nominal
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3" style="font-size: 13px;">
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                        <span class="text-muted"><i class="mdi mdi-cash me-1.5 text-secondary"></i> Nominal Bruto</span>
                        <span class="fw-bold text-dark">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                    @if ($payment->pph > 0)
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-percent me-1.5 text-danger"></i> PPH</span>
                            <span class="fw-semibold text-danger">- Rp {{ number_format($payment->pph, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($payment->cost > 0)
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-minus-circle-outline me-1.5 text-warning"></i> Cost</span>
                            <span class="fw-semibold text-warning">- Rp {{ number_format($payment->cost, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    @if ($payment->pph > 0 || $payment->cost > 0)
                        <div class="d-flex justify-content-between align-items-center pt-1">
                            <span class="fw-bold text-primary"><i class="mdi mdi-cash-check me-1.5"></i> Nett Diterima</span>
                            <span class="fw-bold text-primary" style="font-size: 15px;">Rp {{ number_format($netAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Activity Timeline --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-history text-primary"></i> Riwayat Aktivitas
                </h6>
            </div>
            <div class="card-body p-4">
                @if ($activity->count() > 0)
                    <div class="d-flex flex-column gap-0">
                        @foreach ($activity as $index => $stats)
                            @php
                                if ($stats->status == '1') {
                                    $actStatus = 'Payment Dilihat'; $actColor = 'primary'; $actIcon = 'mdi-eye-outline';
                                } elseif ($stats->status == '2') {
                                    $actStatus = 'Payment Diverifikasi'; $actColor = 'success'; $actIcon = 'mdi-check-circle-outline';
                                } elseif ($stats->status == '3') {
                                    $actStatus = 'Verifikasi Dibatalkan'; $actColor = 'danger'; $actIcon = 'mdi-close-circle-outline';
                                } else {
                                    $actStatus = 'Payment Dibuat'; $actColor = 'info'; $actIcon = 'mdi-plus-circle-outline';
                                }
                            @endphp
                            <div class="d-flex gap-3 mb-3 position-relative">
                                {{-- Connector line --}}
                                @if (!$loop->last)
                                    <div class="position-absolute" style="left: 15px; top: 32px; bottom: -12px; width: 2px; background: #e7e7e8;"></div>
                                @endif
                                <div class="avatar avatar-sm flex-shrink-0">
                                    <span class="avatar-initial rounded-circle bg-label-{{ $actColor }}">
                                        <i class="mdi {{ $actIcon }}" style="font-size: 14px;"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between mb-0.5">
                                        <span class="fw-bold text-dark" style="font-size: 12.5px;">{{ $actStatus }}</span>
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 11px;">
                                        {{ $stats->note }} {{ $stats->user->name }}
                                    </small>
                                    <small class="text-muted" style="font-size: 10.5px;">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        {{ $stats->date->diffInHours(Carbon\Carbon::now()) > 24 ? $stats->date->format('d M Y, H:i') : $stats->date->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="mdi mdi-history text-muted fs-1 mb-2 d-block"></i>
                        <small class="text-muted">Belum ada aktivitas tercatat.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection()
@include('components.modal.payment.date ')
@include('components.modal.payment.pph')
@include('components.modal.payment.cost')
@include('components.modal.payment.confirm')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        $(".invoice-item-pph-label").on('keyup', function() {
            var input = $(this)
            var input_val = input.val();
            var original_len = input_val.length;
            input_val = formatNumber(input_val);
            input_val = input_val;
            input.val(input_val);
            var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
            console.log(nomorInt);
            $(`#pph`).val(nomorInt);
        });

        $(".invoice-item-cost-label").on('keyup', function() {
            var input = $(this)
            var input_val = input.val();
            var original_len = input_val.length;
            input_val = formatNumber(input_val);
            input_val = input_val;
            input.val(input_val);
            var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
            console.log(nomorInt);
            $(`#cost`).val(nomorInt);
        });

        $(document).on('click', '.confirm-payment', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Confirm this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Confirm it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('confirm-payment') }}/payment/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Confirmed!",
                                    text: "Your file has been Confirmed.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/payment-detail/payment/' +
                                        id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.unconfirm-payment', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to UnConfirm this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, UnConfirm it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('unconfirm-payment') }}/payment/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "UnConfirmed!",
                                    text: "Your file has been UnConfirmed.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/payment-detail/payment/' +
                                        id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
    </script>
@endpush
