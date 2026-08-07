<h4 class="fw-bold py-3 mb-1"><span class="text-muted fw-light">Dashboard /</span> Sales Manager</h4>
<p class="text-muted mb-4">Monitor performa tim penjualan dan pipeline secara real-time</p>

@php
    $smFmt = fn($v) => 'Rp ' . number_format($v ?? 0, 0, ',', '.');
@endphp

<!-- Row 1: KPI cards -->
<div class="row mb-2">
    <div class="col-sm-6 col-lg-4 col-xl-2 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-primary rounded"><i class="mdi mdi-target mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Target Penjualan</small>
                        <h6 class="mb-0">{{ $smFmt($smTargetMonth) }}</h6>
                    </div>
                </div>
                <small class="text-muted">Bulan Ini</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-2 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-success rounded"><i class="mdi mdi-trending-up mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Actual Penjualan</small>
                        <h6 class="mb-0">{{ $smFmt($smActualMonth) }}</h6>
                    </div>
                </div>
                <small class="{{ $smVsLastMonthPct >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="mdi {{ $smVsLastMonthPct >= 0 ? 'mdi-arrow-up' : 'mdi-arrow-down' }}"></i>
                    {{ abs($smVsLastMonthPct) }}% vs bulan lalu
                </small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-2 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-warning rounded"><i class="mdi mdi-star-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Achievement</small>
                        <h6 class="mb-0">{{ $smAchievement }}%</h6>
                    </div>
                </div>
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar bg-warning" style="width: {{ min($smAchievement, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-2 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-info rounded"><i class="mdi mdi-file-document-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Quotation</small>
                        <h6 class="mb-0">{{ $smQuotationTotal }}</h6>
                    </div>
                </div>
                <small class="text-muted">Bulan Ini</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-2 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-secondary rounded"><i class="mdi mdi-clipboard-check-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Sales Order (PO Won)</small>
                        <h6 class="mb-0">{{ $smSoTotal }}</h6>
                    </div>
                </div>
                <small class="text-muted">Bulan Ini</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4 col-xl-2 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-dark rounded"><i class="mdi mdi-receipt mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Invoice</small>
                        <h6 class="mb-0">{{ $smInvoiceTotal }}</h6>
                    </div>
                </div>
                <small class="text-muted">Bulan Ini</small>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Sales Achievement / Pipeline by Stage / Revenue by Status -->
<div class="row mb-2">
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Sales Achievement</h5>
                <small class="text-muted">Bulan Ini</small>
            </div>
            <div class="card-body">
                <div id="smAchievementChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Pipeline by Stage</h5>
                <small class="text-muted">Total {{ $smPipelineTotal }} &mdash; berdasarkan status quotation, bukan field stage terpisah</small>
            </div>
            <div class="card-body">
                <div id="smPipelineChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Revenue by Quotation Status</h5>
                <small class="text-muted">Pengganti "Revenue by Product" &mdash; kategori produk belum terstandardisasi</small>
            </div>
            <div class="card-body">
                <div id="smRevenueStatusChart"></div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Sales Team Performance -->
<div class="row mb-2">
    <div class="col-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Sales Team Performance</h5>
                <a href="{{ route('sales-target.index') }}" class="small">Kelola Target <i class="mdi mdi-arrow-right"></i></a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sales</th>
                                <th class="text-end">Target</th>
                                <th class="text-end">Actual</th>
                                <th>Achievement</th>
                                <th class="text-end">Quotation</th>
                                <th class="text-end">PO</th>
                                <th class="text-end">Outstanding</th>
                                <th class="text-end">Forecast</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($smTeamPerformance as $i => $row)
                                <tr>
                                    <td>
                                        <span class="badge bg-label-{{ $i == 0 ? 'warning' : ($i == 1 ? 'secondary' : ($i == 2 ? 'danger' : 'light')) }}">{{ $i + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ url('') }}/{{ $row['image'] ?: 'assets/img/avatars/1.png' }}" class="rounded-circle me-2" width="28" height="28" onerror="this.src='{{ asset('assets') }}/img/avatars/1.png'">
                                            <span class="fw-medium">{{ $row['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ number_format($row['target'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['actual'], 0, ',', '.') }}</td>
                                    <td style="min-width: 120px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar {{ $row['achievement'] >= 100 ? 'bg-success' : ($row['achievement'] >= 60 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ min($row['achievement'], 100) }}%"></div>
                                            </div>
                                            <small>{{ $row['achievement'] }}%</small>
                                        </div>
                                    </td>
                                    <td class="text-end">{{ $row['quotation_count'] }}</td>
                                    <td class="text-end">{{ $row['po_count'] }}</td>
                                    <td class="text-end">{{ number_format($row['outstanding'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['forecast'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted">Belum ada data sales aktif.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Team Activity -->
<div class="row mb-2">
    <div class="col-12 mb-2">
        <h5 class="mb-0">Team Activity</h5>
        <small class="text-muted">KPI aktivitas bulan ini per sales</small>
    </div>
    <div class="col-12 mb-4">
        <div class="row g-2 flex-nowrap overflow-auto pb-1">
            @forelse ($smTeamActivity as $row)
                <div class="col" style="min-width: 170px;">
                    <div class="card h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ url('') }}/{{ $row['image'] ?: 'assets/img/avatars/1.png' }}" class="rounded-circle me-2" width="28" height="28" onerror="this.src='{{ asset('assets') }}/img/avatars/1.png'">
                                <span class="fw-medium text-truncate small">{{ $row['name'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-center">
                                <div>
                                    <h6 class="mb-0">{{ $row['new_leads'] }}</h6>
                                    <small class="text-muted">New Leads</small>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $row['daily_call'] }}</h6>
                                    <small class="text-muted">Daily Call</small>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $row['crm'] }}</h6>
                                    <small class="text-muted">CRM</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center text-muted">Belum ada data sales aktif.</div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Row 5: Quick Action -->
<div class="row mb-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Action</h5>
            </div>
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-6 col-md-2">
                        <a href="{{ route('quotation.index') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-primary rounded"><i class="mdi mdi-file-document-outline mdi-24px"></i></div></div>
                            <small class="d-block">Quotation</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="{{ route('prospect.index') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-info rounded"><i class="mdi mdi-account-details-outline mdi-24px"></i></div></div>
                            <small class="d-block">Prospect Pipeline</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="{{ url('/reports') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-success rounded"><i class="mdi mdi-finance mdi-24px"></i></div></div>
                            <small class="d-block">Sales Report</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="{{ url('/overview') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-warning rounded"><i class="mdi mdi-account-eye-outline mdi-24px"></i></div></div>
                            <small class="d-block">Team Performance</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="{{ route('sales-target.index') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-dark rounded"><i class="mdi mdi-target mdi-24px"></i></div></div>
                            <small class="d-block">Sales Target</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-2">
                        <a href="{{ route('pending-po.sales-order') }}" class="text-decoration-none">
                            <div class="avatar avatar-lg mx-auto mb-2"><div class="avatar-initial bg-label-secondary rounded"><i class="mdi mdi-list-box-outline mdi-24px"></i></div></div>
                            <small class="d-block">Sales Order</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('before-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/apex-charts/apex-charts.css" />
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
    <script>
        (function () {
            const isDark = document.documentElement.classList.contains('dark-style');
            const labelColor = isDark ? '#a8aaae' : '#6d6b77';

            const formatRp = val => {
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'B';
                if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'M';
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            const achievementEl = document.querySelector('#smAchievementChart');
            if (achievementEl) {
                new ApexCharts(achievementEl, {
                    chart: { type: 'bar', height: 220, toolbar: { show: false } },
                    plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '45%' } },
                    series: [{ name: 'Rp', data: [{{ (float) $smTargetMonth }}, {{ (float) $smActualMonth }}] }],
                    xaxis: {
                        categories: ['Target', 'Actual'],
                        labels: { formatter: formatRp, style: { colors: labelColor } },
                    },
                    yaxis: { labels: { style: { colors: labelColor } } },
                    colors: ['#696cff'],
                    dataLabels: { enabled: true, formatter: formatRp },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }

            const pipelineEl = document.querySelector('#smPipelineChart');
            if (pipelineEl) {
                new ApexCharts(pipelineEl, {
                    chart: { type: 'bar', height: 260, toolbar: { show: false } },
                    plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '55%', distributed: true } },
                    series: [{ name: 'Total', data: @json($smPipelineSeries) }],
                    xaxis: {
                        categories: @json($smPipelineLabels),
                        labels: { style: { colors: labelColor } },
                    },
                    yaxis: { labels: { style: { colors: labelColor } } },
                    colors: ['#8592a3', '#03c3ec', '#ffab00', '#ff9f43', '#71dd37'],
                    legend: { show: false },
                    dataLabels: { enabled: true },
                }).render();
            }

            const revenueStatusEl = document.querySelector('#smRevenueStatusChart');
            if (revenueStatusEl) {
                new ApexCharts(revenueStatusEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: @json($smRevenueStatusLabels),
                    series: @json($smRevenueStatusSeries),
                    colors: ['#03c3ec', '#ffab00', '#ff9f43', '#71dd37'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }

        })();
    </script>
@endpush
