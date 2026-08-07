@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.sales.app')
@section('title', 'Invoice ' . ($invoice->no_invoice ?? '#' . $invoice->id))
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">
            <a href="{{ route('invoice.index') }}" class="text-muted">Accounting / Invoice</a> /
        </span>
        {{ $invoice->no_invoice ?? '#' . $invoice->id }}
    </h4>

    <div class="row invoice-preview">
        {{-- Invoice Card --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
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
                <div class="card-body" style="position: relative; z-index: 1;">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column {{ !$quote->tax ? 'float-end' : '' }}">
                        @if ($quote->tax)
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="info">
                                        <p class="mb-1 fw-bolder">Office Address :</p>
                                        <div style="font-size: 10px">
                                            <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                            <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                            <p class="mb-1">
                                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                                &nbsp;&nbsp;<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>info@reftech.id
                                            </p>
                                        </div>
                                    </div>
                                    <div class="npwp_add">
                                        <p class="mb-1 fw-bolder">NPWP Address :</p>
                                        <pre style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;">Komp. Negia Kencana Residence Blok B, No.2 Pasanggrahan, Ujung Berung Kota Bandung - Jawa Barat 40199</pre>
                                        <p class="mb-1 text-black fw-medium p-1" style="background-color: rgb(224, 221, 255); font-size: 10px">
                                            NPWP : 73.728.571.8-429.000
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="text-end">
                            <h1 class="fw-bold" style="color: blue;">INVOICE</h1>
                            <div>
                                <span class="fw-bolder">#{{ $invoice->no_invoice }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="text-black">{{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('d-m-Y') : '-' }}</span>
                            </div>
                            @php
                                $warna = 'bg-label-dark text-dark';
                                $text  = 'Waiting Payment';
                                if ($invoice->status_p) {
                                    $warna = 'bg-label-success text-success';
                                    $text  = 'Verified';
                                }
                            @endphp
                            <h6 class="mt-1 badge {{ $warna }} rounded">{{ $text }}</h6>
                        </div>
                    </div>
                </div>

                <hr class="my-0">

                {{-- Invoice To --}}
                <div class="card-body mb-3">
                    <h5>Invoice To</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" style="border: 1px solid black;">
                            <tr>
                                <td rowspan="3" style="vertical-align: top; width: 50%;">
                                    <div class="row">
                                        <div class="col-4 fw-medium">
                                            <p class="mb-1">Bill To</p>
                                        </div>
                                        <div class="col-8">
                                            <pre style="font-size: 14px; font-family: Inter;" class="mb-1 fw-bolder">: {{ $quote->client?->company ?? '-' }}</pre>
                                        </div>
                                        <div class="col-4 fw-medium">
                                            <p class="mb-1">PIC</p>
                                        </div>
                                        <div class="col-8">
                                            <p class="mb-1">: {{ $quote->pic?->name_pic ?? $quote->attn ?? '-' }}</p>
                                        </div>
                                        <div class="col-4 fw-medium">
                                            <p class="mb-1">NPWP</p>
                                        </div>
                                        <div class="col-8">
                                            <p class="mb-1">: {{ $quote->client?->npwp ?? '-' }}</p>
                                        </div>
                                        <div class="col-4 fw-medium">
                                            <p class="mb-1">Phone</p>
                                        </div>
                                        <div class="col-8">
                                            <p class="mb-1">: {{ $quote->client?->phone ?? '-' }}</p>
                                        </div>
                                        <div class="col-4 fw-medium">
                                            <p class="mb-1">Address</p>
                                        </div>
                                        <div class="col-8">
                                            <pre style="font-size: 14px; font-family: Inter; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $invoice->invoiceTo == '1' ? ($quote->client?->address ?? '-') : ($quote->client?->subAddress ?? '-') }}</pre>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p>Purchase Order :</p>
                                </td>
                                <td>
                                    <p>{{ $quote->po_number ?? '-' }}</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="background-color: #F9F9F9;" class="text-center">
                                    <p class="fs-5 text-black fw-medium m-0">Term Of Payment:</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <pre style="font-size: 14px; font-family: Inter;">{{ $invoice->term ?? $quote->payment_method }}</pre>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Items Table --}}
                @php
                    $colCount = $quote->tax ? 5 : 4; // No, Item, Price, Qty, [DPP,] Amount
                    $bgColor  = 'rgb(224, 248, 248)';
                    $afterDisc = $quote->subtotal - ($quote->subtotal * $quote->diskon / 100);
                    $dppTotal  = $quote->tax ? ($afterDisc * 11 / 12) : 0;
                @endphp
                <div class="table-responsive">
                    <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Item</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th style="width: 5%">Disc</th>
                                @if ($quote->tax)
                                    <th style="width: 15%">DPP</th>
                                @endif
                                <th style="width: 20%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quote->details as $i => $detail)
                                @php
                                    $dpp = $quote->tax ? ($detail->amount * 11 / 12) : 0;
                                @endphp
                                <tr style="font-size: 13px">
                                    <td class="align-top">{{ $i + 1 }}</td>
                                    <td class="text-nowrap align-top">
                                        @if ($detail->type === 'unit' && $detail->unit)
                                            <p class="mb-0 fw-medium" style="font-size: 12px">
                                                {{ $detail->label ?: ($detail->unit->brand . ' ' . $detail->unit->model) }}
                                            </p>
                                            <small class="text-muted">{{ $detail->unit->type }}</small>
                                        @else
                                            <p class="mb-0 fw-medium" style="font-size: 12px">{{ $detail->label }}</p>
                                            @if ($detail->description)
                                                <pre class="mb-0" style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $detail->description }}</pre>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="align-top text-end">RP {{ number_format($detail->price, 0, '', '.') }}</td>
                                    <td class="align-top">{{ $detail->qty }} {{ $detail->info_qty }}</td>
                                    <td class="align-top">{{ $detail->disc > 0 ? $detail->disc . '%' : '-' }}</td>
                                    @if ($quote->tax)
                                        <td class="align-top text-end">RP {{ number_format($dpp, 0, '', '.') }}</td>
                                    @endif
                                    <td class="align-top text-end">RP {{ number_format($detail->amount, 0, '', '.') }}</td>
                                </tr>
                            @endforeach

                            {{-- Totals rows --}}
                            <tr class="fw-medium" style="font-size: 13px">
                                <td colspan="{{ $quote->tax ? 3 : 2 }}" rowspan="9" style="border-bottom: none !important;"></td>
                                <td colspan="{{ $quote->tax ? 3 : 3 }}" class="text-end pl-4 py-0" style="padding-right: 10px !important;">
                                    <p class="m-0">{{ ($quote->tax || $totalPph > 0) ? 'Subtotal' : 'Total' }}</p>
                                </td>
                                <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                    <p class="text-end m-0">RP {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                                </td>
                            </tr>

                            @if ($quote->diskon > 0)
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="3" class="text-end py-0" style="padding-right: 10px !important;">
                                        <p class="m-0">Discount ({{ $quote->diskon }}%)</p>
                                    </td>
                                    <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end">RP {{ number_format($quote->subtotal * $quote->diskon / 100, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="3" class="text-end py-0" style="padding-right: 10px !important;">
                                        <p class="m-0">Total After Discount</p>
                                    </td>
                                    <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end">RP {{ number_format($afterDisc, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                            @endif

                            @if ($quote->tax)
                                <tr class="fw-medium" style="font-size: 13px">
                                    <td colspan="3" class="text-end pl-4 py-0" style="padding-right: 10px !important;">
                                        <p class="m-0">DPP Atas PPN</p>
                                    </td>
                                    <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="text-end m-0">RP {{ number_format($dppTotal, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                                <tr class="fw-medium py-0" style="font-size: 13px">
                                    <td colspan="3" class="text-end py-0" style="padding-right: 10px !important;">
                                        <p class="m-0">VAT 12%</p>
                                    </td>
                                    <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end">RP {{ number_format($quote->tax_amount, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                            @endif

                            @if ($totalPph > 0)
                                <tr class="fw-medium py-0" style="font-size: 13px">
                                    <td colspan="3" class="text-end py-0" style="padding-right: 10px !important;">
                                        <p class="m-0">PPH</p>
                                    </td>
                                    <td class="pr-4 py-0" style="padding-left: 0 !important;">
                                        <p class="m-0 text-end">RP {{ number_format($totalPph, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                            @endif

                            @if ($quote->tax || $totalPph > 0)
                                <tr class="fw-medium py-0" style="font-size: 13px">
                                    <td colspan="3" class="text-end py-0"
                                        style="background-color: {{ $bgColor }}; padding-left: 20px; padding-right: 10px;">
                                        <p class="m-0 fw-bold">Total</p>
                                    </td>
                                    <td class="pr-4 py-0" style="background-color: {{ $bgColor }}; padding-right: 20px;">
                                        <p class="m-0 text-end fw-bold">RP {{ number_format($quote->total, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                            @endif

                            @if (in_array($invoice->type, ['DP', 'BP']))
                                <tr style="font-size: 13px; background: #fff3cd;">
                                    <td colspan="3" class="text-end py-1 fw-bold" style="padding-right: 10px !important;">
                                        <p class="m-0">
                                            {{ $invoice->type === 'DP' ? 'Tagihan DP (' . floatval($invoice->percent) . '%)' : 'Tagihan BP (' . floatval($invoice->percent) . '% sisa)' }}
                                        </p>
                                    </td>
                                    <td class="py-1 fw-bold" style="padding-left: 0 !important; padding-right: 20px;">
                                        <p class="m-0 text-end">RP {{ number_format($totalAfterPph, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- Terbilang --}}
                <p class="fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width: 70%;">
                    Say amount: # {{ $terbilang }} Rupiah
                </p>

                {{-- Bank & TTD --}}
                <div class="row mt-5">
                    <div class="col-6 m-4">
                        <h5 class="my-4">Payment by Transfer or Giro shall be made in Full amount to :</h5>
                        <div class="row">
                            <div class="col-3 fw-medium">
                                <p class="mb-1">Payable to</p>
                                <p class="mb-1">Acc Name</p>
                                <p class="mb-1">Acc No.</p>
                                <p class="mb-1">Swift Code</p>
                            </div>
                            @if ($quote->tax)
                                <div class="col">
                                    <p class="mb-1">: Bank BCA (IDR)</p>
                                    <p class="mb-1">: PT. REFTECH JAYA OPTIMA</p>
                                    <p class="mb-1">: 008 - 6289 - 789</p>
                                    <p class="mb-1">: CENAIDJA</p>
                                </div>
                            @else
                                <div class="col">
                                    <p class="mb-1">: Bank BCA (IDR)</p>
                                    <p class="mb-1">: ARIEP RACHMAN</p>
                                    <p class="mb-1">: 166 - 2242 - 271</p>
                                    <p class="mb-1">: -</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col"></div>
                    <div class="col-4 my-5 text-center">
                        <p>Bandung, {{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->locale('ID')->translatedFormat('d F Y') : \Carbon\Carbon::now()->locale('ID')->translatedFormat('d F Y') }}</p>
                        @if ($quote->tax)
                            <p class="fs-normal fw-bolder">PT. Reftech Jaya Optima</p>
                        @endif
                        @if (isset($invoice->sign))
                            <img src="{{ url('') . '/' . $invoice->sign }}" alt="" srcset="" height="77">
                        @else
                            <div style="padding: 40px 0;"></div>
                        @endif
                        <p class="pt-3 fw-bolder">Ariep Rachman</p>
                        <p>Director</p>
                    </div>
                </div>

            </div>
        </div>
        {{-- End: Invoice --}}

        {{-- Sidebar Actions --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">

            {{-- 1. Primary Actions --}}
            <div class="card mb-3">
                <div class="card-body d-grid gap-2">
                    <div class="btn-group w-100">
                        <a href="{{ route('invoice.show_unit.print', $invoice->id) }}" target="_blank"
                           class="btn btn-primary waves-effect">
                            <i class="mdi mdi-printer-outline me-1"></i> Download
                        </a>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('invoice.show_unit.print', $invoice->id) }}" target="_blank">
                                    <i class="mdi mdi-file-document-outline me-1"></i> Invoice
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('invoice.unit.label_detail', $invoice->id) }}">
                                    <i class="mdi mdi-package-variant-closed me-1"></i> Sampul
                                </a>
                            </li>
                        </ul>
                    </div>
                    <button class="btn btn-outline-secondary w-100 waves-effect" id="backButton">Back</button>
                    <a href="{{ route('unit-quotation.show', $quote->id) }}"
                       class="btn btn-outline-info w-100 waves-effect">
                        View Penawaran
                    </a>
                </div>
            </div>

            {{-- 2. Invoice Info --}}
            <div class="card mb-3">
                <div class="card-header py-2 px-3">
                    <small class="text-uppercase text-muted fw-semibold">Invoice</small>
                </div>
                <div class="card-body d-grid gap-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">No. Invoice</span>
                        <span class="fw-semibold small">{{ $invoice->no_invoice }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Tanggal</span>
                        <span class="fw-semibold small">{{ $invoice->date ? \Carbon\Carbon::parse($invoice->date)->format('d M Y') : '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">No. PO</span>
                        <span class="fw-semibold small">{{ $quote->po_number ?? '-' }}</span>
                    </div>
                    @if ($quote->po_file)
                        <a href="{{ Storage::url($quote->po_file) }}" target="_blank"
                           class="btn btn-outline-success btn-sm w-100 waves-effect">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Lihat File PO
                        </a>
                    @endif
                    @if ($allInvoices->count() > 1)
                        <hr class="my-1">
                        @foreach ($allInvoices as $inv)
                            <a href="{{ route('invoice.show_unit', $inv->id) }}"
                               class="btn btn-sm {{ $inv->id == $invoice->id ? 'btn-primary' : 'btn-outline-secondary' }} w-100 waves-effect">
                                <span class="badge {{ $inv->type === 'DP' ? 'bg-warning' : 'bg-info' }} me-1">{{ $inv->type }}</span>
                                {{ $inv->no_invoice ?? 'Pending' }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- 3. Tax / PPH --}}
            @if (Auth::user()->role != 'Sales')
            <div class="card mb-3">
                <div class="card-header py-2 px-3">
                    <small class="text-uppercase text-muted fw-semibold">Tax / PPH</small>
                </div>
                <div class="card-body d-grid gap-2">
                    @php $pphPerItem = $quote->details->sum(fn($d) => ($d->amount * $d->pph) / 100); @endphp
                    @if ($pphPerItem > 0)
                        <a href="#" class="btn btn-danger w-100 waves-effect delete-pph-unit"
                           data-id="{{ $invoice->id }}">Delete PPH 23</a>
                    @else
                        <button type="button" class="btn btn-outline-info w-100 waves-effect"
                            data-bs-toggle="modal" data-bs-target="#modalAddPph">Input PPH 23</button>
                    @endif
                    @if (($invoice->pph ?? 0) > 0)
                        <a href="#" class="btn btn-danger w-100 waves-effect delete-pph-manual-unit"
                           data-id="{{ $invoice->id }}">Delete PPH Manual</a>
                    @else
                        <button type="button" class="btn btn-outline-secondary w-100 waves-effect"
                            data-bs-toggle="modal" data-bs-target="#modalAddPphManual">Input PPH Manual</button>
                    @endif
                </div>
            </div>
            @endif

            {{-- 4. Hand Sign --}}
            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                <div class="card mb-3">
                    <div class="card-header py-2 px-3">
                        <small class="text-uppercase text-muted fw-semibold">Hand Sign</small>
                    </div>
                    <div class="card-body">
                        @if (isset($invoice->sign))
                            <a href="#" class="btn btn-danger w-100 waves-effect delete-hand-sign-unit"
                               data-id="{{ $invoice->id }}">Delete Hand Sign</a>
                        @else
                            <a href="#" class="btn btn-outline-secondary w-100 waves-effect input-hand-sign-unit"
                               data-id="{{ $invoice->id }}">Input Hand Sign</a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 5. Payment --}}
            <div class="card mb-3">
                <div class="card-header py-2 px-3">
                    <small class="text-uppercase text-muted fw-semibold">Payment</small>
                </div>
                <div class="card-body d-grid gap-2">
                    <button type="button" class="btn btn-outline-secondary w-100 waves-effect"
                        data-bs-toggle="modal" data-bs-target="#modalDetailPayment">Detail Payment</button>
                    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                        @if (!$invoice->status_p)
                            <button type="button" class="btn btn-primary w-100 waves-effect"
                                data-bs-toggle="modal" data-bs-target="#confirmPayment">Confirm Payment</button>
                        @else
                            <a href="#" class="btn btn-danger w-100 waves-effect undo-payment-unit"
                               data-id="{{ $invoice->id }}">Undo Confirm Payment</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

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
            <div class="modal-content">
                <form action="{{ route('invoice.confirm_payment_unit', $invoice->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label fw-semibold">Catatan</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Catatan pembayaran..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary waves-effect">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Detail Payment --}}
    <div class="modal modal-lg fade" id="modalDetailPayment" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Payment — {{ $quote->no_quote }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-semibold mb-1">{{ $quote->client?->company }}</p>
                    <p class="text-muted small mb-3">Payment Method: {{ $quote->payment_method }}</p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Type</th>
                                    <th>Term</th>
                                    <th class="text-end">Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allInvoices as $inv)
                                    @php
                                        $invTotal = $quote->total;
                                        if ($inv->type === 'DP' && $inv->term) {
                                            $pct = floatval(filter_var($inv->term, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
                                            $invTotal = round($quote->total * $pct / 100);
                                        } elseif ($inv->type === 'BP') {
                                            $dpInv = $allInvoices->firstWhere('type', 'DP');
                                            $pct = $dpInv?->term ? floatval(filter_var($dpInv->term, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) : 0;
                                            $invTotal = $quote->total - round($quote->total * $pct / 100);
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $inv->no_invoice ?? '-' }}</td>
                                        <td>
                                            <span class="badge {{ $inv->type === 'DP' ? 'bg-warning' : ($inv->type === 'BP' ? 'bg-info' : 'bg-primary') }}">
                                                {{ $inv->type }}
                                            </span>
                                        </td>
                                        <td>{{ $inv->term ?? '-' }}</td>
                                        <td class="text-end">Rp {{ number_format($invTotal, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($inv->status_p)
                                                <span class="badge bg-success">Verified</span>
                                            @else
                                                <span class="badge bg-danger">Waiting Payment</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold">
                                    <td colspan="3" class="text-end">Grand Total</td>
                                    <td class="text-end">Rp {{ number_format($quote->total, 0, ',', '.') }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
<script>
    $('#backButton').click(function () { window.history.back(); });

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
</script>
@endpush
