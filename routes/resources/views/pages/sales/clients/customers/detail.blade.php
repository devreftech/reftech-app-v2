@extends('layouts.sales.app')
@section('title', 'Detail Customers')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Clients / Customers /</span> Details {{ $customers->company }}
    </h4>
    <div class="row mb-4">
        <div class="col-md-6">
            <h5 class="fw-bold pb-1 mb-3">
                Details
            </h5>
            <div class="card">
                <div class="card-header pb-0">
                    <div class="text-end text-muted">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#updateCustomers-{{ $customers->id }}">
                            <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text">
                    <div class="row mb-1">
                        <div class="col-3">
                            Adress
                        </div>
                        <div class="col-9">
                            : {{ $customers->address }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Phone
                        </div>
                        <div class="col-9">
                            : {{ $customers->phone }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Email
                        </div>
                        <div class="col-9">
                            : {{ $customers->email }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Mobile
                        </div>
                        <div class="col-9">
                            : {{ $customers->mobile }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            R/U
                        </div>
                        <div class="col-9">
                            : {{ $customers->ru }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Source
                        </div>
                        <div class="col-9">
                            : {{ $customers->source }}
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Machine
                        </div>
                        <div class="col-9">
                            : {{ $customers->machine }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Assigned
                        </div>
                        <div class="col-9">
                            : {{ $customers->sales->name }}
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
                        + Create New PIC
                    </button>
                </a>
            </div>
            @foreach ($charge as $pic)
                <div class="card mb-2">
                    <div class="card-header pb-0">
                        <div class="text-end text-muted">
                            <a type="button" data-bs-toggle="modal" data-bs-target="#updatePic-{{ $pic->id }}">
                                <button type="button" class="btn btn-sm btn-label-primary">
                                    <i class="menu-icon tf-icons mdi mdi-14px mdi-account-edit-outline"></i>Edit
                                </button>
                            </a>
                            <button type="button" class="btn btn-sm btn-label-danger">
                                <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline"></i>Delete
                            </button>
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
                        <div class="row mb-1">
                            <div class="col-3">
                                Position
                            </div>
                            <div class="col-9">
                                : {{ $pic->position }}
                            </div>
                        </div>
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-md-12">
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
            <div class="row">
                @foreach ($machines as $machine)
                    <div class="card mb-2 col-6">
                        <div class="card-header pb-0">
                            <div class="text-end text-muted">
                                <button type="button" class="btn btn-sm btn-label-danger">
                                    <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline"></i>Delete
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                            <div class="row mb-1">
                                <div class="col-3">
                                    Brand
                                </div>
                                <div class="col-9">
                                    : {{ $machine->brand }}
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Phone
                                </div>
                                <div class="col-9">
                                    : {{ $machine->type }}
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Email
                                </div>
                                <div class="col-9">
                                    : {{ $machine->serial_number }}
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Position
                                </div>
                                <div class="col-9">
                                    : {{ $machine->bar }}
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Position
                                </div>
                                <div class="col-9">
                                    : {{ $machine->running }}
                                </div>
                            </div>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 my-3">
            <h5 class="fw-bold pb-1 mb-2">
                Daily Call History
            </h5>
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                                <th>Status</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($callhis as $callhistory)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($callhistory->date)->format('d-m-Y') }}
                                    </td>
                                    <td>
                                        {{ $callhistory->action }}
                                    </td>
                                    <td>
                                        {{ $callhistory->status }}
                                    </td>
                                    <td>
                                        {{ $callhistory->clients->area }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Kamu belum punya Call History.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 my-3">
            <h5 class="fw-bold pb-1 mb-2">
                Quotation
            </h5>
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Number Quote</th>
                                <th>Status</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($quote as $quotation)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($quotation->estimated_date)->format('d-m-Y') }}
                                    </td>
                                    <td>
                                        {{ $quotation->no_quote }}
                                    </td>
                                    <td><span
                                            class="badge bg-label-{{ $quotation->status == '25' ? 'info' : ($quotation->status == '50' ? 'warning' : ($quotation->status == '75' ? 'primary' : ($quotation->status == '100' ? 'success' : ($quotation->status == '0' ? 'danger' : '')))) }}">{{ $quotation->status }}%</span>
                                    </td>
                                    <td>
                                        RP {{ number_format($quotation->harga_total, 0, '', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Kamu belum punya Quotation.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('components.modal.client.customers.form')
    @include('components.modal.pic.customers.form-create')
    @foreach ($charge as $pic)
        @include('components.modal.pic.customers.form-update')
    @endforeach
@endsection()
@push('after-style')
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
    </script>
@endpush

