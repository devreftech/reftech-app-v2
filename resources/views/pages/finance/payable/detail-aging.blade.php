@extends('layouts.sales.app')
@section('title', 'Detail Aging Hutang #' . ($product->invoice ?: $product->no_product_in))

@section('content')
    {{-- Page Breadcrumb & Quick Actions Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Account Payable / <a href="{{ route('payable.index_aging') }}" class="text-muted text-decoration-none">Aging Report</a> /</span>
                <span class="text-primary">{{ $product->invoice ?: $product->no_product_in }}</span>
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-clock-time-four-outline me-1"></i> Analisis umur hutang dagang, jatuh tempo, tingkat risiko &amp; riwayat pembayaran
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('payable.index_aging') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Aging
            </a>
            <a href="{{ route('payable.show_invoice', $product->id) }}" class="btn btn-label-primary btn-sm" title="Buka Detail Faktur">
                <i class="mdi mdi-file-document-outline me-1"></i> Invoice
            </a>
            <a href="{{ route('payable.show_receipt', $product->id) }}" class="btn btn-label-info btn-sm" title="Buka Payment Receipt">
                <i class="mdi mdi-receipt-text-check-outline me-1"></i> Payment Receipt
            </a>
            @if($product->id_supplier)
                <a href="{{ route('payable.statement', ['supplier_id' => $product->id_supplier]) }}" class="btn btn-label-dark btn-sm" title="Buka Kartu Hutang Supplier">
                    <i class="mdi mdi-book-open-outline me-1"></i> Kartu Hutang
                </a>
            @endif
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="mdi mdi-cash-plus me-1"></i> Catat Pembayaran
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="mdi mdi-printer-outline me-1"></i> Cetak
            </button>
        </div>
    </div>

    {{-- Hero Aging Analysis & Risk Level Banner --}}
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
        <div class="card-body p-4">
            <div class="row align-items-center justify-content-between g-3">
                <div class="col-12 col-lg-7">
                    <div class="d-flex align-items-start gap-3">
                        <div class="avatar avatar-lg rounded-circle p-2 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                             style="width: 56px; height: 56px; background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); box-shadow: 0 4px 16px rgba(79, 70, 229, 0.25);">
                            <i class="mdi mdi-timer-sand fs-2"></i>
                        </div>
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <span class="badge {{ $bracketBadge }} px-3 py-1 fw-bold fs-7" style="letter-spacing: 0.3px;">
                                    <i class="mdi mdi-chart-timeline-variant me-1"></i> Kategori Umur: {{ $bracketLabel }}
                                </span>
                                <span class="{{ $riskBadge }} px-3 py-1 fw-semibold fs-7">
                                    Tingkat Risiko: {{ $riskLevel }}
                                </span>
                                @if ($product->accept == 1 || $remaining <= 0)
                                    <span class="badge bg-label-success px-3 py-1 fw-semibold fs-7">
                                        <i class="mdi mdi-check-decagram me-1"></i> LUNAS (PAID)
                                    </span>
                                @elseif ($product->accept == 2)
                                    <span class="badge bg-label-warning px-3 py-1 fw-semibold fs-7">
                                        <i class="mdi mdi-progress-clock me-1"></i> PARTIAL
                                    </span>
                                @else
                                    <span class="badge bg-label-danger px-3 py-1 fw-semibold fs-7">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i> UNPAID
                                    </span>
                                @endif
                            </div>
                            <h4 class="fw-bolder mb-1 text-dark">
                                Usia Hutang: <span class="text-primary">{{ $ageDays }} Hari</span>
                                <small class="text-muted fw-normal fs-6">
                                    (sejak {{ $product->date_invoice ? Carbon\Carbon::parse($product->date_invoice)->format('d F Y') : ($product->date ? Carbon\Carbon::parse($product->date)->format('d F Y') : '-') }})
                                </small>
                            </h4>
                            <p class="text-muted mb-0 small">
                                <i class="mdi mdi-information-outline text-info me-1"></i> {{ $riskDesc }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-5 text-lg-end">
                    <div class="p-3 bg-white border rounded-3 d-inline-block text-start w-100 shadow-none" style="max-width: 420px;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-semibold">Status Jatuh Tempo:</span>
                            <span class="badge {{ $dueStatusBadge }} rounded-pill px-3 py-1 fw-semibold">
                                {{ $dueStatusText }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span>Tgl Jatuh Tempo:</span>
                            <span class="fw-bold text-dark">
                                {{ $dueDate ? Carbon\Carbon::parse($dueDate)->format('d F Y') : ($product->date_payment ? Carbon\Carbon::parse($product->date_payment)->format('d F Y') : 'Sesuai Kesepakatan / COD') }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small text-muted mt-1">
                            <span>Termin Pembayaran (TOP):</span>
                            <span class="fw-bold text-dark">
                                {{ $product->purchaseOrder?->payment_type ? $product->purchaseOrder->payment_type . ' (' . ($product->purchaseOrder->top_days ?: '0') . ' Hari)' : ($product->top ?: 'Standard') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Summary Metric Cards Grid --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Nilai Faktur --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small text-uppercase fw-semibold">Total Tagihan (Faktur)</span>
                        <div class="avatar avatar-xs bg-label-primary rounded">
                            <i class="mdi mdi-file-invoice-dollar fs-5"></i>
                        </div>
                    </div>
                    <h4 class="fw-bolder text-dark mb-1">Rp {{ number_format($total, 0, ',', '.') }}</h4>
                    <small class="text-muted">Total kewajiban awal AP</small>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Sudah Terbayar --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small text-uppercase fw-semibold">Sudah Dibayar (Paid)</span>
                        <div class="avatar avatar-xs bg-label-success rounded">
                            <i class="mdi mdi-cash-check fs-5"></i>
                        </div>
                    </div>
                    <h4 class="fw-bolder text-success mb-1">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h4>
                    <small class="text-muted">{{ count($payments) }} kali transaksi pembayaran</small>
                </div>
            </div>
        </div>

        {{-- Card 3: Sisa Hutang (Outstanding) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 {{ $remaining > 0 ? 'bg-label-danger-subtle' : '' }}">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small text-uppercase fw-semibold">Sisa Hutang (Outstanding)</span>
                        <div class="avatar avatar-xs bg-label-danger rounded">
                            <i class="mdi mdi-alert-octagon fs-5"></i>
                        </div>
                    </div>
                    <h4 class="fw-bolder text-danger mb-1">Rp {{ number_format($remaining, 0, ',', '.') }}</h4>
                    <small class="text-danger fw-semibold">
                        {{ $remaining <= 0 ? 'Hutang telah selesai dilunasi' : 'Wajib diselesaikan sebelum denda' }}
                    </small>
                </div>
            </div>
        </div>

        {{-- Card 4: Progress Pelunasan --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted small text-uppercase fw-semibold">Progress Pelunasan</span>
                            <span class="fw-bold text-dark fs-6">{{ $percentPaid }}%</span>
                        </div>
                        <div class="progress mb-2" style="height: 10px; border-radius: 5px;">
                            <div class="progress-bar {{ $percentPaid == 100 ? 'bg-success' : ($percentPaid > 0 ? 'bg-warning' : 'bg-danger') }}"
                                 role="progressbar" style="width: {{ $percentPaid }}%;" aria-valuenow="{{ $percentPaid }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Status:</small>
                        @if ($product->accept == 1 || $remaining <= 0)
                            <span class="badge bg-success py-1 px-2">PAID</span>
                        @elseif ($product->accept == 2)
                            <span class="badge bg-warning py-1 px-2">PARTIAL</span>
                        @else
                            <span class="badge bg-danger py-1 px-2">UNPAID</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Two Columns: Vendor Info & Transaction Documents --}}
    <div class="row g-3 mb-4">
        {{-- Left: Vendor Info --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-domain text-primary me-2 fs-5"></i> Informasi Supplier / Vendor
                    </h6>
                    @if($product->id_supplier)
                        <a href="{{ route('payable.statement', ['supplier_id' => $product->id_supplier]) }}" class="btn btn-xs btn-label-primary">
                            <i class="mdi mdi-book-open-outline me-1"></i> Buka Kartu Hutang
                        </a>
                    @endif
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                            <tr>
                                <td style="width: 140px;" class="text-muted">Nama Supplier</td>
                                <td class="fw-bold text-dark">: {{ $product->supp->supplier ?? $product->supplier ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NPWP Vendor</td>
                                <td class="text-dark">: {{ $product->supp->npwp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kategori</td>
                                <td>: 
                                    @if(strtolower($product->info ?? '') == 'import')
                                        <span class="badge bg-label-info rounded-pill px-2 py-1"><i class="mdi mdi-airplane me-1"></i>Import</span>
                                    @else
                                        <span class="badge bg-label-primary rounded-pill px-2 py-1"><i class="mdi mdi-map-marker-radius-outline me-1"></i>{{ $product->info ?: 'Lokal' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Telepon / Kontak</td>
                                <td class="text-dark">: {{ $product->supp->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Alamat</td>
                                <td class="text-dark">: {{ $product->supp->address ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Transaction Reference & Authorization --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-file-document-outline text-primary me-2 fs-5"></i> Dokumen &amp; Otorisasi Faktur
                    </h6>
                    <div class="d-flex gap-1">
                        @if ($product->accept == 0)
                            <button type="button" class="btn btn-xs btn-label-warning" data-bs-toggle="modal" data-bs-target="#editDate">
                                <i class="mdi mdi-calendar-edit me-1"></i> Ubah Tanggal
                            </button>
                            <button type="button" class="btn btn-xs btn-success accept-product" data-id="{{ $product->id }}">
                                <i class="mdi mdi-check-decagram me-1"></i> Set Lunas (PAID)
                            </button>
                        @else
                            <button type="button" class="btn btn-xs btn-label-danger unconfirm-product" data-id="{{ $product->id }}">
                                <i class="mdi mdi-close-circle-outline me-1"></i> Batalkan Lunas
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                            <tr>
                                <td style="width: 160px;" class="text-muted">No. Invoice Supplier</td>
                                <td class="fw-bold text-primary">: {{ $product->invoice ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. Surat Jalan (DO)</td>
                                <td class="text-dark">: {{ $product->no_do ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. Purchase Order (PO)</td>
                                <td class="text-dark">: 
                                    @if($product->purchaseOrder)
                                        <a href="{{ url('/purchase/' . $product->purchaseOrder->id) }}" class="fw-semibold text-decoration-none">
                                            {{ $product->purchaseOrder->no_po }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Faktur</td>
                                <td class="text-dark">: {{ $product->date_invoice ? Carbon\Carbon::parse($product->date_invoice)->format('d-m-Y') : ($product->date ? Carbon\Carbon::parse($product->date)->format('d-m-Y') : '-') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Jatuh Tempo</td>
                                <td class="fw-bold text-danger">: {{ $dueDate ? Carbon\Carbon::parse($dueDate)->format('d-m-Y') : ($product->date_payment ? Carbon\Carbon::parse($product->date_payment)->format('d-m-Y') : '-') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Dibuat Oleh</td>
                                <td class="text-dark">: {{ $product->creator->name ?? 'System/Admin' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Items Table (Rincian Barang Masuk) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="mdi mdi-package-variant-closed text-primary me-2 fs-5"></i> Rincian Barang Pembelian (Goods Receipt Items)
            </h6>
            <span class="badge bg-label-secondary">{{ count($detProduct) }} Items</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center fw-semibold text-dark">#</th>
                        <th class="fw-semibold text-dark">Nama Barang / Part Number</th>
                        <th class="fw-semibold text-dark">Deskripsi &amp; Spesifikasi</th>
                        <th style="width: 100px;" class="text-center fw-semibold text-dark">Gudang</th>
                        <th style="width: 100px;" class="text-center fw-semibold text-dark">Qty</th>
                        <th style="width: 150px;" class="text-end fw-semibold text-dark">Harga Modal</th>
                        <th style="width: 160px;" class="text-end fw-semibold text-dark">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                    @forelse ($detProduct as $products)
                        @php $no++; @endphp
                        <tr>
                            <td class="text-center align-middle text-muted">{{ $no }}</td>
                            <td class="align-middle fw-bold text-dark">
                                {{ $products->detailProduct?->replacement ?? $products->part_number ?? '-' }}
                            </td>
                            <td class="align-middle text-secondary" style="font-size: 12px;">
                                {{ $products->detailProduct?->product?->description ?? '-' }}
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge bg-label-info">{{ $products->warehouse ?? 'BDG' }}</span>
                            </td>
                            <td class="text-center align-middle fw-semibold text-dark">
                                {{ $products->qty }} {{ $products->detailProduct?->product?->unit ?? 'Unit' }}
                            </td>
                            <td class="text-end align-middle text-dark">
                                Rp {{ number_format($products->modal, 0, ',', '.') }}
                            </td>
                            <td class="text-end align-middle fw-bold text-dark">
                                Rp {{ number_format($products->amount ?: ($products->qty * $products->modal), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Tidak ada rincian barang untuk transaksi ini.
                            </td>
                        </tr>
                    @endforelse
                    <tr class="table-light fw-bold">
                        <td colspan="6" class="text-end text-dark">Subtotal Pembelian:</td>
                        <td class="text-end text-dark">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                    @if($product->ppn > 0)
                        <tr class="table-light">
                            <td colspan="6" class="text-end text-muted">PPN:</td>
                            <td class="text-end text-dark font-monospace">Rp {{ number_format($product->ppn, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($product->pph > 0)
                        <tr class="table-light">
                            <td colspan="6" class="text-end text-muted">PPh:</td>
                            <td class="text-end text-danger font-monospace">- Rp {{ number_format($product->pph, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr class="table-light fw-bold fs-6">
                        <td colspan="6" class="text-end text-dark">Total Hutang Akhir:</td>
                        <td class="text-end text-primary">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat Pembayaran & Bukti Transfer Table --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="mdi mdi-history text-primary me-2 fs-5"></i> Riwayat Pembayaran / Cicilan &amp; Bukti Transfer
            </h6>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="mdi mdi-plus me-1"></i> Catat Pembayaran
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 140px;" class="fw-semibold text-dark">No. Bayar</th>
                        <th style="width: 110px;" class="fw-semibold text-dark">Tanggal</th>
                        <th class="fw-semibold text-dark">Akun Bank / Kas</th>
                        <th style="width: 120px;" class="fw-semibold text-dark">Metode</th>
                        <th style="width: 150px;" class="fw-semibold text-dark text-end">Nominal Bayar</th>
                        <th class="fw-semibold text-dark">Catatan</th>
                        <th style="width: 110px;" class="fw-semibold text-dark text-center">Bukti Transfer</th>
                        <th style="width: 80px;" class="fw-semibold text-dark text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $pay)
                        <tr>
                            <td class="fw-bold text-primary">{{ $pay->payment_number }}</td>
                            <td>{{ Carbon\Carbon::parse($pay->date)->format('d-m-Y') }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $pay->bank->bank ?? 'Kas/Bank' }}</div>
                                <small class="text-muted">{{ $pay->bank->no_rek ?? '-' }}</small>
                            </td>
                            <td><span class="badge bg-label-info rounded-pill px-2 py-1">{{ $pay->payment_method }}</span></td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($pay->amount, 0, ',', '.') }}
                            </td>
                            <td>{{ $pay->note ?: '-' }}</td>
                            <td class="text-center">
                                @if($pay->proof_file)
                                    <a href="{{ asset('storage/' . $pay->proof_file) }}" target="_blank" class="btn btn-xs btn-label-info" data-bs-toggle="tooltip" title="Lihat Bukti Transfer">
                                        <i class="mdi mdi-file-image-outline me-1"></i> Bukti
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-label-danger btn-delete-payment" data-id="{{ $pay->id }}" data-amount="{{ number_format($pay->amount, 0, ',', '.') }}" data-bs-toggle="tooltip" title="Hapus Pembayaran">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="mdi mdi-cash-remove fs-3 d-block mb-1 text-secondary"></i>
                                Belum ada transaksi cicilan atau pembayaran yang dicatat untuk faktur ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL: Catat Pembayaran / Cicilan --}}
    <div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('payable.store_payment', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title text-white fw-bold" id="recordPaymentModalLabel">
                            <i class="mdi mdi-cash-plus me-1"></i> Catat Pembayaran Hutang
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-info py-2 px-3 small d-flex justify-content-between align-items-center mb-3">
                            <span>Sisa Hutang Saat Ini:</span>
                            <strong class="fs-6">Rp {{ number_format($remaining, 0, ',', '.') }}</strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Akun Kas / Bank Pengirim <span class="text-danger">*</span></label>
                            <select name="id_bank" class="form-select select2-basic" required>
                                <option value="">-- Pilih Akun Bank / Kas --</option>
                                @foreach($banks as $b)
                                    <option value="{{ $b->id }}">
                                        {{ $b->bank }} - {{ $b->no_rek }} (Saldo: Rp {{ number_format($b->saldo, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Saldo rekening akan otomatis berkurang sesuai nominal yang dibayarkan.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metode Pembayaran</label>
                            <select name="payment_method" class="form-select">
                                <option value="Bank Transfer" selected>Bank Transfer</option>
                                <option value="Tunai / Cash">Tunai / Cash</option>
                                <option value="Cek / Giro">Cek / Giro</option>
                                <option value="Kartu Kredit Corporate">Kartu Kredit Corporate</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nominal Pembayaran (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="amount" class="form-control" value="{{ $remaining > 0 ? $remaining : $total }}" min="1" max="{{ $total }}" required>
                            </div>
                            <small class="text-muted">Bisa diisi sebagian jika pembayaran dicicil.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload Bukti Transfer / Pembayaran</label>
                            <input type="file" name="proof_file" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf">
                            <small class="text-muted">Format: JPG, PNG, WEBP, atau PDF (Maks. 5 MB)</small>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Catatan / Keterangan Transaksi</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Contoh: Pelunasan termin 1 via m-banking"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-semibold">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: Ubah Tanggal --}}
    <div class="modal fade" id="editDate" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form action="{{ route('payable.editDate', $product->id) }}" method="POST">
                    @csrf
                    <div class="modal-header py-3">
                        <h6 class="modal-title fw-bold">Ubah Tanggal Invoice / Bayar</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <label class="form-label small fw-semibold">Tanggal Baru</label>
                        <input type="date" name="date" class="form-control" value="{{ $product->date_payment ?? $product->date }}" required>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-xs btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-xs btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: Tambah / Edit PPH --}}
    <div class="modal fade" id="addPPH" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form action="{{ route('payable.addPph', $product->id) }}" method="POST">
                    @csrf
                    <div class="modal-header py-3">
                        <h6 class="modal-title fw-bold">Atur Potongan PPH</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <label class="form-label small fw-semibold">Nominal PPH (Rp)</label>
                        <input type="number" name="pph" class="form-control" value="{{ $product->pph ?? 0 }}" min="0" required>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-xs btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-xs btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script>
        // Hapus pembayaran dan kembalikan saldo bank
        $(document).on('click', '.btn-delete-payment', function() {
            var id = $(this).data('id');
            var amount = $(this).data('amount');

            Swal.fire({
                title: 'Hapus Pembayaran Ini?',
                text: 'Nominal pembayaran sebesar Rp ' + amount + ' akan dihapus dan saldo bank akan dikembalikan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '/payable/payment/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                customClass: {
                                    confirmButton: 'btn btn-success waves-effect'
                                }
                            });
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menghapus data!'
                            });
                        }
                    });
                }
            });
        });

        // Trigger Konfirmasi Lunas (PAID)
        $(document).on('click', '.accept-product', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Konfirmasi Pelunasan?",
                text: "Status hutang ini akan diubah menjadi LUNAS (PAID).",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Konfirmasi Lunas!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-success me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect"
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '/payable/receipt/' + id + '/confirm',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: response.message || "Faktur pembelian berhasil ditandai LUNAS.",
                                customClass: {
                                    confirmButton: "btn btn-success waves-effect"
                                }
                            });
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat memproses data!'
                            });
                        }
                    });
                }
            });
        });

        // Trigger Batalkan Konfirmasi (UNPAID)
        $(document).on('click', '.unconfirm-product', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Batalkan Status Lunas?",
                text: "Status pembayaran akan dikembalikan menjadi UNPAID (Belum Lunas).",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Batalkan Lunas!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect"
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '/payable/receipt/' + id + '/unconfirm',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Dibatalkan!",
                                text: response.message || "Status berhasil dikembalikan menjadi UNPAID.",
                                customClass: {
                                    confirmButton: "btn btn-success waves-effect"
                                }
                            });
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat memproses data!'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
