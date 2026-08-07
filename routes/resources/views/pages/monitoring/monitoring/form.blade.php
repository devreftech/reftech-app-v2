@extends('layouts.sales.app')
@section('title', 'Monitoring machine')
@section('content')
    <div class="card mb-3">
        @if ($machine->monitoring()->whereDate('created_at', Carbon\Carbon::today())->exists())
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Data Monitoring Telah Ada
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="card-body text-center">
                <p>Daily {{ $machine->unit->brand }} {{ $machine->unit->unit->sku }} Sudah di input!</p>
                <div class="tombol d-flex gap-2 justify-content-center">
                    <a class="btn btn-primary waves-effect" href="{{ route('create.weekly-monitoring', $machine->id) }}">
                        Input Weekly
                    </a>
                    <a href="{{ route('create.monthly-monitoring', $machine->id) }}"
                        class="btn btn-warning waves-effect">Monthly Monitoring</a>
                    @if ($monitoring->main_desc == null)
                        <button type="button" class="btn btn-secondary d-grid waves-effect" data-bs-toggle="modal"
                            data-bs-target="#addMainLog">Input Maintenance Log</button>
                    @endif
                </div>
            </div>
        @else
            <div class="card-body">
                <form action="{{ route('store.daily-monitoring', $machine->id) }}" method="post"
                    enctype="multipart/form-data" id="myForm">
                    @csrf
                    @if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER')
                        <h5 class="text-center">DAILY CHECK AIR COMPRESSOR {{ $machine->unit->brand }}
                            {{ $machine->unit->unit->sku }}</h5>
                        <div class="daily-compressor">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="row mb-3">
                                        <div class="col-4 col-lg-2">
                                            Location
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : {{ $machine->location }}
                                        </div>
                                        <div class="col-4 col-lg-2">
                                            Tag Number
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : {{ $machine->tag }}
                                        </div>
                                        <div class="col-4 col-lg-2">
                                            Unit
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : {{ $machine->unit->brand }} {{ $machine->unit->unit->sku }}
                                        </div>
                                        @if (Auth::user()->code != 'RMD')
                                            <div class="col-4 col-lg-2">
                                                Date
                                            </div>
                                            <div class="col-8 col-lg-10">
                                                : {{ \Carbon\Carbon::now()->format('d-m-Y') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if (Auth::user()->code == 'RMD')
                                <div class="row my-3">
                                    <div class="col-4">
                                        <label for="Date">Date</label>
                                        <input class="form-control" type="date" id="Date" name="date"
                                            value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Condition</label>
                                    <select id="conditionSelect" name="condition" class="form-select">
                                        <option value="Running">Running</option>
                                        <option value="Stand By">Stand By</option>
                                        <option value="Off">Off</option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Oil Level</label>
                                    <select id="offDisable" name="oil" class="form-select offDisable">
                                        <option value="OK">OK</option>
                                        <option value="Kurang">Kurang</option>
                                    </select>
                                </div>
                                <div class="col col-lg-3">
                                    <label for="defaultInput" class="form-label">Running</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="running"
                                            type="text" placeholder="Hr" min="1" oninput="validateInput(event)">
                                        <span class="input-group-text">Hours</span>
                                    </div>
                                </div>
                                <div class="col col-lg-3">
                                    <label for="defaultInput" class="form-label">Loading Hr</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="loading"
                                            type="text" placeholder="Hr" min="1" oninput="validateInput(event)">
                                        <span class="input-group-text">Hours</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-6">
                                    <label for="defaultInput" class="form-label">Pressure</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="pressure"
                                            type="text" placeholder="Bar" oninput="validateInput(event)">
                                        <span class="input-group-text">Bar</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="temperature"
                                            type="text" placeholder="°C" oninput="validateInput(event)">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Cek Kebocoran</label>
                                    <select id="offDisable" name="leak" class="form-select offDisable">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline mb-3">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="issue"
                                    placeholder="Comments here..."></textarea>
                                <label for="exampleFormControlTextarea1">Issue</label>
                            </div>
                            <div class="mb-4">
                                <label for="formFile" class="form-label">Input Bukti Photo</label>
                                <input class="form-control" type="file" name="picture" id="formFile"
                                    accept="image/*">
                                <p class="text-muted">Note :Format photo harus ada bukti tanggal pada Photonya</p>
                            </div>
                        </div>
                    @else
                        <h5 class="text-center">DAILY CHECK AIR DRYER {{ $machine->unit->brand }}
                            {{ $machine->unit->unit->sku }}</h5>
                        <div class="daily-compressor">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <div class="row mb-3">
                                        <div class="col-4 col-lg-2">
                                            Location
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : {{ $machine->location }}
                                        </div>
                                        <div class="col-4 col-lg-2">
                                            Tag Number
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : {{ $machine->tag }}
                                        </div>
                                        <div class="col-4 col-lg-2">
                                            Unit
                                        </div>
                                        <div class="col-8 col-lg-10">
                                            : {{ $machine->unit->brand }} {{ $machine->unit->unit->sku }}
                                        </div>
                                        @if (Auth::user()->code != 'RMD')
                                            <div class="col-4 col-lg-2">
                                                Date
                                            </div>
                                            <div class="col-8 col-lg-10">
                                                : {{ \Carbon\Carbon::now()->format('d-m-Y') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if (Auth::user()->code == 'RMD')
                                <div class="row my-3">
                                    <div class="col-4">
                                        <label for="Date">Date</label>
                                        <input class="form-control" type="date" id="Date" name="date"
                                            value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Condition</label>
                                    <select id="conditionSelect" name="condition" class="form-select">
                                        <option value="Running">Running</option>
                                        <option value="Stand By">Stand By</option>
                                        <option value="Off">Off</option>
                                    </select>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Dew Point</label>
                                    <div class="input-group input-group-merge">
                                        <input id="offDisable" class="form-control offDisable" name="dew"
                                            type="text" placeholder="Dew Point">
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Auto Drain</label>
                                    <select id="offDisable" name="drain" class="form-select offDisable">
                                        <option value="Ok">Ok</option>
                                        <option value="Not Ok">Not Ok</option>
                                    </select>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <label for="defaultSelect" class="form-label">Cek Kebocoran</label>
                                    <select id="offDisable" name="leak" class="form-select offDisable">
                                        <option value="Ada">Ada</option>
                                        <option value="Tidak Ada">Tidak Ada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature In</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="temperature_in"
                                            type="text" placeholder="°C" oninput="validateInput(event)">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <label for="defaultInput" class="form-label">Temperature Out</label>
                                    <div class="input-group input-group-merge">
                                        <input id="numberInput" class="form-control offDisable" name="temperature_out"
                                            type="text" placeholder="°C" oninput="validateInput(event)">
                                        <span class="input-group-text">°C</span>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <label for="defaultSelect" class="form-label">Cek Fan Kondensor</label>
                                    <select id="offDisable" name="fan" class="form-select offDisable">
                                        <option value="Ok">Ok</option>
                                        <option value="Not Ok">Not Ok</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline mb-3">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="issue"
                                    placeholder="Comments here..."></textarea>
                                <label for="exampleFormControlTextarea1">Issue</label>
                            </div>
                            <div class="mb-4">
                                <label for="formFile" class="form-label">Input Bukti Photo</label>
                                <input class="form-control" type="file" name="picture" id="formFile"
                                    accept="image/*">
                                <p class="text-muted">Note :Format photo harus ada bukti tanggal pada Photonya</p>
                            </div>
                        </div>
                    @endif
                    {{-- <hr>
                    <h5 class="mb-3">MAINTENANCE LOG</h5>
                    <div class="row mb-3">
                        <div class="col-12 col-lg-6">
                            <label for="defaultInput" class="form-label">Maintenance Description</label>
                            <div class="input-group input-group-merge">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="main_desc"></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3">
                            <label for="defaultInput" class="form-label">Next Maintenance Planned</label>
                            <div class="input-group input-group-merge">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="main_next"></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3">
                            <label for="defaultInput" class="form-label">Vendor</label>
                            <div class="input-group input-group-merge">
                                <input id="defaultInput" class="form-control" name="technician" type="text"
                                    placeholder="Vendor">
                            </div>
                        </div>
                    </div> --}}
                    <div class="float-end">
                        <a href="{{ route('index.daily-monitoring', $machine->id) }}" type="button"
                            class="btn btn-lg btn-outline-secondary">
                            Back
                        </a>
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
    @include('components.modal.monitoring.mainlog-create')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/toastr/toastr.css" />
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
    <script src="{{ asset('assets') }}/includes/table-monitoring-machine.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
    <script>
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

        // document.getElementById('myForm').addEventListener('submit', function(e) {
        //     e.preventDefault(); // Mencegah pengiriman form default
        //     const input = document.getElementById('numberInput').value;

        //     if (!/^\d+(,\d+)?$/.test(input)) {
        //         alert('Masukkan angka yang valid (hanya angka dan koma)!');
        //         return;
        //     }

        //     const validValue = parseFloat(input.replace(',', '.')); // Ganti koma dengan titik
        //     console.log('Angka valid yang dikirim:', validValue);
        //     alert(`Nilai yang valid: ${validValue}`);
        // });
    </script>
@endpush
