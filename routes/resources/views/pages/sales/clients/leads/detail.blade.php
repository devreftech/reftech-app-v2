@extends('layouts.sales.app')
@section('title', 'Detail Leads')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Clients / Leads /</span> Details {{ $leads->company }}
    </h4>
    <div class="row mb-4">
        <div class="col-md-6">
            <h5 class="fw-bold pb-1 mb-3">
                Details
            </h5>
            <div class="card">
                <div class="card-header pb-0">
                    <div class="text-end text-muted">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#updateLeads{{ $leads->id }}">
                            <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                        </a>
                        <a href="#" data-id="{{ $leads->id }}"
                            class="btn btn-sm btn-label-danger delete-leads">Delete</a>
                        <a href="#" data-id="{{ $leads->id }}"
                            class="btn btn-sm btn-label-info convert-customers">Convert Cust</a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text">
                    <div class="row mb-1">
                        <div class="col-3">
                            NPWP Address
                        </div>
                        <div class="col-9">
                            : {{ $leads->address }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Sub Address
                        </div>
                        <div class="col-9">
                            : {{ $leads->subAddress }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Area
                        </div>
                        <div class="col-9">
                            : {{ $leads->area }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Phone
                        </div>
                        <div class="col-9">
                            : {{ $leads->phone }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Email
                        </div>
                        <div class="col-9">
                            : {{ $leads->email }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Unit
                        </div>
                        <div class="col-9">
                            : {{ $leads->unit }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Mobile
                        </div>
                        <div class="col-9">
                            : {{ $leads->mobile }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            R/U
                        </div>
                        <div class="col-9">
                            : {{ $leads->ru }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Source
                        </div>
                        <div class="col-9">
                            : {{ $leads->source }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Npwp
                        </div>
                        <div class="col-9">
                            : {{ $leads->npwp }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Assigned
                        </div>
                        <div class="col-9">
                            : {{ $leads->sales->name }}
                        </div>
                    </div>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold pb-1 mb-3">
                    PIC
                </h5>
                <a type="button" data-bs-toggle="modal" data-bs-target="#createPic">
                    <button type="button" class="btn btn-primary">
                        + New PIC
                    </button>
                </a>
            </div>

            {{-- @foreach ($charge as $pic) --}}
            <div class="card mb-2">
                <div class="card-datatable table-responsive pt-0">
                    <table
                        class="datatable-pic-client{{ Auth::user()->role == 'Sales' ? '-sales' : '' }} table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                {{-- <div class="card-header pb-0">
                        <div class="text-end text-muted">
                            <a type="button" data-bs-toggle="modal" data-bs-target="#updatePic-{{ $pic->id }}">
                                <button type="button" class="btn btn-sm btn-label-primary">
                                    <i class="menu-icon tf-icons mdi mdi-14px mdi-account-edit-outline"></i>Edit
                                </button>
                            </a>
                            <a href="#" data-id="{{ $pic->id }}" class="btn btn-sm btn-label-danger delete-pic">
                                <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline"></i>Delete
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                        <div class="row mb-1">
                            <div class="col-3">
                                Name
                            </div>
                            <div class="col-9">
                                : {{ $pic->name_pic }}
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-3">
                                Position
                            </div>
                            <div class="col-9">
                                : {{ $pic->position }}
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-3">
                                Phone
                            </div>
                            <div class="col-9">
                                : {{ $pic->phone_pic }}
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-3">
                                Email
                            </div>
                            <div class="col-9">
                                : {{ $pic->email_pic }}
                            </div>
                        </div>
                        </p>
                    </div> --}}
            </div>
            {{-- @endforeach --}}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 my-3">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold pb-1 mb-2">
                    Daily Call History
                </h5>
                @php
                    $emailPic = 0;
                @endphp

                @foreach ($charge as $pic)
                    @php
                        if ($pic->email_pic != null && $pic->email_pic != '-') {
                            $emailPic++;
                        }
                    @endphp
                @endforeach

                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createAction{{ $leads->id }}" @if ($emailPic <= 0 || $leads->unit == null || $leads->unit == '-') disabled @endif>
                    + New Action
                </button>
            </div>
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-crm-history table table-striped" id="dataTableCrm">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>ID</th>
                                <th>Date</th>
                                <th>ACtion</th>
                                <th>Status</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold pb-1 mb-3">
                    Machine
                </h5>
                <a type="button" data-bs-toggle="modal" data-bs-target="#createMachine">
                    <button type="button" class="btn btn-primary">
                        + Create New machine
                    </button>
                </a>
            </div>
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-machine-client table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Type</th>
                                <th>SN</th>
                                <th>Tag</th>
                                <th>Location</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            {{-- <div class="row">
                @foreach ($machines as $machine)
                    <div class="col-6 col-md-4">
                        <div class="card mb-2">
                            <div class="card-header pb-0">
                                <div class="text-end text-muted">
                                    <a type="button" data-bs-toggle="modal"
                                        data-bs-target="#createMachine{{ $machine->id }}">
                                        <button type="button" class="btn btn-sm btn-label-warning">
                                            Edit
                                        </button>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-label-danger delete-machine"
                                        data-id="{{ $machine->id }}">
                                        <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline"></i>Delete
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Brand
                                    </div>
                                    <div class="col-8">
                                        : {{ $machine->brand }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Type
                                    </div>
                                    <div class="col-8">
                                        : {{ $machine->type }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Serial Number
                                    </div>
                                    <div class="col-8">
                                        : {{ $machine->serial_number }} ({{$machine->reports->count() > 0 ? $machine->reports->count() : '0'}})
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Bar
                                    </div>
                                    <div class="col-8">
                                        : {{ $machine->bar }}
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-4">
                                        Running Hour
                                    </div>
                                    <div class="col-8">
                                        : {{ $machine->running }}
                                    </div>
                                </div>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div> --}}
        </div>
        <div class="col-md-6 my-3">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold m-0 pt-2">
                    Quotation
                </h5>
                <a href="{{ route('quotation.create') }}" type="button" class="btn btn-primary">
                    + New Quotation
                </a>
            </div>

            <div class="card mb-3">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-quotation-leads table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Quote No.</th>
                                <th>Status</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        @if (Auth::user()->detail[0]->area == 'Bekasi' ||
                Auth::user()->detail[0]->area == 'Jabodetabek' ||
                (Auth::user()->detail[0]->area == 'Jawa Barat' && Auth::user()->role == 'Sales'))
            <div class="col-md-6 my-3">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold pb-1 mb-2">
                        Visit History
                    </h5>
                    <a type="button" data-bs-toggle="modal" data-bs-target="#createActionVisit">
                        <button type="button" class="btn btn-primary">
                            + New Action
                        </button>
                    </a>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Status</th>
                                    <th>note</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse ($visit as $visits)
                                    <tr>
                                        <td>
                                            {{ \Carbon\Carbon::parse($visits->date)->format('d-m-Y') }}
                                        </td>
                                        <td>
                                            {{ $visits->action }}
                                        </td>
                                        <td>
                                            {{ $visits->status }}
                                        </td>
                                        <td>
                                            {{ $visits->note }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            Kamu belum punya Visit.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @include('pages.sales.clients.leads.form')
    @include('components.modal.pic.leads.form-create')
    @include('pages.sales.activities.form')
    @include('pages.sales.activities.form-visit')
    @include('components.modal.machine.form')
    @foreach ($machines as $machine)
        @include('components.modal.machine.form-edit')
    @endforeach
    @foreach ($charge as $pic)
        @include('components.modal.pic.leads.form-update')
    @endforeach
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
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-crm-history.js"></script>
    <script src="{{ asset('assets') }}/includes/table-machine-client.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-leads.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pic-client.js"></script>
    <script src="{{ asset('assets') }}/includes/table-pic-client-sales.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        $(document).on('click', '.delete-pic', function() {
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
                        'url': '{{ url('pic') }}/' + id,
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
                                    location.reload();
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
        $(document).on('click', '.delete-leads', function() {
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
                        'url': '{{ url('leads') }}/' + id,
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
                                    window.location.href = '/leads';
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
        $(document).on('click', '.convert-customers', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
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
                        'url': '{{ url('leads') }}/convert/' + id,
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
                                    window.location.href = '/existing/' + id;
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
    </script>
@endpush
