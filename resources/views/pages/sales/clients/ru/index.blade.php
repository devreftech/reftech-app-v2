@extends('layouts.sales.app')
@section('title', 'RESUSER')
@section('content')
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-{{Auth::user()->role == 'Admin' ? 'user-admin' : 'user'}} table table-striped" id="dataTableCrm">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Note</th>
                        <th>Last Contact</th>
                        <th>Next Follow Up</th>
                        @if (Auth::user()->role == 'Admin')
                            <th>Assigned</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-{{Auth::user()->role == 'Admin' ? 'reseller-admin' : 'reseller'}} table table-striped" id="dataTableCrm">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Note</th>
                        <th>Last Contact</th>
                        <th>Next Follow Up</th>
                        @if (Auth::user()->role == 'Admin')
                            <th>Assigned</th>
                        @endif
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
    <script src="{{ asset('assets') }}/includes/table-client-user.js"></script>
    <script src="{{ asset('assets') }}/includes/table-client-user-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-client-reseller.js"></script>
    <script src="{{ asset('assets') }}/includes/table-client-reseller-admin.js"></script>
@endpush
@push('script')
    <script>
        $(document).ready(function() {
            $('#dataTableCrm').on('change', '.status-dropdown', function() {
                var selectedValue = $(this).val();
                var rowId = $(this).data('id');
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    type: 'POST',
                    url: '/existing/update-status/' + rowId,
                    data: {
                        status: selectedValue,
                        _token: csrfToken
                    },
                    success: function(response) {
                        console.log('Perubahan status berhasil dikirim ke server');
                        // Handle response jika perlu
                    },
                    error: function(error) {
                        console.error('Gagal mengirim permintaan ke server:', error);
                        // Handle error jika perlu
                    }
                });
            });
        });
        // $(document).ready(function() {
        //     // Inisialisasi Select2
        //     $('#options').select2({
        //         templateResult: formatState
        //     });
        // });

        // // Fungsi untuk merender opsi dengan badge
        // function formatState(state) {
        //     if (!state.id) {
        //         return state.text;
        //     }
        //     var badgeClass = $(state.element).data('badge');
        //     return $(
        //         '<span class="' + badgeClass + '">' + state.text + '</span>'
        //     );
        // }
    </script>
@endpush
