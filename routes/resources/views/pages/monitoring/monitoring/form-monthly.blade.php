@extends('layouts.sales.app')
@section('title', 'Monitoring machine')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('store.monthly-monitoring', $machine->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <h5 class="text-center">MONTHLY CHECK AIR DRYER {{ $machine->unit->brand }}
                    {{ $machine->unit->unit->sku }}</h5>
                <div class="daily-compressor">
                    <div class="row">
                        <div class="col-12 col-lg-8">
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
                                <div class="col-4 col-lg-2">
                                    Date
                                </div>
                                <div class="col-8 col-lg-10">
                                    : {{ \Carbon\Carbon::now()->format('d-m-Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="defaultSelect" class="form-label">Condition</label>
                            <select id="conditionSelect" name="condition" class="form-select">
                                <option value="Running">Running</option>
                                <option value="Stand By">Stand By</option>
                                <option value="Off">Off</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label for="defaultInput" class="form-label">Input HP (High Pressure)</label>
                            <input id="numberInput" class="form-control offDisable" name="hp" type="text"
                                placeholder="Input HP">
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label for="defaultInput" class="form-label">Input LP (Low Pressure)</label>
                            <input id="numberInput" class="form-control offDisable" name="lp" type="text"
                                placeholder="Input LP">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="defaultInput" class="form-label">Pengecekan dan bersihkan strainer pada timer drain
                                di dryer, inline filter dan receiver tank</label>
                            <div class="input-group input-group-merge">
                                <select name="strainer" class="form-select offDisable">
                                    <option value="Oke">Oke</option>
                                    <option value="Not Oke">Not Oke</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
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
    </div>
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
    <script src="{{ asset('assets') }}/includes/table-monitoring-machine.js"></script>
    <script>
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
    </script>
@endpush
