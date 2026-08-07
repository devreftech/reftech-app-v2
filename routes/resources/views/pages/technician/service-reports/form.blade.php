@extends('layouts.sales.app')
@section('title', 'Create Service Reports')
@section('content')
    <form action="{{ @$report ? route('service-reports.update', @$report->id) : route('service-reports.store') }}"
        method="post" enctype="multipart/form-data" id="serviceReports" name="service-reports">
        @csrf
        @if (@$report)
            @method('PATCH')
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Form Service Reports</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <div class="form-floating mb-3">
                            @if (@$report)
                                <input type="text" class="form-control fw-bold fs-3" id="floatingInputFilled"
                                    aria-describedby="floatingInputFilledHelp" name="no_service" placeholder="No Service"
                                    value="{{ $report->no_service }}">
                            @else
                                <input type="text" class="form-control fw-bold fs-3" id="floatingInputFilled"
                                    aria-describedby="floatingInputFilledHelp" name="no_service" placeholder="No Service"
                                    value="{{ $formattedNumberS . '-S/RJO-' . Auth::user()->code . '/' . $formattedMonthNow . '/' . \Carbon\Carbon::now()->year }}">
                            @endif
                            <label for="floatingInputFilled">Number Service</label>
                            <span class="form-floating-focused"></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        {{-- <div class="form-floating form-floating-outline">
                            <select class="select2 form-select form-select-lg invoice-item-pic" data-allow-clear="true"
                                name="id_pic" id="selectPic">
                                <option selected>----- Select Company | Pic || Sales -----</option>
                                @foreach ($pic as $charge)
                                    <option data-id="{{ $charge->client->id }}" value="{{ $charge->id }}"
                                        {{ @$report->id_pic == $charge->id ? 'selected' : '' }}>
                                        {{ $charge->client->company }} | {{ $charge->name_pic }} ||
                                        {{ $charge->client->sales->name }}</option>
                                @endforeach
                            </select>
                            <label for="select2Basic">Client</label>
                        </div> --}}
                        <div class="form-floating form-floating-outline">
                            <select class="select2 form-select form-select-lg invoice-item-sales" data-allow-clear="true"
                                name="id_sales" id="selectSales" {{ @$reports ? 'disabled' : '' }}>
                                <option selected>----- Select Company Sales -----</option>
                                @foreach ($sales as $sale)
                                    <option data-id="{{ $sale->id }}" value="{{ $sale->id }}"
                                        {{ (isset($selectedSalesId) && $selectedSalesId == $sale->id) || (@$report && @$report->pic->client->sales->id == $sale->id) ? 'selected' : '' }}>
                                        {{ $sale->name }}</option>
                                @endforeach
                            </select>
                            <label for="select2Basic">Sales</label>
                        </div>
                        <input type="text" name="technician" id="" value="{{ Auth::user()->id }}" hidden>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline mb-2">
                            <select id="client-dropdown" class="select2 form-select invoice-item-client" data-id="1"
                                data-allow-clear="true" name="client" disabled>
                                <option> ---- Choose Client Here ---- </option>
                                @if (@$report)
                                    <option data-id="{{ $report->pic->client->id }}" value="{{ $report->pic->client->id }}"
                                        selected>
                                        {{ $report->pic->client->company }}</option>
                                @endif
                            </select>
                            <label for="client-dropdown">Client</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline mb-2">
                            <select id="pic-dropdown" class="select2 form-select invoice-item-pic" data-id="1"
                                data-allow-clear="true" name="id_pic" disabled>
                                <option> ---- Choose PIC Here ---- </option>
                                @if (@$report)
                                    <option data-id="{{ $report->pic->id }}" value="{{ $report->pic->id }}" selected>
                                        {{ $report->pic->name_pic }}</option>
                                @endif
                            </select>
                            <label for="pic-dropdown">PIC</label>
                        </div>
                    </div>
                    <div class="col-12 col-md mb-3">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example"
                                name="type">
                                <option selected="" disabled>---- Choose Service Type ----</option>
                                <option value="Visit" {{ @$report->type == 'Visit' ? 'Selected' : '' }}>Visit</option>
                                <option value="Service" {{ @$report->type == 'Service' ? 'Selected' : '' }}>Service
                                </option>
                                <option value="General" {{ @$report->type == 'General' ? 'Selected' : '' }}>General Check
                                </option>
                                <option value="Rental" {{ @$report->type == 'Rental' ? 'Selected' : '' }}>Rental</option>
                                <option value="Cleaning" {{ @$report->type == 'Cleaning' ? 'Selected' : '' }}>Cleaning
                                </option>
                            </select>
                            <label for="exampleFormControlSelect1">Service Type</label>
                        </div>
                    </div>
                    <div class="col-12 col-md mb-3">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="date" name="date" id="date"
                                value="{{ $report->date ?? now()->format('Y-m-d') }}">
                            {{-- <input type="date" name="date" id="date" value="{{ now()->format('Y-m-d') }}"
                                hidden> --}}
                            <label for="date">Date</label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <div class="form-floating form-floating-outline mb-2">
                            <select id="machine-dropdown" class="select2 form-select invoice-item-machine" data-id="1"
                                data-allow-clear="true" name="machine" disabled>
                                <option> ---- Choose Machine Here ---- </option>
                                @if (@$report)
                                    <option data-id="{{ $report->machine->id }}" value="{{ $report->machine->id }}"
                                        selected>
                                        {{ $report->machine->unit->brand }} {{ $report->machine->unit->pn }} ||
                                        {{ $report->machine->location }} - {{ $report->machine->tag }} -
                                        {{ $report->machine->serial }}
                                    </option>
                                @endif
                            </select>
                            <label for="machine-dropdown">Machine</label>
                        </div>
                    </div>
                    {{-- <div class="col-4 mb-3">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#createMachine">
                            <button type="button" class="btn btn-primary btn-lg">
                                +
                            </button>
                        </a>
                    </div> --}}
                    <div class="col-12 col-md-6">

                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="running" name="running"
                                placeholder="Type Running Here..." value="{{ old('running', @$report->running ?? '') }}">
                            <label for="basic-default-fullname">Running</label>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control input-numeric" id="load" name="load"
                                placeholder="Type Load Here..." value="{{ old('load', @$report->load ?? '') }}">
                            <label for="basic-default-fullname">Load</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="jobdesc" name="jobdesc"
                                placeholder="Type Job Description Type Here ...."
                                value="{{ old('jobdesc', @$report->jobdesc ?? '') }}">
                            <label for="basic-default-fullname">Job Description</label>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="description" name="desc" placeholder="Description here..."
                                style="min-height: 100px;" value="{{ old('desc') }}">{{ @$report->desc ?? '' }}</textarea>
                            <label for="description">Description</label>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="recomendation" name="recomendation" placeholder="Recomendation here..."
                                style="min-height: 100px;" value="{{ old('recomendation') }}">{{ @$report->recomendation ?? '' }}</textarea>
                            <label for="recomendation">Recomendation</label>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">Save changes</button>
                        <a href="{{ route('service-reports.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @include('components.modal.machine.form-technician')

@endsection
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        #image-preview img {
            max-width: 150px;
            margin-left: 16px;
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush
@push('script')
    <script>
        function initNumericInput() {
            var input = $('.input-numeric')
            for (var i = 0; i < input.length; i++) {
                input[i].addEventListener('input', function() {
                    // Hapus karakter selain angka
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }
        }
        $(document).ready(function() {
            var selectedMachineId = '{{ $selectedMachineId ?? $report->id_machine ?? '' }}';
            var selectedSalesId = '{{ $selectedSalesId ?? $report->pic->client->id_sales ?? '' }}';
            var selectedClientId = '{{ $selectedClientId ?? $report->pic->id_client ?? '' }}';
            var selectedPICId = '{{ $selectedPICId ?? $report->id_pic ?? '' }}';
            initNumericInput();
            $('#formFileMultiple').on('change', function() {
                var files = this.files;
                var dynamicInputsContainer = $('#dynamicInputsContainer');
                console.log(dynamicInputsContainer);

                dynamicInputsContainer.empty();

                for (var i = 0; i < files.length; i++) {
                    var dynamicInput =
                        '<input class="form-control mb-2" type="text" name="description[]" placeholder="Deskripsi untuk File ' +
                        (i +
                            1) + '">';
                    dynamicInputsContainer.append(dynamicInput);
                }

                if (files.length !== 3 && files.length !== 6 && files.length !== 9) {
                    alert('Gambar Wajib Kelipatan 3! 3/6/9 Maksimal 9');
                    this.value = ''; // Menghapus file yang tidak memenuhi syarat
                    dynamicInputsContainer.empty();
                }

                console.log(files);
                const previewContainer = document.getElementById('image-preview');
                previewContainer.innerHTML = '';

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imageContainer = document.createElement('div');
                        const imageElement = document.createElement('img');
                        const description = document.createElement('p');

                        imageContainer.className =
                            'image-container'; // Tambahkan kelas sesuai kebutuhan
                        imageElement.src = e.target.result;
                        description.textContent = 'Photo ' + (i + 1);

                        imageContainer.appendChild(imageElement);
                        imageContainer.appendChild(description);
                        previewContainer.appendChild(imageContainer);

                    };

                    reader.readAsDataURL(file);
                }
            });
            $('#selectSales').on('change', function() {
                var salesId = $(this).find(':selected').data('id');
                var Url = '/client/dropdown/' + salesId;

                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        var clientDropdown = $('#client-dropdown');
                        clientDropdown.empty();
                        clientDropdown.append(
                            '<option selected="" disabled> ---- Choose Client Here ---- </option>'
                        );

                        $.each(response, function(key, value) {
                            var option = $('<option></option>').attr('value', value.id)
                                .text(value.company);
                            clientDropdown.append(option);
                        });

                        clientDropdown.prop('disabled', false);

                        if (selectedClientId) {
                            clientDropdown.val(selectedClientId).trigger('change');
                        }
                    }
                });
            });

            $('#client-dropdown').on('change', function() {
                var clientId = $(this).find(':selected').val();
                var Url = '/pic/dropdown/' + clientId;

                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        var picDropdown = $('#pic-dropdown');
                        picDropdown.empty();
                        picDropdown.append(
                            '<option selected="" disabled> ---- Choose PIC Here ---- </option>'
                        );

                        $.each(response, function(key, value) {
                            var option = $('<option></option>').attr('value', value.id)
                                .text(value.name_pic);
                            picDropdown.append(option);
                        });

                        picDropdown.prop('disabled', false);

                        if (selectedPICId) {
                            picDropdown.val(selectedPICId).trigger('change');
                        }
                    }
                });
            });

            $('#pic-dropdown').on('change', function() {
                var clientId = $(this).find(':selected').val();
                var Url = '/machine/dropdown/' + clientId;

                $.ajax({
                    url: Url,
                    type: 'GET',
                    success: function(response) {
                        var machineDropdown = $('#machine-dropdown');
                        machineDropdown.empty();
                        machineDropdown.append(
                            '<option selected="" disabled> ---- Choose Machine Here ---- </option>'
                        );

                        $.each(response, function(key, value) {
                            var option = $('<option></option>').attr('value', value.id)
                                .text(value.brand + " " + value.sku +
                                    " || " + value.location + " - " + value.tag +
                                    " - " + value.serial);
                            machineDropdown.append(option);
                        });

                        machineDropdown.prop('disabled', false);

                        if (selectedMachineId) {
                            machineDropdown.val(selectedMachineId).trigger('change');
                        }
                    }
                });
            });

            // Trigger change event to pre-select dependent dropdowns in order
            if (selectedSalesId) {
                $('#selectSales').trigger('change');
            }

        });
    </script>
@endpush
