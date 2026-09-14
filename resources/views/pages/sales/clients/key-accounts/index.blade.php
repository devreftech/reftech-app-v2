@extends('layouts.sales.app')
@section('title', 'Key Accounts Leaderboard')
@section('content')

    @if (Session::has('message'))
        <div class="bs-toast toast toast-placement-ex m-2 fade top-0 end-0 hide" role="alert" aria-live="assertive"
            aria-atomic="true" data-bs-delay="2000">
            <div class="toast-header">
                <i class="mdi mdi-home me-2 text-success"></i>
                <div class="me-auto fw-semibold">Success</div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">{{ Session::get('message') }}</div>
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between py-3 mb-4">
        <h4 class="fw-bold m-0">
            <span class="text-muted fw-light">Clients /</span> Key Accounts
        </h4>
        
        <!-- Filter Form -->
        <form action="{{ route('key-accounts.index') }}" method="GET" class="d-flex align-items-center gap-2">
            <!-- Search -->
            <div class="input-group" style="max-width: 250px;">
                <input type="text" name="search" class="form-control" placeholder="Cari pelanggan..." value="{{ $search }}">
                <button class="btn btn-outline-primary" type="submit">
                    <i class="mdi mdi-magnify"></i>
                </button>
            </div>

            <!-- Year -->
            <label for="year" class="form-label mb-0 text-nowrap fw-semibold ms-2">Tahun:</label>
            <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                @foreach ($years as $yr)
                    <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                        {{ $yr }}
                    </option>
                @endforeach
            </select>

            <!-- Semester -->
            <label for="semester" class="form-label mb-0 text-nowrap fw-semibold ms-2">Periode:</label>
            <select name="semester" id="semester" class="form-select" onchange="this.form.submit()">
                <option value="all" {{ $selectedSemester == 'all' ? 'selected' : '' }}>Setahun Penuh</option>
                <option value="1" {{ $selectedSemester == '1' ? 'selected' : '' }}>Semester 1 (Jan - Jun)</option>
                <option value="2" {{ $selectedSemester == '2' ? 'selected' : '' }}>Semester 2 (Jul - Des)</option>
            </select>
        </form>
    </div>

    @php
        $topCustomer = $keyAccounts->first();
        $topCustomerPct = ($totalRevenueYear > 0 && $topCustomer) ? round(($topCustomer->total_po / $totalRevenueYear) * 100, 1) : 0;

        $chartLabels = [];
        $chartSeries = [];
        $top5Sum = 0;
        
        foreach ($keyAccounts->take(5) as $ka) {
            $chartLabels[] = $ka->company;
            $chartSeries[] = (int)$ka->total_po;
            $top5Sum += $ka->total_po;
        }
        
        $othersSum = $totalRevenueYear - $top5Sum;
        if ($othersSum > 0) {
            $chartLabels[] = 'Lainnya';
            $chartSeries[] = (int)$othersSum;
        }

        $top5Pct = $totalRevenueYear > 0 ? round(($top5Sum / $totalRevenueYear) * 100, 1) : 0;
        $avgRevenuePerCustomer = $keyAccounts->total() > 0 ? ($totalRevenueYear / $keyAccounts->total()) : 0;
    @endphp

    <!-- Executive Overview Row (Revenue Distribution & Reorganized Cards) -->
    <div class="row g-4 mb-4 align-items-stretch">
        <!-- Col 1: Revenue Distribution Donut Chart (Above Leaderboard) -->
        <div class="col-xl-4 col-lg-5 col-12">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="mdi mdi-chart-donut me-1 text-primary"></i> Revenue Distribution
                        </h6>
                        <small class="text-muted">Top 5 Pelanggan vs Lainnya</small>
                    </div>
                    @if ($totalRevenueYear > 0)
                        <span class="badge bg-label-primary px-2 py-1" title="Top 5 menguasai {{ $top5Pct }}% omzet">
                            Top 5: {{ $top5Pct }}%
                        </span>
                    @endif
                </div>
                <div class="card-body d-flex flex-column justify-content-center p-3" style="min-height: 310px;">
                    @if ($totalRevenueYear > 0)
                        <div id="keyAccountsDistributionChart" class="w-100 my-auto"></div>
                    @else
                        <div class="text-center text-muted my-auto py-4">
                            <i class="mdi mdi-chart-donut fs-1 d-block mb-2 text-secondary"></i>
                            <span class="small">Tidak ada data transaksi untuk grafik periode ini.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Col 2: Rearranged Key Metrics (Top Key Account, Total Revenue, Active Customers) -->
        <div class="col-xl-8 col-lg-7 col-12 d-flex flex-column justify-content-between gap-3">
            
            <!-- Card: Top Key Account Spotlight -->
            <div class="card border-0 shadow-sm flex-fill">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-md rounded bg-label-warning d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-crown text-warning fs-3"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning text-white fw-bold px-2 py-1">RANK #1 KEY ACCOUNT</span>
                                    <small class="text-muted">Kontributor Tertinggi</small>
                                </div>
                                <h5 class="mb-0 fw-bold mt-1 text-dark">
                                    @if ($topCustomer)
                                        <a href="{{ route('existing.show', $topCustomer->id) }}" class="text-dark text-decoration-none">
                                            {{ $topCustomer->company }}
                                        </a>
                                    @else
                                        Belum Ada Transaksi
                                    @endif
                                </h5>
                            </div>
                        </div>
                        @if ($topCustomer)
                            <div class="text-sm-end">
                                <div class="text-muted small">Total Pembelian PO</div>
                                <h4 class="mb-0 fw-bold text-success">
                                    Rp {{ number_format($topCustomer->total_po, 0, ',', '.') }}
                                </h4>
                            </div>
                        @endif
                    </div>

                    @if ($topCustomer)
                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-1 small">
                                <span class="text-muted fw-semibold">Pangsa Omzet terhadap Total Revenue:</span>
                                <span class="fw-bold text-primary">{{ $topCustomerPct }}%</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ min($topCustomerPct, 100) }}%" aria-valuenow="{{ $topCustomerPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-1 border-top border-light text-muted small">
                                <span><i class="mdi mdi-receipt-text-outline me-1"></i> {{ $topCustomer->count_po }} Transaksi PO Selesai</span>
                                <span><i class="mdi mdi-clock-outline me-1"></i> Order Terakhir: <strong>{{ $topCustomer->last_po_date ? \Carbon\Carbon::parse($topCustomer->last_po_date)->format('d-m-Y') : '-' }}</strong></span>
                            </div>
                        </div>
                    @else
                        <p class="text-muted small mb-0">Belum ada transaksi terekam pada periode ini.</p>
                    @endif
                </div>
            </div>

            <!-- Row 2: Total Revenue & Active Customers -->
            <div class="row g-3">
                <!-- Total Revenue -->
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-semibold text-muted small">Total Revenue</span>
                                <span class="avatar avatar-sm rounded bg-label-primary">
                                    <i class="mdi mdi-currency-usd fs-5"></i>
                                </span>
                            </div>
                            <h4 class="mb-1 text-primary fw-bold">Rp {{ number_format($totalRevenueYear, 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center text-muted small">
                                <span>Penerimaan PO Periode {{ $selectedYear }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Customers -->
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-semibold text-muted small">Active Customers</span>
                                <span class="avatar avatar-sm rounded bg-label-info">
                                    <i class="mdi mdi-account-group fs-5"></i>
                                </span>
                            </div>
                            <h4 class="mb-1 text-info fw-bold">{{ $keyAccounts->total() }} Pelanggan</h4>
                            <div class="d-flex align-items-center text-muted small">
                                <span>Rata-rata: <strong>Rp {{ number_format($avgRevenuePerCustomer, 0, ',', '.') }}</strong> / akun</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Main Container Full Width Card Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                    <h5 class="m-0 fw-bold">Key Accounts Leaderboard</h5>
                    <span class="badge bg-label-secondary">
                        Tahun {{ $selectedYear }} - 
                        @if ($selectedSemester == '1') Semester 1
                        @elseif ($selectedSemester == '2') Semester 2
                        @else Setahun Penuh
                        @endif
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Nama Pelanggan</th>
                                    <th class="text-end">Total PO Value</th>
                                    <th class="text-end">Outstanding</th>
                                    <th class="text-center">Jumlah PO</th>
                                    <th class="text-end">Rata-rata / PO</th>
                                    <th class="text-center">Order Terakhir</th>
                                    <th class="text-end">Kontribusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($keyAccounts as $index => $ka)
                                    @php
                                        // calculate exact rank based on current page
                                        $rank = ($keyAccounts->currentPage() - 1) * $keyAccounts->perPage() + $index + 1;
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">
                                            @if ($rank == 1)
                                                <span class="badge bg-label-warning rounded-circle p-2"><i class="mdi mdi-crown text-warning fs-6"></i></span>
                                            @else
                                                #{{ $rank }}
                                            @endif
                                        </td>
                                        <td title="{{ $ka->company }}">
                                            <a href="{{ route('existing.show', $ka->id) }}" class="fw-semibold text-body text-truncate d-inline-block" style="max-width: 220px;">
                                                {{ \Illuminate\Support\Str::limit($ka->company, 25) }}
                                            </a>
                                        </td>
                                        <td class="text-end text-success fw-bold">
                                            Rp {{ number_format($ka->total_po, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end text-danger fw-semibold">
                                            Rp {{ number_format($ka->total_outstanding, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center fw-semibold">
                                            {{ $ka->count_po }}
                                        </td>
                                        <td class="text-end">
                                            Rp {{ number_format($ka->total_po / $ka->count_po, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center small">
                                            {{ $ka->last_po_date ? \Carbon\Carbon::parse($ka->last_po_date)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td class="text-end fw-semibold">
                                            {{ $totalRevenueYear > 0 ? round(($ka->total_po / $totalRevenueYear) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="mdi mdi-alert-circle-outline fs-3 d-block mb-2"></i>
                                            Belum ada transaksi terekam pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination links -->
                    @if ($keyAccounts->total() > 0)
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <small class="text-muted">
                                Showing {{ $keyAccounts->firstItem() }} to {{ $keyAccounts->lastItem() }} of {{ $keyAccounts->total() }} entries
                            </small>
                            <div>
                                {!! $keyAccounts->appends(request()->query())->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    @endif
                </div>
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
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'B';
                if (val >= 1_000_000) return 'Rp ' + (val / 1_000_000).toFixed(1) + 'M';
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            const chartEl = document.querySelector('#keyAccountsDistributionChart');
            if (chartEl && @json($totalRevenueYear) > 0) {
                new ApexCharts(chartEl, {
                    chart: { type: 'donut', height: 320 },
                    labels: @json($chartLabels),
                    series: @json($chartSeries),
                    colors: ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#ff3e1d', '#8592a3'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val) => val.toFixed(1) + '%' },
                    tooltip: { y: { formatter: formatRp } },
                }).render();
            }
        })();
    </script>
@endpush
