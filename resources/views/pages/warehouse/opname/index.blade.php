@extends('layouts.sales.app')
@section('title', 'Stock Opname - Warehouse & Inventory')
@section('content')
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Warehouse &amp; Inventory /</span> Stock Opname Fisik Gudang
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-clipboard-text-clock-outline me-1"></i> Rekonsiliasi berkala (Caturwulan / Quarter) antara stok fisik gudang dan stok sistem
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createOpname">
                <i class="mdi mdi-plus me-1"></i> Buat Sesi Stock Opname
            </button>
        </div>
    </div>

    {{-- KPI Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Total Sesi Audit</span>
                        <h4 class="fw-bolder text-primary mb-0 mt-1">{{ number_format($totalSessions ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 10px;">Semua periode tercatat</small>
                    </div>
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary"><i class="mdi mdi-clipboard-check-outline fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Sesi Tahun Ini ({{ date('Y') }})</span>
                        <h4 class="fw-bolder text-info mb-0 mt-1">{{ number_format($thisYearSessions ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 10px;">Audit aktif tahun {{ date('Y') }}</small>
                    </div>
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info"><i class="mdi mdi-calendar-range fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Total Item Terdaftar</span>
                        <h4 class="fw-bolder text-success mb-0 mt-1">{{ number_format($totalItemsAudited ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 10px;">SKU diperiksa fisik</small>
                    </div>
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-success"><i class="mdi mdi-cube-scan fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: {{ ($totalDiscrepancyItems ?? 0) > 0 ? '#fffbeb' : '#fff' }}; border-color: {{ ($totalDiscrepancyItems ?? 0) > 0 ? '#fde68a' : 'transparent' }} !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-warning small fw-semibold text-uppercase" style="font-size: 11px;">Item Ada Selisih</span>
                        <h4 class="fw-bolder text-warning mb-0 mt-1">{{ number_format($totalDiscrepancyItems ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 10px;">Selisih fisik &ne; sistem</small>
                    </div>
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-warning"><i class="mdi mdi-alert-circle-outline fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main DataTable Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title mb-0 d-flex align-items-center">
                <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i> Daftar Sesi Stock Opname
            </h5>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="table table-hover table-striped align-middle dt-opname-main w-100">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>Periode &amp; Tahun</th>
                        <th>Tanggal Pelaksanaan</th>
                        <th>Petugas Gudang</th>
                        <th>Item Terdaftar</th>
                        <th>Catatan / Keterangan</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($opnames as $index => $opname)
                        @php
                            $qBadgeClass = match ((int)$opname->periode) {
                                1 => 'bg-label-primary',
                                2 => 'bg-label-info',
                                3 => 'bg-label-warning',
                                4 => 'bg-label-success',
                                default => 'bg-label-secondary'
                            };
                            $qRoman = match ((int)$opname->periode) {
                                1 => 'I (Jan - Mar)',
                                2 => 'II (Apr - Jun)',
                                3 => 'III (Jul - Sep)',
                                4 => 'IV (Okt - Des)',
                                default => '-'
                            };
                        @endphp
                        <tr>
                            <td class="text-center fw-semibold text-muted">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">
                                        <span class="badge {{ $qBadgeClass }} me-1">Q{{ $opname->periode }}</span>
                                        Caturwulan {{ $qRoman }}
                                    </span>
                                    <small class="text-muted">Tahun {{ $opname->year ?? date('Y', strtotime($opname->date)) }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-calendar-blank-outline text-muted me-1"></i>
                                    <span>{{ \Carbon\Carbon::parse($opname->date)->translatedFormat('d M Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary text-uppercase fw-bold" style="font-size: 11px;">
                                            {{ substr($opname->user?->name ?? 'G', 0, 2) }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="fw-semibold text-dark d-block">{{ $opname->user?->name ?? '-' }}</span>
                                        <small class="text-muted">{{ $opname->user?->role ?? 'Petugas' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-label-dark font-monospace fs-7">
                                        <i class="mdi mdi-cube-outline me-1"></i>{{ number_format($opname->detail_count ?? 0, 0, ',', '.') }} SKU
                                    </span>
                                    @if (($opname->selisih_count ?? 0) > 0)
                                        <span class="badge bg-label-danger" title="{{ $opname->selisih_count }} item memiliki selisih">
                                            <i class="mdi mdi-alert-outline me-1"></i>{{ $opname->selisih_count }} Selisih
                                        </span>
                                    @else
                                        <span class="badge bg-label-success" title="Semua stok klop">
                                            <i class="mdi mdi-check me-1"></i>Match
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">{{ !empty($opname->note) && $opname->note !== '-' ? $opname->note : '-' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('opname.show', $opname->id) }}" class="btn btn-sm btn-primary" title="Kelola &amp; Input Stok Fisik">
                                        <i class="mdi mdi-pencil-box-outline me-1"></i> Input
                                    </a>
                                    <a href="{{ route('opname.show_print', $opname->id) }}" target="_blank" class="btn btn-sm btn-label-secondary" title="Cetak / Download Form Opname">
                                        <i class="mdi mdi-printer-outline"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-label-danger btn-delete-opname" data-id="{{ $opname->id }}" data-periode="Quarter {{ $opname->periode }} ({{ $opname->year }})" title="Hapus Sesi">
                                        <i class="mdi mdi-trash-can-outline"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-center">
                                    <i class="mdi mdi-clipboard-text-outline text-muted" style="font-size: 48px;"></i>
                                    <p class="text-muted mt-2 mb-3">Belum ada sesi Stock Opname yang dibuat.</p>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createOpname">
                                        <i class="mdi mdi-plus me-1"></i> Buat Sesi Pertama
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Delete Helper --}}
    <form id="formDeleteOpname" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @include('components.modal.warehouse.opname.form')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            var table = $('.dt-opname-main').DataTable({
                dom: '<"row mx-2 py-2"<"col-md-6 d-flex align-items-center"l><"col-md-6 d-flex justify-content-end align-items-center"f>>t<"row mx-2 py-2"<"col-md-6"i><"col-md-6"p>>',
                order: [[0, 'asc']],
                displayLength: 15,
                lengthMenu: [10, 15, 25, 50, 100],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari sesi opname, petugas, catatan...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ sesi",
                    infoEmpty: "Menampilkan 0 sesi",
                    infoFiltered: "(disaring dari _MAX_ total sesi)",
                    zeroRecords: "Tidak ditemukan data yang sesuai",
                    paginate: {
                        next: '<i class="mdi mdi-chevron-right"></i>',
                        previous: '<i class="mdi mdi-chevron-left"></i>'
                    }
                }
            });

            // Handle SweetAlert Delete
            $(document).on('click', '.btn-delete-opname', function() {
                var id = $(this).data('id');
                var periode = $(this).data('periode');
                
                Swal.fire({
                    title: "Hapus Sesi Opname?",
                    text: "Sesi " + periode + " beserta seluruh rincian input stok fisik akan dihapus permanen.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal",
                    customClass: {
                        confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect"
                    },
                    buttonsStyling: false
                }).then(function(result) {
                    if (result.isConfirmed) {
                        var form = $('#formDeleteOpname');
                        form.attr('action', '{{ url("/stock-opname") }}/' + id);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
