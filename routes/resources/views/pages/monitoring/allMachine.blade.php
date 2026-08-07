@extends('layouts.sales.app')
@section('title', 'All Machine')

<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
            <div class="mb-xl-0 pb-1">
                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                    <span class="app-brand-logo demo">
                        <span style="color: var(--bs-primary)">
                            <img class="text-md"
                                src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                alt="" srcset="" width="60%">
                        </span>
                    </span>
                </div>
                <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                <div style="font-size: 10px">
                    <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                    <p class="mb-1">Bandung – Jawa Barat 40218</p>
                    <p class="mb-1">
                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                        54417653{{ '  |  ' }}<i
                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                    </p>
                </div>
            </div>
            <div class="text-end">
                <h3 class="fw-bold">Recap Monthly All Machine</h3>
            </div>
        </div>
        <hr class="my-2">

        @foreach ($result as $item)
            <div class="d-flex justify-content-between mb-2">
                <h5>{{ $item['machine'] }}</h5>
                <h5>{{ $item['plant'] }}</h5>
            </div>

            <div class="table-responsive text-nowrap">
                @if ($item['unit'] != 'REFRIGERANT AIR DRYER')
                    <table class="table table-bordered">
                        <thead class="table-light" align="center">
                            <th style="vertical-align: middle;">Date</th>
                            <th style="vertical-align: middle;">Condition</th>
                            <th style="vertical-align: middle;">Running<br>Hour</th>
                            <th style="vertical-align: middle;">Load<br>Hour</th>
                            <th style="vertical-align: middle;">Press.</th>
                            <th style="vertical-align: middle;">Temp.<br>(85°C - 95°C)</th>
                            <th style="vertical-align: middle;">Oil<br>Level</th>
                            <th style="vertical-align: middle;">Kebocoran</th>
                            <th style="vertical-align: middle;">PIC</th>
                        </thead>
                        <tbody>
                            @foreach ($item['daily'] as $daily)
                                <tr>
                                    <td>{{ $daily['date'] }}</td>
                                    <td>{{ $daily['condition'] }}</td>
                                    <td>{{ $daily['running'] }}</td>
                                    <td>{{ $daily['loading'] }}</td>
                                    <td>{{ $daily['pressure'] }}</td>
                                    <td>
                                        @php
                                            $stringTemp = $daily['temp'] ?? ''; // Pastikan nilai tidak null
                                            $tempNumber = null;

                                            if (preg_match('/\d+(\.\d+)?/', $stringTemp, $matches)) {
                                                $tempNumber = (float) $matches[0]; // Gunakan float agar mendukung desimal
                                            }
                                        @endphp

                                        @if (!is_null($tempNumber) && $tempNumber > 94)
                                            <p class="mb-0 fw-bold fs-6 text-danger">
                                                {{ $daily['temp'] }}</p>
                                        @else
                                            {{ $daily['temp'] }}
                                        @endif
                                    </td>
                                    <td>{{ $daily['oil_level'] }}</td>
                                    <td>{{ $daily['leak'] }}</td>
                                    <td>{{ $daily['pic'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <table class="table table-bordered">
                        <thead class="table-light" align="center">
                            <th style="vertical-align: middle;">Date</th>
                            <th style="vertical-align: middle;">Condition</th>
                            <th style="vertical-align: middle;">Temp. IN</th>
                            <th style="vertical-align: middle;">Temp. OUT</th>
                            <th style="vertical-align: middle;">Dew P.</th>
                            <th style="vertical-align: middle;">Auto<br>Drain</th>
                            <th style="vertical-align: middle;">Fan<br>Kondenser</th>
                            <th style="vertical-align: middle;">Kebocoran</th>
                            <th style="vertical-align: middle;">PIC</th>
                        </thead>
                        <tbody>
                            @foreach ($item['daily'] as $daily)
                                <tr>
                                    <td>{{ $daily['date'] }}</td>
                                    <td>{{ $daily['condition'] }}</td>
                                    <td>{{ $daily['temp'] }}</td>
                                    <td>{{ $daily['temp_out'] }}</td>
                                    <td>{{ $daily['dew'] }}</td>
                                    <td>{{ $daily['drain'] }}</td>
                                    <td>{{ $daily['fan'] }}</td>
                                    <td>{{ $daily['leak'] }}</td>
                                    <td>{{ $daily['pic'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="issue mt-5">
                <h5>Issue Recommendation</h5>
                <div class="table-responsive text-nowrap mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width:20%;">Date</th>
                                <th>Issue</th>
                                <th style="width:25%;">Pic</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($item['log'] as $issues)
                                <tr>
                                    <td>{{ $issues['date'] ?? 'N/A' }}</td>
                                    <td>
                                        <pre class="mb-1"
                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;">{{ $issues['log'] }}</pre>
                                    </td>
                                    <td>{{ $issues['pic'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"> Belum Ada Issue Recomendation</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mainlog mt-5">
                <h5>Maintenance Log</h5>
                <div class="table-responsive text-nowrap mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width:20%;">Date</th>
                                <th>Maintenance</th>
                                <th style="width:25%;">Pic</th>
                                {{-- <th style="width:10%;">Button</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($item['mainlog'] as $mainlog)
                                <tr>
                                    <td>{{ $mainlog['date'] ?? 'N/A' }}</td>
                                    <td>
                                        <pre class="mb-1"
                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;">{{ $mainlog['log'] }}</pre>
                                    </td>
                                    <td>{{ $mainlog['technician'] }}</td>
                                    {{-- @if ($mainlog['id_service'] != null)
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
                                @endif --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"> Belum Ada Maintenance Log</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="weekly mb-5">
                <h5>Weekly Monitoring</h5>

                <div class="table-responsive text-nowrap mb-4">
                    <table class="table table-bordered">
                        @if ($item['unit'] == 'REFRIGERANT AIR DRYER')
                            <thead class="table-light">
                                <th style="vertical-align: middle;">Week</th>
                                <th style="vertical-align: middle;">Condition</th>
                                <th style="vertical-align: middle;">Vibration</th>
                                <th style="vertical-align: middle;">Voltage</th>
                                <th style="vertical-align: middle;">Ampere L</th>
                                <th style="vertical-align: middle;">Cleaning Cooler</th>
                                <th style="vertical-align: middle;">Cek Coupling / Belt</th>
                                <th style="vertical-align: middle;">Cleaning Compressor & Area</th>
                                <th style="vertical-align: middle;">PIC</th>
                            </thead>
                            <tbody>
                                @php
                                    $noWeek = 1;
                                @endphp
                                @forelse ($item['weekly'] as $monweek)
                                    <tr>
                                        <td>{{ $noWeek }}</td>
                                        <td>{{ $monweek['condition'] }}</td>
                                        <td>{{ $monweek['vibration'] }}</td>
                                        <td>{{ $monweek['voltage'] }}</td>
                                        <td>{{ $monweek['ampere'] }}</td>
                                        <td>
                                            @if ($monweek['cooler'] == 1)
                                                {{-- <i
                                                class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i> --}}
                                                cleaning
                                            @else
                                                {{-- <i
                                                class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i> --}}
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if ($monweek['coupling'] == 1)
                                                {{-- <i
                                                    class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i> --}}
                                                Ok
                                            @else
                                                {{-- <i
                                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i> --}}
                                                Not Ok
                                            @endif
                                        </td>
                                        <td>
                                            @if ($monweek['area'] == 1)
                                                {{-- <i
                                                class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i> --}}
                                                cleaning
                                            @else
                                                {{-- <i
                                                class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i> --}}
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $monweek['name'] }}</td>
                                    </tr>
                                    @php
                                        $noWeek++;
                                    @endphp
                                @empty
                                    <tr>
                                        <td colspan="7">Belum Ada Monitoring week</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        @else
                            <thead class="table-light">
                                <th>Week</th>
                                <th>Condition</th>
                                <th>Voltage</th>
                                <th>Ampere</th>
                                <th>Auto Drain</th>
                                <th>Pre</th>
                                <th>After</th>
                                <th>Condensor</th>
                                <th>PIC</th>
                            </thead>
                            <tbody>
                                @php
                                    $noWeek = 1;
                                @endphp
                                @foreach ($item['weekly'] as $monweek)
                                    <tr>
                                        <td>{{ $noWeek }}</td>
                                        <td>{{ $monweek['condition'] }}</td>
                                        <td>{{ $monweek['voltage'] }}</td>
                                        <td>{{ $monweek['ampere'] }}</td>
                                        <td>{{ $monweek['drain'] }}</td>
                                        <td>{{ $monweek['pre'] }}</td>
                                        <td>{{ $monweek['after'] }}</td>
                                        <td>
                                            @if ($monweek['condensor'] == 1)
                                                {{-- <i
                                                    class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i> --}}
                                                cleaning
                                            @else
                                                {{-- <i
                                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i> --}}
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $monweek['name'] }}</td>
                                    </tr>
                                    @php
                                        $noWeek++;
                                    @endphp
                                @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
                {{-- <div class="table-responsive text-nowrap mb-4">
                    <table class="table table-bordered">
                        @if ($item['unit'] != 'REFRIGERANT AIR DRYER')
                            <thead class="table-light">
                                <th>Week</th>
                                <th>Condition</th>
                                <th>Vibration</th>
                                <th>Voltage</th>
                                <th>Running Amp.</th>
                                <th>Cleaning Cooler</th>
                                <th>Cek Coupling / Belt</th>
                                <th>Compressor / Area</th>
                            </thead>
                            <tbody>
                                @forelse ($item['weekly'] as $monweek)
                                    <tr>
                                        <td>{{ $monweek['date'] }}</td>
                                        <td>{{ $monweek['condition'] }}</td>
                                        <td>{{ $monweek['vibration'] }}</td>
                                        <td>{{ $monweek['voltage'] }}</td>
                                        <td>{{ $monweek['ampere'] }}</td>
                                        <td>
                                            @if ($monweek['cooler'] == 1)
                                                <i
                                                    class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                                            @else
                                                <i
                                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($monweek['coupling'] == 1)
                                                <i
                                                    class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                                            @else
                                                <i
                                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($monweek['area'] == 1)
                                                <i
                                                    class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                                            @else
                                                <i
                                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">Belum Ada Monitoring week</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        @else
                            <thead class="table-light">
                                <th>Week</th>
                                <th>Condition</th>
                                <th>Voltage</th>
                                <th>Ampere</th>
                                <th>Auto Drain</th>
                                <th>Pre</th>
                                <th>After</th>
                                <th>Condensor</th>
                            </thead>
                            <tbody>
                                @forelse ($item['weekly'] as $monweek)
                                    <tr>
                                        <td>{{ $monweek['date'] }}</td>
                                        <td>{{ $monweek['condition'] }}</td>
                                        <td>{{ $monweek['voltage'] }}</td>
                                        <td>{{ $monweek['ampere'] }}</td>
                                        <td>{{ $monweek['drain'] }}</td>
                                        <td>{{ $monweek['pre'] }}</td>
                                        <td>{{ $monweek['after'] }}</td>
                                        <td>
                                            @if ($monweek['condensor'] == 1)
                                                <i
                                                    class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                                            @else
                                                <i
                                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum Ada Monitoring week</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        @endif
                    </table>
                </div> --}}
            </div>

            @if ($item['unit'] == 'REFRIGERANT AIR DRYER')
                <div class="monthly mb-5">
                    <h5>Monthly Monitoring</h5>
                    <div class="table-responsive text-nowrap mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <thead class="table-light">
                                    <th>Date</th>
                                    <th>Condition</th>
                                    <th>HP (High Pressure)</th>
                                    <th>LP (Low Pressure)</th>
                                    <th>Strainer</th>
                                    <th>PIC</th>
                                </thead>
                            <tbody>
                                @forelse ($item['monthly'] as $monweek)
                                    <tr>
                                        <td>{{ $monweek['date'] ?? '-' }}</td>
                                        <td>{{ $monweek['condition'] ?? '-' }}</td>
                                        <td>{{ $monweek['hp'] ?? '-' }}</td>
                                        <td>{{ $monweek['lp'] ?? '-' }}</td>
                                        <td>{{ $monweek['strainer'] ?? '-' }}</td>
                                        <td>{{ $monweek['pic'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">Belum Ada Monitoring week</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <table class="no-break">
                <tr>
                    <td class="text-center">
                        <p>PT Reftech Jaya Optima</p>
                        <div class="">
                            <img src="{{ url('') . '/asset/sign/ttdAngel.jpg' }}" alt="" srcset=""
                                height="100">
                        </div>
                        <p>Angel Irene</p>
                    </td>
                    <td style="width: 50%"></td> <!-- Kolom kosong untuk jarak tengah -->
                    <td class="text-center">
                        <p>PT Fajar Surya Wisesa</p>
                        <div class="pb-5"></div>
                        <p class="pt-3">..........................................</p>
                    </td>
                </tr>
            </table>

            <div class="invoice mb-4">
                @foreach ($item['reports'] as $service)
                    <div class="page-break"></div>
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md"
                                            src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                            alt="" srcset="" class="img-logo">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                            <div style="font-size: 10px">
                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                <p class="mb-1">
                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                    54417653{{ '  |  ' }}<i
                                        class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold">SERVICE REPORT</h3>
                            <div>
                                <span class="fw-bolder">#{{ $service['no_service'] }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted">{{ $service['date'] }}</span>
                            </div>
                            <div class="mt-1">
                                @php
                                    $badgeClass = '';
                                    $label = $service['type'];

                                    switch ($service['type']) {
                                        case 'Visit':
                                            $badgeClass = 'success';
                                            break;
                                        case 'Service':
                                            $badgeClass = 'danger';
                                            break;
                                        case 'General':
                                            $badgeClass = 'primary';
                                            $label = 'General Check';
                                            break;
                                        default:
                                            $badgeClass = '';
                                            break;
                                    }
                                @endphp
                                <span
                                    class="badge fs-6 rounded-pill bg-label-{{ $badgeClass }}">{{ $label }}</span>
                            </div>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row mb-3">
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Customers </p>
                            <p class="mb-1">Address </p>
                            <p class="mb-1">PIC </p>
                        </div>
                        <div class="col-4">
                            <p class="mb-1">: {{ $service['company'] }}</p>
                            <p class="mb-1">: {{ $service['area'] }}</p>
                            <p class="mb-1">: {{ $service['pic'] }}</p>
                        </div>
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Unit Type </p>
                            <p class="mb-1">Serial Number </p>
                            <p class="mb-1">Running & Load </p>
                        </div>
                        <div class="col-4">
                            <p class="mb-1">: {{ $service['unit'] }}</p>
                            <p class="mb-1">:
                                {{ $service['tag'] }} |
                                {{ $service['location'] }}
                            </p>
                            <p class="mb-1">: {{ $service['running'] }} | {{ $service['load'] }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-2 fw-medium">
                            <p class="mb-1">Job Description </p>
                        </div>
                        <div class="col-10 d-flex gap-1">
                            <p>: </p>
                            <p class="mb-1">: {{ $service['jobdesc'] }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <h5 class="my-2">Description</h5>
                            <pre class="mb-1"
                                style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $service['desc'] }}
                </pre>
                        </div>
                        <div class="col-6">
                            <h5 class="my-2">Recomendation</h5>
                            <pre class="mb-1"
                                style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $service['recomendation'] }}</pre>
                        </div>
                    </div>
                    <hr>
                    <h5 class="my-4">Picture</h5>
                    <div class="row mb-5">
                        @foreach ($service['picture'] as $picture)
                            <div class="col-4 text-center">
                                <img src="{{ url('') . '/' . $picture->picture }}" alt="" srcset=""
                                    style="max-width : 200px;">
                                <p>{{ $picture->keterangan }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="row mt-5">
                        <div class="col-4 mt-5 text-center">
                            <p>PT Reftech Jaya Optima</p>
                            @if (isset($service['technician_sign']))
                                <img src="{{ url('') . '/' . $service['technician_sign'] }}" alt=""
                                    srcset="" height="100">
                            @else
                                <div class="pb-5"></div>
                            @endif
                            <p class="pt-3">( {{ $service['technician'] }} )</p>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4 mt-5 text-center">
                            <p class="">{{ $service['company'] }}</p>
                            @if (isset($service['sign_client']))
                                <img src="{{ url('') . '/' . $service['sign_client'] }}" alt=""
                                    srcset="" height="100">
                            @else
                                <div class="pb-5"></div>
                            @endif
                            <p class="pt-3">( {{ $service['pic'] }} )</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="page-break"></div>
        @endforeach
    </div>
</div>
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-monitoring-print.css" />
    <link rel="stylesheet" href="style.css">
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/js/app-invoice-print.js"></script>
    <script>
        window.addEventListener('beforeprint', () => {
            const rows = document.querySelectorAll('table tr');
            rows.forEach((row, index) => {
                const rect = row.getBoundingClientRect();
                if (rect.top > window.innerHeight) {
                    row.style.marginTop = '20mm';
                }
            });
        });
    </script>
@endpush
