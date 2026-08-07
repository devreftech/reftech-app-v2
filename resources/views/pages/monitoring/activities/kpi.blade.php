@extends('layouts.sales.app')
@section('title', 'Detail Overview Sales')
@section('content')
    <div class="row">
        <div class="col-12 col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <img src="{{ url('') . '/' . $user->image }}" alt="" srcset="" class="h-100 w-100">
                </div>
            </div>
        </div>
        <div class="col-12 col-md-9">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-1">
                            <div class="form-check form-check-success">
                                <input class="form-check-input checkPlanning" type="checkbox" name="planing" value="1"
                                    id="customCheckSuccess" {{ $monitoring && $monitoring->planning ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="col alert-planning {{ $monitoring && $monitoring->planning ? 'alert-success' : '' }}">
                            <div id="planing">Update Planning Pekerjaan Tim Lapangan</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-1">
                            <div class="form-check form-check-success">
                                <input class="form-check-input checkSync" type="checkbox" name="sync" value="1"
                                    id="customCheckSuccess" {{ $monitoring && $monitoring->sync ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="col alert-sync {{ $monitoring && $monitoring->sync ? 'alert-success' : '' }}">
                            <div id="sync">Sinkronisasi Planing Dengan Aktual Pekerjaan</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-1">
                            <div class="form-check form-check-success">
                                <input class="form-check-input checkAbnormal" type="checkbox" name="abnormal" value="1"
                                    id="customCheckSuccess" {{ $monitoring && $monitoring->abnormal ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="col alert-abnormal {{ $monitoring && $monitoring->abnormal ? 'alert-success' : '' }}">
                            <div id="sync">Cek Issue / Temuan Abnormal Dilapangan</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-1">
                            <div class="form-check form-check-success">
                                <input class="form-check-input checkLog" type="checkbox" name="log" value="1"
                                    id="customCheckSuccess" {{ $monitoring && $monitoring->log ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="col alert-log {{ $monitoring && $monitoring->log ? 'alert-success' : '' }}">
                            <div id="log">Update Maintenance Log pekerjaan & Sinkronisasi Dengan Aktual Activity di
                                Lapangan</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-1">
                            <div class="form-check form-check-success">
                                <input class="form-check-input checkTimeline" type="checkbox" name="timeline" value="1"
                                    id="customCheckSuccess" {{ $monitoring && $monitoring->timeline ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="col alert-timeline {{ $monitoring && $monitoring->timeline ? 'alert-success' : '' }}">
                            <div id="timeline">Update Timeline Weekly Cleaning Dengan Actual Pekerjaan</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-1">
                            <div class="form-check form-check-success">
                                <input class="form-check-input checkPreventive" type="checkbox" name="preventive"
                                    value="1" id="customCheckSuccess"
                                    {{ $monitoring && $monitoring->preventive ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div
                            class="col alert-preventive {{ $monitoring && $monitoring->preventive ? 'alert-success' : '' }}">
                            <div id="preventive">Update Timeline Preventive Maintenance ( Pergantian Sparepart )</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-reports-admin table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Service</th>
                                <th class="text-center">Company</th>
                                <th class="text-center">Job Desc</th>
                                <th class="text-center">Brand Type</th>
                                <th class="text-center">Serial / Tag</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Sales</th>
                                <th class="text-center">Technician</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
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
    <script src="{{ asset('assets') }}/includes/table-reports-admin.js"></script>
@endpush

@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
            $('.checkPlanning').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-planning').addClass('alert-success');
                } else {
                    $('.alert-planning').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-planning', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        planing: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Planning status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkSync').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-sync').addClass('alert-success');
                } else {
                    $('.alert-sync').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-sync', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        sync: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('sync status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkAbnormal').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-abnormal').addClass('alert-success');
                } else {
                    $('.alert-abnormal').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-abnormal', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        abnormal: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('abnormal status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkLog').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-log').addClass('alert-success');
                } else {
                    $('.alert-log').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-log', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        log: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('log status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkTimeline').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-timeline').addClass('alert-success');
                } else {
                    $('.alert-timeline').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-timeline', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        timeline: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('timeline status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

            $('.checkPreventive').on('change', function() {
                let isChecked = $(this).is(':checked');

                // Tambah atau hapus class alert-success
                if (isChecked) {
                    $('.alert-preventive').addClass('alert-success');
                } else {
                    $('.alert-preventive').removeClass('alert-success');
                }

                $.ajax({
                    url: '/check-preventive', // sesuaikan dengan route kamu
                    type: 'POST',
                    data: {
                        preventive: isChecked ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('preventive status updated:', response);
                    },
                    error: function(xhr) {
                        console.error('Terjadi error:', xhr.responseText);
                    }
                });
            });

        });
    </script>
@endpush
