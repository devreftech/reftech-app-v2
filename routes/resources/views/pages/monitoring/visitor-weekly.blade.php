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
                    <div class="card">
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
                                    <h3 class="fw-bold">WEEKLY MONITORING</h3>
                                    <p class="mt-1">{{$startDate}} - {{$endDate}}</p>
                                    {{-- <div>
                                        <span class="fw-bolder">{{ $machine->unit->unit->unit }}</span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="fw-bold">{{ $machine->unit->brand }}
                                            {{ $machine->unit->unit->sku }}</span>
                                    </div>
                                    <div class="mt-1">
                                        <span class="text-muted">{{ $machine->tag }} - {{ $machine->location }}</span>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('visitor.daily-monitoring', $machine->id) }}"
                                    class="btn btn-success waves-effect">Go To Daily Monitoring</a>
                            </div>
                            <h5>Machine Compressor </h5>
                            <div class="table-responsive text-nowrap mt-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <th>Unit</th>
                                        <th>Condition</th>
                                        <th>Auto Drain</th>
                                        <th>Vibration</th>
                                        <th>Voltage</th>
                                        <th>Running Ampere</th>
                                        <th>Cooler</th>
                                        <th>Coupling</th>
                                        <th>Compressor/Area</th>
                                        <th>PIC</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($monitoringAC as $item)
                                            <tr class="{{ $item->idM == $machine->id ? 'bg-label-warning' : '' }}">
                                                <td>{{ $item->unit->brand }} {{ $item->unit->unit->sku }} ||
                                                    {{ $item->tag }} - {{ $item->location }}
                                                </td>
                                                <td>{{ $item->drain ?? '-' }}</td>
                                                <td>{{ $item->condition ?? '-' }}</td>
                                                <td>{{ $item->vibration ?? '-' }}</td>
                                                <td>{{ $item->voltage ?? '-' }}</td>
                                                <td>{{ $item->ampere ?? '-' }}</td>
                                                <td>
                                                    @if ($item->cooler == 1)
                                                        <i
                                                            class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                                                    @else
                                                        <i
                                                            class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->coupling == 1)
                                                        <i
                                                            class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                                                    @else
                                                        <i
                                                            class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($item->area == 1)
                                                        <i
                                                            class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                                                    @else
                                                        <i
                                                            class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $item->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <h5 class="mt-5">Machine Dryer </h5>
                            <div class="table-responsive text-nowrap mt-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <th>Unit</th>
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
                                        @foreach ($monitoringDRYER as $item)
                                            <tr class="{{ $item->idM == $machine->id ? 'bg-label-warning' : '' }}">
                                                <td>{{ $item->unit->brand }} {{ $item->unit->unit->sku }} ||
                                                    {{ $item->tag }} - {{ $item->location }}
                                                </td>
                                                <td>{{ $item->condition ?? '-' }}</td>
                                                <td>{{ $item->vibration ?? '-' }}</td>
                                                <td>{{ $item->ampere ?? '-' }}</td>
                                                <td>{{ $item->drain ?? '-' }}</td>
                                                <td>{{ $item->pre ?? '-' }}</td>
                                                <td>{{ $item->after ?? '-' }}</td>
                                                <td>
                                                    @if ($item->condensor == 1)
                                                        <i
                                                            class="mdi mdi-check-circle-outline scaleX-n1-rtl text-success me-1 mdi-14px"></i>
                                                    @else
                                                        <i
                                                            class="mdi mdi-alpha-x-circle-outline scaleX-n1-rtl text-danger me-1 mdi-14px"></i>
                                                    @endif
                                                </td>
                                                <td>{{ $item->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
