@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.sales.app')
@section('title', 'Request Invoice — Unit Quotation')
@section('content')
    <div class="row invoice-preview">
        {{-- Invoice Preview --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <img src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                     alt="Reftech" width="40%">
                            </div>
                            <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                            <div style="font-size: 10px">
                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                <p class="mb-1">022 54417653 | admin@reftech.id</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold">INVOICE REQUEST</h3>
                            <p class="mb-1 fw-semibold">#{{ $quote->no_quote }}</p>
                            <span class="badge bg-warning">Unit Quotation</span>
                            <p class="mt-2 text-muted small">{{ $quote->date?->format('d-m-Y') }}</p>
                        </div>
                    </div>
                </div>

                <hr class="my-0">

                {{-- Client Info --}}
                <div class="card-body mb-3">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="fw-semibold fs-5 mb-3">Quote To:</h6>
                            <div class="row">
                                <div class="col-auto fw-medium">
                                    <p class="mb-1">Company</p>
                                    <p class="mb-1">Attn</p>
                                    <p class="mb-1">Phone</p>
                                </div>
                                <div class="col">
                                    <p class="mb-1">: {{ $quote->client?->company ?? '-' }}</p>
                                    <p class="mb-1">: {{ $quote->pic?->name_pic ?? $quote->attn ?? '-' }}</p>
                                    <p class="mb-1">: {{ $quote->client?->phone ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="row justify-content-end">
                                <div class="col-auto fw-medium">
                                    <p class="mb-1">Sales</p>
                                    <p class="mb-1">Payment Method</p>
                                    <p class="mb-1">No. PO</p>
                                    <p class="mb-1">Judul</p>
                                </div>
                                <div class="col-auto">
                                    <p class="mb-1">: {{ $quote->sales?->name ?? '-' }}</p>
                                    <p class="mb-1">: <strong>{{ $quote->payment_method }}</strong></p>
                                    <p class="mb-1">: {{ $quote->po_number }}</p>
                                    <p class="mb-1">: {{ $quote->title ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead class="table-light border-top">
                            <tr>
                                <th>No.</th>
                                <th>Item</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Disc</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quote->details as $i => $detail)
                                <tr style="font-size: 13px">
                                    <td class="align-top">{{ $i + 1 }}</td>
                                    <td class="align-top">
                                        @if ($detail->type === 'unit' && $detail->unit)
                                            <p class="mb-0 fw-semibold">{{ $detail->label ?: ($detail->unit->brand . ' ' . $detail->unit->model) }}</p>
                                            <small class="text-muted">{{ $detail->unit->type }}</small>
                                        @else
                                            <p class="mb-0 fw-semibold">{{ $detail->label }}</p>
                                            @if ($detail->description)
                                                <small class="text-muted">{{ $detail->description }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="align-top text-end">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td class="align-top text-center">{{ $detail->qty }} {{ $detail->info_qty }}</td>
                                    <td class="align-top text-center">{{ $detail->disc > 0 ? $detail->disc . '%' : '-' }}</td>
                                    <td class="align-top text-end">Rp {{ number_format($detail->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <table class="table table-borderless" style="font-size: 13px">
                                <tr>
                                    <td class="fw-medium">Subtotal</td>
                                    <td class="text-end">Rp {{ number_format($quote->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @if ($quote->diskon > 0)
                                    <tr>
                                        <td class="fw-medium">Diskon ({{ $quote->diskon }}%)</td>
                                        <td class="text-end text-danger">- Rp {{ number_format($quote->subtotal * $quote->diskon / 100, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                @if ($quote->tax)
                                    <tr>
                                        <td class="fw-medium">PPN 11%</td>
                                        <td class="text-end">Rp {{ number_format($quote->tax_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="fw-bold fs-5">Total</td>
                                    <td class="text-end fw-bold fs-5">Rp {{ number_format($quote->total, 0, ',', '.') }}</td>
                                </tr>
                                @if ($quote->payment_method === 'DP & BP')
                                    @php
                                        $pct = floatval($invoice->percent ?? 100);
                                        $amt = round($quote->total * $pct / 100);
                                    @endphp
                                    <tr style="background: #f0f4ff;">
                                        <td class="fw-bold">
                                            {{ $invoice->type === 'DP' ? 'Tagihan DP (' . $pct . '%)' : 'Tagihan BP (' . $pct . '% sisa)' }}
                                        </td>
                                        <td class="text-end fw-bold">Rp {{ number_format($amt, 0, ',', '.') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Sidebar --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">

            {{-- Accept Form --}}
            <div class="card mb-3">
                <div class="card-header fw-semibold">Terbitkan Invoice</div>
                <div class="card-body">
                    @php
                        $invPercent = floatval($invoice->percent ?? 100);
                        $invAmount  = round($quote->total * $invPercent / 100);
                    @endphp

                    {{-- Invoice Amount Preview --}}
                    <div class="mb-3 p-2 rounded" style="background: #f0f4ff;">
                        <div class="small text-muted">
                            @if ($invoice->type === 'DP')
                                Down Payment ({{ $invPercent }}%)
                            @elseif ($invoice->type === 'BP')
                                Balance Payment ({{ $invPercent }}% dari sisa)
                            @else
                                Full Payment
                            @endif
                        </div>
                        <div class="fw-bold">Rp {{ number_format($invAmount, 0, ',', '.') }}</div>
                    </div>

                    <form action="{{ route('accept.unit', $invoice->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Tanggal Invoice</label>
                            <input type="date" name="invoice_date" class="form-control form-control-sm"
                                   value="{{ now()->toDateString() }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Term of Payment</label>
                            <select name="term" class="form-select form-select-sm">
                                <option value="Cash" {{ $quote->payment_method === 'Cash Before Delivery' ? 'selected' : '' }}>Cash</option>
                                <option value="NET 7">NET 7</option>
                                <option value="NET 14">NET 14</option>
                                <option value="NET 30" {{ $quote->payment_method === 'TOP 30' ? 'selected' : '' }}>NET 30</option>
                                <option value="NET 60" {{ $quote->payment_method === 'TOP 60' ? 'selected' : '' }}>NET 60</option>
                                <option value="NET 90" {{ $quote->payment_method === 'TOP 90' ? 'selected' : '' }}>NET 90</option>
                            </select>
                        </div>

                        @foreach ($allInvoices as $inv)
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">
                                    No. Invoice
                                    @if ($quote->payment_method === 'DP & BP')
                                        <span class="badge {{ $inv->type === 'DP' ? 'bg-warning text-dark' : 'bg-info' }} ms-1">{{ $inv->type }}</span>
                                    @endif
                                </label>
                                <input type="text" name="no_invoice_{{ $inv->id }}"
                                       class="form-control form-control-sm"
                                       value="{{ $nextNumbers[$inv->id] ?? '' }}" required>
                            </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary d-grid w-100 waves-effect mb-2">
                            Terbitkan Invoice
                        </button>
                    </form>
                    <button class="btn btn-outline-secondary d-grid w-100 waves-effect" id="backButton">
                        Kembali
                    </button>
                </div>
            </div>

            {{-- Info PO --}}
            <div class="card mb-3">
                <div class="card-body">
                    <p class="mb-1 text-muted fw-semibold small">No. PO</p>
                    <p class="mb-3 fw-bold">{{ $quote->po_number }}</p>
                    <p class="mb-1 text-muted fw-semibold small">Payment Method</p>
                    <p class="mb-3 fw-bold">{{ $quote->payment_method }}</p>
                    @if ($quote->po_file)
                        <a href="{{ Storage::url($quote->po_file) }}" target="_blank"
                           class="btn btn-outline-primary d-grid w-100 waves-effect">
                            <i class="mdi mdi-file-pdf-box me-1"></i> Lihat File PO
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
@endpush

@push('script')
<script>
    $('#backButton').click(function () {
        window.history.back();
    });
</script>
@endpush
