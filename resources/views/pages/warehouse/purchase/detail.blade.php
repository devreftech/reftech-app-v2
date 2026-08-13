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
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.06), 0 0 1px 0 rgba(67, 89, 113, 0.12) !important;
            border-radius: 0.75rem !important;
            background-color: #ffffff;
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

        <div class="row">
            {{-- Main Content --}}
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">

                {{-- Card Metadata: Informasi Pihak Terkait & Dokumen --}}
                <div class="card modern-card mb-4">
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

                {{-- Card: Daftar Item PR --}}
                <div class="card modern-card mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-clipboard-text-outline me-2 text-primary fs-4"></i> Daftar Item Purchase Request
                            @if ($purchase)
                                <span class="badge bg-label-primary ms-2">{{ $purchase->no_pr ?? '-' }}</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th>No PR</th>
                                        <th>Item / Equivalent</th>
                                        <th class="text-center">Qty</th>
                                        <th>Catatan / Note</th>
                                        <th>Info Pengiriman</th>
                                        <th class="text-center" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @forelse (($purchase->details ?? collect()) as $pr)
                                        <tr>
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
                                                <span class="fw-bold text-dark fs-6">{{ $pr->qty }}</span> 
                                                <span class="text-muted small">{{ $pr->equivalent->product->unit ?? '' }}</span>
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
                                            <td>
                                                @if ($purchase->status >= 2 && $pr->purchase_type)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="p-2 rounded bg-label-secondary d-flex flex-column gap-1" style="font-size: 0.8rem; min-width: 160px; line-height: 1.3;">
                                                            <span class="badge {{ $pr->purchase_type == 'Lokal' ? 'bg-label-info' : 'bg-label-primary' }} align-self-start mb-1">
                                                                {{ $pr->purchase_type }}
                                                            </span>
                                                            <span class="text-dark"><i class="mdi mdi-truck-delivery-outline text-muted me-1"></i>{{ $pr->cargo }}</span>
                                                            <span class="text-dark"><i class="mdi mdi-barcode text-muted me-1"></i>{{ $pr->no_resi ?: 'Belum ada resi' }}</span>
                                                            <span class="text-muted small"><i class="mdi mdi-calendar-outline me-1"></i>{{ $pr->purchase_date ? \Carbon\Carbon::parse($pr->purchase_date)->format('d-m-Y') : '-' }}</span>
                                                        </div>
                                                        <a href="#" data-bs-toggle="tooltip" title="Edit Info Pengiriman"
                                                            class="btn btn-sm btn-icon btn-label-secondary waves-effect rounded-circle edit-delivery-info"
                                                            data-id="{{ $pr->id }}"
                                                            data-purchase-type="{{ $pr->purchase_type }}"
                                                            data-cargo="{{ $pr->cargo }}"
                                                            data-no-resi="{{ $pr->no_resi }}"
                                                            data-purchase-date="{{ $pr->purchase_date }}">
                                                            <i class="mdi mdi-pencil-outline"></i>
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="badge bg-label-secondary"><i class="mdi mdi-clock-outline me-1"></i>Belum Dikirim</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1 flex-wrap">
                                                    <a href="#" data-bs-toggle="tooltip" title="Edit Item"
                                                        class="btn btn-sm btn-icon btn-label-secondary waves-effect rounded-circle edit-purchase-item"
                                                        data-id="{{ $pr->id }}"
                                                        data-qty="{{ $pr->qty }}"
                                                        data-note="{{ $pr->note ?? '' }}">
                                                        <i class="mdi mdi-pencil-outline"></i>
                                                    </a>
                                                    <a href="#" data-bs-toggle="tooltip" title="Hapus Item"
                                                        class="btn btn-sm btn-icon btn-label-danger waves-effect rounded-circle delete-purchase-item"
                                                        data-id="{{ $pr->id }}">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @php $no++; @endphp
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Tidak Ada Purchase Request</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
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
                                        <label for="editPurchaseQty" class="form-label">Qty</label>
                                        <input type="number" class="form-control" id="editPurchaseQty" name="qty" min="1" required>
                                    </div>
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

                {{-- Card: Diskusi & Kolaborasi --}}
                <div class="card modern-card mb-4" id="diskusi">
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

            {{-- Sidebar Actions --}}
            <div class="col-xl-3 col-md-4 col-12 invoice-actions">
                <div class="card modern-card mb-3">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-cog-outline me-2 text-primary fs-5"></i> Tindakan PR
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        @if ($purchase && $purchase->status == 0)
                            <a href="#" class="btn btn-primary d-flex align-items-center justify-content-center w-100 mb-2 waves-effect acc-purchase"
                                data-id="{{ $purchase->id }}">
                                <i class="mdi mdi-check-all me-2 fs-5"></i> ACC Purchase Request
                            </a>
                        @elseif ($purchase && $purchase->status == 1)
                            <a href="#" class="btn btn-info text-white d-flex align-items-center justify-content-center w-100 mb-2 waves-effect delivery-purchase"
                                data-id="{{ $purchase->id }}">
                                <i class="mdi mdi-truck-delivery me-2 fs-5"></i> On Delivery
                            </a>
                        @endif

                        @php
                            $stat = ($purchase && $purchase->status == 2) ? 1 : 0;
                        @endphp

                        @if(Auth::user()->role == 'Logistic')
                            <a class="btn btn-primary d-flex align-items-center justify-content-center w-100 mb-2 waves-effect {{ $stat == 1 ? '' : 'disabled' }}"
                                href="{{ $stat == 1 ? route('purchase-request.goods-receipt', $pending->id) : '#' }}"
                                tabindex="{{ $stat == 1 ? '0' : '-1' }}" aria-disabled="{{ $stat == 1 ? 'false' : 'true' }}">
                                <i class="mdi mdi-checkbox-marked-circle-outline me-2 fs-5"></i> Verifikasi Penerimaan (GR)
                            </a>
                        @else
                            <a class="btn btn-primary d-flex align-items-center justify-content-center w-100 mb-2 waves-effect {{ $stat == 1 ? '' : 'disabled' }}"
                                href="{{ $stat == 1 ? route('purchase-request.done-all', $pending->id) : '#' }}"
                                tabindex="{{ $stat == 1 ? '0' : '-1' }}" aria-disabled="{{ $stat == 1 ? 'false' : 'true' }}">
                                <i class="mdi mdi-printer me-2 fs-5"></i> Cetak Product In
                            </a>
                        @endif

                        @if (Auth::user()->role != 'Logistic')
                            <button class="btn btn-outline-danger d-flex align-items-center justify-content-center w-100 waves-effect delete-invoice mb-2"
                                data-id="{{ $quotation->id }}">
                                <i class="mdi mdi-delete-outline me-2 fs-5"></i> Hapus Penawaran
                            </button>
                        @endif

                        <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center w-100 waves-effect" id="backButton">
                            <i class="mdi mdi-arrow-left me-2 fs-5"></i> Kembali
                        </button>
                    </div>
                </div>
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
            background-color: #ECEAFE !important;
            border-radius: 12px 12px 2px 12px !important;
            color: #2F3349 !important;
            border: 1px solid #d5d0fa;
        }

        .chat-bubble-other {
            background-color: #ffffff !important;
            border-radius: 12px 12px 12px 2px !important;
            color: #2F3349 !important;
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
        var deliveryModalEl = document.getElementById('modalDeliveryInfo');
        var deliveryModal = new bootstrap.Modal(deliveryModalEl);
        var deliveryContext = {};

        $(document).on('click', '.delivery-purchase', function() {
            deliveryContext = {
                mode: 'single',
                id: $(this).data('id'),
            };
            $('#deliveryInfoForm')[0].reset();
            $('#deliveryInfoModalTitle').text('Info Pengiriman');
            $('#deliveryInfoSubmitBtn').text('On Delivery');
            deliveryModal.show();
        });
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

            var urlMap = {
                edit: '{{ url('purchase-request') }}/delivery-info/' + deliveryContext.id,
                single: '{{ url('purchase-request') }}/delivery/' + deliveryContext.id,
            };
            var url = urlMap[deliveryContext.mode] || urlMap.single;
            var successText = deliveryContext.mode === 'edit' ?
                'Info pengiriman berhasil diperbarui.' : 'Your file has been deliveried.';

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
