@extends('layouts.sales.app')
@section('title', 'Contract')
@section('content')
    <div class="row invoice-preview">
        {{-- Invoice --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    @if ($contract->type == 'Selling')
                        <div
                            class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column {{ $quote->tax == 0 ? 'float-end' : '' }}">
                            @if ($quote->tax != '0')
                                <div class="mb-xl-0 pb-1">
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                        <span class="app-brand-logo demo">
                                            <span style="color: var(--bs-primary)">
                                                <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                    alt="" srcset="" width="60%">
                                            </span>
                                        </span>
                                    </div>
                                    <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                                    <div style="font-size: 10px">
                                        <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                        <p class="mb-1">
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                            {{ '  |  ' }}<i
                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>info@reftech.id
                                        </p>
                                        <p class="mb-1">
                                        </p>
                                    </div>
                                </div>
                            @endif
                            <div class="text-end">
                                <h3 class="fw-bold">SELLING CONTRACT</h3>
                                <div>
                                    <span class="fw-bolder">#{{ $contract->no_contract }}</span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-muted">{{ Carbon\Carbon::parse($contract->date)->format('d-m-Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div
                            class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column {{ $quote->tax == 0 ? 'float-end' : '' }}">
                            @if ($quote->tax != '0')
                                <div class="mb-xl-0 pb-1">
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                        <span class="app-brand-logo demo">
                                            <span style="color: var(--bs-primary)">
                                                <img class="text-md" src="{{ asset('/asset') }}/logo/Logo-update-size.png"
                                                    alt="" srcset="" width="60%">
                                            </span>
                                        </span>
                                    </div>
                                    <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                    <div style="font-size: 10px">
                                        <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                        <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                        <p class="mb-1">
                                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62
                                            812-1000-0997
                                            {{ ' | ' }}<i
                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                        </p>
                                    </div>
                                </div>
                            @endif
                            <div class="text-end">
                                <h3 class="fw-bold">Confirm Order</h3>
                                <div>
                                    <span class="fw-bolder">#{{ $contract->no_contract }}</span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-muted">{{ Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="fw-semibold fs-4 mb-3">Quote To:</h6>
                        </div>
                        <div class="col-6 mb-2">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Company </p>
                            <p class="mb-1">Name PIC</p>
                            <p class="mb-1">Phone </p>
                        </div>
                        <div class="col-4">
                            <p class="mb-1">: {{ $quote->pic->client->company }}</p>
                            <p class="mb-1">: {{ $quote->pic->name_pic }}</p>
                            <p class="mb-1">: {{ $quote->pic->client->phone }}</p>
                        </div>
                        <div class="col-3 fw-medium text-end">
                            <p class="mb-1">Sales :</p>
                            <p class="mb-1">Email :</p>
                        </div>
                        <div class="col-3 text-end">
                            <p class="mb-1">
                                @if ($quote->tax != '0')
                                    {{ $contract->type == 'Selling' ? 'PT Reftech Jaya Optima' : 'PT Kojisha Innotiv Indonesia' }}
                                @else
                                    {{ $quote->sales->name }}
                                @endif
                            </p>
                            <p class="mb-1"> {{ $quote->pic->client->email }}</p>
                        </div>
                    </div>
                </div>
                @if ($quote->type == 'Sparepart')
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead class="table-light border-top">
                                <tr>
                                    <th>No.</th>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Discount</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 0;
                                @endphp
                                @foreach ($dquote as $product)
                                    @php
                                        $no++;
                                    @endphp
                                    <tr style="font-size: 13px">
                                        <td class="align-top">{{ $no }}</td>
                                        <td class="text-nowrap align-top">
                                            <p class="mb-0 fw-semibold" style="font-size: 12px">
                                                @if ($product->id_equivalent == '0')
                                                    -
                                                @else
                                                    {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
                                                @endif
                                            </p>
                                            <pre class="mb-0"
                                                style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail_product }}</pre>
                                        </td>
                                        <td class="align-top text-end">{{ $product->amount == 0 ? 'SBO' : 'RP ' . number_format($product->price, 0, '', '.') }}
                                        </td>
                                        <td class="align-top">{{ $product->qty }} {{ $product->info_qty }} </td>
                                        <td class="align-top">{{ $product->disc }}%</td>
                                        <td class="align-top text-end">RP {{ number_format($product->amount, 0, '', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="align-top px-4 py-5">
                                        <span>Thanks for your business</span>
                                    </td>
                                    <td colspan="2" class="text-end px-4 py-5">
                                        <p class="mb-2">Subtotal:</p>
                                        <p class="mb-2">Discount:</p>
                                        <p class="mb-2">Total After Discount:</p>
                                        <p class="mb-2">Tax {{ $quote->tax == '11' ? '(11%)' : '' }}:</p>
                                        <p class="mb-2">Shipping Cost:</p>
                                        <p class="mb-0">Total:</p>
                                    </td>
                                    @php
                                        if ($quote->diskon > 0) {
                                            $afterDisc = $quote->subtotal - $quote->diskon;
                                        } else {
                                            $afterDisc = $quote->subtotal;
                                        }

                                        if ($quote->tax != 0) {
                                            $vat = ($afterDisc * $quote->tax) / 100;
                                        } else {
                                            $vat = 0;
                                        }
                                    @endphp
                                    <td colspan="2" class="px-4 py-5">
                                        <p class="fw-semibold mb-2 text-end">RP
                                            {{ number_format($quote->subtotal, 0, '', '.') }}</p>
                                        <p class="fw-semibold mb-2 text-end">RP
                                            {{ number_format($quote->diskon, 0, '', '.') }}
                                        <p class="fw-semibold mb-2 text-end">RP
                                            {{ number_format($quote->subtotal - $quote->diskon, 0, '', '.') }}
                                        <p class="fw-semibold mb-2 text-end">
                                            {{ $tax == '0' ? '0' : 'RP ' . number_format($vat, 0, '', '.') }}</p>
                                        </p>
                                        <p class="fw-semibold mb-2 text-end">RP
                                            {{ number_format($quote->shipping, 0, '', '.') }}</p>
                                        <p class="fw-semibold mb-0 text-end">RP
                                            {{ number_format($quote->harga_total, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <h5 class="my-4">Term & Condition</h5>
                        <div class="row">
                            <div class="col-3 fw-medium">
                                <p class="mb-1">Validity Of Quotation</p>
                                <p class="mb-1">Price </p>
                                <p class="mb-1">Delivery Process </p>
                                <p class="mb-1">Payment </p>
                                <p class="mb-1">Note </p>
                            </div>
                            <div class="col">
                                <p class="mb-1">: {{ $quote->termncon[0]->validity }}</p>
                                <p class="mb-1">: {{ $quote->termncon[0]->pricing }}</p>
                                <p class="mb-1">: {{ $quote->termncon[0]->delivery_process }}</p>
                                <p class="mb-1">: {{ $quote->termncon[0]->payment }}</p>
                                <p class="mb-1">: {{ $quote->termncon[0]->note }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered m-0">
                            <thead class="table-light border-top">
                                <tr>
                                    <th style="width: 1%">No.</th>
                                    <th style="width: 50%">Item Description</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $abjad = 64;
                                @endphp
                                @foreach ($subQuote as $subJudul)
                                    @php
                                        $no = 0;
                                        $abjad++;
                                    @endphp
                                    <tr style="font-size: 13px border-bottom:none !important;" class="border-top">
                                        <td class="align-top"
                                            style="border-bottom:none !important; background-color: #f0f0f0;">
                                            <p class="fw-bold mb-0">{{ chr($abjad) }}</p>
                                        </td>
                                        <td class="text-nowrap align-top" colspan="4"
                                            style="border-bottom:none !important; background-color: #f0f0f0;">
                                            <p class="fw-bold mb-0">{{ $subJudul->subtitle }}</p>
                                        </td>
                                    </tr>
                                    @foreach ($subJudul->detail as $product)
                                        <tr
                                            style="font-size: 13px; border-bottom:none !important; border-top:none !important;">
                                            <td class="align-top py-1" style="border-bottom:none !important;">
                                                @php
                                                    $no++;
                                                @endphp
                                                <p class="mb-1">{{ $no }}</p>
                                            </td>
                                            <td class="text-nowrap align-top py-1" style="border-bottom:none !important;">
                                                <p class="mb-1">{{ $product->product }}</p>
                                                @if ($product->detail != '-')
                                                    <pre class="mb-0"
                                                        style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail }}</pre>
                                                @endif
                                            </td>
                                            <td class="align-top py-1" style="border-bottom:none !important;">
                                                <p class="mb-0">{{ $product->qty }} {{ $product->info_qty }}</p>
                                            </td>
                                            <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                                <p class="mb-0">RP {{ number_format($product->price, 0, '', '.') }}</p>
                                            </td>
                                            <td class="align-top py-1 text-end" style="border-bottom:none !important;">
                                                <p class="mb-0">RP {{ number_format($product->amount, 0, '', '.') }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                <tr class="border-top">
                                    <td colspan="4" class="px-4 border-right" style="background-color: #E7FF00">
                                        <p class="fw-semibold mb-0 text-black">TOTAL PRICE,
                                            {{ $quote->tax != 0 ? 'INCLUDE' : 'EXCLUDE' }} VAT 11%</p>
                                    </td>
                                    <td class="text-end px-4 border-left" style="background-color: #E7FF00">
                                        <p class="fw-semibold mb-0 text-end text-black">RP
                                            {{ number_format($quote->harga_total, 0, '', '.') }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body mt-2">
                        <h5>Note :</h5>
                        <pre class="mb-0"
                            style="font-size: 16px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap; text-align: justify;">{{ $quote->termncon[0]->note }}</pre>
                    </div>
                    <div class="card-body mt-2">
                        <h5 class="my-4">Term & Condition</h5>
                        <div class="row">
                            <div class="col-3 fw-medium">
                                <p class="mb-1">Validity Of Quotation</p>
                                <p class="mb-1">Price </p>
                                <p class="mb-1">Delivery Process </p>
                                <p class="mb-1">Payment </p>
                                <p class="mb-1">Warranty </p>
                            </div>
                            <div class="col">
                                <p class="mb-1">: {{ $quote->termncon[0]->validity }}</p>
                                <p class="mb-1">: {{ $quote->termncon[0]->pricing }}</p>
                                <p class="mb-1">: {{ $quote->termncon[0]->delivery_process }}</p>
                                <p class="mb-1">: {{ $quote->termncon[0]->payment }}</p>
                                <p class="mb-1">: {{ $quote->termncon[0]->warranty }}</p>
                            </div>
                        </div>
                    </div>
                @endif


                @if ($contract->type == 'Selling')
                    <div class="row mt-3">
                        <div class="col-4 my-5 text-center">
                            <p class="fs-normal fw-medium">Authorized By,</p>
                            @if ($quote->tax != '0')
                                <img src="{{ asset('/asset') }}/contract\sign-irene.jpeg" alt="" srcset=""
                                    style="width: 100px; height: 77px;">
                            @else
                                <img src="{{ asset('/asset') }}/sign\ttdirene.jpg" alt="" srcset=""
                                    style="width: 100px; height: 77px;">
                            @endif
                            <p class="pt-3">Mrs. Irene</p>
                            @if ($quote->tax != '0')
                                <p>PT. Reftech Jaya Optima</p>
                            @endif
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4 my-5 text-center">
                            <p class="fs-normal fw-medium">Accepted By Customer,</p>
                            <div class="pb-5"></div>
                            <p class="pt-5">{{ $quote->pic->name_pic }}</p>
                            <p>{{ $quote->pic->client->company }}</p>
                        </div>
                    </div>
                @else
                    <div class="row mt-5">
                        <div class="col-4 my-5 text-center">
                            <p class="fs-normal fw-medium">Authorized By,</p>
                            <img src="{{ asset('/asset') }}/contract\sign-dedeh.png" alt="" srcset=""
                                style="width: 100px; height: 77px;">
                            <p class="pt-3">Dedeh Sulastri</p>
                            <p>Director</p>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4 my-5 text-center">
                            <p class="fs-normal fw-medium">Accepted By Customer,</p>
                            <div class="pb-5"></div>
                            <p class="pt-5">{{ $quote->pic->name_pic }}</p>
                            <p>{{ $quote->pic->client->company }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    @if ($contract->level == '0')
                        <button type="button" class="btn btn-primary d-grid w-100 waves-effect mb-3"
                            data-bs-toggle="modal" data-bs-target="#acceptContract{{ $contract->id }}">
                            Accept
                        </button>
                        <a href="#" class="btn btn-outline-danger d-grid w-100 mb-3 waves-effect delete-contract"
                            data-id="{{ $contract->id }}" data-quote="{{ $quote->id }}">Reject</a>
                    @elseif($contract->level == '1')
                        <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                            href="{{ route('contract.print', $contract->id) }}">
                            Download
                        </a>
                        <a href="#" class="btn btn-outline-danger d-grid w-100 mb-3 waves-effect delete-contract"
                            data-id="{{ $contract->id }}" data-quote="{{ $quote->id }}">Delete</a>
                    @endif
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
            {{-- End : Button Invoice --}}
        </div>
        @php
            // Inisialisasi variabel
            $sellingContract = null;
            $orderContract = null;
            $requestedSellingContract = null;
            $requestedOrderContract = null;
            $result = '';

            // Loop untuk menemukan kontrak dengan tipe Selling dan Order
            if ($contract->type == 'Selling' && $contract->quotation->tax == '0') {
                $sellingNonTax = $contract;
            } elseif ($contract->type == 'Selling' && $contract->quotation->tax == '11') {
                $sellingTax = $contract;
            } elseif ($contract->type == 'Order' && $contract->quotation->tax == '0') {
                $orderNonTax = $contract;
            } elseif ($contract->type == 'Order' && $contract->quotation->tax == '11') {
                $orderTax = $contract;
            }
            if (isset($sellingTax)) {
                $result = $formattedNumberSP;
            } elseif (isset($sellingNonTax)) {
                $result = $formattedNumberSNP;
            } elseif (isset($orderTax)) {
                $result = $formattedNumberCP;
            } elseif (isset($orderNonTax)) {
                $result = $formattedNumberCNP;
            }
        @endphp
        @include('components.modal.accounting.accept-contract')
    @endsection
    @push('after-style')
        <!-- Page CSS -->
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    @endpush
    @push('after-script')
        <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    @endpush
    @push('page-script')
        <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    @endpush
    @push('script')
        <script>
            $(document).on('click', '.delete-contract', function() {
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
                                        text: "Your file has been deleted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/quotation/' + quoteId;
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
            $('#backButton').click(function() {
                window.history.back();
            });
        </script>
    @endpush
