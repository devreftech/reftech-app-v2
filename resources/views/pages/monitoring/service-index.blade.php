@extends('layouts.sales.app')
@section('title', 'Monitoring machine')
@section('content')
    @if (Auth::user()->role != 'Technician' || Auth::user()->id == 13)
        <div class="row mb-4">
            <div class="col-12 col-md-3 mb-2">
                <div class="card bg-label-info h-100">
                    <div class="card-body">
                        <h5>Machine On All Plant</h5>
                    </div>
                    <div class="card-footer">
                        <p class="text-black float-end fs-5">{{ $allPlantMonitoring }}/ <span
                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="row">
                    <div class="col-6 col-md-4 mb-2">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant GT 3 / BOILER</h5>
                                <p class="float-end fs-5">{{ $GT3Monitoring }}/ <span
                                        class="text-muted fs-6">{{ $GT3 }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mb-2">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant GT 1-2</h5>
                                <p class="float-end fs-5">{{ $GTMonitoring }}/ <span
                                        class="text-muted fs-6">{{ $GT }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mb-2">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant INC</h5>
                                <p class="float-end fs-5">{{ $INCMonitoring }}/ <span
                                        class="text-muted fs-6">{{ $INC }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant PM 1-2</h5>
                                <p class="float-end fs-5">{{ $PM12Monitoring }}/ <span
                                        class="text-muted fs-6">{{ $PM12 }}</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant PM 3-5</h5>
                                <p class="float-end fs-5">{{ $PM35Monitoring }}/ <span
                                        class="text-muted fs-6">{{ $PM35 }}</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant PM 7-8</h5>
                                <p class="float-end fs-5">{{ $PM78Monitoring }}/ <span
                                        class="text-muted fs-6">{{ $PM78 }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Machine </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-compressor-client table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Brand</th>
                                        <th>Unit</th>
                                        <th>Tag</th>
                                        <th>Location</th>
                                        <th>PIC</th>
                                        <th>Info</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-12 col-md-6">
        <div class="card mb-3">
            <div class="card-body">
                <h5> Dryer </h5>
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-dryer-client table table-striped">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th>ID</th>
                                <th>Brand</th>
                                <th>Type</th>
                                <th>Tag</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}
        </div>
        <div class="row">
            {{-- <div class="col-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Issue & Maintenance Log </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-issue-month table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>month</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Recap Monitoring </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-recap-month table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>month</th>
                                        <th>action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div> --}}
            {{-- <div class="col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Service Reports </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-reports-fajar table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>No Service</th>
                                        <th>Brand Type</th>
                                        <th>Tag</th>
                                        <th>Location</th>
                                        <th>Job Desc</th>
                                        <th>Date</th>
                                        <th>Technician</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    @else
        <h3>Rekap Issue & Maintenance Log {{ \Carbon\Carbon::createFromFormat('m', $month)->format('F') }} ,
            {{ $year }}
        </h3>
        @foreach ($result as $item)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h5>{{ $item['machine'] }}</h5>
                        <a href="{{ route('visitor.daily-monitoring', $item['id']) }}">
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
    @endif
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
    <script src="{{ asset('assets') }}/includes/table-reports-fp.js"></script>
@endpush

@push('script')
    <script>
        var recapRoute = "{{ route('service-manager.recap', [':month', ':year']) }}";
    </script>
@endpush
