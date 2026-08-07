@extends('layouts.sales.app')
@section('title', 'Service Reports')
@section('content')
    <div class="row invoice-preview">
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card mb-4">
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
                                <span class="text-muted">{{ $machine->unit->brand }} {{ $machine->unit->unit->sku }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted">{{ $machine->tag }} - {{ $machine->location }}</span>
                            </div>
                        </div>
                    </div>
                    <hr class="my-2">
                    {{-- <div class="row mb-3">
                        <div class="col-lg-2 col-4 fw-medium">
                            <p class="mb-1">Customers </p>
                            <p class="mb-1">Address </p>
                            <p class="mb-1">PIC </p>
                        </div>
                        <div class="col-lg-4 col-8">
                            <p class="mb-1">: a</p>
                            <p class="mb-1">: a</p>
                            <p class="mb-1">: a</p>
                        </div>
                        <div class="col-lg-2 col-4 fw-medium">
                            <p class="mb-1">Unit Type </p>
                            <p class="mb-1">Serial Number </p>
                            <p class="mb-1">Running & Load </p>
                        </div>
                        <div class="col-lg-4 col-8">
                            <p class="mb-1">: a</p>
                            <p class="mb-1">: a</p>
                            <p class="mb-1">: a</p>
                        </div>
                    </div>
                    <hr> --}}

                    {{-- @if (Auth::user()->role == 'Client') --}}
                        <h5>Daily Check</h5>
                        <div class="table-responsive text-nowrap mb-4">
                            @if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER')
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <th>Date</th>
                                        <th>Condition</th>
                                        <th>R Hr.</th>
                                        <th>L Hr.</th>
                                        <th>Press.</th>
                                        <th>Temp. (90°C - 100°C)</th>
                                        <th>Oil Lvl</th>
                                        <th>Kebocoran</th>
                                        <th>PIC</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($compressor as $item)
                                            <tr>
                                                @if (Auth::user()->role == 'Client')
                                                    <td>
                                                        {{ $item['date'] }}
                                                    </td>
                                                @else
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-label-secondary dropdown-toggle waves-effect"
                                                                data-bs-toggle="dropdown"
                                                                aria-expanded="false">{{ $item['date'] }}</button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class=" dropdown-item cursor-pointer"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editIssue-{{ $item['id'] }}">
                                                                        Update Issue
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class=" dropdown-item cursor-pointer"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editMainlog-{{ $item['id'] }}">
                                                                        Update Mainlog
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        {{-- <a class="text-black text-decoration-underline cursor-pointer"
                                                    data-bs-toggle="modal" data-bs-target="#editIssue-{{ $item['id'] }}">
                                                    {{ $item['date'] }}
                                                </a> --}}
                                                    </td>
                                                @endif
                                                <td>{{ $item['condition'] }}</td>
                                                <td>{{ $item['running'] }}</td>
                                                <td>{{ $item['loading'] }}</td>
                                                <td>{{ $item['pressure'] }}</td>
                                                <td>
                                                    @php
                                                        $stringTemp = $item['temp'] ?? '';
                                                        $stringTemp = str_replace(',', '.', $stringTemp); // ganti koma jadi titik

                                                        $tempNumber = null;

                                                        if (preg_match('/\d+(\.\d+)?/', $stringTemp, $matches)) {
                                                            $tempNumber = (float) $matches[0];
                                                        }
                                                    @endphp

                                                    @if (!is_null($tempNumber) && $tempNumber >= 100)
                                                        <p class="mb-0 fw-bold fs-6 text-danger">
                                                            {{ $item['temp'] }}
                                                        </p>
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
                                    <thead class="table-light">
                                        <th>Date</th>
                                        <th>Condition</th>
                                        <th>Temp IN</th>
                                        <th>Temp OUT</th>
                                        <th>Dew P.</th>
                                        <th>Auto Drain</th>
                                        <th>Fan Kondenser</th>
                                        <th>Kebocoran</th>
                                        <th>PIC</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($dryer as $item)
                                            <tr>
                                                @if (Auth::user()->role == 'Client')
                                                    <td>
                                                        {{ $item['date'] }}
                                                    </td>
                                                @else
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-label-secondary dropdown-toggle waves-effect"
                                                                data-bs-toggle="dropdown"
                                                                aria-expanded="false">{{ $item['date'] }}</button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class=" dropdown-item cursor-pointer"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editIssue-{{ $item['id'] }}">
                                                                        Update Issue
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class=" dropdown-item cursor-pointer"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editMainlog-{{ $item['id'] }}">
                                                                        Update Mainlog
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        {{-- <a class="text-black text-decoration-underline cursor-pointer"
                                                    data-bs-toggle="modal" data-bs-target="#editIssue-{{ $item['id'] }}">
                                                    {{ $item['date'] }}
                                                </a> --}}
                                                    </td>
                                                @endif
                                                <td>{{ $item['condition'] }}</td>
                                                <td>{{ $item['temp'] }}</td>
                                                <td>{{ $item['temp_out'] }}</td>
                                                <td>
                                                    @if (!is_null($item['dew']) && $item['dew'] > 10)
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
                    {{-- @else
                        @if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER')
                            <table class="datatable-compressor table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Condition</th>
                                        <th>R Hr.</th>
                                        <th>L Hr.</th>
                                        <th>Press.</th>
                                        <th>Temp. (85°C - 94°C)</th>
                                        <th>Oil Lvl</th>
                                        <th>Kebocoran</th>
                                        <th>PIC</th>
                                    </tr>
                                </thead>
                            </table>
                        @else
                            <table class="datatable-dryer table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Condition</th>
                                        <th>Temp IN</th>
                                        <th>Temp OUT</th>
                                        <th>Dew P.</th>
                                        <th>Auto Drain</th>
                                        <th>Fan Kondenser</th>
                                        <th>Kebocoran</th>
                                        <th>PIC</th>
                                    </tr>
                                </thead>
                            </table>
                        @endif
                    @endif --}}
                </div>
            </div>

            @if ($machine->unit->unit->unit == 'REFRIGERANT AIR DRYER')
                <div class="card invoice-preview-card mb-4">
                    <div class="card-body">
                        <div class="monthly mb-4">
                            <h5>Monthly Check</h5>
                            <div class="table-responsive text-nowrap mb-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <th>Date</th>
                                        <th>HP (High Pressure)</th>
                                        <th>LP (Low Pressure)</th>
                                        <th>Strainer</th>
                                    </thead>
                                    <tbody>
                                        @if ($monthly)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($monthly->date)->format('d-m-Y') }}</td>
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

            <div class="card invoice-preview-card mb-4">
                <div class="card-body">
                    <div class="issue mb-4">
                        <table class="datatable-issue table table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Issue</th>
                                    <th>Recommendation</th>
                                    <th>Part Number</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card invoice-preview-card mb-4">
                <div class="card-body">
                    <div class="mainlog mb-4">
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Maintenance Log</h5>
                            <button type="button" class="btn btn-primary d-grid waves-effect" data-bs-toggle="modal"
                                data-bs-target="#addMainLog">+ Mainlog</button>
                        </div>
                        <div class="table-responsive text-nowrap mb-4">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Maintenance</th>
                                        <th>PIC</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($mainlog as $main)
                                        <tr>
                                            <td>
                                                {{ \Carbon\Carbon::parse($main->date)->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                {{ $main->desc }}
                                            </td>
                                            <td>
                                                {{ $main->teknisi->name }}
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a data-id="{{ $main->id }}" data-month="{{ $months }}"
                                                        data-machine="{{ $machine->id }}"
                                                        class="btn btn-sm btn-label-danger delete-mainlog waves-effect">
                                                        <i
                                                            class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-label-warning waves-effect"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editMainlog-{{ $main->id }}">
                                                        <i
                                                            class="menu-icon tf-icons mdi mdi-14px mdi-file-edit-outline m-0"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum Ada Mainlog</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('service-manager-daily-prokemas.print', [request()->route('id'), request()->route('month')]) }}">
                        Download
                    </a>
                    <button id="buttonShare" data-id="1"
                        class="btn btn-success d-grid w-100 waves-effect mb-3">Bagikan</button>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
        </div>
        {{-- End : Button Invoice --}}
        @foreach ($compressor as $comp)
            @include('components.modal.monitoring.issue')
        @endforeach
        @foreach ($mainlog as $log)
            @include('components.modal.monitoring.mainlog')
        @endforeach
        @include('components.modal.monitoring.mainlog-create-service')
    </div>
@endsection
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/includes/table-compressor-daily.js"></script>
    <script src="{{ asset('assets') }}/includes/table-dryer-daily.js"></script>
    <script src="{{ asset('assets') }}/includes/table-daily-issue.js"></script>
    <script src="{{ asset('assets') }}/includes/table-daily-mainlog.js"></script>
@endpush
@push('script')
    <script>
        $(() => {
            $('#buttonShare').on('click', function() {
                const id = $(this).data('id')
                if (navigator.share) {
                    navigator.share({
                        title: 'Service Reports',
                        text: 'Check out this link!',
                        url: '{{ route('service-reports.show', ':id') }}'.replace(':id', id)
                    }).then(() => {
                        console.log('Thanks for sharing!');
                    }).catch(err => {
                        console.error('Error sharing:', err);
                    });
                } else {
                    alert('Sharing not supported in this browser.');
                }
            });

            $('#backButton').click(function() {
                window.history.back();
            });
        });

        function validateInput(event) {
            const input = event.target;
            // Izinkan hanya angka dan koma
            input.value = input.value.replace(/[^0-9,]/g, '');
        }
        $('#conditionSelect').on('change', function() {
            var condition = $(this).val();
            var disable = $('.offDisable');
            var number = $('#numberInput');

            if (condition == 'Off') {
                disable.prop('disabled', true);
                // number.prop('disabled', true);
            } else {
                // number.prop('disabled', false);
                disable.prop('disabled', false);
            }
            console.log(number);

            console.log(condition);
        });
        $(document).on('click', '.delete-mainlog', function() {
            var id = $(this).data('id');
            var month = $(this).data('month');
            var machine = $(this).data('machine');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('monitoring') }}/daily-mainlog/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/service-manager-daily/' +
                                        machine + '/' + month;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
    </script>
@endpush
