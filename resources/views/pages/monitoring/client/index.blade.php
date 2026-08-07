@extends('layouts.sales.app')
@section('title', 'Monitoring machine')
@section('content')
    <div class="row mb-4">
        <div class="col">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h3>Daily</h3>
                        <h4>{{ $allPlantMonitoring }}/ <span class="text-muted fs-3">{{ $allPlant }}</span></h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-alpha-a-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0 fw-semibold">
                                            Plant BM 1-2
                                        </h6>
                                        <p class="fs-5">{{ $PM12Monitoring }}/ <span
                                                class="text-muted fs-6">{{ $PM12 }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-alpha-b-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0 fw-semibold">
                                            Plant BM 3-5
                                        </h6>
                                        <p class="fs-5">{{ $PM35Monitoring }}/ <span
                                                class="text-muted fs-6">{{ $PM35 }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-alpha-c-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0 fw-semibold">
                                            Plant BM 7-8
                                        </h6>
                                        <p class="fs-5">{{ $PM78Monitoring }}/ <span
                                                class="text-muted fs-6">{{ $PM78 }}</span></p>
                                    </div>
                                </div>
                            </li>
                        </div>
                        <div class="col-6">
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-alpha-d-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0 fw-semibold">
                                            GT 3 / BOILER
                                        </h6>
                                        <p class="fs-5">{{ $GT3Monitoring }}/ <span
                                                class="text-muted fs-6">{{ $GT3 }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-alpha-e-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0 fw-semibold">
                                            Plant GT 1-2
                                        </h6>
                                        <p class="fs-5">{{ $GTMonitoring }}/ <span
                                                class="text-muted fs-6">{{ $GT }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-alpha-f-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0 fw-semibold">
                                            Plant INC
                                        </h6>
                                        <p class="fs-5">{{ $INCMonitoring }}/ <span
                                                class="text-muted fs-6">{{ $INC }}</span></p>
                                    </div>
                                </div>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3>Weekly Juni</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-numeric-1-circle-outline"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Week 1
                                        </a>
                                        <p class="fs-5">{{ $weekly1April }}/ <span
                                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-numeric-2-circle-outline"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Week 2
                                        </a>
                                        <p class="fs-5">{{ $weekly2April }}/ <span
                                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-numeric-3-circle-outline"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Week 3
                                        </a>
                                        <p class="fs-5">{{ $weekly3April }}/ <span
                                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                                    </div>
                                </div>
                            </li>
                        </div>
                        <div class="col-6">
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-numeric-4-circle-outline"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Week 4
                                        </a>
                                        <p class="fs-5">{{ $weekly4April }}/ <span
                                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-numeric-5-circle-outline"></i>
                                            <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Week 5
                                        </a>
                                        <p class="fs-5">{{ $weekly5April }}/ <span
                                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-danger rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-file-document-outline"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Service Reports
                                        </a>
                                        <p class="fs-5">{{ $cleaningApril }}/ <span
                                                class="text-muted fs-6">240</span></p>
                                    </div>
                                </div>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h3>Weekly</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-numeric-1-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Week 1
                                        </a>
                                        <p class="fs-5">{{ $weekly1 }}/ <span
                                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-numeric-2-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Week 2
                                        </a>
                                        <p class="fs-5">{{ $weekly2 }}/ <span
                                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-numeric-3-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Week 3
                                        </a>
                                        <p class="fs-5">{{ $weekly3 }}/ <span
                                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                                    </div>
                                </div>
                            </li>
                        </div>
                        <div class="col-6">
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-numeric-4-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Week 4
                                        </a>
                                        <p class="fs-5">{{ $weekly4 }}/ <span
                                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-numeric-5-circle-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Week 5
                                        </a>
                                        <p class="fs-5">{{ $weekly5 }}/ <span
                                                class="text-muted fs-6">{{ $allPlant }}</span></p>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <div class="avatar-initial bg-label-danger rounded">
                                        <div>
                                            <i class="mdi mdi-48px mdi-file-document-outline"></i>
                                            {{-- <img src="../../assets/img/icons/payments/credit-card.png" alt="credit-card"> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <a href="{{ route('monitoring.fajarPaper-detail-weekly') }}"
                                            class="mb-0 fw-semibold fs-6 fw-medium text-black">
                                            Service Reports
                                        </a>
                                        <p class="fs-5">{{ $cleaning }}/ <span class="text-muted fs-6">240</span>
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <h1 class="text-black">{{ $monthly }}/ <span class="text-muted fs-3">{{ $allDryer }}</span>
                    </h1>
                    <h5>Monthly</h5>
                </div>
            </div>
        </div>

        {{-- <div class="col-12 col-md-3 mb-2">
            <div class="card bg-label-success h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <h1 class="text-black">{{ $allPlantMonitoring }}/ <span
                            class="text-muted fs-3">{{ $allPlant }}</span>
                    </h1>
                    <h5>Data Input Daily</h5>
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
        </div> --}}
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5> Machine </h5>
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-compressor-client table table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Brand</th>
                                    <th>Unit</th>
                                    <th>Tag</th>
                                    <th>Location</th>
                                    <th>PIC</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5> Issue Daily </h5>
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-issue-monitoring table table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Tag</th>
                                    <th>Model / Type</th>
                                    <th>Issue</th>
                                    <th>Pic</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="row">

        <div class="col-12 col-md-6">
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
        </div>
        <div class="col-12 col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5> Recap Weekly </h5>
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-recap-month-week table table-striped">
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
    </div>
    @foreach ($issued as $monitor)
        @include('components.modal.monitoring.client.issue')
    @endforeach
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
    <script src="{{ asset('assets') }}/includes/table-client-daily.js"></script>
    <script src="{{ asset('assets') }}/includes/table-issue-client-monitoring.js"></script>
    <script src="{{ asset('assets') }}/includes/table-recap-month-week.js"></script>
    <script src="{{ asset('assets') }}/includes/table-recap-month.js"></script>
    <script src="{{ asset('assets') }}/includes/table-issue-month.js"></script>
@endpush

@push('script')
@endpush
