@extends('layouts.sales.app')
@section('title', 'Detail Suppliers')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y p-0">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Supplier Details</h4>
                <p class="text-muted mb-0 small">Informasi detail supplier dan riwayat transaksi invoice barang masuk</p>
            </div>
            <div class="d-flex gap-2">
                <a type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateSupplier-{{ $supplier->id }}">
                    <i class="mdi mdi-pencil-outline me-1"></i> Edit Supplier
                </a>
                <a href="#" data-id="{{ $supplier->id }}" class="btn btn-outline-danger btn-sm delete-supplier">
                    <i class="mdi mdi-delete-outline me-1"></i> Delete
                </a>
            </div>
        </div>

        {{-- Supplier Info & PIC Row --}}
        <div class="row g-4 mb-4 supplier-company-pic-row">
            {{-- Supplier Info Card (65%) --}}
            <div class="col-12 supplier-col-company">
                <div class="card modern-card h-100">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-domain me-2 text-primary fs-4"></i> {{ $supplier->supplier }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6 border-end-md">
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0" style="width: 120px;">Address</td>
                                        <td>: <span class="fw-medium text-dark">{{ $supplier->address ?: '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Area</td>
                                        <td>: <span class="fw-medium text-dark">{{ $supplier->area ?: '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Phone</td>
                                        <td>: <span class="fw-medium text-dark">{{ $supplier->phone ?: '-' }}</span></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0" style="width: 120px;">Email</td>
                                        <td>: <span class="fw-medium text-primary">{{ $supplier->email ?: '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">NPWP</td>
                                        <td>: <span class="fw-medium text-dark">{{ $supplier->npwp ?: '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-semibold text-muted ps-0">Info</td>
                                        <td>: <span class="fw-medium text-dark">{{ $supplier->info ?: '-' }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PIC Card (35%) --}}
            <div class="col-12 supplier-col-pic">
                <div class="card modern-card h-100">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="mdi mdi-account-tie-outline me-2 text-primary fs-4"></i> PIC Supplier
                        </h5>
                        <a type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createSupplierPic">
                            <i class="mdi mdi-plus"></i>
                        </a>
                    </div>
                    <div class="card-body p-4">
                        @forelse ($pics as $pic)
                            <div class="d-flex justify-content-between align-items-start py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $pic->name_pic }}</div>
                                    <div class="text-muted small">{{ $pic->position ?: '-' }}</div>
                                    <div class="text-muted small">{{ $pic->phone_pic ?: '-' }}</div>
                                    <div class="text-muted small">{{ $pic->email_pic ?: '-' }}</div>
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    <a type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal"
                                        data-bs-target="#updateSupplierPic-{{ $pic->id }}">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </a>
                                    <a href="#" data-id="{{ $pic->id }}" class="btn btn-sm btn-label-danger delete-supplier-pic">
                                        <i class="mdi mdi-delete-outline"></i>
                                    </a>
                                </div>
                            </div>
                            @include('components.modal.warehouse.supplier.pic-update')
                        @empty
                            <p class="text-muted mb-0">Belum ada PIC untuk supplier ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @include('components.modal.warehouse.supplier.pic-create')

        {{-- Total Order Card --}}
        <div class="card modern-card mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="mdi mdi-chart-bar me-2 text-primary fs-4"></i> Total Order
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 align-items-center">
                    <div class="col-12 col-md-3">
                        <div class="text-muted small mb-1">Total Order {{ $currentYear }}</div>
                        <div class="fw-bold text-dark" style="font-size: 1.75rem;">
                            Rp {{ number_format($currentYearTotal, 0, ',', '.') }}
                        </div>
                        <div class="text-muted small mt-2">Dari invoice barang masuk supplier ini sepanjang tahun berjalan.</div>
                    </div>
                    <div class="col-12 col-md-9">
                        <div id="supplierYearlyOrderChart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Invoice History Table Card --}}
        <div class="card modern-card mb-3">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="mdi mdi-history me-2 text-primary fs-4"></i> Invoice History
                </h5>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product-in-supplier table table-bordered">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Invoice</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>VAT</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    @include('components.modal.warehouse.supplier.form')
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
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/apex-charts/apex-charts.css" />
    <style>
        @media (min-width: 768px) {
            .supplier-company-pic-row {
                flex-wrap: nowrap !important;
            }

            .supplier-col-company {
                flex: 0 0 65% !important;
                max-width: 65% !important;
                width: 65% !important;
            }

            .supplier-col-pic {
                flex: 0 0 35% !important;
                max-width: 35% !important;
                width: 35% !important;
            }
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-in-supplier.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
    <script>
        (function () {
            const isDark = document.documentElement.classList.contains('dark-style');
            const labelColor = isDark ? '#a8aaae' : '#6d6b77';
            const borderColor = isDark ? '#404152' : '#dbdade';

            const formatRp = val => {
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'B';
                if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'M';
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            const yearlyLabels = @json($yearlyLabels);
            const yearlyTotals = @json($yearlyTotals);

            const chartEl = document.querySelector('#supplierYearlyOrderChart');
            if (chartEl) {
                new ApexCharts(chartEl, {
                    chart: { type: 'bar', height: 220, toolbar: { show: false } },
                    series: [{ name: 'Total Order', data: yearlyTotals }],
                    colors: ['#696cff'],
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } },
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: yearlyLabels,
                        labels: { style: { colors: labelColor, fontSize: '13px' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    yaxis: { labels: { formatter: formatRp, style: { colors: labelColor, fontSize: '11px' } } },
                    grid: { borderColor, strokeDashArray: 5 },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }
        })();
    </script>
@endpush
@push('script')
    <script>
        $(document).on('click', '.delete-supplier-pic', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('supplier/pic') }}/' + id,
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
                                    text: "PIC berhasil dihapus.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    location.reload();
                                }, 1500);
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
                        text: "Your Delete is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });

        $(document).on('click', '.delete-supplier', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('supplier') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleteed!",
                                    text: "Your file has been Deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/supplier/';
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
                        text: "Your Delete is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
            // Swal.fire({
            //     title: "Are you sure?",
            //     text: "You won't be able to revert this!",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonColor: "#3085d6",
            //     cancelButtonColor: "#d33",
            //     confirmButtonText: "Yes, delete it!"
            // }).then((result) => {
            //     if (result.isConfirmed) {
            //         $.ajax({
            //             'url': '{{ url('leads') }}/' + id,
            //             'type': 'POST',
            //             'data': {
            //                 '_method': 'DELETE',
            //                 '_token': '{{ csrf_token() }}'
            //             },
            //             success: function(response) {
            //                 if (response == 1) {
            //                     Swal.fire({
            //                         title: "Deleted!",
            //                         text: "Your file has been deleted.",
            //                         icon: "success"
            //                     })
            //                     window.setTimeout(function() {
            //                         location.reload();
            //                     }, 2000);
            //                 } else {
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: 'Oops...',
            //                         text: 'Data Failed to Delete!'
            //                     });
            //                 }
            //             }
            //         });
            //     }
            // });
        });
    </script>
@endpush
