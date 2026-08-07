@extends('layouts.sales.app')
@section('title', 'Data Product')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        Unit
    </h4>
    {{-- <div class="card mb-4">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                            <div>
                                <p class="mb-2">Comodity</p>
                                <h4 class="mb-2">{{ $commodity }}</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-success"></span></p>
                            </div>
                            <div class="avatar me-sm-4">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="mdi mdi-home-outline mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none me-4">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                            <div>
                                <p class="mb-2">Equivalent</p>
                                <h4 class="mb-2">{{ $sproduct }}</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-success"></span></p>
                            </div>
                            <div class="avatar me-lg-4">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="mdi mdi-laptop mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                        <hr class="d-none d-sm-block d-lg-none">
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                            <div>
                                <p class="mb-2">Pruchase Order</p>
                                <h4 class="mb-2">1</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-success"></span></p>
                            </div>
                            <div class="avatar me-sm-4">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="mdi mdi-wallet-giftcard mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="mb-2">Loss Order</p>
                                <h4 class="mb-2">2</h4>
                                <p class="mb-0"><span class="badge rounded-pill bg-label-danger"></span></p>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-secondary">
                                    <i class="mdi mdi-currency-usd mdi-24px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    @if (Auth::user()->role == 'Admin')
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product-unit table table-striped mb-3">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Brand</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>Sn</th>
                            <th>Power</th>
                            <th>Pressure</th>
                            <th>Capacity</th>
                            <th>status</th>
                            <th>Unit Price</th>
                            <th>Rental Price</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product-unit-second table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            <th>Brand</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>Sn</th>
                            <th>Power</th>
                            <th>Pressure</th>
                            <th>Capacity</th>
                            <th>status</th>
                            <th>Unit Price</th>
                            <th>Rental Price</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    @else
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product-sales-unit table table-striped mb-3">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            {{-- <th>Info</th> --}}
                            <th>Brand</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>SN</th>
                            <th>Power</th>
                            <th>Pressure</th>
                            <th>Capacity</th>
                            {{-- <th>status</th>
                            <th>Unit Price</th>
                            <th>Rental Price</th> --}}
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product-sales-unit-second table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>ID</th>
                            {{-- <th>Info</th> --}}
                            <th>Brand</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>SN</th>
                            <th>Power</th>
                            <th>Pressure</th>
                            <th>Capacity</th>
                            {{-- <th>status</th>
                            <th>Unit Price</th>
                            <th>Rental Price</th> --}}
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    @endif
    @include('components.modal.warehouse.unit.form')
    @foreach ($units as $unitr)
        @include('components.modal.warehouse.unit.form-edit')
        @include('components.modal.warehouse.unit.detail')
    @endforeach
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-second.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-sales.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-sales-second.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
            $(".price-label").on('keyup click change', function() {
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
                $(`#semuanya`).val(nomorInt);
                console.log('ini value semuanya :' + $('#semuanya').val());
            });
            $(".rental-label").on('keyup click change', function() {
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
                $(`#rental`).val(nomorInt);
                console.log('ini value rental :' + $('#rental').val());
            });
            $(".best-label").on('keyup click change', function() {
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
                $(`#best`).val(nomorInt);
                console.log('ini value best :' + $('#best').val());
            });
            $(".harga-label").on('keyup click change', function() {
                var input = $(this)
                var input_val = input.val();
                var idHarga = input.data('id');

                // original length
                var original_len = input_val.length;

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                $(`#harga` + idHarga).val(nomorInt);
                console.log('ini value harga :' + $('#harga' + idHarga).val());
            });
            $(".harga-rental-label").on('keyup click change', function() {
                var input = $(this)
                var input_val = input.val();
                var idHarga = input.data('id');

                // original length
                var original_len = input_val.length;

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                $(`#hargaRental` + idHarga).val(nomorInt);
                console.log('ini value harga :' + $('#hargaRental' + idHarga).val());
            });
            $(".harga-best-label").on('keyup click change', function() {
                var input = $(this)
                var input_val = input.val();
                var idHarga = input.data('id');

                // original length
                var original_len = input_val.length;

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                $(`#totalBest` + idHarga).val(nomorInt);
                console.log('ini value harga :' + $('#totalBest' + idHarga).val());
            });
        });
    </script>
@endpush
