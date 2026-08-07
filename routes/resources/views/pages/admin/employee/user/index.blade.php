@extends('layouts.sales.app')
@section('title', 'Data User')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        User
    </h4>
    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-user table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>NIP</th>
                        <th>Name</th>
                        <th>Entry Date</th>
                        <th>Role</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    @include('components.modal.user.form')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script>
        $(document).ready(function() {
            $(".cursor-pointer").click(function() {
                $(this).children().toggleClass("mdi-eye-off-outline mdi-eye-outline");
                toggleInputType($('#password'));
            });

            function toggleInputType(inputElement) {
                var currentType = inputElement.attr("type");
                var newType = (currentType === "password") ? "text" : "password";
                inputElement.attr("type", newType);
            }
            $("#phone").on("input", function() {
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
            $(".total-label").on('keyup click change', function() {
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
                // console.log(id);
                $(`#total`).val(nomorInt);
            });
            $('#ddSales').on('change', function() {
                var role = $(this).val();
                console.log(role);
                if (role == 'Sales') {
                    $('#inputTarget').removeAttr('hidden');
                } else {
                    $('#inputTarget').attr('hidden', true);
                }
            });
        });
    </script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/pages-account-settings-account.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product.js"></script>
    <script src="{{ asset('assets') }}/includes/table-user.js"></script>
@endpush
