@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.sales.app')
@section('title', 'Invoice ' . ($invoice->no_invoice ?? '#' . $invoice->id))
@section('content')
    <div class="d-flex align-items-center justify-content-between py-3 mb-4">
        <h4 class="fw-bold m-0">
            <span class="text-muted fw-light">
                <a href="{{ route('invoice.index') }}" class="text-muted">Accounting / Invoice</a> /
            </span>
            {{ $invoice->no_invoice ?? '#' . $invoice->id }}
        </h4>
        <div class="d-flex align-items-center gap-2">
            @if (isset($pendingPO) && $pendingPO)
                <a href="{{ route('pending-po.show', $pendingPO->id) }}"
                   target="_blank"
                   class="btn btn-outline-info shadow-sm waves-effect"
                   title="Buka Halaman Sales Order untuk PO ini">
                    <i class="mdi mdi-clipboard-text-outline me-1"></i> Sales Order
                </a>
            @else
                <a href="{{ route('pending-po.sales-order') }}"
                   target="_blank"
                   class="btn btn-outline-secondary shadow-sm waves-effect"
                   title="Buka Daftar Sales Order">
                    <i class="mdi mdi-clipboard-text-outline me-1"></i> Sales Order
                </a>
            @endif

            @if (in_array(Auth::user()->role, ['Admin', 'Accounting', 'Finance Manager', 'Finance']))
                @if (isset($monitoringTask) && $monitoringTask)
                    <a href="{{ route('kanban.monitoring-document') }}?task_id={{ $monitoringTask->id }}"
                       target="_blank"
                       class="btn btn-outline-primary shadow-sm waves-effect"
                       title="Buka Papan Monitoring Document & Otomatis Buka Card Detail">
                        <i class="mdi mdi-text-box-search-outline me-1"></i> Monitoring Document
                    </a>
                @else
                    <a href="{{ route('kanban.monitoring-document') }}"
                       target="_blank"
                       class="btn btn-outline-secondary shadow-sm waves-effect"
                       title="Buka Papan Monitoring Document">
                        <i class="mdi mdi-view-dashboard-outline me-1"></i> Monitoring Document
                    </a>
                @endif

                @if (isset($bast) && $bast)
                    <button type="button" class="btn btn-outline-success shadow-sm waves-effect" onclick="switchToBastTab()" title="Lihat Berita Acara Serah Terima">
                        <i class="mdi mdi-certificate-outline me-1"></i> BAST ({{ $bast->no_bast }})
                    </button>
                @else
                    <button type="button" class="btn btn-success shadow-sm waves-effect" data-bs-toggle="modal" data-bs-target="#modalCreateBast" title="Buat Berita Acara Serah Terima Baru">
                        <i class="mdi mdi-plus me-1"></i> BAST
                    </button>
                @endif
            @endif
        </div>
    </div>

    {{-- NAV TABS: INVOICE & DELIVERY ORDER --}}
    <div class="nav-align-top mb-4">
        <ul class="nav nav-tabs nav-fill shadow-sm rounded border-0 mb-4" role="tablist" style="background:#fff; padding: 5px;">
            <li class="nav-item">
                <button type="button" class="nav-link active fw-bold py-2.5 fs-6" role="tab" data-bs-toggle="tab" data-bs-target="#tab-invoice" aria-controls="tab-invoice" aria-selected="true">
                    <i class="mdi mdi-receipt-text-outline me-2 fs-5 text-primary"></i> Faktur Penjualan (Invoice)
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link fw-bold py-2.5 fs-6" role="tab" data-bs-toggle="tab" data-bs-target="#tab-delivery" aria-controls="tab-delivery" aria-selected="false">
                    <i class="mdi mdi-truck-delivery-outline me-2 fs-5 text-primary"></i> Delivery Order (Surat Jalan)
                    @php
                        $delCount = $quote->deliveries ? $quote->deliveries->count() : 0;
                    @endphp
                    @if ($delCount > 0)
                        <span class="badge rounded-pill bg-primary ms-1" style="font-size:11px;">{{ $delCount }}</span>
                    @endif
                </button>
            </li>
            @if (isset($bast) && $bast)
                <li class="nav-item">
                    <button type="button" class="nav-link fw-bold py-2.5 fs-6" id="btn-tab-bast" role="tab" data-bs-toggle="tab" data-bs-target="#tab-bast" aria-controls="tab-bast" aria-selected="false">
                        <i class="mdi mdi-certificate-outline me-2 fs-5 text-success"></i> Berita Acara (BAST)
                        <span class="badge bg-success ms-1" style="font-size:10px;">{{ $bast->no_bast }}</span>
                    </button>
                </li>
            @endif
        </ul>

        <div class="tab-content p-0 bg-transparent border-0 shadow-none">
            {{-- TAB 1: INVOICE DETAIL --}}
            <div class="tab-pane fade show active" id="tab-invoice" role="tabpanel">
                <div class="row invoice-preview">
                    {{-- Invoice Card --}}
                    <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                        <div class="d-flex justify-content-end mb-2">
                            <div class="btn-group btn-group-sm" role="group" aria-label="Invoice language toggle">
                                <button type="button" class="btn btn-primary invoice-lang-btn active" data-lang="id">ID</button>
                                <button type="button" class="btn btn-outline-primary invoice-lang-btn" data-lang="en">EN</button>
                            </div>
                        </div>
            <div class="card invoice-preview-card" style="position: relative; overflow: hidden;">

                {{-- Watermark --}}
                @if ($invoice->status_p)
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 160px; font-weight: 900; color: rgba(40, 167, 69, 0.10); pointer-events: none; z-index: 0; letter-spacing: 12px; white-space: nowrap; user-select: none;">
                        PAID
                    </div>
                @else
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 140px; font-weight: 900; color: rgba(220, 53, 69, 0.10); pointer-events: none; z-index: 0; letter-spacing: 12px; white-space: nowrap; user-select: none;">
                        UNPAID
                    </div>
                @endif

                {{-- Header --}}
                <div class="card-body p-4" style="position: relative; z-index: 1;">
                    @php $isKojisha = $quote->client?->info === 'Kojisha'; @endphp
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column {{ !$quote->tax ? 'justify-content-end' : '' }} gap-3 mb-0">
                        @if ($quote->tax)
                            <div class="mb-xl-0 pb-1">
                                @if ($isKojisha)
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                        <span class="app-brand-logo demo">
                                            <img src="{{ asset('/asset') }}/logo/Logo-update-size.png" alt="Kojisha Logo" width="180">
                                        </span>
                                    </div>
                                    <div class="d-flex flex-row align-items-start gap-4 mt-2" style="font-size: 11px;">
                                        <div class="info" style="max-width: 260px;">
                                            <p class="mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                                <i class="mdi mdi-office-building-outline me-1 text-primary"></i><span class="i18n" data-en="Office Address :">Alamat Kantor :</span>
                                            </p>
                                            <p class="mb-1 text-muted" style="line-height: 1.4;">Jl. Nancep No. 45A, Setu, Cibitung - Kab. Bekasi 17320</p>
                                            <p class="mb-0 text-muted">
                                                <i class="mdi mdi-phone-outline me-1 text-primary"></i>+62 812-1000-0997 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1 text-primary"></i>admin@kojisha.com
                                            </p>
                                        </div>
                                        <div class="npwp_add" style="max-width: 280px;">
                                            <p class="mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                                <i class="mdi mdi-file-document-outline me-1 text-primary"></i><span class="i18n" data-en="NPWP Address :">Alamat NPWP :</span>
                                            </p>
                                            <p class="mb-1 text-muted" style="line-height: 1.4;">Jl. Nancep No. 45, Setu Cisaat RT. 001 RW. 003 Cibening, Setu</p>
                                            <div class="px-2 py-0.5 rounded-0" style="background:#fff0e0; border:1px solid #ffd8b0; font-size:10.5px; font-weight:600; color:#7a4a10; display:inline-block; border-radius:0 !important;">
                                                <i class="mdi mdi-card-account-details-outline me-1"></i>NPWP: 96.484.859.2-413.000
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                        <span class="app-brand-logo demo">
                                            <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" width="180">
                                        </span>
                                    </div>
                                    <div class="d-flex flex-row align-items-start gap-4 mt-2" style="font-size: 11px;">
                                        <div class="info" style="max-width: 260px;">
                                            <p class="mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                                <i class="mdi mdi-office-building-outline me-1 text-primary"></i><span class="i18n" data-en="Office Address :">Alamat Kantor :</span>
                                            </p>
                                            <p class="mb-1 text-muted" style="line-height: 1.4;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                                            <p class="mb-0 text-muted">
                                                <i class="mdi mdi-phone-outline me-1 text-primary"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1 text-primary"></i>accounting@reftech.id
                                            </p>
                                        </div>
                                        <div class="npwp_add" style="max-width: 280px;">
                                            <p class="mb-1 fw-bold text-dark" style="font-size: 11.5px;">
                                                <i class="mdi mdi-file-document-outline me-1 text-primary"></i><span class="i18n" data-en="NPWP Address :">Alamat NPWP :</span>
                                            </p>
                                            <p class="mb-1 text-muted" style="line-height: 1.4;">Komp. Negla Kencana Residence Blok B, No.2 Pasanggrahan, Ujung Berung Kota Bandung - Jawa Barat 40199</p>
                                            <div class="px-2 py-0.5 rounded-0" style="background:#eef0ff; border:1px solid #d0d0ff; font-size:10.5px; font-weight:600; color:#3d3d8f; display:inline-block; border-radius:0 !important;">
                                                <i class="mdi mdi-card-account-details-outline me-1"></i>NPWP: 0737285718429000
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="mb-xl-0 pb-1">
                                @if ($isKojisha)
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                        <span class="app-brand-logo demo">
                                            <img src="{{ asset('/asset') }}/logo/Logo-update-size.png" alt="Kojisha Logo" width="180">
                                        </span>
                                    </div>
                                    <p class="mb-1 fw-bold text-dark" style="font-size:14px;">PT Kojisha Innotiv Indonesia</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;">Jl. Nancep No. 45A, Setu, Cibitung - Kab. Bekasi 17320</p>
                                @else
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                        <span class="app-brand-logo demo">
                                            <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" width="180">
                                        </span>
                                    </div>
                                    <p class="mb-1 fw-bold text-dark" style="font-size:14px;">PT Reftech Jaya Optima</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                                @endif
                            </div>
                        @endif

                        <div class="text-end">
                            <h1 class="fw-bold" style="color: #2529fa; letter-spacing: 2px;">INVOICE</h1>
                            <p class="mb-1 fw-bold text-dark" style="font-size:14px;">#{{ $invoice->no_invoice }}</p>
                            <p class="mb-1 text-muted small">{{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('d F Y') : '-' }}</p>
                            @php
                                $hasProof = $payments->whereNotNull('file')->where('level', 0)->isNotEmpty();
                                if ($invoice->status_p) {
                                    $warna  = 'bg-label-success text-success';
                                    $textId = 'Terverifikasi';
                                    $textEn = 'Verified';
                                } elseif ($hasProof) {
                                    $warna  = 'bg-label-warning text-warning';
                                    $textId = 'Menunggu Verifikasi';
                                    $textEn = 'Awaiting Verification';
                                } else {
                                    $warna  = 'bg-label-dark text-dark';
                                    $textId = 'Menunggu Pembayaran';
                                    $textEn = 'Waiting Payment';
                                }
                            @endphp
                            <div class="mt-1">
                                <span class="badge {{ $warna }} px-3 py-1 fs-6 fw-bold i18n" data-en="{{ $textEn }}">{{ $textId }}</span>
                            </div>
                        </div>
                    </div>

                    <div style="height:2px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:16px 0 18px;"></div>

                    {{-- Invoice To + Document Info Box --}}
                    <div style="display:flex !important; align-items:stretch !important; gap:14px; margin-bottom:18px; font-size:12px;">
                        {{-- Card 1: Invoice To --}}
                        <div style="flex:1.4; display:flex; flex-direction:column; align-self:stretch; border:1px solid #e0e0e0; border-left:4px solid #696cff; border-radius:4px; padding:12px 16px; background:#fcfcfc;">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1" style="border-bottom: 1px dashed #e4e4e4;">
                                <span class="fw-bold text-uppercase" style="font-size:10.5px; letter-spacing:0.6px; color:#696cff;">
                                    <i class="mdi mdi-domain me-1"></i>Invoice To
                                </span>
                                @if ($quote->client?->npwp)
                                    <span class="px-2 py-0.5 rounded" style="font-size:10px; font-weight:600; background:#f0f2ff; color:#43497a; border:1px solid #d5d9ff;">
                                        <i class="mdi mdi-card-account-details-outline me-1"></i>NPWP: {{ $quote->client->npwp }}
                                    </span>
                                @endif
                            </div>

                            <p class="mb-2 fw-bold text-dark" style="font-size:14px; line-height:1.3;">
                                {{ $quote->client?->company ?? '-' }}
                            </p>

                            @php
                                $picName = $quote->pic?->name_pic ?? $quote->attn;
                                $targetAddress = $invoice->invoiceTo == '1' ? ($quote->client?->address ?? '-') : ($quote->client?->subAddress ?? '-');
                            @endphp

                            <div style="display:grid; grid-template-columns: auto 1fr; gap:4px 12px; font-size:11.5px; color:#333;">
                                @if ($picName)
                                    <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-account-outline me-1 text-primary"></i>Attn / PIC</span>
                                    <span class="fw-medium text-dark">
                                        : {{ $picName }}
                                        @if ($quote->pic?->phone_pic)
                                            <span class="text-muted ms-1">({{ $quote->pic->phone_pic }})</span>
                                        @endif
                                    </span>
                                @endif

                                @if ($quote->client?->phone)
                                    <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-phone-in-talk-outline me-1 text-primary"></i>Office Phone</span>
                                    <span class="fw-medium text-dark">: {{ $quote->client->phone }}</span>
                                @endif

                                @if ($targetAddress && $targetAddress !== '-')
                                    <span class="text-muted" style="white-space:nowrap;"><i class="mdi mdi-map-marker-outline me-1 text-primary"></i>Address</span>
                                    <span class="fw-medium text-dark" style="line-height:1.4;">: {{ $targetAddress }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Card 2: Payment Information --}}
                        <div style="min-width:240px; flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #e0e0e0; border-left:4px solid #8592a3; border-radius:4px; padding:12px 16px; background:#fcfcfc;">
                            <div class="mb-2 pb-1" style="border-bottom: 1px dashed #e4e4e4;">
                                <span class="fw-bold text-uppercase" style="font-size:10.5px; letter-spacing:0.6px; color:#566a7f;">
                                    <i class="mdi mdi-file-document-outline me-1"></i>Payment Information
                                </span>
                            </div>

                            @php
                                $hasCbdPayment = $payments->contains(fn($p) => in_array(strtoupper($p->type ?? ''), ['CBD', 'CASH', 'ESCROW']));
                                $hasTempoPayment = $payments->contains(fn($p) => strtoupper($p->type ?? '') === 'TEMPO');
                                $invTermUpper = strtoupper(trim($invoice->term ?? ''));
                                $quoteTermUpper = strtoupper(trim($quote->payment ?? ''));

                                $isTempo = false;
                                if (!$hasCbdPayment && !str_contains($invTermUpper, 'CASH BEFORE DELIVERY') && !str_contains($invTermUpper, 'CBD') && $invTermUpper !== 'CASH' && $invTermUpper !== 'ESCROW') {
                                    if ($hasTempoPayment) {
                                        $isTempo = true;
                                    } elseif (str_contains($invTermUpper, 'TEMPO') || str_contains($invTermUpper, 'HARI') || str_contains($invTermUpper, 'DAYS') || str_contains($invTermUpper, 'NET')) {
                                        $isTempo = true;
                                    } elseif (empty($invTermUpper) && (str_contains($quoteTermUpper, 'TEMPO') || str_contains($quoteTermUpper, 'HARI') || str_contains($quoteTermUpper, 'DAYS') || str_contains($quoteTermUpper, 'NET'))) {
                                        $isTempo = true;
                                    }
                                }

                                $tempoPayRec = $isTempo ? ($payments->firstWhere('type', 'Tempo') ?? $payments->first()) : null;
                                $dueDateDisplay = $tempoPayRec?->due_date ? \Carbon\Carbon::parse($tempoPayRec->due_date) : null;
                            @endphp

                            <div style="font-size:11.5px; color:#333;" class="my-auto">
                                <div class="d-flex align-items-center mb-1.5 pb-1" style="border-bottom:1px dashed #f0f0f0;">
                                    <span class="text-muted" style="min-width:110px;"><i class="mdi mdi-clipboard-text-outline me-1 text-primary"></i>PO No</span>
                                    <span class="fw-bold text-dark">: {{ $quote->po_number ?? '-' }}</span>
                                </div>
                                @if ($isTempo && $dueDateDisplay)
                                    <div class="d-flex align-items-center mb-1.5 pb-1" style="border-bottom:1px dashed #f0f0f0;">
                                        <span class="text-muted" style="min-width:110px;"><i class="mdi mdi-calendar-clock me-1 text-warning"></i><span class="i18n" data-en="Due Date">Jatuh Tempo</span></span>
                                        <span class="fw-bold text-warning">: {{ $dueDateDisplay->format('d F Y') }}</span>
                                    </div>
                                @endif
                                <div class="mt-2">
                                    <div class="fw-medium text-dark mb-1">
                                        <i class="mdi mdi-clock-outline me-1 text-primary"></i>Term of Payment :
                                    </div>
                                    <div class="ps-2 ms-1" style="border-left:3px solid #696cff; margin-top:4px;">
                                        <div class="fw-bold text-dark ps-2" style="font-size:11.5px; line-height:1.45; white-space:pre-line;"><i class="mdi mdi-chevron-right text-primary me-1" style="font-size:13px;"></i>{{ $invoice->term ?? $quote->payment_method ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {{-- Items Table --}}
                    @php
                        $specLabels = [
                            'brand'=>'Brand','model'=>'Model','type_unit'=>'Type',
                            'bar'=>'Max Pressure','air_cap'=>'Air Capacity','power'=>'Motor Power',
                            'voltage'=>'Voltage','connect'=>'Drive','cooling'=>'Cooling Method',
                            'exhaust'=>'Connection','refrigerant_type'=>'Refrigerant Type','pdp'=>'PDP',
                            'filtration'=>'Filtration','oil_content'=>'Oil Content','grade'=>'Grade',
                            'capacity'=>'Capacity','material'=>'Material','test_pressure'=>'Test Pressure',
                            'inlet_pressure'=>'Inlet Pressure','outlet_pressure'=>'Outlet Pressure',
                            'inlet_cap'=>'Inlet Capacity (LP)','outlet_cap'=>'Outlet Capacity (HP)',
                            'dimension'=>'Dimension','weight'=>'Weight',
                        ];
                        $specUnits = [
                            'bar'=>' Bar','air_cap'=>' m³/min','test_pressure'=>' Bar',
                            'inlet_pressure'=>' Bar','outlet_pressure'=>' Bar',
                            'inlet_cap'=>' m³/min','outlet_cap'=>' m³/min',
                            'weight'=>' Kg','capacity'=>' Liter',
                        ];
                        $hasDisc = $quote->details->where('disc', '>', 0)->count() > 0;
                        $colCount = 5 + ($hasDisc ? 1 : 0) + ($quote->tax ? 1 : 0);
                    @endphp
                    <div class="table-responsive rounded border mb-3">
                        <table class="table table-bordered items-top-align-table m-0" style="width:100%; font-size:12px;">
                            <thead style="font-size:11px; background:#eeeeff; color:#3d3d8f;">
                                <tr>
                                    <th class="text-center align-middle py-2" style="width:4%; font-weight:700; border-color:#d0d0ff;">No.</th>
                                    <th class="text-center align-middle py-2" style="font-weight:700; border-color:#d0d0ff;"><span class="i18n" data-en="DESCRIPTION">DESKRIPSI</span></th>
                                    <th class="text-center align-middle py-2" style="width:10%; font-weight:700; border-color:#d0d0ff;">Qty</th>
                                    <th class="text-center align-middle py-2" style="width:18%; font-weight:700; border-color:#d0d0ff;"><span class="i18n" data-en="PRICE (IDR)">HARGA (IDR)</span></th>
                                    @if ($hasDisc)
                                        <th class="text-center align-middle py-2" style="width:7%; font-weight:700; border-color:#d0d0ff;">Disc</th>
                                    @endif
                                    @if ($quote->tax)
                                        <th class="text-center align-middle py-2" style="width:15%; font-weight:700; border-color:#d0d0ff;">DPP (IDR)</th>
                                    @endif
                                    <th class="text-center align-middle py-2" style="width:18%; font-weight:700; border-color:#d0d0ff;"><span class="i18n" data-en="TOTAL PRICE (IDR)">TOTAL HARGA (IDR)</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $itemNo = 1;
                                    $headerCount = 0;
                                @endphp
                                @foreach ($quote->details as $item)
                                    @if ($item->type === 'header' || $item->type === 'heading')
                                        @php
                                            $lbl = trim($item->label ?? '');
                                            if (!preg_match('/^[A-Z0-9][\.\)]/i', $lbl)) {
                                                $lbl = chr(65 + ($headerCount % 26)) . '. ' . $lbl;
                                            }
                                            $headerCount++;
                                        @endphp
                                        <tr style="background:#f0f0ff;">
                                            <td colspan="{{ $colCount }}" class="fw-bold text-primary text-uppercase px-3" style="padding: 5px 10px; font-size:11.5px; border-top:1px solid #d0d0ff; border-bottom:1px solid #d0d0ff;">
                                                <i class="mdi mdi-bookmark-outline me-1"></i>{{ $lbl }}
                                            </td>
                                        </tr>
                                    @else
                                        @php $dpp = $quote->tax ? ($item->amount * 11 / 12) : 0; @endphp
                                        <tr style="font-size: 12px">
                                            <td class="text-center align-top py-2">{{ $itemNo++ }}</td>
                                            <td class="align-top py-2">
                                                @if ($item->type === 'unit' && $item->unit)
                                                    <p class="mb-1 fw-semibold" style="font-size: 12px">
                                                        {{ $item->label ?: ($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : '')) }}
                                                    </p>
                                                    @php $specs = $item->getSpecVisibleArray(); @endphp
                                                    @if (!empty($specs))
                                                        <div class="spec-detail-rows" style="font-size:11px; color:#777; margin-top:4px; {{ $invoice->show_spec ? '' : 'display:none;' }}">
                                                            @foreach ($specs as $field)
                                                                @if ($field === 'unit') @continue @endif
                                                                @php $val = $item->unit->$field ?? null; @endphp
                                                                @if ($val && isset($specLabels[$field]))
                                                                    <div style="display:flex; padding:1px 0;">
                                                                        <span style="min-width:110px; flex-shrink:0;">{{ $specLabels[$field] }}</span>
                                                                        <span>: {{ $val }}{{ $specUnits[$field] ?? '' }}</span>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @elseif ($item->type === 'equivalent' || $item->type === 'sparepart' || $item->id_equivalent || $item->equivalent)
                                                     @if ($item->equivalent)
                                                         @php
                                                             $brandPn = trim(($item->equivalent->brand ?? '') . ($item->equivalent->pn ? ' - ' . $item->equivalent->pn : ''));
                                                             $subDesc = $item->label;
                                                             if (empty($subDesc) || $subDesc === $brandPn) {
                                                                 $subDesc = optional($item->equivalent->product)->description ?? optional($item->equivalent->product)->name;
                                                             }
                                                         @endphp
                                                         <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ $brandPn ?: $item->label }}</p>
                                                         @if ($subDesc && $subDesc !== $brandPn)
                                                             <div style="font-size: 12px; color: #333333; font-weight: 500; margin-top: 2px; line-height: 1.4;">{{ $subDesc }}</div>
                                                         @endif
                                                     @else
                                                         <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ $item->label }}</p>
                                                     @endif
                                                @else
                                                    <p class="mb-0 fw-bold text-dark" style="font-size: 12px">{{ $item->label }}</p>
                                                @endif
                                                @if ($item->description)
                                                     <div style="font-size: 11px; color: #444; white-space: pre-line; margin-top: 3px; line-height: 1.4;">{{ $item->description }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center align-top py-2">
                                                {{ (float) $item->qty }} {{ $item->info_qty ?? 'Unit' }}
                                                @if ($item->remaining_qty <= 0)
                                                    <div><span class="badge bg-label-success mt-1" style="font-size:9.5px;"><span class="i18n" data-en="Fully Delivered">Terkirim Semua</span></span></div>
                                                @elseif ($item->delivered_qty > 0)
                                                    <div><span class="badge bg-label-warning mt-1" style="font-size:9.5px;"><span class="i18n" data-en="Remaining {{ $item->remaining_qty }}">Sisa {{ $item->remaining_qty }}</span></span></div>
                                                @else
                                                    <div><span class="badge bg-label-secondary mt-1" style="font-size:9.5px;"><span class="i18n" data-en="Not Delivered Yet">Belum Dikirim</span></span></div>
                                                @endif
                                            </td>
                                            <td class="text-end align-top py-2">{{ number_format($item->price, 0, '', '.') }}</td>
                                            @if ($hasDisc)
                                                <td class="text-center align-top py-2">{{ $item->disc > 0 ? (float) $item->disc . '%' : '-' }}</td>
                                            @endif
                                            @if ($quote->tax)
                                                <td class="text-end align-top py-2">{{ number_format($dpp, 0, '', '.') }}</td>
                                            @endif
                                            <td class="text-end align-top py-2 fw-semibold">{{ number_format($item->amount, 0, '', '.') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Financial Summary Box --}}
                    @php
                        $afterDisc = $quote->diskon > 0
                            ? $quote->subtotal - $quote->discount_amount
                            : $quote->subtotal;
                        $isDpInvoice = in_array($invoice->type, ['DP', 'Down Payment']);
                        $isBpInvoice = in_array($invoice->type, ['BP', 'Balance Payment']);
                        $dpPct  = floatval($invoice->percent);
                        $dpBase = round($afterDisc * $dpPct / 100);          // porsi barang untuk DP (sebelum PPN)
                        $dpDpp  = round($dpBase * 11 / 12);                   // DPP nilai lain, diambil dari DP
                        $dpPpn  = round($dpDpp * 0.12);                      // PPN 12% atas DPP DP
                        $dpShip = $quote->shipping > 0 ? round($quote->shipping * $dpPct / 100) : 0;
                        // BP: porsi tagihan ini + porsi DP yang sudah ditagih sebelumnya
                        $bpPct     = floatval($invoice->percent);
                        $bpDpPct   = max(0, 100 - $bpPct);
                        $bpBase    = round($afterDisc * $bpPct / 100);       // porsi barang untuk BP (sebelum PPN)
                        $bpDpBase  = round($afterDisc * $bpDpPct / 100);     // porsi barang DP yang sudah ditagih
                        $bpDpp     = round($bpBase * 11 / 12);               // DPP nilai lain, diambil dari BP
                        $bpPpn     = round($bpDpp * 0.12);                   // PPN 12% atas DPP BP
                        $bpShip    = $quote->shipping > 0 ? round($quote->shipping * $bpPct / 100) : 0;
                    @endphp
                    <div class="d-flex justify-content-end mb-3">
                        <div style="min-width:280px; font-size:12px; border:1px solid #d0d0ff; border-left:4px solid #696cff; border-radius:6px; overflow:hidden; background:#fff;">
                            <table style="width:100%; border-collapse:collapse;">
                                @if ($isDpInvoice)
                                    <tr>
                                        <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="Sub Total">Sub Total</span></td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($quote->subtotal, 0, '', '.') }}</td>
                                    </tr>
                                    @if ($quote->diskon > 0)
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;">Discount{{ $quote->discount_label ? ' ' . $quote->discount_label : '' }}</td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">- Rp {{ number_format($quote->discount_amount, 0, '', '.') }}</td>
                                        </tr>
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;">After Discount</td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                                        </tr>
                                    @endif
                                    <tr style="border-top:1px solid #eeeeff; background:#f7f7f7;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="DP {{ $dpPct }}%">DP {{ $dpPct }}%</span></td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#555;">Rp {{ number_format($dpBase, 0, '', '.') }}</td>
                                    </tr>
                                    @if ($quote->tax)
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="DPP on PPN">DPP Atas PPN</span></td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($dpDpp, 0, '', '.') }}</td>
                                        </tr>
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;">PPN 12%</td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($dpPpn, 0, '', '.') }}</td>
                                        </tr>
                                    @endif
                                    @if ($dpShip > 0)
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;">Shipping Cost ({{ $dpPct }}%)</td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($dpShip, 0, '', '.') }}</td>
                                        </tr>
                                    @endif
                                    @if ($totalPph > 0)
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;">PPH 23</td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">- Rp {{ number_format($totalPph, 0, '', '.') }}</td>
                                        </tr>
                                    @endif
                                    <tr style="border-top:2px solid #e6c300; background:yellow;">
                                        <td style="padding:9px 16px 9px 14px; font-weight:800; font-size:13px; color:#000;">TOTAL</td>
                                        <td style="padding:9px 14px 9px 0; text-align:right; font-weight:800; font-size:13px; color:#000;">Rp {{ number_format($totalAfterPph, 0, '', '.') }}</td>
                                    </tr>
                                @elseif ($isBpInvoice)
                                    <tr>
                                        <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="Sub Total">Sub Total</span></td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($quote->subtotal, 0, '', '.') }}</td>
                                    </tr>
                                    @if ($quote->diskon > 0)
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;">Discount{{ $quote->discount_label ? ' ' . $quote->discount_label : '' }}</td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">- Rp {{ number_format($quote->discount_amount, 0, '', '.') }}</td>
                                        </tr>
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;">After Discount</td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                                        </tr>
                                    @endif
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="DP {{ $bpDpPct }}%">DP {{ $bpDpPct }}%</span></td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($bpDpBase, 0, '', '.') }}</td>
                                    </tr>
                                    <tr style="border-top:1px solid #eeeeff; background:#f7f7f7;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="BP {{ $bpPct }}%">BP {{ $bpPct }}%</span></td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#555;">Rp {{ number_format($bpBase, 0, '', '.') }}</td>
                                    </tr>
                                    @if ($quote->tax)
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="DPP on PPN">DPP Atas PPN</span></td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($bpDpp, 0, '', '.') }}</td>
                                        </tr>
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;">PPN 12%</td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($bpPpn, 0, '', '.') }}</td>
                                        </tr>
                                    @endif
                                    @if ($bpShip > 0)
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;">Shipping Cost ({{ $bpPct }}%)</td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($bpShip, 0, '', '.') }}</td>
                                        </tr>
                                    @endif
                                    @if ($totalPph > 0)
                                        <tr style="border-top:1px solid #eeeeff;">
                                            <td style="padding:6px 16px 6px 14px; color:#555;">PPH 23</td>
                                            <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">- Rp {{ number_format($totalPph, 0, '', '.') }}</td>
                                        </tr>
                                    @endif
                                    <tr style="border-top:2px solid #e6c300; background:yellow;">
                                        <td style="padding:9px 16px 9px 14px; font-weight:800; font-size:13px; color:#000;">TOTAL</td>
                                        <td style="padding:9px 14px 9px 0; text-align:right; font-weight:800; font-size:13px; color:#000;">Rp {{ number_format($totalAfterPph, 0, '', '.') }}</td>
                                    </tr>
                                @else
                                <tr>
                                    <td style="padding:6px 16px 6px 14px; color:#555;">Subtotal</td>
                                    <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($quote->subtotal, 0, '', '.') }}</td>
                                </tr>
                                @if ($quote->diskon > 0)
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">Discount{{ $quote->discount_label ? ' ' . $quote->discount_label : '' }}</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">- Rp {{ number_format($quote->discount_amount, 0, '', '.') }}</td>
                                    </tr>
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">After Discount</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                                    </tr>
                                @endif
                                @if ($quote->tax)
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="DPP on PPN">DPP Atas PPN</span></td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($afterDisc * 11 / 12, 0, '', '.') }}</td>
                                    </tr>
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">PPN 12%</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($quote->tax_amount, 0, '', '.') }}</td>
                                    </tr>
                                @endif
                                @if ($quote->shipping > 0)
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">Shipping Cost</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#333;">Rp {{ number_format($quote->shipping, 0, '', '.') }}</td>
                                    </tr>
                                @endif
                                @if ($totalPph > 0)
                                    <tr style="border-top:1px solid #eeeeff;">
                                        <td style="padding:6px 16px 6px 14px; color:#555;">PPH 23</td>
                                        <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">- Rp {{ number_format($totalPph, 0, '', '.') }}</td>
                                    </tr>
                                @endif
                                @php
                                    $showTagihanBreakdown = in_array($invoice->type, ['DP', 'BP', 'Balance Payment', 'Down Payment']) || floatval($invoice->percent) < 100;
                                @endphp
                                <tr style="border-top:2px solid #d0d0ff; background:{{ !$showTagihanBreakdown ? 'yellow' : '#f0f0ff' }};">
                                    <td style="padding:9px 16px 9px 14px; font-weight:700; font-size:13px; color:{{ !$showTagihanBreakdown ? '#000' : '#3d3d8f' }};">TOTAL</td>
                                    <td style="padding:9px 14px 9px 0; text-align:right; font-weight:700; font-size:13px; color:{{ !$showTagihanBreakdown ? '#000' : '#696cff' }};">Rp {{ number_format($showTagihanBreakdown ? $quote->total : $totalAfterPph, 0, '', '.') }}</td>
                                </tr>
                                @if ($showTagihanBreakdown)
                                    @if (in_array($invoice->type, ['BP', 'Balance Payment']))
                                        @php
                                            $dpInvoices = isset($allInvoices) ? $allInvoices->reject(fn($i) => $i->id == $invoice->id) : collect();
                                            $dpPercent  = $dpInvoices->sum(fn($i) => floatval($i->percent));
                                            if ($dpPercent <= 0 && floatval($invoice->percent) < 100) {
                                                $dpPercent = 100 - floatval($invoice->percent);
                                            }
                                            $dpAmount = round($quote->total * $dpPercent / 100);
                                        @endphp
                                        @if ($dpAmount > 0)
                                            <tr style="border-top:1px solid #eeeeff;">
                                                <td style="padding:6px 16px 6px 14px; color:#555;"><span class="i18n" data-en="DP ALREADY PAID ({{ $dpPercent }}%)">DP TELAH DIBAYAR ({{ $dpPercent }}%)</span></td>
                                                <td style="padding:6px 14px 6px 0; text-align:right; font-weight:500; color:#dc3545;">Rp {{ number_format($dpAmount, 0, '', '.') }}</td>
                                            </tr>
                                        @endif
                                    @endif
                                    @php
                                        $billingType = in_array($invoice->type, ['DP', 'Down Payment']) ? 'DP' : (in_array($invoice->type, ['BP', 'Balance Payment']) ? 'BP' : strtoupper($invoice->type));
                                        $billingPct  = floatval($invoice->percent);
                                        $billingLabelId = 'TAGIHAN ' . $billingType . ' (' . $billingPct . '%)';
                                        $billingLabelEn = 'AMOUNT DUE - ' . $billingType . ' (' . $billingPct . '%)';
                                        // Rincian DPP / PPN untuk porsi tagihan ini (mengikuti rumus DPP nilai lain 11/12 seperti baris kontrak di atas)
                                        $billDpp   = round($afterDisc * 11 / 12 * $billingPct / 100);
                                        $billPpn   = round($quote->tax_amount * $billingPct / 100);
                                        $billGross = $billDpp + $billPpn;
                                    @endphp
                                    @if ($quote->tax)
                                        <tr style="border-top:1px solid #eeeeff; background:#fffdf2;">
                                            <td style="padding:5px 16px 5px 26px; color:#666; font-size:11.5px;"><span class="i18n" data-en="DPP {{ $billingType }} ({{ $billingPct }}%)">DPP {{ $billingType }} ({{ $billingPct }}%)</span></td>
                                            <td style="padding:5px 14px 5px 0; text-align:right; font-weight:500; color:#333; font-size:11.5px;">Rp {{ number_format($billDpp, 0, '', '.') }}</td>
                                        </tr>
                                        <tr style="background:#fffdf2;">
                                            <td style="padding:5px 16px 5px 26px; color:#666; font-size:11.5px;"><span class="i18n" data-en="VAT 12% {{ $billingType }} ({{ $billingPct }}%)">PPN 12% {{ $billingType }} ({{ $billingPct }}%)</span></td>
                                            <td style="padding:5px 14px 5px 0; text-align:right; font-weight:500; color:#333; font-size:11.5px;">Rp {{ number_format($billPpn, 0, '', '.') }}</td>
                                        </tr>
                                        @if ($totalPph > 0)
                                            <tr style="background:#fffdf2;">
                                                <td style="padding:5px 16px 5px 26px; color:#666; font-size:11.5px;"><span class="i18n" data-en="{{ $billingType }} (DPP + VAT)">{{ $billingType }} (DPP + PPN)</span></td>
                                                <td style="padding:5px 14px 5px 0; text-align:right; font-weight:500; color:#333; font-size:11.5px;">Rp {{ number_format($billGross, 0, '', '.') }}</td>
                                            </tr>
                                            <tr style="background:#fffdf2;">
                                                <td style="padding:5px 16px 5px 26px; color:#666; font-size:11.5px;">PPH 23</td>
                                                <td style="padding:5px 14px 5px 0; text-align:right; font-weight:500; color:#dc3545; font-size:11.5px;">- Rp {{ number_format($totalPph, 0, '', '.') }}</td>
                                            </tr>
                                        @endif
                                    @endif
                                    <tr style="border-top:2px solid #e6c300; background:yellow;">
                                        <td style="padding:8px 16px 8px 14px; font-weight:800; font-size:12.5px; color:#000;">
                                            <span class="i18n" data-en="{{ $billingLabelEn }}">{{ $billingLabelId }}</span>
                                        </td>
                                        <td style="padding:8px 14px 8px 0; text-align:right; font-weight:800; font-size:13px; color:#000;">Rp {{ number_format($totalAfterPph, 0, '', '.') }}</td>
                                    </tr>
                                @endif
                                @endif
                            </table>
                        </div>
                    </div>

                    {{-- Terbilang Box --}}
                    <div class="p-3 rounded-0 mb-4" style="background:#f0f2ff; border: 1px dashed #696cff; border-radius:0 !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-cash-multiple text-primary fs-5"></i>
                            <span class="fw-bold text-primary" style="font-size:12px;"><span class="i18n" data-en="In Words :">Terbilang :</span></span>
                            <span class="fw-bold text-dark i18n" style="font-size:12.5px;" data-en="# {{ $terbilangEn }} Rupiah"># {{ $terbilang }} Rupiah</span>
                        </div>
                    </div>

                    {{-- Bank & TTD --}}
                    <div class="row pt-2 align-items-end">
                        <div class="col-md-7">
                            <div class="p-3 rounded-0 border" style="background:#fafafa; font-size:11.5px; border-radius:0 !important;">
                                <p class="fw-bold mb-2 text-dark" style="font-size:12px;">
                                    <i class="mdi mdi-bank-outline me-1 text-primary"></i><span class="i18n" data-en="Payment : Bank Transfer / Giro">Pembayaran : Transfer / Giro</span>
                                </p>
                                <table style="width:100%; border-collapse:collapse;">
                                    @if ($quote->tax)
                                        @if ($isKojisha)
                                            <tr>
                                                <td style="padding:2px 0; color:#555; width:90px;">Nama Bank</td>
                                                <td style="padding:2px 0; font-weight:600; color:#111;">: Bank BCA (IDR)</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:2px 0; color:#555;">Nama Akun</td>
                                                <td style="padding:2px 0; font-weight:700; color:#696cff;">: KOJISHA INNOTIV INDONESIA PT</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:2px 0; color:#555;">No. Rekening</td>
                                                <td style="padding:2px 0; font-weight:700; color:#111;">: 5223876543</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td style="padding:2px 0; color:#555; width:90px;">Nama Bank</td>
                                                <td style="padding:2px 0; font-weight:600; color:#111;">: Bank BCA (IDR)</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:2px 0; color:#555;">Nama Akun</td>
                                                <td style="padding:2px 0; font-weight:700; color:#696cff;">: PT. REFTECH JAYA OPTIMA</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:2px 0; color:#555;">No. Rekening</td>
                                                <td style="padding:2px 0; font-weight:700; color:#111;">: 008 - 6289 - 789</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:2px 0; color:#555;">Swift Code</td>
                                                <td style="padding:2px 0; font-weight:500; color:#333;">: CENAIDJA</td>
                                            </tr>
                                        @endif
                                    @else
                                        @if ($isKojisha)
                                            <tr>
                                                <td style="padding:2px 0; color:#555; width:90px;">Bank Name</td>
                                                <td style="padding:2px 0; font-weight:600; color:#111;">: Bank BCA (IDR)</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:2px 0; color:#555;">Acc Name</td>
                                                <td style="padding:2px 0; font-weight:700; color:#696cff;">: REGITA DWI MELINDA</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:2px 0; color:#555;">Acc No.</td>
                                                <td style="padding:2px 0; font-weight:700; color:#111;">: 1560239137</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td style="padding:2px 0; color:#555; width:90px;">Bank Name</td>
                                                <td style="padding:2px 0; font-weight:600; color:#111;">: Bank BCA (IDR)</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:2px 0; color:#555;">Acc Name</td>
                                                <td style="padding:2px 0; font-weight:700; color:#696cff;">: ARIEP RACHMAN</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:2px 0; color:#555;">Acc No.</td>
                                                <td style="padding:2px 0; font-weight:700; color:#111;">: 166 - 2242 - 271</td>
                                            </tr>
                                        @endif
                                    @endif
                                </table>
                            </div>
                        </div>
                        <div class="col-md-5 text-center mt-3 mt-md-0">
                            @php
                                $signDateBase = $invoice->date ? \Carbon\Carbon::parse($invoice->date) : \Carbon\Carbon::now();
                                $signDateId   = $signDateBase->copy()->locale('id')->translatedFormat('d F Y');
                                $signDateEn   = $signDateBase->copy()->locale('en')->translatedFormat('d F Y');
                            @endphp
                            <p class="mb-1 text-muted" style="font-size:11.5px;">{{ $isKojisha ? 'Bekasi' : 'Bandung' }}, <span class="i18n" data-en="{{ $signDateEn }}">{{ $signDateId }}</span></p>
                            @if ($quote->tax)
                                <p class="fw-bold mb-1 text-dark" style="font-size:12px;">{{ $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima' }}</p>
                            @endif
                            @if (isset($invoice->sign))
                                <div class="my-2">
                                    <img src="{{ url('') . '/' . $invoice->sign }}" alt="Signature" height="70">
                                </div>
                            @else
                                <div style="padding: 30px 0;"></div>
                            @endif
                            <p class="mb-0 fw-bold text-dark" style="font-size:13px; border-bottom:1px solid #ddd; display:inline-block; padding-bottom:2px;">{{ $isKojisha ? 'Dedeh Sulastri' : 'Ariep Rachman' }}</p>
                            <p class="mb-0 text-muted" style="font-size:11px;">Director</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        {{-- End: Invoice --}}

        {{-- Sidebar Actions --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">

            {{-- 1. Primary Actions Card --}}
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-body d-grid gap-2 p-3">
                    <div class="btn-group w-100">
                        <a href="{{ route('invoice.show_unit.print', $invoice->id) }}" target="_blank"
                           class="btn btn-primary waves-effect fw-medium invoice-print-link">
                            <i class="mdi mdi-printer-outline me-1"></i> Print / Download
                        </a>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item invoice-print-link" href="{{ route('invoice.show_unit.print', $invoice->id) }}" target="_blank">
                                    <i class="mdi mdi-file-document-outline me-1"></i> Invoice Print
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('invoice.unit.label_detail', $invoice->id) }}">
                                    <i class="mdi mdi-package-variant-closed me-1"></i> Label Sampul
                                </a>
                            </li>
                        </ul>
                    </div>

                    @if (Auth::user()->role !== 'Sales')
                    <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light border">
                        <label class="form-check-label text-dark small mb-0 fw-medium" for="toggle-spec">
                            <i class="mdi mdi-text-box-search-outline me-1 text-primary"></i>Tampilkan Spek
                        </label>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="toggle-spec"
                                data-id="{{ $invoice->id }}"
                                {{ $invoice->show_spec ? 'checked' : '' }}>
                        </div>
                    </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button class="btn btn-label-secondary flex-grow-1 waves-effect" id="backButton">
                            <i class="mdi mdi-arrow-left me-1"></i>Back
                        </button>
                        <a href="{{ route('unit-quotation.show', $quote->id) }}"
                           class="btn btn-label-info flex-grow-1 waves-effect">
                            <i class="mdi mdi-file-eye-outline me-1"></i>Quotation
                        </a>
                    </div>
                </div>
            </div>

            {{-- Invoice Settings Card --}}
            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header py-2 px-3 bg-light border-bottom">
                        <small class="text-uppercase text-muted fw-bold" style="font-size:10px; letter-spacing:0.5px;">Invoice Settings</small>
                    </div>
                    <div class="card-body d-grid gap-2 p-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 waves-effect text-start"
                            data-bs-toggle="modal" data-bs-target="#changeDate">
                            <i class="mdi mdi-calendar-edit me-1 text-primary"></i> Change Date
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 waves-effect text-start"
                            data-bs-toggle="modal" data-bs-target="#editInvoiceModal">
                            <i class="mdi mdi-pencil-outline me-1 text-primary"></i> Edit No Invoice / PO / Term
                        </button>
                        @if ($isTempo)
                            <button type="button" class="btn btn-outline-warning btn-sm w-100 waves-effect text-start"
                                data-bs-toggle="modal" data-bs-target="#dueDate">
                                <i class="mdi mdi-calendar-clock me-1 text-warning"></i> Set / Edit Due Date
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 2. Invoice Info Card --}}
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header py-2 px-3 bg-light border-bottom">
                    <small class="text-uppercase text-muted fw-bold" style="font-size:10px; letter-spacing:0.5px;">Invoice Information</small>
                </div>
                <div class="card-body d-grid gap-2 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">No. Invoice</span>
                        <span class="fw-bold small text-primary">#{{ $invoice->no_invoice }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Tanggal</span>
                        <span class="fw-semibold small">{{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('d M Y') : '-' }}</span>
                    </div>
                    @if ($isTempo)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Jatuh Tempo</span>
                            @if ($dueDateDisplay)
                                <span class="fw-bold small text-warning"><i class="mdi mdi-calendar-clock me-1"></i>{{ $dueDateDisplay->format('d M Y') }}</span>
                            @else
                                <span class="badge bg-label-secondary" style="font-size:10px;">Belum Di-set</span>
                            @endif
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">No. PO</span>
                        <span class="fw-semibold small">{{ $quote->po_number ?? '-' }}</span>
                    </div>
                    @if ($quote->po_file)
                        <a href="{{ $quote->po_file_url }}" target="_blank"
                           class="btn btn-outline-success btn-sm w-100 waves-effect">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Lihat File PO
                        </a>
                    @endif
                    @if ($allInvoices->count() > 1)
                        <hr class="my-1">
                        @foreach ($allInvoices as $inv)
                            @php
                                $invTypeCode = in_array($inv->type, ['DP', 'Down Payment']) ? 'DP' : (in_array($inv->type, ['BP', 'Balance Payment']) ? 'BP' : strtoupper($inv->type));
                            @endphp
                            <a href="{{ route('invoice.show_unit', $inv->id) }}"
                               class="btn btn-sm {{ $inv->id == $invoice->id ? 'btn-primary' : 'btn-outline-secondary' }} w-100 waves-effect">
                                <span class="badge {{ $invTypeCode === 'DP' ? 'bg-warning' : 'bg-info' }} me-1">{{ $invTypeCode }}</span>
                                {{ $inv->no_invoice ?? 'Pending' }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- 3. Tax / PPH & Hand Sign --}}
            @if (Auth::user()->role != 'Sales')
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header py-2 px-3 bg-light border-bottom">
                    <small class="text-uppercase text-muted fw-bold" style="font-size:10px; letter-spacing:0.5px;">Tax (PPH) &amp; Hand Sign</small>
                </div>
                <div class="card-body d-grid gap-2 p-3">
                    @php $pphPerItem = $quote->details->sum(fn($d) => ($d->amount * $d->pph) / 100); @endphp
                    <div class="d-flex gap-2">
                        @if ($pphPerItem > 0)
                            <a href="#" class="btn btn-outline-danger btn-sm flex-grow-1 waves-effect delete-pph-unit"
                               data-id="{{ $invoice->id }}" title="Delete PPH 23">
                                <i class="mdi mdi-delete-outline me-1"></i>Delete PPH 23
                            </a>
                        @else
                            <button type="button" class="btn btn-outline-info btn-sm flex-grow-1 waves-effect"
                                data-bs-toggle="modal" data-bs-target="#modalAddPph">
                                <i class="mdi mdi-calculator me-1"></i>PPH 23
                            </button>
                        @endif

                        @if (($invoice->pph ?? 0) > 0)
                            <a href="#" class="btn btn-outline-danger btn-sm flex-grow-1 waves-effect delete-pph-manual-unit"
                               data-id="{{ $invoice->id }}" title="Delete PPH Manual">
                                <i class="mdi mdi-delete-outline me-1"></i>Delete PPH Manual
                            </a>
                        @else
                            <button type="button" class="btn btn-outline-secondary btn-sm flex-grow-1 waves-effect"
                                data-bs-toggle="modal" data-bs-target="#modalAddPphManual">
                                <i class="mdi mdi-pencil-box-outline me-1"></i>PPH Manual
                            </button>
                        @endif
                    </div>

                    @if (in_array(Auth::user()->role, ['Admin', 'Accounting']))
                        @if (isset($invoice->sign))
                            <a href="#" class="btn btn-outline-danger btn-sm w-100 waves-effect delete-hand-sign-unit"
                               data-id="{{ $invoice->id }}">
                                <i class="mdi mdi-signature-freehand me-1"></i>Delete Hand Sign
                            </a>
                        @else
                            <a href="#" class="btn btn-outline-secondary btn-sm w-100 waves-effect input-hand-sign-unit"
                               data-id="{{ $invoice->id }}">
                                <i class="mdi mdi-draw me-1"></i>Hand Sign
                            </a>
                        @endif
                    @endif
                </div>
            </div>
            @endif

            {{-- 5. Payment --}}
            <div class="card mb-3">
                <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between">
                    <small class="text-uppercase text-muted fw-semibold">Payment</small>
                    @if ($payments->isNotEmpty())
                        <span class="badge bg-label-success small">Rp {{ number_format($payments->sum('amount'), 0, '', '.') }}</span>
                    @endif
                </div>

                {{-- Invoice Summary --}}
                <div class="card-body p-0">
                    @foreach ($allInvoices as $inv)
                        @php
                            $invTypeCode = in_array($inv->type, ['DP', 'Down Payment']) ? 'DP' : (in_array($inv->type, ['BP', 'Balance Payment']) ? 'BP' : strtoupper($inv->type));
                            $invTotal = $quote->total;
                            if ($invTypeCode === 'DP' && $inv->term) {
                                $pct      = preg_match('/^DP\s*(\d+(?:\.\d+)?)\s*%/i', $inv->term, $m) ? floatval($m[1]) : 0;
                                $invTotal = round($quote->total * $pct / 100);
                            } elseif ($invTypeCode === 'BP') {
                                $dpInv    = $allInvoices->first(fn($i) => in_array($i->type, ['DP', 'Down Payment']));
                                $pct      = $dpInv?->term && preg_match('/^DP\s*(\d+(?:\.\d+)?)\s*%/i', $dpInv->term, $m) ? floatval($m[1]) : 0;
                                $invTotal = $quote->total - round($quote->total * $pct / 100);
                            }
                        @endphp
                        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
                            <div>
                                <p class="mb-0 small fw-semibold">
                                    <span class="badge {{ $invTypeCode === 'DP' ? 'bg-warning' : ($invTypeCode === 'BP' ? 'bg-info' : 'bg-primary') }} me-1" style="font-size:10px">{{ $invTypeCode }}</span>
                                    Rp {{ number_format($invTotal, 0, '', '.') }}
                                </p>
                                <p class="mb-0 text-muted" style="font-size:11px">{{ $inv->no_invoice ?? 'Belum diterbitkan' }}</p>
                            </div>
                            @if ($inv->status_p)
                                <span class="badge bg-label-success" style="font-size:10px">Verified</span>
                            @else
                                <span class="badge bg-label-warning" style="font-size:10px">Unpaid</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Payment Records --}}
                @if ($payments->isNotEmpty())
                <div class="border-top">
                    <div class="px-3 pt-2 pb-1">
                        <small class="text-uppercase text-muted fw-semibold" style="font-size:10px">Payment Received</small>
                    </div>
                    @foreach ($payments as $pay)
                    @php
                        $netPay = $pay->amount - ($pay->pph ?? 0) - ($pay->cost ?? 0);
                    @endphp
                    <div class="d-flex align-items-start justify-content-between px-3 py-2 border-bottom" id="pay-row-{{ $pay->id }}">
                        <div>
                            <p class="mb-0 fw-semibold small">
                                Rp {{ number_format($pay->amount, 0, '', '.') }}
                                @if ($pay->type)
                                    <span class="badge bg-label-primary ms-1" style="font-size:10px">{{ $pay->type }}</span>
                                @endif
                            </p>
                            @if ($pay->pph > 0 || $pay->cost > 0)
                                <div class="d-flex flex-wrap gap-1 my-1">
                                    @if ($pay->pph > 0)
                                        <span class="badge bg-label-danger" style="font-size:10px">PPH 23: -Rp {{ number_format($pay->pph, 0, '', '.') }}</span>
                                    @endif
                                    @if ($pay->cost > 0)
                                        <span class="badge bg-label-warning" style="font-size:10px">Admin Bank: -Rp {{ number_format($pay->cost, 0, '', '.') }}</span>
                                    @endif
                                    <span class="badge bg-label-success fw-bold" style="font-size:10px">Nett: Rp {{ number_format($netPay, 0, '', '.') }}</span>
                                </div>
                            @endif
                            @if ($pay->method)
                                <p class="mb-0 text-muted" style="font-size:11px">{{ $pay->method }}</p>
                            @endif
                            @if ($pay->note)
                                <p class="mb-0 text-muted" style="font-size:11px">{{ $pay->note }}</p>
                            @endif
                            <div class="mt-1 d-flex flex-wrap gap-1">
                                @if ($pay->file)
                                    <a href="{{ asset($pay->file) }}" target="_blank"
                                       class="badge bg-label-success text-decoration-none" style="font-size:10px">
                                        <i class="mdi mdi-file-check-outline"></i> Bukti Transfer
                                    </a>
                                @else
                                    <span class="badge bg-label-warning" style="font-size:10px">Belum ada bukti</span>
                                @endif
                                @if ($pay->level == 1)
                                    <span class="badge bg-label-success" style="font-size:10px">
                                        <i class="mdi mdi-check-circle-outline"></i> Paid
                                    </span>
                                @else
                                    <span class="badge bg-label-warning" style="font-size:10px">
                                        <i class="mdi mdi-clock-outline"></i> Unconfirmed
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1 ms-2">
                            <a href="{{ route('payment_detail.payment', $pay->id) }}"
                               class="btn btn-sm btn-outline-primary waves-effect py-1 px-2"
                               style="font-size:11px;"
                               title="Lihat Detail Payment">
                                <i class="mdi mdi-eye-outline me-1"></i>Detail
                            </a>
                            <div class="d-flex gap-1">
                                @if (!$pay->file && Auth::user()->role === 'Sales')
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-success btn-upload-proof-inv"
                                        data-id="{{ $pay->id }}" title="Upload Bukti">
                                        <i class="mdi mdi-upload"></i>
                                    </button>
                                @endif
                                @if ($pay->file && $pay->level == 0 && Auth::user()->role === 'Sales')
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-proof"
                                        data-id="{{ $pay->id }}" title="Hapus Bukti Transfer">
                                        <i class="mdi mdi-file-remove-outline"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Actions --}}
                @if ($quote->status === 'po_received' && Auth::user()->role === 'Sales')
                    <div class="card-footer p-3 d-grid gap-2">
                        <button type="button" class="btn btn-outline-success w-100 waves-effect"
                            data-bs-toggle="modal" data-bs-target="#modalAddPayment">
                            <i class="mdi mdi-cash-plus me-1"></i> Tambah Payment
                        </button>
                    </div>
                @elseif ($payments->isEmpty())
                    <div class="card-footer p-3">
                        <div class="alert alert-warning p-2 mb-0" style="font-size:11px; border-radius:0 !important;">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> Menunggu Sales menambahkan data Payment.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    </div> {{-- End Tab 1 (#tab-invoice) --}}

    {{-- TAB 2: DELIVERY ORDER / SURAT JALAN --}}
    <div class="tab-pane fade" id="tab-delivery" role="tabpanel">
        <div class="row">
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">

                {{-- Sales Order Delivery & Address Instructions Card (Ultra-Clean & Modern Minimalist) --}}
                @if (isset($pendingPO) && $pendingPO)
                    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid #e2e8f0 !important; border-radius: 12px; background: #ffffff;">
                        <div class="card-body p-4">
                            {{-- Header Title & Badges --}}
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-3 border-bottom" style="border-color: #f1f5f9 !important;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="background: #f1f5f9; color: #475569; width: 38px; height: 38px;">
                                        <i class="mdi mdi-truck-fast-outline fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-0.5">
                                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 14.5px;">Instruksi Pengiriman & Alamat</h6>
                                            <span class="badge bg-label-primary font-monospace" style="font-size: 10.5px; padding: 2px 7px;">{{ $pendingPO->no_pending ?: '#' . $pendingPO->id }}</span>
                                        </div>
                                        <small class="text-muted" style="font-size: 11.5px;">Instruksi pengiriman dari Sales Order</small>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if ($pendingPO->combine_shipping_and_parts)
                                        <span class="badge rounded-pill bg-label-success px-3 py-1.5 fw-semibold" style="font-size: 11px; border: 1px solid #bbf7d0;">
                                            <i class="mdi mdi-link-variant me-1"></i> Barang & Part Digabung
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-label-danger px-3 py-1.5 fw-semibold" style="font-size: 11px; border: 1px solid #fecaca;">
                                            <i class="mdi mdi-link-variant-off me-1"></i> Barang & Part Dipisah
                                        </span>
                                    @endif

                                    @if ($pendingPO->ekspidisi)
                                        <span class="badge rounded-pill bg-label-info px-3 py-1.5 fw-semibold" style="font-size: 11px; border: 1px solid #bae6fd;">
                                            <i class="mdi mdi-truck-outline me-1"></i> Ekspedisi: {{ $pendingPO->ekspidisi }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- 2 Address Cards --}}
                            <div class="row g-3" style="font-size: 12px;">
                                {{-- Alamat Pengiriman Barang / Unit --}}
                                <div class="col-md-6">
                                    <div class="p-3.5 rounded-3 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: #e2e8f0 !important;">
                                            <span class="fw-bold text-dark text-uppercase small" style="font-size: 11px; letter-spacing: 0.4px;">
                                                <i class="mdi mdi-map-marker-outline text-danger me-1 fs-6"></i> Alamat Pengiriman Barang / Unit
                                            </span>
                                            @if (($pendingPO->shipping_address_type ?? 'customer') === 'customer')
                                                <span class="badge bg-white text-muted border" style="font-size: 9.5px; font-weight: 500;">Sesuai Customer</span>
                                            @else
                                                <span class="badge bg-warning text-dark" style="font-size: 9.5px; font-weight: 600;">Manual</span>
                                            @endif
                                        </div>
                                        <p class="mb-2 text-dark fw-medium" style="line-height: 1.5; font-size: 12.5px;">
                                            @if (($pendingPO->shipping_address_type ?? 'customer') === 'customer')
                                                {{ $quote->client->address ?? '-' }}
                                            @else
                                                {{ $pendingPO->shipping_address_manual ?: ($quote->client->address ?? '-') }}
                                            @endif
                                        </p>
                                        @if ($pendingPO->shipping_recipient)
                                            <div class="pt-2 mt-2 border-top text-muted d-flex align-items-center justify-content-between" style="border-color: #e2e8f0 !important; font-size: 11.5px;">
                                                <span><i class="mdi mdi-account-outline me-1 text-primary"></i>Penerima: <strong class="text-dark">{{ $pendingPO->shipping_recipient->name_pic }}</strong></span>
                                                @if ($pendingPO->shipping_recipient->phone)
                                                    <span class="text-primary fw-semibold"><i class="mdi mdi-phone-outline me-1"></i>{{ $pendingPO->shipping_recipient->phone }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Alamat Pengiriman Dokumen / Invoice --}}
                                <div class="col-md-6">
                                    <div class="p-3.5 rounded-3 h-100" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: #e2e8f0 !important;">
                                            <span class="fw-bold text-dark text-uppercase small" style="font-size: 11px; letter-spacing: 0.4px;">
                                                <i class="mdi mdi-file-document-outline text-primary me-1 fs-6"></i> Alamat Pengiriman Dokumen
                                            </span>
                                            @if (($pendingPO->doc_address_type ?? 'customer') === 'customer')
                                                <span class="badge bg-white text-muted border" style="font-size: 9.5px; font-weight: 500;">Sesuai Customer</span>
                                            @else
                                                <span class="badge bg-warning text-dark" style="font-size: 9.5px; font-weight: 600;">Manual</span>
                                            @endif
                                        </div>
                                        <p class="mb-2 text-dark fw-medium" style="line-height: 1.5; font-size: 12.5px;">
                                            @if (($pendingPO->doc_address_type ?? 'customer') === 'customer')
                                                {{ $quote->client->address ?? '-' }}
                                            @else
                                                {{ $pendingPO->doc_address_manual ?: ($quote->client->address ?? '-') }}
                                            @endif
                                        </p>
                                        @if ($pendingPO->doc_recipient)
                                            <div class="pt-2 mt-2 border-top text-muted d-flex align-items-center justify-content-between" style="border-color: #e2e8f0 !important; font-size: 11.5px;">
                                                <span><i class="mdi mdi-account-outline me-1 text-primary"></i>Penerima: <strong class="text-dark">{{ $pendingPO->doc_recipient->name_pic }}</strong></span>
                                                @if ($pendingPO->doc_recipient->phone)
                                                    <span class="text-primary fw-semibold"><i class="mdi mdi-phone-outline me-1"></i>{{ $pendingPO->doc_recipient->phone }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 1. Progress Pengiriman Barang (Parsial Tracker) --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="mdi mdi-chart-box-outline me-2 text-primary fs-5"></i> Progress Pengiriman Item (Shipment Tracker)
                        </h6>
                        @php
                            $totalOrdered = $quote->details->where('type', '!=', 'header')->sum('qty');
                            $totalRemaining = $quote->details->where('type', '!=', 'header')->sum('remaining_qty');
                            $totalDelivered = max(0, $totalOrdered - $totalRemaining);
                            $percentDelivered = $totalOrdered > 0 ? round(($totalDelivered / $totalOrdered) * 100) : 0;
                        @endphp
                        <span class="badge {{ $totalRemaining == 0 ? 'bg-success' : ($totalDelivered > 0 ? 'bg-warning' : 'bg-secondary') }} py-1.5 px-3 fs-7">
                            {{ $totalRemaining == 0 ? 'Terkirim Semua (100%)' : ($totalDelivered > 0 ? "Terkirim Parsial ({$percentDelivered}%)" : 'Belum Ada Pengiriman') }}
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size:12px;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width:5%;">No</th>
                                        <th>Deskripsi Barang / Sparepart</th>
                                        <th class="text-center" style="width:12%;">Qty Pesan</th>
                                        <th class="text-center" style="width:12%;">Terkirim</th>
                                        <th class="text-center" style="width:12%;">Sisa Qty</th>
                                        <th class="text-center" style="width:18%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $itemNo = 1; @endphp
                                    @foreach ($quote->details as $item)
                                        @if ($item->type === 'header')
                                            <tr style="background:#f4f4fe;">
                                                <td colspan="6" class="fw-bold text-uppercase py-2 px-3 text-primary" style="font-size:11px; letter-spacing:0.5px;">
                                                    <i class="mdi mdi-bookmark-outline me-1"></i> {{ $item->label }}
                                                </td>
                                            </tr>
                                        @else
                                            @php
                                                if ($item->id_equivalent && $item->equivalent) {
                                                    $spParts = array_filter([
                                                        $item->equivalent->brand ?? '',
                                                        $item->equivalent->pn ?? '',
                                                        $item->label ?: optional($item->equivalent->product)->description ?: $item->description
                                                    ]);
                                                    $itemDescStr = implode(' — ', $spParts);
                                                } elseif ($item->type === 'unit' && $item->unit) {
                                                    $itemDescStr = $item->label ?: trim($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : ''));
                                                } else {
                                                    $itemDescStr = $item->label ?: $item->description;
                                                }
                                                $deliveredQty = max(0, $item->qty - $item->remaining_qty);
                                            @endphp
                                            <tr>
                                                <td class="text-center fw-semibold">{{ $itemNo++ }}</td>
                                                <td class="fw-semibold text-dark">{{ $itemDescStr }}</td>
                                                <td class="text-center fw-bold">{{ (float)$item->qty }} {{ $item->info_qty }}</td>
                                                <td class="text-center text-success fw-bold">{{ (float)$deliveredQty }} {{ $item->info_qty }}</td>
                                                <td class="text-center {{ $item->remaining_qty > 0 ? 'text-danger' : 'text-muted' }} fw-bold">{{ (float)$item->remaining_qty }} {{ $item->info_qty }}</td>
                                                <td class="text-center">
                                                    @if ($item->remaining_qty == 0)
                                                        <span class="badge bg-label-success"><i class="mdi mdi-check-circle-outline me-1"></i>Terkirim Semua</span>
                                                    @elseif ($deliveredQty > 0)
                                                        <span class="badge bg-label-warning"><i class="mdi mdi-clock-outline me-1"></i>Sisa {{ (float)$item->remaining_qty }}</span>
                                                    @else
                                                        <span class="badge bg-label-secondary"><i class="mdi mdi-truck-outline me-1"></i>Belum Dikirim</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 2. Riwayat Surat Jalan Terbuat (Delivery History Log) --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="mdi mdi-history me-2 text-primary fs-5"></i> Riwayat Surat Jalan Terbuat ({{ $quote->deliveries->count() }})
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        @if ($quote->deliveries->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="mdi mdi-truck-delivery-outline fs-1 text-light d-block mb-2"></i>
                                <p class="mb-1 fw-semibold">Belum Ada Surat Jalan Terbuat</p>
                                <p class="small text-muted mb-0">Gunakan tombol <strong>"Buat Surat Jalan Baru"</strong> pada menu sebelah kanan untuk menerbitkan Surat Jalan.</p>
                            </div>
                        @else
                            <div class="d-flex flex-column gap-3">
                                @foreach ($quote->deliveries->sortByDesc('created_at') as $del)
                                    <div class="border rounded p-3 bg-white hover-shadow transition-all">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                    <h6 class="fw-bold mb-0 text-primary">Surat Jalan #{{ $del->id }}</h6>
                                                    <span class="badge {{ strtolower($del->type) === 'ekspedisi' ? 'bg-label-info' : 'bg-label-primary' }}">
                                                        <i class="mdi {{ strtolower($del->type) === 'ekspedisi' ? 'mdi-package-variant-closed' : 'mdi-account-hard-hat' }} me-1"></i>
                                                        {{ ucfirst($del->type ?? 'Ekspedisi') }}
                                                    </span>
                                                    @if ($del->isSignedByCustomer())
                                                        <span class="badge bg-label-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                                                            <i class="mdi mdi-check-decagram me-1"></i> Telah Ditandatangani
                                                        </span>
                                                    @else
                                                        <span class="badge bg-label-warning rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 11px;">
                                                            <i class="mdi mdi-draw me-1"></i> Menunggu TTD
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="mb-0 text-muted small">
                                                    <i class="mdi mdi-calendar-outline me-1"></i>Tanggal: {{ $del->date ? \Carbon\Carbon::parse($del->date)->format('d-m-Y') : '' }}
                                                    &nbsp;|&nbsp;
                                                    <i class="mdi mdi-map-marker-outline me-1"></i>Alamat: {{ $quote->client ? ($del->destination == '1' ? $quote->client->address : $quote->client->subAddress) : '-' }}
                                                </p>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2.5" data-bs-toggle="modal" data-bs-target="#modal-delivery-preview-{{ $del->id }}">
                                                    <i class="mdi mdi-eye-outline me-1"></i> Detail
                                                </button>
                                                <a href="{{ route('print.delivery', $del->id) }}" target="_blank" class="btn btn-sm btn-primary py-1 px-2.5">
                                                    <i class="mdi mdi-printer-outline me-1"></i> Cetak SJ
                                                </a>
                                                @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                                                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 delete-delivery" data-id="{{ $del->id }}" title="Hapus Surat Jalan">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        @if ($del->detail && $del->detail->isNotEmpty())
                                            @php
                                                $itemCountInDel = $del->detail->where('type', '!=', 'header')->count();
                                                $totalQtyInDel = $del->detail->where('type', '!=', 'header')->sum('qty');
                                            @endphp
                                            <div class="border-top pt-2 mt-2 d-flex align-items-center justify-content-between text-muted small" style="font-size:11.5px;">
                                                <span>
                                                    <i class="mdi mdi-cube-outline me-1 text-primary"></i>Total Item Dikirim: <strong class="text-dark">{{ $itemCountInDel }} Jenis Barang</strong> (Total Qty: {{ (float)$totalQtyInDel }})
                                                </span>
                                                <button type="button" class="btn btn-xs btn-label-primary py-0.5 px-2 rounded" data-bs-toggle="modal" data-bs-target="#modal-delivery-preview-{{ $del->id }}">
                                                    <i class="mdi mdi-text-box-search-outline me-1"></i>Lihat Rincian Item
                                                </button>
                                            </div>
                                        @endif

                                        {{-- Digital Signature Subsection (BAST & Contract Pattern) --}}
                                        <div class="mt-2.5 pt-2 border-top" style="border-color: #f1f5f9 !important;">
                                            @if ($del->isSignedByCustomer())
                                                <div class="p-2.5 rounded-3 border d-flex flex-wrap align-items-center justify-content-between gap-2" style="background: #f8fafc; font-size: 11.5px;">
                                                    <div class="d-flex align-items-center gap-2.5">
                                                        @if ($del->customer_signature)
                                                            <div class="bg-white border rounded p-1 d-flex align-items-center justify-content-center shadow-xs position-relative" style="width: 58px; height: 42px;">
                                                                <img src="{{ asset($del->customer_signature) }}" alt="TTD" style="max-width: 100%; max-height: 100%; object-fit: contain; z-index: 2;">
                                                                @if ($del->customer_signed_stamp)
                                                                    <img src="{{ asset($del->customer_signed_stamp) }}" alt="Stempel" style="position: absolute; max-height: 32px; opacity: 0.6; transform: rotate(-5deg); z-index: 1;">
                                                                @endif
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold text-dark d-flex align-items-center gap-1.5">
                                                                <i class="mdi mdi-check-circle text-success fs-6"></i>
                                                                <span>Diterima &amp; Ditandatangani: <strong class="text-primary">{{ $del->customer_signer_name }}</strong></span>
                                                                @if ($del->customer_signer_position)
                                                                    <span class="text-muted fw-normal">({{ $del->customer_signer_position }})</span>
                                                                @endif
                                                            </div>
                                                            <div class="text-muted mt-0.5" style="font-size: 10.5px;">
                                                                <i class="mdi mdi-clock-outline me-1"></i>{{ $del->customer_signed_at ? $del->customer_signed_at->format('d/m/Y H:i') : '-' }} WIB
                                                                @if ($del->customer_ip)
                                                                    &nbsp;&bull;&nbsp;<i class="mdi mdi-ip-network-outline me-0.5"></i>IP: {{ $del->customer_ip }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex align-items-center gap-1.5 ms-auto">
                                                        <a href="{{ $del->sign_url }}" target="_blank" class="btn btn-xs btn-outline-primary py-1 px-2.5 rounded d-flex align-items-center gap-1">
                                                            <i class="mdi mdi-eye-outline"></i>
                                                            <span>Lihat Portal TTD</span>
                                                        </a>
                                                        @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                                                            <form action="{{ route('delivery.reset-signature', $del->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus / mereset tanda tangan customer pada Surat Jalan ini? Customer dapat menandatangani ulang.');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-outline-danger py-1 px-2 rounded d-flex align-items-center gap-1" title="Hapus / Reset Tanda Tangan">
                                                                    <i class="mdi mdi-refresh"></i>
                                                                    <span>Reset TTD</span>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="p-2.5 rounded-3 border" style="background: #fafafa; font-size: 11.5px;">
                                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-2">
                                                        <span class="fw-semibold text-dark d-flex align-items-center gap-1">
                                                            <i class="mdi mdi-draw text-warning fs-6"></i>
                                                            <span>Tanda Tangan Digital Penerima (Online Signature)</span>
                                                        </span>
                                                        <span class="text-muted" style="font-size: 10.5px;">Kirimkan tautan agar customer dapat memeriksa &amp; menandatangani di HP/Tablet:</span>
                                                    </div>

                                                    @php
                                                        $picPhone = preg_replace('/[^0-9]/', '', ($quote->pic?->phone ?? ''));
                                                        if (str_starts_with($picPhone, '0')) {
                                                            $picPhone = '62' . substr($picPhone, 1);
                                                        }
                                                        $clientComp = $quote->client?->company ?? '';
                                                        $picName = $quote->pic?->name ?? '';
                                                        $entityFullName = ($quote->client?->info === 'Kojisha') ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima';
                                                        $waMsg = rawurlencode("Halo Bapak/Ibu " . ($picName ?: '') . " (" . $clientComp . "),\n\nBerikut kami lampirkan tautan dokumen Surat Jalan (Delivery Order #" . $del->id . ") untuk PO " . ($quote->po_number ?: $quote->no_quote) . ".\nSilakan periksa rincian penerimaan barang dan bubuhi tanda tangan digital melalui tautan berikut:\n" . $del->sign_url . "\n\nTerima kasih.\n" . $entityFullName);
                                                        $waUrl = "https://wa.me/" . ($picPhone ?: '') . "?text=" . $waMsg;
                                                    @endphp

                                                    <div class="row g-2 align-items-center">
                                                        <div class="col-lg-7 col-md-6 col-12">
                                                            <div class="input-group input-group-sm">
                                                                <input type="text" class="form-control bg-white font-monospace" id="del-sign-url-{{ $del->id }}" value="{{ $del->sign_url }}" readonly style="font-size: 11px;">
                                                                <button class="btn btn-outline-primary btn-copy-del-url" type="button" data-input-id="del-sign-url-{{ $del->id }}" title="Salin Tautan Tanda Tangan">
                                                                    <i class="mdi mdi-content-copy me-1"></i>Salin Link
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-5 col-md-6 col-12 d-flex gap-1.5 justify-content-md-end">
                                                            <a href="{{ $waUrl }}" target="_blank" class="btn btn-sm btn-success py-1 px-2.5 text-white d-inline-flex align-items-center gap-1 shadow-xs" style="font-size: 11px;">
                                                                <i class="mdi mdi-whatsapp fs-6"></i>
                                                                <span>WhatsApp</span>
                                                            </a>
                                                            <a href="{{ $del->sign_url }}" target="_blank" class="btn btn-sm btn-primary py-1 px-2.5 d-inline-flex align-items-center gap-1 shadow-xs" style="font-size: 11px;">
                                                                <i class="mdi mdi-open-in-new fs-6"></i>
                                                                <span>Buka Portal TTD</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Right Sidebar Actions for Delivery Tab --}}
            <div class="col-xl-3 col-md-4 col-12">
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-2 px-3">
                        <small class="text-uppercase text-muted fw-bold">Delivery Quick Actions</small>
                    </div>
                    <div class="card-body p-3 d-grid gap-2">
                        @if ($quote->status === 'po_received' && $totalRemaining > 0 && (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting'))
                            <button type="button" class="btn btn-success d-grid w-100 shadow-sm py-2" data-bs-toggle="modal" data-bs-target="#modalSJUnit">
                                <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                                    <i class="mdi mdi-truck-delivery-outline fs-5"></i> Buat Surat Jalan Baru
                                </span>
                            </button>
                        @elseif ($totalRemaining == 0)
                            <div class="alert alert-success p-2 mb-0 text-center" style="font-size:12px;">
                                <i class="mdi mdi-check-circle fs-5 d-block mb-1"></i>
                                <strong>Pengiriman Selesai</strong><br>Seluruh item telah berhasil dikirim.
                            </div>
                        @endif
                        <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary w-100 py-2">
                            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> {{-- End Tab 2 --}}

    {{-- TAB 3: BAST (BERITA ACARA SERAH TERIMA) --}}
    @if (isset($bast) && $bast)
        @php
            $isReftech = $bast->entity === 'Reftech';
            $entityFullName = $isReftech ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia';
        @endphp
        <div class="tab-pane fade" id="tab-bast" role="tabpanel">
            <div class="row">
                {{-- Document Card (Visual matches BAST Print) --}}
                <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                    <div class="card invoice-preview-card border-0 shadow-sm p-4 p-md-5" style="background: #ffffff; color: #000000; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                        
                        {{-- Header --}}
                        <div class="d-flex justify-content-between align-items-start pb-3 mb-3"
                            style="border-bottom: 2px solid #cbd5e1; display: flex !important; flex-direction: row !important; justify-content: space-between !important; align-items: flex-start !important;">
                            <div class="mb-0 pb-1">
                                @if ($isReftech)
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-1">
                                        <span class="app-brand-logo demo">
                                            <span style="color: var(--bs-primary)">
                                                <img class="text-md"
                                                    src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                                    alt="Reftech Logo" width="160">
                                            </span>
                                        </span>
                                    </div>
                                    <p class="mb-0 text-uppercase fw-bold" style="font-size: 11.5px; color: #4f46e5 !important; letter-spacing: 0.5px; line-height: 1.2;">
                                        COMPRESSED AIR SOLUTION
                                    </p>
                                    <p class="mb-1" style="font-size: 9.5px; font-weight: 600; color: #000000;">
                                        Sales &nbsp;|&nbsp; Service &nbsp;|&nbsp; Rental &nbsp;|&nbsp; Measurement Air Audit
                                    </p>
                                    <div style="font-size: 9px; color: #000000; font-weight: 500;">
                                        <i class="mdi mdi-certificate-outline me-1 text-primary"></i>
                                        <span class="fw-bold" style="color: #696cff !important;">ISO Certified:</span> 
                                        ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                                    </div>
                                @else
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                        <span class="app-brand-logo demo">
                                            <span style="color: var(--bs-primary)">
                                                <img class="text-md" src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt="Kojisha Logo" width="160">
                                            </span>
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="text-end" style="padding-top: 4px;">
                                @if ($isReftech)
                                    <p class="fw-bolder text-uppercase" style="font-size: 15px; color: #4f46e5 !important; letter-spacing: 0.3px; line-height: 1.2; margin-bottom: 4px !important;">PT REFTECH JAYA OPTIMA</p>
                                    <div style="font-size: 10px; line-height: 1.35; color: #000000; font-weight: 500;">
                                        <p class="mb-0" style="color: #000000;">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                                        <p class="mb-0" style="color: #000000;">Bandung – Jawa Barat 40218</p>
                                        <p class="mb-0 text-nowrap" style="white-space: nowrap; color: #000000;">
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>022 54417653{{ '  |  ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>admin@reftech.id{{ '  |  ' }}<i class="mdi mdi-web scaleX-n1-rtl me-1 mdi-14px text-primary"></i>www.reftech.id
                                        </p>
                                    </div>
                                @else
                                    <p class="fw-bolder text-uppercase" style="font-size: 15px; color: #4f46e5 !important; letter-spacing: 0.3px; line-height: 1.2; margin-bottom: 4px !important;">PT KOJISHA INNOTIV INDONESIA</p>
                                    <div style="font-size: 10px; line-height: 1.35; color: #000000; font-weight: 500;">
                                        <p class="mb-0" style="color: #000000;">Jl. Nancep No. 45A, Setu</p>
                                        <p class="mb-0" style="color: #000000;">Cibitung - Kab. Bekasi 17320</p>
                                        <p class="mb-0 text-nowrap" style="white-space: nowrap; color: #000000;">
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>+62 812-1000-0997
                                            {{ '   ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px text-primary"></i>admin@kojisha.com
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Title --}}
                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-1 text-uppercase" style="color: #4f46e5 !important; font-size: 18px; letter-spacing: 0.5px;">{{ $bast->type === 'Rental' ? 'Berita Acara Serah Terima Unit Rental' : 'Berita Acara Serah Terima Pekerjaan' }}</h4>
                            <div class="fw-bold" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #4f46e5 !important; font-size: 18px; letter-spacing: 0.5px;">{{ $bast->no_bast }}</div>
                        </div>

                        @if ($bast->type === 'Rental')
                            <p class="mb-3" style="font-size: 13.5px; line-height: 1.6; color: #000000;">
                                Bersama dengan ini kami <strong class="text-uppercase" style="color: #000000;">{{ $entityFullName }}</strong>, telah melakukan pengiriman, instalasi, dan commissioning serta <strong style="color: #000000;">MENYERAHKAN UNIT RENTAL</strong> dalam kondisi baik dan siap beroperasi kepada <strong style="color: #000000;">{{ $bast->customer_name }}</strong> untuk unit sbb :
                            </p>
                        @else
                            <p class="mb-3" style="font-size: 13.5px; line-height: 1.6; color: #000000;">
                                Bersama dengan ini kami <strong class="text-uppercase" style="color: #000000;">{{ $entityFullName }}</strong>, telah menyelesaikan pekerjaan hingga
                                <strong style="color: #000000;">SELESAI</strong> untuk pekerjaan sbb :
                            </p>
                        @endif

                        <div class="border rounded p-3 text-center fw-bold text-uppercase mb-3" style="font-size: 16px; color: #000000; border: 1.5px solid #000000 !important; background: #fafafa;">
                            {{ $bast->work_title }}
                        </div>

                        <table class="mb-2" style="font-size: 13.5px; width: 100%; color: #000000;">
                            @if ($bast->type === 'Rental')
                                <tr>
                                    <td style="width: 250px; padding: 4px 0; color: #000000;">Tanggal Commissioning</td>
                                    <td style="width: 20px; padding: 4px 0; color: #000000;">:</td>
                                    <td style="padding: 4px 0; color: #000000; font-weight: 600;">
                                        @if ($bast->work_date)
                                            {{ \Carbon\Carbon::parse($bast->work_date)->format('d-m-Y') }}
                                        @else
                                            <span style="display: inline-block; min-width: 200px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; color: #000000;">Masa Rental</td>
                                    <td style="padding: 4px 0; color: #000000;">:</td>
                                    <td style="padding: 4px 0; color: #000000; font-weight: 600;">
                                        @if ($bast->rental_start_date && $bast->rental_end_date)
                                            {{ \Carbon\Carbon::parse($bast->rental_start_date)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($bast->rental_end_date)->format('d-m-Y') }}
                                        @elseif ($bast->rental_start_date)
                                            {{ \Carbon\Carbon::parse($bast->rental_start_date)->format('d-m-Y') }} s/d <span style="display: inline-block; min-width: 120px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                                        @elseif ($bast->rental_end_date)
                                            <span style="display: inline-block; min-width: 120px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span> s/d {{ \Carbon\Carbon::parse($bast->rental_end_date)->format('d-m-Y') }}
                                        @else
                                            <span style="display: inline-block; min-width: 120px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span> s/d <span style="display: inline-block; min-width: 120px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                                        @endif
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td style="width: 250px; padding: 4px 0; color: #000000;">Tanggal Pekerjaan</td>
                                    <td style="width: 20px; padding: 4px 0; color: #000000;">:</td>
                                    <td style="padding: 4px 0; color: #000000; font-weight: 600;">
                                        @if ($bast->work_date)
                                            {{ \Carbon\Carbon::parse($bast->work_date)->format('d-m-Y') }}
                                        @else
                                            <span style="display: inline-block; min-width: 200px; border-bottom: 1.5px dotted #000; height: 16px; vertical-align: middle;"></span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td style="padding: 4px 0; color: #000000;">Sesuai PO/ kontrak no.</td>
                                <td style="padding: 4px 0; color: #000000;">:</td>
                                <td style="padding: 4px 0; color: #000000; font-weight: 600;">{{ $bast->po_number ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 4px 0; color: #000000;">Terhadap unit-unit sebagai berikut</td>
                                <td style="padding: 4px 0; color: #000000;">:</td>
                                <td style="padding: 4px 0; color: #000000;"></td>
                            </tr>
                        </table>

                        <table class="table table-bordered mb-3" style="font-size: 13px; color: #000000; border: 1.5px solid #000000;">
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th style="width: 8%; color: #000000; font-weight: 700; border: 1px solid #000000; text-align: center;">No.</th>
                                    <th style="color: #000000; font-weight: 700; border: 1px solid #000000;">Unit</th>
                                    <th style="color: #000000; font-weight: 700; border: 1px solid #000000;">Serial No.</th>
                                    <th style="width: 15%; color: #000000; font-weight: 700; border: 1px solid #000000; text-align: center;">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($bast->units && $bast->units->isNotEmpty())
                                    @foreach ($bast->units as $unit)
                                        <tr>
                                            <td style="color: #000000; border: 1px solid #000000; text-align: center;">{{ $loop->iteration }}</td>
                                            <td style="color: #000000; border: 1px solid #000000; font-weight: 500;">{{ $unit->unit_name }}</td>
                                            <td style="color: #000000; border: 1px solid #000000;">{{ $unit->serial_no ?: '-' }}</td>
                                            <td style="color: #000000; border: 1px solid #000000; text-align: center; font-weight: 600;">{{ $unit->qty }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-2 text-muted" style="border: 1px solid #000000;">Tidak ada unit terdaftar.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <p class="mb-2" style="font-size: 13.5px; font-weight: 700; color: #000000;">
                            Hasil Pengecekan Pada Saat Test Running :
                        </p>

                        <div class="mb-3">
                            <textarea class="form-control" rows="3" style="font-size: 13px; line-height: 1.5; border: 1.5px solid #000000; width: 100%; resize: none; background: #fff; color: #000000; font-weight: 500;" readonly>{{ $bast->test_running_result }}</textarea>
                        </div>

                        @if ($bast->type === 'Rental')
                            <p class="mb-1" style="font-size: 13.5px; color: #000000;">
                                Demikian <strong style="color: #000000;">BERITA ACARA SERAH TERIMA UNIT RENTAL</strong> ini di tanda tangani oleh kedua belah pihak :
                            </p>

                            <table class="table-borderless mb-3 ms-2" style="font-size: 13.5px; line-height: 1.6; width: auto; color: #000000;">
                                <tr>
                                    <td style="width: 220px; padding: 2px 0; color: #000000;">• Yang Menyerahkan (Penyedia)</td>
                                    <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                                    <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $entityFullName }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 220px; padding: 2px 0; color: #000000;">• Yang Menerima (Penyewa)</td>
                                    <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                                    <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $bast->customer_name }}</strong></td>
                                </tr>
                            </table>

                            <p class="mb-3" style="font-size: 13.5px; line-height: 1.6; color: #000000;">
                                Dengan ini unit rental tersebut di atas dinyatakan telah <strong style="color: #000000;">DITERIMA DALAM KONDISI BAIK &amp; SIAP BEROPERASI</strong>. Unit tetap merupakan milik/aset <strong style="color: #000000;">{{ $entityFullName }}</strong> dan akan diambil/ditarik kembali setelah masa sewa/rental berakhir.
                            </p>
                        @else
                            <p class="mb-1" style="font-size: 13.5px; color: #000000;">
                                Demikian <strong style="color: #000000;">BERITA ACARA SERAH TERIMA PEKERJAAN</strong> ini di tanda tangani oleh kedua belah pihak :
                            </p>

                            <table class="table-borderless mb-3 ms-2" style="font-size: 13.5px; line-height: 1.6; width: auto; color: #000000;">
                                <tr>
                                    <td style="width: 180px; padding: 2px 0; color: #000000;">• Pelaksana pekerjaan</td>
                                    <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                                    <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $entityFullName }}</strong></td>
                                </tr>
                                <tr>
                                    <td style="width: 180px; padding: 2px 0; color: #000000;">• Pemberi pekerjaan</td>
                                    <td style="width: 20px; padding: 2px 0; color: #000000;">:</td>
                                    <td style="padding: 2px 0; color: #000000;"><strong style="color: #000000;">{{ $bast->customer_name }}</strong></td>
                                </tr>
                            </table>

                            <p class="mb-3" style="font-size: 13.5px; line-height: 1.6; color: #000000;">
                                Dengan ini segala hal yang berhubungan dengan pekerjaan tersebut diatas dinyatakan
                                <strong style="color: #000000;">SELESAI</strong>.
                            </p>
                        @endif

                        {{-- Signature --}}
                        <div class="d-flex justify-content-between pt-4 mt-2" style="font-size: 13.5px; color: #000000;">
                            <div class="text-center" style="width: 42%;">
                                <p class="fw-bold text-uppercase mb-0" style="font-size: 13.5px; color: #000000;">{{ $entityFullName }}</p>
                                @if ($bast->sign)
                                    <div class="d-flex align-items-center justify-content-center" style="height: 80px; margin: 4px 0;">
                                        <img src="{{ asset($bast->sign) }}" alt="Hand Sign" style="max-height: 75px; max-width: 170px; object-fit: contain;">
                                    </div>
                                    <p class="mb-0 fw-bold text-dark" style="font-size: 13.5px; color: #000000;">
                                        ( <u>{{ $isReftech ? 'Ariep Rachman' : 'Dedeh Sulastri' }}</u> )
                                    </p>
                                @else
                                    <div style="height: 80px;"></div>
                                    <p class="mb-0" style="font-size: 13.5px; color: #000000; font-weight: 600;">( ........................................ )</p>
                                @endif
                            </div>
                            <div class="text-center" style="width: 42%;">
                                <p class="fw-bold text-uppercase mb-0" style="font-size: 13.5px; color: #000000;">{{ $bast->customer_name }}</p>
                                @if ($bast->customer_signature)
                                    <div class="d-flex align-items-center justify-content-center position-relative" style="height: 80px; margin: 4px 0;">
                                        <img src="{{ asset($bast->customer_signature) }}" alt="Customer Signature" style="max-height: 75px; max-width: 170px; object-fit: contain; z-index: 2;">
                                        @if ($bast->customer_signed_stamp)
                                            <img src="{{ asset($bast->customer_signed_stamp) }}" alt="Stamp" style="position: absolute; max-height: 65px; opacity: 0.75; z-index: 1; transform: rotate(-5deg);">
                                        @endif
                                    </div>
                                    <p class="mb-0 fw-bold text-dark" style="font-size: 13.5px; color: #000000;">
                                        ( <u>{{ $bast->customer_signer_name }}</u> )
                                    </p>
                                    @if ($bast->customer_signer_position)
                                        <small class="text-muted d-block" style="font-size: 11px;">{{ $bast->customer_signer_position }}</small>
                                    @endif
                                @else
                                    <div style="height: 80px;"></div>
                                    <p class="mb-0" style="font-size: 13.5px; color: #000000; font-weight: 600;">( ........................................ )</p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Sidebar BAST --}}
                <div class="col-xl-3 col-md-4 col-12">
                    {{-- Share Link TTD Online Customer --}}
                    <div class="card mb-3 border-0 shadow-sm" style="border-radius: 8px;">
                        <div class="card-header py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between" style="background-color: #f8fafc;">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-1.5" style="font-size: 12.5px;">
                                <i class="mdi mdi-draw text-primary fs-5"></i>
                                <span>TTD Online Customer</span>
                            </h6>
                            @if ($bast->isSignedByCustomer())
                                <span class="badge bg-success" style="font-size: 10px;">Sudah TTD</span>
                            @else
                                <span class="badge bg-warning text-dark" style="font-size: 10px;">Menunggu TTD</span>
                            @endif
                        </div>
                        <div class="card-body p-3">
                            @if ($bast->isSignedByCustomer())
                                <div class="p-2.5 rounded mb-2.5" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; font-size: 11.5px;">
                                    <div class="d-flex align-items-center gap-1 text-success fw-bold mb-1">
                                        <i class="mdi mdi-check-circle"></i> Ditandatangani Customer
                                    </div>
                                    <div class="text-dark"><strong>{{ $bast->customer_signer_name }}</strong></div>
                                    @if ($bast->customer_signer_position)
                                        <div class="text-muted small">{{ $bast->customer_signer_position }}</div>
                                    @endif
                                    <div class="text-muted mt-1" style="font-size: 10.5px;">
                                        <i class="mdi mdi-clock-outline me-1"></i>{{ $bast->customer_signed_at ? $bast->customer_signed_at->format('d/m/Y H:i') : '-' }} WIB
                                    </div>
                                    @if ($bast->customer_ip)
                                        <div class="text-muted" style="font-size: 10.5px;">
                                            <i class="mdi mdi-ip-network-outline me-1"></i>IP: {{ $bast->customer_ip }}
                                        </div>
                                    @endif
                                    @if ($bast->customer_signature)
                                        <div class="mt-2 text-center p-1.5 bg-white rounded border">
                                            <img src="{{ asset($bast->customer_signature) }}" alt="Customer Signature" style="max-height: 45px; max-width: 100%; object-fit: contain;">
                                        </div>
                                    @endif
                                </div>

                                <div class="d-flex flex-column gap-2">
                                    <a href="{{ $bast->sign_url }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                        <i class="mdi mdi-eye-outline"></i>
                                        <span>Lihat Halaman TTD</span>
                                    </a>
                                    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                                        <form action="{{ route('bast.reset-signature', $bast->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus / mereset tanda tangan customer pada BAST ini? Customer akan dapat menandatangani ulang.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                                <i class="mdi mdi-delete-outline"></i>
                                                <span>Hapus / Reset TTD Customer</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted mb-2" style="font-size: 11.5px; line-height: 1.4;">
                                    Kirim tautan berikut ke customer agar dapat memeriksa BAST &amp; membubuhkan tanda tangan digital:
                                </p>

                                <div class="input-group input-group-sm mb-2.5">
                                    <input type="text" class="form-control" id="bast-sign-url" value="{{ $bast->sign_url }}" readonly style="font-size: 11px;">
                                    <button class="btn btn-primary" type="button" id="btn-copy-bast-sign-url" title="Salin Link">
                                        <i class="mdi mdi-content-copy"></i>
                                    </button>
                                </div>

                                @php
                                    $picPhone = preg_replace('/[^0-9]/', '', ($quote->pic?->phone ?? ''));
                                    if (str_starts_with($picPhone, '0')) {
                                        $picPhone = '62' . substr($picPhone, 1);
                                    }
                                    $clientComp = $bast->customer_name ?: ($quote->client?->company ?? '');
                                    $picName = $quote->pic?->name ?? '';
                                    $docTypeLabel = $bast->type === 'Rental' ? 'Berita Acara Serah Terima Unit Rental' : 'Berita Acara Serah Terima (BAST)';
                                    $waMessage = rawurlencode("Halo Bapak/Ibu " . ($picName ?: '') . " (" . $clientComp . "),\n\nBerikut kami lampirkan tautan dokumen " . $docTypeLabel . " (" . ($bast->no_bast ?: '') . ").\nSilakan periksa rincian serah terima dan bubuhi tanda tangan digital melalui tautan berikut:\n" . $bast->sign_url . "\n\nTerima kasih.\n" . $entityFullName);
                                    $waLink = "https://wa.me/" . ($picPhone ?: '') . "?text=" . $waMessage;
                                @endphp

                                <div class="d-flex flex-column gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1" id="btn-copy-bast-link-action">
                                        <i class="mdi mdi-link-variant"></i>
                                        <span>Salin Link TTD</span>
                                    </button>
                                    <a href="{{ $waLink }}" target="_blank" class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1 text-white">
                                        <i class="mdi mdi-whatsapp fs-5"></i>
                                        <span>Kirim via WhatsApp</span>
                                    </a>
                                    <a href="{{ $bast->sign_url }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                        <i class="mdi mdi-open-in-new"></i>
                                        <span>Buka Portal TTD</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom py-2 px-3">
                            <small class="text-uppercase text-muted fw-bold">Aksi BAST</small>
                        </div>
                        <div class="card-body p-3 d-grid gap-2">
                            {{-- Hand Sign Button --}}
                            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                                @if ($bast->sign)
                                    <button type="button" class="btn btn-outline-danger d-grid w-100 delete-hand-sign-bast shadow-sm py-2" data-id="{{ $bast->id }}">
                                        <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                                            <i class="mdi mdi-signature-freehand fs-5"></i> Delete Hand Sign
                                        </span>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-outline-success d-grid w-100 input-hand-sign-bast shadow-sm py-2" data-id="{{ $bast->id }}">
                                        <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                                            <i class="mdi mdi-draw fs-5"></i> Hand Sign
                                        </span>
                                    </button>
                                @endif
                            @endif

                            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                                <button type="button" class="btn btn-outline-primary d-grid w-100 btn-edit-bast shadow-sm py-2" data-id="{{ $bast->id }}">
                                    <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                                        <i class="mdi mdi-pencil-outline fs-5"></i> Edit BAST
                                    </span>
                                </button>
                            @endif
                            <a href="{{ route('bast.print', $bast->id) }}" target="_blank" class="btn btn-primary d-grid w-100 shadow-sm py-2">
                                <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                                    <i class="mdi mdi-printer-outline fs-5"></i> Cetak BAST
                                </span>
                            </a>
                            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                                <button type="button" class="btn btn-outline-danger d-grid w-100 delete-bast shadow-sm py-2" data-id="{{ $bast->id }}">
                                    <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                                        <i class="mdi mdi-delete-outline fs-5"></i> Hapus BAST
                                    </span>
                                </button>
                            @endif
                            <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary w-100 py-2">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar Invoice
                            </a>
                        </div>
                    </div>

                    {{-- BAST Quick Information Card --}}
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom py-2 px-3">
                            <small class="text-uppercase text-muted fw-bold" style="font-size:10px; letter-spacing:0.5px;">Informasi BAST</small>
                        </div>
                        <div class="card-body p-3 d-grid gap-2" style="font-size: 12px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">No. BAST</span>
                                <span class="fw-bold text-primary">{{ $bast->no_bast }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Tipe</span>
                                <span class="badge {{ $bast->type === 'Rental' ? 'bg-label-info' : 'bg-label-secondary' }}">{{ $bast->type === 'Rental' ? 'BAST Rental' : 'Default BAST' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Entitas</span>
                                <span class="fw-semibold text-dark">{{ $bast->entity }}</span>
                            </div>
                            @if ($bast->type === 'Rental')
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Commissioning</span>
                                    <span class="fw-semibold text-dark">{{ $bast->work_date ? \Carbon\Carbon::parse($bast->work_date)->format('d M Y') : 'Isi Manual' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Masa Rental</span>
                                    <span class="fw-semibold text-dark" style="font-size:11px;">
                                        @if ($bast->rental_start_date && $bast->rental_end_date)
                                            {{ \Carbon\Carbon::parse($bast->rental_start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($bast->rental_end_date)->format('d/m/Y') }}
                                        @else
                                            Isi Manual
                                        @endif
                                    </span>
                                </div>
                            @else
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Tgl Pekerjaan</span>
                                    <span class="fw-semibold text-dark">{{ $bast->work_date ? \Carbon\Carbon::parse($bast->work_date)->format('d M Y') : 'Isi Manual' }}</span>
                                </div>
                            @endif
                            @if ($bast->po_number)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">No. PO</span>
                                    <span class="badge bg-label-primary">{{ $bast->po_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div> {{-- End Tab Content --}}
</div> {{-- End Nav Align Top --}}

    {{-- MODALS PREVIEW SURAT JALAN (Root level to prevent backdrop z-index overlay issue) --}}
    @if ($quote->deliveries && $quote->deliveries->isNotEmpty())
        @foreach ($quote->deliveries as $del)
            <div class="modal fade" id="modal-delivery-preview-{{ $del->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light py-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-truck-delivery-outline text-primary fs-4"></i>
                                <div>
                                    <h5 class="modal-title fw-bold mb-0">Surat Jalan #{{ $del->id }}</h5>
                                    <small class="text-muted">Jenis: {{ ucfirst($del->type ?? 'Ekspedisi') }}</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            {{-- Header Document Info --}}
                            <div class="row g-3 mb-4 pb-3 border-bottom" style="font-size: 12px;">
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded border h-100">
                                        <span class="text-uppercase text-muted fw-bold small d-block mb-1">Pengirim (Shipper)</span>
                                        <strong class="text-dark fs-6 d-block mb-1">PT Reftech Jaya Optima</strong>
                                        <p class="mb-0 text-muted" style="line-height:1.4;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded border h-100">
                                        <span class="text-uppercase text-muted fw-bold small d-block mb-1">Penerima (Deliver To)</span>
                                        <strong class="text-dark fs-6 d-block mb-1">{{ $quote->client->company ?? '-' }}</strong>
                                        <p class="mb-0 text-muted" style="line-height:1.4;">
                                            <i class="mdi mdi-map-marker-outline me-1"></i>{{ $quote->client ? ($del->destination == '1' ? $quote->client->address : $quote->client->subAddress) : '-' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded border h-100">
                                        <span class="text-uppercase text-muted fw-bold small d-block mb-1">Shipment Info</span>
                                        <p class="mb-1"><span class="fw-semibold">PO / Quote No:</span> <span class="badge bg-label-primary">{{ $quote->po_number ?: $quote->no_quote }}</span></p>
                                        <p class="mb-1"><span class="fw-semibold">Jenis:</span> {{ ucfirst($del->type ?? 'Ekspedisi') }}</p>
                                        <p class="mb-1 d-flex align-items-center gap-1">
                                            <span class="fw-semibold">Tanggal:</span>
                                            <span>{{ $del->date ? \Carbon\Carbon::parse($del->date)->format('d-m-Y') : '' }}</span>
                                            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                                                <button type="button" class="btn btn-xs btn-link p-0 ms-1" data-bs-toggle="collapse" data-bs-target="#edit-delivery-date-{{ $del->id }}" title="Ubah Tanggal">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>
                                            @endif
                                        </p>
                                        @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                                            <div class="collapse" id="edit-delivery-date-{{ $del->id }}">
                                                <form action="{{ route('delivery.update', $del->id) }}" method="POST" class="d-flex gap-1 align-items-center mt-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="date" name="date" class="form-control form-control-sm" style="width:150px;" value="{{ $del->date }}">
                                                    <button type="submit" class="btn btn-xs btn-primary py-1 px-2">Simpan</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Table Items --}}
                            <h6 class="fw-bold mb-2 text-dark"><i class="mdi mdi-format-list-bulleted me-1 text-primary"></i> Daftar Item yang Dikirim</h6>
                            <div class="table-responsive border rounded mb-3">
                                <table class="table table-sm table-striped table-hover m-0" style="font-size: 12px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width:6%;">No</th>
                                            <th>Deskripsi Barang</th>
                                            <th class="text-center" style="width:20%;">Qty Dikirim</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $itemNoModal = 1; @endphp
                                        @if ($del->detail && $del->detail->isNotEmpty())
                                            @foreach ($del->detail as $dt)
                                                @if (($dt->type ?? 'item') === 'header')
                                                    <tr style="background:#f0f0ff;">
                                                        <td colspan="3" class="fw-bold text-uppercase py-1.5 px-3 text-primary" style="font-size:11px;">
                                                            <i class="mdi mdi-bookmark-outline me-1"></i> {{ $dt->desc }}
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr>
                                                        <td class="text-center align-middle">{{ $itemNoModal++ }}</td>
                                                        <td class="align-middle fw-medium text-dark">{{ $dt->desc }}</td>
                                                        <td class="text-center align-middle fw-bold text-primary">{{ (float)$dt->qty }} {{ $dt->info_qty }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="3" class="text-center py-3 text-muted">Belum ada detail barang.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            {{-- Digital Signature Info in Modal --}}
                            <div class="card border mb-2" style="border-radius: 8px; background: #f8fafc;">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">
                                            <i class="mdi mdi-draw text-primary me-1"></i> Tanda Tangan Penerima (Digital Signature)
                                        </h6>
                                        @if ($del->isSignedByCustomer())
                                            <span class="badge bg-label-success rounded-pill px-2.5 py-0.5" style="font-size: 11px;">
                                                <i class="mdi mdi-check-circle me-1"></i> Sudah Ditandatangani
                                            </span>
                                        @else
                                            <span class="badge bg-label-warning rounded-pill px-2.5 py-0.5" style="font-size: 11px;">
                                                <i class="mdi mdi-clock-outline me-1"></i> Menunggu Tanda Tangan
                                            </span>
                                        @endif
                                    </div>

                                    @if ($del->isSignedByCustomer())
                                        <div class="d-flex align-items-center gap-3 p-2.5 bg-white border rounded">
                                            @if ($del->customer_signature)
                                                <div class="p-1 border rounded bg-lighter d-flex align-items-center justify-content-center position-relative" style="width: 80px; height: 55px;">
                                                    <img src="{{ asset($del->customer_signature) }}" alt="TTD" style="max-height: 100%; max-width: 100%; object-fit: contain; z-index: 2;">
                                                    @if ($del->customer_signed_stamp)
                                                        <img src="{{ asset($del->customer_signed_stamp) }}" alt="Stempel" style="position: absolute; max-height: 40px; opacity: 0.6; transform: rotate(-5deg); z-index: 1;">
                                                    @endif
                                                </div>
                                            @endif
                                            <div style="font-size: 12px;">
                                                <div class="fw-bold text-dark">{{ $del->customer_signer_name }}</div>
                                                @if ($del->customer_signer_position)
                                                    <div class="text-muted">{{ $del->customer_signer_position }}</div>
                                                @endif
                                                <div class="text-muted mt-1" style="font-size: 11px;">
                                                    <i class="mdi mdi-calendar-clock me-1"></i>{{ $del->customer_signed_at ? $del->customer_signed_at->format('d F Y, H:i') : '-' }} WIB
                                                    @if ($del->customer_ip)
                                                        &bull; IP: {{ $del->customer_ip }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted mb-2" style="font-size: 11.5px;">
                                            Customer belum membubuhi tanda tangan. Salin tautan atau buka portal di bawah:
                                        </p>
                                        <div class="input-group input-group-sm mb-2">
                                            <input type="text" class="form-control font-monospace" id="modal-del-sign-url-{{ $del->id }}" value="{{ $del->sign_url }}" readonly style="font-size: 11px;">
                                            <button class="btn btn-outline-primary btn-copy-del-url" type="button" data-input-id="modal-del-sign-url-{{ $del->id }}">
                                                <i class="mdi mdi-content-copy me-1"></i>Salin
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <a href="{{ $del->sign_url }}" target="_blank" class="btn btn-outline-primary">
                                <i class="mdi mdi-draw me-1"></i> Buka Portal TTD
                            </a>
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                            <a href="{{ route('print.delivery', $del->id) }}" target="_blank" class="btn btn-primary">
                                <i class="mdi mdi-printer-outline me-1"></i> Cetak Surat Jalan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- Modal Buat Surat Jalan --}}
    @if (($quote->status === 'po_received') && (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting'))
        <div class="modal fade" id="modalSJUnit" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('unit-quotation.storeDelivery', $quote->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_invoice" value="{{ $invoice->id }}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Buat Surat Jalan — {{ $quote->no_quote }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal <span class="text-muted fw-normal">(opsional)</span></label>
                                    <input type="date" class="form-control" name="date"
                                        value="{{ \Carbon\Carbon::today()->toDateString() }}">
                                    <div class="form-text">Boleh dikosongkan kalau tanggal belum dilampirkan.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tujuan / Alamat</label>
                                    <select class="form-select" name="destination" required>
                                        @if ($quote->client)
                                            <option value="1">{{ $quote->client->address }}</option>
                                            @if ($quote->client->subAddress)
                                                <option value="2">{{ $quote->client->subAddress }}</option>
                                            @endif
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jenis Pengiriman</label>
                                    <select class="form-select" name="type">
                                        <option value="Ekspedisi">Ekspedisi</option>
                                        <option value="Teknisi">Teknisi</option>
                                    </select>
                                </div>
                            </div>

                            <label class="form-label fw-semibold">Item yang Dikirim</label>
                            <p class="text-muted mb-2" style="font-size:11.5px;">
                                Centang item yang dikirim kali ini. Qty default = sisa yang belum terkirim, bisa dikurangi kalau cuma kirim sebagian.
                            </p>
                            <div class="table-responsive border rounded">
                                <table class="table table-sm table-bordered m-0" style="font-size:12px;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:5%"></th>
                                            <th>Description</th>
                                            <th class="text-center" style="width:15%">Sisa</th>
                                            <th class="text-center" style="width:20%">Qty Dikirim</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($quote->details as $item)
                                            @php
                                                if ($item->id_equivalent && $item->equivalent) {
                                                    $spParts = array_filter([
                                                        $item->equivalent->brand ?? '',
                                                        $item->equivalent->pn ?? '',
                                                        $item->label ?: optional($item->equivalent->product)->description ?: $item->description
                                                    ]);
                                                    $itemDisplayLabel = implode(' — ', $spParts);
                                                } elseif ($item->type === 'unit' && $item->unit) {
                                                    $itemDisplayLabel = $item->label ?: trim($item->unit->brand . ' ' . $item->unit->sku . ($item->unit->model ? ' — ' . $item->unit->model : ''));
                                                } else {
                                                    $itemDisplayLabel = $item->label ?: $item->description;
                                                }
                                            @endphp
                                            @if ($item->type === 'header')
                                                <tr style="background:#f0f0ff;">
                                                    <td colspan="4" class="fw-bold text-uppercase py-1 px-2 text-primary" style="font-size:11px;">{{ $item->label }}</td>
                                                </tr>
                                            @elseif ($item->remaining_qty > 0)
                                                <tr>
                                                    <td class="text-center align-middle">
                                                        <input class="form-check-input item-check" type="checkbox" name="item_ids[]"
                                                            value="{{ $item->id }}" data-target="qty-{{ $item->id }}" checked>
                                                    </td>
                                                    <td class="align-middle fw-medium">{{ $itemDisplayLabel }}</td>
                                                    <td class="text-center align-middle">{{ $item->remaining_qty }} {{ $item->info_qty }}</td>
                                                    <td class="align-middle">
                                                        <input type="number" step="any" min="0" max="{{ $item->remaining_qty }}"
                                                            value="{{ $item->remaining_qty }}" name="qty[{{ $item->id }}]"
                                                            id="qty-{{ $item->id }}" class="form-control form-control-sm">
                                                    </td>
                                                </tr>
                                            @else
                                                <tr class="text-muted">
                                                    <td class="text-center align-middle">
                                                        <input type="checkbox" class="form-check-input" disabled>
                                                    </td>
                                                    <td class="align-middle text-decoration-line-through">{{ $itemDisplayLabel }}</td>
                                                    <td class="text-center align-middle">0</td>
                                                    <td class="align-middle"><span class="badge bg-label-success">Terkirim Semua</span></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Buat Surat Jalan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal PPH 23 per item --}}
    <div class="modal fade" id="modalAddPph" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('invoice.unit.pph', $invoice->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add PPH 23 — {{ $invoice->no_invoice }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @foreach ($quote->details as $i => $detail)
                            <div class="row g-2 mb-3 align-items-center">
                                <div class="col-8">
                                    <p class="mb-0 fw-medium" style="font-size: 13px">
                                        @if ($detail->type === 'unit' && $detail->unit)
                                            {{ $detail->label ?: ($detail->unit->brand . ' ' . $detail->unit->model) }}
                                        @else
                                            {{ $detail->label }}
                                        @endif
                                    </p>
                                </div>
                                <div class="col-4">
                                    <div class="input-group input-group-merge">
                                        <input type="number" class="form-control" name="pph[{{ $i }}]"
                                               value="{{ $detail->pph }}" placeholder="2" min="0" max="100" step="0.1">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal PPH Manual --}}
    <div class="modal-onboarding modal fade animate__animated" id="modalAddPphManual" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <form action="{{ route('invoice.unit.pph_manual', $invoice->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="onboarding-content mb-0">
                            <h4 class="onboarding-title text-body">Add PPH Manual</h4>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp.</span>
                                            <input type="text" class="form-control invoice-item-pph-manual-label"
                                                id="pphManualLabel" name="pphLabel" placeholder="Put PPH Here"
                                                data-type="currency" value="{{ old('pph') }}">
                                            <input class="form-control invoice-item-pph-manual" type="number"
                                                name="pph" id="pphManual" value="{{ old('pph') }}" hidden>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Confirm Payment --}}
    <div class="modal fade" id="confirmPayment" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('invoice.confirm_payment_unit', $invoice->id) }}" method="POST" id="formConfirmPaymentUnit">
                    @csrf
                    <div class="modal-header bg-light py-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-cash-check text-primary fs-4"></i>
                            <h5 class="modal-title fw-bold mb-0">Konfirmasi Pembayaran</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        @php
                            $unconfirmedPays = $payments->where('level', 0);
                            $targetPayAmount = $unconfirmedPays->isNotEmpty() ? $unconfirmedPays->sum('amount') : $payments->sum('amount');
                            $firstUnconfirmed = $unconfirmedPays->first() ?? $payments->first();
                            $defaultPph = $firstUnconfirmed?->pph ?? 0;
                            $defaultCost = $firstUnconfirmed?->cost ?? 0;
                        @endphp

                        <div class="alert alert-primary d-flex align-items-center justify-content-between p-3 mb-3" style="border-radius: 8px;">
                            <div>
                                <small class="d-block text-muted text-uppercase fw-semibold" style="font-size: 11px;">Nominal Pembayaran (Bruto)</small>
                                <span class="fw-bold fs-5 text-primary" id="dispConfirmGross">Rp {{ number_format($targetPayAmount, 0, ',', '.') }}</span>
                            </div>
                            <span class="badge bg-primary">Unit Invoice</span>
                        </div>

                        {{-- Baris 1: PPH 23 --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex justify-content-between">
                                <span>PPH 23 <span class="text-muted fw-normal">(Opsional)</span></span>
                                <span class="text-danger small" style="font-size: 11px;">*Mengurangi nominal payment</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-medium">Rp</span>
                                <input type="text" class="form-control format-rupiah-confirm" 
                                    id="confirm_pph_label" 
                                    placeholder="0"
                                    value="{{ $defaultPph > 0 ? number_format($defaultPph, 0, ',', '.') : '' }}">
                                <input type="hidden" name="pph" id="confirm_pph" value="{{ $defaultPph }}">
                            </div>
                            <div class="form-text text-muted" style="font-size: 11px;">
                                Masukkan nominal rupiah PPH 23 jika dipotong oleh customer.
                            </div>
                        </div>

                        {{-- Baris 2: Admin Bank --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex justify-content-between">
                                <span>Admin Bank <span class="text-muted fw-normal">(Opsional)</span></span>
                                <span class="text-warning small" style="font-size: 11px;">*Biaya transaksi</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-medium">Rp</span>
                                <input type="text" class="form-control format-rupiah-confirm" 
                                    id="confirm_cost_label" 
                                    placeholder="0"
                                    value="{{ $defaultCost > 0 ? number_format($defaultCost, 0, ',', '.') : '' }}">
                                <input type="hidden" name="cost" id="confirm_cost" value="{{ $defaultCost }}">
                            </div>
                            <div class="form-text text-muted" style="font-size: 11px;">
                                Masukkan nominal biaya admin bank / transfer jika ada.
                            </div>
                        </div>

                        {{-- Ringkasan Realtime --}}
                        <div class="bg-light p-3 rounded mb-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-1 small">
                                <span class="text-muted">Nominal Bruto:</span>
                                <span class="fw-semibold text-dark">Rp {{ number_format($targetPayAmount, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1 small text-danger" id="rowPreviewPph" style="display: {{ $defaultPph > 0 ? 'flex' : 'none' }} !important;">
                                <span>Potongan PPH 23:</span>
                                <span class="fw-semibold" id="dispPreviewPph">- Rp {{ number_format($defaultPph, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1 small text-warning" id="rowPreviewCost" style="display: {{ $defaultCost > 0 ? 'flex' : 'none' }} !important;">
                                <span>Biaya Admin Bank:</span>
                                <span class="fw-semibold" id="dispPreviewCost">- Rp {{ number_format($defaultCost, 0, ',', '.') }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">Nett Diterima:</span>
                                <span class="fw-bold text-success fs-6" id="dispPreviewNett">Rp {{ number_format($targetPayAmount - $defaultPph - $defaultCost, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Catatan <span class="text-muted fw-normal">(Opsional)</span></label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Catatan pembayaran...">{{ $firstUnconfirmed?->note ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-bold waves-effect">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Add Payment --}}
    <div class="modal fade" id="modalAddPayment" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-cash-plus me-1"></i> Tambah Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('unit-quotation.add-payment', $quote->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe Payment <span class="text-danger">*</span></label>
                            <select class="form-select" name="type" id="inv-add-payment-type" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="DP">DP (Down Payment)</option>
                                <option value="BP">BP (Balance Payment)</option>
                                <option value="CBD">CBD</option>
                                <option value="COD">COD</option>
                                <option value="Tempo">Tempo</option>
                            </select>
                        </div>
                        <div class="mb-3" id="inv-tempo-group" style="display:none">
                            <label class="form-label fw-semibold">Tempo (hari)</label>
                            <input type="number" class="form-control" name="tempo" min="1" placeholder="misal: 30">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Metode <span class="text-danger">*</span></label>
                            <select class="form-select" name="method" required>
                                <option value="">-- Pilih Metode --</option>
                                <option value="Transfer">Transfer</option>
                                <option value="Cash">Cash</option>
                                <option value="Giro">Giro</option>
                                <option value="Escrow">Escrow</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="amount" required min="1" placeholder="Masukkan jumlah yang diterima">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Persentase (%)</label>
                            <input type="number" class="form-control" name="percent" min="1" max="100" placeholder="opsional, misal: 50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Catatan</label>
                            <input type="text" class="form-control" name="note" placeholder="opsional">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Upload Bukti Payment --}}
    <div class="modal fade" id="modalUploadBukti" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Bukti Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formUploadBuktiInv" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="file" class="form-control" name="file" accept="image/*,.pdf" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Invoice Modal --}}
    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
        <div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-labelledby="editInvoiceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editInvoiceModalLabel">Edit No Invoice, PO & Term of Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('invoice.update', $invoice->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="invoiceNumber" class="form-label">No Invoice</label>
                                <input type="text" class="form-control" id="invoiceNumber" name="invoice" value="{{ old('invoice', $invoice->no_invoice) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="invoiceNoPo" class="form-label">No PO</label>
                                <input type="text" class="form-control" id="invoiceNoPo" name="no_po" maxlength="100" value="{{ old('no_po', $invoice->no_po ?? $quote->po_number) }}">
                                <div class="form-text">Perubahan No PO di sini ikut memperbarui No PO di Smart Quote &amp; invoice lain pada quote yang sama.</div>
                            </div>
                            <div class="mb-3">
                                <label for="termPayment" class="form-label">Term of Payment</label>
                                <textarea class="form-control" id="termPayment" name="payment" rows="4" required>{{ old('payment', $invoice->term ?? $quote->payment_method) }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('components.modal.invoice.date')
        @include('components.modal.invoice.due-date')
    @endif

    {{-- Modal Buat BAST (Berita Acara Serah Terima) --}}
    @if (!isset($bast) || !$bast)
        <div class="modal fade" id="modalCreateBast" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form id="formCreateBast" method="POST" action="{{ route('bast.store') }}">
                    @csrf
                    <input type="hidden" name="id_kanban_task" value="{{ $monitoringTask ? $monitoringTask->id : '' }}">
                    <input type="hidden" name="id_quotation" value="{{ $quote->id }}">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-light py-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-certificate-outline text-success fs-4"></i>
                                <h5 class="modal-title fw-bold mb-0">Buat Berita Acara Serah Terima (BAST)</h5>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            @php
                                $invoiceEntity = (($quote->client?->info ?? $invoice->flag) === 'Kojisha') ? 'Kojisha' : 'Reftech';
                                $invoiceEntityFullName = $invoiceEntity === 'Kojisha' ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima';
                            @endphp
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Tipe BAST</label>
                                    <select name="type" id="createBastType" class="form-select fw-bold text-primary" required>
                                        <option value="Default" selected>Default BAST (Standard)</option>
                                        <option value="Rental">BAST Rental (Khusus Rental)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Entitas Perusahaan <span class="badge bg-label-secondary ms-1" style="font-size: 10px;">Otomatis dari Invoice</span></label>
                                    <input type="hidden" name="entity" value="{{ $invoiceEntity }}">
                                    <input type="text" class="form-control bg-light fw-semibold text-dark" value="{{ $invoiceEntityFullName }}" readonly tabindex="-1">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" id="createBastWorkDateLabel">Tanggal Pekerjaan</label>
                                    <input type="date" name="work_date" id="createBastWorkDate" class="form-control" value="{{ \Carbon\Carbon::today()->toDateString() }}">
                                    <div class="form-text text-muted" style="font-size: 11px;">Opsional — kosongkan jika ingin ditulis manual di printout.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nomor PO / Ref</label>
                                    <input type="text" name="po_number" class="form-control" value="{{ $quote->po_number ?: $quote->no_quote }}">
                                </div>
                            </div>

                            {{-- Baris Khusus Tipe BAST Rental --}}
                            <div class="row g-3 mb-3 d-none" id="createBastRentalDatesRow">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border border-primary border-opacity-25">
                                        <label class="form-label fw-bold text-primary mb-2 d-flex align-items-center gap-1">
                                            <i class="mdi mdi-calendar-range"></i> Masa Rental :
                                        </label>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted mb-1">Tanggal Mulai</label>
                                                <input type="date" name="rental_start_date" id="createBastRentalStart" class="form-control">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small text-muted mb-1">Tanggal Berakhir</label>
                                                <input type="date" name="rental_end_date" id="createBastRentalEnd" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-text text-muted mt-2" style="font-size: 11px;">
                                            <i class="mdi mdi-information-outline me-1"></i>Masa rental bisa dikosongkan jika ingin diisi/ditulis manual pada printout.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Customer (Penerima)</label>
                                    <input type="text" name="customer_name" class="form-control" value="{{ $quote->client->company ?? '' }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nama Pekerjaan (Title)</label>
                                    <input type="text" name="work_title" class="form-control" value="{{ $quote->title ?: ($quote->no_quote ?? '') }}" required>
                                </div>
                            </div>

                            <h6 class="fw-bold mb-2 text-dark mt-4"><i class="mdi mdi-format-list-bulleted me-1 text-success"></i> Rincian Unit / Barang</h6>
                            <div class="table-responsive border rounded mb-3">
                                <table class="table table-sm align-middle m-0" id="tableBastUnits">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Unit / Barang</th>
                                            <th style="width:30%;">No. Seri (Serial Number)</th>
                                            <th style="width:15%;" class="text-center">Qty</th>
                                            <th style="width:8%;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($quote->details && $quote->details->isNotEmpty())
                                            @foreach ($quote->details as $idx => $item)
                                                @php
                                                    $unitName = '';
                                                    if ($item->fixedAsset) {
                                                        $brandModel = trim(($item->fixedAsset->merk_model ?: ($item->fixedAsset->unit ? ($item->fixedAsset->unit->brand . ' ' . $item->fixedAsset->unit->model) : '')));
                                                        $unitName = $brandModel ?: ($item->label ?: $item->description);
                                                    } elseif ($item->unit) {
                                                        $unitName = trim(($item->unit->brand ? $item->unit->brand . ' ' : '') . ($item->unit->model ?: $item->unit->name ?: ''));
                                                    }
                                                    if (!$unitName) {
                                                        $unitName = $item->label ?: $item->description;
                                                    }

                                                    $serialNo = $item->fixedAsset?->serial_number
                                                        ?: $item->unit?->sn
                                                        ?: ($item->id_unit ? \App\Models\UnitInventory::where('id_unit', $item->id_unit)->value('serial_number') : null)
                                                        ?: ($item->fixedAsset?->code ?: '');
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <input type="text" name="units[{{ $idx }}][unit_name]" class="form-control form-control-sm" value="{{ $unitName }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="units[{{ $idx }}][serial_no]" class="form-control form-control-sm" value="{{ $serialNo }}" placeholder="S/N (Opsional)">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="units[{{ $idx }}][qty]" class="form-control form-control-sm text-center" value="1" min="1">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-xs btn-outline-danger remove-bast-unit-row"><i class="mdi mdi-delete-outline"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td><input type="text" name="units[0][unit_name]" class="form-control form-control-sm" placeholder="Nama Unit" required></td>
                                                <td><input type="text" name="units[0][serial_no]" class="form-control form-control-sm" placeholder="S/N"></td>
                                                <td><input type="number" name="units[0][qty]" class="form-control form-control-sm text-center" value="1" min="1"></td>
                                                <td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger remove-bast-unit-row"><i class="mdi mdi-delete-outline"></i></button></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-xs btn-outline-success mb-3" id="btnAddBastUnitRow">
                                <i class="mdi mdi-plus me-1"></i> Tambah Baris Unit
                            </button>

                            <div class="mb-2">
                                <label class="form-label fw-semibold">Hasil Test Running / Catatan Serah Terima</label>
                                <textarea name="test_running_result" class="form-control" rows="3" placeholder="Contoh: Unit telah terpasang, dites running dengan hasil baik & berfungsi normal."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success fw-bold" id="btnSubmitCreateBast">
                                <i class="mdi mdi-check me-1"></i> Simpan BAST
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Shared BAST Modal (Used for Edit & Create) --}}
    @include('components.modal.bast.create')

@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        /* Theme's .table tbody td rule forces vertical-align:middle !important with higher
           specificity than the .align-top utility class — override it here for the item table. */
        table.items-top-align-table tbody td {
            vertical-align: top !important;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
<script>
    // Invoice language toggle (ID / EN) — swaps label text within the invoice card,
    // and carries the chosen language over to the Print/Download links via ?lang=
    function setInvoicePrintLinkLang(lang) {
        $('.invoice-print-link').each(function () {
            var url = new URL($(this).attr('href'), window.location.origin);
            url.searchParams.set('lang', lang);
            $(this).attr('href', url.toString());
        });
    }

    $(document).on('click', '.invoice-lang-btn', function () {
        var lang = $(this).data('lang');

        $('.invoice-lang-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('active btn-primary');

        $('.invoice-preview-card .i18n').each(function () {
            var $el = $(this);
            if ($el.data('idText') === undefined) {
                $el.data('idText', $el.text());
            }
            $el.text(lang === 'en' ? ($el.data('en') || $el.data('idText')) : $el.data('idText'));
        });

        setInvoicePrintLinkLang(lang);
    });

    setInvoicePrintLinkLang('id');

    $('#backButton').click(function () { window.history.back(); });

    // Buat Surat Jalan: nonaktifkan qty saat item di-uncheck
    $(document).on('change', '.item-check', function () {
        var $qty = $('#' + $(this).data('target'));
        $qty.prop('disabled', !this.checked);
    });

    // Toggle spesifikasi
    $('#toggle-spec').on('change', function () {
        var id      = $(this).data('id');
        var showing = $(this).is(':checked');

        $.post('/invoice/unit/' + id + '/toggle-spec', { _token: '{{ csrf_token() }}' });

        if (showing) {
            $('.spec-detail-rows').show();
        } else {
            $('.spec-detail-rows').hide();
        }
    });

    // PPH Manual format
    $(".invoice-item-pph-manual-label").on('keyup', function () {
        var nomorInt = parseInt($(this).val().replace(/\./g, ''), 10);
        if (!isNaN(nomorInt)) {
            $(this).val(nomorInt.toLocaleString('id-ID'));
            $("#pphManual").val(nomorInt);
        }
    });

    // Delete PPH 23
    $(document).on('click', '.delete-pph-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus PPH 23?',
            text: 'Semua nilai PPH per item akan di-reset ke 0.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/unit/' + id + '/pph/delete',
                    type: 'POST',
                    data: { '_method': 'PATCH', '_token': '{{ csrf_token() }}' },
                    success: function () { location.reload(); },
                });
            }
        });
    });

    // Delete PPH Manual
    $(document).on('click', '.delete-pph-manual-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus PPH Manual?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/unit/' + id + '/pph-manual/delete',
                    type: 'POST',
                    data: { '_method': 'PATCH', '_token': '{{ csrf_token() }}' },
                    success: function () { location.reload(); },
                });
            }
        });
    });

    // Undo confirm payment
    $(document).on('click', '.undo-payment-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            url: '/invoice/unit/' + id + '/payment/undo',
            type: 'POST',
            data: { '_method': 'PATCH', '_token': '{{ csrf_token() }}' },
            success: function () { location.reload(); },
        });
    });

    // Input Hand Sign
    $(document).on('click', '.input-hand-sign-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Input Hand Sign?',
            text: 'Tanda tangan akan otomatis ditambahkan ke invoice.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, input!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/unit/' + id + '/sign',
                    type: 'POST',
                    data: { '_token': '{{ csrf_token() }}' },
                    success: function (response) {
                        if (response == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Hand sign berhasil ditambahkan.',
                                customClass: { confirmButton: 'btn btn-success waves-effect' },
                            });
                            setTimeout(function () { location.reload(); }, 1500);
                        }
                    },
                });
            }
        });
    });

    // Add Payment — toggle Tempo field
    $('#inv-add-payment-type').on('change', function () {
        if ($(this).val() === 'Tempo') {
            $('#inv-tempo-group').show().find('input').prop('required', true);
        } else {
            $('#inv-tempo-group').hide().find('input').prop('required', false).val('');
        }
    });

    // Upload Bukti — set action URL dinamis lalu buka modal
    var $uploadBtn = null;
    $(document).on('click', '.btn-upload-proof-inv', function () {
        var id = $(this).data('id');
        $uploadBtn = $(this);
        $('#formUploadBuktiInv').data('payment-id', id).attr('action', '/smart-quote/payment/' + id + '/proof');
        $('#modalUploadBukti').modal('show');
    });

    // Intercept submit → AJAX (biar response JSON tidak tampil di browser)
    $('#formUploadBuktiInv').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        var url      = $(this).attr('action');
        var payId    = $(this).data('payment-id');
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#modalUploadBukti').modal('hide');
                $('#formUploadBuktiInv')[0].reset();
                if (res.success) {
                    var $row = $('#pay-row-' + payId);
                    $row.find('.badge.bg-label-warning').replaceWith(
                        '<a href="' + res.file_url + '" target="_blank" class="badge bg-label-success text-decoration-none" style="font-size:10px">' +
                        '<i class="mdi mdi-file-check-outline"></i> Bukti Transfer</a>'
                    );
                    if ($uploadBtn) $uploadBtn.remove();
                    $row.find('.d-flex.gap-1.ms-2').append(
                        '<button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-delete-proof" data-id="' + payId + '" title="Hapus Bukti Transfer"><i class="mdi mdi-file-remove-outline"></i></button>'
                    );
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Bukti transfer berhasil diupload.', timer: 1500, showConfirmButton: false })
                        .then(function () { window.location.reload(); });
                }
            },
            error: function () {
                $('#modalUploadBukti').modal('hide');
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal upload. Cek format dan ukuran file.' });
            }
        });
    });

    // Hapus Bukti Transfer
    $(document).on('click', '.btn-delete-proof', function () {
        var id   = $(this).data('id');
        var $btn = $(this);
        Swal.fire({
            title: 'Hapus bukti transfer?',
            text: 'File bukti transfer akan dihapus, payment tetap ada.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-2 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '/smart-quote/payment/' + id + '/proof',
                type: 'POST',
                data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res.success) {
                        var $row = $btn.closest('[id^="pay-row-"]');
                        $row.find('.badge.bg-label-success')
                            .replaceWith('<span class="badge bg-label-warning" style="font-size:10px">Belum ada bukti</span>');
                        $btn.remove();
                        $row.find('.d-flex.gap-1.ms-2').prepend(
                            '<button type="button" class="btn btn-sm btn-icon btn-outline-success btn-upload-proof-inv" data-id="' + id + '" title="Upload Bukti"><i class="mdi mdi-upload"></i></button>'
                        );
                        Swal.fire({ icon: 'success', title: 'Dihapus', text: 'Bukti transfer berhasil dihapus.', timer: 1500, showConfirmButton: false });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan.' });
                }
            });
        });
    });

    // Delete Hand Sign
    $(document).on('click', '.delete-hand-sign-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Hand Sign?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/invoice/unit/' + id + '/del-sign',
                    type: 'POST',
                    data: { '_method': 'DELETE', '_token': '{{ csrf_token() }}' },
                    success: function (response) {
                        if (response == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                customClass: { confirmButton: 'btn btn-success waves-effect' },
                            });
                            setTimeout(function () { location.reload(); }, 1500);
                        }
                    },
                });
            }
        });
    });

    // Delete Delivery / Surat Jalan
    $(document).on('click', '.delete-delivery', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Surat Jalan #' + id + '?',
            text: 'Item yang ada di Surat Jalan ini akan dikembalikan ke sisa Qty pengiriman.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/delivery/' + id,
                    type: 'POST',
                    data: { '_method': 'DELETE', '_token': '{{ csrf_token() }}' },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Surat Jalan Dihapus!',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                        });
                        setTimeout(function () {
                            window.location.hash = 'tab-delivery';
                            location.reload();
                        }, 1200);
                    },
                    error: function () {
                        Swal.fire('Oops...', 'Gagal menghapus Surat Jalan.', 'error');
                    }
                });
            }
        });
    });

    // Auto switch tab if URL hash exists (e.g. #tab-delivery)
    function activateTabFromHash() {
        if (window.location.hash) {
            var activeTab = document.querySelector(`button[data-bs-target="${window.location.hash}"]`);
            if (activeTab) {
                var tab = new bootstrap.Tab(activeTab);
                tab.show();
            }
        }
    }
    activateTabFromHash();

    // Sync URL hash when switching tabs
    $(document).on('shown.bs.tab', 'button[data-bs-toggle="tab"]', function (e) {
        var target = $(e.target).attr('data-bs-target');
        if (target) {
            history.replaceState(null, null, target);
        }
    });

    // Switch to BAST tab
    window.switchToBastTab = function() {
        var btn = document.getElementById('btn-tab-bast');
        if (btn) {
            var tab = new bootstrap.Tab(btn);
            tab.show();
        }
    };

    // BAST Type change handler (delegated)
    $(document).on('change', '#createBastType', function () {
        var type = $(this).val();
        if (type === 'Rental') {
            $('#createBastWorkDateLabel').text('Tanggal Commissioning');
            $('#createBastRentalDatesRow').removeClass('d-none');
            $('#tableBastUnits tbody input[name$="[qty]"]').val(1);
        } else {
            $('#createBastWorkDateLabel').text('Tanggal Pekerjaan');
            $('#createBastRentalDatesRow').addClass('d-none');
        }
    });

    // BAST Form Submit (delegated)
    $(document).on('submit', '#formCreateBast', function (e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#btnSubmitCreateBast');
        $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...');

        var formData = $form.serialize();
        $.ajax({
            url: '{{ route("bast.store") }}',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (res) {
                $('#modalCreateBast').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'BAST Berhasil Dibuat!',
                    text: res.message || '',
                    customClass: { confirmButton: 'btn btn-success waves-effect' },
                });
                setTimeout(function () {
                    window.location.hash = 'tab-bast';
                    location.reload();
                }, 1000);
            },
            error: function (xhr) {
                var msg = 'Gagal membuat BAST.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    if (xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).map(function(e){ return e[0]; }).join('<br>');
                    }
                }
                Swal.fire({ icon: 'error', title: 'Oops...', html: msg });
                $btn.prop('disabled', false).html('<i class="mdi mdi-check me-1"></i> Simpan BAST');
            }
        });
    });

    // Add BAST Unit Row
    $(document).on('click', '#btnAddBastUnitRow', function () {
        var rowIdx = $('#tableBastUnits tbody tr').length;
        var newRow = `<tr>
            <td><input type="text" name="units[${rowIdx}][unit_name]" class="form-control form-control-sm" placeholder="Nama Unit / Barang" required></td>
            <td><input type="text" name="units[${rowIdx}][serial_no]" class="form-control form-control-sm" placeholder="No Seri (Opsional)"></td>
            <td><input type="number" name="units[${rowIdx}][qty]" class="form-control form-control-sm text-center" value="1" min="1"></td>
            <td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger remove-bast-unit-row"><i class="mdi mdi-delete-outline"></i></button></td>
        </tr>`;
        $('#tableBastUnits tbody').append(newRow);
    });

    // Remove BAST Unit Row
    $(document).on('click', '.remove-bast-unit-row', function () {
        if ($('#tableBastUnits tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            Swal.fire('Info', 'Minimal harus ada 1 unit barang.', 'info');
        }
    });

    // Edit BAST
    $(document).on('click', '.btn-edit-bast', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        var $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            url: '{{ url("/bast") }}/' + id + '/edit-data',
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (response) {
                if (response && response.bast) {
                    var b = response.bast;
                    if (typeof window.openBastModal === 'function') {
                        window.openBastModal({
                            bastId: b.id,
                            type: b.type,
                            entity: b.entity,
                            customerName: b.customer_name,
                            workTitle: b.work_title,
                            poNumber: b.po_number,
                            workDate: b.work_date,
                            rentalStartDate: b.rental_start_date,
                            rentalEndDate: b.rental_end_date,
                            testRunningResult: b.test_running_result,
                            units: b.units,
                            idKanbanTask: '{{ $monitoringTask ? $monitoringTask->id : "" }}',
                            idQuotation: '{{ $quote->id }}'
                        });
                    }
                } else {
                    Swal.fire('Error', 'Gagal memuat data BAST.', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Gagal mengambil data BAST dari server.', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    // Handle BAST Saved event from shared modal
    $(document).on('bast:saved', function (e, response, isEdit) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: isEdit ? 'BAST berhasil diperbarui.' : 'BAST berhasil disimpan.',
            customClass: { confirmButton: 'btn btn-success waves-effect' },
        });
        setTimeout(function () {
            window.location.hash = 'tab-bast';
            location.reload();
        }, 1000);
    });

    // Input Hand Sign BAST
    $(document).on('click', '.input-hand-sign-bast', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Input Hand Sign BAST?',
            text: 'Tanda tangan resmi pelaksana akan otomatis ditambahkan ke BAST.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, input!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("/bast") }}/' + id + '/sign',
                    type: 'POST',
                    data: { '_token': '{{ csrf_token() }}' },
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Hand sign berhasil ditambahkan ke BAST.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                        });
                        setTimeout(function () {
                            window.location.hash = 'tab-bast';
                            location.reload();
                        }, 1000);
                    },
                    error: function () {
                        Swal.fire('Error', 'Gagal menambahkan Hand Sign.', 'error');
                    }
                });
            }
        });
    });

    // Delete Hand Sign BAST
    $(document).on('click', '.delete-hand-sign-bast', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Hand Sign BAST?',
            text: 'Tanda tangan pada BAST ini akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("/bast") }}/' + id + '/del-sign',
                    type: 'POST',
                    data: {
                        '_method': 'DELETE',
                        '_token': '{{ csrf_token() }}'
                    },
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: 'Hand sign BAST berhasil dihapus.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                        });
                        setTimeout(function () {
                            window.location.hash = 'tab-bast';
                            location.reload();
                        }, 1000);
                    },
                    error: function () {
                        Swal.fire('Error', 'Gagal menghapus Hand Sign.', 'error');
                    }
                });
            }
        });
    });

    // Copy BAST Sign URL
    $(document).on('click', '#btn-copy-bast-sign-url, #btn-copy-bast-link-action', function () {
        var urlInput = document.getElementById('bast-sign-url');
        if (urlInput) {
            urlInput.select();
            urlInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(urlInput.value).then(function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Berhasil Disalin!',
                    text: 'Tautan tanda tangan BAST telah disalin ke clipboard.',
                    timer: 1800,
                    showConfirmButton: false
                });
            }).catch(function () {
                document.execCommand('copy');
                Swal.fire({
                    icon: 'success',
                    title: 'Link Berhasil Disalin!',
                    text: 'Tautan tanda tangan BAST telah disalin ke clipboard.',
                    timer: 1800,
                    showConfirmButton: false
                });
            });
        }
    });

    // Copy Delivery Order Sign URL
    $(document).on('click', '.btn-copy-del-url', function () {
        var inputId = $(this).data('input-id');
        var urlInput = document.getElementById(inputId);
        if (urlInput) {
            urlInput.select();
            urlInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(urlInput.value).then(function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Berhasil Disalin!',
                    text: 'Tautan tanda tangan Surat Jalan telah disalin ke clipboard.',
                    timer: 1800,
                    showConfirmButton: false
                });
            }).catch(function () {
                document.execCommand('copy');
                Swal.fire({
                    icon: 'success',
                    title: 'Link Berhasil Disalin!',
                    text: 'Tautan tanda tangan Surat Jalan telah disalin ke clipboard.',
                    timer: 1800,
                    showConfirmButton: false
                });
            });
        }
    });

    // Delete BAST
    $(document).on('click', '.delete-bast', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus BAST ini?',
            text: 'Data Berita Acara Serah Terima akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/bast/' + id,
                    type: 'POST',
                    data: { '_method': 'DELETE', '_token': '{{ csrf_token() }}' },
                    success: function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'BAST Dihapus!',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                        });
                        setTimeout(function () {
                            window.location.hash = 'tab-invoice';
                            location.reload();
                        }, 1200);
                    },
                    error: function () {
                        Swal.fire('Oops...', 'Gagal menghapus BAST.', 'error');
                    }
                });
            }
        });
    });

    // Realtime live calculation for Confirm Payment Unit modal
    (function () {
        var baseGross = {{ (float) ($targetPayAmount ?? 0) }};

        function parseNominal(val) {
            if (!val) return 0;
            var num = val.toString().replace(/[^0-9]/g, '');
            return num ? parseInt(num, 10) : 0;
        }

        function formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }

        function updateConfirmCalculations() {
            var pphVal = parseNominal($('#confirm_pph_label').val());
            var costVal = parseNominal($('#confirm_cost_label').val());

            $('#confirm_pph').val(pphVal);
            $('#confirm_cost').val(costVal);

            if (pphVal > 0) {
                $('#rowPreviewPph').attr('style', 'display: flex !important;');
                $('#dispPreviewPph').text('- Rp ' + formatRupiah(pphVal));
            } else {
                $('#rowPreviewPph').attr('style', 'display: none !important;');
            }

            if (costVal > 0) {
                $('#rowPreviewCost').attr('style', 'display: flex !important;');
                $('#dispPreviewCost').text('- Rp ' + formatRupiah(costVal));
            } else {
                $('#rowPreviewCost').attr('style', 'display: none !important;');
            }

            var nett = baseGross - pphVal - costVal;
            $('#dispPreviewNett').text('Rp ' + formatRupiah(nett));
        }

        $(document).on('input', '.format-rupiah-confirm', function () {
            var val = parseNominal($(this).val());
            $(this).val(val > 0 ? formatRupiah(val) : '');
            updateConfirmCalculations();
        });

        $('#confirmPayment').on('shown.bs.modal', function () {
            updateConfirmCalculations();
        });
    })();
</script>

@endpush
