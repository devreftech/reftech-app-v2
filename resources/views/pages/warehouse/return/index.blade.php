@extends('layouts.sales.app')
@section('title', 'Manajemen Retur Barang')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header & Breadcrumb --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Operasional & Logistik</li>
                    <li class="breadcrumb-item active" aria-current="page">Retur Barang</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-0 text-primary">
                <i class="mdi mdi-archive-cancel-outline me-2"></i>Manajemen Retur Barang
            </h4>
            <p class="text-muted small mb-0 mt-1">
                Monitoring, audit fisik, dan verifikasi alur pengembalian barang pelanggan (Sales Return) serta ke vendor pemasok (Purchase Return).
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary waves-effect" onclick="window.location.reload();">
                <i class="mdi mdi-refresh me-1"></i> Refresh
            </button>
            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modalNewReturnGuide">
                <i class="mdi mdi-plus me-1"></i> Buat Retur Baru
            </button>
        </div>
    </div>

    {{-- KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Retur --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm return-metric-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Total Retur</p>
                            <h3 class="fw-bold mb-1 text-primary">{{ number_format($totalCount, 0, ',', '.') }}</h3>
                            <span class="badge bg-label-primary rounded-pill small">
                                <i class="mdi mdi-file-document-outline me-1"></i>Keseluruhan Berkas
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-primary rounded p-2">
                            <i class="mdi mdi-archive-cancel fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Menunggu Verifikasi --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm return-metric-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Menunggu Verifikasi</p>
                            <h3 class="fw-bold mb-1 text-warning">{{ number_format($pendingCount, 0, ',', '.') }}</h3>
                            <span class="badge bg-label-warning rounded-pill small">
                                <i class="mdi mdi-clock-outline me-1"></i>Perlu Tindakan
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-warning rounded p-2">
                            <i class="mdi mdi-clock-alert-outline fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Selesai / Disetujui --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm return-metric-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Selesai / Approved</p>
                            <h3 class="fw-bold mb-1 text-success">{{ number_format($completedCount, 0, ',', '.') }}</h3>
                            <span class="badge bg-label-success rounded-pill small">
                                <i class="mdi mdi-check-circle-outline me-1"></i>Tuntas Diproses
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-success rounded p-2">
                            <i class="mdi mdi-check-decagram-outline fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Kuantitas Unit --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm return-metric-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Total Fisik Unit</p>
                            <h3 class="fw-bold mb-1 text-info">{{ number_format($totalQty, 0, ',', '.') }}</h3>
                            <span class="badge bg-label-info rounded-pill small">
                                <i class="mdi mdi-package-variant-closed me-1"></i>Akumulasi Item
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-info rounded p-2">
                            <i class="mdi mdi-cube-send fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card border-0 shadow-sm">
        {{-- Card Header & Tab Filter --}}
        <div class="card-header border-bottom pb-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                {{-- Filter Tabs --}}
                <ul class="nav nav-pills card-header-pills" id="returnFilterTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" data-filter="all">
                            Semua Retur <span class="badge bg-secondary ms-1">{{ $totalCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-filter="pending">
                            <i class="mdi mdi-clock-outline me-1"></i>Menunggu Verifikasi 
                            <span class="badge bg-warning ms-1">{{ $pendingCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-filter="completed">
                            <i class="mdi mdi-check-circle-outline me-1"></i>Selesai 
                            <span class="badge bg-success ms-1">{{ $completedCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-filter="customer">
                            <i class="mdi mdi-account-arrow-left me-1"></i>Retur Customer
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-filter="supplier">
                            <i class="mdi mdi-truck-delivery-outline me-1"></i>Retur Supplier
                        </button>
                    </li>
                </ul>

                {{-- Status Filter Quick Dropdown --}}
                <div class="d-flex align-items-center gap-2">
                    <select id="selectStatusFilter" class="form-select form-select-sm" style="width: 170px;">
                        <option value="">Semua Status</option>
                        <option value="0">Menunggu Verifikasi</option>
                        <option value="1">Selesai / Approved</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="card-body pt-3">
            @if(session('success'))
                <div class="alert alert-solid-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-solid-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tableReturn">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th>No. Retur & Tanggal</th>
                            <th>Tipe Dokumen</th>
                            <th>Pihak / Mitra (Customer / Supplier)</th>
                            <th>Referensi Dokumen</th>
                            <th class="text-center">Kuantitas & Item</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($returns as $index => $r)
                            @php
                                $isSupplier = !empty($r->id_product_in);
                                $isCustomer = !$isSupplier;
                                $typeCategory = $isSupplier ? 'supplier' : 'customer';
                                $statusCategory = $r->status == 1 ? 'completed' : 'pending';
                                
                                // Partner name
                                $company = '-';
                                $salesName = null;
                                $refDoc = '-';
                                
                                if ($isCustomer && $r->pending) {
                                    $quote = $r->pending->quotation;
                                    $company = $quote?->pic?->client?->company ?? 'Customer Umum';
                                    $salesName = $quote?->sales?->name;
                                    $refDoc = $quote?->no_quote ?? '-';
                                } elseif ($isSupplier && $r->productIn) {
                                    $company = $r->productIn->supplier?->nama_supplier ?? 'Supplier Umum';
                                    $refDoc = $r->productIn->invoice ?? $r->productIn->no_product_in ?? '-';
                                } else {
                                    $company = 'Dokumen Internal #' . $r->id;
                                }

                                $totalItemCount = $r->detail->count();
                                $totalItemQty = $r->detail->sum('qty');
                            @endphp
                            <tr data-type="{{ $typeCategory }}" data-status="{{ $r->status }}" data-category="{{ $statusCategory }}">
                                <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('return.show', $r->id) }}" class="fw-bold text-primary return-code-link">
                                            {{ $r->no_return }}
                                        </a>
                                        <span class="text-muted small">
                                            <i class="mdi mdi-calendar-blank-outline me-1"></i>{{ $r->date ? date('d M Y', strtotime($r->date)) : '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if ($isCustomer)
                                        <span class="badge bg-label-info rounded-pill px-2 py-1">
                                            <i class="mdi mdi-account-arrow-left me-1"></i>Retur Penjualan
                                        </span>
                                    @else
                                        <span class="badge bg-label-warning rounded-pill px-2 py-1">
                                            <i class="mdi mdi-truck-delivery-outline me-1"></i>Retur Pembelian
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ $company }}</span>
                                        @if ($salesName)
                                            <span class="text-muted small">
                                                <span class="badge bg-label-secondary py-0 px-1 me-1">Sales</span> {{ $salesName }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-secondary">
                                        {{ $refDoc }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-primary fw-bold" data-bs-toggle="tooltip" title="{{ $totalItemCount }} jenis barang">
                                        {{ $totalItemQty }} Unit
                                    </span>
                                    <div class="text-muted small mt-1">
                                        {{ $totalItemCount }} item
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($r->status == 1)
                                        <span class="badge bg-success rounded-pill px-3 py-1">
                                            <i class="mdi mdi-check-circle-outline me-1"></i>Selesai
                                        </span>
                                    @else
                                        <span class="badge bg-warning rounded-pill px-3 py-1 text-dark">
                                            <i class="mdi mdi-clock-outline me-1"></i>Menunggu Verifikasi
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        {{-- Detail Button --}}
                                        <a href="{{ route('return.show', $r->id) }}" 
                                           class="btn btn-sm btn-icon btn-outline-primary waves-effect" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Lihat Rincian Retur">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>

                                        {{-- If pending, allow quick action --}}
                                        @if ($r->status == 0)
                                            <a href="{{ route('product-in.return', $r->id) }}" 
                                               class="btn btn-sm btn-icon btn-outline-info waves-effect" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Proses / Cetak Product In">
                                                <i class="mdi mdi-receipt-text-outline"></i>
                                            </a>
                                        @endif

                                        {{-- Delete Button --}}
                                        <button type="button" 
                                                class="btn btn-sm btn-icon btn-outline-danger waves-effect btn-delete-return" 
                                                data-id="{{ $r->id }}" 
                                                data-no="{{ $r->no_return }}" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="Hapus Retur">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                        <form id="delete-form-{{ $r->id }}" action="{{ route('return.destroy', $r->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary mb-3 rounded-circle p-3">
                                            <i class="mdi mdi-archive-remove-outline fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-secondary mb-1">Belum Ada Transaksi Retur</h5>
                                        <p class="text-muted small mb-0">Belum ada pengembalian barang yang tercatat pada sistem saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Panduan Buat Retur Baru --}}
<div class="modal fade" id="modalNewReturnGuide" tabindex="-1" aria-labelledby="modalNewReturnGuideLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalNewReturnGuideLabel">
                    <i class="mdi mdi-information-outline text-primary me-2"></i>Alur Pembuatan Retur Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4">
                    Pembuatan retur barang di ERP Reftech dilakukan secara terintegrasi melalui modul transaksi asal agar mutasi stok dan jurnal pembukuan tercatat akurat:
                </p>

                <div class="d-flex gap-3 mb-3 p-3 rounded bg-label-primary align-items-start">
                    <div class="avatar avatar-sm bg-primary text-white rounded mt-1">
                        <i class="mdi mdi-account-arrow-left"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">1. Retur Penjualan dari Customer</h6>
                        <p class="small text-muted mb-2">
                            Buka modul <strong>Sales Invoice</strong>, cari faktur penjualan terkait, lalu klik opsi <strong>"Return Quotation"</strong>.
                        </p>
                        <a href="{{ url('/payment-index/invoice') }}" class="btn btn-xs btn-primary waves-effect">
                            Buka Sales Invoice <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="d-flex gap-3 p-3 rounded bg-label-warning align-items-start">
                    <div class="avatar avatar-sm bg-warning text-white rounded mt-1">
                        <i class="mdi mdi-truck-delivery-outline"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">2. Retur Pembelian ke Supplier</h6>
                        <p class="small text-muted mb-2">
                            Buka modul <strong>Penerimaan Barang (Product In)</strong> atau <strong>Faktur Hutang (AP)</strong>, pilih faktur terkait, lalu klik opsi <strong>"Retur Barang"</strong>.
                        </p>
                        <a href="{{ url('/payable/invoice') }}" class="btn btn-xs btn-warning waves-effect text-dark">
                            Buka Faktur AP Supplier <i class="mdi mdi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .return-metric-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .return-metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.08) !important;
        }
        .return-code-link {
            transition: color 0.15s ease;
        }
        .return-code-link:hover {
            text-decoration: underline;
        }
        #tableReturn thead th {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize DataTable
            var table = $('#tableReturn').DataTable({
                order: [[1, 'desc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari No Retur, Customer, Referensi...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ retur",
                    infoEmpty: "Menampilkan 0 s/d 0 dari 0 retur",
                    zeroRecords: "Tidak ada data retur yang sesuai dengan filter pencarian",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Lanjut",
                        previous: "Kembali"
                    }
                },
                dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 text-md-end"f>>t<"row align-items-center mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 text-md-end"p>>'
            });

            // Custom Tab Filtering
            $('#returnFilterTabs .nav-link').on('click', function () {
                $('#returnFilterTabs .nav-link').removeClass('active');
                $(this).addClass('active');

                var filter = $(this).data('filter');
                $.fn.dataTable.ext.search = [];

                if (filter === 'pending') {
                    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData, counter) {
                        var row = table.row(dataIndex).node();
                        return $(row).attr('data-status') === '0';
                    });
                } else if (filter === 'completed') {
                    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData, counter) {
                        var row = table.row(dataIndex).node();
                        return $(row).attr('data-status') === '1';
                    });
                } else if (filter === 'customer') {
                    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData, counter) {
                        var row = table.row(dataIndex).node();
                        return $(row).attr('data-type') === 'customer';
                    });
                } else if (filter === 'supplier') {
                    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData, counter) {
                        var row = table.row(dataIndex).node();
                        return $(row).attr('data-type') === 'supplier';
                    });
                }
                table.draw();
            });

            // Dropdown Status Filter
            $('#selectStatusFilter').on('change', function () {
                var val = $(this).val();
                $.fn.dataTable.ext.search = [];
                if (val !== '') {
                    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData, counter) {
                        var row = table.row(dataIndex).node();
                        return $(row).attr('data-status') === val;
                    });
                }
                table.draw();
            });

            // SweetAlert2 Delete Confirmation
            $(document).on('click', '.btn-delete-return', function () {
                var returnId = $(this).data('id');
                var returnNo = $(this).data('no');

                Swal.fire({
                    title: 'Hapus Dokumen Retur?',
                    text: 'Anda akan menghapus berkas retur #' + returnNo + '. Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: 'Ya, Hapus Data',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger me-2',
                        cancelButton: 'btn btn-outline-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-form-' + returnId).submit();
                    }
                });
            });
        });
    </script>
@endpush
