<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../../assets/" data-template="vertical-menu-template">

<head>
    @include('includes.sales.meta')
    @section('title', 'Monitoring Visit')
    @include('includes.sales.style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />


    {{--  laravel style  --}}
    <script src="{{ asset('/assets') }}/vendor/js/helpers.js"></script>

    {{-- ! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section --}}
    {{-- ? Template customizer: To hide customizer set displayCustomizer value false in config.js.  --}}
    <script src="{{ asset('/assets') }}/vendor/js/template-customizer.js"></script>

    {{--  ? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.   --}}
    <script src="{{ asset('assets') }}/js/config.js"></script>
    @routes
</head>

<body>
    <!--  Layout wrapper  -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="container">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-row flex-column">
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
                                <div>
                                    <h3 class="fw-bold">DAILY MONITORING</h3>
                                    <div>
                                        <span class="fw-bolder">{{ $machine->unit->unit->unit }}</span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="fw-bold">{{ $machine->unit->brand }}
                                            {{ $machine->unit->unit->sku }}</span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="text-muted">{{ $machine->tag }} - {{ $machine->location }}</span>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="daily mb-4">
                                <h4>Daily Check</h4>
                                <div class="table-responsive text-nowrap mt-4">
                                    @if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER')
                                        <table class="table table-bordered">
                                            <thead class="table-light" align="center">
                                                <th style="vertical-align: middle;">Date</th>
                                                <th style="vertical-align: middle;">Condition</th>
                                                <th style="vertical-align: middle;">Running<br>Hour</th>
                                                <th style="vertical-align: middle;">Load Hour</th>
                                                <th style="vertical-align: middle;">Press.</th>
                                                <th style="vertical-align: middle;">Temp.<br>(85°C - 94°C)</th>
                                                <th style="vertical-align: middle;">Oil Level</th>
                                                <th style="vertical-align: middle;">Kebocoran</th>
                                                <th style="vertical-align: middle;">PIC</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($compressor as $item)
                                                    <tr align="center">
                                                        <td>{{ $item['date'] }}</td>
                                                        <td>{{ $item['condition'] }}</td>
                                                        <td>{{ $item['running'] }}</td>
                                                        <td>{{ $item['loading'] }}</td>
                                                        <td>{{ $item['pressure'] }}</td>
                                                        <td>
                                                            @php
                                                                $stringTemp = $item['temp'] ?? ''; // Pastikan nilai tidak null
                                                                $tempNumber = null;

                                                                if (
                                                                    preg_match('/\d+(\.\d+)?/', $stringTemp, $matches)
                                                                ) {
                                                                    $tempNumber = (float) $matches[0]; // Gunakan float agar mendukung desimal
                                                                }
                                                            @endphp

                                                            @if (!is_null($tempNumber) && $tempNumber > 94)
                                                                <p class="mb-0 fw-bold fs-6 text-danger">
                                                                    {{ $item['temp'] }}</p>
                                                            @else
                                                                {{ $item['temp'] }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $item['oil_level'] }}</td>
                                                        <td>{{ $item['leak'] }}</td>
                                                        <td>{{ $item['pic'] }}</td>
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
                                                <th style="vertical-align: middle;">Dewpoint</th>
                                                <th style="vertical-align: middle;">Auto Drain</th>
                                                <th style="vertical-align: middle;">Fan<br>Condensor</th>
                                                <th style="vertical-align: middle;">Kebocoran</th>
                                                <th style="vertical-align: middle;">PIC</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($dryer as $item)
                                                    <tr align="center">
                                                        <td>{{ $item['date'] }}</td>
                                                        <td>{{ $item['condition'] }}</td>
                                                        <td>{{ $item['temp'] }}</td>
                                                        <td>{{ $item['temp_out'] }}</td>
                                                        <td>
                                                            @php
                                                                $stringDew = $item['dew'] ?? ''; // Pastikan nilai tidak null
                                                                $dewNumber = null;

                                                                if (preg_match('/\d+(\.\d+)?/', $stringDew, $matches)) {
                                                                    $dewNumber = (float) $matches[0]; // Gunakan float agar mendukung desimal
                                                                }
                                                            @endphp
                                                            @if (!is_null($dewNumber) && $dewNumber > 10)
                                                                <p class="mb-0 fw-bold fs-6 text-danger">
                                                                    {{ $item['dew'] }}</p>
                                                            @else
                                                                {{ $item['dew'] }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $item['drain'] }}</td>
                                                        <td>{{ $item['fan'] }}</td>
                                                        <td>{{ $item['leak'] }}</td>
                                                        <td>{{ $item['pic'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($machine->id != 495 || $machine->id != 495)
                        <div class="weekly mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4>Weekly Check</h4>
                                    <div class="table-responsive text-nowrap mb-4">
                                        <table class="table table-bordered">
                                            @if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER')
                                                <thead class="table-light" align="center">
                                                    <th style="vertical-align: middle;">Week</th>
                                                    <th style="vertical-align: middle;">Condition</th>
                                                    <th style="vertical-align: middle;">Vibration<br>(mm/s)</th>
                                                    <th style="vertical-align: middle;">Voltage (V)</th>
                                                    <th style="vertical-align: middle;">Ampere (A)</th>
                                                    <th style="vertical-align: middle;">Cleaning<br>Cooler</th>
                                                    <th style="vertical-align: middle;">Cek Coupling<br>/ Belt</th>
                                                    <th style="vertical-align: middle;">Cleaning<br>Compressor & Area
                                                    </th>
                                                    <th style="vertical-align: middle;">PIC</th>
                                                </thead>
                                                <tbody align="center">
                                                    @php
                                                        $noWeek = 1;
                                                    @endphp
                                                    @forelse ($weeksoy as $monweek)
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
                                                                @elseif($monweek['coupling'] == 0)
                                                                    {{-- <i
                                                                    class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i> --}}
                                                                    Not Ok
                                                                @else
                                                                    -
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
                                                <thead class="table-light" align="center">
                                                    <th style="vertical-align: middle;">Week</th>
                                                    <th style="vertical-align: middle;">Condition</th>
                                                    <th style="vertical-align: middle;">Voltage (V)</th>
                                                    <th style="vertical-align: middle;">Ampere (A)</th>
                                                    <th style="vertical-align: middle;">Auto Drain</th>
                                                    <th style="vertical-align: middle;">Pre</th>
                                                    <th style="vertical-align: middle;">After</th>
                                                    <th style="vertical-align: middle;">Cleaning<br>Condensor</th>
                                                    <th style="vertical-align: middle;">PIC</th>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $noWeek = 1;
                                                    @endphp
                                                    @foreach ($weeksoy as $monweek)
                                                        <tr align="center">
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
                                </div>
                            </div>
                        </div>
                        @if ($machine->unit->unit->unit == 'REFRIGERANT AIR DRYER')
                            <div class="monthly mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h4>Monthly Check</h4>
                                        <div class="table-responsive text-nowrap mb-4">
                                            <table class="table table-bordered">
                                                <thead class="table-light" align="center">
                                                    <th style="vertical-align: middle;">Date</th>
                                                    <th style="vertical-align: middle;">HP (High Pressure)</th>
                                                    <th style="vertical-align: middle;">LP (Low Pressure)</th>
                                                    <th style="vertical-align: middle;">Strainer</th>
                                                </thead>
                                                <tbody>
                                                    @if ($monthly)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($monthly->date)->format('d-m-Y') }}
                                                            </td>
                                                            <td>{{ $monthly->hp }}</td>
                                                            <td>{{ $monthly->lp }}</td>
                                                            <td>{{ $monthly->strainer }}</td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td>-</td>
                                                            <td>-</td>
                                                            <td>-</td>
                                                            <td>-</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    <div class="issue mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4>Issue & Recommendation</h4>
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered">
                                        <thead class="table-light" align="center">
                                            <th style="vertical-align: middle;">Date</th>
                                            <th style="vertical-align: middle;">Issue</th>
                                            <th style="vertical-align: middle;">Recommendation</th>
                                            <th style="vertical-align: middle;">PN (Material)</th>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 0;
                                            @endphp
                                            @forelse ($issue as $issues)
                                                @php
                                                    $no++;
                                                @endphp
                                                <tr>
                                                    <td>{{ $issues->date }}</td>
                                                    <td>
                                                        <pre class="mb-1"
                                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;">{{ $issues->issue }}</pre>
                                                    </td>
                                                    <td>
                                                        <pre class="mb-1"
                                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;">{{ $issues->recommendation }}</pre>
                                                    </td>
                                                    <td>{{ $issues->pn }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Belum Ada Issue</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($machine->id != 495 || $machine->id != 495)
                        <div class="quote mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4>Quotation</h4>
                                    <div class="table-responsive text-nowrap mb-4">
                                        <table class="table table-bordered">
                                            <thead class="table-light" align="center">
                                                <tr>
                                                    <th style="vertical-align: middle;">Date</th>
                                                    <th style="vertical-align: middle;">No. Quote</th>
                                                    <th style="vertical-align: middle;">No. PR</th>
                                                    <th style="vertical-align: middle;">Title</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($quotes as $quote)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($quote->estimated_date)->format('d-m-Y') }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('quotation.show', $quote->id) }}"
                                                                class="text-black">
                                                                {{ $quote->no_quote }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $quote->no_pr }}</td>
                                                        <td>{{ $quote->title }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">Belum Ada Quote
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mainlog mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4>Maintenance Log</h4>
                                <div class="table-responsive text-nowrap">
                                    <table class="table table-bordered" align="center">
                                        <thead class="table-light" align="center">
                                            <th style="vertical-align: middle;">Date</th>
                                            <th style="vertical-align: middle;">Maintenance Description</th>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 0;
                                            @endphp
                                            @forelse ($mainlog as $item)
                                                @php
                                                    $no++;
                                                @endphp
                                                <tr>
                                                    <td>{{ $item->date }}</td>
                                                    <td>
                                                        <pre class="mb-1"
                                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;">{{ $item->desc }}</pre>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Belum Ada Maintenance Log
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-backdrop fade"></div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    {{--  javascript --}}
    @stack('before-script')

    @include('includes.sales.script')

    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/js/main.js"></script>

    <script>
        $(document).on('click', '.view-quote', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan

            $.ajax({
                url: '{{ url('quotation') }}/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
        $(document).on('click', '.view-quotation', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan

            console.log(id);

            $.ajax({
                url: '{{ url('quotation') }}/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
        $(document).on('click', '.view-prospect', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan    

            $.ajax({
                url: '{{ url('prospect') }}/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
    </script>

    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-monitoring-machine-visit.js"></script>
    <script src="{{ asset('assets') }}/includes/table-monitoring-dryer-visit.js"></script>

    @stack('script')
</body>

</html>
