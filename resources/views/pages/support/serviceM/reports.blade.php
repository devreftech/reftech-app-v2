@extends('layouts.sales.app')
@section('title', 'Service Reports Management - Service Department')

@push('before-style')
    <style>
        .clean-card {
            border: 1px solid #edf2f9;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            transition: all 0.25s ease-in-out;
            background: #fff;
        }
        .clean-card:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.07);
        }
        .sr-stat-card {
            border: 1px solid #edf2f9;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #fff;
            position: relative;
            overflow: hidden;
        }
        .sr-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .sr-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
        }
        .sr-stat-card.primary::before { background: linear-gradient(90deg, #696cff, #8592a3); }
        .sr-stat-card.warning::before { background: linear-gradient(90deg, #ffab00, #ffc107); }
        .sr-stat-card.success::before { background: linear-gradient(90deg, #71dd37, #38d67a); }
        .sr-stat-card.danger::before { background: linear-gradient(90deg, #ff3e1d, #ff6b52); }

        .sr-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .pulse-badge {
            animation: pulse-animation 2s infinite;
        }
        @keyframes pulse-animation {
            0% { box-shadow: 0 0 0 0 rgba(255, 171, 0, 0.5); }
            70% { box-shadow: 0 0 0 8px rgba(255, 171, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 171, 0, 0); }
        }

        .table-custom-head th {
            background-color: #f8f9fa !important;
            font-size: 0.76rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            font-weight: 700 !important;
            color: #566a7f !important;
            border-bottom: 2px solid #e7eaf0 !important;
            vertical-align: middle !important;
            padding: 0.75rem 0.85rem !important;
        }
        .table-custom-head td {
            vertical-align: middle !important;
            font-size: 0.85rem !important;
            padding: 0.75rem 0.85rem !important;
        }

        .avatar-tech-initial {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #e7e7ff;
            color: #696cff;
            font-weight: 700;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-circle-tech {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e7e7ff;
            color: #696cff;
            font-weight: 700;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(105, 108, 255, 0.2);
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .avatar-circle-tech:hover {
            transform: scale(1.12);
            box-shadow: 0 4px 8px rgba(105, 108, 255, 0.35);
        }

        .avatar-circle-sales {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #d7f5fc;
            color: #03c3ec;
            font-weight: 700;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(3, 195, 236, 0.2);
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .avatar-circle-sales:hover {
            transform: scale(1.12);
            box-shadow: 0 4px 8px rgba(3, 195, 236, 0.35);
        }

        /* Nav Tabs Custom Styling */
        .sr-nav-tabs {
            border-bottom: 1px solid #e7eaf0;
            padding: 0.5rem 1.25rem 0;
            gap: 6px;
            background: #fbfcfe;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }
        .sr-nav-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.75rem 1.15rem;
            font-weight: 600;
            font-size: 0.88rem;
            color: #64748b;
            background: transparent;
            border-radius: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease-in-out;
        }
        .sr-nav-tabs .nav-link:hover {
            color: #334155;
            background: rgba(105, 108, 255, 0.04);
        }
        .sr-nav-tabs .nav-link.active {
            color: #696cff;
            font-weight: 700;
            border-bottom-color: #696cff;
            background: #fff;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .sr-nav-tabs .nav-link.tab-waiting.active {
            color: #ffab00;
            border-bottom-color: #ffab00;
        }
        .sr-nav-tabs .nav-link.tab-approved.active {
            color: #71dd37;
            border-bottom-color: #71dd37;
        }
        .sr-nav-tabs .nav-link.tab-rejected.active {
            color: #ff3e1d;
            border-bottom-color: #ff3e1d;
        }

        .tab-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 12px;
        }

        .table-filter-input {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.78rem;
            padding: 0.35rem 0.55rem;
            width: 100%;
            transition: border-color 0.2s;
        }
        .table-filter-input:focus {
            border-color: #696cff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(105, 108, 255, 0.15);
        }

        .table-filter-select {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.78rem;
            padding: 0.35rem 0.55rem;
            width: 100%;
            background-color: #fff;
            color: #475569;
            font-weight: 500;
            transition: border-color 0.2s;
        }
        .table-filter-select:focus {
            border-color: #696cff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(105, 108, 255, 0.15);
        }
    </style>
@endpush

@section('content')
    {{-- ===== HEADER & ACTION BAR ===== --}}
    <div class="card clean-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="badge bg-label-primary fs-6 px-3 py-1">
                            <i class="mdi mdi-wrench-clock-outline me-1"></i>
                            Service Department
                        </span>
                        <span class="text-muted fw-semibold fs-6">Service Manager Portal</span>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark">Service Reports & Approval Hub</h4>
                    <p class="text-muted mb-0 small">
                        Pusat kendali laporan servis: tinjau antrean approval, verifikasi checklist teknis, dan kelola arsip laporan secara terpusat.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('service-reports.create') }}" class="btn btn-primary waves-effect waves-light shadow-sm">
                        <i class="mdi mdi-plus-circle-outline me-1"></i> Buat Service Report Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== KPI METRICS CARDS ===== --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Reports This Year --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card sr-stat-card primary h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Total Laporan {{ $currentYear }}</span>
                        <div class="sr-icon-box bg-label-primary">
                            <i class="mdi mdi-clipboard-text-multiple-outline"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark" id="kpi-total-year">{{ number_format($totalReportsYear) }}</h3>
                    <div class="d-flex align-items-center text-muted small">
                        <i class="mdi mdi-calendar-check text-primary me-1"></i>
                        <span><strong>{{ number_format($totalReportsMonth) }}</strong> laporan di bulan {{ \Carbon\Carbon::now()->isoFormat('MMMM') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Pending Approval --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card sr-stat-card warning h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Menunggu Approval</span>
                        <div class="sr-icon-box bg-label-warning {{ $pendingCount > 0 ? 'pulse-badge' : '' }}" id="kpi-pending-icon">
                            <i class="mdi mdi-clock-alert-outline"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-1">
                        <h3 class="fw-bold mb-0 text-dark" id="kpi-pending-count">{{ number_format($pendingCount) }}</h3>
                        <span class="badge bg-label-warning rounded-pill px-2 py-0 {{ $pendingCount > 0 ? '' : 'd-none' }}" id="kpi-pending-badge" style="font-size: 0.72rem;">Perlu Aksi</span>
                    </div>
                    <div class="d-flex align-items-center text-muted small">
                        <i class="mdi mdi-alert-circle-outline text-warning me-1"></i>
                        <span>Antrean butuh tinjauan Service Manager</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Approved --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card sr-stat-card success h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Disetujui (Approved)</span>
                        <div class="sr-icon-box bg-label-success">
                            <i class="mdi mdi-check-decagram-outline"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark" id="kpi-approved-count">{{ number_format($approvedYearCount) }}</h3>
                    <div class="d-flex align-items-center text-muted small">
                        <i class="mdi mdi-draw-pen text-success me-1"></i>
                        <span><strong>{{ number_format($signedCount) }}</strong> telah ditandatangani customer</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Rejected / Revision --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card sr-stat-card danger h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Ditolak / Revisi</span>
                        <div class="sr-icon-box bg-label-danger">
                            <i class="mdi mdi-alert-octagon-outline"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark" id="kpi-rejected-count">{{ number_format($rejectedYearCount) }}</h3>
                    <div class="d-flex align-items-center text-muted small">
                        <i class="mdi mdi-undo-variant text-danger me-1"></i>
                        <span>Dikembalikan ke teknisi untuk perbaikan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== COMBINED MASTER CARD WITH TABS ===== --}}
    <div class="card clean-card mb-4">
        {{-- Card Header: Title & Filter Tahun --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-4 pb-3 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <div class="sr-icon-box bg-label-primary text-primary" style="width: 42px; height: 42px; font-size: 1.3rem;">
                    <i class="mdi mdi-database-cog-outline"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Data Service Reports</h5>
                    <small class="text-muted">Kelola seluruh persetujuan dan riwayat laporan servis dalam satu tampilan terpadu</small>
                </div>
            </div>

            {{-- Year Selector --}}
            <div class="d-flex align-items-center gap-2">
                <label for="sr-manager-year-filter" class="form-label mb-0 fw-semibold small text-muted">
                    <i class="mdi mdi-calendar-range me-1"></i>Tahun:
                </label>
                <select class="form-select form-select-sm w-auto shadow-none fw-semibold" id="sr-manager-year-filter">
                    @php $cy = date('Y'); @endphp
                    @for ($y = $cy; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $y == $cy ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- Nav Tabs: Status Categories --}}
        <ul class="nav sr-nav-tabs" id="srStatusTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active tab-all" data-bs-toggle="tab" data-status-filter="" type="button" role="tab">
                    <i class="mdi mdi-format-list-bulleted"></i>
                    <span>Semua Laporan</span>
                    <span class="tab-badge bg-label-secondary" id="badge-tab-all">{{ number_format($totalReportsYear) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-waiting" data-bs-toggle="tab" data-status-filter="Pending" type="button" role="tab">
                    <i class="mdi mdi-clock-alert-outline text-warning"></i>
                    <span>Menunggu Approval (Waiting)</span>
                    <span class="tab-badge bg-warning text-white" id="badge-tab-waiting">{{ number_format($pendingCount) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-approved" data-bs-toggle="tab" data-status-filter="Approved" type="button" role="tab">
                    <i class="mdi mdi-check-decagram-outline text-success"></i>
                    <span>Disetujui (Approved)</span>
                    <span class="tab-badge bg-label-success" id="badge-tab-approved">{{ number_format($approvedYearCount) }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link tab-rejected" data-bs-toggle="tab" data-status-filter="Rejected" type="button" role="tab">
                    <i class="mdi mdi-alert-octagon-outline text-danger"></i>
                    <span>Ditolak / Revisi (Rejected)</span>
                    <span class="tab-badge bg-label-danger" id="badge-tab-rejected">{{ number_format($rejectedYearCount) }}</span>
                </button>
            </li>
        </ul>

        {{-- Table Container --}}
        <div class="card-datatable table-responsive pt-2 px-3 pb-3">
            <table class="table table-hover table-custom-head table-bordered w-100" id="table-sr-manager">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 140px;">No Service</th>
                        <th class="text-center">Perusahaan</th>
                        <th class="text-center">Job Desc</th>
                        <th class="text-center">Unit / Mesin</th>
                        <th class="text-center" style="width: 100px;">Tanggal</th>
                        <th class="text-center" style="width: 75px;">Teknisi</th>
                        <th class="text-center" style="width: 75px;">Sales</th>
                        <th class="text-center" style="width: 110px;">Status</th>
                    </tr>
                    <tr class="filters-row">
                        <th><input type="text" class="table-filter-input" placeholder="Cari No..." /></th>
                        <th><input type="text" class="table-filter-input" placeholder="Cari Perusahaan..." /></th>
                        <th><input type="text" class="table-filter-input" placeholder="Cari Job Desc..." /></th>
                        <th><input type="text" class="table-filter-input" placeholder="Cari Unit / Serial..." /></th>
                        <th><input type="text" class="table-filter-input" placeholder="Cari Tanggal..." /></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th>
                            <select class="table-filter-select" id="column-status-select">
                                <option value="">Semua Status</option>
                                <option value="Approved">Approved</option>
                                <option value="Pending">Pending</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            var currentStatusFilter = "";

            // Helper to escape HTML safely
            function escapeHtml(str) {
                if (!str) return '';
                return $('<div>').text(str).html();
            }

            // Initialize DataTable
            var tableEl = $("#table-sr-manager");
            var dt = tableEl.DataTable({
                ajax: {
                    type: "GET",
                    url: "{{ url('db/reports/admin') }}",
                    headers: { "Content-Type": "application/json" },
                    data: function(d) {
                        d.year = $("#sr-manager-year-filter").val();
                    },
                    dataSrc: function(json) {
                        var list = json.data || [];
                        var total = list.length;
                        var pending = 0;
                        var approved = 0;
                        var rejected = 0;

                        list.forEach(function(item) {
                            var st = (item.approval_status || 'pending').toLowerCase();
                            if (st === 'approved') approved++;
                            else if (st === 'rejected') rejected++;
                            else pending++;
                        });

                        // Update Tab Badges
                        $('#badge-tab-all').text(total);
                        $('#badge-tab-waiting').text(pending);
                        $('#badge-tab-approved').text(approved);
                        $('#badge-tab-rejected').text(rejected);

                        // Update KPI cards
                        $('#kpi-total-year').text(total);
                        $('#kpi-pending-count').text(pending);
                        $('#kpi-approved-count').text(approved);
                        $('#kpi-rejected-count').text(rejected);

                        if (pending > 0) {
                            $('#kpi-pending-badge').removeClass('d-none');
                            $('#kpi-pending-icon').addClass('pulse-badge');
                        } else {
                            $('#kpi-pending-badge').addClass('d-none');
                            $('#kpi-pending-icon').removeClass('pulse-badge');
                        }

                        return list;
                    }
                },
                columns: [
                    { data: "no_service" },
                    { data: "company" },
                    { data: "jobdesc" },
                    { data: "brand_type" },
                    { data: "date" },
                    { data: "technician" },
                    { data: "sales" },
                    { data: "approval_status", defaultContent: "pending" }
                ],
                columnDefs: [
                    // Col 0: No Service + Type badge
                    {
                        targets: 0,
                        className: "text-nowrap",
                        render: function(data, type, row) {
                            if (type !== 'display') return data || '';
                            var detailUrl = "{{ url('service-reports') }}/" + row.id;
                            var typeColor = 'primary';
                            if (row.type === 'Visit') typeColor = 'info';
                            else if (row.type === 'Service') typeColor = 'danger';
                            else if (row.type === 'General') typeColor = 'success';

                            var html = '<a class="fw-bold text-primary d-flex align-items-center gap-1" href="' + detailUrl + '">';
                            html += '<i class="mdi mdi-file-document-outline"></i> ' + escapeHtml(data || '-') + '</a>';
                            html += '<div class="mt-1 d-flex align-items-center gap-1 flex-wrap">';
                            if (row.type) {
                                html += '<span class="badge bg-label-' + typeColor + ' rounded-pill" style="font-size: 0.68rem;">' + escapeHtml(row.type) + '</span>';
                            }
                            if (row.pm_level) {
                                html += '<span class="badge bg-label-secondary rounded-pill" style="font-size: 0.68rem;">PM ' + escapeHtml(row.pm_level) + '</span>';
                            }
                            html += '</div>';
                            return html;
                        }
                    },
                    // Col 1: Company
                    {
                        targets: 1,
                        render: function(data) {
                            return '<span class="fw-semibold text-dark">' + escapeHtml(data || '-') + '</span>';
                        }
                    },
                    // Col 2: Job Desc
                    {
                        targets: 2,
                        render: function(data) {
                            if (!data) return '-';
                            var clean = String(data);
                            var truncated = clean.length > 50 ? clean.substring(0, 50) + '...' : clean;
                            return '<span class="text-muted small" data-bs-toggle="tooltip" title="' + escapeHtml(clean) + '">' + escapeHtml(truncated) + '</span>';
                        }
                    },
                    // Col 3: Brand & Type + Serial / Tag (Combined)
                    {
                        targets: 3,
                        render: function(data, type, row) {
                            if (type !== 'display') return (data || '') + ' ' + (row.serial_tag || '');
                            var brand = escapeHtml(data || '-');
                            var serial = escapeHtml(row.serial_tag || '-');
                            return '<div>' +
                                '<div class="fw-semibold text-dark small mb-1">' + brand + '</div>' +
                                '<div class="small text-muted font-monospace"><i class="mdi mdi-tag-outline me-1"></i>' + serial + '</div>' +
                                '</div>';
                        }
                    },
                    // Col 4: Date
                    {
                        targets: 4,
                        className: "text-center text-nowrap",
                        render: function(data, type) {
                            if (type === 'display') {
                                return data ? '<i class="mdi mdi-calendar-outline text-muted me-1"></i>' + moment(data).format('DD-MM-YYYY') : '-';
                            }
                            return data;
                        }
                    },
                    // Col 5: Technician (Avatar)
                    {
                        targets: 5,
                        className: "text-center text-nowrap",
                        render: function(data) {
                            if (!data) return '<span class="text-muted small">-</span>';
                            var name = String(data).trim();
                            var parts = name.split(/\s+/);
                            var initial = parts.length >= 2 
                                ? (parts[0][0] + parts[1][0]).toUpperCase() 
                                : name.substring(0, 2).toUpperCase();

                            return '<div class="avatar-circle-tech mx-auto" data-bs-toggle="tooltip" data-bs-placement="top" title="Teknisi: ' + escapeHtml(name) + '">' +
                                escapeHtml(initial) +
                                '</div>';
                        }
                    },
                    // Col 6: Sales (Avatar)
                    {
                        targets: 6,
                        className: "text-center text-nowrap",
                        render: function(data) {
                            if (!data) return '<span class="text-muted small">-</span>';
                            var name = String(data).trim();
                            var parts = name.split(/\s+/);
                            var initial = parts.length >= 2 
                                ? (parts[0][0] + parts[1][0]).toUpperCase() 
                                : name.substring(0, 2).toUpperCase();

                            return '<div class="avatar-circle-sales mx-auto" data-bs-toggle="tooltip" data-bs-placement="top" title="Sales: ' + escapeHtml(name) + '">' +
                                escapeHtml(initial) +
                                '</div>';
                        }
                    },
                    // Col 7: Status
                    {
                        targets: 7,
                        className: "text-center text-nowrap",
                        render: function(data, type, row) {
                            var status = (data || 'pending').toLowerCase();
                            if (type !== 'display') return status;

                            if (status === 'approved') {
                                return '<span class="badge bg-label-success rounded-pill"><i class="mdi mdi-check-circle-outline me-1"></i> Approved</span>';
                            } else if (status === 'rejected') {
                                var note = row.reject_note ? ' data-bs-toggle="tooltip" title="' + escapeHtml(row.reject_note) + '"' : '';
                                return '<span class="badge bg-label-danger rounded-pill"' + note + '><i class="mdi mdi-close-circle-outline me-1"></i> Rejected</span>';
                            } else {
                                return '<span class="badge bg-label-warning rounded-pill"><i class="mdi mdi-clock-outline me-1"></i> Pending</span>';
                            }
                        }
                    }
                ],
                order: [[0, "desc"]],
                dom: '<"row align-items-center mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row align-items-center mt-3"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"p>>',
                lengthMenu: [10, 25, 50, 75, 100],
                displayLength: 10,
                drawCallback: function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            // Column Filters logic (Row 2 of header - text inputs)
            $('.filters-row th input').on('keyup change', function() {
                var colIdx = $(this).closest('th').index();
                if (dt.column(colIdx).search() !== this.value) {
                    dt.column(colIdx).search(this.value).draw();
                }
            });

            // Column Status Select Filter Change Handler
            $('#column-status-select').on('change', function() {
                var status = $(this).val();
                currentStatusFilter = status;

                // Sync active nav tab
                $('#srStatusTabs button').removeClass('active');
                if (status === 'Pending') {
                    $('#srStatusTabs button.tab-waiting').addClass('active');
                } else if (status === 'Approved') {
                    $('#srStatusTabs button.tab-approved').addClass('active');
                } else if (status === 'Rejected') {
                    $('#srStatusTabs button.tab-rejected').addClass('active');
                } else {
                    $('#srStatusTabs button.tab-all').addClass('active');
                }

                if (status) {
                    dt.column(7).search('^' + status + '$', true, false).draw();
                } else {
                    dt.column(7).search('').draw();
                }
            });

            // Year Filter Change Handler
            $('#sr-manager-year-filter').on('change', function() {
                dt.ajax.reload();
            });

            // Status Tabs Click Handler
            $('#srStatusTabs button[data-bs-toggle="tab"]').on('click', function() {
                var status = $(this).data('status-filter');
                currentStatusFilter = status;

                // Sync status select dropdown
                $('#column-status-select').val(status);

                // Apply status filter on column 7 (Status)
                if (status) {
                    dt.column(7).search('^' + status + '$', true, false).draw();
                } else {
                    dt.column(7).search('').draw();
                }
            });

            // Quick Approve Handler with AJAX
            $(document).on('click', '.btn-quick-approve', function(e) {
                e.preventDefault();
                var reportId = $(this).data('id');
                var reportNo = $(this).data('no');

                Swal.fire({
                    title: 'Setujui Service Report?',
                    html: `Apakah Anda yakin ingin menyetujui laporan <strong>#${reportNo}</strong>?<br><small class="text-muted">Setelah disetujui, laporan ini akan dapat diakses oleh tim Sales dan siap dipublikasikan ke customer.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="mdi mdi-check me-1"></i> Ya, Setujui Laporan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-success waves-effect waves-light me-2',
                        cancelButton: 'btn btn-label-secondary waves-effect'
                    },
                    buttonsStyling: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function() {
                        return $.ajax({
                            url: '{{ url("service-reports") }}/' + reportId + '/approve',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            dataType: 'json'
                        }).then(function(response) {
                            return response;
                        }).catch(function(error) {
                            Swal.showValidationMessage(
                                (error.responseJSON && error.responseJSON.message) 
                                    ? error.responseJSON.message 
                                    : 'Terjadi kesalahan saat menyetujui laporan.'
                            );
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Disetujui!',
                            text: result.value.message || 'Service report berhasil disetujui.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                            buttonsStyling: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                        dt.ajax.reload(null, false);
                    }
                });
            });

            // Quick Reject Handler with AJAX
            $(document).on('click', '.btn-quick-reject', function(e) {
                e.preventDefault();
                var reportId = $(this).data('id');
                var reportNo = $(this).data('no');

                Swal.fire({
                    title: 'Tolak Service Report',
                    html: `Tuliskan alasan penolakan/catatan perbaikan untuk laporan <strong>#${reportNo}</strong> agar teknisi dapat memperbaikinya:`,
                    input: 'textarea',
                    inputPlaceholder: 'Contoh: Foto checklist pekerjaan belum lengkap, mohon lampirkan foto pressure bar...',
                    inputAttributes: {
                        'aria-label': 'Tulis alasan penolakan di sini',
                        'rows': 4,
                        'style': 'font-size: 0.9rem;'
                    },
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="mdi mdi-close-thick me-1"></i> Tolak & Kirim Catatan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        confirmButton: 'btn btn-danger waves-effect waves-light me-2',
                        cancelButton: 'btn btn-label-secondary waves-effect'
                    },
                    buttonsStyling: false,
                    showLoaderOnConfirm: true,
                    inputValidator: (value) => {
                        if (!value || !value.trim()) {
                            return 'Alasan penolakan wajib diisi agar teknisi mengetahui apa yang harus diperbaiki!';
                        }
                    },
                    preConfirm: function(rejectNote) {
                        return $.ajax({
                            url: '{{ url("service-reports") }}/' + reportId + '/reject',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                reject_note: rejectNote
                            },
                            dataType: 'json'
                        }).then(function(response) {
                            return response;
                        }).catch(function(error) {
                            Swal.showValidationMessage(
                                (error.responseJSON && error.responseJSON.message) 
                                    ? error.responseJSON.message 
                                    : 'Terjadi kesalahan saat menolak laporan.'
                            );
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then(function(result) {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Laporan Dikembalikan',
                            text: result.value.message || 'Service report ditolak dan dikembalikan ke teknisi.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                            buttonsStyling: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                        dt.ajax.reload(null, false);
                    }
                });
            });
        });
    </script>
@endpush
