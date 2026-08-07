@extends('layouts.sales.app')
@section('title', 'Invoice Kojisha')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Invoice Kojisha
    </h4>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-invoice-ppn table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Invoice No.</th>
                        <th>No PO.</th>
                        <th>Company</th>
                        <th>type</th>
                        <th>Total Price</th>
                        <th>Date</th>
                        <th>Sales</th>
                        {{-- <th>Action</th> --}}
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-invoice-nonppn table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Invoice No.</th>
                        <th>No PO.</th>
                        <th>Company</th>
                        <th>type</th>
                        <th>Total Price</th>
                        <th>Date</th>
                        <th>Sales</th>
                        {{-- <th>Action</th> --}}
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
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
    <script src="{{ asset('assets') }}/includes/table-invoice-kojisha-ppn.js"></script>
    <script src="{{ asset('assets') }}/includes/table-invoice-kojisha-nonppn.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    @endpush

@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // $(document).on('click', '.reject-contract', function() {
        //     var id = $(this).data('id');
        //     var quoteId = $(this).data('quote');
        //     Swal.fire({
        //         title: "Are you sure?",
        //         text: "You won't be able to revert this!",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonText: "Yes, Reject it!",
        //         customClass: {
        //             confirmButton: "btn btn-primary me-3 waves-effect waves-light",
        //             cancelButton: "btn btn-label-secondary waves-effect",
        //         },
        //         buttonsStyling: false,
        //     }).then(function(result) {
        //         if (result.value) {
        //             $.ajax({
        //                 'url': '{{ url('contract') }}/' + id,
        //                 'type': 'DELETE',
        //                 'data': {
        //                     '_method': 'Reject',
        //                     '_token': '{{ csrf_token() }}'
        //                 },
        //                 success: function(response) {
        //                     if (response == 1) {
        //                         Swal.fire({
        //                             icon: "success",
        //                             title: "Rejectd!",
        //                             text: "Your file has been Rejectd.",
        //                             customClass: {
        //                                 confirmButton: "btn btn-success waves-effect",
        //                             },
        //                         })
        //                         window.setTimeout(function() {
        //                             window.location.href = '/contract';
        //                         }, 2000);
        //                     } else {
        //                         Swal.fire({
        //                             icon: 'error',
        //                             title: 'Oops...',
        //                             text: 'Data Failed to Reject!'
        //                         });
        //                     }
        //                 }
        //             });
        //         } else if (result.dismiss === Swal.DismissReason.cancel) {
        //             Swal.fire({
        //                 title: "Cancelled",
        //                 text: "Your imaginary file is safe :)",
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
