@extends('layouts.sales.app')
@section('title', 'AP Aging Report')
@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Account Payable /</span> Aging Report
            </h4>
            <p class="text-muted mb-0 small"><i class="mdi mdi-calendar-clock-outline me-1"></i> Umur hutang ke supplier berdasarkan tanggal barang masuk yang belum dibayar</p>
        </div>
    </div>

    {{-- Total Outstanding --}}
    <div class="card mb-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%); border-left: 5px solid #696cff !important;">
        <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <label class="form-label text-uppercase fw-bold text-primary small mb-1" style="letter-spacing: .5px;">
                    <i class="mdi mdi-cash-multiple me-1"></i> Total Outstanding (Belum Dibayar)
                </label>
                <div class="fw-bold text-primary" style="font-size: 1.75rem;">Rp {{ number_format($unpaid->sum('total'), 0, ',', '.') }}</div>
            </div>
            <span class="badge bg-label-secondary px-3 py-2 fs-6 rounded-pill">{{ $unpaid->count() }} Transaksi</span>
        </div>
    </div>

    {{-- Aging buckets --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid #28a745 !important;">
                <div class="card-body">
                    <div class="text-muted small mb-1">Current (0-30 Hari)</div>
                    <div class="fw-bold fs-5 text-success">Rp {{ number_format($bucketCurrent->sum('total'), 0, ',', '.') }}</div>
                    <div class="text-muted small">{{ $bucketCurrent->count() }} transaksi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="text-muted small mb-1">31-60 Hari</div>
                    <div class="fw-bold fs-5" style="color:#c79100;">Rp {{ number_format($bucket31to60->sum('total'), 0, ',', '.') }}</div>
                    <div class="text-muted small">{{ $bucket31to60->count() }} transaksi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid #fd7e14 !important;">
                <div class="card-body">
                    <div class="text-muted small mb-1">61-90 Hari</div>
                    <div class="fw-bold fs-5" style="color:#fd7e14;">Rp {{ number_format($bucket61to90->sum('total'), 0, ',', '.') }}</div>
                    <div class="text-muted small">{{ $bucket61to90->count() }} transaksi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid #dc3545 !important;">
                <div class="card-body">
                    <div class="text-muted small mb-1">90+ Hari</div>
                    <div class="fw-bold fs-5 text-danger">Rp {{ number_format($bucket90plus->sum('total'), 0, ',', '.') }}</div>
                    <div class="text-muted small">{{ $bucket90plus->count() }} transaksi</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center">
            <h6 class="card-title mb-0 fw-bold text-dark">
                <i class="mdi mdi-format-list-bulleted me-2 text-primary fs-5"></i> Daftar Hutang Belum Dibayar
            </h6>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-sales-aging-ap table table-bordered">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
                        <th>Date</th>
                        <th>Days Outstanding</th>
                        <th>Aging Bucket</th>
                        <th>Suppliers</th>
                        <th>Total Invoice</th>
                        <th>Total Item</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
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
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ap-aging.js"></script>
@endpush

@push('script')
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(document).on('click', '.delete-payable', function() {
            var id = $(this).data('id');
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
                        'url': '{{ url('payable-acount') }}/' + id,
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
                                    window.location.href = '/expense-account';
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
