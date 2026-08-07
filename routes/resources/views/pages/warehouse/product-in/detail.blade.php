@extends('layouts.sales.app')
@section('title', 'Product In')
@section('content')
    <div class="row invoice-preview">
        {{-- Invoice --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md" src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt=""
                                            srcset="" width="60%">
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold">Barang Masuk</h3>
                            <div>
                                <span class="fw-bolder">#{{ $product->invoice }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted">{{ Carbon\Carbon::parse($product->date)->format('d-m-Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="row">
                        <div class="col-4 col-lg-2 fw-medium">
                            <p class="mb-1">Supplier </p>
                            <p class="mb-1">Note</p>
                        </div>
                        <div class="col-8">
                            <p class="mb-1">: {{ $product->supplier ?? optional($product->supp)->supplier ?? '-' }}</p>
                            <p class="mb-1">: {{ $product->note }}</p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table m-0 mb-4">
                        <thead class="table-light border-top">
                            <tr>
                                <th>No.</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Modal</th>
                                <th>Discount</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 0;
                            @endphp
                            @foreach ($detail as $products)
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
                                    @if (Auth::user()->role == 'Logistic')
                                        <td class="align-top">RP {{ str_repeat('*', strlen((string) $products->modal)) }}
                                        </td>
                                        <td class="align-top">RP {{ str_repeat('*', strlen((string) $products->disc)) }}
                                        </td>
                                        <td class="align-top">RP {{ str_repeat('*', strlen((string) $products->amount)) }}
                                        </td>
                                    @else
                                        <td class="align-top">RP {{ number_format($products->modal, 0, '', '.') }}</td>
                                        <td class="align-top">RP {{ number_format($products->disc, 0, '', '.') }}</td>
                                        <td class="align-top">RP {{ number_format($products->amount, 0, '', '.') }}</td>
                                    @endif
                                </tr>
                            @endforeach
                            <tr style="font-size: 13px">
                                <td colspan="4" style="border:none;"></td>
                                <td>Subtotal</td>
                                @if (Auth::user()->role == 'Logistic')
                                    <td>: RP {{ str_repeat('*', strlen((string) $product->subtotal)) }}</td>
                                @else
                                    <td>: RP {{ number_format($product->subtotal, 0, '', '.') }}</td>
                                @endif
                            </tr>
                            <tr style="font-size: 13px">
                                <td colspan="4" style="border:none;"></td>
                                <td>Tax {{ $product->tax == '11' ? '11%' : '' }}</td>
                                @if (Auth::user()->role == 'Logistic')
                                    <td>: RP {{ str_repeat('*', strlen((string) $tax)) }}</td>
                                @else
                                    <td>: RP {{ number_format($tax, 0, '', '.') }}</td>
                                @endif
                            </tr>
                            <tr style="font-size: 13px;">
                                <td colspan="4" style="border:none;"></td>
                                <td>Shipping</td>
                                <td>: RP {{ number_format($product->shipping, 0, '', '.') }}</td>
                            </tr>
                            <tr style="font-size: 13px">
                                <td colspan="4" style="border:none;"></td>
                                <td style="border:none;">Total</td>
                                @if (Auth::user()->role == 'Logistic')
                                    <td style="border:none;">: RP {{ str_repeat('*', strlen((string) $product->total)) }}
                                    </td>
                                @else
                                    <td style="border:none;">: RP {{ number_format($product->total, 0, '', '.') }}</td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
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
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            @if ($product->accept == '0')
                <div class="card mb-3">
                    <div class="card-body">
                        <a href="#" class="btn btn-success d-grid w-100 waves-effect accept-product"
                            data-id="{{ $product->id }}">Accept</a>
                    </div>
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('productIn.print', $product->id) }}">
                        Download
                    </a>
                    {{-- <a href="javascript{0}" type="button" class="btn btn-outline-secondary d-grid w-100 waves-effect mb-3">
                        Download
                    </a> --}}
                    @if (Auth::user()->role == 'Admin')
                        <button type="button" class="btn btn-secondary d-grid w-100 waves-effect mb-3"
                            data-bs-toggle="modal" data-bs-target="#editPrice-{{ $product->id }}">
                            Edit Price
                        </button>
                    @endif
                    <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete-invoice"
                        data-id="{{ $product->id }}">Delete</a>
                </div>
            </div>
        </div>
        {{-- End : Button Invoice --}}
    </div>
    @include('components.modal.product.edit-price')

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
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }


        $(".invoice-item-modal-label").on('keyup', function() {
            var input = $(this)
            var id = input.data('id');
            var input_val = input.val();

            // original length
            var original_len = input_val.length;

            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // send updated string to input
            input.val(input_val);
            var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
            // console.log(id);
            $(`#modal-${id}`).val(nomorInt);
            var modal = $(`#modal-${id}`).val();
            console.log(modal);
        });

        $(document).on('click', '.delete-invoice', function() {
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
                        'url': '{{ url('product-in') }}/' + id,
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
                                    window.location.href = '/product-in';
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
        $(document).on('click', '.accept-product', function() {
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
                        'url': '{{ url('product-in') }}/accept/' + id,
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
                                window.setTimeout(function() {
                                    window.location.href = '/product-in/' + id;
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
