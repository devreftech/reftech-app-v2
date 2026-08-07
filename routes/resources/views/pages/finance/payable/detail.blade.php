@extends('layouts.sales.app')
@section('title', 'Payable')
@section('content')
    <div class="row invoice-preview">
        {{-- Invoice --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                {{-- <div class="card-body">
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
                </div> --}}
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="row">
                            <h4>Payable of {{ $payable->memo }}</h4>
                            <div class="col-4 fw-medium">
                                <p class="mb-1">no_voucher </p>
                                <p class="mb-1">no_cheque</p>
                                <p class="mb-1">bank</p>
                            </div>
                            <div class="col-8">
                                <p class="mb-1">: {{ $payable->no_voucher }}</p>
                                <p class="mb-1">: {{ $payable->no_cheque }}</p>
                                <p class="mb-1">: {{ $payable->bank->bank }} {{ $payable->bank->no_rek }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="mb-1">
                                <span class="text-muted">{{ Carbon\Carbon::parse($payable->date)->format('d-m-Y') }}</span>
                            </div>
                            <div>
                                <span class="fw-bolderr">{{ $payable->payee }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table m-0 mb-4">
                        <thead class="table-light border-top">
                            <tr>
                                <th>No.</th>
                                <th>Code</th>
                                <th>Account</th>
                                <th>Memo</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 0;
                            @endphp
                            @foreach ($detailPayable as $detail)
                                @php
                                    $no++;
                                @endphp
                                <tr style="font-size: 13px">
                                    <td class="align-top">{{ $no }}</td>
                                    <td class="align-top">
                                        <p class="mb-0 fw-semibold">
                                            {{ $detail->account->code }}
                                        </p>
                                    </td>
                                    <td class="align-top">
                                        <p>
                                            {{ $detail->account->name }}
                                        </p>
                                    </td>
                                    <td class="align-top">
                                        {{ $detail->memo }}
                                    </td>
                                    <td class="align-top">RP {{ number_format($detail->amount, 0, '', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr style="font-size: 13px">
                                <td colspan="3" style="border:none;"></td>
                                <td>Total</td>
                                <td>: RP {{ number_format($payable->amount, 0, '', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="fs-5 fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:100%;"> Say
                        amount: #
                        {{ $terbilang }} Rupiah</p>
                </div>
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('payable.print', $payable->id) }}">
                        Download
                    </a>
                    {{-- <a href="javascript{0}" type="button" class="btn btn-outline-secondary d-grid w-100 waves-effect mb-3">
                        Download
                    </a> --}}
                    <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete-payable mb-3"
                        data-id="{{ $payable->id }}">Delete</a>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
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
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        $('#backButton').click(function() {
            window.history.back();
        });

        $(document).on('click', '.delete-payable', function() {
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
                        'url': '{{ url('payable') }}/' + id,
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
                                    window.location.href = '/payable';
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
    </script>
@endpush
