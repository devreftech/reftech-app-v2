@extends('layouts.sales.app')
@section('title', 'Surat Mutasi Stok #' . str_pad($change->id, 4, '0', STR_PAD_LEFT) . ' - Warehouse Transfer')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        /* Document Card */
        .transfer-doc-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        /* Logistics Timeline Tracker */
        .transfer-tracker {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            padding: 20px 10px;
        }
        .tracker-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }
        .tracker-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 8px;
            transition: all 0.3s;
        }
        .tracker-icon-box.completed {
            background: #e8fadf;
            color: #71dd37;
            border: 2px solid #71dd37;
        }
        .tracker-icon-box.active {
            background: #fff8e1;
            color: #ffab00;
            border: 2px solid #ffab00;
            box-shadow: 0 0 0 4px rgba(255, 171, 0, 0.2);
            animation: pulse-border 2s infinite;
        }
        .tracker-icon-box.pending {
            background: #f5f5f7;
            color: #b0b8c4;
            border: 2px dashed #d0d7e2;
        }
        .tracker-line {
            position: absolute;
            top: 42px;
            left: 15%;
            width: 70%;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }
        .tracker-line-progress {
            position: absolute;
            top: 42px;
            left: 15%;
            height: 3px;
            background: #71dd37;
            z-index: 1;
            transition: width 0.4s;
        }

        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(255, 171, 0, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 171, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 171, 0, 0); }
        }

        /* Route Badges */
        .route-badge-lg {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        .route-bks-bdg {
            background: rgba(105, 108, 255, 0.12);
            color: #696cff;
            border: 1px solid rgba(105, 108, 255, 0.3);
        }
        .route-bdg-bks {
            background: rgba(3, 195, 236, 0.12);
            color: #03c3ec;
            border: 1px solid rgba(3, 195, 236, 0.3);
        }

        /* Info Block */
        .info-block {
            background: #f8f9fc;
            border-radius: 12px;
            border: 1px solid #eef1f6;
            padding: 16px;
            height: 100%;
        }

        /* Signature Boxes for Print */
        .signature-box {
            text-align: center;
            padding-top: 10px;
        }
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px dashed #566a7f;
            display: inline-block;
            width: 80%;
        }

        /* Print Mode Optimization */
        @media print {
            .no-print, .navbar, .layout-navbar, .layout-menu, .footer, .btn, .breadcrumb {
                display: none !important;
            }
            .content-wrapper, .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            .transfer-doc-card {
                box-shadow: none !important;
                border: none !important;
            }
            .signature-area {
                display: flex !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y px-4">
        {{-- Flash Notification --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm border-0 no-print" role="alert">
                <i class="mdi mdi-check-circle-outline me-2 mdi-24px"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Top Bar Navigation --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 no-print">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mb-1 font-12">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('change-warehouse.index') }}">Transfer Gudang</a></li>
                        <li class="breadcrumb-item active fw-bold">Detail #TRF-{{ str_pad($change->id, 4, '0', STR_PAD_LEFT) }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="mdi mdi-swap-horizontal-bold text-primary font-26"></i>
                        Bukti Transfer Gudang #TRF-{{ str_pad($change->id, 4, '0', STR_PAD_LEFT) }}
                    </h4>
                    @if($change->status == 2)
                        <span class="badge bg-label-success rounded-pill px-3 py-1 font-12">
                            <i class="mdi mdi-check-decagram me-1"></i> Selesai Diterima
                        </span>
                    @else
                        <span class="badge bg-label-warning rounded-pill px-3 py-1 font-12">
                            <i class="mdi mdi-truck-fast-outline me-1"></i> Dalam Pengiriman
                        </span>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('change-warehouse.index') }}" class="btn btn-outline-secondary d-flex align-items-center gap-1 waves-effect">
                    <i class="mdi mdi-arrow-left me-1"></i>
                    <span>Kembali</span>
                </a>
                <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1 waves-effect" onclick="window.print()">
                    <i class="mdi mdi-printer-outline me-1"></i>
                    <span>Cetak Surat Jalan</span>
                </button>
                @if($change->status != 2)
                    <button type="button" class="btn btn-success d-flex align-items-center gap-1 shadow waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#acceptTransferModal">
                        <i class="mdi mdi-check-circle-outline me-1"></i>
                        <span>Konfirmasi Terima Barang</span>
                    </button>
                @endif
            </div>
        </div>

        {{-- Logistics Timeline Stepper --}}
        @php
            $fromCode = strtoupper(trim($change->from ?? 'BDG'));
            $toCode = strtoupper(trim($change->to ?? 'BKS'));
            $isReceived = ($change->status == 2);
            $routeClass = ($fromCode == 'BKS') ? 'route-bks-bdg' : 'route-bdg-bks';
            $progressWidth = $isReceived ? '70%' : '35%';
        @endphp

        <div class="card shadow-sm border-0 mb-4 no-print">
            <div class="card-body py-3 px-4">
                <div class="transfer-tracker">
                    <div class="tracker-line"></div>
                    <div class="tracker-line-progress" style="width: {{ $progressWidth }};"></div>

                    {{-- Step 1: Dispatched --}}
                    <div class="tracker-step">
                        <div class="tracker-icon-box completed">
                            <i class="mdi mdi-package-up"></i>
                        </div>
                        <span class="font-12 fw-bold text-heading">1. Dikirim dari Gudang Asal</span>
                        <small class="text-primary fw-semibold font-11">
                            {{ $fromCode == 'BKS' ? 'Gudang Bekasi (BKS)' : 'Gudang Bandung (BDG)' }}
                        </small>
                        <small class="text-muted font-10">
                            {{ $change->date ? \Carbon\Carbon::parse($change->date)->translatedFormat('d M Y') : '-' }} &bull; Oleh: {{ optional($change->sender)->name ?? 'Gudang' }}
                        </small>
                    </div>

                    {{-- Step 2: In Transit --}}
                    <div class="tracker-step">
                        <div class="tracker-icon-box {{ $isReceived ? 'completed' : 'active' }}">
                            <i class="mdi mdi-truck-fast-outline"></i>
                        </div>
                        <span class="font-12 fw-bold text-heading">2. Dalam Ekspedisi / Kurir</span>
                        <small class="text-warning fw-semibold font-11">
                            Kurir: {{ $change->kurir ?? 'Internal Staff' }}
                        </small>
                        <small class="text-muted font-10">
                            {{ $isReceived ? 'Pengiriman Tuntas' : 'Sedang Menuju Gudang Tujuan' }}
                        </small>
                    </div>

                    {{-- Step 3: Destination --}}
                    <div class="tracker-step">
                        <div class="tracker-icon-box {{ $isReceived ? 'completed' : 'pending' }}">
                            <i class="mdi mdi-home-variant-outline"></i>
                        </div>
                        <span class="font-12 fw-bold text-heading">3. Tiba di Gudang Tujuan</span>
                        <small class="{{ $isReceived ? 'text-success' : 'text-muted' }} fw-semibold font-11">
                            {{ $toCode == 'BKS' ? 'Gudang Bekasi (BKS)' : 'Gudang Bandung (BDG)' }}
                        </small>
                        <small class="text-muted font-10">
                            @if($isReceived)
                                {{ $change->date_recieve ? \Carbon\Carbon::parse($change->date_recieve)->translatedFormat('d M Y') : '-' }} &bull; Penerima: {{ optional($change->reciever)->name ?? 'Petugas' }}
                            @else
                                Menunggu Konfirmasi Fisik
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Document & Action Sidebar Grid --}}
        <div class="row g-4">
            {{-- Document Card (Left) --}}
            <div class="col-xl-9 col-lg-8 col-12">
                <div class="transfer-doc-card p-4 p-md-5">
                    {{-- Document Header --}}
                    <div class="d-flex justify-content-between flex-sm-row flex-column pb-4 border-bottom mb-4">
                        <div class="mb-sm-0 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" style="max-height: 48px; width: auto;">
                            </div>
                            <h5 class="fw-bold text-heading mb-1">PT REFTECH JAYA OPTIMA</h5>
                            <div class="text-muted font-11" style="line-height: 1.6;">
                                <div>Taman Kopo Indah V, Ruko Sommerville No. 31</div>
                                <div>Bandung – Jawa Barat 40218</div>
                                <div><i class="mdi mdi-phone-outline me-1 font-12"></i> 022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1 font-12"></i> info@reftech.id</div>
                            </div>
                        </div>
                        <div class="text-sm-end">
                            <span class="badge bg-label-primary font-12 fw-bold px-3 py-1 rounded-pill mb-1">
                                DOKUMEN MUTASI RESMI
                            </span>
                            <h3 class="fw-bold text-heading mb-0">SURAT JALAN TRANSFER</h3>
                            <div class="text-primary fw-bold font-15 mb-2">#TRF-{{ str_pad($change->id, 4, '0', STR_PAD_LEFT) }}</div>
                            <div class="font-12 text-muted">
                                <div><strong>Tanggal Kirim:</strong> {{ $change->date ? \Carbon\Carbon::parse($change->date)->translatedFormat('d F Y') : '-' }}</div>
                                @if($isReceived && $change->date_recieve)
                                    <div class="text-success"><strong>Tanggal Diterima:</strong> {{ \Carbon\Carbon::parse($change->date_recieve)->translatedFormat('d F Y') }}</div>
                                @endif
                                <div><strong>Jasa Pengantar:</strong> {{ $change->kurir ?? 'Internal Staff' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Title & Notes Bar --}}
                    <div class="mb-4 p-3 bg-light rounded-3 border">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-8">
                                <span class="text-muted font-11 fw-bold text-uppercase d-block">Judul / Keperluan Transfer:</span>
                                <h6 class="fw-bold text-heading font-14 mb-0">{{ $change->title ?? 'Perpindahan Stok Antar Gudang' }}</h6>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <span class="route-badge-lg {{ $routeClass }}">
                                    <span>{{ $fromCode == 'BKS' ? 'Bekasi (BKS)' : 'Bandung (BDG)' }}</span>
                                    <i class="mdi mdi-arrow-right-thin font-20"></i>
                                    <span>{{ $toCode == 'BKS' ? 'Bekasi (BKS)' : 'Bandung (BDG)' }}</span>
                                </span>
                            </div>
                        </div>
                        @if($change->note && $change->note !== '-')
                            <div class="mt-2 pt-2 border-top font-12 text-muted">
                                <i class="mdi mdi-note-text-outline me-1 text-primary"></i><strong>Catatan Pengirim:</strong> {{ $change->note }}
                            </div>
                        @endif
                        @if($isReceived && $change->note_recieve && $change->note_recieve !== '-')
                            <div class="mt-1 font-12 text-success">
                                <i class="mdi mdi-comment-check-outline me-1"></i><strong>Catatan Penerima:</strong> {{ $change->note_recieve }}
                            </div>
                        @endif
                    </div>

                    {{-- Sender & Receiver Two-Column Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="info-block">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-11 text-muted text-uppercase fw-bold">GUDANG ASAL (PENGIRIM)</span>
                                    <span class="badge bg-label-primary font-10">OUT</span>
                                </div>
                                <h6 class="fw-bold text-heading font-14 mb-1">
                                    {{ $fromCode == 'BKS' ? 'Gudang Utama Bekasi (BKS)' : 'Gudang Cabang Bandung (BDG)' }}
                                </h6>
                                <p class="text-muted font-11 mb-2">
                                    {{ $fromCode == 'BKS' ? 'Jl. Nancep No. 45A, Setu, Cibitung - Kab. Bekasi 17320' : 'Taman Kopo Indah V, Ruko Sommerville No. 31, Bandung 40218' }}
                                </p>
                                <div class="font-12 pt-2 border-top">
                                    <span class="text-muted">Diserahkan Oleh:</span>
                                    <strong class="text-heading ms-1">{{ optional($change->sender)->name ?? 'Petugas Gudang' }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-block">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="font-11 text-muted text-uppercase fw-bold">GUDANG TUJUAN (PENERIMA)</span>
                                    <span class="badge bg-label-success font-10">IN</span>
                                </div>
                                <h6 class="fw-bold text-heading font-14 mb-1">
                                    {{ $toCode == 'BKS' ? 'Gudang Utama Bekasi (BKS)' : 'Gudang Cabang Bandung (BDG)' }}
                                </h6>
                                <p class="text-muted font-11 mb-2">
                                    {{ $toCode == 'BKS' ? 'Jl. Nancep No. 45A, Setu, Cibitung - Kab. Bekasi 17320' : 'Taman Kopo Indah V, Ruko Sommerville No. 31, Bandung 40218' }}
                                </p>
                                <div class="font-12 pt-2 border-top">
                                    <span class="text-muted">Diterima Oleh:</span>
                                    <strong class="{{ $isReceived ? 'text-success' : 'text-muted' }} ms-1">
                                        {{ optional($change->reciever)->name ?? ($isReceived ? 'Petugas Penerima' : 'Menunggu Konfirmasi') }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="table-responsive border rounded-3 mb-4">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="font-11 text-uppercase text-muted letter-spacing-1">
                                    <th class="text-center" style="width: 40px;">No</th>
                                    <th>Nama Commodity / Sparepart</th>
                                    <th>Part Number &amp; Spesifikasi</th>
                                    <th class="text-center" style="width: 110px;">Tipe</th>
                                    <th class="text-center" style="width: 110px;">Kuantitas</th>
                                    <th class="text-center" style="width: 120px;">Kondisi Fisik</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalQty = 0;
                                @endphp
                                @forelse ($detChange as $index => $item)
                                    @php
                                        $qty = (int) $item->qty;
                                        $totalQty += $qty;
                                        $commodity = optional(optional($item->replacement)->product)->commodity ?? 'Sparepart #' . $item->id_replacement;
                                        $desc = optional(optional($item->replacement)->product)->detail_desc ?? '-';
                                        $partNo = optional($item->replacement)->replacement ?? '-';
                                        $type = (optional(optional($item->replacement)->product)->go == 'Genuine') ? 'Genuine (G)' : 'Replacement (R)';
                                        $typeBadge = (optional(optional($item->replacement)->product)->go == 'Genuine') ? 'bg-label-primary' : 'bg-label-info';
                                    @endphp
                                    <tr>
                                        <td class="text-center text-muted font-12">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold text-heading font-13">{{ $commodity }}</div>
                                            <small class="text-muted font-11 d-block">{{ $desc }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-secondary font-12 font-monospace">{{ $partNo }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $typeBadge }} font-11 rounded-pill">{{ $type }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold font-14 text-heading">{{ number_format($qty) }}</span>
                                            <small class="text-muted font-11">pcs</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-success rounded-pill font-11">
                                                <i class="mdi mdi-check-circle-outline font-12 me-1"></i> Terverifikasi
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada rincian item dalam transfer ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light border-top">
                                <tr>
                                    <th colspan="4" class="text-end font-12 text-uppercase text-muted pe-3">Total Barang Termutasi:</th>
                                    <th class="text-center">
                                        <span class="fw-bold font-15 text-primary">{{ number_format($totalQty) }}</span>
                                        <span class="font-11 text-muted">pcs</span>
                                    </th>
                                    <th class="text-center text-muted font-11">{{ count($detChange) }} jenis</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Signature Box (Especially for Printing) --}}
                    <div class="signature-area pt-4 border-top mt-5">
                        <div class="row g-3 text-center">
                            <div class="col-3 signature-box">
                                <span class="font-11 text-muted text-uppercase d-block mb-1">Dibuat &amp; Diserahkan:</span>
                                <div class="signature-line"></div>
                                <strong class="font-12 text-heading d-block mt-2">{{ optional($change->sender)->name ?? 'Petugas Gudang' }}</strong>
                                <small class="text-muted font-10">Staf Gudang Pengirim</small>
                            </div>
                            <div class="col-3 signature-box">
                                <span class="font-11 text-muted text-uppercase d-block mb-1">Dibawa Oleh:</span>
                                <div class="signature-line"></div>
                                <strong class="font-12 text-heading d-block mt-2">{{ $change->kurir ?? 'Ekspedisi / Kurir' }}</strong>
                                <small class="text-muted font-10">Driver / Kurir Pengantar</small>
                            </div>
                            <div class="col-3 signature-box">
                                <span class="font-11 text-muted text-uppercase d-block mb-1">Diterima &amp; Diverifikasi:</span>
                                <div class="signature-line"></div>
                                <strong class="font-12 text-heading d-block mt-2">{{ optional($change->reciever)->name ?? 'Petugas Penerima' }}</strong>
                                <small class="text-muted font-10">Staf Gudang Penerima</small>
                            </div>
                            <div class="col-3 signature-box">
                                <span class="font-11 text-muted text-uppercase d-block mb-1">Diketahui Oleh:</span>
                                <div class="signature-line"></div>
                                <strong class="font-12 text-heading d-block mt-2">Supervisor Logistik</strong>
                                <small class="text-muted font-10">Head of Operations</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Sidebar (Right) --}}
            <div class="col-xl-3 col-lg-4 col-12 no-print">
                {{-- Quick Actions Card --}}
                <div class="card shadow-sm border mb-3">
                    <div class="card-header border-bottom py-3">
                        <h6 class="card-title mb-0 fw-bold font-13 d-flex align-items-center gap-2">
                            <i class="mdi mdi-cogs text-primary"></i> Aksi Surat Jalan
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        @if($change->status != 2)
                            <button class="btn btn-success d-grid w-100 waves-effect mb-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#acceptTransferModal">
                                <span class="d-flex align-items-center justify-content-center gap-2">
                                    <i class="mdi mdi-check-circle-outline font-18"></i>
                                    <strong>Konfirmasi Terima Barang</strong>
                                </span>
                            </button>
                        @else
                            <button class="btn btn-label-success d-grid w-100 mb-3" disabled>
                                <span class="d-flex align-items-center justify-content-center gap-1">
                                    <i class="mdi mdi-check-all font-16"></i>
                                    <span>Telah Diterima &amp; Selesai</span>
                                </span>
                            </button>
                        @endif

                        <button type="button" class="btn btn-outline-secondary d-grid w-100 waves-effect mb-2" onclick="window.print()">
                            <i class="mdi mdi-printer-outline me-1"></i> Cetak Surat Jalan (A4)
                        </button>

                        <a href="{{ route('change-warehouse.index') }}" class="btn btn-outline-primary d-grid w-100 waves-effect">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>

                {{-- Transfer Overview Widget --}}
                <div class="card shadow-sm border mb-3">
                    <div class="card-header border-bottom py-3">
                        <h6 class="card-title mb-0 fw-bold font-13 d-flex align-items-center gap-2">
                            <i class="mdi mdi-clipboard-text-outline text-primary"></i> Ringkasan Mutasi
                        </h6>
                    </div>
                    <div class="card-body p-3 font-12">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Status Mutasi:</span>
                            @if($isReceived)
                                <span class="badge bg-label-success font-11">Selesai Diterima</span>
                            @else
                                <span class="badge bg-label-warning font-11">Dalam Pengiriman</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Gudang Asal:</span>
                            <strong>{{ $fromCode }} ({{ $fromCode == 'BKS' ? 'Bekasi' : 'Bandung' }})</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Gudang Tujuan:</span>
                            <strong>{{ $toCode }} ({{ $toCode == 'BKS' ? 'Bekasi' : 'Bandung' }})</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Jasa Kurir:</span>
                            <strong>{{ $change->kurir ?? 'Internal Staff' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Total Varian:</span>
                            <strong>{{ count($detChange) }} item</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted">Total Fisik:</span>
                            <strong class="text-primary font-14">{{ number_format($totalQty) }} pcs</strong>
                        </div>
                    </div>
                </div>

                {{-- Person In Charge Card --}}
                <div class="card shadow-sm border">
                    <div class="card-header border-bottom py-3">
                        <h6 class="card-title mb-0 fw-bold font-13 d-flex align-items-center gap-2">
                            <i class="mdi mdi-account-group-outline text-primary"></i> Penanggung Jawab
                        </h6>
                    </div>
                    <div class="card-body p-3 font-12">
                        <div class="mb-3">
                            <span class="text-muted d-block font-11">Petugas Pengirim:</span>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <div class="avatar avatar-xs bg-label-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-account font-14"></i>
                                </div>
                                <strong class="text-heading">{{ optional($change->sender)->name ?? 'Gudang Pengirim' }}</strong>
                            </div>
                        </div>
                        <div>
                            <span class="text-muted d-block font-11">Petugas Penerima:</span>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <div class="avatar avatar-xs bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-account-check font-14"></i>
                                </div>
                                <strong class="{{ $isReceived ? 'text-success' : 'text-muted' }}">
                                    {{ optional($change->reciever)->name ?? ($isReceived ? 'Petugas Penerima' : 'Menunggu Penerimaan...') }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Terima Barang --}}
    <div class="modal fade" id="acceptTransferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form method="POST" action="{{ route('change-warehouse.accept', $change->id) }}">
                    @csrf
                    <div class="modal-header bg-label-success pb-3 border-bottom">
                        <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2">
                            <i class="mdi mdi-check-decagram-outline font-22"></i>
                            Konfirmasi Penerimaan Transfer Gudang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-4">
                        <div class="alert alert-warning py-2 mb-3 font-12 d-flex align-items-center gap-2">
                            <i class="mdi mdi-information-outline font-18 text-warning"></i>
                            <div>Kuantitas stok barang akan otomatis bertambah pada gudang tujuan ({{ $toCode == 'BKS' ? 'Gudang Bekasi' : 'Gudang Bandung' }}).</div>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded-3 border">
                            <label class="form-label font-11 text-muted text-uppercase fw-bold d-block mb-1">Dokumen Transfer</label>
                            <h6 class="fw-bold text-heading font-14 mb-1">#TRF-{{ str_pad($change->id, 4, '0', STR_PAD_LEFT) }} - {{ $change->title }}</h6>
                            <span class="badge bg-label-primary font-12">Rute: {{ $fromCode }} ➔ {{ $toCode }} (Kurir: {{ $change->kurir ?? '-' }})</span>
                        </div>

                        <div class="mb-3">
                            <label for="modalAcceptNote" class="form-label fw-semibold font-13">Catatan Penerimaan / Kondisi Fisik Barang</label>
                            <textarea class="form-control font-13" id="modalAcceptNote" name="note" rows="3" placeholder="Contoh: Barang telah tiba lengkap, fisik tersegel baik dan tidak ada cacat..."></textarea>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirmCheck" required checked>
                            <label class="form-check-label font-12 text-muted" for="confirmCheck">
                                Saya mengonfirmasi bahwa fisik barang telah diverifikasi dan diterima dengan benar.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-1 px-4">
                            <i class="mdi mdi-check me-1"></i> Konfirmasi Terima Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
