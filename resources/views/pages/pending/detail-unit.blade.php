@extends('layouts.sales.app')
@section('title', 'Detail Sales Order')
@section('content')
    @php
        $isInvoiceApproved = $invoices->contains(fn ($inv) => $inv->no_invoice !== null);
    @endphp

    @if (!$isInvoiceApproved)
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2 fs-4"></i>
            <div>
                <strong>Perhatian:</strong> Invoice untuk PO ini belum di-approve oleh Accounting. Proses logistik (Update Status, Resi, &amp; Done) dikunci sementara hingga invoice disetujui.
            </div>
        </div>
    @endif

    <div class="card mb-4 text-white border-0 position-relative shadow-sm" style="background: linear-gradient(135deg, #696cff 0%, #3f42b3 100%) !important;">
        <div class="position-absolute overflow-hidden" style="inset: 0; border-radius: inherit; z-index: 1;">
            <div class="position-absolute translate-middle" style="top: 0; right: 0; width: 250px; height: 250px; border-radius: 50%; background: rgba(255,255,255,0.08);"></div>
            <div class="position-absolute translate-middle" style="bottom: -50px; left: -50px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.05);"></div>
        </div>
        <div class="card-body p-4 position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-white text-primary fw-bold text-uppercase px-3 py-1.5 fs-7" style="border-radius: 5px;">Sales Order</span>
                        @php
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
                        @endphp
                        <span class="badge {{ $statusBadge }} fw-bold"><i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> {{ $statusName }}</span>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">{{ $quote->client->company ?? '-' }}</h3>
                    <p class="mb-1 opacity-80 small">
                        <i class="mdi mdi-tag-outline me-1"></i> No SO: <span class="fw-semibold text-white">{{ $pending->no_pending }}</span>
                        <span class="mx-2">|</span>
                        <i class="mdi mdi-calendar-blank-outline me-1"></i> Tanggal: <span class="fw-semibold text-white">{{ \Carbon\Carbon::parse($pending->date)->format('d M Y') }}</span>
                    </p>
                    <p class="mb-0 opacity-80 small">
                        <i class="mdi mdi-account-tie me-1"></i> Sales PIC: <span class="fw-semibold text-white">{{ $quote->sales->name ?? '-' }}</span>
                        <span class="mx-2">|</span>
                        <i class="mdi mdi-account me-1"></i> PIC Klien: <span class="fw-semibold text-white">{{ $quote->pic->name_pic ?? ($quote->attn ?? '-') }}</span>
                        @if ($quote->pic->phone_pic ?? null)
                            <span class="mx-2">|</span>
                            <i class="mdi mdi-phone-outline me-1"></i> <span class="fw-semibold text-white">{{ $quote->pic->phone_pic }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex flex-column flex-md-row justify-content-md-end gap-2 align-items-md-center">
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
                    <button type="button" class="btn btn-outline-light waves-effect waves-light text-white" data-bs-toggle="modal" data-bs-target="#editAddressesUnit">
                        <i class="mdi mdi-map-marker-outline me-1"></i> Edit Alamat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="mdi mdi-information-outline fs-4 text-primary"></i>
        <h5 class="fw-bold mb-0 text-dark text-uppercase" style="letter-spacing: .3px; font-size: 14px;">Informasi Detail</h5>
    </div>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="row g-0">
                <!-- Alamat Dokumen & Pengiriman -->
                <div class="col-lg-4 info-col p-4">
                    <h6 class="fw-bold text-primary mb-3 text-uppercase" style="font-size: 12.5px; letter-spacing: .3px;"><i class="mdi mdi-map-marker-outline me-2"></i> Alamat Dokumen &amp; Pengiriman</h6>
                            @php
                                $chargeLabel = function ($val) {
                                    if ($val == 1) return ['Company', 'bg-label-primary'];
                                    if ($val == 2) return ['Customer', 'bg-label-success'];
                                    return [null, null];
                                };
                                $docChargeVal = $pending->combine_shipping_and_parts ? $pending->charged : $pending->doc_charged;
                                $shippingChargeVal = $pending->combine_shipping_and_parts ? $pending->charged : $pending->shipping_charged;
                                [$docChargeText, $docChargeClass] = $chargeLabel($docChargeVal);
                                [$shippingChargeText, $shippingChargeClass] = $chargeLabel($shippingChargeVal);
                            @endphp

                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(113, 221, 55, 0.08); color: #71dd37;">
                                    <i class="mdi mdi-package-variant fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Keterangan Gabung</small>
                                    <span class="badge {{ $pending->combine_shipping_and_parts ? 'bg-label-success' : 'bg-label-danger' }} fw-semibold" style="font-size: 10px; padding: 3px 6px;">
                                        {{ $pending->combine_shipping_and_parts ? 'Barang & Part Digabung' : 'Barang & Part Dipisah' }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(105, 108, 255, 0.08); color: #696cff;">
                                    <i class="mdi mdi-file-document-box-outline fs-5"></i>
                                </div>
                                <div class="text-wrap" style="max-width: calc(100% - 50px);">
                                    <small class="text-muted d-block" style="font-size: 11px;">Alamat Dokumen / Invoice</small>
                                    @if (($pending->doc_address_type ?? 'customer') === 'customer')
                                        <span class="badge bg-label-secondary mb-1 btn-xs" style="font-size: 9px; padding: 2px 4px;">Sesuai Customer</span>
                                        <span class="fw-semibold text-dark d-block" style="font-size: 12px; line-height: 1.4;">{{ $quote->client->address ?? '-' }}</span>
                                    @else
                                        <span class="badge bg-label-warning mb-1 btn-xs" style="font-size: 9px; padding: 2px 4px;">Manual</span>
                                        <span class="fw-semibold text-dark d-block" style="font-size: 12px; line-height: 1.4;">{{ $pending->doc_address_manual }}</span>
                                    @endif
                                    @if ($docChargeText)
                                        <span class="badge {{ $docChargeClass }} fw-semibold mt-1 btn-xs" style="font-size: 9px; padding: 2px 4px;">Charged to: {{ $docChargeText }}</span>
                                    @endif
                                    @if ($pending->doc_recipient)
                                        <span class="badge bg-label-info fw-semibold mt-1 btn-xs" style="font-size: 9px; padding: 2px 4px;">Penerima: {{ $pending->doc_recipient->name_pic }} | Telp: {{ $pending->doc_recipient->phone ?? '-' }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(255, 62, 29, 0.08); color: #ff3e1d;">
                                    <i class="mdi mdi-map-marker-outline fs-5"></i>
                                </div>
                                <div class="text-wrap" style="max-width: calc(100% - 50px);">
                                    <small class="text-muted d-block" style="font-size: 11px;">Alamat Pengiriman Barang</small>
                                    @if (($pending->shipping_address_type ?? 'customer') === 'customer')
                                        <span class="badge bg-label-secondary mb-1 btn-xs" style="font-size: 9px; padding: 2px 4px;">Sesuai Customer</span>
                                        <span class="fw-semibold text-dark d-block" style="font-size: 12px; line-height: 1.4;">{{ $quote->client->address ?? '-' }}</span>
                                    @else
                                        <span class="badge bg-label-warning mb-1 btn-xs" style="font-size: 9px; padding: 2px 4px;">Manual</span>
                                        <span class="fw-semibold text-dark d-block" style="font-size: 12px; line-height: 1.4;">{{ $pending->shipping_address_manual }}</span>
                                    @endif
                                    @if ($shippingChargeText)
                                        <span class="badge {{ $shippingChargeClass }} fw-semibold mt-1 btn-xs" style="font-size: 9px; padding: 2px 4px;">Charged to: {{ $shippingChargeText }}</span>
                                    @endif
                                    @if ($pending->shipping_recipient)
                                        <span class="badge bg-label-info fw-semibold mt-1 btn-xs" style="font-size: 9px; padding: 2px 4px;">Penerima: {{ $pending->shipping_recipient->name_pic }} | Telp: {{ $pending->shipping_recipient->phone ?? '-' }}</span>
                                    @endif
                                </div>
                            </div>
                </div>

                <!-- Document Info -->
                <div class="col-lg-4 info-col p-4">
                    <h6 class="fw-bold text-primary mb-3 text-uppercase" style="font-size: 12.5px; letter-spacing: .3px;"><i class="mdi mdi-file-document-outline me-2"></i> Informasi Dokumen</h6>
                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(255, 171, 0, 0.08); color: #ffab00;">
                                    <i class="mdi mdi-cart-outline fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">No PO</small>
                                    @if ($quote->po_number)
                                        <span class="fw-bold text-dark">{{ $quote->po_number }}</span>
                                    @else
                                        <span class="text-danger fw-semibold">Belum ada No PO</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-1 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3 mt-1" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(113, 221, 55, 0.08); color: #71dd37;">
                                    <i class="mdi mdi-receipt-outline fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block mb-1" style="font-size: 11px;">Invoice</small>
                                    @forelse ($invoices as $inv)
                                        <div class="mb-1">
                                            @if ($inv->no_invoice)
                                                <a class="fw-bold text-success d-block" href="{{ route('invoice.show', $inv->id) }}">{{ $inv->no_invoice }}</a>
                                            @else
                                                <span class="text-danger fw-semibold d-block" style="font-size: 12px;">{{ $inv->type }} - Belum di-approve</span>
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-danger fw-semibold">Belum ada invoice</span>
                                    @endforelse
                                </div>
                            </div>
                            @php
                                $isPaymentConfirmed = $invoices->contains(fn ($i) => $i->status_p == 1);
                                $isTempoPayment = stripos($quote->payment_method ?? '', 'Tempo') !== false;
                            @endphp
                            <div class="d-flex align-items-center mb-1 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(255, 62, 29, 0.08); color: #ff3e1d;">
                                    <i class="mdi mdi-checkbox-marked-circle-outline fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Status Pembayaran</small>
                                    @if ($isPaymentConfirmed)
                                        @if ($isTempoPayment)
                                            <span class="badge bg-label-info fw-semibold">Credit</span>
                                        @else
                                            <span class="badge bg-label-success fw-semibold">Paid</span>
                                        @endif
                                    @else
                                        <span class="badge bg-label-danger fw-semibold">Unpaid</span>
                                    @endif
                                </div>
                            </div>
                </div>

                <!-- Shipping / Resi Info -->
                <div class="col-lg-4 info-col p-4 d-flex flex-column" style="max-height: 450px; overflow-y: auto;">
                    <h6 class="fw-bold text-primary mb-3 text-uppercase" style="font-size: 12.5px; letter-spacing: .3px;"><i class="mdi mdi-truck-delivery-outline me-2"></i> Informasi Pengiriman</h6>
                            @php
                                switch ($pending->delivery) {
                                    case 1: $kurir = 'JNE / J&T / Cargo'; break;
                                    case 2: $kurir = 'Send By Technician'; break;
                                    case 3: $kurir = 'Taken Directly'; break;
                                    case 4: $kurir = 'Other'; break;
                                    default: $kurir = 'Belum Ada Kurir'; break;
                                }
                            @endphp
                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(3, 195, 236, 0.08); color: #03c3ec;">
                                    <i class="mdi mdi-truck-outline fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Kurir</small>
                                    <span class="fw-semibold text-dark">{{ $kurir }}</span>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark mt-2 mb-2 border-bottom pb-1" style="font-size: 13px;">Daftar Resi & Ongkos Kirim:</h6>
                            @forelse ($resis as $r)
                                <div class="mb-3 p-2 rounded bg-label-light border border-light hover-light">
                                    <small class="text-muted d-block" style="font-size: 10px;">No Tracking / Resi</small>
                                    <span class="fw-bold text-dark fs-7">{{ $r->no_track ?? '-' }}</span>
                                    @if ($r->note)
                                        <p class="mb-0 text-muted small mt-1" style="line-height: 1.3;">{{ $r->note }}</p>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                                        <span class="fw-bold text-primary" style="font-size: 12px;">Rp {{ number_format($r->cost ?? 0, 0, '.', ',') }}</span>
                                        @if ($r->image)
                                            <a href="#" onclick="openPdfViewer('{{ url($r->image) }}', 'Resi {{ $r->no_track }}'); return false;" class="btn btn-xs btn-outline-primary px-2" style="font-size: 10px; padding: 2px 4px;">
                                                <i class="mdi mdi-image-outline"></i> Resi
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted" style="font-size: 12px;">
                                    <i class="mdi mdi-alert-circle-outline d-block fs-3 mb-1"></i> Belum ada resi pengiriman yang diunggah.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

    <div class="d-flex align-items-center gap-2 mb-3 mt-2">
        <i class="mdi mdi-package-variant-closed fs-4 text-primary"></i>
        <h5 class="fw-bold mb-0 text-dark text-uppercase" style="letter-spacing: .3px; font-size: 14px;">Logistik &amp; Pengiriman</h5>
    </div>

    <!-- Items Table -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="m-0 fw-bold text-primary"><i class="mdi mdi-package-variant-closed me-2"></i> Daftar Barang</h5>
            @if ($pending->status != '6' && $pending->status != '8')
                <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                    data-bs-target="#replacementEditUnit" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                    <i class="mdi mdi-list-status me-1"></i> Update Status &amp; Gudang
                </button>
            @endif
        </div>
        <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Gudang</th>
                                <th>Status</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                                // Qty asli yang di-order di quotation, dikelompokkan per equivalent —
                                // dipakai sebagai acuan kolom Qty (bukan bdg+bks yang cuma qty stok teralokasi,
                                // yang bisa lebih kecil dari qty order saat status Kurang).
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
                                @endphp
                                @php
                                    $goLabels = [
                                        'Replacement' => 'bg-label-warning',
                                        'Genuine'     => 'bg-label-success',
                                        'OEM'         => 'bg-label-info',
                                    ];
                                    $goVal = $item->equivalent->product->go ?? null;
                                    $orderedQty = $item->id_equivalent ? ($orderedQtyByEquivalent[$item->id_equivalent] ?? null) : null;
                                @endphp
                                <tr style="font-size: 13px">
                                    <td class="text-center">{{ $no }}</td>
                                    <td class="fw-semibold">
                                        @if (empty($item->id_equivalent) || $item->id_equivalent == '0')
                                            {{ $item->note ?: '-' }}
                                        @else
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <span>{{ $item->equivalent->brand ?? '' }} {{ $item->equivalent->pn ?? '' }}</span>
                                                @if ($goVal && isset($goLabels[$goVal]))
                                                    <span class="badge {{ $goLabels[$goVal] }}" style="font-size: 9px;">{{ $goVal }}</span>
                                                @endif
                                            </div>
                                            @if ($item->equivalent->product->description ?? null)
                                                <div class="text-muted fw-normal text-wrap" style="font-size: 11.5px; line-height: 1.4; max-width: 320px;">{{ $item->equivalent->product->description }}</div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $orderedQty !== null ? (float) $orderedQty : ($item->bdg + $item->bks) }}</td>
                                    <td>
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
                                    <td>
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
            @if (!$purchase && $dPending->where('status', 3)->count() > 0)
                <div class="alert alert-warning d-flex align-items-center mb-3">
                    <i class="mdi mdi-clock-alert-outline me-2 fs-5"></i>
                    Ada item yang stoknya kurang, tapi Purchase Request belum dibuat — menunggu konfirmasi payment DP dari Accounting.
                </div>
            @endif
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="m-0 fw-bold text-primary"><i class="mdi mdi-cart-arrow-down me-2"></i> Purchase Request</h5>
                    @if ($pending->status != '6' && $pending->status != '8')
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#purchaseReqUnit" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                            <i class="mdi mdi-plus-box me-1"></i> Purchase Request
                        </button>
                    @endif
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>No PR</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Note</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                                // Samakan label & warna dengan tab status di halaman /purchase-request.
                                // Status 1 (Approved) yang PO-nya sudah terbit dianggap sudah masuk
                                // tahap "Purchase Order" (lihat poCount di PurchaseController::index),
                                // jadi labelnya diganti biar jelas tinggal nunggu barang dari supplier.
                                if (($purchase->status ?? null) == '1' && ($purchase->purchaseOrders->count() ?? 0) > 0) {
                                    $status_pr = 'Menunggu Pengiriman Supplier';
                                    $color_pr = 'bg-label-dark';
                                } else {
                                    switch ($purchase->status ?? null) {
                                        case '1': $status_pr = 'Approved'; $color_pr = 'bg-label-warning'; break;
                                        case '2': $status_pr = 'Delivery'; $color_pr = 'bg-label-info'; break;
                                        case '3': $status_pr = 'Good Receipt'; $color_pr = 'bg-label-success'; break;
                                        default: $status_pr = 'New Purchase'; $color_pr = 'bg-label-primary'; break;
                                    }
                                }
                            @endphp
                            @forelse (($purchase->details ?? collect()) as $pr)
                                <tr>
                                    <td class="text-center">{{ $no }}</td>
                                    <td class="fw-bold"><a href="{{ route('purchase-request.show', $pending->id) }}" class="text-primary">{{ $purchase->no_pr ?? '-' }}</a></td>
                                    <td>
                                        @if ($pr->id_equivalent == '0')
                                            -
                                        @else
                                            {{ $pr->equivalent->brand ?? '' }} {{ $pr->equivalent->pn ?? '' }}
                                        @endif
                                    </td>
                                    <td>{{ $pr->qty }} {{ $pr->equivalent->product->unit ?? '' }}</td>
                                    <td class="text-wrap" style="max-width: 250px;">{{ $pr->note ?? '-' }}</td>
                                    <td><span class="badge {{ $color_pr }}">{{ $status_pr }}</span></td>
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

            <!-- Returns Section (hidden kalau belum ada return sama sekali — modul retur lebih lengkap menyusul) -->
            @if ($return->isNotEmpty())
                <div class="card mb-4 shadow-sm border">
                    <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold"><i class="mdi mdi-arrow-u-left-bottom text-primary me-1"></i> Retur Barang</h5>
                        <a href="#" class="btn btn-sm btn-outline-danger clear-return-unit waves-effect" data-id="{{ $pending->id }}">
                            <i class="mdi mdi-eraser-variant me-1"></i> Clear Return
                        </a>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
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
                                            <a href="{{ route('return.show', $retur->id) }}" class="fw-bold text-primary">
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
                <div class="card invoice-preview-card border shadow-sm mb-4">
                    <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="m-0 fw-bold text-success"><i class="mdi mdi-file-document-check-outline me-1"></i> Surat Jalan Barang Keluar ({{ $product->vers }})</h5>
                        <h6 class="m-0 fw-semibold text-muted">#{{ $product->no_type == '1' ? $product->invoice : $product->po }}</h6>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <span class="text-muted fw-semibold d-block mb-1">Customers / Alamat Pengiriman:</span>
                                <pre class="mb-0 text-dark fw-medium p-2 bg-light rounded border text-wrap" style="font-family: inherit; font-size: 14px;">{{ $product->detail_client }}</pre>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted fw-semibold d-block mb-1">Tanggal Keluar:</span>
                                <p class="mb-0 fw-medium text-dark"><i class="mdi mdi-calendar-range me-1"></i> {{ Carbon\Carbon::parse($product->date)->format('d-m-Y') }}</p>
                            </div>
                            <div class="col-md-3">
                                <span class="text-muted fw-semibold d-block mb-1">Dibuat Oleh:</span>
                                <p class="mb-0 fw-medium text-dark"><i class="mdi mdi-account-circle-outline me-1"></i> {{ $product->user->name }}</p>
                            </div>
                            @if($product->note)
                                <div class="col-12">
                                    <span class="text-muted fw-semibold d-block mb-1">Catatan Tambahan:</span>
                                    <pre class="mb-0 text-muted p-2 bg-light rounded border text-wrap" style="font-family: inherit; font-size: 13px;">{{ $product->note }}</pre>
                                </div>
                            @endif
                        </div>

                        <div class="table-responsive border rounded mt-4">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Item Keluar</th>
                                        <th>Qty</th>
                                        <th>Harga Satuan</th>
                                        <th>Subtotal</th>
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
                                            <td>{{ $products->qty }} {{ $products->detailProduct->product->unit }}</td>
                                            <td>Rp {{ number_format($products->price, 0, ',', '.') }}</td>
                                            <td class="fw-bold">Rp {{ number_format($products->amount, 0, ',', '.') }}</td>
                                        </tr>
                                        @php $no++; @endphp
                                    @endforeach
                                    <tr class="table-light">
                                        <td colspan="3" class="border-0"></td>
                                        <td class="fw-semibold">Shipping Cost</td>
                                        <td class="fw-bold">: Rp {{ number_format($product->shipping, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="table-light">
                                        <td colspan="3" class="border-0"></td>
                                        <td class="fw-semibold border-top text-primary">Grand Total</td>
                                        <td class="fw-bold border-top text-primary">: Rp {{ number_format($product->total, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

    <!-- Activity Timeline -->
    @if ($activity->count() >= 1)
        <div class="d-flex align-items-center gap-2 mb-3 mt-2">
            <i class="mdi mdi-clock-outline fs-4 text-primary"></i>
            <h5 class="fw-bold mb-0 text-dark text-uppercase" style="letter-spacing: .3px; font-size: 14px;">Riwayat Aktivitas</h5>
        </div>
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body pt-4">
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
        <div class="modal-onboarding modal fade animate__animated" id="replacementEditUnit" tabindex="-1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="onboarding-content mb-0">
                            <h4 class="onboarding-title text-body">{{ $quote->client->company ?? '-' }}</h4>
                            <p class="text-muted mb-3" style="font-size: 12.5px;">Ubah angka BDG / BKS untuk memindahkan alokasi stok item ke gudang lain — sistem otomatis melepas alokasi lama dan mengecek ketersediaan stok gudang tujuan sebelum menyimpan.</p>
                            <div class="card">
                                <div class="table-responsive text-nowrap h-100">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th style="width: 25%">Item</th>
                                                <th style="width: 15%">Status</th>
                                                <th style="width: 10%">BDG</th>
                                                <th style="width: 10%">BKS</th>
                                                <th style="width: 20%">Note</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            @forelse ($dPending as $i => $item)
                                                <tr>
                                                    <td>{{ $i + 1 }}</td>
                                                    <td class="text-start">
                                                        <div class="form-floating form-floating-outline mb-2">
                                                            <select class="form-select select2-equivalent-ajax" data-allow-clear="true" name="equivalent[]" style="width:100%">
                                                                <option value="0"> ---- Choose Equivalent Here ---- </option>
                                                                @if ($item->equivalent)
                                                                    <option value="{{ $item->equivalent->id }}" selected>
                                                                        {{ $item->equivalent->brand }} {{ $item->equivalent->pn }} - {{ $item->equivalent->product?->go == 'Replacement' ? 'R' : 'G' }}
                                                                    </option>
                                                                @endif
                                                            </select>
                                                            <label class="mb-2">Equivalent</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <select class="form-select" name="status[]">
                                                                <option value="1" {{ $item->status == '1' ? 'selected' : '' }}>On Check</option>
                                                                <option value="2" {{ $item->status == '2' ? 'selected' : '' }}>Ready Stock</option>
                                                                <option value="3" {{ $item->status == '3' ? 'selected' : '' }}>Kurang</option>
                                                                <option value="4" {{ $item->status == '4' ? 'selected' : '' }}>Pre-Order</option>
                                                                <option value="5" {{ $item->status == '5' ? 'selected' : '' }}>Delivery Process</option>
                                                                <option value="6" {{ $item->status == '6' ? 'selected' : '' }}>Done</option>
                                                                <option value="7" {{ $item->status == '7' ? 'selected' : '' }}>Cancel</option>
                                                            </select>
                                                            <label>Status</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" class="form-control" name="bdg[]" value="{{ $item->bdg }}">
                                                            <label>Bandung</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" class="form-control" name="bks[]" value="{{ $item->bks }}">
                                                            <label>Bekasi</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <textarea class="form-control" name="note[]" placeholder="Comments here...">{{ $item->note }}</textarea>
                                                            <label>Note</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center py-3 text-muted">Tidak ada data barang</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mt-4 border-0">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal Purchase Request (single item, generic route) -->
    <form action="{{ route('purchase-request.store-project', $pending->id) }}" method="post">
        @csrf
        <div class="modal-onboarding modal fade animate__animated" id="purchaseReqUnit" tabindex="-1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content text-center">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="onboarding-content mb-0">
                            <h4 class="onboarding-title text-body">{{ $quote->client->company ?? '-' }}</h4>
                            <div class="card">
                                <div class="table-responsive text-nowrap h-100">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 35%">Item</th>
                                                <th>Qty</th>
                                                <th style="width: 35%">Note</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="text-start">
                                                    <div class="form-floating form-floating-outline mb-2">
                                                        <select class="form-select select2-equivalent-ajax" data-allow-clear="true" name="id_equivalent" style="width:100%">
                                                            <option value="0"> ---- Choose Equivalent Here ---- </option>
                                                        </select>
                                                        <label class="mb-2">Equivalent</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" class="form-control" name="qty" min="0">
                                                        <label>Qty</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <textarea class="form-control" name="note" placeholder="Text Note here..."></textarea>
                                                        <label>Note</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
        $('.select2-equivalent-ajax').each(function () {
            var $sel = $(this);
            $sel.select2({
                dropdownParent: $sel.closest('.modal'),
                placeholder: '---- Choose Equivalent Here ----',
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
