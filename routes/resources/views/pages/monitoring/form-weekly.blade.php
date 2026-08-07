@extends('layouts.sales.app')
@section('title', 'Monitoring machine')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form
                action="{{ @$weekly ? route('update.weekly-monitoring', $weekly->id) : route('store.weekly-monitoring', $machine->id) }}"
                method="post" enctype="multipart/form-data">
                @csrf
                @if ($machine->unit->unit->unit != 'REFRIGERANT AIR DRYER')
                    <h5 class="text-center">WEEKLY CHECK AIR COMPRESSOR {{ $machine->unit->brand }}
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
                            <div class="col-12 col-lg-4">
                                <label for="defaultSelect" class="form-label">Week</label>
                                <select id="conditionSelect" name="week" class="form-select">
                                    <option value="1" {{ @$weekly->week == 1 ? 'selected' : '' }}>Week 1</option>
                                    <option value="2" {{ @$weekly->week == 2 ? 'selected' : '' }}>Week 2</option>
                                    <option value="3" {{ @$weekly->week == 3 ? 'selected' : '' }}>Week 3</option>
                                    <option value="4" {{ @$weekly->week == 4 ? 'selected' : '' }}>Week 4</option>
                                    <option value="5" {{ @$weekly->week == 5 ? 'selected' : '' }}>Week 5</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-6 mb-3">
                                <label for="defaultSelect" class="form-label">Condition</label>
                                <select id="conditionSelect" name="condition" class="form-select">
                                    <option value="Running" @selected(old('condition', @$weekly->condition) == 'Running')>Running</option>
                                    <option value="Stand By" @selected(old('condition', @$weekly->condition) == 'Stand By')>Stand By</option>
                                    <option value="Off" @selected(old('condition', @$weekly->condition) == 'Off')>Off</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-3 mb-3">
                                <label for="defaultSelect" class="form-label">Cek / Uji Auto Drain</label>
                                <select id="offDisable" name="drain" class="form-select offDisable"
                                    @disabled(@$weekly->condition == 'off')>
                                    <option value="Ok" @selected(old('drain', @$weekly->drain) == 'Ok')>Ok</option>
                                    <option value="Not Ok" @selected(old('drain', @$weekly->drain) == 'Not Ok')>Not Ok</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-3 mb-3">
                                <label for="defaultInput" class="form-label">Vibration</label>
                                <div class="row">
                                    <div class="input-group input-group-merge">
                                        <div class="col-3">
                                            <input id="defaultInput" class="form-control offDisable" name="v"
                                                type="text" placeholder="V"
                                                value="{{ old('vibration', @$weekly->vibration) }}"
                                                @disabled(@$weekly->condition == 'off')>
                                        </div>
                                        <div class="col text-center">/</div>
                                        <div class="col-3">
                                            <input id="defaultInput" class="form-control offDisable" name="h"
                                                type="text" placeholder="H"
                                                value="{{ old('vibration', @$weekly->vibration) }}"
                                                @disabled(@$weekly->condition == 'off')>
                                        </div>
                                        <div class="col text-center">/</div>
                                        <div class="col-3">
                                            <input id="defaultInput" class="form-control offDisable" name="a"
                                                type="text" placeholder="A"
                                                value="{{ old('vibration', @$weekly->vibration) }}"
                                                @disabled(@$weekly->condition == 'off')>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 mb-3">
                                <label for="defaultInput" class="form-label">Voltage (V) <span class="text-danger">* WAJIB
                                        INPUT (R/S/T)</span></label>
                                <div class="input-group input-group-merge">
                                    <input id="defaultInput" class="form-control offDisable" name="voltage" type="text"
                                        placeholder="R/S/T" value="{{ substr(old('voltage', @$weekly->voltage), 0, -2) }}"
                                        @disabled(@$weekly->condition == 'off')>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 mb-3">
                                <label for="defaultInput" class="form-label">Running Ampere (A) <span class="text-danger">*
                                        WAJIB INPUT (R/S/T)</span></label>
                                <div class="input-group input-group-merge">
                                    <input id="defaultInput" class="form-control offDisable" name="ampere" type="text"
                                        placeholder="R/S/T" value="{{ substr(old('ampere', @$weekly->ampere), 0, -2) }}"
                                        @disabled(@$weekly->condition == 'off')>
                                </div>
                            </div>
                            <div class="col-12 col-lg-2 mb-2">
                                <div class="row">
                                    <div class="col-6 col-lg-12">
                                        <label for="defaultInput" class="form-label">Cleaning Cooler Menggunakan
                                            Angin</label>
                                    </div>
                                    <div class="col-6 col-lg-12">
                                        <input class="form-check-input" type="checkbox" name="cooler" value="1"
                                            id="defaultCheck1" @checked(old('cooler', @$weekly->cooler) == 1) @disabled(@$weekly->condition == 'off')>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-2 mb-2">
                                <div class="row">
                                    <div class="col-6 col-lg-12">
                                        <label for="defaultInput" class="form-label">Cek Coupling / V-Belt</label>
                                    </div>
                                    <div class="col-6 col-lg-12">
                                        <input class="form-check-input" type="checkbox" name="coupling" value="1"
                                            id="defaultCheck1" @checked(old('cooler', @$weekly->coupling) == 1) @disabled(@$weekly->condition == 'off')>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-2 mb-2">
                                <div class="row">
                                    <div class="col-6 col-lg-12">
                                        <label for="defaultInput" class="form-label">Cleaning Compressor/Area</label>
                                    </div>
                                    <div class="col-6 col-lg-12">
                                        <input class="form-check-input" type="checkbox" name="area" value="1"
                                            id="defaultCheck1" @checked(old('cooler', @$weekly->area) == 1) @disabled(@$weekly->condition == 'off')>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <h5 class="text-center">WEEKLY CHECK AIR DRYER {{ $machine->unit->brand }}
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
                            <div class="col-12 col-lg-4">
                                <label for="defaultSelect" class="form-label">Week</label>
                                <select id="conditionSelect" name="week" class="form-select"
                                    @disabled(@$weekly)>
                                    <option value="1" @selected(old('week', @$weekly->week) == '1')>Week 1</option>
                                    <option value="2" @selected(old('week', @$weekly->week) == '2')>Week 2</option>
                                    <option value="3" @selected(old('week', @$weekly->week) == '3')>Week 3</option>
                                    <option value="4" @selected(old('week', @$weekly->week) == '4')>Week 4</option>
                                    <option value="5" @selected(old('week', @$weekly->week) == '5')>Week 5</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 col-lg-6 mb-3">
                                <label for="defaultSelect" class="form-label">Condition</label>
                                <select id="conditionSelect" name="condition" class="form-select">
                                    <option value="Running" @selected(old('condition', @$weekly->condition) == 'Running')>Running</option>
                                    <option value="Stand By" @selected(old('condition', @$weekly->condition) == 'Stand By')>Stand By</option>
                                    <option value="Off" @selected(old('condition', @$weekly->condition) == 'Off')>Off</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-3">
                                <label for="defaultInput" class="form-label">Voltage (V) <span class="text-danger">*
                                        WAJIB INPUT (R/S/T)</span></label>
                                <div class="input-group input-group-merge">
                                    <input id="defaultInput" class="form-control offDisable" name="voltage"
                                        type="text" placeholder="R/S/T"
                                        value="{{ substr(old('voltage', @$weekly->voltage), 0, -2) }}"
                                        @disabled(@$weekly->condition == 'off')>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 mb-3">
                                <label for="defaultInput" class="form-label">Ampere Refrigerant Compressor (A)</label>
                                <div class="input-group input-group-merge">
                                    <input id="defaultInput" class="form-control offDisable" name="ampere"
                                        type="text" placeholder="R/S/T"
                                        value="{{ substr(old('ampere', @$weekly->ampere), 0, -2) }}"
                                        @disabled(@$weekly->condition == 'off')>
                                </div>
                            </div>
                            {{-- <div class="col-12 col-lg-6">
                                <label for="defaultInput" class="form-label">Dew Point</label>
                                <div class="input-group input-group-merge">
                                    <input id="defaultInput" class="form-control" name="dew" type="text"
                                        placeholder="Dew Point">
                                </div>
                            </div> --}}
                            <div class="col-12 col-lg-3 mb-3">
                                <label for="defaultInput" class="form-label">Pre Filter</label>
                                <div class="input-group input-group-merge">
                                    <div class="input-group input-group-merge">
                                        <select name="pre" class="form-select offDisable"
                                            @disabled(@$weekly->condition == 'off')>
                                            <option value="Oke" @selected(old('pre', @$weekly->pre) == 'Oke')>Oke</option>
                                            <option value="Change" @selected(old('pre', @$weekly->pre) == 'Change')>Change</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 mb-3">
                                <label for="defaultInput" class="form-label">After Filter</label>
                                <div class="input-group input-group-merge">
                                    <select name="after" class="form-select offDisable" @disabled(@$weekly->condition == 'off')>
                                        <option value="Oke" @selected(old('after', @$weekly->after) == 'Oke')>Oke</option>
                                        <option value="Change" @selected(old('after', @$weekly->after) == 'Change')>Change</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 mb-3">
                                <label for="defaultInput" class="form-label">Auto Drain</label>
                                <div class="input-group input-group-merge">
                                    <select name="drain" class="form-select offDisable" @disabled(@$weekly->condition == 'off')>
                                        <option value="Oke" @selected(old('drain', @$weekly->drain) == 'Oke')>Oke</option>
                                        <option value="Not Oke" @selected(old('drain', @$weekly->drain) == 'Not Oke')>Not Oke</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 mb-2">
                                <div class="row">
                                    <div class="col-6 col-lg-12">
                                        <label for="defaultInput" class="form-label">Cleaning Kisi Kisi Kondensor</label>
                                    </div>
                                    <div class="col-6 col-lg-12">
                                        <input class="form-check-input" type="checkbox" name="condensor" value="1"
                                            id="defaultCheck1" @checked(old('cooler', @$weekly->condensor) == 1)>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="form-floating form-floating-outline mb-3">
                    <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="desc"
                        placeholder="Comments here...">{{ old('desc', @$weekly->desc) }}</textarea>
                    <label for="exampleFormControlTextarea1">Desc</label>
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
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
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
