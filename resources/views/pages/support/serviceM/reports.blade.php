@extends('layouts.sales.app')
@section('title', 'Service Reports')
@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Service Department /</span> Service Reports
        </h4>
        <a href="{{ route('service-reports.create') }}" class="btn btn-primary shadow-sm">
            <i class="mdi mdi-plus me-1"></i> Buat Service Report
        </a>
    </div>
    <div class="card mb-4 border-warning">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">
                <i class="mdi mdi-clipboard-check-outline me-1"></i>Pending Approval
            </h5>
            <span class="badge bg-warning rounded-pill">
                {{ $pendingReports->where('approval_status', 'pending')->count() }} menunggu
            </span>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th class="text-center">No Service</th>
                        <th class="text-center">Company</th>
                        <th class="text-center">Job Desc</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Technician</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingReports as $r)
                        <tr>
                            <td class="text-nowrap">
                                <a class="fw-bold text-primary" href="{{ route('service-reports.show', $r->id) }}">
                                    {{ $r->no_service }}
                                </a>
                            </td>
                            <td>{{ optional(optional($r->pic)->client)->company ?? '-' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($r->jobdesc, 60) }}</td>
                            <td class="text-center text-nowrap">{{ \Carbon\Carbon::parse($r->date)->format('d-m-Y') }}</td>
                            <td>{{ optional($r->technician)->name ?? '-' }}</td>
                            <td class="text-center">
                                @if ($r->approval_status === 'rejected')
                                    <span class="badge bg-label-danger" data-bs-toggle="tooltip"
                                        title="{{ $r->reject_note }}">Ditolak</span>
                                @else
                                    <span class="badge bg-label-warning">Belum Dicek</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Tidak ada report yang menunggu approval.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
    <script src="{{ asset('assets') }}/includes/table-reports-admin.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: @json(session('success')),
                    customClass: { confirmButton: 'btn btn-success waves-effect' },
                    buttonsStyling: false,
                });
            @endif
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: @json($errors->first()),
                    customClass: { confirmButton: 'btn btn-danger waves-effect' },
                    buttonsStyling: false,
                });
            @endif

            $(document).on('click', '.accept-issue', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, Accept it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('monitoring-client') }}/accept-issue/' + id,
                            'type': 'POST',
                            'data': {
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Accepted!",
                                        text: "Your file has been Accepted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href = '/';
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Accept!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "You Cancel Accept :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });

        });
    </script>
@endpush
