@extends('layouts.sales.app')
@section('title', 'Year Reports')
@section('content')
    <h4 class="fw-bold py-3 mb-1">
        Sales Report &mdash; Product Movement
    </h4>
    <p class="text-muted mb-4">
        Rekap kuantitas produk keluar per periode (Tahun/Semester), dipisah berdasarkan channel penjualan Online & Offline.
        Target tim di sini juga dipakai untuk hitung pencapaian target di halaman Overview.
    </p>

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0"><i class="mdi mdi-cart-outline me-1"></i>Product Out &mdash; Online</h5>
        </div>
        <div class="card-datatable table-responsive pt-0">
                <table class="datatable-sales-reports-online table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Year</th>
                            <th>Semester</th>
                            <th>Total Quantity</th>
                            <th>Target Tim</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0"><i class="mdi mdi-cart-outline me-1"></i>Product Out &mdash; Offline</h5>
        </div>
        <div class="card-datatable table-responsive pt-0">
                <table class="datatable-sales-reports-offline table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Year</th>
                            <th>Semester</th>
                            <th>Total Quantity</th>
                            <th>Target Tim</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
        </div>
    </div>
    @include('pages.warehouse.reports.form')
    @include('pages.warehouse.reports.edit-form')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
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
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-sales-reports-online.js"></script>
    <script src="{{ asset('assets') }}/includes/table-sales-reports-offline.js"></script>
@endpush

@push('script')
    <script>
        $(function () {
            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $(document).on('keyup click change', '.total-label', function () {
                var input = $(this);
                input.val(formatNumber(input.val()));
                $('#target').val(parseFloat(input.val().replace(/[.,]/g, '')) || '');
            });

            $(document).on('keyup click change', '.edit-total-label', function () {
                var input = $(this);
                input.val(formatNumber(input.val()));
                $('#editTarget').val(parseFloat(input.val().replace(/[.,]/g, '')) || '');
            });

            // Isi modal Edit Report dari tombol "Edit" di dropdown action tiap baris.
            $(document).on('click', '.edit-report', function () {
                var id = $(this).data('id');
                var year = $(this).data('year');
                var semester = String($(this).data('semester'));
                var target = $(this).data('target') || '';

                $('#formEditReport').attr('action', '/sale-report/' + id);
                $('#editYear').val(year);
                $('#editSemester').val(semester);
                $('#editTarget').val(target);
                $('#editTargetLabel').val(target ? formatNumber(String(target)) : '');
            });
        });
    </script>
@endpush
