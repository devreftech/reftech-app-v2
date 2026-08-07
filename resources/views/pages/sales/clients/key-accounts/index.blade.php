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

    <!-- KPI Cards Row -->
    <div class="row mb-4">
        <!-- Card 1: Total Revenue -->
        <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-primary rounded">
                                <i class="mdi mdi-currency-usd mdi-24px"></i>
                            </div>
                        </div>
                        <span class="badge bg-label-primary">Total Revenue</span>
                    </div>
                    <h4 class="mb-1 text-primary fw-bold">Rp {{ number_format($totalRevenueYear, 0, ',', '.') }}</h4>
                    <p class="mb-0 text-muted small">
                        Penerimaan PO pada periode yang dipilih
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 2: Top Customer -->
        @php
            $topCustomer = $keyAccounts->first();
        @endphp
        <div class="col-md-4 col-sm-6 mb-4 mb-md-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="mdi mdi-trophy-outline mdi-24px"></i>
                            </div>
                        </div>
                        <span class="badge bg-label-success">Top Key Account</span>
                    </div>
                    <h4 class="mb-1 text-success fw-bold text-truncate" title="{{ $topCustomer?->company ?? '-' }}">
                        {{ $topCustomer?->company ?? '-' }}
                    </h4>
                    @if ($topCustomer)
                        <p class="mb-0 text-muted small">
                            Kontribusi: Rp {{ number_format($topCustomer->total_po, 0, ',', '.') }}
                            ({{ $totalRevenueYear > 0 ? round(($topCustomer->total_po / $totalRevenueYear) * 100, 1) : 0 }}%)
                        </p>
                    @else
                        <p class="mb-0 text-muted small">Belum ada transaksi di periode ini</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 3: Active Key Accounts Count -->
        <div class="col-md-4 col-sm-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-info rounded">
                                <i class="mdi mdi-account-multiple-outline mdi-24px"></i>
                            </div>
                        </div>
                        <span class="badge bg-label-info">Active Customers</span>
                    </div>
                    <h4 class="mb-1 text-info fw-bold">{{ $keyAccounts->total() }} Customers</h4>
                    <p class="mb-0 text-muted small">Jumlah pelanggan aktif dalam periode ini</p>
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
                                            {{ $ka->last_po_date ? \Carbon\Carbon::parse($ka->last_po_date)->translatedFormat('d M Y') : '-' }}
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

    <!-- Chart Row -->
    @php
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
    @endphp
    <div class="row">
        <div class="col-xl-4 col-lg-5 col-md-6 col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Revenue Distribution</h5>
                    <small class="text-muted">Kontribusi Top 5 Pelanggan vs Lainnya</small>
                </div>
                <div class="card-body d-flex flex-column justify-content-between" style="min-height: 350px;">
                    @if ($totalRevenueYear > 0)
                        <div id="keyAccountsDistributionChart" class="my-auto"></div>
                    @else
                        <div class="text-center text-muted my-auto">
                            <i class="mdi mdi-chart-donut fs-1 d-block mb-2"></i>
                            Tidak ada data grafik untuk ditampilkan pada periode ini.
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
