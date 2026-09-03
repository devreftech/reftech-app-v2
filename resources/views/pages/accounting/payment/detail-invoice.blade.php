@extends('layouts.sales.app')
@section('title', 'Detail Invoice - ' . ($invoice->no_invoice ?? 'Invoice'))
@section('content')

@php
    $vat = ($quote->subtotal / 100) * $quote->tax;
    $totalPaid = $payment->sum('amount');
    $outstanding = max(0, $quote->harga_total - $totalPaid);
    $paidPercent = $quote->harga_total > 0 ? min(100, round(($totalPaid / $quote->harga_total) * 100)) : 0;

    // Payment Status Badge
    if ($outstanding <= 0 && $quote->harga_total > 0) {
        $statusBadge = 'bg-label-success';
        $statusText = 'LUNAS';
        $statusIcon = 'mdi-check-decagram';
    } elseif ($totalPaid > 0 && $outstanding > 0) {
        $statusBadge = 'bg-label-warning';
        $statusText = 'DIBAYAR SEBAGIAN (' . $paidPercent . '%)';
        $statusIcon = 'mdi-clock-outline';
    } else {
        $statusBadge = 'bg-label-danger';
        $statusText = 'BELUM DIBAYAR';
        $statusIcon = 'mdi-alert-circle-outline';
    }

    // Overdue calculation
    $dueDateFormatted = '-';
    $overdueText = 'Bukan Tempo / Lunas';
    $overdueBadge = 'bg-label-secondary';
    $overdueIcon = 'mdi-calendar-check-outline';

    foreach ($payment as $pay) {
        if ($pay->type == 'Tempo' && $pay->due_date) {
            $dueDateObj = \Carbon\Carbon::parse($pay->due_date);
            $dueDateFormatted = $dueDateObj->format('d M Y');
            $selisih = now()->startOfDay()->diffInDays($dueDateObj->startOfDay(), false);

            if ($outstanding <= 0) {
                $overdueText = 'Sudah Lunas';
                $overdueBadge = 'bg-label-success';
                $overdueIcon = 'mdi-check-circle-outline';
            } elseif ($selisih > 0) {
                $overdueText = $selisih . ' Hari Lagi';
                $overdueBadge = 'bg-label-info';
                $overdueIcon = 'mdi-timer-sand';
            } elseif ($selisih == 0) {
                $overdueText = 'Jatuh Tempo Hari Ini';
                $overdueBadge = 'bg-label-warning';
                $overdueIcon = 'mdi-alert-circle-outline';
            } else {
                $overdueText = 'Terlambat ' . abs($selisih) . ' Hari';
                $overdueBadge = 'bg-label-danger';
                $overdueIcon = 'mdi-alert-octagon-outline';
            }
            break;
        }
    }

    $companyName = $quote->pic?->client?->company ?? ($quote->client?->company ?? '-');
    $picName = $quote->pic?->name ?? '-';
    $picPhone = $quote->pic?->phone ?? '-';
    $picEmail = $quote->pic?->email ?? '-';
    $address = $quote->pic?->client?->address ?? ($quote->client?->address ?? '-');
    $npwp = $quote->pic?->client?->npwp ?? ($quote->client?->npwp ?? null);
    $salesName = $quote->sales?->name ?? '-';

    $isSparepart = $quote->type == 'Sparepart' || (isset($dQuote) && $dQuote->count() > 0 && (!isset($subQuote) || $subQuote->count() == 0));
    $itemCount = $isSparepart ? (isset($dQuote) ? $dQuote->count() : 0) : (isset($subQuote) ? $subQuote->sum(fn($s) => $s->detail ? $s->detail->count() : 0) : 0);
@endphp

<div class="container-fluid px-0 py-2">
    {{-- Header & Actions --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <h4 class="fw-bolder text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="mdi mdi-receipt-text-outline text-primary fs-3"></i>
                            {{ $invoice->no_invoice ?? 'Detail Invoice' }}
                        </h4>
                        <span class="badge {{ $statusBadge }} rounded-pill px-3 py-1 fw-bold fs-7 d-inline-flex align-items-center gap-1">
                            <i class="mdi {{ $statusIcon }}"></i> {{ $statusText }}
                        </span>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 small text-muted">
                            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('payment_index.invoice') }}" class="text-decoration-none">Account Receivable</a></li>
                            <li class="breadcrumb-item active text-muted">{{ $invoice->no_invoice }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('invoice.show', $invoice->id) }}" target="_blank" class="btn btn-primary btn-sm px-3 shadow-sm">
                        <i class="mdi mdi-printer-outline me-1"></i> Cetak Invoice
                    </a>
                    <a href="{{ route('payment_index.invoice') }}" class="btn btn-label-secondary btn-sm px-3">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Top 4 KPI Metrics --}}
    <div class="row g-3 mb-4">
        {{-- Grand Total --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px;">
                            <i class="mdi mdi-cash-multiple me-1"></i> Total Invoice
                        </span>
                        <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-receipt-text fs-6"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder text-primary fs-4 mb-1">
                        Rp {{ number_format($quote->harga_total, 0, ',', '.') }}
                    </h3>
                    <small class="text-muted" style="font-size: 11.5px;">
                        @if($quote->tax > 0)
                            Subtotal: Rp {{ number_format($quote->subtotal, 0, ',', '.') }} (Inc. PPN)
                        @else
                            Non-PPN
                        @endif
                    </small>
                </div>
            </div>
        </div>

        {{-- Total Terbayar --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 5px solid #28a745 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px;">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Sudah Dibayar
                        </span>
                        <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-cash-check fs-6"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder text-success fs-4 mb-1">
                        Rp {{ number_format($totalPaid, 0, ',', '.') }}
                    </h3>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <small class="text-muted" style="font-size: 11.5px;">{{ $payment->count() }} transaksi masuk</small>
                        <span class="badge bg-label-success py-0 px-1" style="font-size: 11px;">{{ $paidPercent }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sisa Outstanding --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, {{ $outstanding > 0 ? '#fff8f8 0%, #ffeded 100%' : '#f8f9fa 0%, #f1f3f5 100%' }}); border-left: 5px solid {{ $outstanding > 0 ? '#ff3e1d' : '#6c757d' }} !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold {{ $outstanding > 0 ? 'text-danger' : 'text-secondary' }} small" style="letter-spacing: .5px;">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> Sisa Outstanding
                        </span>
                        <div class="avatar avatar-xs {{ $outstanding > 0 ? 'bg-label-danger' : 'bg-label-secondary' }} rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-scale-balance fs-6"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder {{ $outstanding > 0 ? 'text-danger' : 'text-dark' }} fs-4 mb-1">
                        Rp {{ number_format($outstanding, 0, ',', '.') }}
                    </h3>
                    <small class="text-muted" style="font-size: 11.5px;">
                        {{ $outstanding > 0 ? 'Saldo piutang yang belum terbayar' : 'Seluruh piutang telah lunas' }}
                    </small>
                </div>
            </div>
        </div>

        {{-- Jatuh Tempo & Sales --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fffcf0 0%, #fff8e1 100%); border-left: 5px solid #ffab00 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-warning small" style="letter-spacing: .5px;">
                            <i class="mdi mdi-calendar-clock-outline me-1"></i> Jatuh Tempo
                        </span>
                        <div class="avatar avatar-xs bg-label-warning rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-account-tie-outline fs-6"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder text-dark fs-5 mb-1">
                        {{ $dueDateFormatted }}
                    </h3>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <span class="badge {{ $overdueBadge }} py-0 px-2" style="font-size: 11px;">
                            <i class="mdi {{ $overdueIcon }} me-1"></i>{{ $overdueText }}
                        </span>
                        <small class="text-muted fw-semibold text-truncate" style="font-size: 11.5px;" title="{{ $salesName }}">
                            {{ $salesName }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="row g-4 mb-4">
        {{-- LEFT COLUMN: Items & Payment Timeline (8 cols) --}}
        <div class="col-12 col-xl-8">
            {{-- Item Penawaran Table Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                            <i class="mdi mdi-format-list-bulleted fs-6"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Rincian Item Penawaran</h6>
                    </div>
                    <span class="badge bg-label-primary rounded-pill px-3">{{ $itemCount }} Item Terdaftar</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center text-muted fw-bold text-uppercase py-3" style="width: 40px; font-size: 11.5px;">#</th>
                                    <th class="text-muted fw-bold text-uppercase py-3" style="font-size: 11.5px;">Item &amp; Deskripsi</th>
                                    <th class="text-center text-muted fw-bold text-uppercase py-3" style="width: 100px; font-size: 11.5px;">Qty</th>
                                    <th class="text-end text-muted fw-bold text-uppercase py-3" style="width: 150px; font-size: 11.5px;">Harga Satuan</th>
                                    <th class="text-end text-muted fw-bold text-uppercase py-3" style="width: 160px; font-size: 11.5px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($isSparepart)
                                    @forelse ($dQuote as $index => $product)
                                        <tr>
                                            <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark mb-0.5">
                                                    {{ $product->equivalent->brand ?? '' }} {{ $product->equivalent->pn ?? '' }}
                                                </div>
                                                @if($product->detail_product)
                                                    <div class="text-muted small" style="font-size: 12px; white-space: pre-wrap; line-height: 1.4;">{{ $product->detail_product }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-label-secondary px-2 py-1 fw-semibold">
                                                    {{ $product->qty }} {{ $product->info_qty ?? 'Pcs' }}
                                                </span>
                                            </td>
                                            <td class="text-end text-muted">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end fw-bold text-dark">
                                                Rp {{ number_format($product->amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada rincian item.</td>
                                        </tr>
                                    @endforelse
                                @else
                                    @php $itemCounter = 0; @endphp
                                    @forelse ($subQuote as $subJudul)
                                        @if($subQuote->count() > 1 || !empty($subJudul->subtitle))
                                            <tr style="background-color: #f6f7fb;">
                                                <td colspan="5" class="fw-bold text-primary py-2 px-3">
                                                    <i class="mdi mdi-folder-outline me-1"></i> {{ $subJudul->subtitle }}
                                                </td>
                                            </tr>
                                        @endif
                                        @foreach ($subJudul->detail as $product)
                                            @php $itemCounter++; @endphp
                                            <tr>
                                                <td class="text-center fw-semibold text-muted">{{ $itemCounter }}</td>
                                                <td>
                                                    <div class="fw-bold text-dark mb-0.5">
                                                        {{ $product->product }}
                                                    </div>
                                                    @if($product->detail && $product->detail !== '-')
                                                        <div class="text-muted small" style="font-size: 12px; white-space: pre-wrap; line-height: 1.4;">{{ $product->detail }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-label-secondary px-2 py-1 fw-semibold">
                                                        {{ $product->qty }} {{ $product->info_qty ?? 'Unit' }}
                                                    </span>
                                                    @if($product->disc > 0)
                                                        <div class="badge bg-label-danger py-0 px-1 mt-1" style="font-size: 10px;">Disc {{ $product->disc }}%</div>
                                                    @endif
                                                </td>
                                                <td class="text-end text-muted">
                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                </td>
                                                <td class="text-end fw-bold text-dark">
                                                    Rp {{ number_format($product->amount, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada rincian item penawaran.</td>
                                        </tr>
                                    @endforelse
                                @endif
                            </tbody>
                            <tfoot class="border-top">
                                <tr>
                                    <td colspan="4" class="text-end fw-semibold text-muted py-2">Subtotal</td>
                                    <td class="text-end fw-bold text-dark py-2">Rp {{ number_format($quote->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @if ($quote->tax > 0)
                                    <tr>
                                        <td colspan="4" class="text-end fw-semibold text-muted py-2">
                                            <span class="badge bg-label-primary me-1 py-0">PPN 11%</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark py-2">Rp {{ number_format($vat, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                <tr style="background-color: #f8f9ff;">
                                    <td colspan="4" class="text-end fw-bolder text-primary py-3 fs-6">Grand Total Invoice</td>
                                    <td class="text-end fw-bolder text-primary py-3 fs-6">Rp {{ number_format($quote->harga_total, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Riwayat Pembayaran Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                            <i class="mdi mdi-history fs-6"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Riwayat Pembayaran &amp; Penerimaan Kas</h6>
                    </div>
                    <span class="badge bg-label-success rounded-pill px-3">{{ $payment->count() }} Transaksi Tercatat</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-muted fw-bold text-uppercase py-3" style="font-size: 11.5px;">No. Payment</th>
                                    <th class="text-muted fw-bold text-uppercase py-3" style="font-size: 11.5px;">Tanggal</th>
                                    <th class="text-muted fw-bold text-uppercase py-3" style="font-size: 11.5px;">Metode / Tipe</th>
                                    <th class="text-end text-muted fw-bold text-uppercase py-3" style="font-size: 11.5px;">Nominal Masuk</th>
                                    <th class="text-center text-muted fw-bold text-uppercase py-3" style="font-size: 11.5px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payment as $item)
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
                                            $badgeText = 'Draft / Batal';
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('payment_detail.payment', $item->id) }}" class="fw-bold text-primary text-decoration-none">
                                                <i class="mdi mdi-receipt me-1"></i>#PYMN-{{ $item->id }}
                                            </a>
                                            @if($item->note)
                                                <div class="text-muted small" style="font-size: 11.5px;">Note: {{ $item->note }}</div>
                                            @endif
                                        </td>
                                        <td class="text-muted">
                                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
                                        </td>
                                        <td>
                                            <span class="d-inline-flex align-items-center gap-1 text-dark fw-semibold">
                                                <i class="mdi mdi-bank-transfer text-primary"></i> {{ $item->type ?? 'Bank Transfer' }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-success fs-7">
                                            Rp {{ number_format($item->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                                <i class="mdi {{ $badgeIcon }}"></i> {{ $badgeText }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="avatar avatar-md mx-auto mb-2 bg-label-secondary rounded-circle d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-cash-remove fs-4 text-secondary"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">Belum Ada Transaksi Pembayaran</h6>
                                            <small class="text-muted">Riwayat penerimaan pembayaran invoice ini akan tercatat di tabel ini.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Customer, Metadata & Summary Cards (4 cols) --}}
        <div class="col-12 col-xl-4">
            {{-- Customer Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="mdi mdi-domain fs-6"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark">Informasi Pelanggan</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-circle bg-primary text-white fw-bold fs-5">
                                {{ strtoupper(substr($companyName, 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $companyName }}">{{ $companyName }}</h6>
                            <p class="text-muted mb-0 small" style="line-height: 1.4;">
                                <i class="mdi mdi-map-marker-outline text-danger me-1"></i>{{ $address }}
                            </p>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 d-flex flex-column gap-2 mb-3" style="font-size: 12.5px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="mdi mdi-account-outline me-1"></i>PIC:</span>
                            <span class="fw-semibold text-dark">{{ $picName }}</span>
                        </div>
                        @if($picPhone && $picPhone !== '-')
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="mdi mdi-phone-outline me-1"></i>Telepon:</span>
                                <span class="fw-semibold text-dark">{{ $picPhone }}</span>
                            </div>
                        @endif
                        @if($picEmail && $picEmail !== '-')
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="mdi mdi-email-outline me-1"></i>Email:</span>
                                <span class="fw-semibold text-dark text-truncate" style="max-width: 170px;" title="{{ $picEmail }}">{{ $picEmail }}</span>
                            </div>
                        @endif
                    </div>

                    @if ($npwp)
                        <div class="d-flex align-items-center gap-2 p-2 px-3 bg-label-info rounded-2" style="font-size: 12px;">
                            <i class="mdi mdi-card-account-details-outline fs-5"></i>
                            <span class="fw-semibold">NPWP: {{ $npwp }}</span>
                        </div>
                    @else
                        <div class="d-flex align-items-center gap-2 p-2 px-3 bg-label-secondary rounded-2" style="font-size: 12px;">
                            <i class="mdi mdi-alert-circle-outline fs-5"></i>
                            <span>NPWP Belum Dilengkapi</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Document & Contract Info Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="mdi mdi-file-document-outline fs-6"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark">Detail Dokumen</h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush" style="font-size: 13px;">
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted">No. Invoice</span>
                            <span class="fw-bold text-dark">{{ $invoice->no_invoice }}</span>
                        </li>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted">Tanggal Invoice</span>
                            <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</span>
                        </li>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted">Syarat / Terms</span>
                            <span class="badge bg-label-secondary fw-semibold">{{ $invoice->term ?? 'Normal' }}</span>
                        </li>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted">Sales Person</span>
                            <span class="fw-bold text-primary">{{ $salesName }}</span>
                        </li>
                        @if($quote->no_quotation)
                            <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0">
                                <span class="text-muted">No. Penawaran</span>
                                <span class="fw-semibold text-dark">{{ $quote->no_quotation }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Progress Status Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="mdi mdi-chart-pie fs-6"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark">Ringkasan Pelunasan</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-semibold text-muted">Status Pelunasan</span>
                            <span class="fw-bold text-primary">{{ $paidPercent }}%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $paidPercent }}%" aria-valuenow="{{ $paidPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 p-3 bg-light rounded-3" style="font-size: 13px;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Nilai Tagihan:</span>
                            <span class="fw-bold text-dark">Rp {{ number_format($quote->harga_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Dana Masuk:</span>
                            <span class="fw-bold text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                        </div>
                        <hr class="my-1">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold text-dark">Sisa Tagihan (AR):</span>
                            <span class="fw-bolder {{ $outstanding > 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($outstanding, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection()

@push('after-style')
<style>
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

