@extends('layouts.sales.app')
@section('title', 'Monitoring Prokemas')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Machine Monitoring Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold text-primary">
                        <i class="mdi mdi-air-conditioner me-2"></i>Daftar Mesin Monitoring - PT Prokemas Adhikari Kreasi
                    </h5>
                    <span class="badge bg-label-primary">Prokemas</span>
                </div>
                <div class="card-body pt-3">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-machine-monitoring table table-striped table-hover">
                            <thead>
                                <tr class="table-info">
                                    <th></th>
                                    <th>ID</th>
                                    <th>Status</th>
                                    <th>Brand</th>
                                    <th>Type</th>
                                    <th>Unit</th>
                                    <th>SN</th>
                                    <th>PIC</th>
                                    <th>Time</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Issue Monitoring Card -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold text-danger">
                        <i class="mdi mdi-alert-circle-outline me-2"></i>Daftar Issue Monitoring Hari Ini
                    </h5>
                    <span class="badge bg-label-danger">Daily Issues</span>
                </div>
                <div class="card-body pt-3">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-issue-monitoring table table-striped table-hover">
                            <thead>
                                <tr class="table-warning">
                                    <th></th>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Brand</th>
                                    <th>Type</th>
                                    <th>SN</th>
                                    <th>Description</th>
                                    <th>PIC</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script>
        $(function () {
            var dtMachineMonitoring = $('.datatable-machine-monitoring');
            var dtIssueMonitoring = $('.datatable-issue-monitoring');

            if (dtMachineMonitoring.length) {
                dtMachineMonitoring.DataTable({
                    ajax: {
                        url: '/db/machine/prokemas',
                        type: 'GET'
                    },
                    columns: [
                        { data: '' },
                        { data: 'id' },
                        { data: 'time' },
                        { data: 'brand' },
                        { data: 'sku' },
                        { data: 'unit' },
                        { data: 'serial' },
                        { data: 'name' },
                        { data: 'time' },
                        { data: '' }
                    ],
                    columnDefs: [
                        {
                            className: 'control',
                            orderable: false,
                            searchable: false,
                            responsivePriority: 2,
                            targets: 0,
                            render: function () {
                                return '';
                            }
                        },
                        {
                            targets: 1,
                            render: function (data) {
                                return '<span class="fw-semibold text-muted">#' + data + '</span>';
                            }
                        },
                        {
                            targets: 2,
                            render: function (data, type, full) {
                                if (full.time || full.name) {
                                    return '<span class="badge bg-label-success"><i class="mdi mdi-check-circle-outline me-1"></i>Sudah Dimonitor</span>';
                                }
                                return '<span class="badge bg-label-secondary"><i class="mdi mdi-clock-outline me-1"></i>Belum Dimonitor</span>';
                            }
                        },
                        {
                            targets: 3,
                            render: function (data) {
                                return '<span class="fw-bold text-dark">' + (data || '-') + '</span>';
                            }
                        },
                        {
                            targets: 4,
                            render: function (data) {
                                return '<span class="badge bg-label-info">' + (data || '-') + '</span>';
                            }
                        },
                        {
                            targets: 5,
                            render: function (data) {
                                return '<span class="text-truncate" style="max-width: 200px;">' + (data || '-') + '</span>';
                            }
                        },
                        {
                            targets: 6,
                            render: function (data) {
                                return '<span class="font-monospace fw-semibold">' + (data || '-') + '</span>';
                            }
                        },
                        {
                            targets: 7,
                            render: function (data) {
                                return data ? '<span class="fw-semibold text-primary">' + data + '</span>' : '<span class="text-muted">-</span>';
                            }
                        },
                        {
                            targets: 8,
                            render: function (data) {
                                return data ? '<span class="badge bg-label-secondary">' + data + '</span>' : '<span class="text-muted">-</span>';
                            }
                        },
                        {
                            targets: 9,
                            orderable: false,
                            searchable: false,
                            render: function (data, type, full) {
                                var month = full.month || (new Date().getMonth() + 1);
                                var url = '/service-manager-daily-prokemas/' + full.id + '/' + month;
                                return '<a href="' + url + '" class="btn btn-xs btn-primary waves-effect waves-light"><i class="mdi mdi-eye-outline me-1"></i>Detail</a>';
                            }
                        }
                    ],
                    order: [[1, 'asc']],
                    dom: '<"card-header flex-column flex-md-row d-flex justify-content-between align-items-center"<"head-label"><"dt-action-buttons text-end pt-3 pt-md-0"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    displayLength: 10,
                    language: {
                        emptyTable: 'Tidak ada data mesin ditemukan untuk Prokemas.',
                        search: '',
                        searchPlaceholder: 'Cari mesin...'
                    }
                });
            }

            if (dtIssueMonitoring.length) {
                dtIssueMonitoring.DataTable({
                    ajax: {
                        url: '/db/issue/prokemas',
                        type: 'GET'
                    },
                    columns: [
                        { data: '' },
                        { data: 'id' },
                        { data: 'date' },
                        { data: 'brand' },
                        { data: 'sku' },
                        { data: 'serial' },
                        { data: 'issue' },
                        { data: 'name' },
                        { data: 'issue_level' }
                    ],
                    columnDefs: [
                        {
                            className: 'control',
                            orderable: false,
                            searchable: false,
                            targets: 0,
                            render: function () {
                                return '';
                            }
                        },
                        {
                            targets: 1,
                            render: function (data) {
                                return '<span class="fw-semibold text-muted">#' + (data || '-') + '</span>';
                            }
                        },
                        {
                            targets: 2,
                            render: function (data) {
                                return data ? moment(data).format('DD-MMM-YYYY') : '-';
                            }
                        },
                        {
                            targets: 3,
                            render: function (data) {
                                return '<span class="fw-bold">' + (data || '-') + '</span>';
                            }
                        },
                        {
                            targets: 4,
                            render: function (data) {
                                return '<span class="badge bg-label-info">' + (data || '-') + '</span>';
                            }
                        },
                        {
                            targets: 5,
                            render: function (data) {
                                return '<span class="font-monospace">' + (data || '-') + '</span>';
                            }
                        },
                        {
                            targets: 6,
                            render: function (data) {
                                return data ? '<span class="text-wrap">' + data + '</span>' : '<span class="text-muted">-</span>';
                            }
                        },
                        {
                            targets: 7,
                            render: function (data) {
                                return data ? '<span class="fw-semibold">' + data + '</span>' : '<span class="text-muted">-</span>';
                            }
                        },
                        {
                            targets: 8,
                            render: function (data) {
                                return '<span class="badge bg-label-danger"><i class="mdi mdi-alert-circle-outline me-1"></i>Open Issue</span>';
                            }
                        }
                    ],
                    dom: '<"card-header flex-column flex-md-row d-flex justify-content-between align-items-center"<"head-label"><"dt-action-buttons text-end pt-3 pt-md-0"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    displayLength: 10,
                    language: {
                        emptyTable: 'Tidak ada issue hari ini.',
                        search: '',
                        searchPlaceholder: 'Cari issue...'
                    }
                });
            }
        });
    </script>
@endpush
