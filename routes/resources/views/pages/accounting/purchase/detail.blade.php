@extends('layouts.sales.app')
@section('title', 'Detail Purchase Order')
@section('content')
    {{-- @php
        if ($quote->pic->client->info == 'Reftech') {
            $bgColor = 'rgb(224, 248, 248)';
        } else {
            $bgColor = 'rgb(255, 232, 210)';
        }
    @endphp --}}
    <div class="row invoice-preview">
        {{-- Invoice --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card mb-3">
                <div class="card-body">
                    {{-- @if ($quote->pic->client->info == 'Reftech') --}}
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
                            <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                            <div style="font-size: 10px">
                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                <p class="mb-1">
                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                    {{ '   ' }}<i
                                        class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>info@reftech.id
                                </p>
                                <p class="mb-1">
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold">Purchase Order</h3>
                            <div>
                                <span class="fw-bolder">#{{ $purchase->no_po }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted">{{ Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }}</span>
                            </div>
                        </div>
                    </div>
                    {{-- @else
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md" src="{{ asset('/asset') }}/logo/Kojisha-Log.png"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                <div style="font-size: 10px">
                                    <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                    <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                    <p class="mb-1">
                                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                                        {{ '   ' }}<i
                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h3 class="fw-bold">QUOTATION</h3>
                                <div>
                                    <span class="fw-bolder">#{{ $quote->no_quote }}</span>
                                </div>
                                @if ($quote->num_rev >= 1)
                                    <div class="mt-1">
                                        <span class="fw-bolder py-1 px-2"
                                            style="background-color: {{ $bgColor }}; border-radius: 10px;">REV -
                                            {{ $quote->num_rev }}</span>
                                    </div>
                                @endif
                                <div class="mt-1">
                                    <span
                                        class="text-muted">{{ $quote->status == '25' ? 'DRAFT' : ($quote->status == '50' ? 'SEND' : ($quote->status == '75' ? 'NEGOTIATION' : ($quote->status == '100' ? 'DONE PO' : ($quote->status == '0' ? 'LOSS' : '')))) }}</span>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="text-muted">{{ Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif --}}
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="row">
                        <div class="col-6">
                            <h6 class="fw-semibold fs-4 mb-3">Vendor:</h6>
                        </div>
                        <div class="col-6 mb-2">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-4 fw-medium">
                                    <p class="mb-1">ATTN </p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-1">: {{ $purchase->attn }}</p>
                                </div>
                                <div class="col-4 fw-medium">
                                    <p class="mb-1">Company </p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-1">: {{ $purchase->company }}</p>
                                </div>
                                <div class="col-4 fw-medium">
                                    <p class="mb-1">Phone </p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-1">: {{ $purchase->phone }}</p>
                                </div>
                                <div class="col-4 fw-medium">
                                    <p class="mb-1">Address</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-1">: {{ $purchase->address }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col-6 fw-medium text-end">
                                    <p class="mb-1">Mobile :</p>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="mb-1"> {{ $purchase->mobile ?? '-' }}</p>
                                </div>
                                <div class="col-6 fw-medium text-end">
                                    <p class="mb-1">Email :</p>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="mb-1"> {{ $purchase->email ?? '-' }}</p>
                                </div>
                                <div class="col-6 fw-medium text-end">
                                    <p class="mb-1">Payment :</p>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="mb-1"> {{ $purchase->payment ?? '-' }}</p>
                                </div>
                                <div class="col-6 fw-medium text-end">
                                    <p class="mb-1">Delivery Time :</p>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="mb-1"> {{ $purchase->delivery ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mb-5">
                    <table class="table m-0">
                        <thead class="table-light border-top">
                            <tr>
                                <th>No.</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Disc</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 0;
                            @endphp
                            @foreach ($dPurchase as $product)
                                @php
                                    $no++;
                                @endphp
                                <tr style="font-size: 13px">
                                    <td class="align-top">{{ $no }}</td>
                                    <td class="text-nowrap align-top">
                                        <p class="mb-0 fw-semibold" style="font-size: 12px">
                                            {{ $product->product }}
                                        </p>
                                    </td>
                                    <td class="align-top">{{ $product->qty }} {{ $product->info_qty }} </td>
                                    <td class="align-top text-end">RP {{ number_format($product->price, 0, '', '.') }}</td>
                                    <td class="align-top">{{ $product->disc }} % </td>
                                    <td class="align-top text-end">RP {{ number_format($product->amount, 0, '', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr style="border-bottom: #FFFFFF;">
                                <td colspan="4" class="align-top px-4 py-5">
                                    <div class="row">
                                        <div class="col-3 fw-medium">
                                            <p class="mb-1">Note </p>
                                        </div>
                                        <div class="col">
                                            <p class="mb-1">: {{ $purchase->note }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end px-4 py-5">
                                    <p class="mb-2">Subtotal:</p>
                                    <p class="mb-2">Diskon:</p>
                                    @if ($purchase->vat > 0)
                                        <p class="mb-2">Total:</p>
                                        <p class="mb-2">DPP Nilai Lain :</p>
                                        <p class="mb-2">VAT 12% :</p>
                                    @endif
                                    @if ($totalPph > 0)
                                        <p class="mb-2">Total PPH :</p>
                                    @endif
                                    <p class="mb-2 fw-bolder">Total Price :</p>
                                </td>
                                @php
                                    $tax = ($purchase->total * 11) / 100;
                                    $noTax = $purchase->total - ($purchase->total * 11) / 100;
                                    $dpp = ($noTax * 11) / 12;
                                @endphp
                                <td class="px-4 py-5">
                                    <p class="fw-semibold mb-2 text-end">RP
                                        {{ number_format($purchase->subtotal, 0, '', '.') }}</p>
                                    <p class="fw-semibold mb-2 text-end">RP
                                        {{ number_format($purchase->diskon, 0, '', '.') }}</p>
                                    @if ($purchase->vat > 0)
                                        <p class="fw-semibold mb-2 text-end">RP
                                            {{ number_format($noTax, 0, '', '.') }}
                                        </p>
                                        <p class="fw-semibold mb-2 text-end">
                                            {{ $dpp == '0' ? '0' : 'RP ' . number_format($dpp, 0, '', '.') }}</p>
                                        <p class="fw-semibold mb-2 text-end">
                                            {{ $tax == '0' ? '0' : 'RP ' . number_format($tax, 0, '', '.') }}</p>
                                    @endif
                                    @if ($totalPph > 0)
                                        <p class="fw-semibold mb-2 text-end">
                                            {{ $totalPph == '0' ? '0' : 'RP ' . number_format($totalPph, 0, '', '.') }}
                                        </p>
                                    @endif
                                    <p class="fw-semibold mb-2 text-end">
                                        {{ $purchase->total == '0' ? '0' : 'RP ' . number_format($purchase->total, 0, '', '.') }}
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-4 text-center">
                        <p class="fs-normal fw-bolder">Authorized By.</p>
                        <img src="{{ url('') . '/asset/sign/ttdAngel.jpg' }}" alt="" srcset=""
                            height="77">
                        <p class="pt-3">Reftech Jaya Optima</p>
                    </div>
                    <div class="col-4"></div>
                    <div class="col-4 text-center">
                        <p class="fs-normal fw-bolder">Accepted By Vendor.</p>
                        <div class="pb-5"></div>
                        <p class="pt-3 mb-0 mt-2">{{ $purchase->attn }}</p>
                        <p>{{ $purchase->company }}</p>
                    </div>
                </div>
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('purchase.show_print', $purchase->id) }}">
                        Download
                    </a>
                    <a class="btn btn-label-info d-grid w-100 mb-3 waves-effect"
                        href="{{ route('purchase.edit', $purchase->id) }}">
                        Edit
                    </a>
                    <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-purchase mb-3"
                        data-id="{{ $purchase->id }}">Delete</a>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
            <div class="card">
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
                    {{-- <a type="button" data-bs-toggle="modal" data-bs-target="#addPph"
                        class="d-grid w-100 waves-effect mb-3">
                        <button type="button" class="btn btn-twitter">
                            Input PPH Manual
                        </button>
                    </a> --}}
                </div>
            </div>
        </div>
    </div>
    @include('components.modal.purchase.pph')
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
        let formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        });
        $('#backButton').click(function() {
            window.history.back();
        });

        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }
        $(".invoice-item-price-label").on('keyup', function() {
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
            console.log(id);
            $(`#price-${id}`).val(nomorInt);
        });
        $(".invoice-item-price-label").on('keyup', function() {
            var total = 0; // Mengatur ulang total pada setiap event keyup
            $('.invoice-item-price').each((index, element) => {
                let value = $(element).val();
                value = value ? parseInt(value) : 0;
                total += value;
            });
            $('#totalLabel').val(`${formatter.format(total)}`);
            $('#total').val(total);
        });
        $(".invoice-item-amount-label").on('keyup', function() {
            var input = $(this)
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
            $(`#amount`).val(nomorInt);
        });
        $(document).on('click', '.delete-quotation', function() {
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
                        'url': '{{ url('quotation') }}/' + id,
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
                                    window.location.href = '/quotation';
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
            // Swal.fire({
            //     title: "Are you sure?",
            //     text: "You won't be able to revert this!",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonColor: "#3085d6",
            //     cancelButtonColor: "#d33",
            //     confirmButtonText: "Yes, delete it!"
            // }).then((result) => {
            //     if (result.isConfirmed) {
            //         $.ajax({
            //             'url': '{{ url('leads') }}/' + id,
            //             'type': 'POST',
            //             'data': {
            //                 '_method': 'DELETE',
            //                 '_token': '{{ csrf_token() }}'
            //             },
            //             success: function(response) {
            //                 if (response == 1) {
            //                     Swal.fire({
            //                         title: "Deleted!",
            //                         text: "Your file has been deleted.",
            //                         icon: "success"
            //                     })
            //                     window.setTimeout(function() {
            //                         location.reload();
            //                     }, 2000);
            //                 } else {
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: 'Oops...',
            //                         text: 'Data Failed to Delete!'
            //                     });
            //                 }
            //             }
            //         });
            //     }
            // });
        });
        $(document).on('click', '.cancel-po', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Convert this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Convert it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('quotation') }}/' + id + '/cancel_po',
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/quotation/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.convert-flag', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Convert this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Convert it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('quotation') }}/' + id + '/convert_flag',
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your file has been converted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/quotation/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
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
        $(document).on('click', '.delete-fee', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Delete this fee?",
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
                        'url': '{{ url('quotation') }}/' + id +
                            '/delete_fee',
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your fee has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/quotation/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Delete is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-file', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Delete this file?",
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
                        'url': '{{ url('quotation') }}/' + id +
                            '/delete_po',
                        'type': 'DELETE',
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
                                    window.location.href =
                                        '/quotation/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Delete is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.request-selling', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Request this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Request it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('request/selling-contract') }}/' +
                            id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Requested!",
                                    text: "Your file has been Requested.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/quotation/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Request!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Request is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.request-order', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Request this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Request it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('request/confirm-order') }}/' +
                            id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Requested!",
                                    text: "Your file has been Requested.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/quotation/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Request!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Request is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.unarchive-quotation', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Un Archive this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Un Archive it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('un-archive') }}/quotation/' +
                            id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Converted!",
                                    text: "Your Quotation has been Un Archive.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/quotation/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.delete-archive', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure Delete this with all source (invoice, selling contract, ect)?",
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
                        'url': '{{ url('delete-archive') }}/quotation/' +
                            id,
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
                                    window.location.href =
                                        '/quotation';
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
        $(document).on('click', '.delete-payments', function() {
            var id = $(this).data('id');
            var quote = $(this).data('quote');
            Swal.fire({
                title: "Are you sure Delete this payment?",
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
                        'url': '{{ url('quotation') }}/' + id +
                            '/delete_payment',
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
                                    window.location.href =
                                        '/quotation/' +
                                        quote;
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
        $(document).on('change', '.change-primary', function() {
            var selectedValue = $(this).val();
            var rowId = $(this).data('id');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                type: 'POST',
                url: '/quotation/' + selectedValue + '/change_primary',
                data: {
                    status: selectedValue,
                    _token: csrfToken
                },
                success: function(response) {
                    console.log(
                        'Perubahan status berhasil dikirim ke server');
                    window.setTimeout(function() {
                        window.location.href = '/quotation/' +
                            selectedValue;
                    }, 10);
                },
                error: function(error) {
                    console.error('Gagal mengirim permintaan ke server:',
                        error);
                }
            });
        });
        $(document).on('change', '.change-primary-service', function() {
            var selectedValue = $(this).val();
            var rowId = $(this).data('id');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                type: 'POST',
                url: '/quotation/' + selectedValue + '/change_primary',
                data: {
                    status: selectedValue,
                    _token: csrfToken
                },
                success: function(response) {
                    console.log(
                        'Perubahan status berhasil dikirim ke server');
                    window.setTimeout(function() {
                        window.location.href =
                            '/quote/service-show/' + selectedValue;
                    }, 10);
                },
                error: function(error) {
                    console.error('Gagal mengirim permintaan ke server:',
                        error);
                }
            });
        });
        $(document).on('click', '.btn-no-address', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "This Quotation Don't Have Address",
                text: "You need to Putting Address on your client!",
                icon: "warning",
                showCancelButton: false,
                showConfirmButton: false,
                cancelButtonText: "Oke!",
                customClass: {
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            });
        });

        function copyDownloadLink(link) {
            navigator.clipboard.writeText(link)
                .then(() => {
                    alert('Link berhasil disalin!');
                })
                .catch(err => {
                    alert('Gagal menyalin link');
                    console.error(err);
                });
        }
    </script>
@endpush
