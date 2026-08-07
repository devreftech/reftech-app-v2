@extends('layouts.sales.app')
@section('title', 'Fixed Asset')
@section('content')
    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Finance /</span> Fixed Asset
    </h4>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('fixed.create') }}" class="btn btn-primary btn-sm">
            <i class="mdi mdi-plus"></i> New Fixed Asset
        </a>
    </div>

    <div class="card">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="fixed-asset-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-tanah" type="button">
                        <i class="mdi mdi-terrain me-1"></i>Tanah
                        <span class="badge rounded-pill bg-primary ms-1" id="badge-tanah">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bangunan" type="button">
                        <i class="mdi mdi-office-building me-1"></i>Bangunan
                        <span class="badge rounded-pill bg-primary ms-1" id="badge-bangunan">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-kendaraan" type="button">
                        <i class="mdi mdi-car me-1"></i>Kendaraan
                        <span class="badge rounded-pill bg-primary ms-1" id="badge-kendaraan">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-mesin" type="button">
                        <i class="mdi mdi-cog-outline me-1"></i>Mesin
                        <span class="badge rounded-pill bg-primary ms-1" id="badge-mesin">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-peralatan" type="button">
                        <i class="mdi mdi-desktop-classic me-1"></i>Peralatan Kantor
                        <span class="badge rounded-pill bg-primary ms-1" id="badge-peralatan">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tools" type="button">
                        <i class="mdi mdi-toolbox-outline me-1"></i>Tools
                        <span class="badge rounded-pill bg-primary ms-1" id="badge-tools">-</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content">
                {{-- Tanah --}}
                <div class="tab-pane fade show active" id="tab-tanah">
                    <div class="table-responsive">
                        <table class="datatable-fixed-generic table table-bordered" data-type="Tanah"
                            data-badge="badge-tanah">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Keterangan</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Tgl Beli</th>
                                    <th>Tgl Pakai</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Bangunan --}}
                <div class="tab-pane fade" id="tab-bangunan">
                    <div class="table-responsive">
                        <table class="datatable-fixed-generic table table-bordered" data-type="Bangunan"
                            data-badge="badge-bangunan">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Keterangan</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Tgl Beli</th>
                                    <th>Tgl Pakai</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Kendaraan --}}
                <div class="tab-pane fade" id="tab-kendaraan">
                    <div class="table-responsive">
                        <table class="datatable-fixed-kendaraan table table-bordered" data-badge="badge-kendaraan">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Jenis Kendaraan</th>
                                    <th>Merk/Model</th>
                                    <th>Plat Nomor</th>
                                    <th>Atas Nama</th>
                                    <th>Tgl Beli</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Mesin --}}
                <div class="tab-pane fade" id="tab-mesin">
                    <div class="table-responsive">
                        <table class="datatable-fixed-mesin table table-bordered" data-badge="badge-mesin">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Brand</th>
                                    <th>Type</th>
                                    <th>SN</th>
                                    <th>Kondisi</th>
                                    <th>Tgl Beli</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Peralatan Kantor --}}
                <div class="tab-pane fade" id="tab-peralatan">
                    <div class="table-responsive">
                        <table class="datatable-fixed-generic table table-bordered" data-type="Peralatan Kantor"
                            data-badge="badge-peralatan">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Keterangan</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Tgl Beli</th>
                                    <th>Tgl Pakai</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Tools --}}
                <div class="tab-pane fade" id="tab-tools">
                    <div class="table-responsive">
                        <table class="datatable-fixed-tools table table-bordered" data-badge="badge-tools">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Nama Tools</th>
                                    <th>Teknisi</th>
                                    <th>Qty</th>
                                    <th>Tgl Serah Terima</th>
                                    <th>Status Finance</th>
                                    <th>Harga</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.modal.finance.income')
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
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/includes/table-fixed-asset.js"></script>
@endpush

@push('script')
    <script>
        const yearSelect = document.getElementById('yearSelect');
        const currentYear = new Date().getFullYear();

        // generate year -5 sampai +5
        for (let y = currentYear - 5; y <= currentYear + 5; y++) {
            const option = document.createElement('option');
            option.value = y;
            option.textContent = y;
            if (y === currentYear) option.selected = true;
            yearSelect.appendChild(option);
        }

        yearSelect.addEventListener('change', function() {
            if (!this.value) return;

            window.open(`/income-print/${this.value}`, '_blank');
        });

        document.getElementById('html5-month-input').addEventListener('change', function() {
            if (!this.value) return;

            const [year, month] = this.value.split('-');

            window.open(`/income-print/${month}/${year}`, '_blank');
        });

        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        $(".invoice-item-price-label").on('keyup', function() {
            var input = $(this)
            var input_val = input.val();

            // original length
            var original_len = input_val.length;

            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // send updated string to input
            input.val(input_val);
            var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
            console.log(nomorInt);
            $(`#pricy`).val(nomorInt);
        });

        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Activate tab based on URL query parameter 'type'
            const urlParams = new URLSearchParams(window.location.search);
            const tabType = urlParams.get('type');
            if (tabType) {
                let targetTabId = '';
                if (tabType === 'Tanah') targetTabId = '#tab-tanah';
                else if (tabType === 'Bangunan') targetTabId = '#tab-bangunan';
                else if (tabType === 'Kendaraan') targetTabId = '#tab-kendaraan';
                else if (tabType === 'Mesin') targetTabId = '#tab-mesin';
                else if (tabType === 'Peralatan Kantor') targetTabId = '#tab-peralatan';
                else if (tabType === 'Tools') targetTabId = '#tab-tools';

                if (targetTabId) {
                    const tabButton = $(`#fixed-asset-tab-nav button[data-bs-target="${targetTabId}"]`);
                    if (tabButton.length) {
                        tabButton.click();
                    }
                }
            }
        });

        $(document).on('click', '.delete-expense', function() {
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
                        'url': '{{ url('expense-account') }}/' + id,
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
