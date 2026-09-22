@extends('layouts.sales.app')
@section('title', 'Purchase Request')
@section('content')
    <style>
        .purchase-request-page {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .purchase-request-page .table,
        .purchase-request-page .table th,
        .purchase-request-page .table td,
        .purchase-request-page .card-title,
        .purchase-request-page .meta-label,
        .purchase-request-page .meta-value {
            font-family: inherit;
        }

        .purchase-request-page .card,
        .purchase-request-page .modern-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.06), 0 0 1px 0 rgba(67, 89, 113, 0.12);
            border-radius: 0.75rem !important;
        }
    </style>

    <div class="container-fluid flex-grow-1 container-p-y p-0 purchase-request-page">
        {{-- Header Page Title --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Detail Purchase Request</h4>
                <p class="text-muted mb-0 small">Kelola, setujui, dan pantau status pengajuan pembelian barang</p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="badge bg-label-primary fs-6 px-3 py-2">
                    <i class="mdi mdi-receipt-text-outline me-1"></i>SO: {{ $pending->no_pending ?? ($purchase->no_pr ?? '-') }}
                </span>
                @if ($purchase)
                    <span class="badge bg-label-secondary fs-6 px-3 py-2">
                        PR: {{ $purchase->no_pr ?? '-' }}
                    </span>
                    @if ($purchase->purchaseOrders && $purchase->purchaseOrders->count())
                        @foreach ($purchase->purchaseOrders as $linkedPo)
                            <a href="{{ route('purchase.show', $linkedPo->id) }}" class="badge {{ $linkedPo->is_direct_purchase ? 'bg-label-info' : 'bg-label-success' }} fs-6 px-3 py-2 text-decoration-none" title="Lihat {{ $linkedPo->is_direct_purchase ? 'Direct Purchase' : 'Purchase Order' }} {{ $linkedPo->no_po }}">
                                <i class="mdi {{ $linkedPo->is_direct_purchase ? 'mdi-cart-arrow-down' : 'mdi-file-document-outline' }} me-1"></i>{{ $linkedPo->is_direct_purchase ? 'DP: ' : 'PO: ' }}{{ $linkedPo->no_po }}
                            </a>
                        @endforeach
                    @endif
                @endif
            </div>
        </div>

        {{-- Baris 1: Informasi Pihak Terkait & Dokumen (Kiri) + Tindakan PR (Kanan) --}}
        <div class="row mb-4">
            {{-- Card Metadata: Informasi Pihak Terkait & Dokumen --}}
            <div class="col-xl-9 col-lg-8 col-12 mb-lg-0 mb-4">
                <div class="card modern-card h-100 mb-0">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-account-box-outline me-2 text-primary fs-4"></i> Informasi Pihak Terkait & Dokumen
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Column 1: Client & Sales Info -->
                            <div class="col-md-6 border-end-md">
                                <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                    Pihak Terkait
                                </h6>
                                <table class="table table-borderless table-sm mb-0">
                                    @if ($quotation)
                                        <tr>
                                            <td class="fw-semibold text-muted ps-0" style="width: 140px;">Sales</td>
                                            <td>: <span class="fw-medium text-dark">{{ $quotation->sales->name ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted ps-0">Flag / Info</td>
                                            <td>: <span class="fw-medium text-dark">{{ $quotation->pic->client->info ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted ps-0">Perusahaan / Client</td>
                                            <td>: <span class="fw-bold text-primary">{{ $quotation->pic->client->company ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted ps-0">PIC Client</td>
                                            <td>: <span class="fw-medium text-dark">{{ $quotation->pic->name_pic ?? '-' }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted ps-0">Alamat Kirim</td>
                                            <td>: <span class="fw-medium text-dark text-wrap">{{ $quotation->pic->client->address ?? '-' }}</span></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="fw-semibold text-muted ps-0" style="width: 140px;">Pembuat PR</td>
                                            <td>: <span class="fw-medium text-dark">{{ $purchase->user->name ?? ($pending->user->name ?? Auth::user()->name) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted ps-0">Jenis Pengadaan</td>
                                            <td>: <span class="badge bg-label-info">Pengadaan Manual / Internal</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted ps-0">Keperluan / Judul</td>
                                            <td>: <span class="fw-bold text-primary">{{ $pending?->title ?: ($purchase?->title ?: 'Pengadaan Internal') }}</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted ps-0">Tujuan</td>
                                            <td>: <span class="fw-medium text-dark">Gudang / Workshop Reftech</span></td>
                                        </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Column 2: Document Info -->
                            <div class="col-md-6">
                                <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                    Dokumen Terkait
                                </h6>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0" style="width: 140px;">No Quotation</td>
                                        <td>:
                                            @if ($quotation)
                                                @php
                                                    if ($isUnitQuotation) {
                                                        $link = 'unit-quotation.show';
                                                    } elseif ($quotation->type == 'Sparepart') {
                                                        $link = 'quotation.show';
                                                    } elseif ($quotation->type == 'Overhaul') {
                                                        $link = 'show-overhaul.quotation';
                                                    } else {
                                                        $link = 'show-service.quotation';
                                                    }
                                                @endphp
                                                <a class="text-primary fw-bold" href="{{ route($link, $quotation->id) }}">
                                                    {{ $quotation->no_quote }}
                                                </a>
                                            @else
                                                <span class="text-muted fst-italic">- (Manual Non-Quotation)</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">No Invoice</td>
                                        <td>:
                                            @if (@$invoice->no_invoice)
                                                <a class="text-primary fw-bold" href="{{ route('invoice.show', $invoice->id) }}">
                                                    {{ $invoice->no_invoice }}
                                                </a>
                                            @else
                                                <span class="text-muted fst-italic">Belum ada invoice</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">No Sales Order</td>
                                        <td>:
                                            @if ($pending)
                                                <a class="text-primary fw-bold" href="{{ route('pending-po.show', $pending->id) }}">
                                                    {{ $pending->no_pending }}
                                                </a>
                                            @else
                                                <span class="text-muted fst-italic">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Purchase Order</td>
                                        <td>:
                                            @if ($purchase && $purchase->purchaseOrders && $purchase->purchaseOrders->count())
                                                <div class="d-inline-flex flex-wrap align-items-center gap-1">
                                                    @foreach ($purchase->purchaseOrders as $linkedPo)
                                                        <a class="badge bg-label-primary text-decoration-none fw-semibold d-inline-flex align-items-center gap-1" href="{{ route('purchase.show', $linkedPo->id) }}" title="Buka Detail PO">
                                                            <i class="mdi mdi-file-document-outline"></i>{{ $linkedPo->no_po }}
                                                            <i class="mdi mdi-open-in-new font-11"></i>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @elseif ($purchase && $purchase->status == 1)
                                                <span class="text-muted fst-italic me-2">Belum terhubung</span>
                                                <button type="button" class="btn btn-xs btn-outline-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalLinkPo">
                                                    <i class="mdi mdi-link-variant"></i> Hubungkan ke PO
                                                </button>
                                            @else
                                                <span class="text-muted fst-italic">Belum ada PO</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Payment Status</td>
                                        <td>:
                                            @php
                                                $paymentInfo = null;
                                                $paymentRecord = $quotation ? \App\Models\Payment::where($isUnitQuotation ? 'id_unit_quotation' : 'id_quotation', $quotation->id)->orderByDesc('id')->first() : null;
                                                if ($paymentRecord) {
                                                    if ($paymentRecord->type === 'Tempo') {
                                                        $days = $paymentRecord->tempo ?: preg_replace('/[^0-9]/', '', (string)$paymentRecord->note);
                                                        $paymentInfo = ['label' => $days ? 'Credit (' . $days . ' Days)' : 'Credit', 'class' => 'bg-label-info'];
                                                    } elseif ($paymentRecord->date_confirm || $paymentRecord->level == 1) {
                                                        $paymentInfo = ['label' => 'Payment Confirmed', 'class' => 'bg-label-success'];
                                                    } else {
                                                        $paymentInfo = ['label' => 'Unconfirmed', 'class' => 'bg-label-warning'];
                                                    }
                                                } elseif ($isUnitQuotation && ($quotation->payment_method || $quotation->payment || (@$invoice && $invoice->type == 'CT'))) {
                                                    $rawTempo = $quotation->payment_method ?: ($quotation->payment ?: @$invoice->term);
                                                    $days = preg_replace('/[^0-9]/', '', (string)$rawTempo);
                                                    $paymentInfo = ['label' => $days ? 'Credit (' . $days . ' Days)' : 'Credit', 'class' => 'bg-label-info'];
                                                } elseif (@$invoice && $invoice->type == 'CT') {
                                                    $days = preg_replace('/[^0-9]/', '', (string)$invoice->term);
                                                    $paymentInfo = ['label' => $days ? 'Credit (' . $days . ' Days)' : 'Credit', 'class' => 'bg-label-info'];
                                                } elseif (@$invoice && $invoice->status_p == 1) {
                                                    $paymentInfo = ['label' => 'Payment Confirmed', 'class' => 'bg-label-success'];
                                                }
                                            @endphp
                                            @if ($paymentInfo)
                                                <span class="badge {{ $paymentInfo['class'] }} fw-semibold">{{ $paymentInfo['label'] }}</span>
                                            @elseif ($invoice)
                                                <span class="badge {{ $invoice->status_p == 1 ? 'bg-label-success' : 'bg-label-danger' }} fw-semibold">
                                                    {{ $invoice->status_p == 1 ? 'Payment Confirmed' : 'Unpaid' }}
                                                </span>
                                            @else
                                                <span class="badge bg-label-secondary">No Invoice</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">PO Date</td>
                                        <td>: <span class="fw-medium text-dark"><i class="mdi mdi-calendar me-1 text-muted"></i>{{ \Carbon\Carbon::parse($quotation->po_date)->format('d-m-Y') }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Actions: Tindakan PR --}}
            <div class="col-xl-3 col-lg-4 col-12 invoice-actions">
                <div class="card modern-card h-100 mb-0">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-cog-outline me-2 text-primary fs-5"></i> Tindakan PR
                        </h5>
                    </div>
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            @if ($purchase && $purchase->status == 0)
                                <a href="#" class="btn btn-primary d-flex align-items-center justify-content-center w-100 mb-2 waves-effect acc-purchase"
                                    data-id="{{ $purchase->id }}">
                                    <i class="mdi mdi-check-all me-2 fs-5"></i> Approve PR
                                </a>
                                <a href="#" class="btn btn-outline-danger d-flex align-items-center justify-content-center w-100 mb-2 waves-effect reject-purchase"
                                    data-id="{{ $purchase->id }}">
                                    <i class="mdi mdi-close-circle-outline me-2 fs-5"></i> Reject PR
                                </a>
                            @elseif ($purchase && $purchase->status == 4)
                                <div class="alert alert-danger py-2 px-3 mb-2 small">
                                    <div class="fw-bold mb-1"><i class="mdi mdi-close-circle-outline me-1"></i>PR Ditolak</div>
                                    <div>{{ $purchase->rejected_reason }}</div>
                                    <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                        oleh {{ $purchase->rejector->name ?? '-' }} · {{ \Carbon\Carbon::parse($purchase->rejected_at)->diffForHumans() }}
                                    </div>
                                </div>
                            @elseif ($purchase && $purchase->status == 1)
                                <div class="alert alert-warning py-2 px-3 mb-2 small">
                                    <div class="fw-bold mb-1"><i class="mdi mdi-check-decagram-outline me-1 text-success"></i>PR Telah Disetujui</div>
                                    Pilih opsi pengadaan barang di bawah atau centang item pada tabel di samping.
                                </div>
                                <a href="{{ route('purchase.create', ['from_pr' => $purchase->id]) }}" class="btn btn-primary d-flex align-items-center justify-content-center w-100 mb-2 waves-effect shadow-xs">
                                    <i class="mdi mdi-file-document-edit-outline me-2 fs-5"></i> + Buat Purchase Order (PO)
                                </a>
                                <a href="{{ route('purchase.direct-create', ['from_pr' => $purchase->id]) }}" class="btn btn-primary d-flex align-items-center justify-content-center w-100 mb-2 waves-effect shadow-xs" style="background-color: #0d9488; border-color: #0d9488;">
                                    <i class="mdi mdi-cart-arrow-down me-2 fs-5"></i> + Direct Purchase
                                </a>
                                <button type="button" class="btn btn-outline-primary d-flex align-items-center justify-content-center w-100 mb-2 waves-effect shadow-xs" data-bs-toggle="modal" data-bs-target="#modalLinkPo">
                                    <i class="mdi mdi-link-variant me-2 fs-5"></i> Hubungkan ke PO
                                </button>
                                @if(Auth::check() && in_array(Auth::user()->role, ['Developer', 'Admin', 'Super Admin', 'Logistic']))
                                    <button type="button" class="btn btn-outline-warning d-flex align-items-center justify-content-center w-100 mb-2 waves-effect shadow-xs btn-rollback-approved-to-new" data-id="{{ $purchase->id }}" data-no-pr="{{ $purchase->no_pr }}">
                                        <i class="mdi mdi-undo-variant me-2 fs-5"></i> Rollback ke New PR
                                    </button>
                                @endif
                            @endif

                            @php
                                // Status pengiriman & GR dihitung per-PO, LEPAS dari status level-PR —
                                // satu PR bisa pecah ke beberapa PO yang datang & diverifikasi di waktu
                                // berbeda-beda. Info pengiriman diisi per-alokasi lewat modal "Update
                                // Delivery Info" (nulis ke purchase_request_detail_allocation), jadi
                                // dicek di level alokasi, bukan level PR/detail.
                                $poDeliveryStatus = [];
                                if ($purchase) {
                                    foreach ($purchase->purchaseOrders as $po) {
                                        $allocationsForThisPo = $purchase->details
                                            ->flatMap(fn ($d) => $d->allocations)
                                            ->where('id_purchase_order', $po->id);
                                        $poDeliveryStatus[$po->id] = $allocationsForThisPo->isNotEmpty()
                                            && $allocationsForThisPo->every(fn ($a) => !is_null($a->purchase_type));
                                    }
                                }
                            @endphp
                            @if ($purchase && $purchase->purchaseOrders->count())
                                <div class="text-muted small fw-bold mb-1 mt-2 d-flex align-items-center justify-content-between">
                                    <span><i class="mdi mdi-link-variant me-1 text-primary"></i>PO Terkait ({{ $purchase->purchaseOrders->count() }}):</span>
                                </div>
                                @foreach ($purchase->purchaseOrders as $po)
                                    @php $isOnDelivery = $poDeliveryStatus[$po->id] ?? false; @endphp
                                    <div class="border rounded p-2 mb-2 bg-light-subtle">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <a href="{{ route('purchase.show', $po->id) }}" class="small text-primary fw-semibold d-inline-flex align-items-center" title="Buka Detail PO">
                                                <i class="mdi mdi-file-document-outline me-1"></i>{{ $po->no_po }}
                                                <i class="mdi mdi-open-in-new ms-1 font-11"></i>
                                            </a>
                                            @if ($po->receipt_status == 'Received')
                                                <span class="badge bg-label-success font-11">Diterima</span>
                                            @elseif ($isOnDelivery)
                                                <span class="badge bg-label-info font-11">Sedang Dikirim</span>
                                            @else
                                                <span class="badge bg-label-warning font-11">Menunggu Kirim</span>
                                            @endif
                                        </div>

                                        <a href="{{ route('purchase.show', $po->id) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center w-100 mb-1">
                                            <i class="mdi mdi-eye-outline me-1"></i> Buka Purchase Order
                                        </a>

                                        @if(Auth::user()->role == 'Logistic' || (Auth::user()->isDeveloper() ?? false))
                                            <a class="btn btn-primary btn-sm d-flex align-items-center justify-content-center w-100 waves-effect {{ ($isOnDelivery && $po->receipt_status != 'Received') ? '' : 'disabled' }}"
                                                href="{{ ($isOnDelivery && $po->receipt_status != 'Received') ? route('purchase.goods-receipt', $po->id) : '#' }}"
                                                tabindex="{{ ($isOnDelivery && $po->receipt_status != 'Received') ? '0' : '-1' }}" aria-disabled="{{ ($isOnDelivery && $po->receipt_status != 'Received') ? 'false' : 'true' }}">
                                                <i class="mdi mdi-checkbox-marked-circle-outline me-1 fs-6"></i> Verifikasi Penerimaan (GR)
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center w-100 waves-effect mt-2" id="backButton">
                            <i class="mdi mdi-arrow-left me-2 fs-5"></i> Kembali
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Baris 2: Card Daftar Item PR (FULL WIDTH) --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card modern-card mb-0">
                    @php
                        $canCreatePo = ($purchase && (int) $purchase->status >= 1);
                        $canEditPrQty = in_array(Auth::user()->role, ['Logistic', 'Admin']);
                        $prColspan = 7 + ($canCreatePo ? 1 : 0) + ($canEditPrQty ? 1 : 0);
                    @endphp
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                                <i class="mdi mdi-clipboard-text-outline me-2 text-primary fs-4"></i> Daftar Item Purchase Request
                                @if ($purchase)
                                    <span class="badge bg-label-primary ms-2">{{ $purchase->no_pr ?? '-' }}</span>
                                @endif
                            </h5>
                            @if ($canCreatePo)
                                <span class="badge bg-label-info font-11 d-none d-sm-inline-flex" id="selectedPrItemsBadge">
                                    <span id="countSelectedPrItems">0</span> item dipilih
                                </span>
                            @endif
                        </div>
                        @if ($canCreatePo)
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-primary shadow-xs" id="btnCreatePoFromSelected">
                                    <i class="mdi mdi-file-document-edit-outline me-1"></i> + Buat PO
                                </button>
                                <button type="button" class="btn btn-sm btn-success shadow-xs" id="btnCreateDirectFromSelected" style="background-color: #0d9488; border-color: #0d9488;">
                                    <i class="mdi mdi-cart-arrow-down me-1"></i> + Direct Purchase
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered align-middle mb-0" id="prItemsTable">
                                <thead>
                                    <tr>
                                        @if ($canCreatePo)
                                            <th style="width: 40px;" class="text-center">
                                                <input type="checkbox" class="form-check-input" id="checkAllPrItems" title="Pilih Semua Item">
                                            </th>
                                        @endif
                                        <th style="width: 45px;" class="text-center">No</th>
                                        <th style="width: 140px;">No PR</th>
                                        <th>Nama Item / Deskripsi</th>
                                        <th>Equivalent</th>
                                        <th>Pembelian Terakhir</th>
                                        <th class="text-center" style="width: 160px;">Qty &amp; Alokasi</th>
                                        <th>Catatan / Note</th>
                                        @if ($canEditPrQty)
                                            <th class="text-center" style="width: 50px;"></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @forelse (($purchase->details ?? collect()) as $pr)
                                        @php $remaining = $pr->remainingQty; @endphp
                                        <tr>
                                            @if ($canCreatePo)
                                                <td class="text-center">
                                                    @if ($remaining > 0)
                                                        <input type="checkbox" class="form-check-input check-pr-item" value="{{ $pr->id }}" data-remaining="{{ $remaining }}" checked>
                                                    @else
                                                        <span class="badge bg-label-success p-1" data-bs-toggle="tooltip" title="Sudah teralokasi penuh ke PO"><i class="mdi mdi-check font-12"></i></span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="text-center fw-medium">{{ $no }}</td>
                                            <td class="fw-bold text-dark">{{ $purchase->no_pr ?? '-' }}</td>
                                            <td style="max-width: 250px; white-space: normal;">
                                                <div class="fw-semibold text-dark">
                                                    {{ $pr->equivalent->product->description ?? ($pr->equivalent->pn ?? '-') }}
                                                </div>
                                                @if ($pr->equivalent->product && $pr->equivalent->product->commodity)
                                                    <div class="text-muted font-11 mt-1">
                                                        <i class="mdi mdi-tag-outline me-1"></i>{{ $pr->equivalent->product->commodity }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($pr->id_equivalent == '0' || !$pr->equivalent)
                                                    <span class="text-muted">-</span>
                                                @else
                                                    @php
                                                        $detPrice = $detQuotation->firstWhere('id_equivalent', $pr->id_equivalent);
                                                    @endphp
                                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                                        <span class="fw-bold text-dark">{{ $pr->equivalent->brand }} {{ $pr->equivalent->pn }}</span>
                                                        @if ($pr->equivalent->product && $pr->equivalent->product->go)
                                                            <span class="badge {{ $pr->equivalent->product->go == 'Genuine' ? 'bg-label-success' : 'bg-label-warning' }} font-10">
                                                                {{ $pr->equivalent->product->go }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-label-success font-11" title="Harga Jual Penawaran">
                                                            <i class="mdi mdi-cash-multiple me-1"></i>{{ $detPrice ? 'Rp ' . number_format($detPrice->price, 0, '', '.') : '-' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $pId = $pr->equivalent->id_product ?? null;
                                                    $hist = ($pId && isset($lastPurchaseHistory[$pId])) ? $lastPurchaseHistory[$pId] : null;
                                                @endphp
                                                @if ($hist && $hist['has_history'] && $hist['price'] > 0)
                                                    <div class="d-flex flex-column gap-1">
                                                        <div class="d-flex align-items-center gap-1">
                                                            @if ($hist['purchase_type'] == 'Impor')
                                                                <span class="badge bg-label-danger font-11"><i class="mdi mdi-airplane-landing me-1"></i>Impor</span>
                                                            @else
                                                                <span class="badge bg-label-primary font-11"><i class="mdi mdi-store-outline me-1"></i>Lokal</span>
                                                            @endif
                                                            <span class="fw-bold text-dark font-12">
                                                                @if (Auth::user()->role == 'Admin')
                                                                    Rp {{ number_format($hist['price'], 0, '', '.') }}
                                                                @else
                                                                    Rp ***
                                                                @endif
                                                            </span>
                                                        </div>
                                                        @if ($hist['supplier_name'])
                                                            <div class="text-muted font-11 text-truncate" style="max-width: 190px;" title="{{ $hist['supplier_name'] }}">
                                                                <i class="mdi mdi-domain me-1"></i>{{ $hist['supplier_name'] }}
                                                            </div>
                                                        @endif
                                                        @if ($hist['purchase_date'])
                                                            <div class="text-muted font-10">
                                                                <i class="mdi mdi-calendar-blank-outline me-1"></i>{{ \Carbon\Carbon::parse($hist['purchase_date'])->format('d M Y') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted font-11"><em>Belum ada riwayat</em></span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-dark fs-6">{{ $pr->totalQty }} {{ $pr->equivalent->product->unit ?? '' }}</span>
                                                @if ($remaining > 0)
                                                    <div>
                                                        <span class="badge bg-label-warning font-11" data-bs-toggle="tooltip" title="Sisa kebutuhan belum terbit PO: {{ $remaining }} {{ $pr->equivalent->product->unit ?? '' }}">
                                                            Sisa belum PO: {{ $remaining }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <div>
                                                        <span class="badge bg-label-success font-11">
                                                            <i class="mdi mdi-check-circle me-1"></i>Semua sudah PO
                                                        </span>
                                                    </div>
                                                @endif
                                                @if ($pr->qty_stock > 0)
                                                    <div>
                                                        <span class="badge bg-label-info font-10" data-bs-toggle="tooltip" title="Kebutuhan SO: {{ $pr->qty }}, tambahan stok: {{ $pr->qty_stock }}">
                                                            {{ $pr->qty }} SO + {{ $pr->qty_stock }} stok
                                                        </span>
                                                    </div>
                                                @endif
                                                @if ($pr->allocations->count())
                                                    <div class="mt-1 d-flex flex-column gap-1 align-items-center">
                                                        @foreach ($pr->allocations as $alloc)
                                                            <a href="{{ route('purchase.show', $alloc->id_purchase_order) }}"
                                                                class="badge bg-label-dark text-decoration-none font-11" data-bs-toggle="tooltip"
                                                                title="{{ $alloc->purchaseOrder->no_po ?? '-' }}">
                                                                {{ $alloc->qty }} pcs → {{ $alloc->purchaseOrder->no_po ?? '-' }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td style="max-width: 220px; white-space: normal;">
                                                @if ($pr->note && $pr->note != '-')
                                                    <div class="p-2 rounded bg-light border-start border-primary border-3 small text-secondary">
                                                        <i class="mdi mdi-comment-text-outline me-1 text-muted"></i>{{ $pr->note }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            @if ($canEditPrQty)
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-icon btn-outline-primary btn-sm edit-purchase-item"
                                                        data-id="{{ $pr->id }}" data-qty="{{ $pr->qty }}" data-qty-stock="{{ $pr->qty_stock }}"
                                                        data-note="{{ $pr->note }}" title="Edit Qty Purchase Request">
                                                        <i class="mdi mdi-pencil-outline"></i>
                                                    </button>
                                                </td>
                                            @endif
                                        </tr>
                                        @php $no++; @endphp
                                    @empty
                                        <tr>
                                            <td colspan="{{ $prColspan }}" class="text-center text-muted py-4">Tidak Ada Purchase Request</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Baris 2b: Card Purchase Order Terkait (FULL WIDTH) --}}
        @if ($purchase && $purchase->purchaseOrders->count())
            @php
                // Kelompokkan alokasi per PO supaya info kirim/GR-nya cukup ditampilkan
                // sekali per PO, bukan diulang di tiap baris item PR.
                // Qty alokasi (alloc->qty) di-clamp ke kebutuhan PR (lihat POController::store),
                // jadi bisa lebih kecil dari qty asli di PO kalau Logistic sengaja beli lebih
                // banyak buat nambah stok — cari qty PO aslinya lewat DetailPurchaseOrder yang
                // match id_product, biar kelebihannya kelihatan di kolom Item.
                $allocsByPo = [];
                foreach ($purchase->details as $d) {
                    foreach ($d->allocations as $alloc) {
                        $po = $purchase->purchaseOrders->firstWhere('id', $alloc->id_purchase_order);
                        $idProduct = $d->equivalent->id_product ?? null;
                        $poDetail = $idProduct ? $po?->detail->firstWhere('id_product', $idProduct) : null;
                        $allocsByPo[$alloc->id_purchase_order][] = [
                            'detail' => $d,
                            'alloc' => $alloc,
                            'po_qty' => $poDetail->qty ?? $alloc->qty,
                        ];
                    }
                }
            @endphp
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card modern-card mb-0">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                                <i class="mdi mdi-file-document-multiple-outline me-2 text-primary fs-4"></i> Purchase Order Terkait
                                <span class="badge bg-label-primary ms-2">{{ $purchase->purchaseOrders->count() }} PO</span>
                            </h5>
                            @if ($purchase->status >= 1)
                                <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalLinkPo">
                                    <i class="mdi mdi-link-variant me-1"></i> Hubungkan PO Lainnya
                                </button>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive text-nowrap">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;" class="text-center">No</th>
                                            <th>No PO</th>
                                            <th>Supplier</th>
                                            <th>Item</th>
                                            <th>Info Pengiriman</th>
                                            <th class="text-center">Status GR</th>
                                            <th class="text-center" style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchase->purchaseOrders as $po)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-1 mb-1">
                                                        <a href="{{ route('purchase.show', $po->id) }}" class="fw-bold text-primary">
                                                            {{ $po->no_po }}
                                                        </a>
                                                        @if ($po->is_direct_purchase)
                                                            <span class="badge bg-label-info font-10">Direct Purchase</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ \Carbon\Carbon::parse($po->created_at)->format('d-m-Y H:i') }}
                                                    </div>
                                                </td>
                                                <td>{{ $po->company ?: '-' }}</td>
                                                <td>
                                                    @php $poAllocs = $allocsByPo[$po->id] ?? []; @endphp
                                                    @if (count($poAllocs))
                                                        <ul class="list-unstyled mb-0 small">
                                                            @foreach ($poAllocs as $entry)
                                                                <li>
                                                                    <span class="fw-semibold">{{ $entry['po_qty'] }} pcs</span>
                                                                    {{ $entry['detail']->equivalent->brand ?? '' }}
                                                                    {{ $entry['detail']->equivalent->pn ?? '' }}
                                                                    @if ($entry['po_qty'] > $entry['alloc']->qty)
                                                                        <span class="badge bg-label-info font-10" data-bs-toggle="tooltip"
                                                                            title="Kebutuhan PR: {{ $entry['alloc']->qty }} pcs, kelebihan: {{ $entry['po_qty'] - $entry['alloc']->qty }} pcs untuk stok">
                                                                            +{{ $entry['po_qty'] - $entry['alloc']->qty }} stok
                                                                        </span>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $firstAlloc = ($allocsByPo[$po->id] ?? [])[0]['alloc'] ?? null;
                                                    @endphp
                                                    @if ($firstAlloc && $firstAlloc->purchase_type)
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <span class="badge {{ $firstAlloc->purchase_type == 'Lokal' ? 'bg-label-info' : 'bg-label-primary' }}">{{ $firstAlloc->purchase_type }}</span>
                                                            <a href="#" data-bs-toggle="tooltip" title="Edit Info Pengiriman"
                                                                class="text-dark edit-delivery-info"
                                                                data-id="{{ $firstAlloc->id }}"
                                                                data-purchase-type="{{ $firstAlloc->purchase_type }}"
                                                                data-cargo="{{ $firstAlloc->cargo }}"
                                                                data-no-resi="{{ $firstAlloc->no_resi }}"
                                                                data-purchase-date="{{ $firstAlloc->purchase_date }}">
                                                                <i class="mdi mdi-pencil-outline"></i>
                                                            </a>
                                                        </div>
                                                        <div class="small">
                                                            <div><strong>Cargo:</strong> {{ $firstAlloc->cargo ?: '-' }}</div>
                                                            @if ($firstAlloc->no_resi)
                                                                <div><strong>Resi:</strong> <code>{{ $firstAlloc->no_resi }}</code></div>
                                                            @endif
                                                            @if ($firstAlloc->purchase_date)
                                                                <div class="text-muted">Tgl: {{ \Carbon\Carbon::parse($firstAlloc->purchase_date)->format('d-m-Y') }}</div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="badge bg-label-secondary">
                                                            <i class="mdi mdi-clock-outline me-1"></i>Belum Dikirim
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($po->receipt_status == 'Received')
                                                        <span class="badge bg-label-success" data-bs-toggle="tooltip" title="{{ $po->no_gr }}">Diterima</span>
                                                    @elseif ($poDeliveryStatus[$po->id] ?? false)
                                                        <span class="badge bg-label-info">Sedang Dikirim</span>
                                                    @else
                                                        <span class="badge bg-label-warning">Menunggu Info Pengiriman</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex align-items-center gap-1">
                                                        <a href="{{ route('purchase.show', $po->id) }}" class="btn btn-sm btn-icon btn-label-secondary waves-effect rounded-circle" data-bs-toggle="tooltip" title="Lihat PO">
                                                            <i class="mdi mdi-eye-outline"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-icon btn-label-danger waves-effect rounded-circle btn-unlink-po" data-po-id="{{ $po->id }}" data-po-no="{{ $po->no_po }}" data-bs-toggle="tooltip" title="Lepas Tautan PO">
                                                            <i class="mdi mdi-link-variant-off"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($purchase && $purchase->status == 1)
            {{-- Card jika PR disetujui tapi belum ada PO terhubung --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card modern-card mb-0">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                                <i class="mdi mdi-file-document-multiple-outline me-2 text-primary fs-4"></i> Purchase Order Terkait
                            </h5>
                            <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalLinkPo">
                                <i class="mdi mdi-link-variant me-1"></i> Hubungkan ke PO Terbit
                            </button>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div class="avatar avatar-md mx-auto mb-2">
                                <span class="avatar-initial rounded-circle bg-label-secondary">
                                    <i class="mdi mdi-link-variant font-22"></i>
                                </span>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Belum Ada Purchase Order (PO) yang Terhubung</h6>
                            <p class="text-muted small mb-3">Jika dokumen PO untuk PR ini sudah diterbitkan di sistem, klik tombol di bawah untuk menghubungkannya.</p>
                            <button type="button" class="btn btn-outline-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#modalLinkPo">
                                <i class="mdi mdi-link-variant me-1"></i> Pilih &amp; Hubungkan PO Terbit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Baris 3: Card Diskusi & Kolaborasi --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card modern-card mb-0" id="diskusi">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-forum-outline me-2 text-primary fs-4"></i> Diskusi & Kolaborasi PR
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        {{-- Daftar pesan --}}
                        <div class="discussion-list mb-4 p-3 rounded border" style="max-height: 400px; overflow-y: auto; background-color: #fcfcfd;">
                            @forelse($discussions as $disc)
                                @php
                                    $isMe = $disc->id_user == Auth::id();
                                @endphp
                                <div class="d-flex gap-3 mb-4 {{ $isMe ? 'flex-row-reverse' : '' }}">
                                    <div class="flex-shrink-0">
                                        <img src="{{ url('') . '/' . $disc->user->image }}"
                                            class="rounded-circle border border-2 border-white shadow-xs"
                                            style="width:38px;height:38px;object-fit:cover;"
                                            alt="{{ $disc->user->name }}">
                                    </div>
                                    <div style="max-width: 75%">
                                        <div class="d-flex align-items-center gap-2 mb-1 {{ $isMe ? 'flex-row-reverse' : '' }}">
                                            <span class="fw-semibold text-dark" style="font-size: 13px;">{{ $disc->user->name }}</span>
                                            <span class="text-muted" style="font-size:10px;">
                                                {{ \Carbon\Carbon::parse($disc->created_at)->diffForHumans() }}
                                            </span>
                                        </div>
                                        <div class="p-3 rounded-3 {{ $isMe ? 'chat-bubble-me' : 'chat-bubble-other' }}"
                                            style="word-break: break-word; font-size: 13.5px; line-height: 1.4;">
                                            @php
                                                $msg = e($disc->message);
                                                foreach ($disc->mentions as $m) {
                                                    $msg = str_replace(
                                                        '@' . $m->user->name,
                                                        '<span class="fw-bold ' . ($isMe ? 'text-primary' : 'text-primary') . '">@' . e($m->user->name) . '</span>',
                                                        $msg
                                                    );
                                                }
                                            @endphp
                                            {!! nl2br($msg) !!}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    <div class="avatar avatar-lg mx-auto mb-3 bg-label-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                        <i class="mdi mdi-forum-outline fs-3"></i>
                                    </div>
                                    <p class="mb-0 fw-medium">Belum ada diskusi.</p>
                                    <small class="text-muted">Mulai percakapan sekarang dengan mengetik pesan di bawah.</small>
                                </div>
                            @endforelse
                        </div>

                        {{-- Form kirim pesan --}}
                        <form action="{{ route('purchase-request.add-discussion', $pending->id) }}" method="POST" id="discussionForm">
                            @csrf
                            <div class="position-relative">
                                <div id="mentionDropdown" class="mention-dropdown-menu" style="display:none;"></div>

                                <textarea
                                        name="message"
                                        id="discussionMessage"
                                        class="form-control shadow-none"
                                        rows="3"
                                        placeholder="Tulis pesan... ketik @ untuk mention rekan tim"
                                        style="padding-right: 120px; resize:none; border-radius: 8px; font-size: 13.5px;"
                                        required></textarea>

                                    {{-- Hidden inputs untuk mention --}}
                                    <div id="mentionInputs"></div>

                                    <button type="submit" class="btn btn-primary position-absolute d-flex align-items-center"
                                        style="bottom:12px;right:12px; padding: 6px 14px; font-size: 13px; border-radius: 6px;">
                                        <i class="mdi mdi-send me-1"></i> Kirim
                                    </button>
                                </div>

                                {{-- Tag mention yang dipilih --}}
                                <div id="mentionTags" class="d-flex flex-wrap gap-1 mt-2"></div>
                            </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Reject PR --}}
        <div class="modal fade" id="rejectPurchaseModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form id="rejectPurchaseForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold text-danger">Reject Purchase Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="rejectReason" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="rejectReason" name="reason" rows="3" required
                                    placeholder="Jelaskan kenapa PR ini ditolak..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="mdi mdi-close-circle-outline me-1"></i> Tolak PR
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Item PR --}}
        <div class="modal fade" id="editPurchaseItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form id="editPurchaseItemForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Item Purchase Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editPurchaseQty" class="form-label">Qty (Kebutuhan SO)</label>
                                <input type="number" class="form-control" id="editPurchaseQty" name="qty" min="1" required>
                            </div>
                            @if (in_array(Auth::user()->role, ['Logistic', 'Admin']) && $purchase && $purchase->status == '0')
                                <div class="mb-3">
                                    <label for="editPurchaseQtyStock" class="form-label">Qty Tambahan (Stok)</label>
                                    <input type="number" class="form-control" id="editPurchaseQtyStock" name="qty_stock" min="0">
                                    <div class="form-text">Tambahan qty di luar kebutuhan SO, buat buffer stok gudang. Hanya bisa diisi sebelum PR di-ACC.</div>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="editPurchaseNote" class="form-label">Note</label>
                                <textarea class="form-control" id="editPurchaseNote" name="note" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Info Pengiriman (On Delivery) --}}
    <div class="modal fade" id="modalDeliveryInfo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deliveryInfoForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deliveryInfoModalTitle">Info Pengiriman</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label d-block">Tipe Pembelian</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="purchase_type" id="purchaseTypeLokal"
                                    value="Lokal" required>
                                <label class="form-check-label" for="purchaseTypeLokal">Lokal</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="purchase_type" id="purchaseTypeImpor"
                                    value="Impor" required>
                                <label class="form-check-label" for="purchaseTypeImpor">Impor</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pembelian</label>
                            <input type="date" class="form-control" name="purchase_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cargo / Ekspedisi</label>
                            <input type="text" class="form-control" name="cargo" placeholder="Contoh: JNE, SiCepat, DHL"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No Resi <span class="text-muted fw-normal">(opsional, bisa diisi belakangan)</span></label>
                            <input type="text" class="form-control" name="no_resi" placeholder="Nomor resi">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="deliveryInfoSubmitBtn">On Delivery</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- End: Modal Info Pengiriman --}}

    {{-- Modal: Hubungkan ke Purchase Order Terbit --}}
    <div class="modal fade" id="modalLinkPo" tabindex="-1" aria-labelledby="modalLinkPoLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <form id="formLinkPo">
                    <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-md flex-shrink-0">
                                <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                    <i class="mdi mdi-link-variant font-22"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0" id="modalLinkPoLabel">Hubungkan ke Purchase Order (PO)</h5>
                                <small class="text-muted font-12">
                                    PR: <span class="fw-semibold text-primary font-monospace">{{ $purchase ? ($purchase->no_pr ?? ('#' . $purchase->id)) : '-' }}</span>
                                </small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 d-flex align-items-start gap-2 mb-3 py-2 px-3 rounded-3" style="background: rgba(105, 108, 255, 0.08); border-left: 4px solid #696cff !important;">
                            <i class="mdi mdi-information-outline text-primary fs-5 mt-0 flex-shrink-0"></i>
                            <div class="font-12 text-dark">
                                Pilih dokumen Purchase Order yang <strong>sudah terbit</strong> di sistem untuk dihubungkan ke Purchase Request ini. Anda dapat memilih lebih dari satu PO.
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label font-12 fw-bold text-dark mb-1">
                                Cari &amp; Pilih Purchase Order (PO) Terbit <span class="text-danger">*</span>
                            </label>
                            <select id="selectLinkPo" name="id_purchase_order[]" class="form-select" multiple="multiple" style="width: 100%;" required>
                            </select>
                            <div class="form-text font-11 text-muted mt-1">
                                <i class="mdi mdi-magnify me-1"></i>Ketik nomor PO (contoh: 115-P/RJO...) atau nama vendor untuk mencari.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light bg-opacity-25 px-4 py-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs" id="btnSubmitLinkPo">
                            <i class="mdi mdi-check-circle-outline me-1"></i>
                            <span>Simpan Tautan PO</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/dropzone/dropzone.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        #modalLinkPo .select2-container--default .select2-selection--multiple {
            border-color: #d9dee3;
            border-radius: 8px;
            min-height: 42px;
            padding: 4px 6px;
        }
        #modalLinkPo .select2-dropdown {
            border-radius: 10px;
            border-color: #d9dee3;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }
        .chat-bubble-me {
            background-color: #ECEAFE;
            border-radius: 12px 12px 2px 12px !important;
            color: #2F3349;
            border: 1px solid #d5d0fa;
        }

        .chat-bubble-other {
            background-color: #ffffff;
            border-radius: 12px 12px 12px 2px !important;
            color: #2F3349;
            border: 1px solid rgba(24, 28, 33, 0.08);
        }

        .discussion-list::-webkit-scrollbar {
            width: 5px;
        }
        .discussion-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .discussion-list::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        /* Mention Dropdown Elegant UI */
        .mention-dropdown-menu {
            position: absolute;
            bottom: calc(100% + 6px);
            left: 0;
            width: 100%;
            max-width: 440px;
            z-index: 1060;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid rgba(105, 108, 255, 0.25) !important;
            box-shadow: 0 14px 34px rgba(34, 48, 62, 0.18), 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
            animation: mentionDropdownFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        html.dark-style .mention-dropdown-menu {
            background: #2b2c40 !important;
            border-color: rgba(105, 108, 255, 0.35) !important;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.55);
        }

        @keyframes mentionDropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mention-dropdown-header {
            padding: 8px 14px;
            background: #f8f9fa;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            font-size: 11.5px;
            color: #566a7f;
        }

        html.dark-style .mention-dropdown-header {
            background: #32344d;
            border-bottom-color: rgba(255,255,255,0.07);
            color: #a8abc2;
        }

        .mention-dropdown-list {
            max-height: 220px;
            overflow-y: auto;
            margin: 0;
            padding: 4px;
            list-style: none;
        }

        .mention-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }

        .mention-item:hover,
        .mention-item.active-item {
            background-color: rgba(105, 108, 255, 0.08);
        }

        html.dark-style .mention-item:hover,
        html.dark-style .mention-item.active-item {
            background-color: rgba(105, 108, 255, 0.2);
        }

        .mention-tag {
            background: rgba(105, 108, 255, 0.1);
            color: #696cff;
            border: 1px solid rgba(105, 108, 255, 0.25);
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 12px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        html.dark-style .mention-tag {
            background: rgba(105, 108, 255, 0.2);
            color: #8c90ff;
            border-color: rgba(105, 108, 255, 0.4);
        }

        .mention-tag .remove-mention {
            cursor: pointer;
            color: #a1acb8;
            font-size: 14px;
            line-height: 1;
        }

        .mention-tag .remove-mention:hover {
            color: #ff3e1d;
        }

        @media (min-width: 768px) {
            .border-end-md {
                border-right: 1px solid rgba(24, 28, 33, 0.08) !important;
            }
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/dropzone/dropzone.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
    <script>

        // Scroll diskusi ke pesan terbaru
        (function () {
            var list = document.querySelector('.discussion-list');
            if (list) list.scrollTop = list.scrollHeight;
        })();

        // @mention logic
        var allUsers = @json($allUsers ?? []);
        var selectedMentions = {}; // id => name
        var mentionStartIndex = -1;
        var activeMentionIndex = 0;
        var currentFilteredUsers = [];

        var textarea = document.getElementById('discussionMessage');
        var dropdown = document.getElementById('mentionDropdown');
        var tagsEl = document.getElementById('mentionTags');
        var inputsEl = document.getElementById('mentionInputs');

        var roleColors = {
            'Admin': 'danger',
            'Super Admin': 'danger',
            'Developer': 'dark',
            'Sales': 'primary',
            'Support': 'info',
            'Logistic': 'warning',
            'Accounting': 'success',
            'Purchasing': 'warning'
        };

        function renderDropdown(query) {
            if (!dropdown || !textarea) return;
            currentFilteredUsers = allUsers.filter(function (u) {
                return u.name.toLowerCase().indexOf(query.toLowerCase()) !== -1 && !selectedMentions[u.id];
            }).slice(0, 8);

            if (!currentFilteredUsers.length) {
                dropdown.style.display = 'none';
                dropdown.innerHTML = '';
                return;
            }

            activeMentionIndex = 0;

            var html = `
                <div class="mention-dropdown-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-1">
                        <i class="mdi mdi-at text-primary"></i>
                        <span class="fw-bold">Pilih Rekan Tim (${currentFilteredUsers.length})</span>
                    </div>
                    <small class="text-muted" style="font-size: 10px;">Tekan ↑ ↓ & Enter</small>
                </div>
                <ul class="mention-dropdown-list">
            `;

            currentFilteredUsers.forEach(function (u, index) {
                var color = roleColors[u.role] || 'primary';
                var initial = (u.name || 'U').charAt(0).toUpperCase();
                var activeCls = index === 0 ? 'active-item' : '';
                var avatarHtml = '';

                if (u.image) {
                    var imgSrc = u.image.startsWith('/') ? u.image : '/' + u.image;
                    avatarHtml = `<img src="${imgSrc}" class="rounded-circle shadow-xs flex-shrink-0" width="30" height="30" style="object-fit:cover;" onerror="this.outerHTML='<span class=\\'avatar-initial rounded-circle bg-label-${color} fw-bold d-flex align-items-center justify-content-center shadow-xs flex-shrink-0\\' style=\\'width:30px;height:30px;font-size:12px;\\'>${initial}</span>'">`;
                } else {
                    avatarHtml = `<span class="avatar-initial rounded-circle bg-label-${color} fw-bold d-flex align-items-center justify-content-center shadow-xs flex-shrink-0" style="width:30px;height:30px;font-size:12px;">${initial}</span>`;
                }

                html += `
                    <li class="mention-item ${activeCls}" data-index="${index}">
                        ${avatarHtml}
                        <div class="flex-grow-1 min-w-0 text-truncate">
                            <span class="fw-semibold text-dark d-block text-truncate" style="font-size: 13px;">${u.name}</span>
                        </div>
                        <span class="badge bg-label-${color} rounded-pill px-2 py-0.5 ms-auto flex-shrink-0" style="font-size: 10px;">
                            ${u.role || 'Team'}
                        </span>
                    </li>
                `;
            });

            html += `</ul>`;
            dropdown.innerHTML = html;
            dropdown.style.display = 'block';

            // Bind click events
            dropdown.querySelectorAll('.mention-item').forEach(function (el) {
                el.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    var idx = parseInt(this.getAttribute('data-index'), 10);
                    if (currentFilteredUsers[idx]) {
                        selectMention(currentFilteredUsers[idx]);
                    }
                });
            });
        }

        function updateActiveItem() {
            if (!dropdown) return;
            var items = dropdown.querySelectorAll('.mention-item');
            items.forEach(function (el, idx) {
                if (idx === activeMentionIndex) {
                    el.classList.add('active-item');
                    el.scrollIntoView({ block: 'nearest' });
                } else {
                    el.classList.remove('active-item');
                }
            });
        }

        function selectMention(user) {
            if (!textarea) return;
            var val = textarea.value;
            var before = val.substring(0, mentionStartIndex);
            var after = val.substring(textarea.selectionStart);
            textarea.value = before + '@' + user.name + ' ' + after;
            textarea.focus();

            selectedMentions[user.id] = user.name;
            if (dropdown) {
                dropdown.style.display = 'none';
                dropdown.innerHTML = '';
            }
            mentionStartIndex = -1;
            renderTags();
        }

        function renderTags() {
            if (!tagsEl || !inputsEl) return;
            tagsEl.innerHTML = '';
            inputsEl.innerHTML = '';
            Object.keys(selectedMentions).forEach(function (id) {
                var span = document.createElement('span');
                span.className = 'mention-tag';
                span.innerHTML = '@' + selectedMentions[id] +
                    ' <span class="remove-mention ms-1" data-id="' + id + '">&times;</span>';
                tagsEl.appendChild(span);

                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'mentions[]';
                inp.value = id;
                inputsEl.appendChild(inp);
            });

            tagsEl.querySelectorAll('.remove-mention').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    delete selectedMentions[this.dataset.id];
                    renderTags();
                });
            });
        }

        if (textarea) {
            textarea.addEventListener('input', function () {
                var val = this.value;
                var pos = this.selectionStart;

                var atPos = -1;
                for (var i = pos - 1; i >= 0; i--) {
                    if (val[i] === '@') { atPos = i; break; }
                    if (val[i] === ' ' || val[i] === '\n') break;
                }

                if (atPos !== -1) {
                    mentionStartIndex = atPos;
                    var query = val.substring(atPos + 1, pos);
                    renderDropdown(query);
                } else {
                    if (dropdown) {
                        dropdown.style.display = 'none';
                        dropdown.innerHTML = '';
                    }
                    mentionStartIndex = -1;
                }
            });

            textarea.addEventListener('keydown', function (e) {
                if (!dropdown || dropdown.style.display === 'none' || !currentFilteredUsers.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    activeMentionIndex = (activeMentionIndex + 1) % currentFilteredUsers.length;
                    updateActiveItem();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    activeMentionIndex = (activeMentionIndex - 1 + currentFilteredUsers.length) % currentFilteredUsers.length;
                    updateActiveItem();
                } else if (e.key === 'Enter' || e.key === 'Tab') {
                    e.preventDefault();
                    if (currentFilteredUsers[activeMentionIndex]) {
                        selectMention(currentFilteredUsers[activeMentionIndex]);
                    }
                } else if (e.key === 'Escape') {
                    dropdown.style.display = 'none';
                    dropdown.innerHTML = '';
                    mentionStartIndex = -1;
                }
            });

            textarea.addEventListener('blur', function () {
                setTimeout(function () {
                    if (dropdown) {
                        dropdown.style.display = 'none';
                        dropdown.innerHTML = '';
                    }
                }, 200);
            });
        }

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target) && e.target !== textarea) {
                dropdown.style.display = 'none';
            }
        });

        // Validasi form sebelum submit
        document.getElementById('discussionForm').addEventListener('submit', function (e) {
            var msg = textarea.value.trim();
            if (!msg) { e.preventDefault(); textarea.focus(); }
        });

        $('#backButton').click(function() {
            window.history.back();
        });

        var editPurchaseItemModal = new bootstrap.Modal(document.getElementById('editPurchaseItemModal'));

        $(document).on('click', '.edit-purchase-item', function() {
            var id = $(this).data('id');
            $('#editPurchaseItemForm').attr('action', '{{ url('purchase-request') }}/update/' + id);
            $('#editPurchaseQty').val($(this).data('qty'));
            $('#editPurchaseQtyStock').val($(this).data('qty-stock') || 0);
            $('#editPurchaseNote').val($(this).data('note') || '');
            editPurchaseItemModal.show();
        });

        $('#editPurchaseItemForm').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: $form.serialize(),
                success: function() {
                    editPurchaseItemModal.hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Item purchase request berhasil diperbarui.',
                        customClass: {
                            confirmButton: 'btn btn-success waves-effect'
                        }
                    }).then(function() {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    var message = 'Gagal memperbarui item.';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: message
                    });
                }
            });
        });

        $(document).on('click', '.delete-purchase-item', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Hapus item?',
                text: 'Item ini akan dihapus dari purchase request.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                customClass: {
                    confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                    cancelButton: 'btn btn-label-secondary waves-effect'
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url('purchase-request') }}/delete/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus',
                                    text: 'Item berhasil dihapus.',
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect'
                                    }
                                }).then(function() {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data gagal dihapus.'
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.acc-purchase', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to acc this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Acc it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('purchase-request') }}/acc/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'PATCH',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Acc succed!",
                                    text: "Your file has been acc.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Acc!'
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

        var rejectPurchaseModal = new bootstrap.Modal(document.getElementById('rejectPurchaseModal'));
        var rejectPurchaseId = null;

        $(document).on('click', '.reject-purchase', function() {
            rejectPurchaseId = $(this).data('id');
            $('#rejectReason').val('');
            rejectPurchaseModal.show();
        });

        $('#rejectPurchaseForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ url('purchase-request') }}/reject/' + rejectPurchaseId,
                type: 'POST',
                data: {
                    '_method': 'PATCH',
                    '_token': '{{ csrf_token() }}',
                    'reason': $('#rejectReason').val()
                },
                success: function(response) {
                    if (response == 1) {
                        rejectPurchaseModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'PR Ditolak',
                            text: 'Purchase Request berhasil ditolak.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                        }).then(function() {
                            window.location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    var message = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error :
                        (xhr.responseJSON && xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).flat().join('\n') : 'Gagal menolak PR.');
                    Swal.fire({ icon: 'error', title: 'Oops...', text: message });
                }
            });
        });

        var deliveryModalEl = document.getElementById('modalDeliveryInfo');
        var deliveryModal = new bootstrap.Modal(deliveryModalEl);
        var deliveryContext = {};

        $(document).on('click', '.edit-delivery-info', function() {
            deliveryContext = {
                mode: 'edit',
                id: $(this).data('id'),
            };
            $('#deliveryInfoForm')[0].reset();
            var $form = $('#deliveryInfoForm');
            $('[name="purchase_type"][value="' + $(this).data('purchase-type') + '"]', $form).prop('checked', true);
            $('[name="cargo"]', $form).val($(this).data('cargo'));
            $('[name="no_resi"]', $form).val($(this).data('no-resi'));
            $('[name="purchase_date"]', $form).val($(this).data('purchase-date'));
            $('#deliveryInfoModalTitle').text('Edit Info Pengiriman');
            $('#deliveryInfoSubmitBtn').text('Simpan');
            deliveryModal.show();
        });

        $('#deliveryInfoForm').on('submit', function(e) {
            e.preventDefault();
            var purchaseType = $('[name="purchase_type"]:checked', this).val();
            var cargo = $('[name="cargo"]', this).val();
            var noResi = $('[name="no_resi"]', this).val();
            var purchaseDate = $('[name="purchase_date"]', this).val();

            var url = '{{ url('purchase-request') }}/delivery-info/' + deliveryContext.id;
            var successText = 'Info pengiriman berhasil diperbarui.';

            $.ajax({
                'url': url,
                'type': 'POST',
                'data': {
                    '_method': 'PATCH',
                    '_token': '{{ csrf_token() }}',
                    'purchase_type': purchaseType,
                    'cargo': cargo,
                    'no_resi': noResi,
                    'purchase_date': purchaseDate,
                },
                success: function(response) {
                    if (response == 1) {
                        deliveryModal.hide();
                        Swal.fire({
                            icon: "success",
                            title: "Delivery succed!",
                            text: successText,
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        })
                        window.setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Data Failed to Delivery!'
                        });
                    }
                },
                error: function(xhr) {
                    var message = 'Data Failed to Delivery!';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: message
                    });
                }
            });
        });

        // Select2 Link PO Modal
        $('#modalLinkPo').on('shown.bs.modal', function() {
            if (!$('#selectLinkPo').hasClass('select2-hidden-accessible')) {
                $('#selectLinkPo').select2({
                    dropdownParent: $('#modalLinkPo'),
                    placeholder: 'Ketik & cari dokumen PO...',
                    allowClear: true,
                    ajax: {
                        url: '{{ route("purchase-order.search-to-link") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                exclude_pr_id: '{{ $purchase ? $purchase->id : "" }}'
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.results
                            };
                        },
                        cache: true
                    },
                    templateResult: function(item) {
                        if (item.loading) return item.text;
                        var $wrapper = $('<div><div class="fw-bold text-dark font-13">' + item.text + '</div></div>');
                        if (item.vendor) {
                            $wrapper.append('<div class="font-11 text-muted"><i class="mdi mdi-domain me-1"></i>' + item.vendor + (item.date ? ' &bull; ' + item.date : '') + '</div>');
                        }
                        if (item.total) {
                            $wrapper.append('<div class="font-11 text-primary fw-semibold">' + item.total + '</div>');
                        }
                        if (item.current_pr) {
                            $wrapper.append('<div class="font-11 text-warning"><i class="mdi mdi-alert-circle-outline me-1"></i>' + item.current_pr + '</div>');
                        }
                        return $wrapper;
                    }
                });
            }
        });

        // Submit Hubungkan PO
        $('#formLinkPo').on('submit', function(e) {
            e.preventDefault();
            var selectedPos = $('#selectLinkPo').val();
            if (!selectedPos || selectedPos.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih PO',
                    text: 'Silakan pilih setidaknya satu Purchase Order yang ingin dihubungkan.'
                });
                return;
            }

            var $btn = $('#btnSubmitLinkPo');
            var originalText = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menghubungkan...');

            $.ajax({
                url: '{{ route("purchase-request.link-po", $pending->id) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id_purchase_order: selectedPos
                },
                success: function(response) {
                    $('#modalLinkPo').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Dihubungkan!',
                        text: response.message || 'Dokumen PO berhasil dihubungkan ke PR ini.',
                        customClass: {
                            confirmButton: 'btn btn-success waves-effect'
                        }
                    }).then(function() {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(originalText);
                    var msg = 'Gagal menghubungkan dokumen PO.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: msg
                    });
                }
            });
        });

        // Lepas Tautan PO
        $(document).on('click', '.btn-unlink-po', function(e) {
            e.preventDefault();
            var poId = $(this).data('po-id');
            var poNo = $(this).data('po-no') || 'ini';

            Swal.fire({
                title: 'Lepas Tautan PO?',
                text: 'Apakah Anda yakin ingin melepas tautan PO ' + poNo + ' dari PR ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff3e1d',
                cancelButtonColor: '#8592a3',
                confirmButtonText: 'Ya, Lepas Tautan',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang melepas tautan PO...',
                        allowOutsideClick: false,
                        didOpen: function() {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route("purchase-request.unlink-po", $pending->id) }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id_purchase_order: poId
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Tautan PO berhasil dilepas.',
                                customClass: {
                                    confirmButton: 'btn btn-success waves-effect'
                                }
                            }).then(function() {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            var msg = 'Gagal melepas tautan PO.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: msg
                            });
                        }
                    });
                }
            });
        });

        // ── Selection PR Items for PO / Direct Purchase Creation ──
        function updateSelectedPrCount() {
            var totalChecked = $('.check-pr-item:checked').length;
            $('#countSelectedPrItems').text(totalChecked);
            var totalAvailable = $('.check-pr-item').length;
            if (totalAvailable > 0) {
                $('#checkAllPrItems').prop('checked', totalChecked === totalAvailable);
            }
        }

        $(document).on('change', '#checkAllPrItems', function() {
            var isChecked = $(this).is(':checked');
            $('.check-pr-item').prop('checked', isChecked);
            updateSelectedPrCount();
        });

        $(document).on('change', '.check-pr-item', function() {
            updateSelectedPrCount();
        });

        // Initialize count on page load
        updateSelectedPrCount();

        // Create PO from selected items
        $('#btnCreatePoFromSelected').on('click', function() {
            var selectedIds = [];
            $('.check-pr-item:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (!selectedIds.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Item Terlebih Dahulu',
                    text: 'Silakan centang minimal 1 item Purchase Request untuk dibuatkan Purchase Order.',
                    customClass: {
                        confirmButton: 'btn btn-primary waves-effect'
                    }
                });
                return;
            }

            var prId = '{{ $purchase ? $purchase->id : "" }}';
            var queryParams = $.param({
                from_pr: prId,
                items: selectedIds
            });

            window.location.href = '{{ route("purchase.create") }}?' + queryParams;
        });

        // Create Direct Purchase from selected items
        $('#btnCreateDirectFromSelected').on('click', function() {
            var selectedIds = [];
            $('.check-pr-item:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (!selectedIds.length) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Item Terlebih Dahulu',
                    text: 'Silakan centang minimal 1 item Purchase Request untuk dibuatkan Direct Purchase.',
                    customClass: {
                        confirmButton: 'btn btn-primary waves-effect'
                    }
                });
                return;
            }

            var prId = '{{ $purchase ? $purchase->id : "" }}';
            var queryParams = $.param({
                from_pr: prId,
                items: selectedIds
            });

            window.location.href = '{{ route("purchase.direct-create") }}?' + queryParams;
        });

        // Rollback Approved to New PR Handler
        $('.btn-rollback-approved-to-new').on('click', function(e) {
            e.preventDefault();
            var prId = $(this).data('id');
            var noPr = $(this).data('no-pr') || ('PR #' + prId);

            Swal.fire({
                title: 'Kembalikan ke New PR?',
                html: '<p class="text-muted font-13 mb-2">Purchase Request <strong class="text-primary">' + noPr + '</strong> akan dikembalikan dari status <strong>Approved</strong> ke <strong>New PR (Draft)</strong>.</p>' +
                    '<p class="text-muted font-12 mb-0"><i class="mdi mdi-information-outline text-warning me-1"></i> Tautan PO terkait (jika ada) akan otomatis dilepas.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="mdi mdi-undo-variant me-1"></i> Ya, Rollback ke New PR',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-warning me-2 shadow-xs',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false,
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return $.ajax({
                        url: '/purchase-request/rollback-new/' + prId,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'PATCH'
                        }
                    }).then(function (response) {
                        return response;
                    }).catch(function (error) {
                        var msg = 'Terjadi kesalahan sistem.';
                        if (error.responseJSON && error.responseJSON.message) {
                            msg = error.responseJSON.message;
                        }
                        Swal.showValidationMessage(msg);
                    });
                },
                allowOutsideClick: function () { return !Swal.isLoading(); }
            }).then(function (result) {
                if (result.isConfirmed && result.value) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.value.message || 'Purchase Request berhasil dikembalikan ke status New PR.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.reload();
                    });
                }
            });
        });
    </script>
@endpush
