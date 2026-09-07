@extends('layouts.sales.app')
@section('title', 'Laporan Laba Rugi per Proyek (Project Profitability)')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .metric-card {
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
        }
        .badge.badge-healthy,
        .badge-healthy {
            background-color: #d1fae5 !important;
            color: #065f46 !important;
            border: 1px solid #6ee7b7 !important;
            font-weight: 700 !important;
        }
        .badge.badge-moderate,
        .badge-moderate {
            background-color: #fef3c7 !important;
            color: #92400e !important;
            border: 1px solid #fcd34d !important;
            font-weight: 700 !important;
        }
        .badge.badge-critical,
        .badge-critical {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
            border: 1px solid #fca5a5 !important;
            font-weight: 700 !important;
        }
        .filter-btn-health {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .filter-btn-health:hover {
            transform: scale(1.03);
            filter: brightness(0.95);
        }
        .filter-btn-health.active-filter {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5) !important;
        }
    </style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Reports / Accounting /</span> Analisa Laba Rugi per Proyek
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-chart-box-outline me-1"></i> Evaluasi real-time Revenue PO vs Modal Sparepart (HPP) vs Beban Operasional Lapangan (AP)
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="mdi mdi-printer me-1"></i> Cetak Laporan
            </button>
        </div>
    </div>

    {{-- Executive Summary KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm metric-card h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Total Revenue (PO)</span>
                        <span class="avatar-initial rounded bg-label-primary p-2">
                            <i class="mdi mdi-cash-multiple fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-primary mb-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">{{ count($projects) }} Kontrak / PO Terdata</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm metric-card h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Total HPP Sparepart/Unit</span>
                        <span class="avatar-initial rounded bg-label-warning p-2">
                            <i class="mdi mdi-cog-box fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-warning mb-1">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Modal material unit/part terpasang</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm metric-card h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Beban Operasional (AP)</span>
                        <span class="avatar-initial rounded bg-label-info p-2">
                            <i class="mdi mdi-truck-fast-outline fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-info mb-1">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Transport, akomodasi, konsum, dll</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm metric-card h-100" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                <div class="card-body p-3 text-white">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white-50 small fw-semibold text-uppercase" style="font-size: 11px;">Total Laba Bersih Proyek</span>
                        <span class="badge bg-white text-primary rounded-pill px-2 py-1 fw-bold" style="font-size: 12px;">
                            {{ $overallMargin }}% Margin
                        </span>
                    </div>
                    <h4 class="fw-bolder text-white mb-1">Rp {{ number_format($totalNetProfit, 0, ',', '.') }}</h4>
                    <small class="text-white-50" style="font-size: 11px;">Revenue - (HPP + Biaya AP)</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Project Health Distribution Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-3">
                    <span class="fw-bold text-dark d-block">Distribusi Margin Proyek</span>
                    <small class="text-muted">Klik kategori untuk filter cepat:</small>
                </div>
                <div class="col-12 col-md-9">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <a href="{{ request()->fullUrlWithQuery(['health' => '']) }}"
                           class="badge filter-btn-health {{ empty($healthFilter) ? 'bg-primary text-white active-filter' : 'bg-label-secondary' }} px-3 py-2 rounded-pill fs-6 text-decoration-none fw-bold">
                            <i class="mdi mdi-view-grid-outline me-1"></i> Semua ({{ $healthyCount + $moderateCount + $criticalCount }})
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['health' => 'healthy']) }}"
                           class="badge badge-healthy filter-btn-health px-3 py-2 rounded-pill fs-6 text-decoration-none {{ $healthFilter === 'healthy' ? 'active-filter' : '' }}">
                            <i class="mdi mdi-check-circle-outline me-1"></i> {{ $healthyCount }} Proyek Sehat (&ge; 25%)
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['health' => 'moderate']) }}"
                           class="badge badge-moderate filter-btn-health px-3 py-2 rounded-pill fs-6 text-decoration-none {{ $healthFilter === 'moderate' ? 'active-filter' : '' }}">
                            <i class="mdi mdi-alert-outline me-1"></i> {{ $moderateCount }} Proyek Moderat (10-24%)
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['health' => 'critical']) }}"
                           class="badge badge-critical filter-btn-health px-3 py-2 rounded-pill fs-6 text-decoration-none {{ $healthFilter === 'critical' ? 'active-filter' : '' }}">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> {{ $criticalCount }} Proyek Kritis (&lt; 10%)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('report.project_profitability') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">Filter Customer / Klien</label>
                    <select name="client_id" class="form-select select2">
                        <option value="">-- Semua Customer --</option>
                        @foreach ($clients as $c)
                            <option value="{{ $c->id }}" {{ $selectedClientId == $c->id ? 'selected' : '' }}>
                                {{ $c->company }} {{ $c->ru ? '(' . $c->ru . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold small">Dari Tanggal PO</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold small">Sampai Tanggal PO</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label fw-semibold small">Status Project</label>
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="1" {{ $selectedStatus === '1' ? 'selected' : '' }}>Aktif / Running</option>
                        <option value="0" {{ $selectedStatus === '0' ? 'selected' : '' }}>Pending / New</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-grid gap-2 d-md-flex">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="mdi mdi-filter-outline me-1"></i> Filter
                    </button>
                    <a href="{{ route('report.project_profitability') }}" class="btn btn-label-secondary" title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Projects Table Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="mdi mdi-file-table-box-multiple-outline text-primary"></i> Rincian Profitabilitas per Proyek (PO)
            </h5>
            <span class="badge bg-label-primary px-3 py-1 rounded-pill">Total {{ count($projects) }} Data</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="profitabilityTable">
                <thead class="bg-light">
                    <tr style="font-size: 12px;">
                        <th class="text-uppercase fw-bold text-muted py-3 px-3">No. PO &amp; Tanggal</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3">Customer &amp; Deskripsi</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-end">Revenue (PO)</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-end">HPP Sparepart</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-end">Biaya AP Operasional</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-end">Laba Bersih</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-center">Margin %</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-center">Status Laba</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px;">
                    @forelse ($projects as $item)
                        @php
                            $healthClass = 'badge-moderate';
                            $healthLabel = 'Moderat';
                            $healthIcon = 'mdi-alert-outline';

                            if ($item['health_status'] === 'healthy') {
                                $healthClass = 'badge-healthy';
                                $healthLabel = 'Sehat';
                                $healthIcon = 'mdi-check-circle-outline';
                            } elseif ($item['health_status'] === 'critical') {
                                $healthClass = 'badge-critical';
                                $healthLabel = 'Kritis';
                                $healthIcon = 'mdi-alert-circle-outline';
                            }
                        @endphp
                        <tr>
                            <td class="px-3">
                                <span class="fw-bold text-dark d-block">
                                    {{ $item['po_number'] }}
                                </span>
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="mdi mdi-calendar-blank-outline me-1"></i>{{ $item['po_date'] ? Carbon\Carbon::parse($item['po_date'])->format('d M Y') : '-' }}
                                </small>
                            </td>
                            <td class="px-3">
                                <span class="fw-bold text-dark d-block">{{ $item['client_name'] }}</span>
                                <small class="text-muted d-block text-truncate" style="max-width: 250px;" title="{{ $item['project_name'] }}">
                                    {{ $item['project_name'] ?: 'Project Maintenance / Spareparts' }}
                                </small>
                            </td>
                            <td class="px-3 text-end fw-bold text-dark">
                                Rp {{ number_format($item['revenue'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 text-end text-warning fw-semibold">
                                Rp {{ number_format($item['hpp'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 text-end text-info fw-semibold">
                                Rp {{ number_format($item['expenses'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 text-end fw-bold {{ $item['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($item['net_profit'], 0, ',', '.') }}
                            </td>
                            <td class="px-3 text-center">
                                <span class="fw-bold {{ $item['margin_percent'] >= 25 ? 'text-success' : ($item['margin_percent'] >= 10 ? 'text-warning' : 'text-danger') }}">
                                    {{ $item['margin_percent'] }}%
                                </span>
                            </td>
                            <td class="px-3 text-center">
                                <span class="badge {{ $healthClass }} rounded-pill px-2.5 py-1" style="font-size: 11px;">
                                    <i class="mdi {{ $healthIcon }} me-1"></i> {{ $healthLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <em>Tidak ada data proyek atau purchase order yang ditemukan sesuai filter.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($projects) > 0)
                    <tfoot class="bg-light fw-bold">
                        <tr style="font-size: 13px;">
                            <td colspan="2" class="text-uppercase text-end px-3 py-3">Total Ringkasan:</td>
                            <td class="text-end px-3 py-3 text-primary">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            <td class="text-end px-3 py-3 text-warning">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                            <td class="text-end px-3 py-3 text-info">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                            <td class="text-end px-3 py-3 {{ $totalNetProfit >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format($totalNetProfit, 0, ',', '.') }}</td>
                            <td class="text-center px-3 py-3 text-primary">{{ $overallMargin }}%</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            if ($('#profitabilityTable tbody tr').length > 1 || !$('#profitabilityTable tbody tr td[colspan]').length) {
                $('#profitabilityTable').DataTable({
                    order: [[2, 'desc']], // Sort by Revenue desc
                    pageLength: 25,
                    language: {
                        search: "Cari Proyek / Customer:",
                        lengthMenu: "Tampilkan _MENU_ baris",
                        info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ proyek",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });
            }
        });
    </script>
@endpush
