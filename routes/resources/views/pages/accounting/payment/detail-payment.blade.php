@extends('layouts.sales.app')
@section('title', 'Payment Recieve AR')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-6 mb-3">
                    <h4 class="mb-3">Customer Info</h4>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="card bg-label-secondary">
                                <div class="card-body">
                                    No Invoice
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    : <a href="{{ route('invoice.show', $invoice->id) }}" class="text-black"
                                        target="_blank">
                                        {{ $invoice->no_invoice }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-label-secondary">
                                <div class="card-body">
                                    Company
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    : {{ $quote->pic->client->company }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-label-secondary">
                                <div class="card-body">
                                    NPWP
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    : {{ $quote->pic->client->npwp }}
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-label-secondary h-100">
                                <div class="card-body">
                                    Address
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    : {{ $quote->pic->client->address }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    @if ($quote->pic->client->info == 'Reftech')
                        <div class="mb-xl-0 pb-1">
                            <div class="svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Logo" width="60%"
                                            class="d-block ms-auto">
                                    </span>
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="mb-xl-0 pb-1">
                            <div class="svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img src="{{ asset('/asset') }}/logo/Kojisha-Log.png" alt="Logo" width="60%"
                                            class="d-block ms-auto">
                                    </span>
                                </span>
                            </div>
                        </div>
                    @endif
                    <div class="info text-end pt-5 px-3">
                        <h6>Payment Receipt No.</h6>
                        <h3>#RCPT-{{ $payment->id }}</h3>
                        <p>{{ Carbon\Carbon::parse($payment->date)->format('d-m-Y') }}</p>
                        @php
                            if ($payment->level == 0) {
                                if ($payment->file == null) {
                                    $warna = 'text-danger';
                                    $text = 'Waiting Payment';
                                } else {
                                    $warna = 'text-warning';
                                    $text = 'Awaiting Verification';
                                }
                            } else {
                                $warna = 'text-success';
                                $text = 'Verified';
                            }

                        @endphp
                        <h4 class=" {{ $warna }}">
                            {{ $text }}</h4>
                    </div>
                </div>
            </div>
            <div class="text-end">
                {{-- <div class="functional d-flex justify-content-between"> --}}
                <a class="mx-2" type="button" data-bs-toggle="modal" data-bs-target="#editDate">
                    <button type="button" class="btn btn-warning">
                        Edit Date
                    </button>
                </a>
                <a class="mx-2" type="button" data-bs-toggle="modal" data-bs-target="#addPPH">
                    <button type="button" class="btn btn-primary">
                        {{ $payment->pph > 0 ? 'Edit' : 'Add' }} PPH
                    </button>
                </a>
                <a class="mx-2" type="button" data-bs-toggle="modal" data-bs-target="#addCost">
                    <button type="button" class="btn btn-info">
                        {{ $payment->cost > 0 ? 'Edit' : 'Add' }} Cost
                    </button>
                </a>
                {{-- </div> --}}
            </div>
            <div class="table-responsive mb-3">
                <table class="table m-0">
                    <thead class="">
                        <tr>
                            <th>Date</th>
                            <th>Payment Method</th>
                            @if ($payment->pph > 0)
                                <th>PPH</th>
                            @endif
                            @if ($payment->cost > 0)
                                <th>Cost</th>
                            @endif
                            <th>Amount</th>
                            @if ($payment->pph > 0 || $payment->cost > 0)
                                <th>Total</th>
                            @endif
                            <th>TAG</th>
                            <th>Proof of Transfer</th>
                            <th>Confirm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="font-size: 14px">
                            <td class="align-top">
                                {{ \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y') }}
                            </td>
                            <td class="align-top"> {{ $payment->method }} </td>
                            @if ($payment->pph > 0)
                                <td class="align-top">RP {{ number_format($payment->pph, 0, '', '.') }}
                            @endif
                            @if ($payment->cost > 0)
                                <td class="align-top">RP {{ number_format($payment->cost, 0, '', '.') }}
                            @endif
                            <td class="align-top">RP {{ number_format($payment->amount, 0, '', '.') }}
                            </td>
                            @if ($payment->pph > 0 || $payment->cost > 0)
                                <td class="align-top">RP {{ number_format($payment->amount - $payment->pph - $payment->cost, 0, '', '.') }}
                            @endif
                            <td class="align-top">
                                {{ $payment->type }} {{ $payment->percent }}%
                            </td>
                            <td class="align-top">
                                <a href="{{ route('view_payment.payment', $payment->id) }}"
                                    class="btn btn-primary d-grid waves-effect" target="_blank">
                                    View
                                </a>
                            </td>
                            <td class="align-top">
                                @if ($payment->level == 0)
                                    <a type="button" data-bs-toggle="modal" data-bs-target="#confirmPayment">
                                        <button type="button" class="btn btn-secondary d-grid waves-effect">
                                            Confirm
                                        </button>
                                    </a>
                                @else
                                    <a href="#" class="btn btn-label-danger d-grid waves-effect unconfirm-payment"
                                        data-id="{{ $payment->id }}">UnConfirm</a>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5>Note</h5>
                        </div>
                        <div class="card-body">
                            {{ $payment->note }}
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5>Activity</h5>
                        </div>
                        <div class="card-body pt-4" id="viewComment">
                            <ul class="timeline card-timeline mb-0">
                                @foreach ($activity as $stats)
                                    @php
                                        if ($stats->status == '1') {
                                            $status = 'Payment Viewed';
                                            $color = 'primary';
                                        } elseif ($stats->status == '2') {
                                            $status = 'Payment Verified';
                                            $color = 'success';
                                        } elseif ($stats->status == '3') {
                                            $status = 'Payment UnVerified';
                                            $color = 'danger';
                                        } else {
                                            $status = 'Payment Created';
                                            $color = 'info';
                                        }
                                    @endphp
                                    <li class="timeline-item timeline-item-transparent clearfix">
                                        <span class="timeline-point timeline-point-{{ $color }}"></span>
                                        <div class="timeline-event">
                                            <div class="timeline-header mb-1">
                                                <h6 class="mb-0">{{ $status }}</h6>
                                                <small
                                                    class="text-muted">{{ $stats->date->diffInHours(Carbon\Carbon::now()) > 24 ? $stats->date->format('d M y h:i:s') : $stats->date->diffForHumans() }}
                                                </small>
                                            </div>
                                            <p class="mb-3">
                                                {{ $stats->note }} {{ $stats->user->name }}
                                            </p>
                                            {{-- @foreach ($stats->comment as $item)
                                                <div class="d-flex justify-content-between align-items-center px-2 mb-2{{ $item->id_user == Auth::user()->id ? ' rounded bg-label-primary float-end' : '' }}"
                                                    style="width : 80%;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <img src="{{ url('') . '/' . $item->user->image }}" alt="ini photo"
                                                            style="width: 50px;" class="mx-2 rounded-pill">
                                                        <p class="mb-0">
                                                            <span class="fw-medium">{{ $item->user->name }}</span>:
                                                            {{ $item->comment }}
                                                        </p>
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ $item->date->diffInHours(Carbon\Carbon::now()) > 24 ? $item->date->format('d M y h:i:s') : $item->date->diffForHumans() }}</small>
                                                </div>
                                            @endforeach --}}
                                        </div>
                                    </li>
                                    {{-- @if ($stats->id == $lastStat->id)
                                        <form action="{{ route('pending-po.addComment', $pending->id) }}" method="post"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-floating mt-3">
                                                <input type="text" class="form-control" id="floatingInputFilled"
                                                    placeholder="Comment" name="comment"
                                                    aria-describedby="floatingInputFilledHelp">
                                                <label for="floatingInputFilled">Comment</label>
                                                <span class="form-floating-focused"></span>
                                            </div>
                                            <button type="submit"
                                                class="btn btn-primary waves-effect waves-light float-end">Comment</button>
                                        </form>
                                    @endif --}}
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection()
@include('components.modal.payment.date ')
@include('components.modal.payment.pph')
@include('components.modal.payment.cost')
@include('components.modal.payment.confirm')

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
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        $(".invoice-item-pph-label").on('keyup', function() {
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
            console.log(nomorInt);
            $(`#pph`).val(nomorInt);
        });

        $(".invoice-item-cost-label").on('keyup', function() {
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
            console.log(nomorInt);
            $(`#cost`).val(nomorInt);
        });

        $(document).on('click', '.confirm-payment', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Confirm this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Confirm it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('confirm-payment') }}/payment/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Confirmed!",
                                    text: "Your file has been Confirmed.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/payment-detail/payment/' +
                                        id;
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
        $(document).on('click', '.unconfirm-payment', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to UnConfirm this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, UnConfirm it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('unconfirm-payment') }}/payment/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "UnConfirmed!",
                                    text: "Your file has been UnConfirmed.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/payment-detail/payment/' +
                                        id;
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
    </script>
@endpush
