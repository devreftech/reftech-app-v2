@extends('layouts.sales.app')
@section('title', 'Daftar Aktiva Tetap (Fixed Asset) - Finance')

@section('content')
    {{-- Top Header Action Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 fs-6">
                    <li class="breadcrumb-item"><a href="{{ route('finance.statement.index') }}">Finance</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Aktiva Tetap (Fixed Asset)</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-office-building-cog text-primary"></i> Manajemen Aktiva Tetap (Fixed Asset)
                <span class="badge bg-label-primary fs-6">{{ $totalCount }} Total Aset</span>
            </h4>
            <p class="text-muted mb-0 small">
                Inventaris dan nilai buku aset tetap perusahaan: tanah, bangunan, armada kendaraan, mesin operasional, peralatan kantor, dan tools teknisi.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('expense-balance.index') }}" class="btn btn-label-secondary btn-sm shadow-xs">
                <i class="mdi mdi-scale-balance me-1"></i> Posisi di Neraca
            </a>
            <a href="{{ route('fixed.create') }}" class="btn btn-primary btn-sm shadow-xs">
                <i class="mdi mdi-plus me-1"></i> Tambah Aset Tetap
            </a>
        </div>
    </div>

    {{-- 4 Executive KPI Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Nilai Perolehan (Gross Asset) --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-asset-card" style="background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%); border-left: 4px solid #6366f1 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Nilai Perolehan (Gross)</span>
                        <div class="avatar avatar-sm bg-label-primary rounded">
                            <i class="mdi mdi-cube-send fs-5 text-primary"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-primary mb-1">Rp {{ number_format($totalNilaiPerolehan, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        <span>Total Nilai Beli &amp; Kapitalisasi {{ $totalCount }} Aset</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Akumulasi Penyusutan --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-asset-card" style="background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%); border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Akumulasi Penyusutan</span>
                        <div class="avatar avatar-sm bg-label-warning rounded">
                            <i class="mdi mdi-trending-down fs-5 text-warning"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-warning mb-1">Rp {{ number_format($totalPenyusutan, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        <span>Metode Garis Lurus (25% per tahun)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Nilai Buku Bersih (Net Book Value) --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-asset-card" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-left: 4px solid #10b981 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Nilai Buku Bersih (NBV)</span>
                        <div class="avatar avatar-sm bg-label-success rounded">
                            <i class="mdi mdi-shield-check-outline fs-5 text-success"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-success mb-1">Rp {{ number_format($totalNilaiBuku, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        <span>Nilai Tercatat pada Neraca Keuangan</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Kategori Inventaris --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 kpi-asset-card" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%); border-left: 4px solid #06b6d4 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Distribusi Kategori</span>
                        <div class="avatar avatar-sm bg-label-info rounded">
                            <i class="mdi mdi-shape-plus fs-5 text-info"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-dark mb-1">6 Kategori Aset</h5>
                    <div class="text-muted small text-truncate" style="font-size: 11px;" title="Tools: {{ $assetCounts['Tools']->count ?? 0 }} | Mesin: {{ $assetCounts['Mesin']->count ?? 0 }} | Kendaraan: {{ $assetCounts['Kendaraan']->count ?? 0 }}">
                        <span>Tools ({{ $assetCounts['Tools']->count ?? 0 }})</span> &bull; 
                        <span>Mesin ({{ $assetCounts['Mesin']->count ?? 0 }})</span> &bull; 
                        <span>Kdr ({{ $assetCounts['Kendaraan']->count ?? 0 }})</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Asset Inventory Card --}}
    <div class="card border-0 shadow-sm rounded-3">
        {{-- Card Header with Navigation Tabs --}}
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs nav-tabs-asset border-0 m-0 px-3 pt-2" id="fixed-asset-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active d-flex align-items-center gap-1 py-3" data-bs-toggle="tab" data-bs-target="#tab-tanah" type="button">
                        <i class="mdi mdi-terrain fs-5"></i>
                        <span class="fw-semibold">Tanah</span>
                        <span class="badge rounded-pill bg-label-secondary ms-1" id="badge-tanah">{{ $assetCounts['Tanah']->count ?? 0 }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link d-flex align-items-center gap-1 py-3" data-bs-toggle="tab" data-bs-target="#tab-bangunan" type="button">
                        <i class="mdi mdi-office-building fs-5"></i>
                        <span class="fw-semibold">Bangunan</span>
                        <span class="badge rounded-pill bg-label-secondary ms-1" id="badge-bangunan">{{ $assetCounts['Bangunan']->count ?? 0 }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link d-flex align-items-center gap-1 py-3" data-bs-toggle="tab" data-bs-target="#tab-kendaraan" type="button">
                        <i class="mdi mdi-car fs-5"></i>
                        <span class="fw-semibold">Kendaraan</span>
                        <span class="badge rounded-pill bg-label-primary ms-1" id="badge-kendaraan">{{ $assetCounts['Kendaraan']->count ?? 0 }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link d-flex align-items-center gap-1 py-3" data-bs-toggle="tab" data-bs-target="#tab-mesin" type="button">
                        <i class="mdi mdi-cog-outline fs-5"></i>
                        <span class="fw-semibold">Mesin Unit</span>
                        <span class="badge rounded-pill bg-label-info ms-1" id="badge-mesin">{{ $assetCounts['Mesin']->count ?? 0 }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link d-flex align-items-center gap-1 py-3" data-bs-toggle="tab" data-bs-target="#tab-peralatan" type="button">
                        <i class="mdi mdi-desktop-classic fs-5"></i>
                        <span class="fw-semibold">Peralatan Kantor</span>
                        <span class="badge rounded-pill bg-label-secondary ms-1" id="badge-peralatan">{{ $assetCounts['Peralatan Kantor']->count ?? 0 }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link d-flex align-items-center gap-1 py-3" data-bs-toggle="tab" data-bs-target="#tab-tools" type="button">
                        <i class="mdi mdi-toolbox-outline fs-5"></i>
                        <span class="fw-semibold">Tools Teknisi</span>
                        <span class="badge rounded-pill bg-label-warning ms-1" id="badge-tools">{{ $assetCounts['Tools']->count ?? 0 }}</span>
                    </button>
                </li>
            </ul>
        </div>

        {{-- Card Body / Tab Panes --}}
        <div class="card-body p-3 p-md-4">
            <div class="tab-content p-0">
                {{-- TAB 1: TANAH --}}
                <div class="tab-pane fade show active" id="tab-tanah">
                    <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded-3 bg-light border">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-terrain fs-4 text-primary"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Kategori: Tanah</h6>
                                <small class="text-muted">Aset properti tanah hak milik dan operasional perusahaan</small>
                            </div>
                        </div>
                        <span class="badge bg-label-primary px-3 py-2">
                            Total Nilai: Rp {{ number_format($assetCounts['Tanah']->total_val ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="datatable-fixed-generic table table-hover align-middle mb-0" data-type="Tanah" data-badge="badge-tanah" style="width: 100%;">
                            <thead class="table-light text-uppercase">
                                <tr>
                                    <th>Kode Aset</th>
                                    <th>Keterangan / Lokasi</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga Perolehan</th>
                                    <th class="text-center">Tgl Beli</th>
                                    <th class="text-center">Tgl Pakai</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: BANGUNAN --}}
                <div class="tab-pane fade" id="tab-bangunan">
                    <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded-3 bg-light border">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-office-building fs-4 text-primary"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Kategori: Bangunan &amp; Gedung</h6>
                                <small class="text-muted">Kantor, gudang logistik, dan fasilitas operasional</small>
                            </div>
                        </div>
                        <span class="badge bg-label-primary px-3 py-2">
                            Total Nilai: Rp {{ number_format($assetCounts['Bangunan']->total_val ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="datatable-fixed-generic table table-hover align-middle mb-0" data-type="Bangunan" data-badge="badge-bangunan" style="width: 100%;">
                            <thead class="table-light text-uppercase">
                                <tr>
                                    <th>Kode Aset</th>
                                    <th>Keterangan / Gedung</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga Perolehan</th>
                                    <th class="text-center">Tgl Beli</th>
                                    <th class="text-center">Tgl Pakai</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 3: KENDARAAN --}}
                <div class="tab-pane fade" id="tab-kendaraan">
                    <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded-3 bg-light border">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-car fs-4 text-primary"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Kategori: Armada Kendaraan</h6>
                                <small class="text-muted">Mobil operasional, truk angkut, dan motor dinas lapangan</small>
                            </div>
                        </div>
                        <span class="badge bg-label-primary px-3 py-2">
                            Total Nilai: Rp {{ number_format($assetCounts['Kendaraan']->total_val ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="datatable-fixed-kendaraan table table-hover align-middle mb-0" data-badge="badge-kendaraan" style="width: 100%;">
                            <thead class="table-light text-uppercase">
                                <tr>
                                    <th>Kode Aset</th>
                                    <th>Jenis</th>
                                    <th>Merk / Model</th>
                                    <th>Plat Nomor</th>
                                    <th>Atas Nama</th>
                                    <th class="text-center">Tgl Beli</th>
                                    <th class="text-end">Harga Perolehan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 4: MESIN --}}
                <div class="tab-pane fade" id="tab-mesin">
                    <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded-3 bg-light border">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-cog-outline fs-4 text-primary"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Kategori: Mesin Unit Operasional</h6>
                                <small class="text-muted">Unit mesin chiller, kompresor, generator, dan peralatan teknis</small>
                            </div>
                        </div>
                        <span class="badge bg-label-primary px-3 py-2">
                            Total Nilai: Rp {{ number_format($assetCounts['Mesin']->total_val ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="datatable-fixed-mesin table table-hover align-middle mb-0" data-badge="badge-mesin" style="width: 100%;">
                            <thead class="table-light text-uppercase">
                                <tr>
                                    <th>Kode Aset</th>
                                    <th>Brand / Unit</th>
                                    <th>Type</th>
                                    <th>Serial Number</th>
                                    <th>Kondisi</th>
                                    <th class="text-center">Tgl Beli</th>
                                    <th class="text-end">Harga Perolehan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 5: PERALATAN KANTOR --}}
                <div class="tab-pane fade" id="tab-peralatan">
                    <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded-3 bg-light border">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-desktop-classic fs-4 text-primary"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Kategori: Peralatan Kantor &amp; IT</h6>
                                <small class="text-muted">Komputer PC, laptop, printer, server, mebeler &amp; pendingin ruangan</small>
                            </div>
                        </div>
                        <span class="badge bg-label-primary px-3 py-2">
                            Total Nilai: Rp {{ number_format($assetCounts['Peralatan Kantor']->total_val ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="datatable-fixed-generic table table-hover align-middle mb-0" data-type="Peralatan Kantor" data-badge="badge-peralatan" style="width: 100%;">
                            <thead class="table-light text-uppercase">
                                <tr>
                                    <th>Kode Aset</th>
                                    <th>Keterangan / Nama Aset</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga Perolehan</th>
                                    <th class="text-center">Tgl Beli</th>
                                    <th class="text-center">Tgl Pakai</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 6: TOOLS --}}
                <div class="tab-pane fade" id="tab-tools">
                    <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded-3 bg-light border">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-toolbox-outline fs-4 text-primary"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Kategori: Tools Teknisi Lapangan</h6>
                                <small class="text-muted">Perkakas kerja, alat ukur, dan perlengkapan servis teknisi</small>
                            </div>
                        </div>
                        <span class="badge bg-label-primary px-3 py-2">
                            Total Nilai: Rp {{ number_format($assetCounts['Tools']->total_val ?? 0, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="datatable-fixed-tools table table-hover align-middle mb-0" data-badge="badge-tools" style="width: 100%;">
                            <thead class="table-light text-uppercase">
                                <tr>
                                    <th>Kode Tools</th>
                                    <th>Nama Tools</th>
                                    <th>Teknisi Pemegang</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Tgl Serah Terima</th>
                                    <th>Status Finance</th>
                                    <th class="text-end">Harga Perolehan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />

    <style>
        body, table, th, td, h4, h5, h6, span, div, p {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        .kpi-asset-card {
            transition: all 0.2s ease-in-out;
        }
        .kpi-asset-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08) !important;
        }

        /* Nav Tabs Asset Styling */
        .nav-tabs-asset .nav-link {
            color: #64748b;
            font-size: 0.92rem;
            border: none;
            border-bottom: 3px solid transparent;
            background: transparent;
            transition: all 0.2s ease-in-out;
            padding: 0.85rem 1.25rem;
        }
        .nav-tabs-asset .nav-link:hover {
            color: #6366f1;
            background: rgba(99, 102, 241, 0.04);
        }
        .nav-tabs-asset .nav-link.active {
            color: #6366f1;
            font-weight: 700;
            border-bottom: 3px solid #6366f1;
            background: transparent;
        }

        /* Table Typography & Spacing */
        table.dataTable thead th {
            font-size: 11.5px;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            font-weight: 700;
            color: #475569;
        }
        table.dataTable tbody td {
            font-size: 13px;
            padding: 10px 14px;
            vertical-align: middle;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-fixed-asset.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
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
    </script>
@endpush
