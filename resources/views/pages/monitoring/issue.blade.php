@extends('layouts.sales.app')
@section('title', 'Monitoring machine')
@section('content')
    <div class="d-flex justify-content-between mb-2">
        <h3>Rekap Issue & Maintenance Log {{ \Carbon\Carbon::createFromFormat('m', $month)->format('F') }} ,
            {{ $year }}
        </h3>
        
        <a href="{{ route('service-manager.allrecap-monitoring', [$dateRec]) }}" target="_blank">
            <button type="button" class="btn btn-primary">
                Details Maintenance
            </button>
        </a>
    </div>
    @foreach ($result as $item)
        <div class="card mb-3">
            <div class="card-body">

                <div class="d-flex justify-content-between mb-2">
                    <h5>{{ $item['machine'] }}</h5>
                    <a href="{{ route('service-manager-daily.visit', [$item['id'], $month]) }}">
                        <button type="button" class="btn btn-primary">
                            Details Maintenance
                        </button>
                    </a>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h5 class="badge rounded-pill bg-label-primary fs-big">Issue Recommendation</h5>
                        <div class="table-responsive text-nowrap mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:10%;">Date</th>
                                        <th>Issue</th>
                                        <th style="width:25%;">Pic</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item['log'] as $log)
                                        <tr>
                                            <!-- Menampilkan tanggal log jika ada -->
                                            <td>{{ $log['date'] ?? 'N/A' }}</td>
                                            <!-- Jika tanggal ada, tampilkan, jika tidak tampilkan 'N/A' -->
                                            <td>
                                                <pre class="mb-1"
                                                    style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;">{{ $log['log'] }}</pre>
                                            </td>
                                            <td>{{ $log['pic'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <h5 class="badge rounded-pill bg-label-success fs-big">Maintenance Log</h5>
                        <div class="table-responsive text-nowrap mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:10%;">Date</th>
                                        <th>Maintenance</th>
                                        <th style="width:25%;">Pic</th>
                                        <th style="width:10%;">Button</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item['mainlog'] as $mainlog)
                                        <tr>
                                            <!-- Menampilkan tanggal mainlog jika ada -->
                                            <td>{{ $mainlog['date'] ?? 'N/A' }}</td>
                                            <!-- Jika tanggal ada, tampilkan, jika tidak tampilkan 'N/A' -->
                                            <td>
                                                <pre class="mb-1"
                                                    style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;">{{ $mainlog['log'] }}</pre>
                                            </td>
                                            <td>{{ $mainlog['technician'] }}</td>
                                            @if ($mainlog['id_service'] != null)
                                                <td>
                                                    <a class="btn btn-warning waves-effect"
                                                        href="{{ route('service-reports.show', $mainlog['id_service']) }}">
                                                        <i class="menu-icon tf-icons mdi mdi-eye-outline"></i>
                                                    </a>
                                                </td>
                                            @elseif($mainlog['id_service'] == null && $mainlog['id_pic'] == Auth::user()->id)
                                                <td>
                                                    <a class="btn btn-primary waves-effect"
                                                        href="{{ route('create.daily-monitoring-reports', [$mainlog['id'], $mainlog['id_machine']]) }}">
                                                        <i class="menu-icon tf-icons mdi mdi-file-plus-outline"></i>
                                                    </a>
                                                </td>
                                            @else
                                                <td>
                                                    Has No Reports
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
    <script src="{{ asset('assets') }}/includes/table-coordinator-compressor.js"></script>
    <script src="{{ asset('assets') }}/includes/table-coordinator-dryer.js"></script>
    <script src="{{ asset('assets') }}/includes/table-recap-month.js"></script>
    <script src="{{ asset('assets') }}/includes/table-issue-month.js"></script>
@endpush
