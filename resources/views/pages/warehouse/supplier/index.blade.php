@extends('layouts.sales.app')
@section('title', 'Direktori Vendor & Supplier')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Pengadaan & Logistik</li>
                    <li class="breadcrumb-item active" aria-current="page">Mitra Supplier</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-primary">
                    <i class="mdi mdi-domain me-2"></i>Direktori Vendor & Supplier
                </h4>
                <span class="badge bg-label-primary rounded-pill">{{ $totalCount }} Mitra</span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Database terpadu pemasok suku cadang, material, dan unit mesin, dilengkapi kontak PIC, status perpajakan, serta histori pengadaan.
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary waves-effect" onclick="window.location.reload();">
                <i class="mdi mdi-refresh me-1"></i> Refresh
            </button>
            <a href="{{ url('/payable/statement') }}" class="btn btn-outline-info waves-effect">
                <i class="mdi mdi-file-document-outline me-1"></i> Kartu Hutang (SOA)
            </a>
            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#createSupplier">
                <i class="mdi mdi-plus me-1"></i> Tambah Supplier
            </button>
        </div>
    </div>

    {{-- Top KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Supplier --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm supplier-kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Total Supplier</p>
                            <h3 class="fw-bold mb-1 text-primary">{{ number_format($totalCount, 0, ',', '.') }}</h3>
                            <span class="badge bg-label-primary rounded-pill small">
                                <i class="mdi mdi-domain me-1"></i>Mitra Terdaftar
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-primary rounded p-2">
                            <i class="mdi mdi-domain fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Supplier Lokal --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm supplier-kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Pemasok Lokal</p>
                            <h3 class="fw-bold mb-1 text-success">{{ number_format($lokalCount, 0, ',', '.') }}</h3>
                            <span class="badge bg-label-success rounded-pill small">
                                <i class="mdi mdi-map-marker-radius-outline me-1"></i>Dalam Negeri
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-success rounded p-2">
                            <i class="mdi mdi-home-city-outline fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Supplier Import --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm supplier-kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Pemasok Import</p>
                            <h3 class="fw-bold mb-1 text-info">{{ number_format($importCount, 0, ',', '.') }}</h3>
                            <span class="badge bg-label-info rounded-pill small">
                                <i class="mdi mdi-airplane-takeoff me-1"></i>Luar Negeri
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-info rounded p-2">
                            <i class="mdi mdi-earth fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mitra Aktif Bertransaksi --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm supplier-kpi-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted fw-semibold mb-1 small text-uppercase tracking-wider">Aktif Bertransaksi</p>
                            <h3 class="fw-bold mb-1 text-warning">{{ number_format($activeCount, 0, ',', '.') }}</h3>
                            <span class="badge bg-label-warning rounded-pill small">
                                <i class="mdi mdi-handshake-outline me-1"></i>{{ number_format($totalTransactions, 0, ',', '.') }} Masuk
                            </span>
                        </div>
                        <div class="avatar avatar-md bg-label-warning rounded p-2">
                            <i class="mdi mdi-truck-delivery-outline fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Directory Card --}}
    <div class="card border-0 shadow-sm">
        {{-- Card Header & Filter Pills --}}
        <div class="card-header border-bottom pb-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                {{-- Filter Tabs --}}
                <ul class="nav nav-pills card-header-pills" id="supplierFilterTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-semibold" data-filter="all">
                            Semua <span class="badge bg-secondary ms-1">{{ $totalCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-filter="lokal">
                            <i class="mdi mdi-map-marker-outline me-1"></i>Lokal 
                            <span class="badge bg-success ms-1">{{ $lokalCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-filter="import">
                            <i class="mdi mdi-airplane me-1"></i>Import 
                            <span class="badge bg-info ms-1">{{ $importCount }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-semibold" data-filter="active">
                            <i class="mdi mdi-check-decagram-outline me-1"></i>Aktif Ber-PO 
                            <span class="badge bg-warning ms-1">{{ $activeCount }}</span>
                        </button>
                    </li>
                </ul>

                {{-- Area Quick Dropdown --}}
                @php
                    $areas = $suppliers->pluck('area')
                        ->filter()
                        ->map(fn($a) => trim($a))
                        ->filter(fn($a) => $a != '-' && $a != '')
                        ->unique()
                        ->sort()
                        ->values();
                @endphp
                <div class="d-flex align-items-center gap-2">
                    <select id="selectAreaFilter" class="form-select form-select-sm" style="min-width: 170px;">
                        <option value="">Semua Wilayah</option>
                        @foreach ($areas as $a)
                            <option value="{{ $a }}">{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="card-body pt-3 pb-0">
            @if(session('success'))
                <div class="alert alert-solid-success alert-dismissible fade show mb-3" role="alert">
                    <i class="mdi mdi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-solid-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        {{-- Table Container --}}
        <div class="card-body pt-1">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tableSupplier">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;" class="text-center">#</th>
                            <th>Perusahaan & Kategori</th>
                            <th>Kontak & Komunikasi</th>
                            <th>Kontak PIC</th>
                            <th>Wilayah & Alamat</th>
                            <th class="text-center">Histori Transaksi</th>
                            <th class="text-center" style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $index => $s)
                            @php
                                $isImport = stripos($s->info ?? '', 'import') !== false || 
                                           stripos($s->area ?? '', 'china') !== false || 
                                           stripos($s->area ?? '', 'shanghai') !== false;
                                $category = $isImport ? 'import' : 'lokal';
                                $hasTransactions = $s->product_in_count > 0 || $s->purchase_count > 0;
                                $firstPic = $s->pics->first();
                                
                                // Generate Initials
                                $words = explode(' ', trim(str_replace(['PT.', 'PT', 'CV.', 'CV'], '', $s->supplier)));
                                $initials = '';
                                foreach ($words as $w) {
                                    if (!empty($w)) {
                                        $initials .= strtoupper(substr($w, 0, 1));
                                    }
                                    if (strlen($initials) >= 2) break;
                                }
                                if (empty($initials)) $initials = 'SP';

                                // Color badge theme for avatar
                                $colors = ['primary', 'success', 'info', 'warning', 'danger', 'dark'];
                                $avatarColor = $colors[$s->id % count($colors)];
                            @endphp
                            <tr data-category="{{ $category }}" 
                                data-active="{{ $hasTransactions ? '1' : '0' }}"
                                data-area="{{ trim($s->area ?? '') }}">
                                <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-label-{{ $avatarColor }} text-{{ $avatarColor }} rounded-circle me-3 fw-bold d-flex align-items-center justify-content-center">
                                            {{ $initials }}
                                        </div>
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('supplier.detail', $s->id) }}" class="fw-bold text-dark supplier-name-link">
                                                {{ $s->supplier }}
                                            </a>
                                            <div class="d-flex align-items-center gap-1 mt-1">
                                                @if ($isImport)
                                                    <span class="badge bg-label-info rounded-pill px-2 py-0 small">
                                                        <i class="mdi mdi-airplane me-1"></i>Import
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-success rounded-pill px-2 py-0 small">
                                                        <i class="mdi mdi-map-marker-outline me-1"></i>Lokal
                                                    </span>
                                                @endif

                                                @if (!empty($s->code))
                                                    <span class="badge bg-label-secondary rounded-pill px-2 py-0 small">
                                                        {{ $s->code }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        @if (!empty($s->phone) && $s->phone != '-')
                                            <a href="tel:{{ $s->phone }}" class="text-body text-truncate mb-1">
                                                <i class="mdi mdi-phone-outline text-primary me-1"></i>{{ $s->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted mb-1"><i class="mdi mdi-phone-off text-muted me-1"></i>-</span>
                                        @endif

                                        @if (!empty($s->email) && $s->email != '-')
                                            <a href="mailto:{{ $s->email }}" class="text-muted text-truncate">
                                                <i class="mdi mdi-email-outline text-info me-1"></i>{{ $s->email }}
                                            </a>
                                        @else
                                            <span class="text-muted"><i class="mdi mdi-email-off-outline text-muted me-1"></i>-</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if ($firstPic)
                                        <div class="d-flex flex-column small">
                                            <span class="fw-semibold text-dark">
                                                <i class="mdi mdi-account-tie-outline text-primary me-1"></i>{{ $firstPic->name_pic }}
                                            </span>
                                            @if (!empty($firstPic->position))
                                                <span class="text-muted">{{ $firstPic->position }}</span>
                                            @endif
                                            @if (!empty($firstPic->phone_pic))
                                                <span class="text-muted">{{ $firstPic->phone_pic }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">
                                            <i class="mdi mdi-account-question-outline me-1"></i>Belum ada PIC
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column small">
                                        <span class="fw-medium text-dark">
                                            <i class="mdi mdi-map-marker text-danger me-1"></i>{{ !empty($s->area) && $s->area != '-' ? $s->area : 'Wilayah N/A' }}
                                        </span>
                                        @if (!empty($s->address) && $s->address != '-')
                                            <span class="text-muted text-truncate" style="max-width: 200px;" title="{{ $s->address }}">
                                                {{ $s->address }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <div class="d-flex gap-1">
                                            <span class="badge bg-label-primary px-2" title="Penerimaan Barang Masuk">
                                                <i class="mdi mdi-package-down me-1"></i>{{ $s->product_in_count }} In
                                            </span>
                                            <span class="badge bg-label-secondary px-2" title="Purchase Order">
                                                <i class="mdi mdi-file-sign me-1"></i>{{ $s->purchase_count }} PO
                                            </span>
                                        </div>
                                        <a href="{{ route('payable.statement', ['supplier_id' => $s->id]) }}" 
                                           class="text-info small fw-semibold text-decoration-none mt-1" 
                                           title="Buka Kartu Hutang Supplier">
                                            <i class="mdi mdi-book-open-outline me-1"></i>Kartu Hutang
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        {{-- Detail Profile --}}
                                        <a href="{{ route('supplier.detail', $s->id) }}" 
                                           class="btn btn-sm btn-icon btn-outline-primary waves-effect" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Lihat Profil & Histori">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>

                                        {{-- Edit Data --}}
                                        <a href="{{ route('supplier.edit-data', $s->id) }}" 
                                           class="btn btn-sm btn-icon btn-outline-warning waves-effect" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Edit Supplier">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </a>

                                        {{-- Delete Button --}}
                                        <button type="button" 
                                                class="btn btn-sm btn-icon btn-outline-danger waves-effect btn-delete-supplier" 
                                                data-id="{{ $s->id }}" 
                                                data-name="{{ $s->supplier }}"
                                                data-transactions="{{ $s->product_in_count + $s->purchase_count }}"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="Hapus Supplier">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                        <form id="delete-form-{{ $s->id }}" action="{{ route('supplier.delete', $s->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-xl bg-label-secondary mb-3 rounded-circle p-3">
                                            <i class="mdi mdi-domain-off fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold text-secondary mb-1">Belum Ada Data Supplier</h5>
                                        <p class="text-muted small mb-0">Klik tombol "Tambah Supplier" untuk mendaftarkan mitra pengadaan baru.</p>
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

{{-- Include Existing Create Supplier Modal --}}
@include('components.modal.warehouse.supplier.form')

@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .supplier-kpi-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .supplier-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.08) !important;
        }
        .supplier-name-link {
            transition: color 0.15s ease;
        }
        .supplier-name-link:hover {
            color: var(--bs-primary) !important;
            text-decoration: underline;
        }
        #tableSupplier thead th {
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
            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize DataTable
            var table = $('#tableSupplier').DataTable({
                order: [[1, 'asc']],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari nama supplier, telepon, email, PIC...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ supplier",
                    infoEmpty: "Menampilkan 0 s/d 0 dari 0 supplier",
                    zeroRecords: "Tidak ada supplier yang sesuai dengan kriteria pencarian",
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
            $('#supplierFilterTabs .nav-link').on('click', function () {
                $('#supplierFilterTabs .nav-link').removeClass('active');
                $(this).addClass('active');

                var filter = $(this).data('filter');
                applyCustomFilters(filter, $('#selectAreaFilter').val());
            });

            // Area Dropdown Filter
            $('#selectAreaFilter').on('change', function () {
                var area = $(this).val();
                var activeTab = $('#supplierFilterTabs .nav-link.active').data('filter');
                applyCustomFilters(activeTab, area);
            });

            function applyCustomFilters(filterType, areaValue) {
                $.fn.dataTable.ext.search = [];

                $.fn.dataTable.ext.search.push(function (settings, data, dataIndex, rowData, counter) {
                    var row = table.row(dataIndex).node();
                    var rowCategory = $(row).attr('data-category');
                    var rowActive = $(row).attr('data-active');
                    var rowArea = ($(row).attr('data-area') || '').toLowerCase();

                    // Check Tab
                    if (filterType === 'lokal' && rowCategory !== 'lokal') return false;
                    if (filterType === 'import' && rowCategory !== 'import') return false;
                    if (filterType === 'active' && rowActive !== '1') return false;

                    // Check Area
                    if (areaValue && areaValue !== '') {
                        if (rowArea.indexOf(areaValue.toLowerCase()) === -1) return false;
                    }

                    return true;
                });

                table.draw();
            }

            // SweetAlert2 Delete Confirmation
            $(document).on('click', '.btn-delete-supplier', function () {
                var supplierId = $(this).data('id');
                var supplierName = $(this).data('name');
                var txCount = parseInt($(this).data('transactions') || 0);

                if (txCount > 0) {
                    Swal.fire({
                        title: 'Supplier Memiliki Transaksi',
                        text: supplierName + ' memiliki ' + txCount + ' riwayat transaksi pengadaan (PO / Penerimaan Barang). Hapus transaksi terkait terlebih dahulu atau nonaktifkan data.',
                        icon: 'error',
                        confirmButtonText: 'Mengerti',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    });
                    return;
                }

                Swal.fire({
                    title: 'Hapus Mitra Supplier?',
                    text: 'Anda akan menghapus data ' + supplierName + '. Tindakan ini tidak dapat dibatalkan.',
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
                        $('#delete-form-' + supplierId).submit();
                    }
                });
            });
        });
    </script>
@endpush
