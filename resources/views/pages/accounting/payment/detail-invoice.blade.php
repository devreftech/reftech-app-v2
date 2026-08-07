@extends('layouts.sales.app')
@section('title', 'Detail Invoice')
@section('content')

@php
    $vat = ($quote->subtotal / 100) * $quote->tax;
    $outstanding = $quote->harga_total - @$payment->sum('amount');
    $paidPercent = $quote->harga_total > 0 ? round(($payment->sum('amount') / $quote->harga_total) * 100) : 0;

    // Overdue calculation
    $overdueText = '-';
    $overdueClass = 'text-muted';
    $overdueIcon = 'mdi-clock-outline';
    foreach ($payment as $pay) {
        if ($pay->type == 'Tempo' && $pay->due_date) {
            $selisih = now()->diffInDays(\Carbon\Carbon::parse($pay->due_date), false);
            if ($selisih > 0) {
                $overdueText = $selisih . ' hari lagi';
                $overdueClass = 'text-info';
                $overdueIcon = 'mdi-timer-sand';
            } elseif ($selisih == 0) {
                $overdueText = 'Jatuh tempo hari ini';
                $overdueClass = 'text-warning';
                $overdueIcon = 'mdi-alert-circle-outline';
            } else {
                $overdueText = 'Terlambat ' . abs($selisih) . ' hari';
                $overdueClass = 'text-danger';
                $overdueIcon = 'mdi-alert-outline';
            }
            break;
        }
    }
@endphp

{{-- Breadcrumb --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-receipt-text-outline text-primary"></i> Detail Invoice
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 13px;">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment_index.invoice') }}">Account Receivable</a></li>
                <li class="breadcrumb-item active">{{ $invoice->no_invoice }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('invoice.show', $invoice->id) }}" target="_blank" class="btn btn-sm btn-label-primary rounded-pill px-3">
            <i class="mdi mdi-printer-outline me-1"></i> Cetak Invoice
        </a>
        <a href="{{ route('payment_index.invoice') }}" class="btn btn-sm btn-label-secondary rounded-pill px-3">
            <i class="mdi mdi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

{{-- Top Info Metric Cards --}}
<div class="row g-3 mb-4">
    {{-- No Invoice --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-primary">
                        <i class="mdi mdi-file-document-outline fs-4"></i>
                    </span>
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">No. Invoice</small>
                    <a href="{{ route('invoice.show', $invoice->id) }}" class="fw-bold text-dark text-truncate d-block" style="font-size: 14px;" target="_blank">
                        {{ $invoice->no_invoice }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- Invoice Date --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-info">
                        <i class="mdi mdi-calendar-check-outline fs-4"></i>
                    </span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Tanggal Invoice</small>
                    <span class="fw-bold text-dark" style="font-size: 14px;">{{ Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
    {{-- Due Date --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-warning">
                        <i class="mdi mdi-calendar-clock-outline fs-4"></i>
                    </span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Jatuh Tempo</small>
                    <span class="fw-bold text-dark" style="font-size: 14px;">
                        {{ @$payment[0]->type == 'Tempo' && @$payment[0]->due_date ? \Carbon\Carbon::parse($payment[0]->due_date)->format('d M Y') : '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    {{-- Terms --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-secondary">
                        <i class="mdi mdi-handshake-outline fs-4"></i>
                    </span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Terms / Syarat</small>
                    <span class="fw-bold text-dark" style="font-size: 14px;">{{ $invoice->term ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- LEFT: Customer Info + Financial Summary --}}
    <div class="col-xl-8">
        {{-- Customer Card --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-domain text-primary"></i> Informasi Customer
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="avatar avatar-lg flex-shrink-0">
                        <span class="avatar-initial rounded bg-primary text-white fw-bold" style="font-size: 18px;">
                            {{ strtoupper(substr($invoice->quote->pic->client->company ?? 'C', 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold text-dark mb-1">{{ $invoice->quote->pic->client->company }}</h5>
                        <p class="text-muted mb-2" style="font-size: 13px;">
                            <i class="mdi mdi-map-marker-outline me-1"></i> {{ $invoice->quote->pic->client->address }}
                        </p>
                        @if ($invoice->quote->pic->client->npwp)
                            <span class="badge bg-label-info rounded-pill px-3 py-1">
                                <i class="mdi mdi-card-account-details-outline me-1"></i> NPWP: {{ $invoice->quote->pic->client->npwp }}
                            </span>
                        @else
                            <span class="badge bg-label-danger rounded-pill px-3 py-1">
                                <i class="mdi mdi-alert-circle-outline me-1"></i> NPWP Belum Diisi
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Quotation Items Table --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-format-list-bulleted text-primary"></i> Detail Item Penawaran
                </h6>
                <span class="badge bg-label-primary rounded-pill">{{ $dQuote->count() }} Item</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr style="font-size: 12px;">
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3" style="width: 5%;">#</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3">Item</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3">Deskripsi</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-center">Qty</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">Harga</th>
                                <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dQuote as $index => $product)
                                <tr style="font-size: 13px;">
                                    <td class="align-top px-3 text-muted">{{ $index + 1 }}</td>
                                    <td class="align-top px-3 fw-semibold text-dark">
                                        {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
                                    </td>
                                    <td class="align-top px-3 text-muted" style="max-width: 280px;">
                                        <span style="white-space: pre-wrap; font-size: 12px;">{{ $product->detail_product }}</span>
                                    </td>
                                    <td class="align-top px-3 text-center">{{ $product->qty }} {{ $product->info_qty }}</td>
                                    <td class="align-top px-3 text-end">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="align-top px-3 text-end fw-semibold">Rp {{ number_format($product->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-top">
                                <td colspan="5" class="text-end fw-semibold text-muted px-3 py-2" style="font-size: 13px;">Subtotal</td>
                                <td class="text-end fw-bold text-dark px-3 py-2" style="font-size: 13px;">Rp {{ number_format($quote->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @if ($quote->tax > 0)
                                <tr>
                                    <td colspan="5" class="text-end fw-semibold text-muted px-3 py-2" style="font-size: 13px;">PPN 11%</td>
                                    <td class="text-end fw-bold text-dark px-3 py-2" style="font-size: 13px;">Rp {{ number_format($vat, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr class="bg-primary bg-opacity-10">
                                <td colspan="5" class="text-end fw-bold text-primary px-3 py-2.5" style="font-size: 14px;">Grand Total</td>
                                <td class="text-end fw-bold text-primary px-3 py-2.5" style="font-size: 14px;">Rp {{ number_format($quote->harga_total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Financial Summary Sidebar --}}
    <div class="col-xl-4">
        {{-- Financial Summary --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-chart-donut text-primary"></i> Ringkasan Keuangan
                </h6>
            </div>
            <div class="card-body p-4">
                {{-- Progress Bar --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-semibold text-dark">Progress Pembayaran</small>
                        <small class="fw-bold text-primary">{{ $paidPercent }}%</small>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar bg-primary rounded-pill" style="width: {{ $paidPercent }}%"></div>
                    </div>
                </div>

                {{-- Summary Items --}}
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom" style="font-size: 13px;">
                        <span class="text-muted d-flex align-items-center gap-1.5">
                            <i class="mdi mdi-receipt-text-outline text-secondary"></i> Total Invoice
                        </span>
                        <span class="fw-bold text-dark">Rp {{ number_format($quote->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if ($quote->tax > 0)
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom" style="font-size: 13px;">
                            <span class="text-muted d-flex align-items-center gap-1.5">
                                <i class="mdi mdi-percent-outline text-secondary"></i> PPN 11%
                            </span>
                            <span class="fw-bold text-dark">Rp {{ number_format($vat, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom" style="font-size: 13px;">
                        <span class="text-muted d-flex align-items-center gap-1.5">
                            <i class="mdi mdi-cash-multiple text-secondary"></i> Grand Total
                        </span>
                        <span class="fw-bold text-primary" style="font-size: 14px;">Rp {{ number_format($quote->harga_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom" style="font-size: 13px;">
                        <span class="text-muted d-flex align-items-center gap-1.5">
                            <i class="mdi mdi-check-circle-outline text-success"></i> Sudah Dibayar
                        </span>
                        <span class="fw-bold text-success">Rp {{ number_format($payment->sum('amount'), 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="font-size: 13px;">
                        <span class="text-muted d-flex align-items-center gap-1.5">
                            <i class="mdi mdi-alert-circle-outline {{ $outstanding > 0 ? 'text-danger' : 'text-success' }}"></i> Sisa Outstanding
                        </span>
                        <span class="fw-bold {{ $outstanding > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 14px;">
                            Rp {{ number_format($outstanding, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Overdue Status Card --}}
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded-circle {{ $overdueClass === 'text-danger' ? 'bg-label-danger' : ($overdueClass === 'text-warning' ? 'bg-label-warning' : 'bg-label-info') }}">
                            <i class="mdi {{ $overdueIcon }} fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Status Jatuh Tempo</small>
                        <span class="fw-bold {{ $overdueClass }}" style="font-size: 14px;">{{ $overdueText }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sales Info --}}
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded-circle bg-label-primary">
                            <i class="mdi mdi-account-outline fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Sales Person</small>
                        <span class="fw-bold text-dark" style="font-size: 14px;">{{ $quote->sales->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Payment History --}}
<div class="card border-0 shadow-sm overflow-hidden mb-4">
    <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="mdi mdi-history text-primary"></i> Riwayat Pembayaran
        </h6>
        <span class="badge bg-label-primary rounded-pill">{{ $payment->count() }} Transaksi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr style="font-size: 12px;">
                        <th class="text-uppercase fw-bold text-muted py-2.5 px-3">No. Payment</th>
                        <th class="text-uppercase fw-bold text-muted py-2.5 px-3">Tanggal</th>
                        <th class="text-uppercase fw-bold text-muted py-2.5 px-3">Metode</th>
                        <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-end">Nominal</th>
                        <th class="text-uppercase fw-bold text-muted py-2.5 px-3">Catatan</th>
                        <th class="text-uppercase fw-bold text-muted py-2.5 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payment as $item)
                        <tr style="font-size: 13px;">
                            <td class="align-middle px-3">
                                <a href="{{ route('payment_detail.payment', $item->id) }}" class="fw-semibold text-primary text-decoration-none">
                                    #PYMN-{{ $item->id }}
                                </a>
                            </td>
                            <td class="align-middle px-3 text-muted">
                                {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                            </td>
                            <td class="align-middle px-3">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-bank-transfer text-primary"></i> Bank Transfer
                                </span>
                            </td>
                            <td class="align-middle px-3 text-end fw-semibold text-dark">
                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                            <td class="align-middle px-3 text-muted" style="max-width: 200px;">
                                {{ $item->note ?? '-' }}
                            </td>
                            @php
                                if ($item->level == 0) {
                                    if ($item->file == null) {
                                        $badgeClass = 'bg-label-danger';
                                        $badgeIcon = 'mdi-clock-outline';
                                        $badgeText = 'Menunggu Pembayaran';
                                    } else {
                                        $badgeClass = 'bg-label-warning';
                                        $badgeIcon = 'mdi-eye-check-outline';
                                        $badgeText = 'Menunggu Verifikasi';
                                    }
                                } elseif ($item->level == 1) {
                                    $badgeClass = 'bg-label-success';
                                    $badgeIcon = 'mdi-check-circle-outline';
                                    $badgeText = 'Terverifikasi';
                                } else {
                                    $badgeClass = 'bg-label-secondary';
                                    $badgeIcon = 'mdi-minus-circle-outline';
                                    $badgeText = 'Belum Di-payment';
                                }
                            @endphp
                            <td class="align-middle px-3 text-center">
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                    <i class="mdi {{ $badgeIcon }}"></i> {{ $badgeText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="avatar avatar-md mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-secondary">
                                        <i class="mdi mdi-cash-remove fs-4 text-secondary"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold text-muted mb-1" style="font-size: 13px;">Belum Ada Pembayaran</h6>
                                <small class="text-muted">Riwayat pembayaran akan muncul di sini setelah transaksi tercatat.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Bottom Summary Strip --}}
    @if ($payment->count() > 0)
        <div class="card-footer bg-body-tertiary border-top px-4 py-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-circle bg-success"><i class="mdi mdi-check-all"></i></span>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px;">Total Terbayar</small>
                            <span class="fw-bold text-success" style="font-size: 14px;">Rp {{ number_format($payment->sum('amount'), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-circle {{ $outstanding > 0 ? 'bg-danger' : 'bg-success' }}">
                                <i class="mdi {{ $outstanding > 0 ? 'mdi-alert-outline' : 'mdi-check-circle-outline' }}"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px;">Sisa Outstanding</small>
                            <span class="fw-bold {{ $outstanding > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 14px;">Rp {{ number_format($outstanding, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-circle {{ $overdueClass === 'text-danger' ? 'bg-danger' : ($overdueClass === 'text-warning' ? 'bg-warning' : 'bg-info') }}">
                                <i class="mdi {{ $overdueIcon }}"></i>
                            </span>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px;">Status Jatuh Tempo</small>
                            <span class="fw-bold {{ $overdueClass }}" style="font-size: 14px;">{{ $overdueText }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection()

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
@endpush
