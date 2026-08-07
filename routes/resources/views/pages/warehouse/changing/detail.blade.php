@extends('layouts.sales.app')
@section('title', 'Detail Quotation')
@section('content')
    <div class="row invoice-preview">
        {{-- Invoice --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card mb-3">
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
                            <h3 class="fw-bold">Change Warehouse</h3>
                            <div>
                                <span class="fw-bolder">{{ $change->title }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="table-responsive">
                    <table class="table m-0">
                        <thead class="table-light border-top">
                            <tr>
                                <th>No.</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>From</th>
                                <th>To</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 0;
                            @endphp
                            @foreach ($detChange as $product)
                                @php
                                    $no++;
                                @endphp
                                <tr style="font-size: 13px">
                                    <td class="align-top">{{ $no }}</td>
                                    <td class="text-nowrap align-top">
                                        <p class="mb-0 fw-semibold" style="font-size: 12px">
                                            {{ $product->replacement->product->commodity }}
                                            ({{ $product->replacement->product->detail_desc }})
                                            ||
                                            {{ $product->replacement->replacement }} -
                                            {{ $product->replacement->product->go == 'Genuine' ? 'G' : 'R' }}
                                        </p>
                                        <pre class="mb-0"
                                            style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $product->detail_product }}</pre>
                                    </td>
                                    <td class="align-top">{{ $product->qty }}</td>
                                    <td class="align-top">{{ $change->from }}</td>
                                    <td class="align-top">{{ $change->to }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body">
                    <a class=" text-white btn btn-primary d-grid w-100 waves-effect" data-bs-toggle="modal"
                        data-bs-target="#acceptNote" {{ $change->status == 2 ? 'disabled' : '' }}>
                        {{ $change->status == 2 ? 'Accepted' : 'Accept' }}
                    </a>
                    {{-- <a href="#" data-id="{{ $change->id }}"
                        class="btn btn-primary d-grid w-100 waves-effect mb-3 accept-change"
                        {{ $change->status == 2 ? 'disabled' : '' }}>
                        {{ $change->status == 2 ? 'Accepted' : 'Accept' }}
                    </a> --}}
                </div>
            </div>
        </div>
        {{-- @endif --}}
    </div>
    {{-- End : Button Invoice --}}
    @include('components.modal.product.accept-note')
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
        $(document).on('click', '.accept-change', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure change warehouse?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, change warehouse!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('change-warehouse') }}/accept/' + id,
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
                                    window.location.href =
                                        '/change-warehouse'
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed change warehouse!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "You cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        // $(document).on('click', '.accept-change', function() {
        //     var id = $(this).data('id');
        //     Swal.fire({
        //         title: "Are you sure to Accept this?",
        //         text: "You won't be able to revert this!",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonText: "Yes, Accept it!",
        //         customClass: {
        //             confirmButton: "btn btn-primary me-3 waves-effect waves-light",
        //             cancelButton: "btn btn-label-secondary waves-effect",
        //         },
        //         buttonsStyling: false,
        //     }).then(function(result) {
        //         if (result.value) {
        //             $.ajax({
        //                 'url': '{{ url('change-warehouse') }}/accept/' + id,
        //                 'type': 'POST',
        //                 'data': {
        //                     '_method': 'POST',
        //                     '_token': '{{ csrf_token() }}'
        //                 },
        //                 success: function(response) {
        //                     if (response == 1) {
        //                         Swal.fire({
        //                             icon: "success",
        //                             title: "Converted!",
        //                             text: "Your file has been converted.",
        //                             customClass: {
        //                                 confirmButton: "btn btn-success waves-effect",
        //                             },
        //                         })
        //                         window.setTimeout(function() {
        //                             window.location.href = '/change-warehouse';
        //                         }, 2000);
        //                     } else {
        //                         Swal.fire({
        //                             icon: 'error',
        //                             title: 'Oops...',
        //                             text: 'Data Failed to Convert!'
        //                         });
        //                         console.log('❌ Gagal, response:', response);
        //                     }
        //                 }
        //             });
        //         } else if (result.dismiss === Swal.DismissReason.cancel) {
        //             Swal.fire({
        //                 title: "Cancelled",
        //                 text: "Your Convert is cancelled :)",
        //                 icon: "error",
        //                 customClass: {
        //                     confirmButton: "btn btn-success waves-effect",
        //                 },
        //             });
        //         }
        //     });
        // });
    </script>
@endpush
