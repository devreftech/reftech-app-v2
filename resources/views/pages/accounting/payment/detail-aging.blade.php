@extends('layouts.sales.app')
@section('title', 'Invoice Aging')
@section('content')

@php
    $clientCompany = $isUnitQuotation ? $quote->client->company : $quote->pic->client->company;
    $clientNpwp    = $isUnitQuotation ? $quote->client->npwp : $quote->pic->client->npwp;
    $clientAddress = $isUnitQuotation ? $quote->client->address : $quote->pic->client->address;
    $clientInfo    = $isUnitQuotation ? $quote->client->info : $quote->pic->client->info;

    $invoiceTotal = $quote->harga_total;
    $paid         = $payment->level == 1 ? $payment->amount : 0;
    $outstanding  = max($invoiceTotal - $paid, 0);
    $isPaid       = $outstanding <= 0;

    // $diffDue > 0 = belum jatuh tempo (sisa hari), $diffDue < 0 = sudah lewat jatuh tempo (hari terlambat)
    if ($isPaid) {
        $statusColor = 'success'; $statusIcon = 'mdi-check-decagram-outline'; $statusText = 'Lunas';
    } elseif ($diffDue < 0) {
        $statusColor = 'danger'; $statusIcon = 'mdi-alert-circle-outline'; $statusText = 'Overdue';
    } elseif ($diffDue == 0) {
        $statusColor = 'warning'; $statusIcon = 'mdi-clock-alert-outline'; $statusText = 'Jatuh Tempo Hari Ini';
    } elseif ($diffDue <= 7) {
        $statusColor = 'warning'; $statusIcon = 'mdi-clock-outline'; $statusText = 'Segera Jatuh Tempo';
    } else {
        $statusColor = 'info'; $statusIcon = 'mdi-timer-sand'; $statusText = 'Current';
    }

    $daysPastDue = $diffDue < 0 ? abs($diffDue) : 0;

    $buckets = [
        'current' => ['label' => 'Current',    'sub' => 'Belum Jatuh Tempo', 'color' => 'info',   'active' => !$isPaid && $diffDue > 0,                      'amount' => (!$isPaid && $diffDue > 0) ? $outstanding : 0],
        'b1'      => ['label' => '1 - 30',      'sub' => 'Hari Terlambat',    'color' => 'danger', 'active' => !$isPaid && $diffDue <= 0 && $diffDue > -30,   'amount' => (!$isPaid && $diffDue <= 0 && $diffDue > -30) ? $outstanding : 0],
        'b2'      => ['label' => '31 - 60',     'sub' => 'Hari Terlambat',    'color' => 'danger', 'active' => !$isPaid && $diffDue <= -30 && $diffDue > -60, 'amount' => (!$isPaid && $diffDue <= -30 && $diffDue > -60) ? $outstanding : 0],
        'b3'      => ['label' => '61 - 90',     'sub' => 'Hari Terlambat',    'color' => 'danger', 'active' => !$isPaid && $diffDue <= -60 && $diffDue > -90, 'amount' => (!$isPaid && $diffDue <= -60 && $diffDue > -90) ? $outstanding : 0],
        'b4'      => ['label' => '> 90',        'sub' => 'Hari Terlambat',    'color' => 'danger', 'active' => !$isPaid && $diffDue <= -90,                   'amount' => (!$isPaid && $diffDue <= -90) ? $outstanding : 0],
    ];
@endphp

{{-- Breadcrumb --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-calendar-clock-outline text-primary"></i> Invoice Aging Detail
            <span class="badge bg-label-{{ $statusColor }} rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" style="font-size: 12px;">
                <i class="mdi {{ $statusIcon }}"></i> {{ $statusText }}
            </span>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 13px;">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment_index.aging') }}">Invoice Aging</a></li>
                <li class="breadcrumb-item active">{{ $invoice->no_invoice ?? '#' . $payment->id }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('payment_index.aging') }}" class="btn btn-sm btn-label-secondary rounded-pill px-3">
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
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="mdi mdi-file-document-outline fs-4"></i></span>
                </div>
                <div class="overflow-hidden">
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Invoice Number</small>
                    <a href="{{ $isUnitQuotation ? route('invoice.show_unit', $invoice->id) : route('invoice.show', $invoice->id) }}"
                        class="fw-bold text-dark text-truncate d-block text-decoration-none" style="font-size: 15px;">
                        {{ $invoice->no_invoice ?? '-' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-secondary"><i class="mdi mdi-receipt-text-outline fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Invoice Total</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;">Rp {{ number_format($invoiceTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-success"><i class="mdi mdi-cash-check fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Paid to Date</small>
                    <span class="fw-bold text-success" style="font-size: 15px;">Rp {{ number_format($paid, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-{{ $outstanding > 0 ? 'danger' : 'secondary' }} fs-4">
                        <i class="mdi mdi-cash-remove fs-4"></i>
                    </span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Outstanding</small>
                    <span class="fw-bold {{ $outstanding > 0 ? 'text-danger' : 'text-dark' }}" style="font-size: 15px;">Rp {{ number_format($outstanding, 0, ',', '.') }}</span>
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
                            <span class="badge bg-label-primary rounded-pill px-3 py-1">
                                <i class="mdi mdi-tag-outline me-1"></i> {{ $quote->no_quote }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoice Detail --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-calendar-text-outline text-primary"></i> Detail Invoice
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3" style="font-size: 13px;">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Invoice Date</small>
                        <span class="fw-semibold text-dark">{{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Invoice Due Date</small>
                        <span class="fw-semibold text-dark">{{ $payment->due_date ? \Carbon\Carbon::parse($payment->due_date)->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Terms</small>
                        <span class="fw-semibold text-dark">{{ $invoice->term ?? '-' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">{{ $diffDue < 0 ? 'Days Past Due' : 'Days Until Due' }}</small>
                        <span class="fw-bold {{ $diffDue < 0 ? 'text-danger' : 'text-dark' }}">
                            {{ $diffDue < 0 ? $daysPastDue . ' hari' : ($diffDue == 0 ? 'Jatuh tempo hari ini' : $diffDue . ' hari lagi') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Aging Buckets --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-chart-timeline-variant text-primary"></i> Aging Buckets
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-2">
                    @foreach ($buckets as $bucket)
                        <div class="col">
                            <div class="rounded p-3 h-100 text-center {{ $bucket['active'] ? 'bg-label-' . $bucket['color'] : 'bg-body-tertiary' }}" style="border: 1px solid {{ $bucket['active'] ? 'var(--bs-' . $bucket['color'] . ')' : 'transparent' }};">
                                <small class="d-block fw-semibold {{ $bucket['active'] ? 'text-' . $bucket['color'] : 'text-muted' }}" style="font-size: 12px;">{{ $bucket['label'] }}</small>
                                <small class="d-block {{ $bucket['active'] ? 'text-' . $bucket['color'] : 'text-muted' }} mb-2" style="font-size: 10.5px;">{{ $bucket['sub'] }}</small>
                                <span class="fw-bold d-block {{ $bucket['active'] ? 'text-' . $bucket['color'] : 'text-dark' }}" style="font-size: 13px;">
                                    Rp {{ number_format($bucket['amount'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Note Card --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-note-text-outline text-primary"></i> Catatan
                </h6>
            </div>
            <div class="card-body p-4">
                @if ($quote->note)
                    <p class="mb-0 text-dark" style="font-size: 13px; line-height: 1.6; white-space: pre-line;">{{ $quote->note }}</p>
                @else
                    <p class="mb-0 text-muted" style="font-size: 13px;">Tidak ada catatan.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="col-xl-4">
        {{-- Aging Status Card --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-shield-alert-outline text-primary"></i> Status Aging
                </h6>
            </div>
            <div class="card-body p-4 text-center">
                <div class="avatar avatar-lg mx-auto mb-3">
                    <span class="avatar-initial rounded-circle bg-label-{{ $statusColor }}">
                        <i class="mdi {{ $statusIcon }} fs-3"></i>
                    </span>
                </div>
                <h5 class="fw-bold text-{{ $statusColor }} mb-1">{{ $statusText }}</h5>
                <small class="text-muted">
                    @if ($isPaid)
                        Sudah dibayar penuh
                    @else
                        {{ $diffDue < 0 ? 'Terlambat ' . $daysPastDue . ' hari dari jatuh tempo' : ($diffDue == 0 ? 'Jatuh tempo hari ini' : 'Jatuh tempo dalam ' . $diffDue . ' hari') }}
                    @endif
                </small>
            </div>
        </div>

        {{-- Reminder Timeline --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-bell-outline text-primary"></i> Reminder Timeline
                </h6>
                <button type="button" class="btn btn-xs btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addReminder">
                    <i class="mdi mdi-plus me-1"></i> Reminder
                </button>
            </div>
            <div class="card-body p-4">
                @forelse ($reminder as $item)
                    <div class="d-flex gap-3 mb-3 position-relative">
                        @if (!$loop->last)
                            <div class="position-absolute" style="left: 15px; top: 32px; bottom: -12px; width: 2px; background: #e7e7e8;"></div>
                        @endif
                        <div class="avatar avatar-sm flex-shrink-0">
                            @if ($item->user && $item->user->image)
                                <img src="{{ url('') . '/' . $item->user->image }}" class="rounded-circle" alt="{{ $item->user->name }}">
                            @else
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="mdi mdi-account-outline" style="font-size: 14px;"></i>
                                </span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-dark" style="font-size: 12.5px;">{{ $item->user->name ?? 'User' }}</span>
                            </div>
                            <small class="text-muted d-block" style="font-size: 11px;">Follow Up ke-{{ $item->status }}</small>
                            <p class="mb-1 mt-1" style="font-size: 12px; white-space: pre-line;">{{ $item->reminder }}</p>
                            <small class="text-muted" style="font-size: 10.5px;">
                                <i class="mdi mdi-clock-outline me-1"></i>
                                {{ $item->created_at->diffInHours(\Carbon\Carbon::now()) > 24 ? $item->created_at->format('d M Y, H:i') : $item->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-3">
                        <i class="mdi mdi-bell-off-outline text-muted fs-1 mb-2 d-block"></i>
                        <small class="text-muted">Belum ada reminder pada payment ini.</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection()
@include('components.modal.payment.add-reminder')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
    <script>
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
                                    window.location.href = '/payment-detail/payment/' + id;
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
