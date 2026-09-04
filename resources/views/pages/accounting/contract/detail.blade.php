@extends('layouts.sales.app')
@section('title', ($contract->type == 'Order' ? 'Confirm Order' : 'Selling Contract') . ' — ' . $contract->no_contract)

@section('content')
    @php
        $isKojisha  = ($quote->pic?->client?->info ?? '') === 'Kojisha' || $contract->type == 'Order';
        $docHeading = $contract->type == 'Order' ? 'CONFIRM ORDER' : 'SELLING CONTRACT';
        $docNoun    = $contract->type == 'Order' ? 'Confirm Order' : 'Selling Contract';
        $entityName = $isKojisha ? 'PT Kojisha Innotiv Indonesia' : 'PT Reftech Jaya Optima';
        $isApproved = $contract->level == '1';
        $hasTax     = $quote->tax != '0';
    @endphp

    {{-- Breadcrumb & Top Bar --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('contract.index') }}" class="text-muted">Accounting</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('contract.index') }}" class="text-muted">Contracts</a>
                    </li>
                    <li class="breadcrumb-item active fw-semibold text-primary">
                        {{ $docNoun }}
                    </li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark">
                    {{ $contract->no_contract ?: 'Contract #' . $contract->id }}
                </h4>
                @if ($isApproved)
                    <span class="badge bg-label-success rounded-pill px-3 py-1 fs-7 d-inline-flex align-items-center gap-1">
                        <i class="mdi mdi-check-decagram fs-6"></i> Approved
                    </span>
                @else
                    <span class="badge bg-label-warning rounded-pill px-3 py-1 fs-7 d-inline-flex align-items-center gap-1">
                        <i class="mdi mdi-clock-outline fs-6"></i> Pending Approval
                    </span>
                @endif
                <span class="badge {{ $contract->type == 'Order' ? 'bg-label-info' : 'bg-label-primary' }} rounded-pill px-2.5 py-1 fs-7">
                    {{ $contract->type }}
                </span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('quotation/' . $quote->id) }}" class="btn btn-outline-secondary btn-sm waves-effect">
                <i class="mdi mdi-file-document-outline me-1"></i> View Quotation
            </a>
            @if ($isApproved)
                <a href="{{ route('contract.print', $contract->id) }}" target="_blank" class="btn btn-primary btn-sm waves-effect">
                    <i class="mdi mdi-printer-outline me-1"></i> Print / Download PDF
                </a>
            @endif
        </div>
    </div>

    <div class="row invoice-preview">
        {{-- Document Sheet Column --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="contract-paper-card">
                {{-- Header --}}
                <div class="doc-header">
                    <div class="brand-info">
                        <div class="brand-logo">
                            @if ($contract->type == 'Selling')
                                <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="PT Reftech Jaya Optima">
                            @else
                                <img src="{{ asset('/asset') }}/logo/Logo-update-size.png" alt="PT Kojisha Innotiv Indonesia">
                            @endif
                        </div>
                        <div class="brand-name">{{ $entityName }}</div>
                        <div class="brand-address">
                            @if ($contract->type == 'Selling')
                                <p class="mb-0">Taman Kopo Indah V, Ruko Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                                <p class="mb-0">Telp: 022 54417653 &nbsp;|&nbsp; Email: info@reftech.id</p>
                                @if ($hasTax)
                                    <p class="mb-0"><strong>NPWP:</strong> 07.372.857.1-842.9000</p>
                                @endif
                            @else
                                <p class="mb-0">Jl. Nancep No. 45A, Setu, Cibitung - Kab. Bekasi 17320</p>
                                <p class="mb-0">Telp: +62 812-1000-0997 &nbsp;|&nbsp; Email: admin@kojisha.com</p>
                                @if ($hasTax)
                                    <p class="mb-0"><strong>NPWP:</strong> 96.484.859.2-413.000</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="doc-title-block">
                        <h1 class="doc-title">{{ $docHeading }}</h1>
                        <div class="doc-number">#{{ $contract->no_contract }}</div>
                        <div class="doc-date">Date: {{ Carbon\Carbon::parse($contract->date)->format('d-m-Y') }}</div>
                    </div>
                </div>

                {{-- Info Section (Quote To & Order Info) --}}
                <div class="info-section">
                    {{-- Quote To --}}
                    <div class="info-card">
                        <div class="info-card-title">Customer / Quote To</div>
                        <div class="info-card-company">{{ $quote->pic?->client?->company ?? '-' }}</div>
                        <div class="info-row">
                            <span class="label">Attn:</span>
                            <span class="value">{{ $quote->pic?->name_pic ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Phone:</span>
                            <span class="value">{{ $quote->pic?->phone_pic ?? $quote->pic?->client?->phone ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Email:</span>
                            <span class="value">{{ $quote->pic?->email_pic ?? $quote->pic?->client?->email ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Address:</span>
                            <span class="value">{{ $quote->pic?->client?->address ?? '-' }}</span>
                        </div>
                    </div>

                    {{-- Contract Details --}}
                    <div class="info-card">
                        <div class="info-card-title">Order Information</div>
                        <div class="info-row">
                            <span class="label">Quotation:</span>
                            <span class="value">
                                <a href="{{ url('quotation/' . $quote->id) }}" class="fw-bold text-primary">
                                    {{ $quote->no_quote }} <i class="mdi mdi-open-in-new" style="font-size: 11px;"></i>
                                </a>
                            </span>
                        </div>
                        <div class="info-row">
                            <span class="label">Seller:</span>
                            <span class="value">{{ $entityName }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Sales:</span>
                            <span class="value">{{ $quote->sales?->name ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Tax Status:</span>
                            <span class="value">{{ $hasTax ? 'PPN ' . $quote->tax . '% (Taxable)' : 'Non-PPN (0%)' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Type:</span>
                            <span class="value">{{ $quote->type }}</span>
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                @if ($quote->type == 'Sparepart')
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 4%; text-align: center;">No</th>
                                <th style="width: 50%;">Item Description</th>
                                <th style="width: 16%; text-align: right;">Price (IDR)</th>
                                <th style="width: 8%; text-align: center;">Qty</th>
                                <th style="width: 6%; text-align: center;">Disc</th>
                                <th style="width: 16%; text-align: right;">Amount (IDR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 0; @endphp
                            @foreach ($dquote as $product)
                                @php $no++; @endphp
                                <tr>
                                    <td style="text-align: center; color: #64748b;">{{ $no }}</td>
                                    <td>
                                        <div class="item-title">
                                            @if ($product->id_equivalent == '0')
                                                -
                                            @else
                                                {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
                                            @endif
                                        </div>
                                        <div class="item-desc">{{ $product->detail_product }}</div>
                                    </td>
                                    <td style="text-align: right;">
                                        {{ $product->amount == 0 ? 'SBO' : number_format($product->price, 0, '', '.') }}
                                    </td>
                                    <td style="text-align: center; font-weight: 600;">
                                        {{ $product->qty }} {{ $product->info_qty }}
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $product->disc }}%
                                    </td>
                                    <td style="text-align: right; font-weight: 700;">
                                        {{ number_format($product->amount, 0, '', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 4%; text-align: center;">No</th>
                                <th style="width: 52%;">Item Description</th>
                                <th style="width: 10%; text-align: center;">Qty</th>
                                <th style="width: 17%; text-align: right;">Price (IDR)</th>
                                <th style="width: 17%; text-align: right;">Amount (IDR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $abjad = 64; @endphp
                            @foreach ($subQuote as $subJudul)
                                @php
                                    $no = 0;
                                    $abjad++;
                                @endphp
                                <tr class="subtitle-row">
                                    <td style="text-align: center; font-weight: 700; color: #0284c7;">{{ chr($abjad) }}</td>
                                    <td colspan="4" style="font-weight: 700;">{{ $subJudul->subtitle }}</td>
                                </tr>
                                @foreach ($subJudul->detail as $product)
                                    @php $no++; @endphp
                                    <tr>
                                        <td style="text-align: center; color: #64748b;">{{ $no }}</td>
                                        <td>
                                            <div class="item-title">{{ $product->product }}</div>
                                            @if ($product->detail != '-')
                                                <div class="item-desc">{{ $product->detail }}</div>
                                            @endif
                                        </td>
                                        <td style="text-align: center; font-weight: 600;">
                                            {{ $product->qty }} {{ $product->info_qty }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($product->price, 0, '', '.') }}
                                        </td>
                                        <td style="text-align: right; font-weight: 700;">
                                            {{ number_format($product->amount, 0, '', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                @endif

                {{-- Totals Section (Right Aligned) --}}
                @php
                    $afterDisc = $quote->diskon > 0 ? $quote->subtotal - $quote->diskon : $quote->subtotal;
                    $vat = $quote->tax != 0 ? ($afterDisc * $quote->tax) / 100 : 0;
                @endphp
                <div class="totals-section">
                    <table class="totals-table">
                        <tr>
                            <td class="label">Subtotal:</td>
                            <td class="val">Rp {{ number_format($quote->subtotal, 0, '', '.') }}</td>
                        </tr>
                        @if ($quote->diskon > 0)
                            <tr>
                                <td class="label" style="color: #dc2626;">Discount:</td>
                                <td class="val" style="color: #dc2626;">- Rp {{ number_format($quote->diskon, 0, '', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="label">After Discount:</td>
                                <td class="val">Rp {{ number_format($afterDisc, 0, '', '.') }}</td>
                            </tr>
                        @endif
                        @if ($hasTax)
                            <tr>
                                <td class="label">VAT / Tax ({{ $quote->tax }}%):</td>
                                <td class="val">{{ 'Rp ' . number_format($vat, 0, '', '.') }}</td>
                            </tr>
                        @endif
                        @if ($quote->shipping > 0)
                            <tr>
                                <td class="label">Shipping:</td>
                                <td class="val">Rp {{ number_format($quote->shipping, 0, '', '.') }}</td>
                            </tr>
                        @endif
                        <tr class="grand-total-row">
                            <td class="label">{{ $hasTax ? 'TOTAL (INC PPN):' : 'TOTAL (EXC PPN):' }}</td>
                            <td class="val">Rp {{ number_format($quote->harga_total, 0, '', '.') }}</td>
                        </tr>
                    </table>
                </div>

                {{-- Single Term & Condition Card --}}
                @if (isset($quote->termncon[0]))
                    <div class="terms-card">
                        <div class="terms-card-header">TERM &amp; CONDITION</div>
                        <div class="terms-card-body">
                            <div class="term-row">
                                <span class="term-label">Validity of Quote</span>
                                <span class="term-sep">:</span>
                                <span class="term-val">{{ $quote->termncon[0]->validity ?: '-' }}</span>
                            </div>
                            <div class="term-row">
                                <span class="term-label">Price</span>
                                <span class="term-sep">:</span>
                                <span class="term-val">{{ $quote->termncon[0]->pricing ?: '-' }}</span>
                            </div>
                            <div class="term-row">
                                <span class="term-label">Delivery Process</span>
                                <span class="term-sep">:</span>
                                <span class="term-val">{{ $quote->termncon[0]->delivery_process ?: '-' }}</span>
                            </div>
                            <div class="term-row">
                                <span class="term-label">Payment</span>
                                <span class="term-sep">:</span>
                                <span class="term-val">{{ $quote->termncon[0]->payment ?: '-' }}</span>
                            </div>
                            @if (!empty($quote->termncon[0]->note))
                                <div class="term-row">
                                    <span class="term-label">Note</span>
                                    <span class="term-sep">:</span>
                                    <span class="term-val">{{ $quote->termncon[0]->note }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Thank you for your business Note (Moved to bottom) --}}
                <div class="business-thanks-note">
                    <p class="thanks-title">Thank you for your business!</p>
                    <p class="thanks-desc mb-0">Dokumen ini merupakan konfirmasi kesepakatan penjualan/pesanan resmi antara <strong>{{ $entityName }}</strong> dan <strong>{{ $quote->pic?->client?->company ?? 'Customer' }}</strong>.</p>
                </div>

                {{-- Signatures Section --}}
                <div class="signature-section">
                    {{-- Authorized By --}}
                    <div class="signature-box">
                        <div class="signature-label">Authorized By,</div>
                        <div class="signature-img-wrap">
                            @if ($contract->type == 'Selling')
                                @if ($hasTax)
                                    <img src="{{ asset('/asset') }}/contract/sign-irene.jpeg" alt="Signature Irene">
                                @else
                                    <img src="{{ asset('/asset') }}/sign/ttdirene.jpg" alt="Signature Irene">
                                @endif
                            @else
                                <img src="{{ asset('/asset') }}/contract/sign-dedeh.png" alt="Signature Dedeh">
                            @endif
                        </div>
                        <div class="signature-name">{{ $contract->type == 'Selling' ? 'Mrs. Irene' : 'Dedeh Sulastri' }}</div>
                        <div class="signature-role">{{ $entityName }}</div>
                    </div>

                    {{-- Accepted By Customer --}}
                    <div class="signature-box">
                        <div class="signature-label">Accepted By Customer,</div>
                        <div class="signature-img-wrap">
                            {{-- Blank area for physical stamp & sign --}}
                        </div>
                        <div class="signature-name">{{ $quote->pic?->name_pic ?: '..............................' }}</div>
                        <div class="signature-role">{{ $quote->pic?->client?->company ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Actions & Info --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            {{-- Action Buttons Card --}}
            <div class="card shadow-sm border mb-3" style="border-radius: 8px; border-color: #e2e8f0 !important;">
                <div class="card-header py-3 px-3.5 border-bottom" style="background-color: #f8fafc;">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-1.5" style="font-size: 13px;">
                            <i class="mdi mdi-gesture-tap-button text-primary fs-5"></i>
                            <span>Actions</span>
                        </h6>
                        <span class="badge {{ $isApproved ? 'bg-label-success' : 'bg-label-warning' }} rounded-pill" style="font-size: 10.5px;">
                            {{ $isApproved ? 'Approved' : 'Pending' }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-2">
                        @if (!$isApproved)
                            <button type="button" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2.5 shadow-sm waves-effect"
                                data-bs-toggle="modal" data-bs-target="#acceptContract{{ $contract->id }}">
                                <i class="mdi mdi-check-circle-outline fs-5"></i>
                                <span class="fw-semibold">Approve {{ $docNoun }}</span>
                            </button>
                        @else
                            <a class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2.5 shadow-sm waves-effect" target="_blank"
                                href="{{ route('contract.print', $contract->id) }}">
                                <i class="mdi mdi-printer-outline fs-5"></i>
                                <span class="fw-semibold">Print / Download PDF</span>
                            </a>
                        @endif

                        <a href="{{ url('quotation/' . $quote->id) }}"
                            class="btn btn-label-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2 waves-effect text-dark">
                            <i class="mdi mdi-file-document-outline fs-5 text-primary"></i>
                            <span>View Quotation</span>
                        </a>

                        <a href="{{ route('contract.index') }}"
                            class="btn btn-label-secondary w-100 d-flex align-items-center justify-content-center gap-2 py-2 waves-effect text-dark">
                            <i class="mdi mdi-format-list-bulleted fs-5 text-secondary"></i>
                            <span>All Contracts</span>
                        </a>

                        <hr class="my-1 border-light">

                        <button type="button" class="btn btn-label-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2 waves-effect delete-contract"
                            data-id="{{ $contract->id }}" data-quote="{{ $quote->id }}">
                            <i class="mdi mdi-trash-can-outline fs-5"></i>
                            <span>{{ $isApproved ? 'Delete Contract' : 'Reject Contract' }}</span>
                        </button>
            </div>

            {{-- Online Customer Signature Card --}}
            <div class="card shadow-sm border mb-3" style="border-radius: 8px; border-color: #e2e8f0 !important;">
                <div class="card-header py-3 px-3.5 border-bottom d-flex align-items-center justify-content-between" style="background-color: #f8fafc;">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-1.5" style="font-size: 13px;">
                        <i class="mdi mdi-draw text-primary fs-5"></i>
                        <span>Customer Signature</span>
                    </h6>
                    @if ($contract->isSignedByCustomer())
                        <span class="badge bg-label-success rounded-pill px-2 py-0.5" style="font-size: 11px;">
                            <i class="mdi mdi-check-decagram"></i> Signed
                        </span>
                    @else
                        <span class="badge bg-label-warning rounded-pill px-2 py-0.5" style="font-size: 11px;">
                            <i class="mdi mdi-clock-outline"></i> Waiting
                        </span>
                    @endif
                </div>
                <div class="card-body p-3">
                    @if ($contract->isSignedByCustomer())
                        <div class="p-2.5 rounded bg-lighter border mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                <i class="mdi mdi-account-check text-success fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 12.5px;">{{ $contract->customer_signer_name }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">{{ $contract->customer_signer_position }}</div>
                                </div>
                            </div>
                            <div class="text-muted" style="font-size: 11px;">
                                <i class="mdi mdi-calendar-clock text-muted me-1"></i>{{ date('d-m-Y H:i', strtotime($contract->signed_at)) }} WIB
                            </div>
                            @if ($contract->customer_ip)
                                <div class="text-muted" style="font-size: 10.5px;">
                                    <i class="mdi mdi-map-marker-radius-outline text-muted me-1"></i>IP: {{ $contract->customer_ip }}
                                </div>
                            @endif
                            @if ($contract->customer_signature)
                                <div class="mt-2 text-center p-2 bg-white rounded border">
                                    <img src="{{ asset($contract->customer_signature) }}" alt="Customer Signature" style="max-height: 50px; max-width: 100%; object-fit: contain;">
                                </div>
                            @endif
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <a href="{{ $contract->sign_url }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5 py-1.5 waves-effect">
                                <i class="mdi mdi-eye-outline"></i>
                                <span>Lihat Halaman TTD</span>
                            </a>
                            <form action="{{ route('contract.reset-signature', $contract->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus / mereset tanda tangan customer ini? Customer akan dapat menandatangani ulang.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5 py-1.5 waves-effect">
                                    <i class="mdi mdi-delete-outline"></i>
                                    <span>Hapus / Reset TTD Customer</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <p class="text-muted mb-2" style="font-size: 11.5px; line-height: 1.4;">
                            Kirim tautan berikut ke customer agar dapat memeriksa kontrak &amp; tanda tangan secara digital:
                        </p>

                        <div class="input-group input-group-sm mb-2.5">
                            <input type="text" class="form-control" id="contract-sign-url" value="{{ $contract->sign_url }}" readonly style="font-size: 11px;">
                            <button class="btn btn-primary" type="button" id="btn-copy-sign-url" title="Salin Link">
                                <i class="mdi mdi-content-copy"></i>
                            </button>
                        </div>

                        @php
                            $picPhone = preg_replace('/[^0-9]/', '', ($quote->pic?->phone ?? ''));
                            if (str_starts_with($picPhone, '0')) {
                                $picPhone = '62' . substr($picPhone, 1);
                            }
                            $clientComp = $quote->pic?->client?->company ?? '';
                            $picName = $quote->pic?->name ?? '';
                            $waMessage = rawurlencode("Halo Bapak/Ibu " . ($picName ?: '') . " (" . $clientComp . "),\n\nBerikut kami lampirkan tautan dokumen " . $docNoun . " (" . ($contract->no_contract ?: '') . ").\nSilakan periksa rincian dokumen dan bubuhi tanda tangan digital melalui tautan berikut:\n" . $contract->sign_url . "\n\nTerima kasih.");
                            $waLink = "https://wa.me/" . ($picPhone ?: '') . "?text=" . $waMessage;
                        @endphp

                        <div class="d-flex flex-column gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5 py-1.5 waves-effect" id="btn-copy-link-action">
                                <i class="mdi mdi-link-variant"></i>
                                <span>Salin Link TTD</span>
                            </button>
                            <a href="{{ $waLink }}" target="_blank" class="btn btn-success btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5 py-1.5 waves-effect text-white">
                                <i class="mdi mdi-whatsapp fs-5"></i>
                                <span>Kirim via WhatsApp</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Contract Info Widget --}}
            <div class="card shadow-sm border mb-3" style="border-radius: 8px; border-color: #e2e8f0 !important;">
                <div class="card-header py-3 px-3.5 border-bottom" style="background-color: #f8fafc;">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-1.5" style="font-size: 13px;">
                        <i class="mdi mdi-information-outline text-primary fs-5"></i>
                        <span>Document Info</span>
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-2" style="font-size: 12px;">
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-pound text-muted me-1"></i>Contract No</span>
                            <span class="fw-bold text-dark font-monospace">{{ $contract->no_contract }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-shape-outline text-muted me-1"></i>Type</span>
                            <span class="badge {{ $contract->type == 'Order' ? 'bg-label-info' : 'bg-label-primary' }} rounded-pill">
                                {{ $contract->type }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-calendar-blank-outline text-muted me-1"></i>Issue Date</span>
                            <span class="fw-medium text-dark">{{ Carbon\Carbon::parse($contract->date)->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-start pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-domain text-muted me-1"></i>Client</span>
                            <span class="fw-semibold text-dark text-end" style="max-width: 60%;">{{ $quote->pic?->client?->company ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-file-document-outline text-muted me-1"></i>Quotation</span>
                            <a href="{{ url('quotation/' . $quote->id) }}" class="fw-semibold text-primary">
                                {{ $quote->no_quote }}
                            </a>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-1">
                            <span class="text-muted"><i class="mdi mdi-cash-multiple text-muted me-1"></i>Total Value</span>
                            <span class="fw-bold text-primary font-monospace fs-6">Rp {{ number_format($quote->harga_total, 0, '', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $result = '';
        if ($contract->type == 'Selling' && $contract->quotation->tax == '11') {
            $result = $formattedNumberSP;
        } elseif ($contract->type == 'Selling' && $contract->quotation->tax == '0') {
            $result = $formattedNumberSNP;
        } elseif ($contract->type == 'Order' && $contract->quotation->tax == '11') {
            $result = $formattedNumberCP;
        } elseif ($contract->type == 'Order' && $contract->quotation->tax == '0') {
            $result = $formattedNumberCNP;
        }
    @endphp
    @include('components.modal.accounting.accept-contract')
@endsection

@push('after-style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        /* Document Paper Card */
        .contract-paper-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 28px 32px;
            color: #0f172a;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 12px;
            line-height: 1.45;
        }

        /* Header */
        .contract-paper-card .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 14px;
            border-bottom: 2px solid #0f172a;
        }

        .contract-paper-card .brand-logo img {
            max-height: 48px;
            width: auto;
            object-fit: contain;
        }

        .contract-paper-card .brand-name {
            font-weight: 700;
            font-size: 14px;
            color: #0f172a;
            margin-top: 4px;
        }

        .contract-paper-card .brand-address {
            font-size: 11px;
            color: #475569;
            line-height: 1.5;
            margin-top: 2px;
        }

        .contract-paper-card .doc-title-block {
            text-align: right;
        }

        .contract-paper-card .doc-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .contract-paper-card .doc-number {
            font-size: 13px;
            font-weight: 700;
            color: #0284c7;
        }

        .contract-paper-card .doc-date {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        /* Info Section (Quote To & Order Info) */
        .contract-paper-card .info-section {
            display: flex;
            gap: 14px;
            margin: 14px 0;
        }

        .contract-paper-card .info-card {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
        }

        .contract-paper-card .info-card-title {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0284c7;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .contract-paper-card .info-card-company {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .contract-paper-card .info-row {
            display: flex;
            font-size: 11px;
            line-height: 1.5;
            color: #334155;
            margin-bottom: 2px;
        }

        .contract-paper-card .info-row .label {
            width: 75px;
            color: #64748b;
            flex-shrink: 0;
        }

        .contract-paper-card .info-row .value {
            font-weight: 500;
        }

        /* Items Table */
        .contract-paper-card .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0 10px 0;
            font-size: 11.5px;
        }

        .contract-paper-card .items-table thead th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 8px 10px;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #cbd5e1;
        }

        .contract-paper-card .items-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .contract-paper-card .items-table .subtitle-row td {
            background-color: #f1f5f9;
            font-weight: 700;
            color: #0f172a;
            padding: 6px 10px;
        }

        .contract-paper-card .items-table .item-title {
            font-weight: 700;
            color: #0f172a;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .contract-paper-card .items-table .item-desc {
            font-size: 10.5px;
            color: #475569;
            line-height: 1.45;
            white-space: pre-wrap;
        }

        /* Totals Block (Right Aligned) */
        .contract-paper-card .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 4px;
            margin-bottom: 12px;
        }

        .contract-paper-card .totals-table {
            width: 300px;
            border-collapse: collapse;
            font-size: 11.5px;
        }

        .contract-paper-card .totals-table td {
            padding: 3px 0;
        }

        .contract-paper-card .totals-table .label {
            color: #64748b;
        }

        .contract-paper-card .totals-table .val {
            text-align: right;
            font-weight: 600;
            color: #0f172a;
        }

        .contract-paper-card .totals-table .grand-total-row td {
            padding-top: 8px;
            border-top: 1.5px solid #0f172a;
            font-weight: 800;
            font-size: 13px;
            color: #0f172a;
        }

        .contract-paper-card .totals-table .grand-total-row .val {
            font-size: 14px;
            color: #0284c7;
        }

        /* Single Term & Condition Card */
        .contract-paper-card .terms-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-top: 12px;
            overflow: hidden;
        }

        .contract-paper-card .terms-card-header {
            background: #f1f5f9;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
        }

        .contract-paper-card .terms-card-body {
            padding: 8px 12px;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .contract-paper-card .term-row {
            display: flex;
            font-size: 11px;
            line-height: 1.5;
            color: #334155;
        }

        .contract-paper-card .term-row .term-label {
            width: 140px;
            color: #64748b;
            flex-shrink: 0;
        }

        .contract-paper-card .term-row .term-sep {
            width: 14px;
            color: #64748b;
            flex-shrink: 0;
        }

        .contract-paper-card .term-row .term-val {
            font-weight: 500;
            color: #0f172a;
        }

        /* Thank you note below */
        .contract-paper-card .business-thanks-note {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 3px solid #0284c7;
            border-radius: 4px;
            padding: 8px 12px;
            margin-top: 12px;
            font-size: 11px;
            color: #475569;
        }

        .contract-paper-card .business-thanks-note .thanks-title {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .contract-paper-card .business-thanks-note .thanks-desc {
            line-height: 1.4;
        }

        /* Signatures */
        .contract-paper-card .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
        }

        .contract-paper-card .signature-box {
            width: 45%;
            text-align: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
        }

        .contract-paper-card .signature-label {
            font-size: 11.5px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .contract-paper-card .signature-img-wrap {
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px 0;
        }

        .contract-paper-card .signature-img-wrap img {
            max-height: 60px;
            max-width: 120px;
            object-fit: contain;
        }

        .contract-paper-card .signature-name {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            margin-top: 4px;
        }

        .contract-paper-card .signature-role {
            font-size: 10.5px;
            color: #64748b;
        }

        @media (max-width: 768px) {
            .contract-paper-card {
                padding: 16px;
            }
            .contract-paper-card .info-section {
                flex-direction: column;
            }
            .contract-paper-card .totals-section {
                justify-content: flex-start;
            }
            .contract-paper-card .totals-table {
                width: 100%;
            }
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        $(document).on('click', '.delete-contract', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var quoteId = $(this).data('quote');
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
                        'url': '{{ url('contract') }}/' + id,
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
                                    text: "Your contract has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                }).then(function() {
                                    window.location.href = '/quotation/' + quoteId;
                                });
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

        function copySignUrl() {
            var input = document.getElementById('contract-sign-url');
            if (!input) return;
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Berhasil Disalin!',
                    text: 'Tautan tanda tangan customer siap dikirimkan.',
                    timer: 2000,
                    showConfirmButton: false,
                });
            }).catch(function () {
                document.execCommand('copy');
                Swal.fire({
                    icon: 'success',
                    title: 'Link Disalin!',
                    timer: 2000,
                    showConfirmButton: false,
                });
            });
        }

        $(document).on('click', '#btn-copy-sign-url, #btn-copy-link-action', function (e) {
            e.preventDefault();
            copySignUrl();
        });
    </script>
@endpush
