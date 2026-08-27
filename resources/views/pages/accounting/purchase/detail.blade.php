@extends('layouts.sales.app')
@section('title', 'Detail Purchase Order')
@section('content')
    @php
        $totalPph = $totalPph ?? 0;
        $hasDisc = $dPurchase->contains(fn($item) => $item->disc > 0);
    @endphp
    <div class="row invoice-preview">
        {{-- Main PO Document Card --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card mb-3 shadow-sm border-0">
                <div class="card-body p-4">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-0">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="PT Reftech Jaya Optima" width="60%">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 fw-bolder" style="font-size: 15px">PT Reftech Jaya Optima</p>
                            <div style="font-size: 12px; color: #555;">
                                <p class="mb-0">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                                <p class="mb-0">Bandung – Jawa Barat 40218</p>
                                <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>info@reftech.id &nbsp;|&nbsp; <i class="mdi mdi-web me-1" style="font-size:11px;"></i>www.reftech.id</p>
                                <p class="mb-0 mt-1" style="font-size:10.5px; color:#444; font-weight:500;">
                                    <i class="mdi mdi-certificate-outline me-1 text-primary"></i><span class="fw-bold" style="color:#696cff;">ISO Certified:</span> ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold mb-1" style="letter-spacing:2px; color:#696cff;">PURCHASE ORDER</h3>
                            <p class="mb-1 fw-bold text-dark" style="font-size:16px;">#{{ $purchase->no_po }}</p>
                            <p class="mb-1 fw-bold" style="font-size:13px; color:#0f172a !important;">
                                <i class="mdi mdi-calendar-blank-outline me-1 text-primary"></i>{{ Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }}
                            </p>
                            <div class="mb-1 mt-1">
                                <span class="badge bg-primary px-3 py-1 fs-6">PURCHASE ORDER</span>
                            </div>
                            @if ($purchase->category)
                                <p class="mb-0 text-muted" style="font-size:11px;">Category: {{ $purchase->category }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Accent Divider --}}
                    <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:14px 0 16px;"></div>

                    {{-- Vendor / Supplier + Ship To / Prepared By Boxes --}}
                    <div style="display:flex !important; align-items:stretch !important; gap:12px; margin-bottom:16px; font-size:12px;">
                        {{-- Vendor Box --}}
                        <div style="flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                            <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Vendor / Supplier</p>
                            <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">{{ $purchase->company ?: '-' }}</p>
                            @php
                                $vendorParts = [];
                                if ($purchase->attn) {
                                    $vendorParts[] = '<i class="mdi mdi-account-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">ATTN: ' . e($purchase->attn) . '</span>';
                                }
                                if ($purchase->phone || $purchase->mobile) {
                                    $vendorParts[] = '<i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($purchase->phone ?: $purchase->mobile) . '</span>';
                                }
                                if ($purchase->email) {
                                    $vendorParts[] = '<i class="mdi mdi-email-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($purchase->email) . '</span>';
                                }
                            @endphp
                            @if (count($vendorParts) > 0)
                                <p class="mb-1" style="font-size:11.5px; color:#333;">
                                    {!! implode(' &nbsp;|&nbsp; ', $vendorParts) !!}
                                </p>
                            @endif
                            @if ($purchase->address)
                                <div class="mb-0" style="display:flex; align-items:flex-start; font-size:11.5px; color:#222;">
                                    <i class="mdi mdi-map-marker-outline me-1" style="font-size:11px; color:#444; line-height:1.4; flex-shrink:0;"></i><span style="font-weight:500; line-height:1.4;">{{ $purchase->address }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Ship To / Terms Box --}}
                        <div style="min-width:260px; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                            <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Ship To & Commercial Terms</p>
                            <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">PT Reftech Jaya Optima</p>
                            <p class="mb-1" style="font-size:11.5px; color:#444;">
                                <i class="mdi mdi-truck-delivery-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">Delivery: {{ $purchase->delivery ?: '-' }}</span>
                            </p>
                            <p class="mb-0" style="font-size:11.5px; color:#222;">
                                <i class="mdi mdi-credit-card-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">Payment: {{ $purchase->payment ?: '-' }}</span>
                            </p>
                        </div>
                    </div>

                    <p class="mb-3" style="font-size:12px; color:#777; font-style:italic;">
                        Dear Sir/Madam, Please find below our official Purchase Order for the following items :
                    </p>

                    {{-- Items Table --}}
                    <div class="table-responsive rounded border mb-3">
                        <table class="table table-bordered m-0" style="width:100%; font-size:12px;">
                            <thead style="font-size:11px; background:#eeeeff; color:#3d3d8f;">
                                <tr>
                                    <th class="text-center py-2" style="width:5%; font-weight:700; border-color:#d0d0ff;">No.</th>
                                    <th class="text-center py-2" style="width:45%; font-weight:700; border-color:#d0d0ff;">Item Description</th>
                                    <th class="text-center py-2" style="width:12%; font-weight:700; border-color:#d0d0ff;">Qty</th>
                                    <th class="text-center py-2" style="width:18%; font-weight:700; border-color:#d0d0ff;">Price (IDR)</th>
                                    @if ($hasDisc)
                                        <th class="text-center py-2" style="width:7%; font-weight:700; border-color:#d0d0ff;">Disc</th>
                                    @endif
                                    <th class="text-center py-2" style="width:13%; font-weight:700; border-color:#d0d0ff;">Amount (IDR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 0; @endphp
                                @foreach ($dPurchase as $product)
                                    @php $no++; @endphp
                                    <tr style="font-size: 12px">
                                        <td class="text-center align-top py-2">{{ $no }}</td>
                                        <td class="align-top py-2">
                                            <p class="mb-0 fw-semibold" style="font-size: 12px; color:#111;">
                                                {{ $product->product }}
                                            </p>
                                        </td>
                                        <td class="text-center align-top py-2">
                                            <span class="fw-bold" style="color:#222;">{{ $product->qty }}</span> {{ $product->info_qty }}
                                        </td>
                                        <td class="text-end align-top py-2">
                                            Rp {{ number_format($product->price, 0, '', '.') }}
                                        </td>
                                        @if ($hasDisc)
                                            <td class="text-center align-top py-2">
                                                {{ $product->disc ? $product->disc . '%' : '-' }}
                                            </td>
                                        @endif
                                        <td class="text-end align-top py-2 fw-semibold" style="color:#111;">
                                            Rp {{ number_format($product->amount, 0, '', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Calculations & Notes --}}
                    @php
                        $tax = ($purchase->total * 11) / 100;
                        $noTax = $purchase->total - ($purchase->total * 11) / 100;
                        $dpp = ($noTax * 11) / 12;
                    @endphp

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded border h-100" style="background:#fafafa; font-size:12px;">
                                <p class="fw-bold mb-1 text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">
                                    <i class="mdi mdi-note-text-outline me-1 text-primary"></i> Note / Catatan
                                </p>
                                <p class="mb-0" style="color:#333; font-style:italic;">
                                    {{ $purchase->note ?: 'Tidak ada catatan khusus.' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-bordered m-0" style="font-size: 12px; border-color: #c5c5c5;">
                                    <tbody>
                                        <tr>
                                            <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">SUBTOTAL</td>
                                            <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle; width: 55%;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Rp</span>
                                                    <span class="fw-semibold">{{ number_format($purchase->subtotal, 0, '', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @if ($purchase->diskon > 0)
                                            <tr>
                                                <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">DISCOUNT</td>
                                                <td class="py-1_5 px-3 text-danger" style="border-color: #c5c5c5; background: #ffffff; vertical-align: middle;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>- Rp</span>
                                                        <span class="fw-semibold">{{ number_format($purchase->diskon, 0, '', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($purchase->vat > 0)
                                            <tr>
                                                <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">DPP NILAI LAIN</td>
                                                <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>Rp</span>
                                                        <span class="fw-semibold">{{ $dpp == '0' ? '0' : number_format($dpp, 0, '', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">VAT 12%</td>
                                                <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>Rp</span>
                                                        <span class="fw-semibold">{{ $tax == '0' ? '0' : number_format($tax, 0, '', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($totalPph > 0)
                                            <tr>
                                                <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">TOTAL PPH</td>
                                                <td class="py-1_5 px-3 text-danger" style="border-color: #c5c5c5; background: #ffffff; vertical-align: middle;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>- Rp</span>
                                                        <span class="fw-semibold">{{ number_format($totalPph, 0, '', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-end fw-bolder text-uppercase py-2 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #000; font-size: 13px; vertical-align: middle;">TOTAL PRICE</td>
                                            <td class="py-2 px-3 fw-bold" style="border-color: #c5c5c5; background: #ffffff; color: #000; font-size: 13px; vertical-align: middle;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold">Rp</span>
                                                    <span class="fw-bold fs-6">{{ $purchase->total == '0' ? '0' : number_format($purchase->total, 0, '', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Signatures --}}
                    <div class="row pt-3 text-center" style="font-size:12px;">
                        <div class="col-6">
                            <p class="fw-bold mb-1" style="color:#333;">Authorized By.</p>
                            <div class="my-1">
                                <img src="{{ url('') . '/asset/sign/ttdAngel.jpg' }}" alt="TTD Angel" height="70">
                            </div>
                            <p class="fw-bold mb-0" style="color:#111;">PT Reftech Jaya Optima</p>
                        </div>
                        <div class="col-6">
                            <div class="pt-2">
                                <p class="fw-bold mb-1" style="color:#333;">Accepted By Vendor.</p>
                                <div style="height:70px;"></div>
                                <p class="fw-bold mb-0" style="color:#111;">{{ $purchase->attn ?: '-' }}</p>
                                <p class="text-muted mb-0" style="font-size:11px;">{{ $purchase->company }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat Goods Receipt & Retur --}}
            @if ($productIns->count())
                <div class="card mt-4 border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-truck-check-outline me-2 text-primary fs-4"></i> Riwayat Goods Receipt &amp; Retur
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @foreach ($productIns as $productIn)
                            <div class="border rounded p-3 mb-3 {{ $loop->last ? 'mb-0' : '' }}">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <div>
                                        <a href="{{ route('product-in.show', $productIn->id) }}" class="fw-bold text-primary text-decoration-none">
                                            {{ $productIn->no_product_in }}
                                        </a>
                                        <span class="text-muted small ms-2">No DO: {{ $productIn->no_do ?? '-' }}</span>
                                    </div>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($productIn->date)->format('d-m-Y') }}</span>
                                </div>

                                @if ($productIn->detail->count())
                                    <div class="mb-2">
                                        <div class="text-muted small fw-bold mb-1">Item Diterima (masuk stok):</div>
                                        @foreach ($productIn->detail as $d)
                                            <span class="badge bg-label-success me-1 mb-1">
                                                {{ $d->detailProduct->product->commodity ?? '-' }} ({{ $d->detailProduct->replacement ?? '-' }}) &times; {{ $d->qty }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                @foreach ($productIn->return as $retur)
                                    <div class="mt-2 p-2 rounded bg-label-danger">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-danger small">
                                                <i class="mdi mdi-keyboard-return me-1"></i>Retur {{ $retur->no_return }}
                                            </span>
                                            @if ($retur->status == 1)
                                                <span class="badge bg-label-success">Selesai</span>
                                            @else
                                                <span class="badge bg-label-warning">Menunggu Proses</span>
                                            @endif
                                        </div>
                                        @foreach ($retur->detail as $rd)
                                            <div class="small text-dark">
                                                {{ $rd->replacement->product->commodity ?? '-' }} ({{ $rd->replacement->replacement ?? '-' }}) &times; {{ $rd->qty }}
                                                @if ($rd->note)
                                                    <span class="text-muted">&mdash; {{ $rd->note }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

                                @if ($productIn->detail->isEmpty() && $productIn->return->isEmpty())
                                    <span class="text-muted small">Tidak ada item.</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar Actions --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body">
                    @if (!$purchase->id_purchase_request && $purchase->receipt_status == 'Pending')
                        @if (!$purchase->on_delivery_at)
                            <a href="#" class="btn btn-info text-white d-grid w-100 mb-3 waves-effect" id="btnUnitDelivery">
                                <i class="mdi mdi-truck-delivery me-1"></i> On Delivery
                            </a>
                        @else
                            <div class="alert alert-success py-2 px-3 mb-3 small">
                                <i class="mdi mdi-check-circle-outline me-1"></i> On Delivery
                                ({{ \Carbon\Carbon::parse($purchase->on_delivery_at)->format('d-m-Y') }})
                                @if ($purchase->on_delivery_cargo)
                                    <br>{{ $purchase->on_delivery_cargo }}{{ $purchase->on_delivery_no_resi ? ' — Resi: ' . $purchase->on_delivery_no_resi : '' }}
                                @endif
                            </div>
                            @if ($purchase->category == 'Unit')
                                <a class="btn btn-success d-grid w-100 mb-3 waves-effect"
                                    href="{{ route('unit-product-in.goods-receipt-form', $purchase->id) }}">
                                    Terima Barang (Unit)
                                </a>
                            @else
                                <a class="btn btn-success d-grid w-100 mb-3 waves-effect"
                                    href="{{ route('purchase.goods-receipt-direct', $purchase->id) }}">
                                    Terima Barang (GR)
                                </a>
                            @endif
                        @endif
                    @endif
                    @if ($sourcePr)
                        @if ($prDeliveryDone)
                            <div class="alert alert-success py-2 px-3 mb-3 small">
                                <i class="mdi mdi-check-circle-outline me-1"></i> Info pengiriman untuk PO ini sudah dikirim.
                            </div>
                        @else
                            <a href="#" class="btn btn-info text-white d-grid w-100 mb-3 waves-effect" id="btnPoDelivery">
                                <i class="mdi mdi-truck-delivery me-1"></i> On Delivery
                            </a>
                        @endif
                        <a href="{{ route('purchase-request.show', $sourcePr->id_pending) }}" class="btn btn-label-secondary d-grid w-100 mb-3 waves-effect">
                            <i class="mdi mdi-file-document-outline me-1"></i> Lihat Purchase Request
                        </a>
                    @endif
                    <a class="btn btn-primary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('purchase.show_print', $purchase->id) }}">
                        Download / Print
                    </a>
                    <a class="btn btn-label-info d-grid w-100 mb-3 waves-effect"
                        href="{{ route('purchase.edit', $purchase->id) }}">
                        Edit PO
                    </a>
                    <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-purchase mb-3"
                        data-id="{{ $purchase->id }}">Delete PO</a>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body">
                    <h6 class="fw-bold text-dark mb-2"><i class="mdi mdi-receipt-text-outline me-1 text-primary"></i>Invoice Supplier</h6>
                    @if ($purchase->no_invoice_supplier)
                        <p class="mb-1 small text-muted">No. Invoice</p>
                        <p class="fw-bold text-dark mb-2">{{ $purchase->no_invoice_supplier }}</p>
                        @if ($purchase->invoice_file)
                            <a href="{{ asset('storage/' . $purchase->invoice_file) }}" target="_blank" class="btn btn-label-secondary btn-sm d-grid w-100 mb-2 waves-effect">
                                <i class="mdi mdi-file-eye-outline me-1"></i> Lihat File Invoice
                            </a>
                        @endif
                        <a href="#" class="btn btn-label-info d-grid w-100 waves-effect" data-bs-toggle="modal" data-bs-target="#modalUploadInvoice">
                            <i class="mdi mdi-pencil-outline me-1"></i> Ubah Invoice
                        </a>
                    @else
                        <p class="small text-muted mb-2">Invoice dari supplier belum diinput.</p>
                        <a href="#" class="btn btn-info text-white d-grid w-100 waves-effect" data-bs-toggle="modal" data-bs-target="#modalUploadInvoice">
                            <i class="mdi mdi-upload me-1"></i> Upload Invoice
                        </a>
                    @endif
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    @if ($totalPph > 0)
                        <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete-pph mb-3"
                            data-id="{{ $purchase->id }}">Delete PPH</a>
                    @else
                        <a type="button" data-bs-toggle="modal" data-bs-target="#addPph"
                            class="d-grid w-100 waves-effect mb-3">
                            <button type="button" class="btn btn-twitter">
                                Input PPH 23
                            </button>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('components.modal.purchase.pph')

    <div class="modal fade" id="modalUploadInvoice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('purchase.upload-invoice', $purchase->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Invoice Supplier — {{ $purchase->no_po }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger py-2 px-3 small mb-3">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label for="noInvoiceSupplier" class="form-label">No. Invoice <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="noInvoiceSupplier" name="no_invoice_supplier"
                                value="{{ old('no_invoice_supplier', $purchase->no_invoice_supplier) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="invoiceFile" class="form-label">
                                File Invoice
                                @if (!$purchase->invoice_file) <span class="text-danger">*</span> @endif
                            </label>
                            <input type="file" class="form-control" id="invoiceFile" name="invoice_file"
                                accept=".pdf,.jpg,.jpeg,.png" {{ $purchase->invoice_file ? '' : 'required' }}>
                            <div class="form-text">Maksimal 5MB, format PDF/JPG/PNG.</div>
                            @if ($purchase->invoice_file)
                                <div class="form-text">File saat ini: <a href="{{ asset('storage/' . $purchase->invoice_file) }}" target="_blank">lihat file</a> (biarkan kosong kalau tidak mau ganti).</div>
                            @endif
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

    @if (!$purchase->id_purchase_request && $purchase->receipt_status == 'Pending' && !$purchase->on_delivery_at)
        <div class="modal fade" id="modalUnitDelivery" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form id="unitDeliveryForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Info Pengiriman — {{ $purchase->no_po }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="unitDeliveryCargo" class="form-label">Cargo / Ekspedisi</label>
                                <input type="text" class="form-control" id="unitDeliveryCargo" name="cargo" required>
                            </div>
                            <div class="mb-3">
                                <label for="unitDeliveryNoResi" class="form-label">No. Resi</label>
                                <input type="text" class="form-control" id="unitDeliveryNoResi" name="no_resi">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">On Delivery</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($sourcePr && !$prDeliveryDone)
        <div class="modal fade" id="modalPoDelivery" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form id="poDeliveryForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Info Pengiriman — {{ $purchase->no_po }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3 mb-2">
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1">Tipe (dari supplier)</label>
                                    <input type="text" class="form-control" value="{{ $prDeliveryType }}" disabled>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1">Tgl Pembelian (dari tgl PO)</label>
                                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }}" disabled>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="poDeliveryCargo" class="form-label">Cargo / Ekspedisi</label>
                                <input type="text" class="form-control" id="poDeliveryCargo" name="cargo" required>
                            </div>
                            <div class="mb-3">
                                <label for="poDeliveryNoResi" class="form-label">No. Resi</label>
                                <input type="text" class="form-control" id="poDeliveryNoResi" name="no_resi">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">On Delivery</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/dropzone/dropzone.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <style>
        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/dropzone/dropzone.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-file-upload.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
    <script>
        $('#backButton').click(function() {
            window.history.back();
        });

        @if ($errors->has('no_invoice_supplier') || $errors->has('invoice_file'))
            document.addEventListener('DOMContentLoaded', function () {
                new bootstrap.Modal(document.getElementById('modalUploadInvoice')).show();
            });
        @endif

        var unitDeliveryModalEl = document.getElementById('modalUnitDelivery');
        if (unitDeliveryModalEl) {
            var unitDeliveryModal = new bootstrap.Modal(unitDeliveryModalEl);

            $('#btnUnitDelivery').on('click', function(e) {
                e.preventDefault();
                unitDeliveryModal.show();
            });

            $('#unitDeliveryForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    'url': '{{ route('purchase.delivery-unit', $purchase->id) }}',
                    'type': 'POST',
                    'data': {
                        '_method': 'PATCH',
                        '_token': '{{ csrf_token() }}',
                        'cargo': $('#unitDeliveryCargo').val(),
                        'no_resi': $('#unitDeliveryNoResi').val(),
                    },
                    success: function(response) {
                        if (response == 1) {
                            unitDeliveryModal.hide();
                            Swal.fire({
                                icon: "success",
                                title: "Delivery succed!",
                                text: "Info pengiriman berhasil disimpan.",
                                customClass: {
                                    confirmButton: "btn btn-success waves-effect",
                                },
                            })
                            window.setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Gagal menyimpan info pengiriman.'
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = 'Gagal menyimpan info pengiriman.';
                        if (xhr.status === 422 && xhr.responseJSON) {
                            if (xhr.responseJSON.message) message = xhr.responseJSON.message;
                            else if (xhr.responseJSON.errors) message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        Swal.fire({ icon: 'error', title: 'Oops...', text: message });
                    }
                });
            });
        }

        var poDeliveryModalEl = document.getElementById('modalPoDelivery');
        if (poDeliveryModalEl) {
            var poDeliveryModal = new bootstrap.Modal(poDeliveryModalEl);

            $('#btnPoDelivery').on('click', function(e) {
                e.preventDefault();
                poDeliveryModal.show();
            });

            $('#poDeliveryForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    'url': '{{ route('purchase.delivery', $purchase->id) }}',
                    'type': 'POST',
                    'data': {
                        '_method': 'PATCH',
                        '_token': '{{ csrf_token() }}',
                        'cargo': $('#poDeliveryCargo').val(),
                        'no_resi': $('#poDeliveryNoResi').val(),
                    },
                    success: function(response) {
                        if (response == 1) {
                            poDeliveryModal.hide();
                            Swal.fire({
                                icon: "success",
                                title: "Delivery succed!",
                                text: "Info pengiriman berhasil disimpan.",
                                customClass: {
                                    confirmButton: "btn btn-success waves-effect",
                                },
                            })
                            window.setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Gagal menyimpan info pengiriman.'
                            });
                        }
                    },
                    error: function(xhr) {
                        var message = 'Gagal menyimpan info pengiriman.';
                        if (xhr.status === 422 && xhr.responseJSON) {
                            if (xhr.responseJSON.message) message = xhr.responseJSON.message;
                            else if (xhr.responseJSON.errors) message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                        }
                        Swal.fire({ icon: 'error', title: 'Oops...', text: message });
                    }
                });
            });
        }

        $(document).on('click', '.delete-purchase', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('purchase') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '{{ route('purchase.index') }}';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
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

        $(document).on('click', '.delete-pph', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('purchase') }}/delete-pph/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'PATCH',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/purchase/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
