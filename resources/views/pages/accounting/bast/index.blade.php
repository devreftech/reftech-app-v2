@extends('layouts.sales.app')
@section('title', 'BAST')

@section('content')
    <div class="d-flex justify-content-between align-items-center py-3 mb-2">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Accounting /</span> BAST
            </h4>
            <p class="text-muted mb-0 small">Daftar Berita Acara Serah Terima Pekerjaan</p>
        </div>
        <button type="button" class="btn btn-primary shadow-sm" id="btnCreateBast">
            <i class="mdi mdi-plus me-1"></i> Buat BAST Manual
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm overflow-hidden mb-4">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0" id="bastTable">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">No. BAST</th>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Customer</th>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Pekerjaan</th>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Tgl Pekerjaan</th>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">No. PO / Kontrak</th>
                        <th class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Dibuat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($basts as $bast)
                        <tr class="bast-row" data-href="{{ route('bast.show', $bast->id) }}" style="cursor: pointer;">
                            <td>
                                <a href="{{ route('bast.show', $bast->id) }}" class="fw-bold text-primary text-decoration-none">
                                    <i class="mdi mdi-file-document-outline me-1"></i>{{ $bast->no_bast }}
                                </a>
                            </td>
                            <td class="fw-semibold text-dark">{{ $bast->customer_name }}</td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 280px;" title="{{ $bast->work_title }}">
                                    {{ $bast->work_title }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    <i class="mdi mdi-calendar-blank-outline me-1"></i>{{ $bast->work_date->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                @if ($bast->po_number)
                                    <span class="badge bg-label-secondary font-monospace">{{ $bast->po_number }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="mdi mdi-account-outline me-1"></i>{{ $bast->creator->name ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('components.modal.bast.create')
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <style>
        .bast-row:hover {
            background-color: rgba(105, 108, 255, 0.04) !important;
            transition: background-color 0.15s ease-in-out;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable) {
                $('#bastTable').DataTable({
                    order: [],
                    language: {
                        emptyTable: 'Belum ada BAST yang dibuat.',
                        zeroRecords: 'Data BAST tidak ditemukan.',
                        search: '',
                        searchPlaceholder: 'Cari BAST...'
                    },
                    pageLength: 15,
                    dom: '<"row mx-2 py-2"<"col-md-6"l><"col-md-6"f>>t<"row mx-2 py-2"<"col-md-6"i><"col-md-6"p>>'
                });
            }
        });

        // Make whole row clickable
        $(document).on('click', '.bast-row', function(e) {
            if (!$(e.target).is('a')) {
                window.location.href = $(this).data('href');
            }
        });

        $('#btnCreateBast').on('click', function() {
            window.openBastModal({});
        });

        $(document).on('bast:saved', function() {
            window.location.reload();
        });
    </script>
@endpush
