@extends('layouts.sales.app')
@section('title', 'Data Product')
@section('content')
    <div class="card mb-3">
        <div class="card-header">
            <h4>Correction Factor Calculator</h4>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-6 mb-3">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="dataIT" placeholder="Intake Temperature (c)"
                            aria-describedby="dataITHelp">
                        <label for="dataIT">Intake Temperature</label>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="dataIP" placeholder="Intake Pressure (c)"
                            aria-describedby="dataIPHelp">
                        <label for="dataIP">Intake Pressure</label>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="dataPDP"
                            placeholder="Pressure Dew Point (3/7/10 C)" aria-describedby="dataPDPHelp">
                        <label for="dataPDP">Pressure Dew Point</label>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="dataAT" placeholder="Ambient Temperature (c)"
                            aria-describedby="dataAtHelp">
                        <label for="dataAt">Ambient Temperature</label>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="form-floating form-floating-outline">
                        <input type="text" class="form-control" id="dataAC" placeholder="Air Capacity"
                            aria-describedby="dataAtHelp">
                        <label for="dataAt">Air Capacity</label>
                    </div>
                </div>
            </div>
            <div class="float-end mb-3">
                <div class="btn btn-primary calculate">
                    Calculate
                </div>
            </div>
            <input type="text" class="form-control form-control-lg result" id="result" placeholder="Results"
                aria-describedby="resultHelp" disabled>
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
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        $(document).on('click', '.calculate', function() {
            var dataIT, dataIP, dataPDP, dataAT, dataAC;

            dataIT = $('#dataIT').val();
            dataIP = $('#dataIP').val();
            dataPDP = $('#dataPDP').val();
            dataAT = $('#dataAT').val();
            dataAC = $('#dataAC').val();
            console.log("dataIT : " + dataIT);
            console.log("dataIP : " + dataIP);
            console.log("dataPDP : " + dataPDP);
            console.log("dataAT : " + dataAT);
            console.log("dataAC : " + dataAC);
            

            var cfIT, cfAP, cfPDP, cfAT;

            switch (true) {
                case (dataIT >= 32 && dataIT <= 33):
                    cfIT = 1.53;
                    break;
                case (dataIT >= 34 && dataIT <= 36):
                    cfIT = 1.39;
                    break;
                case (dataIT >= 37 && dataIT <= 39):
                    cfIT = 1.25;
                    break;
                case (dataIT >= 40 && dataIT <= 41):
                    cfIT = 1.2;
                    break;
                case (dataIT >= 42 && dataIT <= 43):
                    cfIT = 1.06;
                    break;
                case (dataIT >= 44 && dataIT <= 47):
                    cfIT = 1;
                    break;
                case (dataIT >= 48 && dataIT <= 52):
                    cfIT = 0.83;
                    break;
                case (dataIT >= 53 && dataIT <= 57):
                    cfIT = 0.68;
                    break;
                case (dataIT >= 58 && dataIT <= 65):
                    cfIT = 0.58;
                    break;
                default:
                    cfIT = 0;
            }

            switch (true) {
                case (dataIP == 4):
                    cfAP = 0.76;
                    break;
                case (dataIP == 5):
                    cfAP = 0.86;
                    break;
                case (dataIP == 6):
                    cfAP = 0.93;
                    break;
                case (dataIP == 7):
                    cfAP = 1;
                    break;
                case (dataIP == 8):
                    cfAP = 1.04;
                    break;
                case (dataIP == 9):
                    cfAP = 1.07;
                    break;
                case (dataIP == 10):
                    cfAP = 1.12;
                    break;
                default:
                    cfAP = 0;
            }

            switch (true) {
                case (dataPDP == 3):
                    cfPDP = 0.81;
                    break;
                case (dataPDP == 7):
                    cfPDP = 1;
                    break;
                case (dataPDP == 10):
                    cfPDP = 1.17;
                    break;
                default:
                    cfPDP = 0;
            }
            
            switch (true) {
                case (dataAT >= 25 && dataAT <= 28):
                    cfAT = 0.76;
                    break;
                case (dataAT >= 29 && dataAT <= 33):
                    cfAT = 0.86;
                    break;
                case (dataAT >= 34 && dataAT <= 36):
                    cfAT = 0.93;
                    break;
                case (dataAT >= 37 && dataAT <= 38):
                    cfAT = 1;
                    break;
                case (dataAT >= 39 && dataAT <= 42):
                    cfAT = 1.04;
                    break;
                case (dataAT >= 43 && dataAT <= 45):
                    cfAT = 1.07;
                    break;
                default:
                    cfAT = 0;
            }

            var resultTime = cfIT * cfPDP * cfAP * cfAT;
            console.log(resultTime);
            var finalResult = dataAC / resultTime;
            var formattedFinalResult = finalResult.toFixed(2);
            console.log("Hasilnya : " + finalResult);
            $('#result').val(formattedFinalResult);
            
        });
    </script>
@endpush
