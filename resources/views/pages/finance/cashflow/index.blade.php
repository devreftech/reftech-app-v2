@extends('layouts.sales.app')
@section('title', 'Cashflow Statement')
@section('content')
    <div class="py-3 mb-3">
        <h4 class="fw-bold mb-1">
            <span class="text-muted fw-light">Finance / Statement /</span> Cashflow Statement
        </h4>
        <p class="text-muted mb-0 small"><i class="mdi mdi-cash-sync me-1"></i> Laporan arus kas masuk &amp; keluar</p>
    </div>

    {{-- Ringkasan bulan & tahun berjalan, biar gak "blind pick" periode dulu --}}
    @foreach ([
        ['data' => $ringkasanBulan, 'label' => $ringkasanBulanLabel, 'badge' => 'Bulan Berjalan'],
        ['data' => $ringkasanTahun, 'label' => $ringkasanTahunLabel, 'badge' => 'Tahun Berjalan'],
    ] as $r)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="mdi mdi-calendar-month-outline me-2 text-primary fs-5"></i> Ringkasan {{ $r['label'] }}
                </h6>
                <span class="badge bg-label-secondary">{{ $r['badge'] }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light-subtle border h-100">
                            <div class="text-muted small mb-1">Kas Masuk Operasi</div>
                            <div class="fw-bold fs-6 text-success">Rp {{ number_format($r['data']['kasMasuk'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light-subtle border h-100">
                            <div class="text-muted small mb-1">Kas Keluar Operasi</div>
                            <div class="fw-bold fs-6 text-danger">Rp {{ number_format($r['data']['kasKeluar'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light-subtle border h-100">
                            <div class="text-muted small mb-1">Net Aktivitas Investasi</div>
                            <div class="fw-bold fs-6 {{ $r['data']['netInvestasi'] < 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($r['data']['netInvestasi'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 rounded-3 bg-light-subtle border h-100">
                            <div class="text-muted small mb-1">Net Aktivitas Pendanaan</div>
                            <div class="fw-bold fs-6 {{ $r['data']['netPendanaan'] < 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($r['data']['netPendanaan'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <p class="text-muted small mb-3 mt-n2">
        <i class="mdi mdi-information-outline me-1"></i>
        Angka di atas parsial (belum termasuk perubahan persediaan), bukan net cashflow final. Klik salah satu periode di bawah untuk lihat laporan lengkap.
    </p>

    {{-- Warning data belum lengkap --}}
    <div class="alert alert-warning d-flex align-items-start gap-2 mb-3" role="alert">
        <i class="mdi mdi-alert-outline fs-5 mt-1"></i>
        <div>
            <div class="fw-semibold">Laporan Cashflow ini belum lengkap</div>
            <div class="small">
                Belum mencakup <strong>perubahan nilai persediaan</strong> dan <strong>saldo kas awal/akhir periode</strong>
                &mdash; menunggu modul ledger stok &amp; mutasi kas.
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mx-auto" style="max-width: 560px;">
        <div class="card-body text-center py-5">
            <div class="avatar avatar-lg bg-label-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px; height:64px;">
                <i class="mdi mdi-cash-sync fs-3 text-primary"></i>
            </div>
            <h5 class="fw-bold mb-1">Pilih Periode Cashflow Statement</h5>
            <p class="text-muted small mb-4">Laporan akan tampil di halaman detail, tombol Print tersedia di sana</p>
            <div class="row g-3 justify-content-center">
                <div class="col-6">
                    <div class="form-floating form-floating-outline">
                        <input class="form-control" type="month" id="html5-month-input">
                        <label for="html5-month-input">Per Bulan</label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-floating form-floating-outline">
                        <select id="yearSelect" class="form-control"></select>
                        <label for="html5-year-input">Per Tahun</label>
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
    <script src="{{ asset('assets') }}/includes/table-cashflow-data.js"></script>
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
            // if (y === currentYear) option.selected = true;
            yearSelect.appendChild(option);
        }

        yearSelect.addEventListener('change', function() {
            if (!this.value) return;

            window.location.href = `/cashflow-detail/${this.value}`;
        });

        document.getElementById('html5-month-input').addEventListener('change', function() {
            if (!this.value) return;

            const [year, month] = this.value.split('-');

            window.location.href = `/cashflow-detail/${year}/${month}`;
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
