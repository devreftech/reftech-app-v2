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
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-primary fs-6 px-3 py-2">
                    <i class="mdi mdi-receipt-text-outline me-1"></i>SO: {{ $pending->no_pending }}
                </span>
                @if ($purchase)
                    <span class="badge bg-label-secondary fs-6 px-3 py-2">
                        PR: {{ $purchase->no_pr ?? '-' }}
                    </span>
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
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0" style="width: 140px;">Sales</td>
                                        <td>: <span class="fw-medium text-dark">{{ $quotation->sales->name }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Flag / Info</td>
                                        <td>: <span class="fw-medium text-dark">{{ $quotation->pic->client->info ?: '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Perusahaan / Client</td>
                                        <td>: <span class="fw-bold text-primary">{{ $quotation->pic->client->company }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">PIC Client</td>
                                        <td>: <span class="fw-medium text-dark">{{ $quotation->pic->name_pic }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Alamat Kirim</td>
                                        <td>: <span class="fw-medium text-dark text-wrap">{{ $quotation->pic->client->address }}</span></td>
                                    </tr>
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
                                            <a class="text-primary fw-bold" href="{{ route('pending-po.show', $pending->id) }}">
                                                {{ $pending->no_pending }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Payment Status</td>
                                        <td>:
                                            @if ($invoice)
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
                                @php
                                    $fullyAllocated = $purchase->details->every(fn ($d) => $d->remainingQty <= 0);
                                @endphp
                                @if (!$fullyAllocated)
                                    <div class="alert alert-warning py-2 px-3 mb-2 small">
                                        Pilih item di tabel dan buat Purchase Order sampai semua qty teralokasi.
                                    </div>
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
                                <div class="text-muted small fw-bold mb-1">PO Terkait:</div>
                                @foreach ($purchase->purchaseOrders as $po)
                                    @php $isOnDelivery = $poDeliveryStatus[$po->id] ?? false; @endphp
                                    <div class="border rounded p-2 mb-2">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <a href="{{ route('purchase.show', $po->id) }}" class="small text-primary fw-semibold">
                                                <i class="mdi mdi-file-document-outline me-1"></i>{{ $po->no_po }}
                                            </a>
                                            @if ($po->receipt_status == 'Received')
                                                <span class="badge bg-label-success">Diterima</span>
                                            @elseif ($isOnDelivery)
                                                <span class="badge bg-label-info">Sedang Dikirim</span>
                                            @else
                                                <span class="badge bg-label-warning">Menunggu Info Pengiriman</span>
                                            @endif
                                        </div>

                                        @if(Auth::user()->role == 'Logistic')
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
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-clipboard-text-outline me-2 text-primary fs-4"></i> Daftar Item Purchase Request
                            @if ($purchase)
                                <span class="badge bg-label-primary ms-2">{{ $purchase->no_pr ?? '-' }}</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $showAllocationUi = $purchase && $purchase->status == 1;
                            $canEditPrQty = in_array(Auth::user()->role, ['Logistic', 'Admin']);
                            $prColspan = 5 + ($showAllocationUi ? 2 : 0) + ($canEditPrQty ? 1 : 0);
                        @endphp
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered align-middle mb-0" id="prItemsTable">
                                <thead>
                                    <tr>
                                        @if ($showAllocationUi)
                                            <th style="width: 36px;" class="text-center">
                                                <input type="checkbox" class="form-check-input" id="checkAllPrItems">
                                            </th>
                                        @endif
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th>No PR</th>
                                        <th>Item / Equivalent</th>
                                        <th class="text-center">Qty</th>
                                        @if ($showAllocationUi)
                                            <th class="text-center" style="width: 110px;">Qty ke PO</th>
                                        @endif
                                        <th>Catatan / Note</th>
                                        @if ($canEditPrQty)
                                            <th class="text-center" style="width: 60px;"></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @forelse (($purchase->details ?? collect()) as $pr)
                                        @php $remaining = $pr->remainingQty; @endphp
                                        <tr>
                                            @if ($showAllocationUi)
                                                <td class="text-center">
                                                    @if ($remaining > 0)
                                                        <input type="checkbox" class="form-check-input pr-item-check"
                                                            data-id="{{ $pr->id }}" data-remaining="{{ $remaining }}">
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="text-center fw-medium">{{ $no }}</td>
                                            <td class="fw-bold text-dark">{{ $purchase->no_pr ?? '-' }}</td>
                                            <td>
                                                @if ($pr->id_equivalent == '0')
                                                    -
                                                @else
                                                    @php
                                                        $detPrice = $detQuotation->firstWhere('id_equivalent', $pr->id_equivalent);
                                                    @endphp
                                                    <span class="fw-semibold text-dark">{{ $pr->equivalent->brand }} {{ $pr->equivalent->pn }}</span>
                                                    @if ($pr->equivalent->product)
                                                        <span class="badge {{ $pr->equivalent->product->go == 'Genuine' ? 'bg-label-success' : 'bg-label-warning' }} ms-1">
                                                            {{ $pr->equivalent->product->go }}
                                                        </span>
                                                    @endif
                                                    <div class="mt-1">
                                                        <span class="badge bg-label-success">
                                                            <i class="mdi mdi-cash-multiple me-1"></i>{{ $detPrice ? 'Rp ' . number_format($detPrice->price, 0, '', '.') : '-' }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold text-dark fs-6">{{ $remaining }}</span>
                                                <span class="text-muted small">/ {{ $pr->totalQty }} {{ $pr->equivalent->product->unit ?? '' }}</span>
                                                @if ($pr->qty_stock > 0)
                                                    <div>
                                                        <span class="badge bg-label-info" data-bs-toggle="tooltip" title="Kebutuhan SO: {{ $pr->qty }}, tambahan stok: {{ $pr->qty_stock }}">
                                                            {{ $pr->qty }} SO + {{ $pr->qty_stock }} stok
                                                        </span>
                                                    </div>
                                                @endif
                                                @if ($pr->allocations->count())
                                                    <div class="mt-1 d-flex flex-column gap-1 align-items-center">
                                                        @foreach ($pr->allocations as $alloc)
                                                            <a href="{{ route('purchase.show', $alloc->id_purchase_order) }}"
                                                                class="badge bg-label-dark text-decoration-none" data-bs-toggle="tooltip"
                                                                title="{{ $alloc->purchaseOrder->no_po ?? '-' }}">
                                                                {{ $alloc->qty }} pcs → {{ $alloc->purchaseOrder->no_po ?? '-' }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            @if ($showAllocationUi)
                                                <td class="text-center">
                                                    @if ($remaining > 0)
                                                        <input type="number" class="form-control form-control-sm pr-item-qty text-center"
                                                            data-id="{{ $pr->id }}" data-remaining="{{ $remaining }}" min="1"
                                                            value="{{ $remaining }}" disabled style="width: 80px; margin: 0 auto;">
                                                        <small class="text-info d-block mt-1 pr-item-qty-hint d-none"
                                                            data-hint-for="{{ $pr->id }}"></small>
                                                    @else
                                                        <span class="badge bg-label-success">Lunas</span>
                                                    @endif
                                                </td>
                                            @endif
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
                        @if ($showAllocationUi)
                            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light-subtle">
                                <span class="text-muted small"><span id="selectedItemsCount">0</span> item dipilih</span>
                                <a href="#" id="btnCreatePoFromSelection"
                                    class="btn btn-primary btn-sm disabled" tabindex="-1" aria-disabled="true">
                                    <i class="mdi mdi-file-document-plus-outline me-1"></i> Buat Purchase Order dari Item Terpilih
                                </a>
                            </div>
                        @endif
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
                        <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                                <i class="mdi mdi-file-document-multiple-outline me-2 text-primary fs-4"></i> Purchase Order Terkait
                            </h5>
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
                                            <th class="text-center" style="width: 90px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchase->purchaseOrders as $poIdx => $po)
                                            @php $rows = $allocsByPo[$po->id] ?? []; @endphp
                                            <tr>
                                                <td class="text-center fw-medium">{{ $poIdx + 1 }}</td>
                                                <td class="fw-bold text-dark">{{ $po->no_po }}</td>
                                                <td>{{ $po->company ?: '-' }}</td>
                                                <td style="max-width: 240px; white-space: normal;">
                                                    @forelse ($rows as $row)
                                                        @php $extra = max(0, $row['po_qty'] - $row['alloc']->qty); @endphp
                                                        <div class="small mb-1">
                                                            <span class="fw-semibold text-dark">{{ $row['detail']->equivalent->brand ?? '' }} {{ $row['detail']->equivalent->pn ?? '' }}</span>
                                                            <span class="text-muted">&times; {{ $row['alloc']->qty }}</span>
                                                            @if ($extra > 0)
                                                                <span class="badge bg-label-info ms-1" data-bs-toggle="tooltip"
                                                                    title="PO ini beli {{ $row['po_qty'] }} pcs, {{ $row['alloc']->qty }} pcs utk PR ini, sisanya tambahan stok">
                                                                    +{{ $extra }} stok
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @empty
                                                        <span class="text-muted small">-</span>
                                                    @endforelse
                                                </td>
                                                <td style="max-width: 220px; white-space: normal;">
                                                    @php $firstRow = $rows[0] ?? null; @endphp
                                                    @if ($firstRow)
                                                        @php $alloc = $firstRow['alloc']; @endphp
                                                        <div class="mb-1">
                                                            @if ($alloc->purchase_type)
                                                                <div class="p-2 rounded bg-label-secondary d-flex flex-column gap-1" style="font-size: 0.8rem; min-width: 160px; line-height: 1.3;">
                                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                                        <span class="badge {{ $alloc->purchase_type == 'Lokal' ? 'bg-label-info' : 'bg-label-primary' }}">
                                                                            {{ $alloc->purchase_type }}
                                                                        </span>
                                                                        <a href="#" data-bs-toggle="tooltip" title="Edit Info Pengiriman"
                                                                            class="text-dark edit-delivery-info"
                                                                            data-id="{{ $alloc->id }}"
                                                                            data-purchase-type="{{ $alloc->purchase_type }}"
                                                                            data-cargo="{{ $alloc->cargo }}"
                                                                            data-no-resi="{{ $alloc->no_resi }}"
                                                                            data-purchase-date="{{ $alloc->purchase_date }}">
                                                                            <i class="mdi mdi-pencil-outline"></i>
                                                                        </a>
                                                                    </div>
                                                                    <span class="text-dark"><i class="mdi mdi-truck-delivery-outline text-muted me-1"></i>{{ $alloc->cargo }}</span>
                                                                    <span class="text-dark"><i class="mdi mdi-barcode text-muted me-1"></i>{{ $alloc->no_resi ?: 'Belum ada resi' }}</span>
                                                                    <span class="text-muted small"><i class="mdi mdi-calendar-outline me-1"></i>{{ $alloc->purchase_date ? \Carbon\Carbon::parse($alloc->purchase_date)->format('d-m-Y') : '-' }}</span>
                                                                </div>
                                                            @else
                                                                <span class="badge bg-label-secondary">
                                                                    <i class="mdi mdi-clock-outline me-1"></i>Belum Dikirim
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">-</span>
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
                                                    <a href="{{ route('purchase.show', $po->id) }}" class="btn btn-sm btn-icon btn-label-secondary waves-effect rounded-circle" data-bs-toggle="tooltip" title="Lihat PO">
                                                        <i class="mdi mdi-eye-outline"></i>
                                                    </a>
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

                            {{-- Mention dropdown --}}
                            <ul id="mentionDropdown"
                                class="list-group shadow border-0"
                                style="display:none;position:absolute;z-index:999;min-width:240px;max-height:200px;overflow-y:auto; border-radius: 8px;">
                            </ul>

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
@endsection

@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/dropzone/dropzone.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
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

        #mentionDropdown .list-group-item { cursor: pointer; padding: 6px 12px; }
        #mentionDropdown .list-group-item:hover { background: #f0f0f0; }
        #mentionDropdown .list-group-item img { width: 28px; height: 28px; object-fit: cover; }
        .mention-tag { background: #e7f1ff; color: #7367F0; border: 1px solid #bfdbfe; border-radius: 999px; padding: 2px 10px; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; }
        .mention-tag .remove-mention { cursor: pointer; font-weight: bold; color: #6b7280; }
        .mention-tag .remove-mention:hover { color: #ef4444; }

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
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
    <script>
        // Selection item PR untuk dibuatkan Purchase Order (bisa split ke beberapa supplier)
        (function () {
            var $table = $('#prItemsTable');
            if (!$table.length) return;

            function updateSelectionState() {
                var $checked = $table.find('.pr-item-check:checked');
                $('#selectedItemsCount').text($checked.length);

                var $btn = $('#btnCreatePoFromSelection');
                if ($checked.length) {
                    $btn.removeClass('disabled').attr('aria-disabled', 'false').removeAttr('tabindex');
                } else {
                    $btn.addClass('disabled').attr('aria-disabled', 'true').attr('tabindex', '-1');
                }
            }

            $table.on('change', '.pr-item-check', function () {
                var $qtyInput = $table.find('.pr-item-qty[data-id="' + $(this).data('id') + '"]');
                $qtyInput.prop('disabled', !this.checked);
                if (this.checked) {
                    $qtyInput.trigger('focus');
                }
                updateSelectionState();
            });

            $('#checkAllPrItems').on('change', function () {
                var checked = this.checked;
                $table.find('.pr-item-check').prop('checked', checked).trigger('change');
            });

            $table.on('input', '.pr-item-qty', function () {
                var id = $(this).data('id');
                var remaining = parseInt($(this).data('remaining'), 10) || 0;
                var val = parseInt($(this).val(), 10) || 0;
                if (val < 1) $(this).val(1);
                val = parseInt($(this).val(), 10) || 0;

                // Qty ke PO boleh lebih dari sisa kebutuhan PR — kelebihannya jadi
                // tambahan stok, bukan dibatasi ke remaining seperti sebelumnya.
                var $hint = $table.find('.pr-item-qty-hint[data-hint-for="' + id + '"]');
                if (val > remaining) {
                    $hint.removeClass('d-none').html(
                        '<i class="mdi mdi-information-outline"></i> ' + remaining + ' pcs utk PR, +' + (val - remaining) + ' stok'
                    );
                } else {
                    $hint.addClass('d-none');
                }
            });

            $('#btnCreatePoFromSelection').on('click', function (e) {
                e.preventDefault();
                if ($(this).hasClass('disabled')) return;

                var params = [];
                $table.find('.pr-item-check:checked').each(function () {
                    var id = $(this).data('id');
                    var qty = $table.find('.pr-item-qty[data-id="' + id + '"]').val();
                    params.push('items[' + id + ']=' + encodeURIComponent(qty));
                });
                if (!params.length) return;

                window.location.href = '{{ route('purchase.create') }}?from_pr={{ $purchase->id ?? '' }}&' + params.join('&');
            });

            updateSelectionState();
        })();

        // Scroll diskusi ke pesan terbaru
        (function () {
            var list = document.querySelector('.discussion-list');
            if (list) list.scrollTop = list.scrollHeight;
        })();

        // @mention logic
        var allUsers = @json($allUsers);
        var selectedMentions = {}; // id => name
        var mentionStartIndex = -1;

        var textarea = document.getElementById('discussionMessage');
        var dropdown = document.getElementById('mentionDropdown');
        var tagsEl = document.getElementById('mentionTags');
        var inputsEl = document.getElementById('mentionInputs');

        function renderDropdown(query) {
            var filtered = allUsers.filter(function (u) {
                return u.name.toLowerCase().indexOf(query.toLowerCase()) !== -1 && !selectedMentions[u.id];
            }).slice(0, 8);

            dropdown.innerHTML = '';
            if (!filtered.length) { dropdown.style.display = 'none'; return; }

            filtered.forEach(function (u) {
                var li = document.createElement('li');
                li.className = 'list-group-item d-flex align-items-center gap-2';
                li.innerHTML = '<img src="/' + (u.image || 'assets/img/avatars/1.png') + '" class="rounded-circle">' +
                    '<span>' + u.name + '</span>' +
                    '<small class="text-muted ms-auto">' + u.role + '</small>';
                li.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    selectMention(u);
                });
                dropdown.appendChild(li);
            });

            // Posisikan di bawah textarea
            var rect = textarea.getBoundingClientRect();
            dropdown.style.display = 'block';
            dropdown.style.top = (textarea.offsetTop + textarea.offsetHeight) + 'px';
            dropdown.style.left = textarea.offsetLeft + 'px';
        }

        function selectMention(user) {
            // Ganti teks @query dengan @name di textarea
            var val = textarea.value;
            var before = val.substring(0, mentionStartIndex);
            var after = val.substring(textarea.selectionStart);
            textarea.value = before + '@' + user.name + ' ' + after;
            textarea.focus();

            selectedMentions[user.id] = user.name;
            dropdown.style.display = 'none';
            mentionStartIndex = -1;
            renderTags();
        }

        function renderTags() {
            tagsEl.innerHTML = '';
            inputsEl.innerHTML = '';
            Object.keys(selectedMentions).forEach(function (id) {
                var span = document.createElement('span');
                span.className = 'mention-tag';
                span.innerHTML = '@' + selectedMentions[id] +
                    ' <span class="remove-mention" data-id="' + id + '">&times;</span>';
                tagsEl.appendChild(span);

                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'mentions[]';
                inp.value = id;
                inputsEl.appendChild(inp);
            });

            // Hapus mention dari tag
            tagsEl.querySelectorAll('.remove-mention').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    delete selectedMentions[this.dataset.id];
                    renderTags();
                });
            });
        }

        textarea.addEventListener('input', function () {
            var val = this.value;
            var pos = this.selectionStart;

            // Cari posisi @ terakhir sebelum kursor
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
                dropdown.style.display = 'none';
                mentionStartIndex = -1;
            }
        });

        textarea.addEventListener('keydown', function (e) {
            if (dropdown.style.display === 'block') {
                if (e.key === 'Escape') dropdown.style.display = 'none';
            }
        });

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
    </script>
@endpush
