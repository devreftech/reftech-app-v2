@extends('layouts.sales.app')
@section('title', 'Detail Smart Quote')
@section('content')

@php
    use Illuminate\Support\Facades\Storage;
    $statusMap = [
        'draft'        => ['label' => 'DRAFT',        'color' => 'secondary', 'solid' => true],
        'sent'         => ['label' => 'SENT',          'color' => 'info',      'solid' => true],
        'negotiation'  => ['label' => 'NEGOTIATION',   'color' => 'warning',   'solid' => true],
        'revision'     => ['label' => 'REVISI',        'color' => 'primary',   'solid' => true],
        'hot_prospect' => ['label' => 'HOT PROSPECT',  'color' => 'danger',    'solid' => true],
        'po_received'  => ['label' => 'PO RECEIVED',   'color' => 'success',   'solid' => true],
        'loss'         => ['label' => 'LOSS',          'color' => 'dark',      'solid' => true],
    ];
    $st = $statusMap[$quote->status] ?? ['label' => strtoupper($quote->status), 'color' => 'secondary'];
@endphp

{{-- Revision selector (only shows when more than 1 version exists) --}}
@if ($allVersions->count() > 1)
<div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
    <span class="text-muted small fw-semibold">Versi:</span>
    @foreach ($allVersions as $v)
        @php $vLabel = $v->revision_number === 0 ? $v->no_quote : 'Revisi ' . $v->revision_number; @endphp
        @if ($v->id === $quote->id)
            <span class="badge bg-primary">{{ $vLabel }}</span>
        @else
            <a href="{{ route('unit-quotation.show', $v->id) }}"
               class="badge bg-label-secondary text-decoration-none">{{ $vLabel }}</a>
        @endif
    @endforeach
</div>
@endif

<div class="row invoice-preview">

    {{-- ── LEFT: Invoice Card ── --}}
    <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
        <div class="card invoice-preview-card mb-3 shadow-sm border-0">
            <div class="card-body p-4">
                {{-- Header --}}
                <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-0">
                    <div class="mb-xl-0 pb-1">
                        @if ($quote->client?->info === 'Kojisha')
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt="" width="60%">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 fw-bolder" style="font-size: 15px">PT Kojisha Innotiv Indonesia</p>
                            <div style="font-size: 12px; color: #555;">
                                <p class="mb-0">Jl. Nancep No. 45A, Setu</p>
                                <p class="mb-0">Cibitung - Kab. Bekasi 17320</p>
                                <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>+62 812-1000-0997 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>admin@kojisha.com</p>
                            </div>
                        @else
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="" width="60%">
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
                        @endif
                    </div>
                    <div class="text-end">
                        <h3 class="fw-bold mb-1" style="letter-spacing:2px; color:#696cff;">QUOTATION</h3>
                        <p class="mb-1 fw-bold text-dark" style="font-size:16px;">#{{ $quote->no_quote }}</p>
                        <p class="mb-1 fw-bold" style="font-size:13px; color:#0f172a !important;">
                            <i class="mdi mdi-calendar-blank-outline me-1 text-primary"></i>{{ $quote->date?->format('d-m-Y') }}
                        </p>
                        @if ($quote->title)
                            <p class="mb-1 fw-semibold" style="font-size:12.5px; color:#333;">{{ $quote->title }}</p>
                            <div class="form-check form-switch d-flex justify-content-end align-items-center gap-1 mb-1">
                                <label class="form-check-label text-muted" for="toggle-hide-title" style="font-size:10.5px;">Hide Title (Print)</label>
                                <input class="form-check-input" type="checkbox" role="switch" id="toggle-hide-title"
                                    data-id="{{ $quote->id }}" @checked($quote->hide_title)>
                            </div>
                        @endif
                        <div class="mb-1 mt-1">
                            <span class="badge bg-{{ $st['color'] }} px-3 py-1 fs-6">{{ $st['label'] }}</span>
                        </div>
                        @if ($quote->no_pr)
                            <p class="mb-0 text-muted" style="font-size:11px;">No. PR: {{ $quote->no_pr }}</p>
                        @endif
                        @if ($quote->type || $quote->week)
                            <p class="mb-0 text-muted" style="font-size:11px;">
                                Type: {{ $quote->type }}{{ $quote->type && $quote->week ? ' | ' : '' }}{{ $quote->week ? 'Week ' . $quote->week : '' }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Accent Divider --}}
                <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:14px 0 16px;"></div>

                {{-- Quote To + Prepared By Box --}}
                <div style="display:flex !important; align-items:stretch !important; gap:12px; margin-bottom:16px; font-size:12px;">
                    <div style="flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                        <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Quote To</p>
                        <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">
                            {{ $quote->client?->company ?? '-' }}
                            @if ($quote->plant)
                                <span class="badge bg-label-primary ms-1" style="font-size:9.5px; vertical-align:middle;">{{ strtoupper($quote->plant->name) }}</span>
                            @endif
                        </p>
                        @php
                            $contactParts = [];
                            if ($quote->pic?->name_pic) {
                                $contactParts[] = '<i class="mdi mdi-account-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->pic->name_pic) . '</span>';
                            }
                            if ($quote->pic?->phone_pic) {
                                $contactParts[] = '<i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->pic->phone_pic) . '</span>';
                            }
                            if ($quote->client?->email) {
                                $contactParts[] = '<i class="mdi mdi-email-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($quote->client->email) . '</span>';
                            }
                        @endphp
                        @if (count($contactParts) > 0)
                            <p class="mb-1" style="font-size:11.5px; color:#333;">
                                {!! implode(' &nbsp;|&nbsp; ', $contactParts) !!}
                            </p>
                        @endif
                        @if ($quote->address || $quote->plant)
                            <div class="mb-0" style="display:flex; align-items:flex-start; font-size:11.5px; color:#222;">
                                <i class="mdi mdi-map-marker-outline me-1" style="font-size:11px; color:#444; line-height:1.4; flex-shrink:0;"></i><span style="font-weight:500; line-height:1.4;">{{ $quote->address ?? $quote->plant?->address }} {{ $quote->plant ? '(' . $quote->plant->name . ')' : '' }}</span>
                            </div>
                        @endif
                    </div>
                    <div style="min-width:240px; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                        <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Prepared By</p>
                        <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">{{ $quote->sales?->name ?? 'Alifya Syahrani' }}</p>
                        <p class="mb-1 fw-medium" style="font-size:11.5px; color:#444;">
                            <i class="mdi mdi-briefcase-outline me-1" style="font-size:11px; color:#444;"></i>{{ $quote->sales?->title ?? 'Sales Engineer' }}
                        </p>
                        @if ($quote->sales?->email || $quote->sales?->phone)
                            <p class="mb-0" style="font-size:11.5px; color:#222;">
                                @if ($quote->sales?->phone)
                                    <i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">{{ $quote->sales->phone }}</span>
                                @endif
                                @if ($quote->sales?->phone && $quote->sales?->email) &nbsp;|&nbsp; @endif
                                @if ($quote->sales?->email)
                                    <i class="mdi mdi-email-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">{{ $quote->sales->email }}</span>
                                @endif
                            </p>
                        @endif
                    </div>
                </div>

                <p class="mb-3" style="font-size:12px; color:#777; font-style:italic;">
                    Dear Sir/Madam, Please find bellow our price quotation for the following :
                </p>

                {{-- Items Table + Financial Summary — per Opsi kalau quotation ini
                     punya >1 opsi perbandingan harga, atau 1x aja kalau biasa. --}}
                @if ($quote->options->isNotEmpty())
                    @foreach ($quote->options as $i => $option)
                        @if ($i > 0)
                            <div style="border-top:2px dashed #d0d0ff; margin:28px 0 20px;"></div>
                        @endif
                        @if ($quote->options->count() > 1)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary" style="font-size:11px;">Opsi {{ $i + 1 }}</span>
                            <h6 class="mb-0 fw-bold text-dark" style="font-size:13px;">{{ $option->title }}</h6>
                        </div>
                        @endif
                        @include('pages.unit-quotation.partials.option-table', ['items' => $option->details, 'optTotals' => $option])
                    @endforeach
                @else
                    @include('pages.unit-quotation.partials.option-table', ['items' => $quote->details, 'optTotals' => $quote])
                @endif

                {{-- Note (Remarks) --}}
                @if ($quote->note)
                <div style="border:1px solid #e0e0e0; border-left:3px solid #696cff; border-radius:6px; padding:10px 14px; font-size:12px; color:#333; margin-bottom:14px; background:#fafafa;">
                    <p class="mb-1 fw-semibold text-uppercase" style="font-size:10px; color:#888; letter-spacing:.5px;">Remarks / Note</p>
                    @php
                        $noteLines = explode("\n", str_replace("\r", "", $quote->note));
                    @endphp
                    <div style="font-size:12px; color:#222; line-height:1.5;">
                        @foreach ($noteLines as $line)
                            @php
                                $trimmed = trim($line);
                            @endphp
                            @if (empty($trimmed))
                                <div style="height:3px;"></div>
                            @else
                                @php
                                    $hasBullet = preg_match('/^([•\-\*]|\d+[\.\)])\s*(.*)/u', $trimmed, $matches);
                                @endphp
                                @if ($hasBullet && !empty($matches[1]) && !empty($matches[2]))
                                    <div style="display:flex; align-items:flex-start; margin-bottom:3px;">
                                        <span style="flex-shrink:0; min-width:20px; color:#696cff; font-weight:600;">{{ $matches[1] }}</span>
                                        <span style="flex:1;">{{ $matches[2] }}</span>
                                    </div>
                                @else
                                    <div style="margin-bottom:3px;">{{ $line }}</div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Terms & Conditions --}}
                <div style="border:1px solid #e0e0e0; border-radius:6px; padding:12px 16px; font-size:12px; background:#fff; margin-bottom:16px;">
                    <p class="mb-2 fw-semibold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#888;">Term &amp; Condition</p>
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="width:160px; padding:3px 0; color:#555; vertical-align:top;">Validity of Quotation</td>
                            <td style="padding:3px 0; vertical-align:top;">: {{ $quote->validity ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; color:#555; vertical-align:top;">Price</td>
                            <td style="padding:3px 0; vertical-align:top;">: {{ $quote->pricing ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; color:#555; vertical-align:top;">Delivery Process</td>
                            <td style="padding:3px 0; vertical-align:top; white-space:pre-line;">: {{ $quote->delivery_process ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; color:#555; vertical-align:top;">Payment</td>
                            <td style="padding:3px 0; vertical-align:top;">: {{ $quote->payment ?? '-' }}</td>
                        </tr>
                        @if (!empty($quote->warranty))
                        <tr>
                            <td style="padding:3px 0; color:#555; vertical-align:top;">Warranty</td>
                            <td style="padding:3px 0; vertical-align:top;">: {{ $quote->warranty }}</td>
                        </tr>
                        @endif
                    </table>
                </div>

                {{-- Footer Banner --}}
                <div class="p-2 text-center rounded" style="background:#f4f4fe; border:1px solid #e0e0ff;">
                    <p class="mb-0 fw-bold" style="font-size:11px; color:#3d3d8f; letter-spacing:0.5px;">
                        COMPRESSED AIR SOLUTION : Sales &nbsp;|&nbsp; Rental &nbsp;|&nbsp; Maintenance &nbsp;|&nbsp; Air Audit &nbsp;|&nbsp; Installation
                    </p>
                </div>
            </div>
        </div>

        {{-- Activity Timeline & Discussion --}}
        @include('pages.unit-quotation.components.activity-timeline')

    </div>
    {{-- ── RIGHT: Action Sidebar ── --}}
    <div class="col-xl-3 col-md-4 col-12 invoice-actions">

        {{-- Action Card --}}
        @php
            // Kontrak unit: Reftech -> type=Selling, Kojisha -> type=Order. Jangan filter type.
            $isKojisha                = $quote->isKojisha();
            $contractNoun             = $isKojisha ? 'Confirm Order' : 'Selling Contract';
            $sellingContract          = $contracts->where('level', '1')->first();
            $requestedSellingContract = $contracts->where('level', '0')->first();
            $issuedInvoices  = $invoices->filter(fn($i) => !is_null($i->no_invoice));
            $pendingInvoices = $invoices->filter(fn($i) => is_null($i->no_invoice));
            $issuedTotal     = $issuedInvoices->sum(fn($i) => round($quote->total * floatval($i->percent ?? 100) / 100));
            $remaining       = $quote->total - $issuedTotal;
            $isOwnerAdmin    = Auth::user()->role === 'Admin' && $quote->sales?->role === 'Admin';
        @endphp
        @if (Auth::user()->role !== 'Accounting' && (Auth::user()->role !== 'Admin' || $quote->sales?->role === 'Admin'))
        <div class="card mb-3 border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-primary bg-gradient py-3 px-4 d-flex align-items-center justify-content-between text-white">
                <h6 class="card-title mb-0 fw-bold text-white d-flex align-items-center">
                    <i class="mdi mdi-lightning-bolt-outline me-2 fs-5"></i> Quick Actions
                </h6>
                <span class="badge bg-white text-primary fw-semibold" style="font-size: 10px;">CONTROLS</span>
            </div>

            <div class="card-body p-3">
                {{-- 1. Main Action: Download / Print PDF --}}
                <div class="mb-3">
                    <a href="{{ route('unit-quotation.print', $quote->id) }}" target="_blank"
                       class="btn btn-primary d-grid w-100 shadow-sm py-2"
                       style="background: linear-gradient(135deg, #696cff 0%, #3f42db 100%); border: none;">
                        <span class="d-flex align-items-center justify-content-center gap-1 fw-bold fs-6">
                            <i class="mdi mdi-printer-outline fs-5"></i> Print / Download PDF
                        </span>
                    </a>
                </div>

                {{-- 2. Edit & Revisi Row --}}
                @if (($quote->status !== 'po_received') && (Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin'))
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <a href="{{ route('unit-quotation.edit', $quote->id) }}"
                           class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-1">
                            <i class="mdi mdi-pencil-outline"></i> Edit
                        </a>
                    </div>
                    <div class="col-6">
                        @if (Auth::user()->role === 'Sales' || $isOwnerAdmin)
                            <form action="{{ route('unit-quotation.revise', $quote->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-info w-100 d-flex align-items-center justify-content-center gap-1"
                                    onclick="return confirm('Buat revisi dari quotation ini?')">
                                    <i class="mdi mdi-file-replace-outline"></i> Revisi
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Banner: quotation masih punya >1 opsi, belum bisa lanjut ke PO --}}
                @if ($quote->has_multiple_options)
                    <div class="p-3 rounded-3 mb-3 bg-label-warning border shadow-sm">
                        <div class="d-flex align-items-start gap-2">
                            <i class="mdi mdi-alert-outline fs-5"></i>
                            <div style="font-size:12px;">
                                <strong>Quotation ini masih punya {{ $quote->options->count() }} opsi.</strong>
                                Hapus opsi yang tidak dipilih customer dulu (lewat Edit) sebelum lanjut ke PO/Invoice/Contract.
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 3. Workflow Sub-section: Purchase Order & Contract --}}
                @if (!$quote->has_multiple_options && ((Auth::user()->role === 'Sales' && $quote->status !== 'po_received') || $quote->po_file || $sellingContract || $requestedSellingContract || (Auth::user()->role === 'Admin' || Auth::user()->role === 'Accounting')))
                <div class="p-3 rounded-3 mb-3 bg-white border shadow-sm">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-primary" style="font-size: 10px; letter-spacing: 0.5px;">
                            <i class="mdi mdi-file-document-check-outline me-1"></i> PO &amp; Contract
                        </span>
                        @if ($quote->po_file)
                            <span class="badge bg-label-success" style="font-size:9px;">PO ATTACHED</span>
                        @endif
                    </div>

                    {{-- Upload / View PO --}}
                    @if (Auth::user()->role === 'Sales' || $isOwnerAdmin)
                        <button type="button" id="btn-upload-po-wrap" class="btn btn-sm btn-label-success d-flex align-items-center justify-content-center w-100 mb-2 btn-upload-po-unit fw-semibold {{ $quote->status === 'po_received' ? 'd-none' : '' }}"
                            data-npwp="{{ $quote->client->npwp ?? '' }}"
                            data-client-url="{{ $quote->client->role == 'Leads' ? route('detail.leads', $quote->client->id) : route('existing.show', $quote->client->id) }}">
                            <i class="mdi mdi-file-upload-outline me-1"></i> Upload PO
                        </button>
                    @endif
                    <a href="#" id="btn-view-po-wrap"
                       data-url="{{ $quote->po_file ? Storage::url($quote->po_file) : '' }}"
                       onclick="openPdfViewer(this.dataset.url, 'File PO {{ $quote->no_quote ?? '' }}'); return false;"
                       class="btn btn-sm btn-label-secondary d-flex align-items-center justify-content-center w-100 mb-2 fw-semibold {{ $quote->po_file ? '' : 'd-none' }}">
                        <i class="mdi mdi-file-pdf-box text-danger me-1"></i> Lihat File PO
                    </a>

                    {{-- Selling Contract & SUO (Berdampingan) --}}
                    <div class="row g-2 mt-1">
                        {{-- Selling Contract Column --}}
                        <div class="col-6">
                            @if ($sellingContract)
                                <div class="btn-group w-100">
                                    <a class="btn btn-sm btn-label-primary fw-semibold text-truncate" href="{{ route('contract.show', $sellingContract->id) }}" title="Lihat Kontrak">
                                        <i class="mdi mdi-file-document-outline me-1"></i> Kontrak
                                    </a>
                                    <a class="btn btn-sm btn-outline-primary fw-semibold px-2" target="_blank" href="{{ route('contract.print', $sellingContract->id) }}" title="Unduh Kontrak">
                                        <i class="mdi mdi-download"></i>
                                    </a>
                                </div>
                            @elseif ($requestedSellingContract)
                                <div class="p-2 rounded-2 bg-warning-subtle text-warning-emphasis text-center" style="font-size: 10.5px;" title="Menunggu Accounting buat kontrak">
                                    <i class="mdi mdi-clock-outline me-1"></i> Wait Kontrak
                                </div>
                            @elseif (Auth::user()->role === 'Sales' && $quote->status !== 'po_received')
                                <a href="#" data-id="{{ $quote->id }}" class="btn btn-sm btn-label-primary d-flex align-items-center justify-content-center w-100 fw-semibold px-1 text-truncate request-selling-unit" title="Request {{ $contractNoun }}">
                                    <i class="mdi mdi-file-sign me-1"></i> {{ $contractNoun }}
                                </a>
                            @elseif (Auth::user()->role === 'Admin' || Auth::user()->role === 'Accounting')
                                <button type="button" class="btn btn-sm btn-label-primary d-flex align-items-center justify-content-center w-100 fw-semibold px-1 text-truncate"
                                    data-bs-toggle="modal" data-bs-target="#modalSellingContractUnit" title="Create {{ $contractNoun }}">
                                    <i class="mdi mdi-file-plus-outline me-1"></i> {{ $contractNoun }}
                                </button>
                            @endif
                        </div>

                        {{-- SUO Column --}}
                        <div class="col-6">
                            @if ($quote->suo)
                                <a class="btn btn-sm btn-outline-info d-flex align-items-center justify-content-center w-100 fw-semibold px-1 text-truncate"
                                    href="{{ route('suo.show', $quote->suo->id) }}" title="Lihat SUO ({{ $quote->suo->no_suo }})">
                                    <i class="mdi mdi-eye-outline me-1"></i> SUO ({{ $quote->suo->no_suo }})
                                </a>
                            @elseif ($quote->status !== 'po_received')
                                @if (Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin')
                                    <a href="#" data-id="{{ $quote->id }}"
                                        class="btn btn-sm btn-outline-dark d-flex align-items-center justify-content-center w-100 fw-semibold px-1 text-truncate ajukan-suo-unit" title="Ajukan SUO">
                                        <i class="mdi mdi-truck-fast-outline me-1"></i> Ajukan SUO
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- 4. Workflow Sub-section: Billing & Invoices --}}
                @if ($invoices->isNotEmpty())
                <div class="rounded-3 mb-3 overflow-hidden" style="border: 1px solid #dde1ff;">
                    {{-- Header --}}
                    <div class="d-flex align-items-center justify-content-between px-3 py-2" style="background: linear-gradient(90deg, #696cff 0%, #9c9eff 100%);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-receipt-text-outline text-white" style="font-size:15px;"></i>
                            <span class="fw-bold text-white" style="font-size:11.5px; letter-spacing:0.3px;">Billing & Invoices</span>
                        </div>
                        <span class="badge bg-white text-primary fw-bold" style="font-size:9.5px;">{{ $invoices->count() }} Invoice</span>
                    </div>

                    {{-- Progress Bar --}}
                    @php
                        $progressPct = $quote->total > 0 ? min(100, round($issuedTotal / $quote->total * 100)) : 0;
                    @endphp
                    <div class="px-3 pt-2 pb-1" style="background:#f6f7ff;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:10px; color:#666;">Billed</span>
                            <span style="font-size:10px; font-weight:600; color:#696cff;">{{ $progressPct }}%</span>
                        </div>
                        <div style="height:5px; background:#e0e0f0; border-radius:3px; overflow:hidden;">
                            <div style="width:{{ $progressPct }}%; height:100%; background:linear-gradient(90deg,#696cff,#9c9eff); border-radius:3px; transition:width .4s;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span style="font-size:9.5px; color:#888;">Rp {{ number_format($issuedTotal, 0, '', '.') }}</span>
                            <span style="font-size:9.5px; color:#888;">/ Rp {{ number_format($quote->total, 0, '', '.') }}</span>
                        </div>
                    </div>

                    {{-- Invoice List --}}
                    <div class="px-2 pt-1 pb-2" style="background:#fff;">
                        @if ($issuedInvoices->isNotEmpty())
                            @foreach ($issuedInvoices as $inv)
                                @php
                                    $invAmount = round($quote->total * floatval($inv->percent ?? 100) / 100);
                                    $isPaid = $inv->status_p;
                                    $badgeColor = $isPaid ? '#28a745' : '#696cff';
                                    $badgeBg    = $isPaid ? '#e8f8ed' : '#eef0ff';
                                @endphp
                                <a href="{{ route('invoice.show_unit', $inv->id) }}"
                                   class="d-flex align-items-center justify-content-between text-decoration-none rounded-2 px-2 py-2 mb-1"
                                   style="background:#f9f9ff; border:1px solid #e5e5ff; transition:background .15s;"
                                   onmouseover="this.style.background='#eef0ff'" onmouseout="this.style.background='#f9f9ff'">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:28px; height:28px; background:#696cff20; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                                            <i class="mdi mdi-file-document-outline" style="font-size:14px; color:#696cff;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size:11.5px; font-weight:700; color:#222;">#{{ $inv->no_invoice }}</div>
                                            <div style="font-size:10px; color:#888;">Rp {{ number_format($invAmount, 0, '', '.') }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        @if ($inv->type !== 'CT')
                                            @php
                                                $shortType = match($inv->type) {
                                                    'Balance Payment' => 'BP',
                                                    'Down Payment'    => 'DP',
                                                    default           => str_replace(['Balance Payment', 'Down Payment'], ['BP', 'DP'], $inv->type)
                                                };
                                            @endphp
                                            <span style="font-size:9.5px; font-weight:700; padding:2px 6px; border-radius:4px; background:#eef0ff; color:#696cff;">{{ $shortType }}</span>
                                        @endif
                                        <span style="font-size:9.5px; font-weight:600; padding:2px 7px; border-radius:4px; background:{{ $badgeBg }}; color:{{ $badgeColor }};">
                                            {{ $isPaid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="d-flex align-items-center gap-2 px-1 py-2 text-muted" style="font-size:11px;">
                                <i class="mdi mdi-clock-outline text-warning"></i>
                                <span>Menunggu invoice diterbitkan</span>
                            </div>
                        @endif

                        @if ($pendingInvoices->isNotEmpty())
                            <div class="d-flex align-items-center gap-2 px-2 py-1 rounded-2 mt-1" style="background:#fff8e1; border:1px solid #ffe57f; font-size:11px;">
                                <i class="mdi mdi-clock-sand text-warning" style="font-size:14px;"></i>
                                <span style="color:#8a6800; font-weight:500;">{{ $pendingInvoices->count() }} invoice menunggu terbit</span>
                            </div>
                        @endif

                        @if ($issuedInvoices->isNotEmpty() && $pendingInvoices->isEmpty() && $remaining > 0)
                            @if (Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin')
                                <button type="button"
                                    class="btn btn-sm w-100 mt-2 fw-semibold"
                                    style="background:#f0f2ff; color:#696cff; border:1.5px dashed #696cff; font-size:11.5px;"
                                    data-bs-toggle="modal" data-bs-target="#modalRequestNextInvoice">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i> Ajukan Invoice Selanjutnya
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
                @endif

                {{-- 5. Change Status Option --}}
                @if ((Auth::user()->role === 'Sales' || $isOwnerAdmin) && $quote->status !== 'po_received')
                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center w-100 py-1.5"
                        data-bs-toggle="modal" data-bs-target="#modalChangeStatus">
                        <i class="mdi mdi-swap-horizontal me-1"></i> Change Status
                    </button>
                @endif

                {{-- 5b. Detail Sales Order (hanya saat po_received & pendingPo sudah dibuat) --}}
                @if ($quote->status === 'po_received' && $pendingPo)
                    <a href="{{ route('pending-po.show', $pendingPo->id) }}"
                        class="btn btn-sm btn-outline-primary d-flex align-items-center justify-content-center w-100 mt-2 fw-semibold">
                        <i class="mdi mdi-truck-delivery-outline me-1"></i> Detail Sales Order
                    </a>
                @endif
            </div>

            {{-- 6. Danger Zone Footer (Delete / Cancel PO) --}}
            @if (Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin' || Auth::user()->role === 'Accounting')
            <div class="card-footer bg-light-subtle pt-2 pb-3 px-3 border-top">
                @if ($quote->status === 'po_received')
                    @if ($quote->cancel_request)
                        {{-- Pending approval state --}}
                        @if (Auth::user()->role === 'Accounting' || Auth::user()->role === 'Admin')
                            <p class="text-muted small mb-2 text-center" style="font-size: 11px;">
                                <i class="mdi mdi-alert-circle-outline text-warning me-1"></i>
                                Sales mengajukan pembatalan PO
                            </p>
                            <div class="row g-2">
                                <div class="col-6">
                                    <form action="{{ route('unit-quotation.approve-cancel', $quote->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-100"
                                            onclick="return confirm('Setujui pembatalan PO ini?')">
                                            <i class="mdi mdi-check"></i> Setuju
                                        </button>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <form action="{{ route('unit-quotation.reject-cancel', $quote->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                            <i class="mdi mdi-close"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-2 px-1" style="font-size: 11px;">
                                <i class="mdi mdi-clock-outline text-warning"></i>
                                <span class="text-muted">Menunggu persetujuan Accounting</span>
                            </div>
                        @endif
                    @else
                        {{-- Cancel PO button for Sales/Admin --}}
                        @if (Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin')
                            <form action="{{ route('unit-quotation.cancel-po', $quote->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center w-100"
                                    onclick="return confirm('Batalkan PO untuk penawaran ini? Tindakan ini tidak bisa dibatalkan.')">
                                    <i class="mdi mdi-cancel me-1"></i> Cancel PO
                                </button>
                            </form>
                        @endif
                    @endif
                @else
                    @if (Auth::user()->role === 'Sales' || Auth::user()->role === 'Admin')
                    <a href="#" class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center w-100 delete-quote"
                        data-id="{{ $quote->id }}">
                        <i class="mdi mdi-trash-can-outline me-1"></i> Delete Quotation
                    </a>
                    @endif
                @endif
            </div>
            @endif
        </div>
        @elseif ($quote->status === 'po_received' && $quote->cancel_request)
        {{-- Standalone Cancel PO Approval Card for Accounting / Admin --}}
        <div class="card mb-3 border-danger shadow-sm">
            <div class="card-header bg-danger text-white py-2 px-3">
                <h6 class="card-title mb-0 text-white fw-bold d-flex align-items-center">
                    <i class="mdi mdi-alert-circle-outline me-2"></i> Pengajuan Pembatalan PO
                </h6>
            </div>
            <div class="card-body p-3 text-center">
                <p class="text-muted small mb-3" style="font-size: 12px;">
                    Sales (<strong>{{ $quote->sales?->name }}</strong>) mengajukan pembatalan PO untuk penawaran ini.
                </p>
                <div class="row g-2">
                    <div class="col-6">
                        <form action="{{ route('unit-quotation.approve-cancel', $quote->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success w-100 fw-bold"
                                onclick="return confirm('Setujui pembatalan PO ini?')">
                                <i class="mdi mdi-check"></i> Setuju
                            </button>
                        </form>
                    </div>
                    <div class="col-6">
                        <form action="{{ route('unit-quotation.reject-cancel', $quote->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100 fw-bold">
                                <i class="mdi mdi-close"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Kanban Action Card --}}
        <div class="card mb-3">
            <div class="card-header py-3">
                <h5 class="mb-0">Action</h5>
            </div>
            <div class="card-body">
                @if ($kanbanTask)
                    <a href="{{ route('kanban.boards.show', $kanbanTask->board_id) }}?task_id={{ $kanbanTask->id }}"
                        class="btn btn-outline-success w-100">
                        <i class="mdi mdi-view-column-outline me-1"></i> Monitoring Project
                    </a>
                @else
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalPostToKanban">
                        <i class="mdi mdi-view-column-outline me-1"></i> Post to Kanban
                    </button>
                @endif
            </div>
        </div>

        {{-- Payment Card --}}
        @if ($payments->isNotEmpty() || $quote->status === 'po_received')
        <div class="card mb-3">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <h5 class="mb-0">Payment</h5>
                @php $paidTotal = $payments->sum('amount'); @endphp
                @if ($payments->isNotEmpty())
                    <span class="badge bg-label-success">Rp {{ number_format($paidTotal, 0, '', '.') }}</span>
                @endif
            </div>
            @if ($payments->isNotEmpty())
            <div class="card-body p-0">
                @foreach ($payments as $pay)
                <div class="d-flex align-items-start justify-content-between px-3 py-2 border-bottom" id="pay-row-{{ $pay->id }}">
                    <div>
                        <p class="mb-0 fw-semibold small">Rp {{ number_format($pay->amount, 0, '', '.') }}
                            @if ($pay->percent)
                                <span class="text-muted small">({{ $pay->percent }}%)</span>
                            @endif
                            @if ($pay->type)
                                <span class="badge bg-label-primary ms-1 small">{{ $pay->type }}</span>
                            @endif
                        </p>
                        @if ($pay->method)
                            <p class="mb-0 text-muted small">{{ $pay->method }}</p>
                        @endif
                        @if ($pay->note)
                            <p class="mb-0 text-muted small">{{ $pay->note }}</p>
                        @endif
                        <div class="mt-1">
                            @if ($pay->file)
                                <a href="#" onclick="openPdfViewer('{{ asset($pay->file) }}', 'Bukti Transfer'); return false;"
                                   class="badge bg-label-success text-decoration-none me-1">
                                    <i class="mdi mdi-file-check-outline"></i> Bukti Transfer
                                </a>
                            @else
                                <span class="badge bg-label-warning">Belum ada bukti</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-1 ms-2">
                        @if (!$pay->file)
                            <button type="button" class="btn btn-sm btn-icon btn-outline-success btn-upload-proof"
                                data-id="{{ $pay->id }}" title="Upload Bukti Transfer">
                                <i class="mdi mdi-upload"></i>
                            </button>
                        @endif
                        @if (in_array(Auth::user()->role, ['Admin', 'Accounting', 'Sales']) && $pay->level == 0)
                            <button type="button" class="btn btn-sm btn-icon btn-label-secondary btn-edit-payment"
                                data-id="{{ $pay->id }}"
                                data-type="{{ $pay->type }}"
                                data-method="{{ $pay->method }}"
                                data-amount="{{ $pay->amount }}"
                                data-percent="{{ $pay->percent }}"
                                data-note="{{ $pay->note }}"
                                data-tempo="{{ $pay->tempo }}"
                                title="Edit">
                                <i class="mdi mdi-pencil-outline"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-delete-payment"
                                data-id="{{ $pay->id }}" title="Hapus">
                                <i class="mdi mdi-delete-outline"></i>
                            </button>
                        @elseif ($pay->level == 1)
                            <span class="badge bg-label-success align-self-center" data-bs-toggle="tooltip" title="Sudah dikonfirmasi Accounting">
                                <i class="mdi mdi-lock-check-outline"></i> Confirmed
                            </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            @if ($quote->status === 'po_received' && Auth::user()->role === 'Sales')
            <div class="card-footer p-3">
                <button type="button" class="btn btn-outline-success d-flex align-items-center justify-content-center w-100 waves-effect"
                    data-bs-toggle="modal" data-bs-target="#modalAddPayment">
                    <i class="mdi mdi-cash-plus me-1"></i> Tambah Payment
                </button>
            </div>
            @endif
        </div>
        @endif

        {{-- Latest Quotation Status & Summary Card --}}
        @php
            $lastHistory = $quote->statusHistory->sortByDesc('id')->first();
            $statusKey   = $lastHistory ? $lastHistory->status : $quote->status;
            $currHst     = $hstMap[$statusKey] ?? ['label' => ucfirst(str_replace('_',' ',$statusKey)), 'color' => 'secondary', 'icon' => 'mdi-circle-outline'];
            
            $statusTitle = $currHst['label'];
            $statusColor = $currHst['color'];
            $statusIcon  = $currHst['icon'];
            $statusNote  = $lastHistory?->note;
            $statusTime  = $lastHistory?->created_at;
        @endphp
        <div class="card mb-3 border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-light border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi {{ $statusIcon }} text-{{ $statusColor }} fs-5"></i>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">Latest Status</h6>
                </div>
                <span class="badge bg-{{ $statusColor }} px-2.5 py-1 text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                    {{ $statusTitle }}
                </span>
            </div>
            <div class="card-body p-3">
                <div class="p-3 rounded-3 mb-3" style="background: rgba(var(--bs-{{ $statusColor }}-rgb), 0.08); border: 1px dashed rgba(var(--bs-{{ $statusColor }}-rgb), 0.3);">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="fw-bold text-{{ $statusColor }}" style="font-size: 13px;">
                            <i class="mdi {{ $statusIcon }} me-1"></i>{{ $statusTitle }}
                        </span>
                        @if ($statusTime)
                            <small class="text-muted" style="font-size: 10px;" title="{{ $statusTime->format('d M Y H:i') }}">
                                <i class="mdi mdi-clock-outline me-1"></i>{{ $statusTime->diffForHumans() }}
                            </small>
                        @endif
                    </div>
                    @if ($statusNote)
                        <div class="mt-2 text-dark small" style="white-space: pre-wrap; line-height: 1.5; font-size: 12px;">
                            <i class="mdi mdi-text-subject me-1 text-muted"></i>{{ $statusNote }}
                        </div>
                    @else
                        <div class="mt-1 text-muted small" style="font-size: 11px; font-style: italic;">
                            No additional note provided for this status update.
                        </div>
                    @endif
                </div>

                {{-- Key Document Info List --}}
                <div class="d-flex flex-column gap-2" style="font-size: 12px;">
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                        <span class="text-muted"><i class="mdi mdi-account-outline me-1"></i> Sales Person</span>
                        <span class="fw-semibold text-dark">{{ $quote->sales?->name ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                        <span class="text-muted"><i class="mdi mdi-calendar-outline me-1"></i> Issue Date</span>
                        <span class="fw-semibold text-dark">{{ $quote->date?->format('d M Y') ?? '-' }}</span>
                    </div>
                    @if ($quote->no_pr)
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-pound me-1"></i> PR Number</span>
                            <span class="fw-semibold text-dark">{{ $quote->no_pr }}</span>
                        </div>
                    @endif
                    @if ($quote->po_number)
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted"><i class="mdi mdi-clipboard-text-outline me-1"></i> PO Number</span>
                            <span class="fw-semibold text-success">{{ $quote->po_number }}</span>
                        </div>
                    @endif
                    @if ($quote->payment_method)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="mdi mdi-credit-card-outline me-1"></i> Payment Method</span>
                            <span class="fw-semibold text-dark">{{ $quote->payment_method }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>



    </div>
    {{-- END RIGHT --}}

</div>

{{-- Modal Upload PO --}}
<div class="modal fade" id="modalUploadPO" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-upload me-1"></i> Upload Purchase Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUploadPoUnit" action="{{ route('unit-quotation.upload-po', $quote->id) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No. PO <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="po_number"
                               placeholder="Masukkan nomor PO dari customer"
                               value="{{ old('po_number', $quote->po_number) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal PO <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="po_date"
                               value="{{ old('po_date', now()->toDateString()) }}" required>
                        <div class="form-text text-muted">Default hari ini, bisa diubah sesuai tanggal PO sebenarnya.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select class="form-select" name="payment_method" id="select-payment-method" required>
                            <option value="" disabled selected>-- Pilih Metode Pembayaran --</option>
                            <option value="CBD">CBD (Cash Before Delivery)</option>
                            <option value="COD">COD (Cash On Delivery)</option>
                            <option value="DP 50% & Pelunasan NET 50">DP 50% &amp; Pelunasan NET 50</option>
                            <option value="DP 30% & Pelunasan NET 70">DP 30% &amp; Pelunasan NET 70</option>
                            <option value="Tempo">Tempo</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="tempo-days-group">
                        <label class="form-label fw-semibold">Jangka Tempo (hari) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="input-tempo-days"
                                   min="1" placeholder="misal: 30">
                            <span class="input-group-text">Hari</span>
                        </div>
                        <div class="form-text text-muted">Masukkan jumlah hari jangka tempo pembayaran.</div>
                    </div>
                    {{-- hidden field final yang dikirim ke server --}}
                    <input type="hidden" name="payment_method_final" id="input-payment-method-final">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Invoice Pertama <span class="text-danger">*</span></label>
                        <select class="form-select" name="invoice_type" id="select-invoice-type" required>
                            <option value="" disabled selected>-- Pilih --</option>
                            <option value="DP">Down Payment (DP)</option>
                            <option value="CT">Full Payment</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="dp-percent-group">
                        <label class="form-label fw-semibold">Persentase DP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="dp_percent" id="dp-percent-input"
                                   min="1" max="99" value="50" placeholder="50">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text" id="dp-amount-preview">DP: Rp ... | Sisa: Rp ...</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File PO (PDF) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="po_file" accept=".pdf" required>
                        <div class="form-text">Maksimal 5MB, format PDF.</div>
                    </div>
                    <div class="alert alert-info mb-0 py-2">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Status quotation akan otomatis berubah ke <strong>PO Received</strong> setelah upload.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitUploadPoUnit" class="btn btn-success">
                        <i class="mdi mdi-upload me-1"></i> Upload & Konfirmasi PO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Create Selling Contract / Confirm Order (Admin/Accounting) --}}
@if (!isset($sellingContract) || !$sellingContract)
@php
    $ppnCode  = $quote->tax ? 'P' : 'NP';
    if ($isKojisha) {
        $seqCode   = $quote->tax ? ($unitNumbers['nextCP'] ?? '001') : ($unitNumbers['nextCNP'] ?? '001');
        $suffixDoc = 'CO/KII';
    } else {
        $seqCode   = $quote->tax ? ($unitNumbers['nextSP'] ?? '001') : ($unitNumbers['nextSNP'] ?? '001');
        $suffixDoc = 'SELLCTX/RJO';
    }
@endphp
<div class="modal fade" id="modalSellingContractUnit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('unit-quotation.selling-contract', $quote->id) }}" method="POST">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title">Create {{ $contractNoun }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <h5 class="mb-1">{{ $quote->no_quote }}</h5>
                    <p class="text-muted mb-3">{{ $quote->client?->company }}</p>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold">No. {{ $contractNoun }}</label>
                        <input type="text" class="form-control" name="no_contract"
                            value="{{ $seqCode }}/{{ $ppnCode }}/{{ $suffixDoc }}/{{ $thisYear }}"
                            required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary waves-effect">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Ajukan Invoice Selanjutnya --}}
@if (isset($issuedInvoices) && $issuedInvoices->isNotEmpty() && isset($remaining) && $remaining > 0)
<div class="modal fade" id="modalRequestNextInvoice" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-plus-circle-outline me-1"></i> Ajukan Invoice Selanjutnya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('unit-quotation.request-next-invoice', $quote->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Sisa yang belum ditagih</label>
                        <div class="form-control bg-light fw-bold">Rp {{ number_format($remaining, 0, ',', '.') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan Invoice <span class="text-danger">*</span></label>
                        <select class="form-select" name="label" id="next-inv-label" required>
                            <option value="Balance Payment">Balance Payment</option>
                            <option value="Down Payment 2">Down Payment 2</option>
                            <option value="Down Payment 3">Down Payment 3</option>
                            <option value="Pelunasan">Pelunasan</option>
                            <option value="__custom__">Lainnya (isi sendiri)...</option>
                        </select>
                        <input type="text" class="form-control mt-2 d-none" id="next-inv-label-custom"
                               placeholder="Tulis keterangan invoice..." maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Persentase dari sisa <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="percent" id="next-inv-percent"
                                   min="1" max="100" value="100" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text" id="next-inv-amount">
                            = Rp {{ number_format($remaining, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-send-outline me-1"></i> Ajukan Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Modal Post to Kanban --}}
<div class="modal fade" id="modalPostToKanban" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('unit-quotation.post-to-kanban', $quote->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="mdi mdi-view-column-outline me-1"></i> Post to Kanban</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($kanbanBoards->isEmpty())
                        <p class="text-muted mb-0">Anda belum jadi anggota board Kanban manapun.</p>
                    @else
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Board / Project <span class="text-danger">*</span></label>
                            <select class="form-select" name="board_id" id="kanban-board-select" required>
                                <option value="">-- Pilih Board --</option>
                                @foreach ($kanbanBoards as $board)
                                    <option value="{{ $board->id }}">{{ $board->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kolom <span class="text-danger">*</span></label>
                            <select class="form-select" name="column_id" id="kanban-column-select" required disabled>
                                <option value="">-- Pilih Board dulu --</option>
                            </select>
                        </div>
                        <p class="text-muted small mb-0">
                            Kartu akan dibuat dengan judul "{{ $quote->no_quote }} — {{ $quote->client?->company }}" dan langsung tautkan ke quotation ini.
                        </p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" @if ($kanbanBoards->isEmpty()) disabled @endif>
                        <i class="mdi mdi-send-outline me-1"></i> Post
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
                @php
                    // Sinkronkan tipe payment default dengan Payment Method yang diisi waktu upload PO,
                    // tapi tetap bisa diganti manual lewat dropdown ini.
                    $addPaymentTypeMap = ['CBD' => 'CBD', 'COD' => 'COD', 'Tempo' => 'Tempo'];
                    $defaultAddPaymentType = $addPaymentTypeMap[$quote->payment_method]
                        ?? (str_starts_with($quote->payment_method ?? '', 'DP') ? 'DP' : '');
                @endphp
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Payment <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" id="add-payment-type" required>
                            <option value="" @selected($defaultAddPaymentType === '')>-- Pilih Tipe --</option>
                            <option value="DP" @selected($defaultAddPaymentType === 'DP')>DP (Down Payment)</option>
                            <option value="BP" @selected($defaultAddPaymentType === 'BP')>BP (Balance Payment)</option>
                            <option value="CBD" @selected($defaultAddPaymentType === 'CBD')>CBD (Cash Before Delivery)</option>
                            <option value="COD" @selected($defaultAddPaymentType === 'COD')>COD (Cash On Delivery)</option>
                            <option value="Tempo" @selected($defaultAddPaymentType === 'Tempo')>Tempo</option>
                        </select>
                    </div>
                    <div class="mb-3" id="tempo-group" style="display:none;">
                        <label class="form-label fw-semibold">Jangka Tempo (hari) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="tempo" min="1" placeholder="misal: 30">
                            <span class="input-group-text">hari</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select" name="method" id="add-payment-method" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="Transfer">Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Giro">Giro</option>
                            <option value="Escrow">Escrow</option>
                        </select>
                    </div>
                    <div class="mb-3" id="add-payment-escrow-channel-group" style="display:none;">
                        <label class="form-label fw-semibold">Akun Marketplace <span class="text-danger">*</span></label>
                        <select class="form-select" name="escrow_channel" id="add-payment-escrow-channel">
                            <option value="">-- Pilih Akun --</option>
                            <option value="Airend Center">Airend Center</option>
                            <option value="Parts Compressor">Parts Compressor</option>
                            <option value="Kojisha Filter">Kojisha Filter</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (IDR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" inputmode="numeric" class="form-control" name="amount" id="add-payment-amount"
                                   placeholder="Masukkan jumlah yang diterima" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Persentase <span class="text-muted small">(opsional)</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="percent" id="add-payment-percent"
                                   min="1" max="100" placeholder="misal 50">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan <span class="text-muted small">(opsional)</span></label>
                        <input type="text" class="form-control" name="note"
                               placeholder="misal: Down Payment, Pelunasan...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Payment --}}
<div class="modal fade" id="modalEditPayment" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-pencil-outline me-1"></i> Edit Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditPayment" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Payment <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" id="edit-payment-type" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="DP">DP (Down Payment)</option>
                            <option value="BP">BP (Balance Payment)</option>
                            <option value="CBD">CBD (Cash Before Delivery)</option>
                            <option value="COD">COD (Cash On Delivery)</option>
                            <option value="Tempo">Tempo</option>
                        </select>
                    </div>
                    <div class="mb-3" id="edit-tempo-group" style="display:none;">
                        <label class="form-label fw-semibold">Jangka Tempo (hari) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="tempo" id="edit-payment-tempo" min="1" placeholder="misal: 30">
                            <span class="input-group-text">hari</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select" name="method" id="edit-payment-method" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="Transfer">Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Giro">Giro</option>
                            <option value="Escrow">Escrow</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah (IDR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="amount" id="edit-payment-amount"
                                   min="1" step="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Persentase <span class="text-muted small">(opsional)</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="percent" id="edit-payment-percent"
                                   min="1" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan <span class="text-muted small">(opsional)</span></label>
                        <input type="text" class="form-control" name="note" id="edit-payment-note">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-check me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Upload Proof (hidden, shown via JS) --}}
<div class="modal fade" id="modalUploadProof" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="mdi mdi-upload me-1"></i> Upload Bukti Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">File Bukti <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="proof-file-input"
                           accept=".pdf,.jpg,.jpeg,.png">
                    <div class="form-text">PDF / JPG / PNG, maks 5MB</div>
                </div>
                <div id="proof-upload-msg"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btn-do-upload-proof">
                    <i class="mdi mdi-upload me-1"></i> Upload
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Change Status --}}
<div class="modal fade" id="modalChangeStatus" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('unit-quotation.change-status', $quote->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-floating form-floating-outline">
                        <select class="form-select" name="status">
                            <option value="sent"         {{ $quote->status === 'sent'         ? 'selected' : '' }}>Sent</option>
                            <option value="negotiation"  {{ $quote->status === 'negotiation'  ? 'selected' : '' }}>Negotiation</option>
                            <option value="revision"     {{ $quote->status === 'revision'     ? 'selected' : '' }}>Revisi</option>
                            <option value="hot_prospect" {{ $quote->status === 'hot_prospect' ? 'selected' : '' }}>Hot Prospect</option>
                            <option value="po_received"  {{ $quote->status === 'po_received'  ? 'selected' : '' }} {{ $quote->has_multiple_options ? 'disabled' : '' }}>PO Received{{ $quote->has_multiple_options ? ' (hapus opsi lain dulu)' : '' }}</option>
                            <option value="loss"         {{ $quote->status === 'loss'         ? 'selected' : '' }}>Loss</option>
                        </select>
                        <label>Status</label>
                    </div>
                    <div class="form-floating form-floating-outline mt-3">
                        <textarea class="form-control" name="note" style="height:80px" placeholder="Note (optional)"></textarea>
                        <label>Note (optional)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('components.modal.viewer.pdf')

{{-- Modal: Post to Sales Order — selalu di-render (tersembunyi secara default) supaya bisa
     langsung dibuka via JS begitu Upload PO sukses (AJAX), tanpa perlu reload halaman. --}}
@include('components.modal.unit-quotation.convert-po')

@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <style>
        /* Theme's .table tbody td rule forces vertical-align:middle !important with higher
           specificity than the .align-top utility class — override it here for the item table. */
        table.items-top-align-table tbody td {
            vertical-align: top !important;
        }

        /* Dark Mode Overrides for Smart Quote Document Preview */
        html.dark-style .invoice-preview-card {
            background-color: #2b2c40 !important;
            color: #cfcde4;
        }
        html.dark-style .invoice-preview-card [style*="background:#fafafa"],
        html.dark-style .invoice-preview-card [style*="background: #fafafa"] {
            background-color: #32344d !important;
            border-color: rgba(255,255,255,0.08) !important;
        }
        html.dark-style .invoice-preview-card [style*="background:#fff"],
        html.dark-style .invoice-preview-card [style*="background: #fff"],
        html.dark-style .invoice-preview-card [style*="background:#ffffff"] {
            background-color: #2b2c40 !important;
            border-color: rgba(255,255,255,0.08) !important;
        }
        html.dark-style .invoice-preview-card thead[style*="background:#f2f2f2"] {
            background-color: #3a3b52 !important;
            color: #e0e0e0 !important;
        }
        html.dark-style .invoice-preview-card thead[style*="background:#f2f2f2"] th {
            border-color: #55566e !important;
            color: #e0e0e0 !important;
        }
        html.dark-style .invoice-preview-card p[style*="color:#111"],
        html.dark-style .invoice-preview-card span[style*="color:#222"],
        html.dark-style .invoice-preview-card div[style*="color:#222"] {
            color: #cfcde4 !important;
        }
        html.dark-style .invoice-preview-card p[style*="color:#555"],
        html.dark-style .invoice-preview-card p[style*="color:#444"],
        html.dark-style .invoice-preview-card p[style*="color:#333"],
        html.dark-style .invoice-preview-card i[style*="color:#444"] {
            color: #a1a0b5 !important;
        }
        html.dark-style .invoice-preview-card [style*="background:#fff8e1"] {
            background-color: rgba(255, 171, 0, 0.16) !important;
            border-color: rgba(255, 171, 0, 0.3) !important;
            color: #ffab00 !important;
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
@push('script')
<script>
    var totalQuote = {{ $quote->total }};
    // Dipakai buat auto-isi nominal/persentase di modal Tambah Payment sesuai invoice
    // DP/BP yang sudah diterbitkan — lihat syncAddPaymentAmount() & handler tipe payment.
    var issuedInvoicesData = {!! json_encode($issuedInvoices->map(fn($i) => [
        'id'      => $i->id,
        'type'    => $i->type,
        'percent' => (float) ($i->percent ?? 0),
        'paid'    => (bool) $i->status_p,
    ])->values()) !!};

    {{-- Auto-open modal "Post to Sales Order" jika controller mengirim flash open_convert_po --}}
    @if (session('open_convert_po') && $pendingPo)
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('convertPoUnit');
        if (modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });
    @endif

    function updateDpPreview() {
        var pct = parseFloat($('#dp-percent-input').val()) || 50;
        pct = Math.min(99, Math.max(1, pct));
        var dpAmt  = Math.round(totalQuote * pct / 100);
        var remAmt = totalQuote - dpAmt;
        $('#dp-amount-preview').text(
            'DP: Rp ' + dpAmt.toLocaleString('id-ID') +
            ' | Sisa: Rp ' + remAmt.toLocaleString('id-ID')
        );
    }

    // Add Payment modal — show Tempo field only when type=Tempo
    function formatRupiahDigits(digits) {
        return digits ? Number(digits).toLocaleString('id-ID') : '';
    }
    function syncAddPaymentAmount() {
        var pct = parseFloat($('#add-payment-percent').val());
        if (!isNaN(pct) && pct > 0) {
            $('#add-payment-amount').val(formatRupiahDigits(Math.round(totalQuote * pct / 100)));
        }
    }
    // Format tampilan jadi "10.000.000" sambil mengetik, nilai mentah dikirim saat submit.
    $('#add-payment-amount').on('input', function () {
        var digits = $(this).val().replace(/\D/g, '');
        $(this).val(formatRupiahDigits(digits));
    });
    // Kategori kasar tipe invoice ('DP'/'BP'/'CT') dari string type invoice yang aslinya
    // bebas (mis. "Down Payment 2", "Balance Payment", "Pelunasan") — dipakai buat
    // mencocokkan Tipe Payment (DP/BP) dengan invoice yang sudah diterbitkan.
    function invoiceTypeCategory(type) {
        var t = (type || '').toLowerCase();
        if (t === 'dp' || t.indexOf('down payment') !== -1) return 'DP';
        if (t === 'bp' || t.indexOf('balance payment') !== -1 || t.indexOf('pelunasan') !== -1) return 'BP';
        if (t === 'ct') return 'CT';
        return 'OTHER';
    }

    // Cari invoice yang sudah diterbitkan sesuai kategori (DP/BP) — prioritaskan yang
    // belum ditandai Paid, biar persentasenya ikut invoice yang sedang ditagih.
    function findMatchingInvoice(category) {
        var candidates = (issuedInvoicesData || []).filter(function (inv) {
            return invoiceTypeCategory(inv.type) === category;
        });
        if (!candidates.length) return null;
        var unpaid = candidates.filter(function (inv) { return !inv.paid; });
        var pool = unpaid.length ? unpaid : candidates;
        return pool[pool.length - 1];
    }

    $('#add-payment-type').on('change', function () {
        var type = $(this).val();
        if (type === 'Tempo') {
            $('#tempo-group').show().find('input').prop('required', true);
        } else {
            $('#tempo-group').hide().find('input').prop('required', false).val('');
        }
        // CBD/COD selalu pembayaran penuh — persentase & jumlah otomatis terisi.
        if (type === 'CBD' || type === 'COD') {
            $('#add-payment-percent').val(100);
            syncAddPaymentAmount();
            return;
        }
        // DP/BP — samakan persentase & nominalnya dengan invoice yang sudah diterbitkan
        // buat tipe itu, supaya tidak perlu diketik ulang manual.
        if (type === 'DP' || type === 'BP') {
            var matched = findMatchingInvoice(type);
            if (matched && matched.percent > 0) {
                $('#add-payment-percent').val(matched.percent);
                syncAddPaymentAmount();
            }
        }
    });
    $('#add-payment-percent').on('input', syncAddPaymentAmount);
    $('#add-payment-method').on('change', function () {
        var isEscrow = $(this).val() === 'Escrow';
        $('#add-payment-escrow-channel-group').toggle(isEscrow);
        $('#add-payment-escrow-channel').prop('required', isEscrow);
        if (!isEscrow) {
            $('#add-payment-escrow-channel').val('');
        }
    });
    // Post to Kanban — kolom cuma bisa dipilih setelah board-nya dipilih, karena
    // tiap board punya set kolom sendiri-sendiri (fetch via kanban.boards.data).
    $('#kanban-board-select').on('change', function () {
        var boardId = $(this).val();
        var $columnSelect = $('#kanban-column-select');
        $columnSelect.prop('disabled', true).html('<option value="">Memuat...</option>');
        if (!boardId) {
            $columnSelect.html('<option value="">-- Pilih Board dulu --</option>');
            return;
        }
        $.get('/kanban/boards/' + boardId + '/data', function (columns) {
            var options = '<option value="">-- Pilih Kolom --</option>';
            (columns || []).forEach(function (col) {
                options += '<option value="' + col.id + '">' + col.title + '</option>';
            });
            $columnSelect.html(options).prop('disabled', false);
        }).fail(function () {
            $columnSelect.html('<option value="">Gagal memuat kolom</option>');
        });
    });
    $('#modalPostToKanban').on('hidden.bs.modal', function () {
        $('#kanban-board-select').val('').trigger('change');
    });

    $('#modalAddPayment').on('show.bs.modal', function () {
        $('#add-payment-type').trigger('change');
        $('#add-payment-method').trigger('change');
    });
    $('#modalAddPayment form').on('submit', function () {
        $('#add-payment-amount').val($('#add-payment-amount').val().replace(/\D/g, ''));
    });

    // Edit Payment modal — show Tempo field only when type=Tempo
    $('#edit-payment-type').on('change', function () {
        if ($(this).val() === 'Tempo') {
            $('#edit-tempo-group').show().find('input').prop('required', true);
        } else {
            $('#edit-tempo-group').hide().find('input').prop('required', false).val('');
        }
    });

    // Buka modal Edit Payment, isi form dari data-* tombol yang diklik
    $(document).on('click', '.btn-edit-payment', function () {
        var id = $(this).data('id');
        $('#formEditPayment').attr('action', '/smart-quote/payment/' + id);
        $('#edit-payment-type').val($(this).data('type')).trigger('change');
        $('#edit-payment-method').val($(this).data('method'));
        $('#edit-payment-amount').val($(this).data('amount'));
        $('#edit-payment-percent').val($(this).data('percent'));
        $('#edit-payment-note').val($(this).data('note'));
        $('#edit-payment-tempo').val($(this).data('tempo'));
        new bootstrap.Modal(document.getElementById('modalEditPayment')).show();
    });

    // Submit Edit Payment via AJAX supaya halaman tidak reload
    $('#formEditPayment').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function (res) {
                if (res == 1) {
                    window.location.reload();
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Gagal menyimpan perubahan.';
                Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
            }
        });
    });

    // Upload PO modal — Payment Method dropdown logic
    $('#select-payment-method').on('change', function () {
        var val = $(this).val();
        var $tempoDays = $('#tempo-days-group');
        var $tempoDaysInput = $('#input-tempo-days');
        if (val === 'Tempo') {
            $tempoDays.removeClass('d-none');
            $tempoDaysInput.prop('required', true);
        } else {
            $tempoDays.addClass('d-none');
            $tempoDaysInput.prop('required', false).val('');
        }

        // COD/CBD dibayar lunas di muka, jadi invoice pertama otomatis Full Payment.
        if (val === 'COD' || val === 'CBD') {
            $('#select-invoice-type').val('CT').trigger('change');
        }

        // Metode "DP nn% & ..." — ikutkan persentase DP invoice pertama supaya
        // nominalnya otomatis kebaca dari Payment Method, tidak perlu diketik ulang manual.
        var dpMatch = /^DP\s*(\d+(?:\.\d+)?)\s*%/i.exec(val || '');
        if (dpMatch) {
            $('#select-invoice-type').val('DP').trigger('change');
            $('#dp-percent-input').val(dpMatch[1]);
            updateDpPreview();
        }
    });

    // Submit Upload PO via AJAX — biar begitu sukses, langsung buka modal
    // "Post to Sales Order" tanpa reload/loading transition halaman.
    $('#formUploadPoUnit').on('submit', function (e) {
        e.preventDefault();

        var $form = $(this);
        var $btn  = $('#btnSubmitUploadPoUnit');

        var method = $('#select-payment-method').val();
        var finalValue = method;

        if (method === 'Tempo') {
            var days = parseInt($('#input-tempo-days').val());
            if (!days || days < 1) {
                $('#input-tempo-days').focus();
                Swal.fire({ icon: 'warning', title: 'Oops...', text: 'Masukkan jumlah hari untuk metode Tempo.' });
                return false;
            }
            finalValue = 'Tempo ' + days + ' Hari';
        }

        // Set nilai final ke hidden input & rename agar server membaca dari sini
        $('#select-payment-method').prop('name', '');
        $('input[name="payment_method_final"]').attr('name', 'payment_method').val(finalValue);

        var formData = new FormData(this);
        var originalBtnHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Mengupload...');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'Accept': 'application/json' },
            success: function (res) {
                $('#btn-upload-po-wrap').addClass('d-none');
                if (res.po_file_url) {
                    $('#btn-view-po-wrap').attr('data-url', res.po_file_url).removeClass('d-none');
                }

                var uploadModalEl = document.getElementById('modalUploadPO');
                $(uploadModalEl).one('hidden.bs.modal', function () {
                    openConvertPoModalUnit(res);
                });
                bootstrap.Modal.getOrCreateInstance(uploadModalEl).hide();
            },
            error: function (xhr) {
                var res = xhr.responseJSON || {};
                var msg = res.error
                    || (res.errors ? Object.values(res.errors)[0][0] : null)
                    || 'Gagal upload PO.';
                Swal.fire({ icon: 'error', title: 'Oops...', text: msg });
            },
            complete: function () {
                $btn.prop('disabled', false).html(originalBtnHtml);
                // Kembalikan nama field asal, jaga-jaga kalau user retry submit.
                $('#select-payment-method').prop('name', 'payment_method');
                $('#input-payment-method-final').attr('name', 'payment_method_final');
            }
        });
    });

    // Isi & buka modal "Post to Sales Order" pakai data hasil AJAX Upload PO,
    // tanpa perlu reload halaman.
    function openConvertPoModalUnit(res) {
        var p = res.pendingPo || {};
        var q = res.quote || {};

        $('#uq_NoPending').val(p.no_pending || q.po_number || q.no_quote || '');
        $('#uq_title').val(p.title || '');
        $('#uq_selectEkspedisi').val(p.delivery != null ? String(p.delivery) : '');
        $('#uq_convert_combine_shipping_and_parts')
            .prop('checked', p.combine_shipping_and_parts == null ? true : !!p.combine_shipping_and_parts);
        if (p.shipping_recipient_id) {
            $('#uq_combined_recipient_select').val(p.shipping_recipient_id);
            $('#uq_shipping_recipient_select').val(p.shipping_recipient_id);
        }
        if (p.doc_recipient_id) {
            $('#uq_doc_recipient_select').val(p.doc_recipient_id);
        }
        if (p.shipping_address_manual) {
            $('#uq_combined_address_manual, #uq_shipping_address_manual').val(p.shipping_address_manual);
        }
        if (p.doc_address_manual) {
            $('#uq_doc_address_manual').val(p.doc_address_manual);
        }

        toggleAddressLayoutUQ();
        new bootstrap.Modal(document.getElementById('convertPoUnit')).show();
    }

    // Invoice type toggle — show DP% only when DP selected
    $('#select-invoice-type').on('change', function () {
        var $dpGroup = $('#dp-percent-group');
        var $dpInput = $('#dp-percent-input');
        if ($(this).val() === 'DP') {
            $dpGroup.removeClass('d-none');
            $dpInput.prop('required', true);
            updateDpPreview();
        } else {
            $dpGroup.addClass('d-none');
            $dpInput.prop('required', false);
        }
    });

    $('#dp-percent-input').on('input', updateDpPreview);

    // Next invoice — label custom toggle
    $('#next-inv-label').on('change', function () {
        var $custom = $('#next-inv-label-custom');
        if ($(this).val() === '__custom__') {
            $custom.removeClass('d-none').prop('required', true);
        } else {
            $custom.addClass('d-none').prop('required', false).val('');
        }
    });

    // Before submit next invoice: swap __custom__ label
    $('#modalRequestNextInvoice form').on('submit', function () {
        var $sel = $('#next-inv-label');
        if ($sel.val() === '__custom__') {
            var custom = $('#next-inv-label-custom').val().trim();
            if (!custom) { $('#next-inv-label-custom').focus(); return false; }
            $sel.append('<option value="' + custom + '" selected></option>');
            $sel.val(custom);
        }
    });

    // Request Selling Contract (Sales)
    $(document).on('click', '.request-selling-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Request {{ $contractNoun }}?',
            text: 'Permintaan akan dikirim ke accounting untuk diproses.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Request',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post('{{ url('smart-quote') }}/' + id + '/request-selling-contract', {
                    _token: '{{ csrf_token() }}'
                }, function (response) {
                    if (response == 1) {
                        Swal.fire({
                            icon: 'success', title: 'Requested!',
                            text: 'Permintaan {{ $contractNoun }} telah dikirim.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                            buttonsStyling: false,
                        }).then(function () { location.reload(); });
                    }
                });
            }
        });
    });

    $(document).on('change', '#toggle-hide-title', function () {
        var $checkbox = $(this);
        var id = $checkbox.data('id');
        $.post('{{ url('smart-quote') }}/' + id + '/toggle-hide-title', {
            _token: '{{ csrf_token() }}'
        }).fail(function () {
            $checkbox.prop('checked', !$checkbox.is(':checked'));
            Swal.fire({
                icon: 'error', title: 'Gagal',
                text: 'Gagal mengubah pengaturan Hide Title.',
                customClass: { confirmButton: 'btn btn-primary waves-effect' },
                buttonsStyling: false,
            });
        });
    });

    // Next invoice modal — live amount preview
    @if (isset($remaining) && $remaining > 0)
    var remainingAmount = {{ $remaining }};
    $('#next-inv-percent').on('input', function () {
        var pct = parseFloat($(this).val()) || 100;
        pct = Math.min(100, Math.max(1, pct));
        var amt = Math.round(remainingAmount * pct / 100);
        $('#next-inv-amount').text('= Rp ' + amt.toLocaleString('id-ID'));
    });
    @endif

    // Upload Proof Payment
    var currentProofPaymentId = null;
    $(document).on('click', '.btn-upload-proof', function () {
        currentProofPaymentId = $(this).data('id');
        $('#proof-file-input').val('');
        $('#proof-upload-msg').html('');
        new bootstrap.Modal(document.getElementById('modalUploadProof')).show();
    });

    $('#btn-do-upload-proof').on('click', function () {
        var file = $('#proof-file-input')[0].files[0];
        if (!file) {
            $('#proof-upload-msg').html('<div class="alert alert-warning py-2">Pilih file terlebih dahulu.</div>');
            return;
        }
        var fd = new FormData();
        fd.append('file', file);
        fd.append('_token', '{{ csrf_token() }}');
        $(this).prop('disabled', true).text('Uploading...');
        $.ajax({
            url: '/smart-quote/payment/' + currentProofPaymentId + '/proof',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                $('#proof-upload-msg').html('<div class="alert alert-success py-2">Berhasil diupload!</div>');
                setTimeout(function () { location.reload(); }, 1000);
            },
            error: function () {
                $('#proof-upload-msg').html('<div class="alert alert-danger py-2">Gagal upload. Cek ukuran/format file.</div>');
                $('#btn-do-upload-proof').prop('disabled', false).text('Upload');
            }
        });
    });

    // Delete Payment
    $(document).on('click', '.btn-delete-payment', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus payment ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/smart-quote/payment/' + id,
                    type: 'POST',
                    data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        if (res == 1) {
                            $('#pay-row-' + id).fadeOut(300, function () { $(this).remove(); });
                        }
                    }
                });
            }
        });
    });

    $(document).on('click', '.delete-quote', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.post('{{ url('smart-quote') }}/' + id, {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                }, function (response) {
                    if (response == 1) {
                        Swal.fire({
                            icon: 'success', title: 'Deleted!', text: 'Quotation has been deleted.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                        }).then(function () {
                            window.location.href = '{{ route('quotation.index') }}';
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.ajukan-suo-unit', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        Swal.fire({
            title: 'Ajukan SUO dari penawaran ini?',
            text: 'SUO baru akan dibuat otomatis berisi item dari penawaran unit ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Ajukan SUO',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-outline-secondary waves-effect'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '{{ url("suo/from-unit-quotation") }}/' + id,
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'SUO dibuat!',
                                text: 'SUO berhasil diajukan dari penawaran unit ini.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function () {
                                window.location.href = '/suo/' + response.suo_id;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal mengajukan SUO.'
                            });
                        }
                    },
                    error: function (xhr) {
                        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Gagal mengajukan SUO.';
                        Swal.fire({ icon: 'error', title: 'Gagal', text: msg });
                    }
                });
            }
        });
    });

    // Tambah komentar baru
    $('#form-add-comment').on('submit', function (e) {
        e.preventDefault();
        var text = $('#new-comment-text').val().trim();
        if (!text) return;
        $('#btn-submit-comment').prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: '{{ route('unit-quotation.storeComment', $quote->id) }}',
            data: { comment: text, _token: '{{ csrf_token() }}' },
            success: function () {
                location.reload();
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal mengirim komentar.' });
                $('#btn-submit-comment').prop('disabled', false);
            }
        });
    });

    // Edit komentar
    $(document).on('click', '.btn-edit-comment', function () {
        var $item = $(this).closest('.timeline-item');
        var $p = $item.find('.comment-text');
        var currentText = $p.text();

        Swal.fire({
            title: 'Edit Komentar',
            input: 'textarea',
            inputValue: currentText,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed && result.value.trim()) {
                var id = $item.data('comment-id');
                $.ajax({
                    type: 'POST',
                    url: '{{ url('smart-quote/comments') }}/' + id + '/update',
                    data: { comment: result.value.trim(), _token: '{{ csrf_token() }}' },
                    success: function () {
                        location.reload();
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal mengubah komentar.' });
                    }
                });
            }
        });
    });

    // Hapus komentar
    $(document).on('click', '.btn-delete-comment', function () {
        var $item = $(this).closest('.timeline-item');
        var id = $item.data('comment-id');
        Swal.fire({
            title: 'Hapus komentar ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: '{{ url('smart-quote/comments') }}/' + id,
                    data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                    success: function () {
                        location.reload();
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menghapus komentar.' });
                    }
                });
            }
        });
    });

    // Filter Pills Handler for Activity Feed (Status, Comment, Revision)
    $(document).on('click', '.filter-pill', function() {
        $('.filter-pill').removeClass('btn-primary active').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-primary active');
        
        var filter = $(this).data('filter');
        if (filter === 'all') {
            $('.timeline-feed-item').fadeIn(200);
        } else if (filter === 'status') {
            $('.timeline-feed-item.item-status').fadeIn(200);
            $('.timeline-feed-item.item-comment, .timeline-feed-item.item-revision').hide();
        } else if (filter === 'comment') {
            $('.timeline-feed-item.item-comment').fadeIn(200);
            $('.timeline-feed-item.item-status, .timeline-feed-item.item-revision').hide();
        } else if (filter === 'revision') {
            $('.timeline-feed-item.item-revision').fadeIn(200);
            $('.timeline-feed-item.item-status, .timeline-feed-item.item-comment').hide();
        }
    });

    // Initialize Tooltips for Stock Badges
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    }
    if (typeof $.fn.tooltip !== 'undefined') {
        $('[data-bs-toggle="tooltip"]').tooltip({ html: true });
    }
</script>
@endpush
