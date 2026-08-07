@extends('layouts.sales.app')
@section('title', 'Detail Sales Order')
@section('content')
    @php
        $isInvoiceApproved = false;
        if ($invoice) {
            if (is_iterable($invoice)) {
                foreach ($invoice as $inv) {
                    if ($inv->no_invoice !== null) {
                        $isInvoiceApproved = true;
                        break;
                    }
                }
            } else {
                if ($invoice->no_invoice !== null) {
                    $isInvoiceApproved = true;
                }
            }
        }
    @endphp

    @if (!$isInvoiceApproved)
        <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2 fs-4"></i>
            <div>
                <strong>Perhatian:</strong> Invoice untuk PO ini belum di-approve oleh Accounting. Proses logistik (Update Status, Resi, &amp; Done) dikunci sementara hingga invoice disetujui.
            </div>
        </div>
    @endif

    <div class="card mb-4 text-white border-0 overflow-hidden position-relative shadow-sm" style="background: linear-gradient(135deg, #696cff 0%, #3f42b3 100%) !important;">
        <!-- Subtle background circle decorations -->
        <div class="position-absolute translate-middle" style="top: 0; right: 0; width: 250px; height: 250px; border-radius: 50%; background: rgba(255,255,255,0.08); z-index: 1;"></div>
        <div class="position-absolute translate-middle" style="bottom: -50px; left: -50px; width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.05); z-index: 1;"></div>
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
                                default: $statusName = 'In Progress'; $statusBadge = 'bg-secondary text-white'; break;
                            }
                        @endphp
                        <span class="badge {{ $statusBadge }} fw-bold"><i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> {{ $statusName }}</span>
                    </div>
                    <h3 class="fw-bold mb-1 text-white">{{ $quotation->pic->client->company }}</h3>
                    <p class="mb-0 opacity-80 small">
                        <i class="mdi mdi-tag-outline me-1"></i> No SO: <span class="fw-semibold text-white">{{ $pending->no_pending }}</span>
                        <span class="mx-2">|</span>
                        <i class="mdi mdi-calendar-blank-outline me-1"></i> Tanggal: <span class="fw-semibold text-white">{{ \Carbon\Carbon::parse($pending->date)->format('d M Y') }}</span>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0 d-flex flex-column flex-md-row justify-content-md-end gap-2 align-items-md-center">
                    @if ($pending->status != '6' && $pending->status != '8' && $pending->status != '9')
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-light dropdown-toggle waves-effect waves-light text-white"
                                data-bs-toggle="dropdown" aria-expanded="false" {{ (auth()->user()->role != 'Sales' && $isInvoiceApproved) ? '' : 'disabled' }}>
                                <i class="mdi mdi-square-edit-outline me-1"></i> Update Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#deliveryEdit"><i class="mdi mdi-truck-delivery-outline me-2 text-primary"></i>Kurir</a></li>
                                <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#statusEdit"><i class="mdi mdi-list-status me-2 text-warning"></i>Pending PO</a></li>
                                <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-bs-toggle="modal"
                                        data-bs-target="#resiEdit"><i class="mdi mdi-barcode-scan me-2 text-success"></i>Upload Resi</a></li>
                            </ul>
                        </div>
                    @elseif ($pending->status == '6')
                        @if ($pending->id_product_out == null)
                            <button type="button" class="btn btn-danger waves-effect waves-light border-0" data-bs-toggle="modal" data-bs-target="#inputProductOut"
                                {{ (auth()->user()->role != 'Sales' && $isInvoiceApproved) ? '' : 'disabled' }}>
                                <i class="mdi mdi-connection me-1"></i> Connect Product Out
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-light waves-effect waves-light text-white" data-bs-toggle="modal" data-bs-target="#productReturn"
                                {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                                <i class="mdi mdi-arrow-u-left-bottom me-1"></i> Retur Barang
                            </button>
                        @endif
                    @elseif ($pending->status == '9')
                        <button type="button" class="btn btn-success done-po waves-effect waves-light border-0 text-white" data-id="{{ $pending->id }}"
                            {{ (auth()->user()->role != 'Sales' && $isInvoiceApproved) ? '' : 'disabled' }}>
                            <i class="mdi mdi-check-decagram-outline me-1"></i> Done
                        </button>
                        <button type="button" class="btn btn-outline-light waves-effect waves-light text-white" data-bs-toggle="modal" data-bs-target="#inputProductOut"
                            {{ (auth()->user()->role != 'Sales' && $isInvoiceApproved) ? '' : 'disabled' }}>
                            <i class="mdi mdi-connection me-1"></i> Connect Product Out
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs nav-fill mb-4 shadow-sm rounded bg-white p-1" id="orderDetailTabs" role="tablist" style="border: none;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold py-3 text-uppercase fs-7 d-flex align-items-center justify-content-center gap-2 border-0" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail-pane" type="button" role="tab" aria-controls="detail-pane" aria-selected="true" style="border-radius: 5px; transition: all 0.2s;">
                <i class="mdi mdi-information-outline fs-5"></i> Detail
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold py-3 text-uppercase fs-7 d-flex align-items-center justify-content-center gap-2 border-0" id="logistic-tab" data-bs-toggle="tab" data-bs-target="#logistic-pane" type="button" role="tab" aria-controls="logistic-pane" aria-selected="false" style="border-radius: 5px; transition: all 0.2s;">
                <i class="mdi mdi-package-variant-closed fs-5"></i> Logistic
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold py-3 text-uppercase fs-7 d-flex align-items-center justify-content-center gap-2 border-0" id="finance-tab" data-bs-toggle="tab" data-bs-target="#finance-pane" type="button" role="tab" aria-controls="finance-pane" aria-selected="false" style="border-radius: 5px; transition: all 0.2s;">
                <i class="mdi mdi-cash-multiple fs-5"></i> Finance / Accounting
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content p-0 border-0 bg-transparent" id="orderDetailTabsContent">
        <!-- 1. DETAIL TAB -->
        <div class="tab-pane fade show active" id="detail-pane" role="tabpanel" aria-labelledby="detail-tab">
            <!-- Metadata Grid -->
            <div class="row g-4 mb-4">
                <!-- Client & PIC Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header pb-2 border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-bold text-primary"><i class="mdi mdi-office-building-outline me-2"></i> Informasi Klien</h5>
                            @if ($pending->status != '6' && $pending->status != '8' && $pending->status != '9')
                                <button type="button" class="btn btn-sm btn-icon btn-outline-primary border-0 rounded-circle hover-elevate" data-bs-toggle="modal" data-bs-target="#editAddresses" title="Edit Alamat Pengiriman">
                                    <i class="mdi mdi-pencil-outline"></i>
                                </button>
                            @endif
                        </div>
                        <div class="card-body pt-3">
                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(105, 108, 255, 0.08); color: #696cff;">
                                    <i class="mdi mdi-account-tie fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Sales PIC</small>
                                    <span class="fw-semibold text-dark">{{ $quotation->sales->name }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(3, 195, 236, 0.08); color: #03c3ec;">
                                    <i class="mdi mdi-domain fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Klien</small>
                                    <span class="fw-semibold text-dark">{{ $quotation->pic->client->company }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(255, 171, 0, 0.08); color: #ffab00;">
                                    <i class="mdi mdi-account fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">PIC Klien</small>
                                    <span class="fw-semibold text-dark">{{ $quotation->pic->name_pic }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(113, 221, 55, 0.08); color: #71dd37;">
                                    <i class="mdi mdi-flag-outline fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Flag Info</small>
                                    <span class="badge bg-label-info fw-semibold">{{ $quotation->pic->client->info ?? '-' }}</span>
                                </div>
                            </div>

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

                            <!-- Alamat Tagihan / Dokumen -->
                            <div class="d-flex align-items-start mb-3 p-2 rounded hover-light border-top pt-3">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(105, 108, 255, 0.08); color: #696cff;">
                                    <i class="mdi mdi-file-document-box-outline fs-5"></i>
                                </div>
                                <div class="text-wrap" style="max-width: calc(100% - 50px);">
                                    <small class="text-muted d-block" style="font-size: 11px;">Alamat Dokumen / Invoice</small>
                                    @if (($pending->doc_address_type ?? 'customer') === 'customer')
                                        <span class="badge bg-label-secondary mb-1 btn-xs" style="font-size: 9px; padding: 2px 4px;">Sesuai Customer</span>
                                        <span class="fw-semibold text-dark d-block" style="font-size: 12px; line-height: 1.4;">{{ $quotation->pic->client->address }}</span>
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

                            <!-- Alamat Pengiriman Barang -->
                            <div class="d-flex align-items-start mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(255, 62, 29, 0.08); color: #ff3e1d;">
                                    <i class="mdi mdi-map-marker-outline fs-5"></i>
                                </div>
                                <div class="text-wrap" style="max-width: calc(100% - 50px);">
                                    <small class="text-muted d-block" style="font-size: 11px;">Alamat Pengiriman Barang</small>
                                    @if (($pending->shipping_address_type ?? 'customer') === 'customer')
                                        <span class="badge bg-label-secondary mb-1 btn-xs" style="font-size: 9px; padding: 2px 4px;">Sesuai Customer</span>
                                        <span class="fw-semibold text-dark d-block" style="font-size: 12px; line-height: 1.4;">{{ $quotation->pic->client->address }}</span>
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

                            <!-- Status Gabung Barang & Part -->
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
                            
                            @if ($pending->status != '6' && $pending->status != '8' && $pending->status != '9')
                                <!-- Move to Project Monitoring Button -->
                                <form action="{{ route('pending-po.changeType', $pending->id) }}" method="POST" class="mt-3 pt-2 border-top">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning w-100 btn-sm hover-elevate fw-semibold" onclick="return confirm('Apakah Anda yakin ingin memindahkan pesanan ini ke Project Monitoring?')">
                                        <i class="mdi mdi-swap-horizontal me-1"></i> Move to Project Monitoring
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Document Info Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header pb-2 border-bottom py-3">
                            <h5 class="card-title mb-0 fw-bold text-primary"><i class="mdi mdi-file-document-outline me-2"></i> Informasi Dokumen</h5>
                        </div>
                        <div class="card-body pt-3">
                            @php
                                if ($quotation->type == 'Sparepart') {
                                    $link = 'quotation.show';
                                } elseif ($quotation->type == 'Overhaul') {
                                    $link = 'show-overhaul.quotation';
                                } else {
                                    $link = 'show-service.quotation';
                                }
                            @endphp
                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(105, 108, 255, 0.08); color: #696cff;">
                                    <i class="mdi mdi-file-document-box-outline fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">No Penawaran</small>
                                    <a class="fw-bold text-primary" href="{{ route($link, $quotation->id) }}">
                                        {{ $quotation->no_quote }}
                                    </a>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(113, 221, 55, 0.08); color: #71dd37;">
                                    <i class="mdi mdi-receipt-outline fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">No Invoice</small>
                                    @if (@$invoice->no_invoice)
                                        <a class="fw-bold text-success" href="{{ route('invoice.show', $invoice->id) }}">
                                            {{ $invoice->no_invoice }}
                                        </a>
                                    @else
                                        <span class="text-danger fw-semibold">Belum ada invoice</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(255, 171, 0, 0.08); color: #ffab00;">
                                    <i class="mdi mdi-cart-outline fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">No PO</small>
                                    @if (@$invoice->no_po)
                                        <span class="fw-bold text-dark">{{ $invoice->no_po }}</span>
                                    @else
                                        <span class="text-danger fw-semibold">Belum ada No PO</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3 p-2 rounded hover-light">
                                <div class="avatar avatar-sm me-3" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; border-radius: 8px; background-color: rgba(255, 62, 29, 0.08); color: #ff3e1d;">
                                    <i class="mdi mdi-checkbox-marked-circle-outline fs-5"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Status Pembayaran</small>
                                    @if ($invoice)
                                        <span class="badge {{ $invoice->status_p == 1 ? 'bg-label-success' : 'bg-label-danger' }} fw-semibold">
                                            {{ $invoice->status_p == 1 ? 'Payment Confirmed' : 'Unpaid' }}
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary fw-semibold">No Invoice</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping / Resi Info Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header pb-2 border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-bold text-primary"><i class="mdi mdi-truck-delivery-outline me-2"></i> Informasi Pengiriman</h5>
                            @if ($pending->status != '6' && $pending->status != '8' && $pending->status != '9' && $isInvoiceApproved)
                                <button type="button" class="btn btn-sm btn-icon btn-outline-primary border-0 rounded-circle hover-elevate" data-bs-toggle="modal" data-bs-target="#resiEdit" title="Tambah Resi Pengiriman">
                                    <i class="mdi mdi-plus"></i>
                                </button>
                            @endif
                        </div>
                        <div class="card-body pt-3 d-flex flex-column" style="max-height: 450px; overflow-y: auto;">
                            @php
                                switch ($pending->delivery) {
                                    case 1: $kurir = 'Reftech (Internal)'; break;
                                    case 2: $kurir = 'Sicepat'; break;
                                    case 3: $kurir = 'JNE'; break;
                                    case 4: $kurir = 'Lalamove'; break;
                                    case 5: $kurir = 'Indah Cargo'; break;
                                    case 6: $kurir = 'Deliveree'; break;
                                    case 7: $kurir = 'Gojek / Grab'; break;
                                    case 8: $kurir = 'J&T'; break;
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

                            <!-- Rincian Ongkir & Resi -->
                            <h6 class="fw-bold text-dark mt-2 mb-2 border-bottom pb-1" style="font-size: 13px;">Daftar Resi & Ongkos Kirim:</h6>
                            @forelse ($resis as $r)
                                <div class="mb-3 p-2 rounded bg-label-light border border-light hover-light position-relative">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 10px;">No Tracking / Resi</small>
                                            <span class="fw-bold text-dark fs-7">{{ $r->no_track ?? '-' }}</span>
                                        </div>
                                    </div>
                                    @if ($r->note)
                                        <div class="mt-1">
                                            <small class="text-muted d-block" style="font-size: 10px;">Catatan Kurir</small>
                                            <p class="mb-0 text-muted small" style="line-height: 1.3;">{{ $r->note }}</p>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                                        <span class="fw-bold text-primary" style="font-size: 12px;">Rp {{ number_format($r->cost ?? 0, 0, '.', ',') }}</span>
                                        <div class="d-flex gap-1">
                                            @if ($r->image)
                                                <a href="#" onclick="openPdfViewer('{{ url($r->image) }}', 'Resi {{ $r->no_track }}'); return false;" class="btn btn-xs btn-outline-primary px-2" style="font-size: 10px; padding: 2px 4px;">
                                                    <i class="mdi mdi-image-outline"></i> Resi
                                                </a>
                                            @endif
                                            @if ($pending->status != '6' && $pending->status != '8' && $pending->status != '9')
                                                <a href="#" data-id="{{ $r->id }}" data-pending="{{ $pending->id }}" class="btn btn-xs btn-outline-danger delete-resi px-2" style="font-size: 10px; padding: 2px 4px;">
                                                    <i class="mdi mdi-delete-outline"></i>
                                                </a>
                                            @endif
                                        </div>
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

            <!-- Activity Timeline -->
            @if ($activity->count() >= 1)
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header pb-2 border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-primary"><i class="mdi mdi-clock-outline me-2"></i> Activity Timeline</h5>
                    </div>
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
                                        
                                        <!-- Comments list inside timeline item -->
                                        <div class="ms-3 border-start ps-3 py-1">
                                            @foreach ($stats->comment as $com)
                                                <div class="mb-2 p-2 rounded bg-light hover-light border border-light position-relative">
                                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                                        <span class="fw-bold text-dark" style="font-size: 12px;">{{ $com->user->name }}</span>
                                                        <small class="text-muted" style="font-size: 10px;">{{ $com->date }}</small>
                                                    </div>
                                                    <p class="mb-0 text-muted mt-1" style="font-size: 12px; line-height: 1.4;">{{ $com->comment }}</p>
                                                    @if ($com->id_user == Auth::id())
                                                        <a href="#" data-id="{{ $com->id }}" data-pending="{{ $pending->id }}" class="delete-comment position-absolute text-danger" style="top: 8px; right: 8px;" title="Hapus Komentar">
                                                            <i class="mdi mdi-delete-outline fs-6"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        @php
                                            $lastStat = App\Models\ChangeStatus::where('id_pending', $pending->id)
                                                ->orderByDesc('id')
                                                ->first();
                                        @endphp
                                    </div>
                                </li>
                                @if ($stats->id == $lastStat->id && $pending->status != '6')
                                    <form action="{{ route('pending-po.addComment', $pending->id) }}" method="post" enctype="multipart/form-data" class="mb-4">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInputFilled" placeholder="Comment" name="comment" aria-describedby="floatingInputFilledHelp" required>
                                            <label for="floatingInputFilled">Tulis Komentar / Catatan...</label>
                                            <span class="form-floating-focused"></span>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm waves-effect waves-light float-end mb-3 hover-elevate">Kirim Komentar</button>
                                    </form>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <!-- 2. LOGISTIC TAB -->
        <div class="tab-pane fade" id="logistic-pane" role="tabpanel" aria-labelledby="logistic-tab">
            @if ($pending->type == 'Project')
                @if ($pending->status != '6' && $pending->status != '8')
                    <div class="d-flex justify-content-end mb-3 gap-2">
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                            data-bs-target="#replacementEdit" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                            <i class="mdi mdi-list-status me-1"></i> Update Status Barang
                        </button>
                    </div>
                @endif

                <!-- Items Table (Project) -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="m-0 fw-bold text-primary"><i class="mdi mdi-package-variant-closed me-2"></i> Daftar Barang Proyek</h5>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $abjad = 64; @endphp
                                @foreach ($subQuote as $subJudul)
                                    @php
                                        $no = 0;
                                        $abjad++;
                                    @endphp
                                    <tr class="table-secondary">
                                        <td class="align-middle fw-bold text-center">{{ chr($abjad) }}</td>
                                        <td colspan="5" class="align-middle fw-bold">{{ $subJudul->subtitle }}</td>
                                    </tr>
                                    @foreach ($subJudul->detail as $product)
                                        @php
                                            switch ($product->pending[0]->status) {
                                                case 1: $status = 'On Check'; $badge = 'bg-label-warning'; break;
                                                case 2: $status = 'Ready Stock'; $badge = 'bg-label-info'; break;
                                                case 3: $status = 'Kurang'; $badge = 'bg-label-danger'; break;
                                                case 4: $status = 'Pre-Order'; $badge = 'bg-label-primary'; break;
                                                case 5: $status = 'Delivery Process'; $badge = 'bg-label-linkedin'; break;
                                                case 6: $status = 'Done'; $badge = 'bg-label-success'; break;
                                                default: $status = 'Belum Di Cek'; $badge = 'bg-label-secondary'; break;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="text-center">@php $no++; @endphp {{ $no }}</td>
                                            <td class="fw-semibold text-wrap" style="max-width: 250px;">{{ $product->product }}</td>
                                            <td class="text-wrap" style="max-width: 350px;">
                                                @if ($product->detail != '-')
                                                    <pre class="mb-0 text-muted" style="font-family: inherit; font-size: 13px; white-space: pre-wrap;">{{ $product->detail }}</pre>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $product->qty }} {{ $product->info_qty }}</td>
                                            <td>
                                                <span class="badge {{ $pending->status == '6' ? 'bg-label-success' : $badge }}">
                                                    {{ $pending->status == '6' ? 'Done' : $status }}
                                                </span>
                                            </td>
                                            <td class="text-wrap" style="max-width: 250px;">{{ $product->pending[0]->note ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Purchase Requests -->
                @if ($pending->status != '6' && $pending->status != '8')
                    <div class="d-flex justify-content-end mb-3 mt-3 gap-2">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#purchaseReqPrj" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                            <i class="mdi mdi-plus-box me-1"></i> Purchase Request
                        </button>
                    </div>
                @endif
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="m-0 fw-bold text-primary"><i class="mdi mdi-cart-arrow-down me-2"></i> Purchase Request</h5>
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
                                    <th class="text-center" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse ($purchase as $pr)
                                    @php
                                        switch ($pr->status) {
                                            case 1: $status_pr = 'Sudah di ACC'; $color_pr = 'bg-label-primary'; break;
                                            case 2: $status_pr = 'Dalam Pengiriman'; $color_pr = 'bg-label-warning'; break;
                                            case 3: $status_pr = 'Done'; $color_pr = 'bg-label-success'; break;
                                            default: $status_pr = 'Belum Di ACC'; $color_pr = 'bg-label-secondary'; break;
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $no }}</td>
                                        <td class="fw-bold"><a href="{{ route('purchase-request.show', $pr->id_pending) }}" class="text-primary">{{ $pr->no_pr ?? '-' }}</a></td>
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
                                        <td class="text-center">
                                            <button data-id="{{ $pr->id }}" data-pending="{{ $pending->id }}"
                                                data-qty="{{ $pr->qty }}" data-note="{{ $pr->note }}"
                                                class="btn btn-sm btn-outline-primary edit-request waves-effect me-1">
                                                <i class="mdi mdi-pencil-outline"></i> Edit
                                            </button>
                                            <button data-id="{{ $pr->id }}" data-pending="{{ $pending->id }}"
                                                class="btn btn-sm btn-outline-danger delete-request waves-effect">
                                                <i class="mdi mdi-delete-outline"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    @php $no++; @endphp
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Tidak Ada Purchase Request</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                @if ($pending->status != '6' && $pending->status != '8')
                    <div class="d-flex justify-content-end mb-3 gap-2">
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal"
                            data-bs-target="#productEdit" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                            <i class="mdi mdi-list-status me-1"></i> Update Status Barang
                        </button>
                    </div>
                @endif

                <!-- Items Table (Spare Part / Non-Project) -->
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="m-0 fw-bold text-primary"><i class="mdi mdi-package-variant-closed me-2"></i> Daftar Barang Spare Parts</h5>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>G/R</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($detQuotation as $item)
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
                                    <tr style="font-size: 13px">
                                        <td class="text-center">{{ $no }}</td>
                                        <td class="fw-semibold">
                                            @if ($item->id_equivalent == '0')
                                                -
                                            @else
                                                {{ $item->equivalent->brand ?? '' }} {{ $item->equivalent->pn ?? '' }}
                                            @endif
                                        </td>
                                        <td class="text-wrap" style="max-width: 350px;">
                                            <pre class="mb-0 text-muted" style="font-family: inherit; font-size: 13px; white-space: pre-wrap;">{{ $item->detail_product }}</pre>
                                        </td>
                                        <td>{{ $item->equivalent->product->go ?? '-' }}</td>
                                        <td>{{ $item->qty }} {{ $item->info_qty }}</td>
                                        <td>
                                            <span class="badge {{ $pending->status == '6' ? 'bg-label-success' : $badge }}">
                                                {{ $pending->status == '6' ? 'Done' : $status }}
                                            </span>
                                            
                                            @if ($pending->status != '6' && $pending->status != '8' && $item->id_equivalent != '0')
                                                @php
                                                    $bdgStock = $item->equivalent->product->stock ?? 0;
                                                    $bksStock = $item->equivalent->product->warehouse_stock ?? 0;
                                                    $totalStock = $bdgStock + $bksStock;
                                                    $isEnough = $totalStock >= $item->qty;
                                                @endphp
                                                <div class="mt-1" style="font-size: 10px;">
                                                    @if ($item->status == '1' || is_null($item->status))
                                                        @if ($isEnough)
                                                            <span class="text-success fw-bold d-block" style="font-size: 10px;"><i class="mdi mdi-check-circle-outline"></i> Stock Cukup</span>
                                                        @else
                                                            <span class="text-danger fw-bold d-block" style="font-size: 10px;"><i class="mdi mdi-alert-circle-outline"></i> Stock Kurang</span>
                                                        @endif
                                                    @endif
                                                    <span class="text-muted d-block" style="font-size: 9px; line-height: 1.2;">BDG: {{ $bdgStock }} | BKS: {{ $bksStock }} (Total: {{ $totalStock }})</span>
                                                </div>
                                            @elseif ($item->id_equivalent != '0')
                                                @php
                                                    $bdgStock = $item->equivalent->product->stock ?? 0;
                                                    $bksStock = $item->equivalent->product->warehouse_stock ?? 0;
                                                @endphp
                                                <span class="text-muted d-block mt-1" style="font-size: 9px; line-height: 1.2;">Stock: BDG {{ $bdgStock }} | BKS {{ $bksStock }}</span>
                                            @endif
                                        </td>
                                        <td class="text-wrap" style="max-width: 200px;">{{ $pending->status == '6' ? 'Done' : ($item->note ?? '-') }}</td>
                                    </tr>
                                    @php $no++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Purchase Requests -->
                @if ($pending->status != '6' && $pending->status != '8')
                    <div class="d-flex justify-content-end mb-3 mt-3 gap-2">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#purchaseReq" {{ auth()->user()->role != 'Sales' ? '' : 'disabled' }}>
                            <i class="mdi mdi-plus-box me-1"></i> Purchase Request
                        </button>
                    </div>
                @endif
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="m-0 fw-bold text-primary"><i class="mdi mdi-cart-arrow-down me-2"></i> Purchase Request</h5>
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
                                    <th class="text-center" style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse ($purchase as $pr)
                                    @php
                                        switch ($pr->status) {
                                            case 1: $status_pr = 'Sudah di ACC'; $color_pr = 'bg-label-primary'; break;
                                            case 2: $status_pr = 'Dalam Pengiriman'; $color_pr = 'bg-label-warning'; break;
                                            case 3: $status_pr = 'Done'; $color_pr = 'bg-label-success'; break;
                                            default: $status_pr = 'Belum Di ACC'; $color_pr = 'bg-label-secondary'; break;
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $no }}</td>
                                        <td class="fw-bold"><a href="{{ route('purchase-request.show', $pr->id_pending) }}" class="text-primary">{{ $pr->no_pr ?? '-' }}</a></td>
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
                                        <td class="text-center">
                                            <button data-id="{{ $pr->id }}" data-pending="{{ $pending->id }}"
                                                data-qty="{{ $pr->qty }}" data-note="{{ $pr->note }}"
                                                class="btn btn-sm btn-outline-primary edit-request waves-effect me-1">
                                                <i class="mdi mdi-pencil-outline"></i> Edit
                                            </button>
                                            <button data-id="{{ $pr->id }}" data-pending="{{ $pending->id }}"
                                                class="btn btn-sm btn-outline-danger delete-request waves-effect">
                                                <i class="mdi mdi-delete-outline"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    @php $no++; @endphp
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Tidak Ada Purchase Request</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Returns Section -->
                <div class="card mb-4 shadow-sm border">
                    <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold"><i class="mdi mdi-arrow-u-left-bottom text-primary me-1"></i> Retur Barang</h5>
                        <a href="#" class="btn btn-sm btn-outline-danger clear-return waves-effect" data-id="{{ $pending->id }}">
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
                                @forelse ($return as $retur)
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
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada return di invoice ini</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Finished Product Out Invoice -->
            @if ($pending->status == '6' && $pending->id_product_out != null)
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

                        <!-- Product Out Items Table -->
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

            @if ($pending->status == '6' && $pending->id_product_out != null)
                <div class="card invoice-preview-card mb-4 border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt=""
                                                srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="fw-bold">Barang Keluar ({{ $product->vers }})</h3>
                                <div>
                                    <span
                                        class="fw-bolder">#{{ $product->no_type == '1' ? $product->invoice : $product->po }}</span>
                                </div>
                                <div class="mt-1">
                                    <span class="text-muted">{{ Carbon\Carbon::parse($product->date)->format('d-m-Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0">
                    <div class="card-body mb-3">
                        <div class="row">
                            <div class="col-4 col-lg-2 fw-medium">
                                <p class="mb-1">Customers </p>
                            </div>
                            <div class="col-8 col-lg-10">
                                <pre class="mb-1"
                                    style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $product->detail_client }}</pre>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0">
                    <div class="card-body mb-3">
                        <div class="row">
                            <div class="col-4 col-lg-2 fw-medium">
                                <p class="mb-1">Note</p>
                            </div>
                            <div class="col-8 col-lg-6">
                                <pre
                                    style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: auto; white-space: pre-wrap;">: {{ $product->note }}</pre>
                            </div>
                            <div class="col-4 col-lg-2 fw-medium">
                                <p class="mb-1">User</p>
                            </div>
                            <div class="col-8 col-lg-2">
                                <p class="mb-1">: {{ $product->user->name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table m-0 mb-4">
                            <thead class="table-light border-top">
                                <tr>
                                    <th>No.</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 0;
                                @endphp
                                @foreach ($detProduct as $products)
                                    @php
                                        $no++;
                                    @endphp
                                    <tr style="font-size: 13px">
                                        <td class="align-top">{{ $no }}</td>
                                        <td class="text-nowrap align-top">
                                            <p class="mb-0 fw-semibold" style="font-size: 12px">
                                                {{ $products->detailProduct->replacement }}
                                            </p>
                                            <pre class="mb-0"
                                                style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $products->detailProduct->product->description }}</pre>
                                        </td>
                                        <td class="align-top">{{ $products->qty }}
                                            {{ $products->detailProduct->product->unit }}
                                        </td>
                                        <td class="align-top">RP {{ number_format($products->price, 0, '', '.') }}</td>
                                        <td class="align-top">RP {{ number_format($products->amount, 0, '', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr style="font-size: 13px;">
                                    <td colspan="3" style="border:none;"></td>
                                    <td>Shipping</td>
                                    <td>: RP {{ number_format($product->shipping, 0, '', '.') }}</td>
                                </tr>
                                <tr style="font-size: 13px">
                                    <td colspan="3" style="border:none;"></td>
                                    <td style="border:none;">Total</td>
                                    <td style="border:none;">: RP {{ number_format($product->total, 0, '', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- 3. FINANCE / ACCOUNTING TAB -->
        <div class="tab-pane fade" id="finance-pane" role="tabpanel" aria-labelledby="finance-tab">
            @php
                $totalPrAmount = $purchase->where('status', '3')->sum('amount');
                $totalResiAmount = $resis->sum('cost');
                $totalCost = $totalPrAmount + $totalResiAmount;
                $estimatedProfit = $quotation->nett - $totalCost;
                $profitRatio = $quotation->nett > 0 ? ($estimatedProfit / $quotation->nett) * 100 : 0;
                $costRatio = $quotation->nett > 0 ? ($totalCost / $quotation->nett) * 100 : 0;
            @endphp

            <!-- Financial Summary Card -->
            <div class="card bg-label-primary mb-4 border-0 shadow-none">
                <div class="card-body p-4">
                    <h5 class="card-title text-primary fw-bold mb-4"><i class="mdi mdi-chart-line me-2"></i> Ringkasan Kesehatan Keuangan</h5>
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-white rounded border border-light shadow-sm">
                                <span class="text-muted small d-block mb-1">Total Pendapatan (Revenue)</span>
                                <h4 class="fw-bold text-success mb-0">Rp {{ number_format($quotation->nett, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-white rounded border border-light shadow-sm">
                                <span class="text-muted small d-block mb-1">Total Biaya (COGS)</span>
                                <h4 class="fw-bold text-danger mb-0">Rp {{ number_format($totalCost, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="p-3 bg-white rounded border border-light shadow-sm">
                                <span class="text-muted small d-block mb-1">Estimasi Net Profit</span>
                                <h4 class="fw-bold {{ $estimatedProfit >= 0 ? 'text-primary' : 'text-danger' }} mb-0">
                                    Rp {{ number_format($estimatedProfit, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>
                    <hr class="my-4 opacity-50">
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small fw-semibold">Rasio Pengeluaran vs Margin Keuntungan</span>
                            <span class="badge bg-success-subtle text-success fs-7" style="background-color: rgba(113, 221, 55, 0.15); color: #71dd37; padding: 4px 8px; border-radius: 4px;">{{ number_format($profitRatio, 1) }}% Margin</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 12px; overflow: hidden; background-color: rgba(0,0,0,0.05);">
                            <div class="progress-bar bg-danger animate-bar" role="progressbar" style="width: {{ $costRatio }}%" aria-valuenow="{{ $costRatio }}" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $profitRatio }}%" aria-valuenow="{{ $profitRatio }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2 text-muted small" style="font-size: 11px;">
                            <span>Biaya Operasional &amp; Material: {{ number_format($costRatio, 1) }}%</span>
                            <span>Margin Keuntungan: {{ number_format($profitRatio, 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    {{-- @foreach ($pendingPO as $pending)
        @include('components.modal.pending.detail')
    @endforeach --}}
    @include('components.modal.pending.status')
    @include('components.modal.pending.kurir')
    @include('components.modal.pending.product')
    @include('components.modal.pending.product-out')
    @include('components.modal.pending.project')
    @include('components.modal.pending.return')
    @include('components.modal.pending.resi')
    @include('components.modal.viewer.pdf')
    @if ($pending->type == 'Project')
        @include('components.modal.pending.request-project')
    @else
        @include('components.modal.pending.request')
    @endif

    <!-- Modal Edit Purchase Request -->
    <div class="modal fade" id="editPrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="editPrForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-primary mb-0"><i class="mdi mdi-pencil-outline me-1"></i> Edit Purchase Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold" for="editPrQty">Quantity</label>
                            <input type="number" class="form-control" name="qty" id="editPrQty" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold" for="editPrNote">Catatan / Note</label>
                            <textarea class="form-control" name="note" id="editPrNote" rows="3" placeholder="Masukkan catatan..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Alamat Pengiriman -->
    <div class="modal fade animate__animated" id="editAddresses" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('pending-po.updateAddresses', $pending->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-primary mb-0"><i class="mdi mdi-map-marker-plus me-1"></i> Edit Alamat Pengiriman & Dokumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <!-- Toggle switch: Gabung/Pisah Alamat -->
                        <div class="mb-4 p-3 bg-label-light rounded border">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="fw-bold text-dark d-block mb-0" style="font-size: 13.5px;">Gabungkan Alamat Pengiriman?</label>
                                    <small class="text-muted" style="font-size: 11px;">Kirim dokumen & barang ke alamat yang sama.</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="combine_shipping_and_parts" id="detail_combine_shipping_and_parts" value="1" {{ ($pending->combine_shipping_and_parts ?? true) ? 'checked' : '' }} onchange="toggleAddressLayout('detail')">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- CONTAINER ALAMAT GABUNG -->
                            <div id="detail_combined_address_section" class="col-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Alamat Pengiriman (Dokumen & Barang)</label>
                                    <select class="form-select mb-2" id="detail_combined_address_select" onchange="onAddressSelectChange('detail', 'combined')">
                                        <option value="customer" {{ ($pending->shipping_address_type ?? 'customer') === 'customer' ? 'selected' : '' }}>Main Address: {{ $quotation->pic->client->address }}</option>
                                        @foreach ($quotation->pic->client->plants as $plant)
                                            <option value="{{ $plant->address }}" {{ ($pending->shipping_address_type === 'manual' && $pending->shipping_address_manual === $plant->address) ? 'selected' : '' }}>Plant: {{ $plant->name }} ({{ $plant->address }})</option>
                                        @endforeach
                                        <option value="manual" {{ ($pending->shipping_address_type === 'manual' && !in_array($pending->shipping_address_manual, $quotation->pic->client->plants->pluck('address')->toArray())) ? 'selected' : '' }}>-- Alamat Lain (Isi Manual) --</option>
                                    </select>
                                    
                                    <div id="detail_combined_manual_wrapper" class="d-none mt-2">
                                        <textarea class="form-control" name="shipping_address_manual" id="detail_combined_address_manual" rows="3" placeholder="Masukkan alamat manual lengkap...">{{ $pending->shipping_address_manual }}</textarea>
                                    </div>

                                    <div class="mt-3">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Penerima (Dokumen & Barang)</label>
                                        <select class="form-select" id="detail_combined_recipient_select">
                                            @foreach ($quotation->pic->client->pic as $c_pic)
                                                <option value="{{ $c_pic->id }}" {{ $pending->shipping_recipient_id == $c_pic->id ? 'selected' : '' }}>
                                                    {{ $c_pic->name_pic }} {{ $c_pic->posisi ? '(' . $c_pic->posisi . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- CONTAINER ALAMAT PISAH -->
                            <div id="detail_split_address_section" class="col-12 d-none">
                                <div class="row">
                                    <!-- Alamat Dokumen -->
                                    <div class="col-md-6 pe-md-3 border-end">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Alamat Pengiriman Dokumen / Invoice</label>
                                        <select class="form-select mb-2" id="detail_doc_address_select" onchange="onAddressSelectChange('detail', 'doc')">
                                            <option value="customer" {{ ($pending->doc_address_type ?? 'customer') === 'customer' ? 'selected' : '' }}>Main Address: {{ $quotation->pic->client->address }}</option>
                                            @foreach ($quotation->pic->client->plants as $plant)
                                                <option value="{{ $plant->address }}" {{ ($pending->doc_address_type === 'manual' && $pending->doc_address_manual === $plant->address) ? 'selected' : '' }}>Plant: {{ $plant->name }} ({{ $plant->address }})</option>
                                            @endforeach
                                            <option value="manual" {{ ($pending->doc_address_type === 'manual' && !in_array($pending->doc_address_manual, $quotation->pic->client->plants->pluck('address')->toArray())) ? 'selected' : '' }}>-- Alamat Lain (Isi Manual) --</option>
                                        </select>
                                        
                                        <div id="detail_doc_manual_wrapper" class="d-none mt-2">
                                            <textarea class="form-control" name="doc_address_manual" id="detail_doc_address_manual" rows="3" placeholder="Masukkan alamat dokumen manual...">{{ $pending->doc_address_manual }}</textarea>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Penerima Dokumen / Invoice</label>
                                            <select class="form-select" id="detail_doc_recipient_select">
                                                @foreach ($quotation->pic->client->pic as $c_pic)
                                                    <option value="{{ $c_pic->id }}" {{ $pending->doc_recipient_id == $c_pic->id ? 'selected' : '' }}>
                                                        {{ $c_pic->name_pic }} {{ $c_pic->posisi ? '(' . $c_pic->posisi . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Alamat Barang -->
                                    <div class="col-md-6 ps-md-3">
                                        <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Alamat Pengiriman Barang</label>
                                        <select class="form-select mb-2" id="detail_shipping_address_select" onchange="onAddressSelectChange('detail', 'shipping')">
                                            <option value="customer" {{ ($pending->shipping_address_type ?? 'customer') === 'customer' ? 'selected' : '' }}>Main Address: {{ $quotation->pic->client->address }}</option>
                                            @foreach ($quotation->pic->client->plants as $plant)
                                                <option value="{{ $plant->address }}" {{ ($pending->shipping_address_type === 'manual' && $pending->shipping_address_manual === $plant->address) ? 'selected' : '' }}>Plant: {{ $plant->name }} ({{ $plant->address }})</option>
                                            @endforeach
                                            <option value="manual" {{ ($pending->shipping_address_type === 'manual' && !in_array($pending->shipping_address_manual, $quotation->pic->client->plants->pluck('address')->toArray())) ? 'selected' : '' }}>-- Alamat Lain (Isi Manual) --</option>
                                        </select>
                                        
                                        <div id="detail_shipping_manual_wrapper" class="d-none mt-2">
                                            <textarea class="form-control" name="shipping_address_manual" id="detail_shipping_address_manual" rows="3" placeholder="Masukkan alamat barang manual...">{{ $pending->shipping_address_manual }}</textarea>
                                        </div>

                                        <div class="mt-3">
                                            <label class="form-label fw-bold text-dark mb-1" style="font-size: 12.5px;">Penerima Barang</label>
                                            <select class="form-select" id="detail_shipping_recipient_select">
                                                @foreach ($quotation->pic->client->pic as $c_pic)
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
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/tagify/tagify.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bloodhound/bloodhound.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pending-non-project-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pending.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        function onAddressSelectChange(prefix, type) {
            var selectEl = document.getElementById(prefix + '_' + type + '_address_select');
            var wrapperEl = document.getElementById(prefix + '_' + type + '_manual_wrapper');
            
            if (selectEl && selectEl.value === 'manual') {
                if (wrapperEl) wrapperEl.classList.remove('d-none');
            } else {
                if (wrapperEl) wrapperEl.classList.add('d-none');
            }
        }

        function toggleAddressLayout(prefix) {
            var combineCheckbox = document.getElementById(prefix + '_combine_shipping_and_parts');
            var combinedSection = document.getElementById(prefix + '_combined_address_section');
            var splitSection = document.getElementById(prefix + '_split_address_section');
            
            var combinedSelect = document.getElementById(prefix + '_combined_address_select');
            var combinedManual = document.getElementById(prefix + '_combined_address_manual');
            var combinedRecipient = document.getElementById(prefix + '_combined_recipient_select');
            
            var docSelect = document.getElementById(prefix + '_doc_address_select');
            var docManual = document.getElementById(prefix + '_doc_address_manual');
            var docRecipient = document.getElementById(prefix + '_doc_recipient_select');
            
            var shippingSelect = document.getElementById(prefix + '_shipping_address_select');
            var shippingManual = document.getElementById(prefix + '_shipping_address_manual');
            var shippingRecipient = document.getElementById(prefix + '_shipping_recipient_select');

            if (combineCheckbox && combineCheckbox.checked) {
                // Show Combined, Hide Split
                if (combinedSection) combinedSection.classList.remove('d-none');
                if (splitSection) splitSection.classList.add('d-none');
                
                // Enable Combined inputs, Disable Split inputs
                if (combinedSelect) {
                    combinedSelect.removeAttribute('disabled');
                    combinedSelect.setAttribute('name', 'shipping_address_type');
                }
                if (combinedManual) {
                    combinedManual.removeAttribute('disabled');
                    combinedManual.setAttribute('name', 'shipping_address_manual');
                }
                if (combinedRecipient) {
                    combinedRecipient.removeAttribute('disabled');
                    combinedRecipient.setAttribute('name', 'shipping_recipient_id');
                }
                
                if (docSelect) {
                    docSelect.setAttribute('disabled', 'disabled');
                    docSelect.removeAttribute('name');
                }
                if (docManual) {
                    docManual.setAttribute('disabled', 'disabled');
                    docManual.removeAttribute('name');
                }
                if (docRecipient) {
                    docRecipient.setAttribute('disabled', 'disabled');
                    docRecipient.removeAttribute('name');
                }
                if (shippingSelect) {
                    shippingSelect.setAttribute('disabled', 'disabled');
                    shippingSelect.removeAttribute('name');
                }
                if (shippingManual) {
                    shippingManual.setAttribute('disabled', 'disabled');
                    shippingManual.removeAttribute('name');
                }
                if (shippingRecipient) {
                    shippingRecipient.setAttribute('disabled', 'disabled');
                    shippingRecipient.removeAttribute('name');
                }
            } else {
                // Show Split, Hide Combined
                if (combinedSection) combinedSection.classList.add('d-none');
                if (splitSection) splitSection.classList.remove('d-none');
                
                // Enable Split inputs, Disable Combined inputs
                if (combinedSelect) {
                    combinedSelect.setAttribute('disabled', 'disabled');
                    combinedSelect.removeAttribute('name');
                }
                if (combinedManual) {
                    combinedManual.setAttribute('disabled', 'disabled');
                    combinedManual.removeAttribute('name');
                }
                if (combinedRecipient) {
                    combinedRecipient.setAttribute('disabled', 'disabled');
                    combinedRecipient.removeAttribute('name');
                }
                
                if (docSelect) {
                    docSelect.removeAttribute('disabled');
                    docSelect.setAttribute('name', 'doc_address_type');
                }
                if (docManual) {
                    docManual.removeAttribute('disabled');
                    docManual.setAttribute('name', 'doc_address_manual');
                }
                if (docRecipient) {
                    docRecipient.removeAttribute('disabled');
                    docRecipient.setAttribute('name', 'doc_recipient_id');
                }
                if (shippingSelect) {
                    shippingSelect.removeAttribute('disabled');
                    shippingSelect.setAttribute('name', 'shipping_address_type');
                }
                if (shippingManual) {
                    shippingManual.removeAttribute('disabled');
                    shippingManual.setAttribute('name', 'shipping_address_manual');
                }
                if (shippingRecipient) {
                    shippingRecipient.removeAttribute('disabled');
                    shippingRecipient.setAttribute('name', 'shipping_recipient_id');
                }
            }
            onAddressSelectChange(prefix, 'combined');
            onAddressSelectChange(prefix, 'doc');
            onAddressSelectChange(prefix, 'shipping');
        }

        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
            $('.select-project').select2({
                dropdownParent: $('#purchaseReqPrj')
            });
            $('#editAddresses').on('shown.bs.modal', function () {
                toggleAddressLayout('detail');
            });
            toggleAddressLayout('detail');
        });

        $(document).on('click', '.button-clear', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Clear it??",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Cleared it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('notulen') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'PATCH',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Cleared!",
                                    text: "This Notulen has been Cleared.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/notulen';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Cleared!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-resi', function() {
            var id = $(this).data('id');
            var pending = $(this).data('pending');
            Swal.fire({
                title: "Are you sure to Delete it??",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Deleted it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('pending-po') }}/delete-resi/' + id,
                        'type': 'DELETE',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "This Notulen has been Deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/pending-po/' + pending;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Deleted!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });

        $(document).on('click', '.delete-request', function() {
            var id = $(this).data('id');
            var pending = $(this).data('pending');
            Swal.fire({
                title: "Are you sure to Delete it??",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Deleted it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('purchase-request') }}/delete/' + id,
                        'type': 'DELETE',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "This Notulen has been Deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/pending-po/' + pending;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Deleted!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        
        $(document).on('click', '.edit-request', function() {
            var id = $(this).data('id');
            var qty = $(this).data('qty');
            var note = $(this).data('note');
            
            var $form = $('#editPrForm');
            $form.attr('action', '{{ url('purchase-request') }}/update/' + id);
            $('#editPrQty', $form).val(qty);
            $('#editPrNote', $form).val(note);
            
            $('#editPrModal').modal('show');
        });
        $(document).on('click', '.clear-return', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Accept it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('pending-po') }}/clear-return/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Accepted!",
                                    text: "Your file has been Accepted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Accept!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.done-po', function() {
            var id = $(this).data('id');
            var pending = $(this).data('pending');
            Swal.fire({
                title: "Are you sure to Done it??",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Done it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('pending-po') }}/done/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Done!",
                                    text: "This Notulen has been Done.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/pending-po/product-out-project/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Done!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
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

@push('after-style')
    <style>
        .hover-elevate {
            transition: all 0.25s ease;
        }
        .hover-elevate:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
        }
        .btn-white {
            background-color: #ffffff !important;
            color: #696cff !important;
        }
        .btn-white:hover {
            background-color: #f1f1f1 !important;
            color: #4f52b3 !important;
        }
    </style>
@endpush
