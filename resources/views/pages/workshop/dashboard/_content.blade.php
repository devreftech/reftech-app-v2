<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Workshop</h4>

@php
    $statusMeta = [
        'OK' => ['label' => 'Unit OK', 'color' => 'success', 'icon' => 'mdi-check-circle-outline'],
        'Rental' => ['label' => 'On Rental', 'color' => 'primary', 'icon' => 'mdi-truck-outline'],
        'Service' => ['label' => 'Service', 'color' => 'warning', 'icon' => 'mdi-wrench-outline'],
        'Breakdown' => ['label' => 'Breakdown', 'color' => 'danger', 'icon' => 'mdi-alert-circle-outline'],
        'Reserved' => ['label' => 'Reserved', 'color' => 'info', 'icon' => 'mdi-bookmark-outline'],
    ];
@endphp

<!-- KPI Cards -->
<div class="row mb-2">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-primary rounded"><i class="mdi mdi-cube-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Unit Fixed Asset (Mesin)</small>
                        <h5 class="mb-0">{{ $workshopTotalUnit }} Total Unit</h5>
                    </div>
                </div>
                <small class="text-muted">Nilai perolehan Rp {{ number_format($workshopTotalNilaiAset, 0, ',', '.') }}</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-warning rounded"><i class="mdi mdi-wrench-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Perlu Perhatian</small>
                        <h5 class="mb-0">{{ $workshopStatusCounts['Service'] + $workshopStatusCounts['Breakdown'] }} Unit</h5>
                    </div>
                </div>
                <small class="text-muted">Service {{ $workshopStatusCounts['Service'] }} &bull; Breakdown {{ $workshopStatusCounts['Breakdown'] }}</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-info rounded"><i class="mdi mdi-clipboard-check-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Dalam Proses QC</small>
                        <h5 class="mb-0">{{ $workshopQcChecking }} Unit</h5>
                    </div>
                </div>
                <small class="text-muted">Lolos QC {{ $workshopQcOk }} &bull; Reject {{ $workshopQcReject }}</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-secondary rounded"><i class="mdi mdi-recycle-variant mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Kondisi Unit</small>
                        <h5 class="mb-0">{{ $workshopKondisiBaru }} Baru / {{ $workshopKondisiSecond }} Second</h5>
                    </div>
                </div>
                <small class="text-muted">Berdasarkan data acquisition</small>
            </div>
        </div>
    </div>
</div>

<!-- Unit Monitoring Donut + Status Table -->
<div class="row mb-2">
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Unit Fixed Asset Monitoring</h5>
                <small class="text-muted">Status ketersediaan unit workshop</small>
            </div>
            <div class="card-body">
                <div id="workshopStatusChart"></div>
                <ul class="list-unstyled mb-0 mt-2">
                    @foreach ($statusMeta as $key => $meta)
                        <li class="d-flex align-items-center justify-content-between py-1">
                            <span><i class="mdi {{ $meta['icon'] }} text-{{ $meta['color'] }} me-2"></i>{{ $meta['label'] }}</span>
                            <span class="fw-semibold">{{ $workshopStatusCounts[$key] }}</span>
                        </li>
                    @endforeach
                    @if ($workshopStatusOther > 0)
                        <li class="d-flex align-items-center justify-content-between py-1">
                            <span><i class="mdi mdi-help-circle-outline text-secondary me-2"></i>Lainnya / Belum Diklasifikasi</span>
                            <span class="fw-semibold">{{ $workshopStatusOther }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Unit dengan Status Service / Breakdown</h5>
                <a href="{{ route('fixed.index') }}" class="btn btn-sm btn-outline-primary">Lihat semua unit</a>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode Asset</th>
                                <th>Equipment</th>
                                <th>Serial Number</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($workshopAttentionUnits as $unit)
                                <tr>
                                    <td>{{ $unit->code }}</td>
                                    <td>{{ $unit->unit ? trim($unit->unit->brand . ' ' . $unit->unit->model) : '-' }}</td>
                                    <td>{{ $unit->serial_number ?: '-' }}</td>
                                    <td>{{ $unit->kondisi ?: '-' }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $statusMeta[$unit->status_unit]['color'] ?? 'secondary' }}">
                                            {{ $statusMeta[$unit->status_unit]['label'] ?? $unit->status_unit }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada unit dalam status Service / Breakdown.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Unit Terbaru -->
<div class="row mb-2">
    <div class="col-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Unit Fixed Asset Terbaru</h5>
                <a href="{{ route('unit-acquisition.index') }}" class="btn btn-sm btn-outline-primary">Lihat Unit Acquisition</a>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode Asset</th>
                                <th>Equipment</th>
                                <th>Kondisi</th>
                                <th>QC Status</th>
                                <th>Status Unit</th>
                                <th>Tanggal Masuk</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($workshopRecentUnits as $unit)
                                <tr>
                                    <td>{{ $unit->code }}</td>
                                    <td>{{ $unit->unit ? trim($unit->unit->brand . ' ' . $unit->unit->model) : '-' }}</td>
                                    <td>{{ $unit->kondisi ?: '-' }}</td>
                                    <td>
                                        @if ($unit->qc_status === 'checking')
                                            <span class="badge bg-label-warning">Dalam Pengecekan</span>
                                        @elseif ($unit->qc_status === 'ok')
                                            <span class="badge bg-label-success">OK</span>
                                        @elseif ($unit->qc_status === 'reject')
                                            <span class="badge bg-label-danger">Reject</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($unit->status_unit)
                                            <span class="badge bg-label-{{ $statusMeta[$unit->status_unit]['color'] ?? 'secondary' }}">
                                                {{ $statusMeta[$unit->status_unit]['label'] ?? $unit->status_unit }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($unit->created_at)->translatedFormat('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data unit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle Maintenance -->
<div class="row mb-2">
    <div class="col-12">
        <div class="d-flex align-items-center gap-2 mb-2">
            <h5 class="mb-0">Vehicle Maintenance (Kendaraan)</h5>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Vehicle Maintenance Overview</h5>
                <small class="text-muted">Servis, STNK/Pajak, Ganti Kaleng 5 Th</small>
            </div>
            <div class="card-body">
                <div id="workshopVehicleChart"></div>
                <ul class="list-unstyled mb-0 mt-2">
                    <li class="d-flex align-items-center justify-content-between py-1">
                        <span><i class="mdi mdi-wrench-outline text-warning me-2"></i>Perlu Servis</span>
                        <span class="fw-semibold">{{ $workshopVehicleServisDue }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between py-1">
                        <span><i class="mdi mdi-file-document-outline text-info me-2"></i>STNK/Pajak Jatuh Tempo</span>
                        <span class="fw-semibold">{{ $workshopVehiclePajakDue }}</span>
                    </li>
                    <li class="d-flex align-items-center justify-content-between py-1">
                        <span><i class="mdi mdi-card-account-details-outline text-danger me-2"></i>Ganti Kaleng (5 Th) Jatuh Tempo</span>
                        <span class="fw-semibold">{{ $workshopVehicleKalengDue }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Upcoming Schedule Kendaraan</h5>
                    <small class="text-muted">{{ $workshopVehicleOverdueCount }} kendaraan overdue perlu segera ditindaklanjuti</small>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kendaraan</th>
                                <th>Servis Berikutnya</th>
                                <th>STNK/Pajak</th>
                                <th>Ganti Kaleng (5 Th)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse ($workshopVehicles as $v)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $v->plat }}</span>
                                        <div class="text-muted small">{{ $v->jenis }}</div>
                                    </td>
                                    <td>
                                        {{ $v->servis_berikutnya ? $v->servis_berikutnya->translatedFormat('d M Y') : '-' }}
                                        <span class="badge bg-label-{{ $v->servis_status['color'] }} ms-1">{{ $v->servis_status['label'] }}</span>
                                    </td>
                                    <td>
                                        {{ $v->pajak_due ? $v->pajak_due->translatedFormat('d M Y') : '-' }}
                                        <span class="badge bg-label-{{ $v->pajak_status['color'] }} ms-1">{{ $v->pajak_status['label'] }}</span>
                                    </td>
                                    <td>
                                        {{ $v->ganti_kaleng_due ? $v->ganti_kaleng_due->translatedFormat('d M Y') : '-' }}
                                        <span class="badge bg-label-{{ $v->ganti_kaleng_status['color'] }} ms-1">{{ $v->ganti_kaleng_status['label'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $v->overall_status['color'] }}">{{ $v->overall_status['label'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data kendaraan (Fixed Asset kategori Kendaraan).</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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

            const statusEl = document.querySelector('#workshopStatusChart');
            if (statusEl) {
                const labels = @json(array_column($statusMeta, 'label'));
                const series = @json(array_values($workshopStatusCounts));
                new ApexCharts(statusEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: labels,
                    series: series,
                    colors: ['#71dd37', '#696cff', '#ffab00', '#ff3e1d', '#03c3ec'],
                    legend: { show: false },
                    dataLabels: { enabled: true, formatter: (val, opts) => opts.w.config.series[opts.seriesIndex] },
                    tooltip: { y: { formatter: (val) => val + ' unit' } },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Unit',
                                        color: labelColor,
                                    },
                                },
                            },
                        },
                    },
                }).render();
            }

            const vehicleEl = document.querySelector('#workshopVehicleChart');
            if (vehicleEl) {
                const vehicleCounts = @json($workshopVehicleOverviewCounts);
                new ApexCharts(vehicleEl, {
                    chart: { type: 'donut', height: 220 },
                    labels: Object.keys(vehicleCounts),
                    series: Object.values(vehicleCounts),
                    colors: ['#71dd37', '#ffab00', '#03c3ec', '#ff3e1d'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true, formatter: (val, opts) => opts.w.config.series[opts.seriesIndex] },
                    tooltip: { y: { formatter: (val) => val + ' kendaraan' } },
                }).render();
            }
        })();
    </script>
@endpush
