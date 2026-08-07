@extends('layouts.sales.app')
@section('title', 'Purchase Invoice AP')
@section('content')
    <h4 class="fw-bold py-3 mb-4"> <span class="text-muted">Account Payable / Purchase Invoice/</span> Invoice #123123 </h4>
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
                                            <a href="{{ route('product-in.show', $product->id) }}"
                                                class="text-black fs-5 fw-medium" target="_blank">
                                                {{ $product->invoice }}
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
                                            <h5>{{ Carbon\Carbon::parse($product->date)->format('d-m-Y') }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row mb-3">
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
                    </div> --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-muted">Supplier</p>
                                    <h5 class="mb-0">{{ $product->supp->supplier ?? $product->supplier }}</h5>
                                    <p class="mb-0">{{ $product->supp->info }}</p>
                                    <h6 class="mb-0">{{ $product->supp->npwp ?? '' }}</h6>
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
                    <div class="row">
                        <div class="col-6">
                            <p>Total Invoice</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp {{ number_format($product->total, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-6">
                            <p>Advance Payment</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp
                                {{ $product->accept == '1' ? number_format($product->accept, 0, ',', '.') : '0' }}</p>
                        </div>
                        <div class="col-6">
                            <p>Subtotal</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp {{ number_format($product->accept, 0, ',', '.') }}</p>
                        </div>
                        {{-- @php
                            $vat = ($quote->subtotal / 100) * $quote->tax;
                        @endphp
                        @if ($quote->tax > 0)
                            <div class="col-6">
                                <p>VAT 11%</p>
                            </div>
                            <div class="col-6">
                                <p class="text-end fw-bolder">Rp {{ number_format($vat, 0, ',', '.') }}</p>
                            </div>
                        @endif --}}
                        <hr>
                        <div class="col-6">
                            <p class="fw-bolder">Grand Total</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp {{ number_format($product->accept, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-6">
                            <p>Outstanding</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp
                                {{ $product->accept == '1' ? number_format($product->accept, 0, ',', '.') : '0' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h4>Detail Product In</h4>
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
                        @php
                            $no = 0;
                        @endphp
                        @foreach ($detProduct as $products)
                            @php
                                $no++;
                            @endphp
                            <tr style="font-size: 13px">
                                <td class="align-top">{{ $no }}</td>
                                <td class="text-nowrap align-top">
                                    <p class="mb-0 fw-semibold" style="font-size: 12px">
                                        {{ $products->detailProduct->replacement }}
                                    </p>
                                    <pre class="mb-0"
                                        style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $products->detailProduct->product->description }}</pre>
                                </td>
                                <td class="align-top">{{ $products->qty }} {{ $products->detailProduct->product->unit }}
                                </td>
                                <td class="align-top">RP {{ number_format($products->modal, 0, '', '.') }}</td>
                                <td class="align-top">RP {{ number_format($products->amount, 0, '', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-light">
                            <td colspan="4" class="text-end">
                                <p class="mb-0">Total:</p>
                            </td>
                            <td colspan="2">
                                <p class="fw-semibold mb-0 text-end">RP
                                    {{ number_format($product->total, 0, '', '.') }}</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="mb-3">
                    Retur Barang
                </h5>
                @if ($product->accept == 0)
                    <a type="button" data-bs-toggle="modal" data-bs-target="#productReturn">
                        <button type="button" class="btn btn-primary d-grid waves-effect float-end">
                            Retur Barang
                        </button>
                    </a>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table m-0">
                    <thead class="">
                        <tr>
                            <th>Item</th>
                            <th>Desc</th>
                            <th>Qty</th>
                            <th>Note</th>
                            <th style="width: 20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 0;
                        @endphp
                        @forelse ($return as $retur)
                            @php
                                $no++;
                            @endphp
                            <tr style="font-size: 13px">
                                <td class="align-top">{{ $no }}</td>
                                <td class="text-nowrap align-top">
                                    <p class="mb-0 fw-semibold" style="font-size: 12px">
                                        {{ $retur->replacement->replacement }}
                                    </p>
                                    <pre class="mb-0"
                                        style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $retur->replacement->product->description }}</pre>
                                </td>
                                <td class="align-top">{{ $retur->qty }}
                                    {{ $retur->replacement->product->unit }}
                                </td>
                                <td class="align-top">{{ $retur->note }}</td>
                                <td class="align-top">
                                    @if ($retur->status == 0)
                                        <a href="#" class="btn btn-primary d-grid w-100 waves-effect clear-return"
                                            data-id="{{ $retur->id }}">Clear Return</a>
                                    @else
                                        <p class="text-success">Sudah Clear</p>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada return di invoice ini</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- <div class="card">
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
    </div> --}}
    @include('components.modal.payable.return')
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
    <script>
        $(document).on('click', '.clear-return', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Accept it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('product-in') }}/clear-return/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Accepted!",
                                    text: "Your file has been Accepted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Accept!'
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
    </script>
@endpush
