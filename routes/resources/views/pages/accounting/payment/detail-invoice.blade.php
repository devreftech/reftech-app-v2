@extends('layouts.sales.app')
@section('title', 'Sales Invoice AR')
@section('content')
    <h4 class="fw-bold py-3 mb-4"> <span class="text-muted">Account Recieveable / Sales Invoice/</span> Invoice #123123 </h4>
    <div class="row mb-3">
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h4>Information</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body py-1">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="menu-icon tf-icons mdi mdi-file-document-outline m-0 fs-1"></i>
                                        </div>
                                        <div class="col">
                                            <p class="text-muted mb-0"> No Invoice</p>
                                            <a href="{{ route('invoice.show', $invoice->id) }}"
                                                class="text-black fs-5 fw-medium" target="_blank">
                                                {{ $invoice->no_invoice }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body py-1">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="menu-icon tf-icons mdi mdi-file-document-edit-outline m-0 fs-1"></i>
                                        </div>
                                        <div class="col">
                                            <p class="text-muted mb-0">Invoice Date</p>
                                            <h5>{{ Carbon\Carbon::parse($invoice->date)->format('d-m-Y') }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body py-1">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="menu-icon tf-icons mdi mdi-calendar-clock-outline m-0 fs-1"></i>
                                        </div>
                                        <div class="col">
                                            <p class="text-muted mb-0">Due Date</p>
                                            <h5>{{ @$payment[0]->type == 'Tempo' ? @$payment[0]->due_date : '-' }}</h5>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body py-1">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="menu-icon tf-icons mdi mdi-information-outline m-0 fs-1"></i>
                                        </div>
                                        <div class="col">
                                            <p class="text-muted mb-0">Terms</p>
                                            <h5>{{ $invoice->term }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-muted">Customer</p>
                                    <h5 class="mb-0">{{ $invoice->quote->pic->client->company }}</h5>
                                    <p class="mb-0">{{ $invoice->quote->pic->client->address }}</p>
                                    <h6 class="mb-0">{{ $invoice->quote->pic->client->npwp }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4>Summarry</h4>
                    <p class="text-muted">quick financial snapshot</p>
                    <div class="row">
                        <div class="col-6">
                            <p>Total Invoice</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp {{ number_format($quote->subtotal, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-6">
                            <p>Advance Payment</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp {{ number_format(@$payment->sum('amount'), 0, ',', '.') }}</p>
                        </div>
                        <div class="col-6">
                            <p>Subtotal</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp {{ number_format($quote->subtotal, 0, ',', '.') }}</p>
                        </div>
                        @php
                            $vat = ($quote->subtotal / 100) * $quote->tax;
                        @endphp
                        @if ($quote->tax > 0)
                            <div class="col-6">
                                <p>VAT 11%</p>
                            </div>
                            <div class="col-6">
                                <p class="text-end fw-bolder">Rp {{ number_format($vat, 0, ',', '.') }}</p>
                            </div>
                        @endif
                        <hr>
                        <div class="col-6">
                            <p class="fw-bolder">Grand Total</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp {{ number_format($quote->harga_total, 0, ',', '.') }}</p>
                        </div>
                        @php
                            $outstanding = $quote->harga_total - @$payment->sum('amount');
                        @endphp
                        <div class="col-6">
                            <p>Outstanding</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp {{ number_format($outstanding, 0, ',', '.') }}</p>
                        </div>
                        {{-- @if ($payment->count() == 2)
                            <div class="col-6">
                                <p>Advance Paymen</p>
                            </div>
                            <div class="col-6">
                                <p class="text-end fw-bolder">Rp {{ number_format($payment[0]->amount, 0, ',', '.') }}</p>
                            </div>
                        @endif --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h4>Detail Quotation</h4>
            <div class="table-responsive">
                <table class="table m-0">
                    <thead class="">
                        <tr>
                            <th>Item</th>
                            <th>Desc</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dQuote as $product)
                            <tr style="font-size: 13px">
                                <td class="align-top">
                                    {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}</td>
                                <td class="align-top">
                                    <pre class="mb-0"
                                        style="font-size: 12px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail_product }}</pre>
                                </td>
                                <td class="align-top">{{ $product->qty }} {{ $product->info_qty }} </td>
                                <td class="align-top text-end">RP {{ number_format($product->price, 0, '', '.') }}</td>
                                <td class="align-top text-end">RP {{ number_format($product->amount, 0, '', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="table-light">
                            <td colspan="4" class="text-end">
                                <p class="mb-0">Subtotal:</p>
                            </td>
                            <td colspan="2">
                                <p class="fw-semibold mb-0 text-end">RP
                                    {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                            </td>
                        </tr>
                        @if ($quote->tax > 0)
                            <tr class="table-light">
                                <td colspan="4" class="text-end">
                                    <p class="mb-0">VAT %11 :</p>
                                </td>
                                <td colspan="2">
                                    <p class="fw-semibold mb-0 text-end">
                                        {{ $vat == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                </td>
                            </tr>
                        @endif
                        <tr class="table-light">
                            <td colspan="4" class="text-end">
                                <p class="mb-0">Grand Total:</p>
                            </td>
                            <td colspan="2">
                                <p class="fw-semibold mb-0 text-end">RP
                                    {{ number_format($quote->harga_total, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h4>Payment History</h4>
            <div class="table-responsive mb-3">
                <table class="table m-0">
                    <thead class="">
                        <tr>
                            <th>No Payment</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Note</th>
                            <th>status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payment as $item)
                            <tr style="font-size: 13px">
                                <td class="align-top">
                                    #PYMN-{{ $item->id }}
                                </td>
                                <td class="align-top">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                                </td>
                                <td class="align-top"> Bank Transfer </td>
                                <td class="align-top">RP {{ number_format($item->amount, 0, '', '.') }}
                                <td class="align-top">{{ $item->note }}</td>
                                </td>
                                @php
                                    if ($item->level == 0) {
                                        if ($item->file == null) {
                                            $warna = 'bg-label-danger text-danger';
                                            $text = 'Waiting Payment';
                                        } else {
                                            $warna = 'bg-label-warning text-warning';
                                            $text = 'Awaiting Verification';
                                        }
                                    } elseif ($item->level == 1) {
                                        $warna = 'bg-label-success text-success';
                                        $text = 'Verified';
                                    } else {
                                        $warna = 'bg-label-dark text-dark';
                                        $text = 'belum di Payment';
                                    }

                                @endphp
                                <td>
                                    <h6 class="mt-1 badge {{ $warna }} rounded">
                                        {{ $text }}
                                    </h6>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum Ada Payment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-0">Total Paid</p>
                            <h5>
                                Rp {{ number_format($payment->sum('amount'), 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-0">Outstanding</p>
                            <h5>
                                Rp {{ number_format($outstanding, 0, ',', '.') }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-muted mb-0">Overdue</p>
                            <h5>
                                @forelse ($payment as $pay)
                                    @if ($pay->type == 'Tempo')
                                        @php
                                            $selisih = now()->diffInDays(\Carbon\Carbon::parse($pay->due_date), false);
                                        @endphp
                                        @if ($selisih > 0)
                                            {{ $selisih }} Days More
                                        @elseif($selisih == 0)
                                            Today is Due
                                        @else
                                            Late For {{ abs($selisih) }} Days
                                        @endif
                                    @else
                                        -
                                    @endif
                                @empty
                                    -
                                @endforelse
                            </h5>
                        </div>
                    </div>
                </div>
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
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-sales-invoice-ar.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
@endpush
