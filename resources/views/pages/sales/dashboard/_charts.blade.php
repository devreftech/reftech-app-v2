{{-- Sales Dashboard - Visual Analytics --}}
<div class="row gy-4 mb-4">
    <!-- Sales Funnel -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Sales Funnel</h5>
                <small class="text-muted">Leads &rarr; Quotation &rarr; PO Won (bulan ini)</small>
            </div>
            <div class="card-body">
                <div id="salesFunnelChart"></div>
            </div>
        </div>
    </div>

    <!-- Win Rate -->
    <div class="col-lg-3 col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Win Rate</h5>
                <small class="text-muted">PO / Quotation</small>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <div id="salesWinRateChart"></div>
                <span class="text-muted small mt-2">{{ $po->count() ?? 0 }} PO dari {{ $quotation->count() ?? 0 }} Quotation</span>
            </div>
        </div>
    </div>

    <!-- Quotation Status Breakdown -->
    <div class="col-lg-5 col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Quotation by Status</h5>
                <small class="text-muted">Bulan ini</small>
            </div>
            <div class="card-body">
                @if (array_sum($salesStatusSeries ?? []) == 0)
                    <div class="d-flex align-items-center justify-content-center" style="min-height: 220px;">
                        <span class="text-muted">Belum ada quotation bulan ini.</span>
                    </div>
                @else
                    <div id="salesStatusChart"></div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row gy-4 mb-4">
    <!-- Monthly Trend -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Tren 6 Bulan Terakhir</h5>
                <small class="text-muted">Quotation vs PO Won</small>
            </div>
            <div class="card-body">
                <div id="salesTrendChart"></div>
            </div>
        </div>
    </div>

    <!-- Prospect by Source -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Leads by Source</h5>
                <small class="text-muted">Seluruh waktu</small>
            </div>
            <div class="card-body">
                @if (($sourceData ?? collect())->isEmpty())
                    <div class="d-flex align-items-center justify-content-center" style="min-height: 220px;">
                        <span class="text-muted">Belum ada data leads.</span>
                    </div>
                @else
                    <div id="salesSourceChart"></div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Service Sales Forecast -->
<div class="row gy-4 mb-4">
    <div class="col-12">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="card-title mb-0">
                    <h5 class="mb-0">Service Sales Forecast & Projections</h5>
                    <small class="text-muted">Target vs Projections vs Actual PO Realization</small>
                </div>
                <span class="badge bg-label-primary font-semibold">Calendar Year {{ $year ?? now()->year }}</span>
            </div>
            <div class="card-body">
                <div id="forecastChart" style="height: 350px;"></div>
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
            const borderColor = isDark ? '#3b3b40' : '#eceef1';

            const achColorMap = { success: '#71dd37', warning: '#ffab00', danger: '#ff3e1d' };
            const achGaugeEl = document.querySelector('#salesAchievementGauge');
            if (achGaugeEl) {
                const achValue = parseFloat(achGaugeEl.dataset.value || 0);
                const achColor = achColorMap[achGaugeEl.dataset.color] || '#696cff';
                new ApexCharts(achGaugeEl, {
                    chart: { type: 'radialBar', height: 220 },
                    series: [Math.min(achValue, 100)],
                    labels: ['Target'],
                    colors: [achColor],
                    plotOptions: {
                        radialBar: {
                            hollow: { size: '62%' },
                            dataLabels: {
                                value: {
                                    fontSize: '24px',
                                    fontWeight: 700,
                                    color: labelColor,
                                    formatter: () => achValue.toFixed(1) + '%',
                                },
                            },
                        },
                    },
                }).render();
            }

            const funnelEl = document.querySelector('#salesFunnelChart');
            if (funnelEl) {
                new ApexCharts(funnelEl, {
                    chart: { type: 'bar', height: 260, toolbar: { show: false } },
                    plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '55%', distributed: true } },
                    series: [{ name: 'Jumlah', data: @json($salesFunnelSeries ?? [0, 0, 0]) }],
                    xaxis: {
                        categories: @json($salesFunnelLabels ?? ['New Leads', 'Quotation', 'PO Won']),
                        labels: { style: { colors: labelColor } },
                    },
                    yaxis: { labels: { style: { colors: labelColor } } },
                    colors: ['#696cff', '#03c3ec', '#71dd37'],
                    legend: { show: false },
                    dataLabels: { enabled: true, style: { colors: ['#fff'] } },
                    grid: { borderColor: borderColor },
                }).render();
            }

            const winRateEl = document.querySelector('#salesWinRateChart');
            if (winRateEl) {
                new ApexCharts(winRateEl, {
                    chart: { type: 'radialBar', height: 220 },
                    series: [{{ (float) ($salesWinRate ?? 0) }}],
                    labels: ['Win Rate'],
                    colors: ['#71dd37'],
                    plotOptions: {
                        radialBar: {
                            hollow: { size: '60%' },
                            dataLabels: {
                                value: {
                                    fontSize: '22px',
                                    fontWeight: 600,
                                    color: labelColor,
                                    formatter: (val) => val + '%',
                                },
                            },
                        },
                    },
                }).render();
            }

            const statusEl = document.querySelector('#salesStatusChart');
            if (statusEl) {
                new ApexCharts(statusEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: @json($salesStatusLabels ?? []),
                    series: @json($salesStatusSeries ?? []),
                    colors: ['#ff3e1d', '#ffab00', '#03c3ec', '#71dd37'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                }).render();
            }

            const trendEl = document.querySelector('#salesTrendChart');
            if (trendEl) {
                new ApexCharts(trendEl, {
                    chart: { type: 'line', height: 300, toolbar: { show: false } },
                    series: [
                        { name: 'Quotation', data: @json($salesMonthlyQuote ?? []) },
                        { name: 'PO Won', data: @json($salesMonthlyPO ?? []) },
                    ],
                    xaxis: {
                        categories: @json($salesMonthlyLabels ?? []),
                        labels: { style: { colors: labelColor } },
                    },
                    yaxis: { labels: { style: { colors: labelColor } } },
                    colors: ['#696cff', '#71dd37'],
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 4 },
                    legend: { position: 'top', labels: { colors: labelColor } },
                    grid: { borderColor: borderColor },
                }).render();
            }

            const sourceEl = document.querySelector('#salesSourceChart');
            if (sourceEl) {
                new ApexCharts(sourceEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: @json(($sourceData ?? collect())->pluck('source')),
                    series: @json(($sourceData ?? collect())->pluck('total')),
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                }).render();
            }
            const forecastEl = document.querySelector('#forecastChart');
            if (forecastEl) {
                const targetData = @json(collect($months ?? [])->pluck('target'));
                const projectionData = @json(collect($months ?? [])->pluck('projection'));
                const actualData = @json(collect($months ?? [])->pluck('actual'));
                const monthNames = @json(collect($months ?? [])->pluck('name'));

                new ApexCharts(forecastEl, {
                    series: [{
                        name: 'Annual Target',
                        type: 'column',
                        data: targetData
                    }, {
                        name: 'Rolling Projection',
                        type: 'column',
                        data: projectionData
                    }, {
                        name: 'PO Realization (Won)',
                        type: 'line',
                        data: actualData
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        stacked: false,
                        fontFamily: 'Inter, sans-serif',
                        toolbar: { show: false }
                    },
                    stroke: {
                        width: [0, 0, 4],
                        curve: 'smooth',
                        dashArray: [0, 0, 0]
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '45%',
                            borderRadius: 6,
                            dataLabels: { position: 'top' }
                        }
                    },
                    colors: ['#cbd5e1', '#6366f1', '#10b981'],
                    fill: {
                        opacity: [0.75, 0.85, 0.85],
                        type: ['solid', 'solid', 'gradient'],
                        gradient: {
                            shade: 'dark',
                            type: 'vertical',
                            shadeIntensity: 0.5,
                            gradientToColors: ['#34d399'],
                            inverseColors: false,
                            opacityFrom: 0.4,
                            opacityTo: 0.1,
                            stops: [0, 90, 100]
                        }
                    },
                    labels: monthNames,
                    markers: {
                        size: 5,
                        colors: ['#10b981'],
                        strokeColors: '#ffffff',
                        strokeWidth: 2,
                        hover: { size: 7 }
                    },
                    grid: {
                        borderColor: borderColor,
                        strokeDashArray: 4,
                        padding: { left: 20, right: 20 }
                    },
                    xaxis: {
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: labelColor, fontWeight: 600 } }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: labelColor, fontWeight: 600 },
                            formatter: function (value) {
                                if (value >= 1000000) {
                                    return "Rp " + (value / 1000000).toFixed(1) + " M";
                                }
                                return "Rp " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontWeight: 600,
                        labels: { colors: labelColor },
                        markers: { radius: 12 },
                        itemMargin: { horizontal: 10 }
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function (y) {
                                if (typeof y !== "undefined") {
                                    return "Rp " + y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }
                                return y;
                            }
                        }
                    }
                }).render();
            }
        })();
    </script>
@endpush
