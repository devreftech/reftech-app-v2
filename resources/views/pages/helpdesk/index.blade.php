@extends('layouts.sales.app')
@section('title', 'Helpdesk & Bug Report')
@section('content')
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-1">
                    <li class="breadcrumb-item"><a href="/dashboard"><i class="mdi mdi-home-outline me-1"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">IT & System</a></li>
                    <li class="breadcrumb-item active">Helpdesk</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <span>Helpdesk & Pelaporan Kendala</span>
                <span class="badge bg-label-primary rounded-pill fs-7 fw-normal py-1 px-2">Support Center</span>
            </h4>
            <p class="text-muted small mb-0">Laporkan kendala teknis, bug sistem, atau ajukan permintaan fitur baru ke tim developer.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-3 py-2 waves-effect waves-light" data-bs-target="#formHelpdesk" data-bs-toggle="modal">
                <i class="mdi mdi-plus-circle-outline fs-5"></i>
                <span class="fw-semibold">Buat Tiket Baru</span>
            </button>
        </div>
    </div>

    {{-- KPI Stat Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Tiket --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-medium d-block mb-1">Total Tiket User</span>
                            <h3 class="fw-bold text-dark mb-0">{{ number_format($stats['total_user']) }}</h3>
                        </div>
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-primary">
                                <i class="mdi mdi-ticket-confirmation-outline mdi-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 mt-2 text-muted small">
                        <i class="mdi mdi-account-voice fs-6 text-primary"></i>
                        <span>Laporan dari pengguna</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Menunggu / Open --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-medium d-block mb-1">Menunggu (Open)</span>
                            <h3 class="fw-bold text-danger mb-0">{{ number_format($stats['open_user']) }}</h3>
                        </div>
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-danger">
                                <i class="mdi mdi-alert-circle-outline mdi-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 mt-2 text-danger small">
                        <i class="mdi mdi-clock-alert-outline fs-6"></i>
                        <span>Belum ditindaklanjuti</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Sedang Diproses --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-medium d-block mb-1">Dalam Pengerjaan</span>
                            <h3 class="fw-bold text-warning mb-0">{{ number_format($stats['progress_user']) }}</h3>
                        </div>
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-warning">
                                <i class="mdi mdi-progress-wrench mdi-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 mt-2 text-warning small">
                        <i class="mdi mdi-progress-clock fs-6"></i>
                        <span>Sedang diperbaiki tim IT</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Selesai / Resolved --}}
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="text-muted small fw-medium d-block mb-1">Terselesaikan</span>
                            <h3 class="fw-bold text-success mb-0">{{ number_format($stats['resolved_user']) }}</h3>
                        </div>
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-success">
                                <i class="mdi mdi-check-decagram-outline mdi-24px"></i>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 mt-2 text-success small">
                        <i class="mdi mdi-checkbox-marked-circle-outline fs-6"></i>
                        <span>Kendala tuntas diperbaiki</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Section --}}
    @if ($isAdmin)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs card-header-tabs m-0 border-0" id="helpdesk-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active fw-semibold py-3 px-4 d-flex align-items-center gap-2" role="tab" data-bs-toggle="tab" data-bs-target="#navs-user-tickets" aria-controls="navs-user-tickets" aria-selected="true">
                            <i class="mdi mdi-ticket-account mdi-18px"></i>
                            <span>Tiket Pengguna</span>
                            <span class="badge rounded-pill bg-label-primary ms-1">{{ number_format($stats['total_user']) }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold py-3 px-4 d-flex align-items-center gap-2" role="tab" data-bs-toggle="tab" data-bs-target="#navs-system-errors" aria-controls="navs-system-errors" aria-selected="false">
                            <i class="mdi mdi-flash-alert-outline mdi-18px text-danger"></i>
                            <span>Temuan Error System</span>
                            @if ($stats['open_errors'] > 0)
                                <span class="badge rounded-pill bg-danger text-white ms-1">{{ number_format($stats['open_errors']) }} Open</span>
                            @else
                                <span class="badge rounded-pill bg-label-secondary ms-1">{{ number_format($stats['total_errors']) }}</span>
                            @endif
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content p-0">
                    {{-- Tab 1: User Tickets --}}
                    <div class="tab-pane fade show active p-3" id="navs-user-tickets" role="tabpanel">
                        <div class="card-datatable table-responsive pt-1">
                            <table class="datatable-helpdesk-admin table table-hover table-bordered align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 140px;">No Ticket</th>
                                        <th style="width: 160px;">Pelapor</th>
                                        <th>Judul & Kendala</th>
                                        <th style="width: 130px;">Status</th>
                                        <th style="width: 150px;">Tanggal</th>
                                        <th class="text-center" style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    {{-- Tab 2: System Errors --}}
                    <div class="tab-pane fade p-3" id="navs-system-errors" role="tabpanel">
                        <div class="alert alert-danger d-flex align-items-center p-3 mb-3 border-0 bg-label-danger" role="alert">
                            <i class="mdi mdi-shield-alert-outline me-2 fs-4 flex-shrink-0"></i>
                            <div>
                                <span class="fw-bold d-block">Log Runtime Error Otomatis</span>
                                <small class="text-muted">Error HTTP 500 / Exception yang otomatis tercatat oleh aplikasi saat terjadi kendala pada user.</small>
                            </div>
                        </div>
                        <div class="card-datatable table-responsive pt-1">
                            <table class="datatable-helpdesk-system-errors table table-hover table-bordered align-middle w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 140px;">No Ticket</th>
                                        <th style="width: 160px;">User Terdampak</th>
                                        <th>Exception / Komponen</th>
                                        <th style="width: 130px;">Status</th>
                                        <th style="width: 150px;">Waktu Terdeteksi</th>
                                        <th class="text-center" style="width: 80px;">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- View for Non-Admin (Sales, Technician, etc.) --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-format-list-bulleted text-primary"></i>
                    <span>Riwayat Tiket Saya</span>
                </h5>
                <span class="text-muted small">Daftar laporan tiket yang telah Anda buat</span>
            </div>
            <div class="card-body p-3">
                <div class="card-datatable table-responsive pt-1">
                    <table class="datatable-helpdesk table table-hover table-bordered align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 150px;">No Ticket</th>
                                <th>Judul Kendala</th>
                                <th style="width: 140px;">Status</th>
                                <th style="width: 160px;">Tanggal Dibuat</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Modals --}}
    @include('components.modal.helpdesk.form')
    @include('components.modal.helpdesk.detail')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(67, 89, 113, 0.03);
        }
        .helpdesk-stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 50rem;
        }
        .helpdesk-stat-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    @if ($isAdmin)
        <script src="{{ asset('assets') }}/includes/table-helpdesk-admin.js"></script>
    @else
        <script src="{{ asset('assets') }}/includes/table-helpdesk.js"></script>
    @endif
@endpush

@if ($isAdmin)
    @push('script')
        <script>
            // Adjust table columns when switching tabs
            $('#helpdesk-tabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
            });

            function submitHelpdeskStatus(id, status, note) {
                $.ajax({
                    url: '{{ url('helpdesk/status') }}/' + id,
                    type: 'POST',
                    data: {
                        '_method': 'PATCH',
                        '_token': '{{ csrf_token() }}',
                        'status': status,
                        'note': note || '',
                    },
                    success: function(response) {
                        if (response == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Status berhasil diperbarui!',
                                text: 'Status tiket kini: ' + status,
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: {
                                    confirmButton: 'btn btn-success waves-effect',
                                },
                            });
                            window.setTimeout(function() {
                                window.location.href = '/helpdesk';
                            }, 1200);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Gagal memperbarui status tiket.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Jaringan',
                            text: 'Terjadi kesalahan saat mengirim request.'
                        });
                    }
                });
            }

            $(document).on('click', '.button-helpdesk-status', function() {
                var id = $('#detailHelpdesk').attr('data-id');
                var status = $(this).data('status');
                var $detailModalEl = document.getElementById('detailHelpdesk');
                var detailModal = bootstrap.Modal.getInstance($detailModalEl);

                $($detailModalEl).one('hidden.bs.modal', function() {
                    if (status === 'Resolved') {
                        Swal.fire({
                            title: 'Selesaikan Tiket',
                            text: 'Berikan ringkasan atau keterangan bagaimana kendala ini diselesaikan.',
                            input: 'textarea',
                            inputLabel: 'Keterangan Penyelesaian',
                            inputPlaceholder: 'Contoh: Bug query sudah difix pada commit xxxx...',
                            inputAttributes: { style: 'height: 120px' },
                            showCancelButton: true,
                            confirmButtonText: 'Selesaikan Tiket',
                            cancelButtonText: 'Batal',
                            customClass: {
                                confirmButton: 'btn btn-success me-3 waves-effect waves-light',
                                cancelButton: 'btn btn-label-secondary waves-effect',
                            },
                            buttonsStyling: false,
                            inputValidator: function(value) {
                                if (!value || !value.trim()) {
                                    return 'Keterangan penyelesaian wajib diisi!';
                                }
                            },
                        }).then(function(result) {
                            if (!result.value) return;
                            submitHelpdeskStatus(id, status, result.value);
                        });
                    } else {
                        Swal.fire({
                            title: 'Ubah Status Tiket?',
                            text: 'Ubah status tiket menjadi "' + status + '"?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Ubah Status',
                            cancelButtonText: 'Batal',
                            customClass: {
                                confirmButton: 'btn btn-warning me-3 waves-effect waves-light',
                                cancelButton: 'btn btn-label-secondary waves-effect',
                            },
                            buttonsStyling: false,
                        }).then(function(result) {
                            if (!result.value) return;
                            submitHelpdeskStatus(id, status, null);
                        });
                    }
                });
                if (detailModal) detailModal.hide();
            });
        </script>
    @endpush
@endif
