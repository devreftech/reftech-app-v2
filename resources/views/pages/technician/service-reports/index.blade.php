@extends('layouts.sales.app')
@section('title', 'Reports Management')
@section('content')
    @if (Auth::user()->role == 'Guest')
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Project Management /</span> Daily Project Reports
        </h4>
    @else
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Service Department /</span> Reports Management
        </h4>
    @endif

    <div class="nav-align-top mb-4">
        @if (Auth::user()->role != 'Guest')
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link {{ request('tab') == 'project' ? '' : 'active' }}" role="tab"
                        data-bs-toggle="tab" data-bs-target="#tab-service-reports" aria-controls="tab-service-reports"
                        aria-selected="{{ request('tab') == 'project' ? 'false' : 'true' }}">
                        <i class="tf-icons mdi mdi-wrench me-1"></i> Service Reports
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link {{ request('tab') == 'project' ? 'active' : '' }}" role="tab"
                        data-bs-toggle="tab" data-bs-target="#tab-project-reports" aria-controls="tab-project-reports"
                        aria-selected="{{ request('tab') == 'project' ? 'true' : 'false' }}">
                        <i class="tf-icons mdi mdi-briefcase-outline me-1"></i> Project Reports (Daily Report)
                    </button>
                </li>
            </ul>
        @endif

        <div class="tab-content px-0 pb-0 {{ Auth::user()->role == 'Guest' ? 'pt-0' : 'pt-3' }} bg-transparent shadow-none">
            @if (Auth::user()->role != 'Guest')
                {{-- TAB 1: Service Reports --}}
                <div class="tab-pane fade {{ request('tab') == 'project' ? '' : 'show active' }}" id="tab-service-reports"
                    role="tabpanel">
                @if (Auth::user()->role == 'Technician' || Auth::user()->role == 'Coordinator')
                    <div class="card mb-3">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-reports table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>No Service</th>
                                        <th>Company</th>
                                        <th>Job Desc</th>
                                        <th>Unit Type</th>
                                        <th>Serial / Tag</th>
                                        <th class="text-nowrap">Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @elseif(Auth::user()->role == 'Admin' || Auth::user()->role == 'Sales Manager')
                    <div class="card mb-3">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-reports-admin table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">No Service</th>
                                        <th class="text-center">Company</th>
                                        <th class="text-center">Job Desc</th>
                                        <th class="text-center">Brand Type</th>
                                        <th class="text-center">Serial / Tag</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Sales</th>
                                        <th class="text-center">Technician</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="card mb-3">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-reports-sales table table-bordered">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>ID</th>
                                        <th>No Service</th>
                                        <th>Company</th>
                                        <th>Job Desc</th>
                                        <th>Unit Type</th>
                                        <th>Serial / Tag</th>
                                        <th>Date</th>
                                        <th>Technician</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            @endif

            {{-- TAB 2: Project Reports (Daily Report Form) --}}
            <div class="tab-pane fade {{ request('tab') == 'project' ? 'show active' : '' }}" id="tab-project-reports"
                role="tabpanel">
                <div class="card mb-3">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatable-project-reports table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th>Nama Pekerjaan / Kontrak</th>
                                    <th>Tanggal & Hari</th>
                                    <th>Dibuat Oleh</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 120px;">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
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
    @if (Auth::user()->role != 'Guest')
        <script src="{{ asset('assets') }}/includes/table-reports.js"></script>
        <script src="{{ asset('assets') }}/includes/table-reports-admin.js"></script>
        <script src="{{ asset('assets') }}/includes/table-reports-sales.js"></script>
    @endif
    <script src="{{ asset('assets') }}/includes/table-project-reports.js"></script>
@endpush

@push('script')
    <script>
        $(document).on('click', '.view-report', function() {
            let id = $(this).data('id');

            $.ajax({
                url: "/service-reports-viewed",
                type: "POST",
                data: {
                    id: id,
                    _token: "{{ csrf_token() }}"
                },
                success: function() {
                    window.location.href = "/service-reports/" + id;
                }
            });
        });

        // Tab state handling via hash or URL param
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    </script>
@endpush
