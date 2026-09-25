@extends('layouts.sales.app')
@section('title', 'Detail Sales Order - ' . ($pending->no_pending ?? 'SO'))

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .asset-kpi-card {
            border-radius: 10px;
            border: 1px solid rgba(67, 89, 113, 0.12);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.05);
            transition: all 0.2s ease-in-out;
            background: #ffffff;
        }
        .asset-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(67, 89, 113, 0.1);
        }
        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .detail-card {
            border-radius: 10px;
            border: 1px solid rgba(67, 89, 113, 0.12);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.05);
            background: #ffffff;
        }
        .detail-label {
            font-size: 0.72rem;
            color: #8592a3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .detail-value {
            font-size: 0.9rem;
            color: #384551;
            font-weight: 600;
        }
        .info-spec-box {
            border-radius: 8px;
            background: #fbfcfd;
            border: 1px solid #e7ebee;
            padding: 14px;
        }
        .table-custom thead th {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #697a8d;
            padding: 0.75rem 1rem;
            white-space: nowrap;
        }
        .table-custom tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    @php
        $isInvoiceApproved = $invoices->contains(fn ($inv) => $inv->no_invoice !== null);

        switch ($pending->delivery) {
            case 1: $kurir = 'JNE / J&T / Cargo'; break;
            case 2: $kurir = 'Send By Technician'; break;
            case 3: $kurir = 'Taken Directly'; break;
            case 4: $kurir = 'Other'; break;
            default: $kurir = 'Belum Ada Kurir'; break;
        }

        switch ($pending->status) {
            case 1: $statusName = 'On Check'; $statusBadge = 'bg-warning text-dark'; break;
            case 2: $statusName = 'Ready Stock'; $statusBadge = 'bg-info text-white'; break;
            case 3: $statusName = 'Kurang'; $statusBadge = 'bg-danger text-white'; break;
            case 4: $statusName = 'Pre-Order'; $statusBadge = 'bg-primary text-white'; break;
            case 5: $statusName = 'Delivery Process'; $statusBadge = 'bg-linkedin text-white'; break;
            case 6: $statusName = 'Done'; $statusBadge = 'bg-success text-white'; break;
            case 7: $statusName = 'Cancel'; $statusBadge = 'bg-danger text-white'; break;
            default: $statusName = 'New PO'; $statusBadge = 'bg-secondary text-white'; break;
        }

        $chargeLabel = function ($val) {
            if ($val == 1) return ['Company', 'bg-label-primary'];
            if ($val == 2) return ['Customer', 'bg-label-success'];
            return [null, null];
        };
        $docChargeVal = $pending->combine_shipping_and_parts ? $pending->charged : $pending->doc_charged;
        $shippingChargeVal = $pending->combine_shipping_and_parts ? $pending->charged : $pending->shipping_charged;
        [$docChargeText, $docChargeClass] = $chargeLabel($docChargeVal);
        [$shippingChargeText, $shippingChargeClass] = $chargeLabel($shippingChargeVal);

        $isPaymentConfirmed = $invoices->contains(fn ($i) => $i->status_p == 1);
        $isTempoPayment = stripos($quote->payment_method ?? '', 'Tempo') !== false;
    @endphp

    @if (!$isInvoiceApproved)
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2 fs-4"></i>
            <div>
                <strong>Perhatian:</strong> Invoice untuk PO ini belum di-approve oleh Accounting. Proses logistik (Update Status, Resi, &amp; Done) dikunci sementara hingga invoice disetujui.
            </div>
        </div>
    @endif

    {{-- Top Header Banner (Fixed/161 Style) --}}
    <div class="card mb-4 text-white border-0 position-relative shadow-sm" style="background: linear-gradient(135deg, #696cff 0%, #3f42b3 100%) !important;">
        <div class="position-absolute overflow-hidden" style="inset: 0; border-radius: inherit; z-index: 1;">
            <!-- Subtle background circle decorations -->
            <div class="position-absolute translate-middle" style="top: 0; right: 0; width: 250px; height: 250px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>
            <div class="position-absolute translate-middle" style="bottom: -50px; left: -50px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
        </div>
        <div class="card-body p-4 position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-white text-primary fw-bold text-uppercase px-3 py-1.5 fs-7" style="border-radius: 5px;">Sales Order</span>
                        <span class="badge {{ $statusBadge }} fw-bold"><i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> {{ $statusName }}</span>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">{{ $quote->client->company ?? '-' }}</h3>
                    <p class="mb-0 opacity-80 small">
                        <i class="mdi mdi-tag-outline me-1"></i> No SO: <span class="fw-semibold text-white">{{ $pending->no_pending }}</span>
                        <span class="mx-2">|</span>
                        <i class="mdi mdi-calendar-blank-outline me-1"></i> Tanggal: <span class="fw-semibold text-white">{{ \Carbon\Carbon::parse($pending->date)->format('d M Y') }}</span>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex flex-column flex-md-row justify-content-md-end gap-2 align-items-md-center">
                    <a href="{{ route('pending-po.sales-order') }}" class="btn btn-outline-light waves-effect waves-light text-white">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                    @if ($pending->status != '6' && $pending->status != '8' && $pending->status != '9')
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-light dropdown-toggle waves-effect waves-light text-white"
                                data-bs-toggle="dropdown" aria-expanded="false" {{ $isInvoiceApproved ? '' : 'disabled' }}>
                                <i class="mdi mdi-square-edit-outline me-1"></i> Update Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#deliveryEditUnit"><i class="mdi mdi-truck-delivery-outline me-2 text-primary"></i>Kurir</a></li>
                                <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#statusEditUnit"><i class="mdi mdi-list-status me-2 text-warning"></i>Pending PO</a></li>
                                <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#resiEditUnit"><i class="mdi mdi-barcode-scan me-2 text-success"></i>Upload Resi</a></li>
                            </ul>
                        </div>
                    @elseif ($pending->status == '6')
                        @if ($pending->id_product_out == null)
                            <button type="button" class="btn btn-danger waves-effect waves-light border-0" data-bs-toggle="modal" data-bs-target="#inputProductOutUnit"
                                {{ $isInvoiceApproved ? '' : 'disabled' }}>
                                <i class="mdi mdi-connection me-1"></i> Connect Product Out
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-light waves-effect waves-light text-white" data-bs-toggle="modal" data-bs-target="#productReturnUnit">
                                <i class="mdi mdi-arrow-u-left-bottom me-1"></i> Retur Barang
                            </button>
                        @endif
                    @elseif ($pending->status == '9')
                        <button type="button" class="btn btn-success done-po-unit waves-effect waves-light border-0 text-white" data-id="{{ $pending->id }}"
                            {{ $isInvoiceApproved ? '' : 'disabled' }}>
                            <i class="mdi mdi-check-decagram-outline me-1"></i> Done
                        </button>
                        <button type="button" class="btn btn-outline-light waves-effect waves-light text-white" data-bs-toggle="modal" data-bs-target="#inputProductOutUnit"
                            {{ $isInvoiceApproved ? '' : 'disabled' }}>
                            <i class="mdi mdi-connection me-1"></i> Connect Product Out
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KPI Summary Cards (Fixed/161 Style) -->
    <div class="row g-3 mb-4">
        <!-- Total Nilai SO -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon bg-label-primary me-3">
                        <i class="mdi mdi-cash-multiple fs-4"></i>
                    </div>
                    <div>
                        <div class="detail-label">Nilai Pesanan (PO)</div>
                        <h6 class="mb-0 fw-bold text-primary">Rp {{ number_format($quote->grand_total ?? ($quote->total ?? 0), 0, ',', '.') }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- No PO Customer -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon bg-label-warning me-3">
                        <i class="mdi mdi-file-document-outline fs-4"></i>
                    </div>
                    <div>
                        <div class="detail-label">No. PO Customer</div>
                        <h6 class="mb-0 fw-bold text-dark">{{ $pending->no_po_customer ?: ($quote->no_po_customer ?: '-') }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kurir & Pengiriman -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon bg-label-info me-3">
                        <i class="mdi mdi-truck-delivery-outline fs-4"></i>
                    </div>
                    <div>
                        <div class="detail-label">Ekspedisi & Kurir</div>
                        <h6 class="mb-0 fw-bold text-dark">{{ $kurir }}</h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Invoice & Pembayaran -->
        <div class="col-sm-6 col-xl-3">
            <div class="card asset-kpi-card h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="stat-icon {{ $isInvoiceApproved ? 'bg-label-success' : 'bg-label-danger' }} me-3">
                        <i class="mdi {{ $isInvoiceApproved ? 'mdi-check-decagram-outline' : 'mdi-alert-circle-outline' }} fs-4"></i>
                    </div>
                    <div>
                        <div class="detail-label">Invoice & Payment</div>
                        <h6 class="mb-0 fw-bold {{ $isInvoiceApproved ? 'text-success' : 'text-danger' }}">
                            {{ $isInvoiceApproved ? ($isPaymentConfirmed ? 'Paid / Confirmed' : ($isTempoPayment ? 'Invoice OK (Tempo)' : 'Invoice Approved')) : 'Belum Approve' }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Card: Informasi SO, Dokumen & Alamat Pengiriman -->
        <div class="card detail-card mb-3">
            <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center bg-transparent">
                <h5 class="card-title mb-0 fw-bold fs-6 d-flex align-items-center">
                    <i class="mdi mdi-card-bulleted-outline text-primary me-2"></i>Informasi SO, Dokumen &amp; Pengiriman
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm waves-effect" data-bs-toggle="modal" data-bs-target="#editAddressesUnit">
                        <i class="mdi mdi-pencil-outline me-1"></i> Edit Alamat
                    </button>
                </div>
            </div>
            <div class="card-body p-3 p-md-4">
                <div class="row g-3">
                    <!-- Col 1: Alamat Dokumen & Pengiriman -->
                    <div class="col-lg-4 col-12">
                        <div class="info-spec-box h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <h6 class="fw-bold text-dark mb-0 fs-7">
                                        <i class="mdi mdi-map-marker-outline text-primary me-1"></i>Alamat &amp; Penerima
                                    </h6>
                                    <span class="badge {{ $pending->combine_shipping_and_parts ? 'bg-label-success' : 'bg-label-danger' }}" style="font-size: 9px;">
                                        {{ $pending->combine_shipping_and_parts ? 'Digabung' : 'Dipisah' }}
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="detail-label">Alamat Dokumen / Invoice</div>
                                    <div class="detail-value fs-7">
                                        @if (($pending->doc_address_type ?? 'customer') === 'customer')
                                            <span class="badge bg-label-secondary mb-1" style="font-size: 9px; padding: 2px 4px;">Sesuai Customer</span>
                                            <div class="text-dark">{{ $quote->client->address ?? '-' }}</div>
                                        @else
                                            <span class="badge bg-label-warning mb-1" style="font-size: 9px; padding: 2px 4px;">Manual</span>
                                            <div class="text-dark">{{ $pending->doc_address_manual }}</div>
                                        @endif
                                        @if ($docChargeText)
                                            <span class="badge {{ $docChargeClass }} mt-1" style="font-size: 9px; padding: 2px 4px;">Charged: {{ $docChargeText }}</span>
                                        @endif
                                        @if ($pending->doc_recipient)
                                            <div class="small text-muted mt-1">Attn: <strong>{{ $pending->doc_recipient->name_pic }}</strong> ({{ $pending->doc_recipient->phone ?? '-' }})</div>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <div class="detail-label">Alamat Pengiriman Barang</div>
                                    <div class="detail-value fs-7">
                                        @if (($pending->shipping_address_type ?? 'customer') === 'customer')
                                            <span class="badge bg-label-secondary mb-1" style="font-size: 9px; padding: 2px 4px;">Sesuai Customer</span>
                                            <div class="text-dark">{{ $quote->client->address ?? '-' }}</div>
                                        @else
                                            <span class="badge bg-label-warning mb-1" style="font-size: 9px; padding: 2px 4px;">Manual</span>
                                            <div class="text-dark">{{ $pending->shipping_address_manual }}</div>
                                        @endif
                                        @if ($shippingChargeText)
                                            <span class="badge {{ $shippingChargeClass }} mt-1" style="font-size: 9px; padding: 2px 4px;">Charged: {{ $shippingChargeText }}</span>
                                        @endif
                                        @if ($pending->shipping_recipient)
                                            <div class="small text-muted mt-1">Attn: <strong>{{ $pending->shipping_recipient->name_pic }}</strong> ({{ $pending->shipping_recipient->phone ?? '-' }})</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Col 2: Informasi Dokumen & Keuangan -->
                    <div class="col-lg-4 col-12">
                        <div class="info-spec-box h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <h6 class="fw-bold text-dark mb-0 fs-7">
                                        <i class="mdi mdi-file-document-outline text-primary me-1"></i>Dokumen &amp; Invoice
                                    </h6>
                                    @if ($isPaymentConfirmed)
                                        <span class="badge bg-label-success" style="font-size: 9px;">Paid</span>
                                    @else
                                        <span class="badge bg-label-danger" style="font-size: 9px;">Unpaid</span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <div class="detail-label">No. PO Customer</div>
                                    <div class="detail-value text-dark font-monospace">{{ $quote->po_number ?: '-' }}</div>
                                </div>

                                <div class="mb-3">
                                    <div class="detail-label">Daftar Invoice</div>
                                    <div class="detail-value">
                                        @forelse ($invoices as $inv)
                                            <div class="mb-1">
                                                @if ($inv->no_invoice)
                                                    <a class="fw-bold text-primary font-monospace" href="{{ route('invoice.show', $inv->id) }}">
                                                        <i class="mdi mdi-receipt me-1"></i>{{ $inv->no_invoice }}
                                                    </a>
                                                @else
                                                    <span class="badge bg-label-warning" style="font-size: 9px;">{{ $inv->type }} - Pending Approval</span>
                                                @endif
                                            </div>
                                        @empty
                                            <span class="text-muted small">Belum ada invoice</span>
                                        @endforelse
                                    </div>
                                </div>

                                <div>
                                    <div class="detail-label">Metode Pembayaran</div>
                                    <div class="detail-value text-dark">{{ $quote->payment_method ?? 'Cash / Transfer' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Col 3: Informasi Pengiriman & Resi -->
                    <div class="col-lg-4 col-12">
                        <div class="info-spec-box h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                                    <h6 class="fw-bold text-dark mb-0 fs-7">
                                        <i class="mdi mdi-truck-delivery-outline text-primary me-1"></i>Kurir &amp; Resi
                                    </h6>
                                    <span class="badge bg-label-info" style="font-size: 9px;">{{ $kurir }}</span>
                                </div>

                                <div class="detail-label mb-1">Daftar Resi &amp; Biaya Kirim</div>
                                @forelse ($resis as $r)
                                    <div class="p-2 mb-2 rounded bg-white border">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="fw-bold text-dark font-monospace fs-7">{{ $r->no_track ?? '-' }}</span>
                                                @if ($r->note)
                                                    <div class="text-muted small mt-0.5">{{ $r->note }}</div>
                                                @endif
                                            </div>
                                            @if ($r->image)
                                                <a href="#" onclick="openPdfViewer('{{ url($r->image) }}', 'Resi {{ $r->no_track }}'); return false;" class="btn btn-xs btn-outline-primary" style="font-size: 10px; padding: 2px 6px;">
                                                    <i class="mdi mdi-file-image"></i> Lihat
                                                </a>
                                            @endif
                                        </div>
                                        <div class="mt-1 pt-1 border-top d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Ongkir:</small>
                                            <span class="fw-bold text-primary fs-7">Rp {{ number_format($r->cost ?? 0, 0, '.', ',') }}</span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-3 text-muted small">
                                        <i class="mdi mdi-barcode-scan d-block fs-4 text-muted mb-1"></i>
                                        Belum ada data resi pengiriman.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Daftar Barang & Alokasi Stok -->
        <div class="card detail-card mb-3">
            <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center flex-wrap gap-2 bg-transparent">
                <h5 class="card-title mb-0 fw-bold fs-6 d-flex align-items-center">
                    <i class="mdi mdi-package-variant-closed text-primary me-2"></i>Daftar Barang &amp; Alokasi Stok
                </h5>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if ($pending->status != '6' && $pending->status != '7')
                        <button type="button" class="btn btn-warning btn-sm text-dark fw-bold waves-effect shadow-xs d-flex align-items-center gap-1"
                            data-bs-toggle="modal" data-bs-target="#modalCreateProductOut">
                            <i class="mdi mdi-truck-fast-outline me-1"></i> Barang Keluar
                        </button>
                    @endif
                    @if ($pending->status != '6')
                        <button type="button" class="btn btn-outline-warning btn-sm waves-effect" data-bs-toggle="modal"
                            data-bs-target="#replacementEditUnit" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                            <i class="mdi mdi-list-status me-1"></i> Update Status &amp; Gudang
                        </button>
                    @endif
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-custom align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Item Barang</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Alokasi Gudang</th>
                            <th class="text-center">Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $orderedQtyByEquivalent = $quote->details
                                ->whereNotNull('id_equivalent')
                                ->where('id_equivalent', '!=', 0)
                                ->groupBy('id_equivalent')
                                ->map(fn ($rows) => $rows->sum('qty'));
                        @endphp
                        @forelse ($dPending as $item)
                            @php
                                switch ($item->status) {
                                    case 1: $status = 'On Check'; $badge = 'bg-label-warning'; break;
                                    case 2: $status = 'Ready Stock'; $badge = 'bg-label-info'; break;
                                    case 3: $status = 'Kurang'; $badge = 'bg-label-danger'; break;
                                    case 4: $status = 'Pre-Order'; $badge = 'bg-label-primary'; break;
                                    case 5: $status = 'Delivery Process'; $badge = 'bg-label-linkedin'; break;
                                    case 6: $status = 'Done'; $badge = 'bg-label-success'; break;
                                    default: $status = 'Belum Di Cek'; $badge = 'bg-label-secondary'; break;
                                }
                                $goLabels = [
                                    'Replacement' => 'bg-label-warning',
                                    'Genuine'     => 'bg-label-success',
                                    'OEM'         => 'bg-label-info',
                                ];
                                $goVal = $item->equivalent->product->go ?? null;
                                $orderedQty = $item->id_equivalent ? ($orderedQtyByEquivalent[$item->id_equivalent] ?? null) : null;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <td class="fw-semibold">
                                    @if (empty($item->id_equivalent) || $item->id_equivalent == '0')
                                        {{ $item->note ?: '-' }}
                                    @else
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <span class="text-dark">{{ $item->equivalent->brand ?? '' }} {{ $item->equivalent->pn ?? '' }}</span>
                                            @if ($goVal && isset($goLabels[$goVal]))
                                                <span class="badge {{ $goLabels[$goVal] }}" style="font-size: 9px;">{{ $goVal }}</span>
                                            @endif
                                        </div>
                                        @if ($item->equivalent->product->description ?? null)
                                            <div class="text-muted fw-normal text-wrap" style="font-size: 11.5px; line-height: 1.4; max-width: 320px;">{{ $item->equivalent->product->description }}</div>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center fw-bold">{{ $orderedQty !== null ? (float) $orderedQty : ($item->bdg + $item->bks) }}</td>
                                <td class="text-center">
                                    @if ($item->bdg > 0)
                                        <span class="badge bg-label-primary" style="font-size: 10px;">BDG: {{ $item->bdg }}</span>
                                    @endif
                                    @if ($item->bks > 0)
                                        <span class="badge bg-label-info" style="font-size: 10px;">BKS: {{ $item->bks }}</span>
                                    @endif
                                    @if (!$item->bdg && !$item->bks)
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $pending->status == '6' ? 'bg-label-success' : $badge }}">
                                        {{ $pending->status == '6' ? 'Done' : $status }}
                                    </span>
                                </td>
                                <td class="text-wrap" style="max-width: 250px;">{{ $item->note ?? '-' }}</td>
                            </tr>
                            @php $no++; @endphp
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak ada data barang</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Purchase Requests -->
        @if ((isset($purchases) ? $purchases->isEmpty() : !$purchase) && $dPending->where('status', 3)->count() > 0)
            <div class="alert alert-warning d-flex align-items-center mb-3">
                <i class="mdi mdi-clock-alert-outline me-2 fs-5"></i>
                Ada item yang stoknya kurang, tapi Purchase Request belum dibuat — menunggu konfirmasi payment DP dari Accounting.
            </div>
        @endif
        <div class="card detail-card mb-3">
            <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center flex-wrap gap-2 bg-transparent">
                <h5 class="card-title mb-0 fw-bold fs-6 d-flex align-items-center">
                    <i class="mdi mdi-cart-arrow-down text-primary me-2"></i>Purchase Request Terkait
                </h5>
                @if ($pending->status != '6' && $pending->status != '8')
                    <button type="button" class="btn btn-outline-primary btn-sm waves-effect" data-bs-toggle="modal"
                        data-bs-target="#purchaseReqUnit" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                        <i class="mdi mdi-plus-box me-1"></i> Purchase Request
                    </button>
                @endif
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-custom align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>No PR</th>
                            <th>Item Barang</th>
                            <th class="text-center">Qty</th>
                            <th>Catatan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $allPrDetails = collect();
                            $prList = (isset($purchases) && $purchases->isNotEmpty()) ? $purchases : ($purchase ? collect([$purchase]) : collect());
                            foreach ($prList as $prDoc) {
                                if (($prDoc->status ?? null) == '1' && ($prDoc->purchaseOrders->count() ?? 0) > 0) {
                                    $status_pr = 'Menunggu Pengiriman Supplier';
                                    $color_pr = 'bg-label-dark';
                                } else {
                                    switch ($prDoc->status ?? null) {
                                        case '1': $status_pr = 'Approved'; $color_pr = 'bg-label-warning'; break;
                                        case '2': $status_pr = 'Delivery'; $color_pr = 'bg-label-info'; break;
                                        case '3': $status_pr = 'Good Receipt'; $color_pr = 'bg-label-success'; break;
                                        default: $status_pr = 'New Purchase'; $color_pr = 'bg-label-primary'; break;
                                    }
                                }
                                foreach (($prDoc->details ?? collect()) as $det) {
                                    $allPrDetails->push([
                                        'pr' => $prDoc,
                                        'detail' => $det,
                                        'status_pr' => $status_pr,
                                        'color_pr' => $color_pr,
                                    ]);
                                }
                            }
                        @endphp
                        @forelse ($allPrDetails as $row)
                            @php
                                $prDoc = $row['pr'];
                                $det = $row['detail'];
                            @endphp
                            <tr>
                                <td class="text-center">{{ $no }}</td>
                                <td class="fw-bold"><a href="{{ route('purchase-request.show', $prDoc->id) }}" class="text-primary font-monospace">{{ $prDoc->no_pr ?? '-' }}</a></td>
                                <td>
                                    @if ($det->id_equivalent == '0' || empty($det->id_equivalent))
                                        -
                                    @else
                                        {{ $det->equivalent->brand ?? '' }} {{ $det->equivalent->pn ?? '' }}
                                    @endif
                                </td>
                                <td class="text-center fw-bold">{{ $det->qty }} {{ $det->equivalent->product->unit ?? '' }}</td>
                                <td class="text-wrap" style="max-width: 250px;">{{ $det->note ?? '-' }}</td>
                                <td class="text-center"><span class="badge {{ $row['color_pr'] }}">{{ $row['status_pr'] }}</span></td>
                            </tr>
                            @php $no++; @endphp
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Tidak Ada Purchase Request</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Returns Section -->
        @if ($return->isNotEmpty())
            <div class="card detail-card mb-3">
                <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center bg-transparent">
                    <h5 class="card-title mb-0 fw-bold fs-6 d-flex align-items-center">
                        <i class="mdi mdi-arrow-u-left-bottom text-primary me-2"></i>Retur Barang
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-danger clear-return-unit waves-effect" data-id="{{ $pending->id }}">
                        <i class="mdi mdi-eraser-variant me-1"></i> Clear Return
                    </a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-custom align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>No Return</th>
                                <th>No DO</th>
                                <th>Tanggal Return</th>
                                <th>Tanggal Selesai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($return as $retur)
                                <tr>
                                    <td class="text-center">{{ $no }}</td>
                                    <td>
                                        <a href="{{ route('return.show', $retur->id) }}" class="fw-bold text-primary font-monospace">
                                            {{ $retur->no_return }}
                                        </a>
                                    </td>
                                    <td>{{ $retur->product_in->no_do ?? 'Belum Ada Product In' }}</td>
                                    <td>{{ $retur->date }}</td>
                                    <td>{{ $retur->date_done ?? '-' }}</td>
                                </tr>
                                @php $no++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Finished Product Out Invoice -->
        @if ($pending->status == '6' && $pending->id_product_out != null && $product)
            <div class="card detail-card mb-3">
                <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center flex-wrap gap-2 bg-transparent">
                    <h5 class="card-title mb-0 fw-bold fs-6 d-flex align-items-center">
                        <i class="mdi mdi-file-document-check-outline text-success me-2"></i>Surat Jalan Barang Keluar ({{ $product->vers }})
                    </h5>
                    <span class="badge bg-label-success fs-7">#{{ $product->no_type == '1' ? $product->invoice : $product->po }}</span>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="detail-label">Customers / Alamat Pengiriman:</div>
                            <pre class="mb-0 text-dark fw-medium p-2 bg-light rounded border text-wrap" style="font-family: inherit; font-size: 13px;">{{ $product->detail_client }}</pre>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Tanggal Keluar:</div>
                            <p class="mb-0 fw-medium text-dark"><i class="mdi mdi-calendar-range me-1"></i> {{ Carbon\Carbon::parse($product->date)->format('d-m-Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Dibuat Oleh:</div>
                            <p class="mb-0 fw-medium text-dark"><i class="mdi mdi-account-circle-outline me-1"></i> {{ $product->user->name }}</p>
                        </div>
                        @if($product->note)
                            <div class="col-12">
                                <div class="detail-label">Catatan Tambahan:</div>
                                <pre class="mb-0 text-muted p-2 bg-light rounded border text-wrap" style="font-family: inherit; font-size: 13px;">{{ $product->note }}</pre>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive border rounded">
                        <table class="table table-hover table-custom align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Item Keluar</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($detProduct as $products)
                                    <tr>
                                        <td class="text-center">{{ $no }}</td>
                                        <td>
                                            <p class="mb-0 fw-semibold text-primary">{{ $products->detailProduct->replacement }}</p>
                                            <small class="text-muted">{{ $products->detailProduct->product->description }}</small>
                                        </td>
                                        <td class="text-center">{{ $products->qty }} {{ $products->detailProduct->product->unit }}</td>
                                        <td class="text-end">Rp {{ number_format($products->price, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($products->amount, 0, ',', '.') }}</td>
                                    </tr>
                                    @php $no++; @endphp
                                @endforeach
                                <tr class="table-light">
                                    <td colspan="3" class="border-0"></td>
                                    <td class="fw-semibold text-end">Shipping Cost:</td>
                                    <td class="fw-bold text-end">Rp {{ number_format($product->shipping, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="table-light">
                                    <td colspan="3" class="border-0"></td>
                                    <td class="fw-semibold border-top text-primary text-end">Grand Total:</td>
                                    <td class="fw-bold border-top text-primary text-end">Rp {{ number_format($product->total, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Activity Timeline -->
        @if ($activity->count() >= 1)
            <div class="card detail-card mb-3">
                <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center bg-transparent">
                    <h5 class="card-title mb-0 fw-bold fs-6 d-flex align-items-center">
                        <i class="mdi mdi-clock-outline text-primary me-2"></i>Riwayat Aktivitas &amp; Log Perubahan
                    </h5>
                    <span class="badge bg-label-secondary">{{ $activity->count() }} Aktivitas</span>
                </div>
                <div class="card-body p-3 p-md-4">
                    <ul class="timeline card-timeline mb-0">
                        @foreach ($activity as $stats)
                            @php
                                switch ($stats->status) {
                                    case 1: $color = 'warning'; $st = 'On Check'; break;
                                    case 2: $color = 'info'; $st = 'Ready Stock'; break;
                                    case 3: $color = 'danger'; $st = 'Kurang'; break;
                                    case 4: $color = 'primary'; $st = 'Pre-Order'; break;
                                    case 5: $color = 'linkedin'; $st = 'Delivery Process'; break;
                                    case 6: $color = 'success'; $st = 'Done'; break;
                                    case 8: $color = 'danger'; $st = 'Return'; break;
                                    case 9: $color = 'warning'; $st = 'Delayed'; break;
                                    default: $color = 'secondary'; $st = 'In Progress'; break;
                                }
                            @endphp
                            <li class="timeline-item timeline-item-transparent clearfix">
                                <span class="timeline-point timeline-point-{{ $color }}"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0 fw-bold text-dark">Status: <span class="badge bg-label-{{ $color }} btn-xs">{{ $st }}</span></h6>
                                        <small class="text-muted">{{ $stats->created_at->diffForHumans() }} ({{ $stats->created_at->format('d M Y H:i') }})</small>
                                    </div>
                                    <p class="mb-2 small text-muted">Diperbarui oleh: <span class="fw-semibold text-dark">{{ $stats->user->name ?? 'System' }}</span></p>

                                    <div class="ms-3 border-start ps-3 py-1">
                                        @foreach ($stats->comment as $com)
                                            <div class="mb-2 p-2 rounded bg-light hover-light border border-light position-relative">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                    <span class="fw-bold text-dark" style="font-size: 12px;">{{ $com->user->name }}</span>
                                                    <small class="text-muted" style="font-size: 10px;">{{ $com->date }}</small>
                                                </div>
                                                <p class="mb-0 text-muted mt-1" style="font-size: 12px; line-height: 1.4;">{{ $com->comment }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    {{-- ==================== MODALS ==================== --}}

    <!-- Modal Update Status (Pending PO) -->
    <form action="{{ route('pending-po.statusEdit', $pending->id) }}" method="post">
        @method('PATCH')
        @csrf
        <div class="modal-onboarding modal fade animate__animated" id="statusEditUnit" tabindex="-1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="onboarding-content mb-0">
                            <h4 class="onboarding-title text-body">{{ $quote->client->company ?? '-' }}</h4>
                            <div class="form-floating form-floating-outline mb-3">
                                <select class="form-select" name="status">
                                    <option value="1" {{ $pending->status == '1' ? 'selected' : '' }}>On Check</option>
                                    <option value="2" {{ $pending->status == '2' ? 'selected' : '' }}>Ready Stock</option>
                                    <option value="3" {{ $pending->status == '3' ? 'selected' : '' }}>Kurang</option>
                                    <option value="4" {{ $pending->status == '4' ? 'selected' : '' }}>Pre-Order</option>
                                    <option value="5" {{ $pending->status == '5' ? 'selected' : '' }}>Delivery Process</option>
                                    <option value="6" {{ $pending->status == '6' ? 'selected' : '' }}>Done</option>
                                    <option value="7" {{ $pending->status == '7' ? 'selected' : '' }}>Cancel</option>
                                </select>
                                <label>Status</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Kurir -->
    <form action="{{ route('pending-po.deliveryEdit', $pending->id) }}" method="post">
        @method('PATCH')
        @csrf
        <div class="modal-onboarding modal fade animate__animated" id="deliveryEditUnit" tabindex="-1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="onboarding-content mb-0">
                            <h4 class="onboarding-title text-body">{{ $quote->client->company ?? '-' }}</h4>
                            <div class="form-floating form-floating-outline mb-3">
                                <select class="form-select" name="delivery">
                                    <option value="1" {{ $pending->delivery == '1' ? 'selected' : '' }}>JNE / J&T / Cargo</option>
                                    <option value="2" {{ $pending->delivery == '2' ? 'selected' : '' }}>Send By Technician</option>
                                    <option value="3" {{ $pending->delivery == '3' ? 'selected' : '' }}>Taken Directly</option>
                                    <option value="4" {{ $pending->delivery == '4' ? 'selected' : '' }}>Other</option>
                                </select>
                                <label>Delivery</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Upload Resi (reuse shared markup, generic) -->
    @include('components.modal.pending.resi')
    @php
        // components.modal.pending.resi hard-codes id="resiEdit"; alias a second trigger id for this page
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var resiModalEl = document.getElementById('resiEdit');
            if (resiModalEl) resiModalEl.id = 'resiEditUnit';
        });
    </script>

    <!-- Modal Update Status Barang (per item, DetailPendingPO-based) -->
    <form action="{{ route('pending-po.projectEdit', $pending->id) }}" method="post">
        @method('PATCH')
        @csrf
        <div class="modal fade" id="replacementEditUnit" tabindex="-1" aria-labelledby="replacementEditUnitLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-label-primary py-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-primary text-white font-11 rounded-pill font-monospace fw-bold">
                                    {{ $pending->no_pending ?? 'SO' }}
                                </span>
                                <span class="badge bg-label-secondary font-11">{{ $quote->client->company ?? '-' }}</span>
                            </div>
                            <h5 class="modal-title fw-bold text-heading mb-0" id="replacementEditUnitLabel">
                                <i class="mdi mdi-list-status me-1 text-primary"></i> Update Status Barang &amp; Alokasi Gudang
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert bg-label-info border border-info py-2 px-3 mb-3 rounded-3 font-12">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Ubah angka <strong>BDG / BKS</strong> untuk memindahkan alokasi stok item ke gudang lain. Sistem otomatis melepas alokasi lama dan memvalidasi ketersediaan stok fisik gudang tujuan.
                        </div>

                        <div class="table-responsive border rounded-3 bg-white">
                            <table class="table table-sm table-hover align-middle mb-0 font-12">
                                <thead class="table-light font-11 text-uppercase text-muted">
                                    <tr>
                                        <th style="width: 35px;" class="text-center">#</th>
                                        <th style="min-width: 250px;">Item &amp; Equivalent</th>
                                        <th style="width: 140px;" class="text-center">Status Barang</th>
                                        <th style="width: 95px;" class="text-center">Alokasi BDG</th>
                                        <th style="width: 95px;" class="text-center">Alokasi BKS</th>
                                        <th style="min-width: 180px;">Catatan / Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dPending as $i => $item)
                                        @php
                                            $prod = $item->equivalent?->product;
                                            $bdgStock = $prod?->stock ?? 0;
                                            $bksStock = $prod?->warehouse_stock ?? 0;
                                        @endphp
                                        <tr>
                                            <td class="text-center text-muted font-11">{{ $i + 1 }}</td>
                                            <td>
                                                <div class="mb-1">
                                                    <select class="form-select form-select-sm select2-equivalent-ajax font-12" data-allow-clear="true" name="equivalent[]" style="width:100%">
                                                        <option value="0">-- Pilih Equivalent --</option>
                                                        @if ($item->equivalent)
                                                            <option value="{{ $item->equivalent->id }}" selected>
                                                                {{ $item->equivalent->brand }} {{ $item->equivalent->pn }} - {{ $item->equivalent->product?->go == 'Replacement' ? 'R' : 'G' }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 mt-1" style="font-size: 10.5px;">
                                                    <span class="text-muted"><i class="mdi mdi-warehouse me-0.5"></i>Stok Fisik: BDG: <strong class="text-dark">{{ $bdgStock }}</strong> | BKS: <strong class="text-dark">{{ $bksStock }}</strong></span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <select class="form-select form-select-sm font-11 fw-semibold" name="status[]">
                                                    <option value="1" {{ $item->status == '1' ? 'selected' : '' }}>On Check</option>
                                                    <option value="2" {{ $item->status == '2' ? 'selected' : '' }}>Ready Stock</option>
                                                    <option value="3" {{ $item->status == '3' ? 'selected' : '' }}>Kurang</option>
                                                    <option value="4" {{ $item->status == '4' ? 'selected' : '' }}>Pre-Order</option>
                                                    <option value="5" {{ $item->status == '5' ? 'selected' : '' }}>Delivery Process</option>
                                                    <option value="6" {{ $item->status == '6' ? 'selected' : '' }}>Done</option>
                                                    <option value="7" {{ $item->status == '7' ? 'selected' : '' }}>Cancel</option>
                                                </select>
                                            </td>
                                            <td class="text-center">
                                                <input type="number" class="form-control form-control-sm text-center fw-bold font-12" name="bdg[]" value="{{ (int)$item->bdg }}" min="0">
                                            </td>
                                            <td class="text-center">
                                                <input type="number" class="form-control form-control-sm text-center fw-bold font-12" name="bks[]" value="{{ (int)$item->bks }}" min="0">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm font-11" name="note[]" value="{{ $item->note }}" placeholder="Catatan item...">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center py-4 text-muted font-12">Tidak ada data barang</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2 px-3 d-flex align-items-center justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                            <i class="mdi mdi-check-circle me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Purchase Request (Unit / Project) -->
    <form action="{{ route('purchase-request.store-project', $pending->id) }}" method="post">
        @csrf
        <div class="modal fade" id="purchaseReqUnit" tabindex="-1" aria-labelledby="purchaseReqUnitLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-light py-3 border-bottom">
                        <div>
                            <h5 class="modal-title fw-bold text-primary mb-1 d-flex align-items-center" id="purchaseReqUnitLabel">
                                <i class="mdi mdi-cart-plus me-2 fs-4"></i> Buat Purchase Request (PR)
                            </h5>
                            <div class="text-muted font-12">
                                <span class="fw-semibold text-dark">{{ $quote->client->company ?? '-' }}</span> 
                                &bull; SO #{{ $pending->id }} 
                                @if($pending->id_unit_quotation)
                                    &bull; Unit Quotation #{{ $pending->id_unit_quotation }}
                                @endif
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="alert alert-primary d-flex align-items-center py-2 px-3 mb-3">
                            <i class="mdi mdi-information-outline fs-5 me-2"></i>
                            <span class="font-12">
                                Masukkan <strong>Qty PR</strong> dan <strong>Catatan</strong> untuk item yang ingin dipesan ke supplier. Item dengan Qty 0 akan diabaikan secara otomatis.
                            </span>
                        </div>

                        {{-- Table of items from Sales Order --}}
                        <div class="card border shadow-none mb-3">
                            <div class="card-header bg-transparent py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark font-13"><i class="mdi mdi-format-list-checks me-1 text-primary"></i> Daftar Item dari Sales Order</span>
                                <span class="badge bg-label-info font-11">{{ $dPending->whereNotNull('id_equivalent')->where('id_equivalent', '!=', 0)->count() }} Item Terdaftar</span>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="font-12">
                                            <th style="width: 40px;" class="text-center">No</th>
                                            <th>Item &amp; Equivalent</th>
                                            <th style="width: 130px;" class="text-center">Stok &amp; Status</th>
                                            <th style="width: 90px;" class="text-center">Qty Order</th>
                                            <th style="width: 120px;" class="text-center">Qty PR</th>
                                            <th style="width: 250px;">Catatan PR</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-13">
                                        @php
                                            $noModal = 1;
                                            $orderItems = $dPending->whereNotNull('id_equivalent')->where('id_equivalent', '!=', 0);
                                        @endphp
                                        @forelse ($orderItems as $item)
                                            @php
                                                $prod = $item->equivalent->product ?? null;
                                                $bdgStock = $prod->stock ?? 0;
                                                $bksStock = $prod->warehouse_stock ?? 0;
                                                $orderedQty = $item->id_equivalent ? ($orderedQtyByEquivalent[$item->id_equivalent] ?? ($item->bdg + $item->bks)) : ($item->bdg + $item->bks);
                                                $shortage = max(0, (float)$orderedQty - ($item->bdg + $item->bks));
                                                $defaultQty = ($item->status == 3 || $shortage > 0) ? ($shortage > 0 ? $shortage : (float)$orderedQty) : 0;
                                            @endphp
                                            <tr>
                                                <td class="text-center fw-medium">{{ $noModal }}</td>
                                                <td style="max-width: 320px; white-space: normal;">
                                                    <input type="hidden" name="id_equivalent[]" value="{{ $item->id_equivalent }}">
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <span class="fw-bold text-dark">{{ $item->equivalent->brand ?? '' }} {{ $item->equivalent->pn ?? '' }}</span>
                                                        @if ($prod && $prod->go)
                                                            <span class="badge {{ $prod->go == 'Genuine' ? 'bg-label-success' : 'bg-label-warning' }} font-10">{{ $prod->go }}</span>
                                                        @endif
                                                    </div>
                                                    @if ($prod && $prod->description)
                                                        <div class="text-muted font-11 mt-1">{{ $prod->description }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($item->status == 3)
                                                        <span class="badge bg-label-danger font-11 mb-1">Kurang</span>
                                                    @elseif ($item->status == 2)
                                                        <span class="badge bg-label-success font-11 mb-1">Ready</span>
                                                    @else
                                                        <span class="badge bg-label-secondary font-11 mb-1">On Check</span>
                                                    @endif
                                                    <div class="text-muted font-10">BDG: {{ $bdgStock }} | BKS: {{ $bksStock }}</div>
                                                </td>
                                                <td class="text-center fw-bold text-dark">
                                                    {{ $orderedQty }} {{ $prod->unit ?? '' }}
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm text-center fw-bold"
                                                        name="qty[]" min="0" step="any"
                                                        value="{{ $defaultQty }}" placeholder="0">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="note[]" value="{{ $item->status == 3 ? 'Kebutuhan SO kurang stok' : '' }}"
                                                        placeholder="Catatan item...">
                                                </td>
                                            </tr>
                                            @php $noModal++; @endphp
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-3 text-muted">Tidak ada item dari quotation yang memiliki Equivalent</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Section Optional: Tambah Item Manual / Di Luar SO --}}
                        <div class="card border border-dashed shadow-none">
                            <div class="card-header bg-transparent py-2 px-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <span class="fw-bold text-secondary font-12"><i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Item Manual / Di Luar List SO (Opsional)</span>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-add-manual-pr-row">
                                    <i class="mdi mdi-plus me-1"></i> Tambah Baris Manual
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div id="manualPrItemsContainer" class="d-flex flex-column gap-2">
                                    <div class="manual-pr-row border rounded-3 p-2 bg-light bg-opacity-50">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-5">
                                                <label class="form-label font-11 mb-1">Cari Equivalent Master</label>
                                                <select class="form-select select2-equivalent-ajax" data-allow-clear="true" name="manual_id_equivalent[]" style="width:100%">
                                                    <option value="0"> ---- Cari Part Number / Brand ---- </option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label font-11 mb-1">Qty</label>
                                                <input type="number" class="form-control form-control-sm" name="manual_qty[]" min="0" step="any" placeholder="0">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label font-11 mb-1">Catatan</label>
                                                <input type="text" class="form-control form-control-sm" name="manual_note[]" placeholder="Catatan item manual...">
                                            </div>
                                            <div class="col-md-1 text-center pt-3">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-manual-pr-row" title="Hapus Baris" style="display: none;">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 border-top">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-cart-plus me-1"></i> Buat Purchase Request
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @include('components.modal.pending.modal-product-out')

    <!-- Modal Connect Product Out -->
    <form action="{{ route('pending-po.connect_out', $pending->id) }}" method="post">
        @method('PATCH')
        @csrf
        <div class="modal-onboarding modal fade animate__animated" id="inputProductOutUnit" tabindex="-1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="onboarding-content mb-0">
                            <h4 class="onboarding-title text-body">{{ $quote->client->company ?? '-' }}</h4>
                            <div class="form-floating form-floating-outline mb-2">
                                <select id="product-dropdown-unit" class="select2 form-select" data-allow-clear="true" name="product">
                                    <option selected disabled>---------Pilih No Invoice Product Out--------</option>
                                    @forelse ($allproductOut as $item)
                                        <option value="{{ $item->id }}">{{ $item->invoice }}</option>
                                    @empty
                                        <option value="" disabled>No Product Out</option>
                                    @endforelse
                                </select>
                                <label>Choose Product Out</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Retur Barang (reuse shared markup, generic) -->
    @include('components.modal.pending.return')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('productReturn');
            if (el) el.id = 'productReturnUnit';
        });
    </script>

    <!-- Modal Edit Alamat Pengiriman -->
    <div class="modal fade animate__animated" id="editAddressesUnit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('pending-po.updateAddresses', $pending->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-primary mb-0"><i class="mdi mdi-map-marker-plus me-1"></i> Edit Alamat Pengiriman & Dokumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4 p-3 bg-label-light rounded border">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="fw-bold text-dark d-block mb-0" style="font-size: 13.5px;">Gabungkan Alamat Pengiriman?</label>
                                    <small class="text-muted" style="font-size: 11px;">Kirim dokumen & barang ke alamat yang sama.</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="combine_shipping_and_parts" id="unit_combine_shipping_and_parts" value="1" {{ ($pending->combine_shipping_and_parts ?? true) ? 'checked' : '' }} onchange="toggleAddressLayoutUnit()">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div id="unit_combined_address_section" class="col-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Alamat Pengiriman (Dokumen & Barang)</label>
                                    <select class="form-select mb-2" id="unit_combined_address_select" onchange="onAddressSelectChangeUnit('combined')">
                                        <option value="customer" {{ ($pending->shipping_address_type ?? 'customer') === 'customer' ? 'selected' : '' }}>Main Address: {{ $quote->client->address ?? '-' }}</option>
                                        @foreach ($quote->client->plants ?? [] as $plant)
                                            <option value="{{ $plant->address }}" {{ ($pending->shipping_address_type === 'manual' && $pending->shipping_address_manual === $plant->address) ? 'selected' : '' }}>Plant: {{ $plant->name }} ({{ $plant->address }})</option>
                                        @endforeach
                                        <option value="manual" {{ $pending->shipping_address_type === 'manual' ? 'selected' : '' }}>-- Alamat Lain (Isi Manual) --</option>
                                    </select>
                                    <div id="unit_combined_manual_wrapper" class="d-none mt-2">
                                        <textarea class="form-control" name="shipping_address_manual" id="unit_combined_address_manual" rows="3" placeholder="Masukkan alamat manual lengkap...">{{ $pending->shipping_address_manual }}</textarea>
                                    </div>
                                    <div class="mt-3">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Penerima (Dokumen & Barang)</label>
                                        <select class="form-select" id="unit_combined_recipient_select">
                                            @foreach ($quote->client->pic ?? [] as $c_pic)
                                                <option value="{{ $c_pic->id }}" {{ $pending->shipping_recipient_id == $c_pic->id ? 'selected' : '' }}>
                                                    {{ $c_pic->name_pic }} {{ $c_pic->posisi ? '(' . $c_pic->posisi . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="unit_split_address_section" class="col-12 d-none">
                                <div class="row">
                                    <div class="col-md-6 pe-md-3 border-end">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Alamat Pengiriman Dokumen / Invoice</label>
                                        <select class="form-select mb-2" id="unit_doc_address_select" onchange="onAddressSelectChangeUnit('doc')">
                                            <option value="customer" {{ ($pending->doc_address_type ?? 'customer') === 'customer' ? 'selected' : '' }}>Main Address: {{ $quote->client->address ?? '-' }}</option>
                                            @foreach ($quote->client->plants ?? [] as $plant)
                                                <option value="{{ $plant->address }}" {{ ($pending->doc_address_type === 'manual' && $pending->doc_address_manual === $plant->address) ? 'selected' : '' }}>Plant: {{ $plant->name }} ({{ $plant->address }})</option>
                                            @endforeach
                                            <option value="manual" {{ $pending->doc_address_type === 'manual' ? 'selected' : '' }}>-- Alamat Lain (Isi Manual) --</option>
                                        </select>
                                        <div id="unit_doc_manual_wrapper" class="d-none mt-2">
                                            <textarea class="form-control" name="doc_address_manual" id="unit_doc_address_manual" rows="3" placeholder="Masukkan alamat dokumen manual...">{{ $pending->doc_address_manual }}</textarea>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Penerima Dokumen / Invoice</label>
                                            <select class="form-select" id="unit_doc_recipient_select">
                                                @foreach ($quote->client->pic ?? [] as $c_pic)
                                                    <option value="{{ $c_pic->id }}" {{ $pending->doc_recipient_id == $c_pic->id ? 'selected' : '' }}>
                                                        {{ $c_pic->name_pic }} {{ $c_pic->posisi ? '(' . $c_pic->posisi . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 ps-md-3">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Alamat Pengiriman Barang</label>
                                        <select class="form-select mb-2" id="unit_shipping_address_select" onchange="onAddressSelectChangeUnit('shipping')">
                                            <option value="customer" {{ ($pending->shipping_address_type ?? 'customer') === 'customer' ? 'selected' : '' }}>Main Address: {{ $quote->client->address ?? '-' }}</option>
                                            @foreach ($quote->client->plants ?? [] as $plant)
                                                <option value="{{ $plant->address }}" {{ ($pending->shipping_address_type === 'manual' && $pending->shipping_address_manual === $plant->address) ? 'selected' : '' }}>Plant: {{ $plant->name }} ({{ $plant->address }})</option>
                                            @endforeach
                                            <option value="manual" {{ $pending->shipping_address_type === 'manual' ? 'selected' : '' }}>-- Alamat Lain (Isi Manual) --</option>
                                        </select>
                                        <div id="unit_shipping_manual_wrapper" class="d-none mt-2">
                                            <textarea class="form-control" name="shipping_address_manual" id="unit_shipping_address_manual" rows="3" placeholder="Masukkan alamat barang manual...">{{ $pending->shipping_address_manual }}</textarea>
                                        </div>
                                        <div class="mt-3">
                                            <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Penerima Barang</label>
                                            <select class="form-select" id="unit_shipping_recipient_select">
                                                @foreach ($quote->client->pic ?? [] as $c_pic)
                                                    <option value="{{ $c_pic->id }}" {{ $pending->shipping_recipient_id == $c_pic->id ? 'selected' : '' }}>
                                                        {{ $c_pic->name_pic }} {{ $c_pic->posisi ? '(' . $c_pic->posisi . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-3">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.modal.viewer.pdf')
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .info-col + .info-col {
            border-top: 1px solid #eceef1;
        }
        @media (min-width: 992px) {
            .info-col + .info-col {
                border-top: 0;
                border-left: 1px solid #eceef1;
            }
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('script')
<script>
    function onAddressSelectChangeUnit(type) {
        var selectEl = document.getElementById('unit_' + type + '_address_select');
        var wrapperEl = document.getElementById('unit_' + type + '_manual_wrapper');
        if (selectEl && selectEl.value === 'manual') {
            if (wrapperEl) wrapperEl.classList.remove('d-none');
        } else {
            if (wrapperEl) wrapperEl.classList.add('d-none');
        }
    }

    function toggleAddressLayoutUnit() {
        var combineCheckbox = document.getElementById('unit_combine_shipping_and_parts');
        var combinedSection = document.getElementById('unit_combined_address_section');
        var splitSection = document.getElementById('unit_split_address_section');

        var combinedSelect = document.getElementById('unit_combined_address_select');
        var combinedManual = document.getElementById('unit_combined_address_manual');
        var combinedRecipient = document.getElementById('unit_combined_recipient_select');

        var docSelect = document.getElementById('unit_doc_address_select');
        var docManual = document.getElementById('unit_doc_address_manual');
        var docRecipient = document.getElementById('unit_doc_recipient_select');

        var shippingSelect = document.getElementById('unit_shipping_address_select');
        var shippingManual = document.getElementById('unit_shipping_address_manual');
        var shippingRecipient = document.getElementById('unit_shipping_recipient_select');

        if (combineCheckbox && combineCheckbox.checked) {
            if (combinedSection) combinedSection.classList.remove('d-none');
            if (splitSection) splitSection.classList.add('d-none');

            if (combinedSelect) { combinedSelect.removeAttribute('disabled'); combinedSelect.setAttribute('name', 'shipping_address_type'); }
            if (combinedManual) { combinedManual.removeAttribute('disabled'); combinedManual.setAttribute('name', 'shipping_address_manual'); }
            if (combinedRecipient) { combinedRecipient.removeAttribute('disabled'); combinedRecipient.setAttribute('name', 'shipping_recipient_id'); }

            if (docSelect) { docSelect.setAttribute('disabled', 'disabled'); docSelect.removeAttribute('name'); }
            if (docManual) { docManual.setAttribute('disabled', 'disabled'); docManual.removeAttribute('name'); }
            if (docRecipient) { docRecipient.setAttribute('disabled', 'disabled'); docRecipient.removeAttribute('name'); }
            if (shippingSelect) { shippingSelect.setAttribute('disabled', 'disabled'); shippingSelect.removeAttribute('name'); }
            if (shippingManual) { shippingManual.setAttribute('disabled', 'disabled'); shippingManual.removeAttribute('name'); }
            if (shippingRecipient) { shippingRecipient.setAttribute('disabled', 'disabled'); shippingRecipient.removeAttribute('name'); }
        } else {
            if (combinedSection) combinedSection.classList.add('d-none');
            if (splitSection) splitSection.classList.remove('d-none');

            if (combinedSelect) { combinedSelect.setAttribute('disabled', 'disabled'); combinedSelect.removeAttribute('name'); }
            if (combinedManual) { combinedManual.setAttribute('disabled', 'disabled'); combinedManual.removeAttribute('name'); }
            if (combinedRecipient) { combinedRecipient.setAttribute('disabled', 'disabled'); combinedRecipient.removeAttribute('name'); }

            if (docSelect) { docSelect.removeAttribute('disabled'); docSelect.setAttribute('name', 'doc_address_type'); }
            if (docManual) { docManual.removeAttribute('disabled'); docManual.setAttribute('name', 'doc_address_manual'); }
            if (docRecipient) { docRecipient.removeAttribute('disabled'); docRecipient.setAttribute('name', 'doc_recipient_id'); }
            if (shippingSelect) { shippingSelect.removeAttribute('disabled'); shippingSelect.setAttribute('name', 'shipping_address_type'); }
            if (shippingManual) { shippingManual.removeAttribute('disabled'); shippingManual.setAttribute('name', 'shipping_address_manual'); }
            if (shippingRecipient) { shippingRecipient.removeAttribute('disabled'); shippingRecipient.setAttribute('name', 'shipping_recipient_id'); }
        }
        onAddressSelectChangeUnit('combined');
        onAddressSelectChangeUnit('doc');
        onAddressSelectChangeUnit('shipping');
    }

    $(document).ready(function () {
        $('#editAddressesUnit').on('shown.bs.modal', function () {
            toggleAddressLayoutUnit();
        });
        toggleAddressLayoutUnit();

        $('.select2').each(function () {
            $(this).select2({ dropdownParent: $(this).closest('.modal') });
        });

        // Dropdown Equivalent (Update Status Barang & Purchase Request) — search AJAX ke master
        // product, bukan embed ribuan <option> statis (dulu bikin memory exhausted karena
        // di-render ulang per baris item).
        function initEquivalentSelect2($elem) {
            $elem.select2({
                dropdownParent: $elem.closest('.modal'),
                placeholder: '---- Cari Part Number / Brand ----',
                allowClear: true,
                width: '100%',
                minimumInputLength: 1,
                ajax: {
                    url: '/db/equivalent/search',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) { return { q: params.term }; },
                    processResults: function (data) {
                        var items = Array.isArray(data) ? data : (data.data || []);
                        return {
                            results: $.map(items, function (eq) {
                                var id = eq.id_equivalent || eq.id;
                                var text = (eq.brand || '') + ' ' + (eq.pn || '') + ' - ' + (eq.genuine_status === 'Replacement' ? 'R' : 'G');
                                return { id: id, text: text };
                            })
                        };
                    }
                }
            });
        }

        $('.select2-equivalent-ajax').each(function () {
            initEquivalentSelect2($(this));
        });

        // Dynamic Manual PR Item Rows
        $(document).on('click', '.btn-add-manual-pr-row', function (e) {
            e.preventDefault();
            var $container = $('#manualPrItemsContainer');
            var newRowHtml = `
                <div class="manual-pr-row border rounded-3 p-2 bg-light bg-opacity-50">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <label class="form-label font-11 mb-1">Cari Equivalent Master</label>
                            <select class="form-select select2-equivalent-ajax" data-allow-clear="true" name="manual_id_equivalent[]" style="width:100%">
                                <option value="0"> ---- Cari Part Number / Brand ---- </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-11 mb-1">Qty</label>
                            <input type="number" class="form-control form-control-sm" name="manual_qty[]" min="0" step="any" placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-11 mb-1">Catatan</label>
                            <input type="text" class="form-control form-control-sm" name="manual_note[]" placeholder="Catatan item manual...">
                        </div>
                        <div class="col-md-1 text-center pt-3">
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove-manual-pr-row" title="Hapus Baris">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            var $newRow = $(newRowHtml);
            $container.append($newRow);
            initEquivalentSelect2($newRow.find('.select2-equivalent-ajax'));
            $container.find('.btn-remove-manual-pr-row').show();
        });

        $(document).on('click', '.btn-remove-manual-pr-row', function (e) {
            e.preventDefault();
            var $container = $('#manualPrItemsContainer');
            $(this).closest('.manual-pr-row').remove();
            var rows = $container.find('.manual-pr-row');
            if (rows.length <= 1) {
                rows.find('.btn-remove-manual-pr-row').hide();
            }
        });

        $(document).on('click', '.clear-return-unit', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: 'Clear semua data return?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Clear',
                cancelButtonText: 'Batal',
                customClass: { confirmButton: 'btn btn-primary me-3 waves-effect', cancelButton: 'btn btn-label-secondary waves-effect' },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("pending-po/clear-return") }}/' + id,
                        type: 'POST',
                        data: { _method: 'PATCH', _token: '{{ csrf_token() }}' },
                        success: function () { location.reload(); }
                    });
                }
            });
        });

        $(document).on('click', '.done-po-unit', function () {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Tandai Sales Order ini Done?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Done',
                cancelButtonText: 'Batal',
                customClass: { confirmButton: 'btn btn-primary me-3 waves-effect', cancelButton: 'btn btn-label-secondary waves-effect' },
                buttonsStyling: false,
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("pending-po/done") }}/' + id,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function () { location.reload(); }
                    });
                }
            });
        });
    });
</script>
@endpush
