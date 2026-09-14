@extends('layouts.sales.app')
@section('title', 'Proyeksi Arus Kas (Cash Flow Forecast)')
@section('no-container') @endsection
@section('content')
<div class="container-fluid px-4 py-3">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Laporan /</span> Proyeksi Arus Kas (Cash Flow Forecast)
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-chart-timeline-variant me-1"></i> Estimasi posisi likuiditas kas &amp; bank 30–90 hari ke depan berdasarkan jadwal jatuh tempo piutang &amp; hutang
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="btn-group" role="group">
                <a href="{{ route('finance.cashflow.forecast', ['horizon' => 30]) }}" 
                   class="btn btn-sm {{ $horizonDays == 30 ? 'btn-primary' : 'btn-label-primary' }}">30 Hari</a>
                <a href="{{ route('finance.cashflow.forecast', ['horizon' => 60]) }}" 
                   class="btn btn-sm {{ $horizonDays == 60 ? 'btn-primary' : 'btn-label-primary' }}">60 Hari</a>
                <a href="{{ route('finance.cashflow.forecast', ['horizon' => 90]) }}" 
                   class="btn btn-sm {{ $horizonDays == 90 ? 'btn-primary' : 'btn-label-primary' }}">90 Hari</a>
            </div>
            <button onclick="window.print()" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-printer me-1"></i> Cetak
            </button>
        </div>
    </div>

    {{-- Metric Cards --}}
    <div class="row g-3 mb-4">
        {{-- Saldo Kas Saat Ini --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-left: 5px solid #22c55e !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-success small" style="font-size: 11px;">
                            <i class="mdi mdi-bank me-1"></i> Saldo Kas &amp; Bank Riil
                        </span>
                        <span class="badge bg-label-success rounded-pill">{{ count($bankAccounts) }} Rekening</span>
                    </div>
                    <h4 class="fw-bolder text-success mb-1">Rp {{ number_format($currentLiquidCash, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Total saldo per hari ini</small>
                </div>
            </div>
        </div>

        {{-- Proyeksi Masuk --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-primary small" style="font-size: 11px;">
                            <i class="mdi mdi-arrow-bottom-left me-1"></i> Proyeksi Masuk (+)
                        </span>
                        <span class="badge bg-label-primary rounded-pill">{{ count($allInflows) }} Tagihan</span>
                    </div>
                    <h4 class="fw-bolder text-primary mb-1">Rp {{ number_format($totalProjectedInflow, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Piutang AR dalam {{ $horizonDays }} hari</small>
                </div>
            </div>
        </div>

        {{-- Proyeksi Keluar --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-left: 5px solid #f97316 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-warning small" style="font-size: 11px;">
                            <i class="mdi mdi-arrow-top-right me-1"></i> Proyeksi Keluar (-)
                        </span>
                        <span class="badge bg-label-warning rounded-pill">{{ count($apOutflows) }} Hutang AP</span>
                    </div>
                    <h4 class="fw-bolder text-dark mb-1">Rp {{ number_format($totalProjectedOutflow, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Hutang supplier + estimasi OPEX</small>
                </div>
            </div>
        </div>

        {{-- Proyeksi Saldo Akhir --}}
        <div class="col-12 col-sm-6 col-xl-3">
            @php $isPositive = $finalProjectedCash >= 0; @endphp
            <div class="card border-0 shadow-sm h-100" style="background: {{ $isPositive ? 'linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%)' : 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)' }}; border-left: 5px solid {{ $isPositive ? '#a855f7' : '#ef4444' }} !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold small" style="color: {{ $isPositive ? '#7e22ce' : '#dc2626' }}; font-size: 11px;">
                            <i class="mdi mdi-scale-balance me-1"></i> Proyeksi Saldo Akhir
                        </span>
                        <span class="badge rounded-pill" style="background-color: {{ $isPositive ? '#f3e8ff' : '#fee2e2' }}; color: {{ $isPositive ? '#7e22ce' : '#dc2626' }};">
                            {{ $isPositive ? 'Surplus' : 'Defisit' }}
                        </span>
                    </div>
                    <h4 class="fw-bolder mb-1" style="color: {{ $isPositive ? '#7e22ce' : '#dc2626' }};">
                        Rp {{ number_format($finalProjectedCash, 0, ',', '.') }}
                    </h4>
                    <small class="text-muted" style="font-size: 11px;">Posisi kas setelah {{ $horizonDays }} hari</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
            <div>
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="mdi mdi-chart-line me-2 text-primary"></i> Tren Proyeksi Kas Mingguan
                </h5>
                <small class="text-muted">Pergerakan arus kas masuk, arus kas keluar, dan estimasi saldo akhir berjalan</small>
            </div>
        </div>
        <div class="card-body p-3">
            <div id="cashFlowForecastChart" style="min-height: 320px;"></div>
        </div>
    </div>

    {{-- Table Proyeksi Mingguan --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark">
                <i class="mdi mdi-calendar-week me-2 text-primary"></i> Rincian Proyeksi per Minggu
            </h6>
            <span class="badge bg-label-secondary">{{ count($weeklyBuckets) }} Minggu</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th>Minggu Ke</th>
                        <th>Rentang Periode</th>
                        <th class="text-end text-success">Estimasi Masuk (AR)</th>
                        <th class="text-end text-warning">Hutang AP Supplier</th>
                        <th class="text-end text-muted">Beban Rutin (OPEX)</th>
                        <th class="text-end text-danger">Total Kas Keluar</th>
                        <th class="text-end">Net Mingguan</th>
                        <th class="text-end fw-bold">Estimasi Saldo Kas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weeklyBuckets as $wb)
                        <tr>
                            <td class="fw-bold text-dark">{{ $wb['label'] }}</td>
                            <td>{{ $wb['period'] }}</td>
                            <td class="text-end text-success fw-semibold">
                                {{ $wb['inflow'] > 0 ? '+ Rp ' . number_format($wb['inflow'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-warning">
                                {{ $wb['ap'] > 0 ? 'Rp ' . number_format($wb['ap'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="text-end text-muted">
                                Rp {{ number_format($wb['opex'], 0, ',', '.') }}
                            </td>
                            <td class="text-end text-danger fw-semibold">
                                - Rp {{ number_format($wb['outflow'], 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold {{ $wb['net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($wb['net'] >= 0 ? '+' : '') . 'Rp ' . number_format($wb['net'], 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bolder {{ $wb['ending_cash'] >= 0 ? 'text-primary' : 'text-danger' }}">
                                Rp {{ number_format($wb['ending_cash'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('before-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/apex-charts/apex-charts.css" />
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
    <script>
        (function () {
            const isDark = document.documentElement.classList.contains('dark-style');
            const labelColor = isDark ? '#a8aaae' : '#6d6b77';
            const borderColor = isDark ? '#404152' : '#dbdade';

            const formatRp = val => {
                if (Math.abs(val) >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'B';
                if (Math.abs(val) >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'M';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
            };

            const chartEl = document.querySelector('#cashFlowForecastChart');
            if (chartEl) {
                new ApexCharts(chartEl, {
                    chart: {
                        height: 320,
                        type: 'line',
                        stacked: false,
                        toolbar: { show: false }
                    },
                    series: [
                        {
                            name: 'Kas Masuk (Inflow)',
                            type: 'column',
                            data: @json($chartInflows)
                        },
                        {
                            name: 'Kas Keluar (Outflow)',
                            type: 'column',
                            data: @json($chartOutflows)
                        },
                        {
                            name: 'Proyeksi Saldo Kas',
                            type: 'line',
                            data: @json($chartBalances)
                        }
                    ],
                    colors: ['#22c55e', '#ef4444', '#696cff'],
                    stroke: {
                        width: [0, 0, 3],
                        curve: 'smooth'
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '40%',
                            borderRadius: 4
                        }
                    },
                    xaxis: {
                        categories: @json($chartCategories),
                        labels: { style: { colors: labelColor, fontSize: '11px' } }
                    },
                    yaxis: [
                        {
                            title: { text: 'Arus Kas (Rp)', style: { color: labelColor } },
                            labels: { formatter: formatRp, style: { colors: labelColor } }
                        },
                        {
                            opposite: true,
                            title: { text: 'Estimasi Saldo (Rp)', style: { color: labelColor } },
                            labels: { formatter: formatRp, style: { colors: labelColor } }
                        }
                    ],
                    legend: {
                        position: 'top',
                        labels: { colors: labelColor }
                    },
                    grid: { borderColor, strokeDashArray: 5 },
                    tooltip: {
                        shared: true,
                        y: { formatter: formatRp }
                    }
                }).render();
            }
        })();
    </script>
@endpush
