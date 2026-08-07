@extends('layouts.sales.app')
@section('title', 'Product Out')
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
                            <h3 class="fw-bold">Barang Keluar ({{ $product->vers }})</h3>
                            <div>
                                <span
                                    class="fw-bolder">#{{ $product->no_type == '1' ? $product->invoice : $product->po }}</span>
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
                            <p class="mb-1">Customers </p>
                        </div>
                        <div class="col-8 col-lg-10">
                            <pre class="mb-1"
                                style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: {{ $product->detail_client }}</pre>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="row">
                        <div class="col-4 col-lg-2 fw-medium">
                            <p class="mb-1">Note</p>
                        </div>
                        <div class="col-8 col-lg-6">
                            <pre
                                style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-x: auto; white-space: pre-wrap;">: {{ $product->note }}</pre>
                        </div>
                        <div class="col-4 col-lg-2 fw-medium">
                            <p class="mb-1">User</p>
                        </div>
                        <div class="col-8 col-lg-2">
                            <p class="mb-1">: {{ $product->user->name }}</p>
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
                                <th>Price</th>
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
                                            style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $products->detailProduct?->product?->description ?? '-' }}</pre>
                                    </td>
                                    <td class="align-top">{{ $products->qty }}
                                        {{ $products->detailProduct->product->unit ?? '-' }}
                                    </td>
                                    <td class="align-top">RP {{ number_format($products->price, 0, '', '.') }}</td>
                                    <td class="align-top">RP {{ number_format($products->amount, 0, '', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr style="font-size: 13px;">
                                <td colspan="3" style="border:none;"></td>
                                <td>Shipping</td>
                                <td>: RP {{ number_format($product->shipping, 0, '', '.') }}</td>
                            </tr>
                            <tr style="font-size: 13px">
                                <td colspan="3" style="border:none;"></td>
                                <td style="border:none;">Total</td>
                                <td style="border:none;">: RP {{ number_format($product->total, 0, '', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="form-floating form-floating-outline mb-2">
                        <select class="form-select change-no" name="changeNo" id="changeNo"
                            aria-label="Default select example" data-id="{{ $product->id }}">
                            <option value="1" {{ $product->no_type == '1' ? 'Selected' : '' }}>
                                No Invoice
                            </option>
                            <option value="2" {{ $product->no_type == '2' ? 'Selected' : '' }}>
                                No PO
                            </option>
                        </select>
                        <label for="changeNo">No Product Out</label>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect"
                        href="javascript:void(0);">
                        Print
                    </a>
                    <a href="javascript:void(0);" type="button"
                        class="btn btn-outline-secondary d-grid w-100 waves-effect mb-3">
                        Download
                    </a>
                    <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete-invoice"
                        data-id="{{ $product->id }}">
                        Delete
                    </a>
                </div>
            </div>
        </div>
        {{-- End : Button Invoice --}}
    </div>
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
                        'url': '{{ url('product-out') }}/' + id,
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
                                    window.location.href = '/product-out';
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

        $(document).on('change', '.change-no', function() {
            var selectedValue = $(this).val();
            var rowId = $(this).data('id');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                type: 'POST',
                url: '/product-out/' + rowId + '/change_no',
                data: {
                    status: selectedValue,
                    _token: csrfToken
                },
                success: function(response) {
                    console.log('Perubahan status berhasil dikirim ke server');
                    window.setTimeout(function() {
                        window.location.href = '/product-out/' + rowId;
                    }, 10);
                },
                error: function(error) {
                    console.error('Gagal mengirim permintaan ke server:', error);
                }
            });
        });
    </script>
@endpush
