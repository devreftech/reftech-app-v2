@extends('layouts.sales.app')
@section('title', 'Payment Recieve AR')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-6 mb-3">
                    <h4 class="mb-3">Invoice Aging</h4>
                    <div class="row">
                        <div class="col-6 mb-3">
                            Invoice Number
                        </div>
                        <div class="col-6 mb-3">
                            : <a class="text-black"
                                href="{{ route('invoice.show', $invoice->id) }}">{{ $invoice->no_invoice }}</a>
                        </div>
                        <div class="col-6 mb-3">
                            Invoice Date
                        </div>
                        <div class="col-6 mb-3">
                            : {{ $invoice->date }}
                        </div>
                        <div class="col-6 mb-3">
                            Invoice Due Date
                        </div>
                        <div class="col-6 mb-3">
                            : {{ $payment->due_date }}
                        </div>
                        <div class="col-6 mb-3">
                            Terms
                        </div>
                        <div class="col-6 mb-3">
                            : {{ $invoice->term }}
                        </div>
                        <div class="col-6 mb-3">
                            Address
                        </div>
                        <div class="col-6 mb-3">
                            : {{ $quote->pic->client->address }}
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="info text-end">
                        <p class="badge bg-label-danger text-danger rounded">Overdue</p>
                        <p>Days Past Due : {{ $diffDue < 0 ? abs($diffDue) : 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer border">
            <div class="row mt-3">
                <div class="col-4">
                    <div class="card">
                        <div class="card-body bg-label-secondary">
                            <p>invoice Total</p>
                            <h5>Rp {{ number_format($quote->harga_total, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body bg-label-secondary">
                            <p>Paid to Date</p>
                            <h5>-</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body bg-label-secondary">
                            <p>Outstanding</p>
                            <h5 class="text-danger">Rp {{ number_format($quote->harga_total, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h4>Aging Buckets</h4>
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body text-start {{ $diffDue <= 0 ? 'bg-label-danger text-danger' : '' }}">
                            <p>Current Days</p>
                            <h5 class="{{ $diffDue <= 0 ? 'text-danger' : '' }}">Rp
                                {{ $diffDue <= 0 ? number_format($quote->harga_total, 0, ',', '.') : 0 }}</h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div
                            class="card-body text-start {{ $diffDue > 0 && $diffDue <= 30 ? 'bg-label-danger text-danger' : '' }}">
                            <p>0 - 30 Days</p>
                            <h5 class="{{ $diffDue > 0 && $diffDue <= 30 ? 'text-danger' : '' }}">Rp
                                {{ $diffDue > 0 && $diffDue <= 30 ? number_format($quote->harga_total, 0, ',', '.') : 0 }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div
                            class="card-body text-start {{ $diffDue > 30 && $diffDue <= 60 ? 'bg-label-danger text-danger' : '' }}">
                            <p>31 - 60 Days</p>
                            <h5 class="{{ $diffDue > 30 && $diffDue <= 60 ? 'text-danger' : '' }}">Rp
                                {{ $diffDue > 30 && $diffDue <= 60 ? number_format($quote->harga_total, 0, ',', '.') : 0 }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div
                            class="card-body text-start {{ $diffDue > 60 && $diffDue <= 90 ? 'bg-label-danger text-danger' : '' }}">
                            <p>61 - 60 Days</p>
                            <h5 class="{{ $diffDue > 60 && $diffDue <= 90 ? 'text-danger' : '' }}">Rp
                                {{ $diffDue > 60 && $diffDue <= 90 ? number_format($quote->harga_total, 0, ',', '.') : 0 }}
                            </h5>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-body text-start {{ $diffDue > 90 ? 'bg-label-danger text-danger' : '' }}">
                            <p>> 90 Days</p>
                            <h5 class="{{ $diffDue > 90 ? 'text-danger' : '' }}">Rp
                                {{ $diffDue > 90 ? number_format($quote->harga_total, 0, ',', '.') : 0 }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h4>Note</h4>
                </div>
                <div class="card-body">
                    {{-- {{$quote->note}} --}}
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between mb-2">
                        <h4>Reminder Timeline</h4>
                        <a type="button" data-bs-toggle="modal" data-bs-target="#addReminder">
                            <button type="button" class="btn btn-primary">
                                + Reminder
                            </button>
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    @forelse ($reminder as $item)
                        <ul class="timeline card-timeline mb-0">
                            <li class="timeline-item timeline-item-transparent clearfix">
                                <span class="timeline-point timeline-point-primary"></span>
                                <div class="timeline-event">
                                    <div class="d-flex align-items-center mb-1">
                                        <img src="{{ url('') . '/' . $item->user->image }}" alt="ini photo"
                                            style="width: 50px;" class="mx-2 rounded-pill">
                                        <p class="mb-0">
                                            <span class="fw-medium">{{ $item->user->name }}
                                        </p>
                                    </div>
                                    <div class="timeline-header mb-1">
                                        <h6 class="mb-0">Proggress Follow Up Ke-{{ $item->status }}</h6>
                                        <small
                                            class="text-muted">{{ $item->created_at->diffInHours(Carbon\Carbon::now()) > 24 ? $item->created_at->format('d M y h:i:s') : $item->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <p class="mb-3">
                                        {{ $item->reminder }}
                                    </p>
                                </div>
                            </li>
                        </ul>
                    @empty
                        <hr>
                        <h6 class="text-center my-2">Belum Ada Reminder Pada Payment Ini.</h6>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection()
@include('components.modal.payment.add-reminder')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
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
    </script>
@endpush
