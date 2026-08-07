@extends('layouts.sales.app')
@section('title', 'Monitoring machine')
@section('content')
    <div class="row mb-3">
        <div class="col-12 col-md-6 mb-4">
            <h5 class=" mb-2">Data Unit</h5>
        </div>
        <div class="col-6 col-md-3">
            <div class="issue">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold m-0 pt-2">
                        Issue
                    </h5>
                    <a type="button" data-bs-toggle="modal" data-bs-target="#updateIssue">
                        <button type="button" class="btn btn-primary waves-effect waves-light">
                            {{ $monitoring->issue || $monitoring->issue == '-' ? 'Edit' : '+' }}
                        </button>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold m-0 pt-2">
                    Recomendation
                </h5>
                <a type="button" data-bs-toggle="modal" data-bs-target="#updateRecommendation">
                    <button type="button" class="btn btn-primary waves-effect waves-light">
                        {{ $monitoring->recommendation && $monitoring->recommendation != '-' ? 'Edit' : '+' }}
                    </button>
                </a>
            </div>
        </div>
        <div class="col-12 col-md-6 mb-4">
            <div class="unit">
                <div class="card h-px-200">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4">Date Issue</div>
                            <div class="col-8">: {{ $monitoring->date }}</div>
                            <div class="col-4">Location</div>
                            <div class="col-8">: {{ $monitoring->machine->location }}</div>
                            <div class="col-4">Tag Number</div>
                            <div class="col-8">: {{ $monitoring->machine->tag }}</div>
                            <div class="col-4">Type / Model </div>
                            <div class="col-8">: {{ $monitoring->machine->unit->brand }}
                                {{ $monitoring->machine->unit->unit->sku }}</div>
                            <div class="col-4">PIC User</div>
                            <div class="col-8">: {{ $monitoring->machine->desc }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="issue">
                <div class="card h-px-200">
                    <div class="card-body">
                        <pre
                            style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;">{{ $monitoring->issue ?? 'Belum ada Issue' }}</pre>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-px-200">
                <div class="card-body">
                    <pre
                        style="font-size: 15px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 250px; overflow-x: auto; white-space: pre-wrap;">{{ $monitoring->recommendation && $monitoring->recommendation != '-' ? $monitoring->recommendation : 'Belum ada Recommendation' }}</pre>
                </div>
            </div>
        </div>
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold m-0 pt-2">
                    quotation
                </h5>
                <a href="{{ route('monitoring.create-quotation', $monitoring->id) }}" type="button"
                    class="btn btn-primary waves-effect waves-light">
                    +
                </a>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>No. Quote</th>
                                    <th>No. PR</th>
                                    <th>Title</th>
                                    <th>Nominal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($quotes as $quote)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('quotation.show', $quote->id) }}" class="text-black">
                                                {{ $quote->no_quote }}
                                            </a>
                                        </td>
                                        <td>{{ $quote->no_pr }}</td>
                                        <td>{{ $quote->title }}</td>
                                        <td>Rp {{ number_format($quote->harga_total, 0, ',', '.') }}</td>
                                        @php
                                            switch ($quote->status) {
                                                case 20:
                                                    $labelColor = 'secondary';
                                                    $title = 'Send WA / Email';
                                                    break;
                                                case 30:
                                                    $labelColor = 'dark';
                                                    $title = 'Inquiry Accepted';
                                                    break;
                                                case 40:
                                                    $labelColor = 'info';
                                                    $title = 'Progress Follow Up';
                                                    break;
                                                case 60:
                                                    $labelColor = 'primary';
                                                    $title = 'Negotiation / Revisi';
                                                    break;
                                                case 80:
                                                    $labelColor = 'warning';
                                                    $title = 'Hot Prospect';
                                                    break;
                                                case 100:
                                                    $labelColor = 'success';
                                                    $title = 'Done PO';
                                                    break;
                                                case 0:
                                                    $labelColor = 'danger';
                                                    $title = 'Loss';
                                                    break;
                                                default:
                                                    return 0;
                                                    break;
                                            }
                                        @endphp
                                        <td>
                                            <span
                                                class="badge bg-label-{{ $labelColor }}">{{ $quote->status }}%</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum Ada Quote</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="pn mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold m-0 pt-2">
                        Part Number
                    </h5>
                    <div class="tombol">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#updatePn">
                            <button type="button" class="btn btn-primary waves-effect waves-light">
                                +
                            </button>
                        </a>
                    </div>
                </div>
                <div class="card ">
                    <div class="card-body">
                        <div class="table-responsive text-nowrap mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40%;">PN</th>
                                        <th>Description</th>
                                        <th>Stock</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pn as $machine)
                                        <tr>
                                            <td>{{ $machine->pn }}</td>
                                            <td>{{ $machine->desc }}</td>
                                            <td>{{ $machine->stock }}</td>
                                            <td>
                                                <a href="#" data-id="{{ $machine->id }}"
                                                    data-monitoring="{{ $monitoring->id }}"
                                                    class="btn btn-sm btn-label-danger delete-pn">Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mainlog mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold m-0 pt-2">
                        Maintenance Log
                    </h5>
                </div>
                <div class="card ">
                    <div class="card-body">
                        @if ($monitoring->mainlog)
                            <a type="button" class="w-100" data-bs-toggle="modal" data-bs-target="#plusMainlog">
                                <button type="button" class="btn btn-primary waves-effect waves-light w-100">
                                    + Maintenance Log
                                </button>
                            </a>
                        @else
                            {{ $monitoring->mainlog->desc }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold m-0 pt-2">
                    Activity Timeline
                </h5>
                <div class="tombol">
                    <a href="#" data-id="{{ $monitoring->id }}" class="btn btn-warning arsip-mon">Arsip</a>
                    <a type="button" data-bs-toggle="modal" data-bs-target="#updateStatus">
                        <button type="button" class="btn btn-primary waves-effect waves-light">
                            Update Status
                        </button>
                    </a>
                </div>
            </div>
            <div class="card h-auto">
                <div class="card-body">
                    @foreach ($status as $item)
                        @php
                            switch ($item->status) {
                                case '0':
                                    $stat = 'Monitoring Created';
                                    $label = 'bg-label-dark';
                                    break;
                                case '1':
                                    $stat = 'Process FU to User';
                                    $label = 'bg-label-info';
                                    break;
                                case '2':
                                    $stat = 'Send Inquiry';
                                    $label = 'bg-label-warning';
                                    break;
                                case '3':
                                    $stat = 'Hold By User';
                                    $label = 'bg-label-danger';
                                    break;
                                case '4':
                                    $stat = 'Done';
                                    $label = 'bg-label-success';
                                    break;
                                case '5':
                                    $stat = 'Archived';
                                    $label = 'bg-label-dark';
                                    break;

                                default:
                                    # code...
                                    break;
                            }
                        @endphp
                        <div class="d-flex justify-content-between">
                            <h5 class="badge rounded-pill {{ $label }} fs-5 fw-5">
                                {{ $stat }}
                            </h5>
                            <h6>
                                {{ $item->pic->name }}
                            </h6>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p>{{ $item->desc }}</p>
                            <p>{{ $item->date }}</p>
                        </div>
                        <hr>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @include('components.modal.monitoring.client.mainlog')
    @include('components.modal.monitoring.client.issueDet')
    @include('components.modal.monitoring.client.recommendation')
    @include('components.modal.monitoring.client.status')
    @include('components.modal.monitoring.client.pn')
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
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-client-daily.js"></script>
    <script src="{{ asset('assets') }}/includes/table-issue-client-monitoring.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        $(document).on('click', '.delete-pn', function() {
            var id = $(this).data('id');
            var monitoring = $(this).data('monitoring');
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
                        'url': '{{ url('monitoring-client') }}/fajarPaper-deletePN/' + id,
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
                                        '/monitoring-client/fajarPaper/' + monitoring;
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
        $(document).on('click', '.arsip-mon', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, archive it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('monitoring-client') }}/fajarPaper-arsipStatus/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'PATCH',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Archived!",
                                    text: "Your file has been archived.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href =
                                        '/monitoring-client/fajarPaper-archive';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Archive!'
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
