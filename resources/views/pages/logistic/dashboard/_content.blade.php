<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Logistics</h4>

<!-- KPI Cards -->
<div class="row mb-2">
    <div class="col-sm-6 col-lg-3 mb-4">
        <a href="{{ route('pending-po.sales-order') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-label-primary rounded"><i class="mdi mdi-cart-outline mdi-24px"></i></div>
                        </div>
                        <div>
                            <small class="text-muted d-block">New Sales Orders</small>
                            <h4 class="mb-0">{{ $logSoBaruCount }} <small class="fs-6 fw-normal text-muted">Order</small></h4>
                        </div>
                    </div>
                    <small class="text-primary">View Details <i class="mdi mdi-arrow-right"></i></small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <a href="{{ route('purchase-request.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-label-success rounded"><i class="mdi mdi-clipboard-text-outline mdi-24px"></i></div>
                        </div>
                        <div>
                            <small class="text-muted d-block">PR Pending Approval</small>
                            <h4 class="mb-0">{{ $logPrPendingCount }} <small class="fs-6 fw-normal text-muted">PR</small></h4>
                        </div>
                    </div>
                    <small class="text-primary">View Details <i class="mdi mdi-arrow-right"></i></small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <a href="{{ route('suo.logistic.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-label-warning rounded"><i class="mdi mdi-truck-fast-outline mdi-24px"></i></div>
                        </div>
                        <div>
                            <small class="text-muted d-block">SUO Pending Confirmation</small>
                            <h4 class="mb-0">{{ $logSuoPendingCount }} <small class="fs-6 fw-normal text-muted">SUO</small></h4>
                        </div>
                    </div>
                    <small class="text-primary">View Details <i class="mdi mdi-arrow-right"></i></small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <a href="{{ route('product-in.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-label-info rounded"><i class="mdi mdi-dolly mdi-24px"></i></div>
                        </div>
                        <div>
                            <small class="text-muted d-block">Incoming Goods Pending Receipt</small>
                            <h4 class="mb-0">{{ $logIncomingPendingCount }} <small class="fs-6 fw-normal text-muted">PO</small></h4>
                        </div>
                    </div>
                    <small class="text-primary">View Details <i class="mdi mdi-arrow-right"></i></small>
                </div>
            </div>
        </a>
    </div>
</div>
<div class="row mb-2">
    <div class="col-sm-6 col-lg-3 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="avatar avatar-md me-3">
                        <div class="avatar-initial bg-label-secondary rounded"><i class="mdi mdi-truck-outline mdi-24px"></i></div>
                    </div>
                    <div>
                        <small class="text-muted d-block">PO Pending Shipment</small>
                        <h5 class="mb-0">Purchase Order</h5>
                    </div>
                </div>
                <span class="badge bg-label-secondary">Under Construction</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3 mb-4">
        <a href="{{ route('stock.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-label-danger rounded"><i class="mdi mdi-package-variant-closed-remove mdi-24px"></i></div>
                        </div>
                        <div>
                            <small class="text-muted d-block">Low Stock</small>
                            <h4 class="mb-0">{{ $logLowStockCount }} <small class="fs-6 fw-normal text-muted">Item</small></h4>
                        </div>
                    </div>
                    <small class="text-primary">View Details <i class="mdi mdi-arrow-right"></i></small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Auto PR / Incoming Goods / Low Stock -->
<div class="row mb-2">
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Auto-generated PR from SO (Insufficient Stock)</h5>
                <span class="badge bg-label-warning">{{ $logPrFromSo->count() }} New</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>PR No</th>
                            <th>From SO</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logPrFromSo as $pr)
                            <tr>
                                <td><a href="{{ route('purchase-request.index') }}">{{ $pr->no_pr }}</a></td>
                                <td>{{ optional($pr->pending)->no_pending ?? '-' }}</td>
                                <td>{{ optional(optional($pr->equivalent)->product)->commodity ?? '-' }}</td>
                                <td>{{ $pr->qty }}</td>
                                <td>{{ \Carbon\Carbon::parse($pr->date)->translatedFormat('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No PR pending approval.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('purchase-request.index') }}" class="small">View All PR <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Incoming Goods - Pending Receipt</h5>
                <span class="badge bg-label-info">{{ $logIncomingPendingCount }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>DO No. / Invoice</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logIncomingPending as $pin)
                            <tr>
                                <td><a href="{{ route('product-in.index') }}">{{ $pin->no_do ?: ($pin->invoice ?: '#'.$pin->id) }}</a></td>
                                <td>{{ optional($pin->supp)->supplier ?? $pin->supplier ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($pin->date)->translatedFormat('d/m/Y') }}</td>
                                <td><span class="badge bg-label-warning">Pending</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No goods pending receipt.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('product-in.index') }}" class="small">View All <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Low Stock</h5>
                <span class="badge bg-label-danger">{{ $logLowStockCount }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>BDG</th>
                            <th>BKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logLowStock as $item)
                            <tr>
                                <td>
                                    {{ optional($item->product)->commodity ?? '-' }}
                                    <small class="text-muted d-block">{{ $item->replacement }}</small>
                                </td>
                                <td class="text-danger">{{ $item->stock }}</td>
                                <td class="text-danger">{{ $item->warehouse_stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">All stock levels are healthy.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('stock.index') }}" class="small">View All Stock <i class="mdi mdi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- SO Status / PO Status -->
<div class="row mb-2">
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Sales Order Status</h5>
            </div>
            <div class="card-body">
                <div id="logStatusSoChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">PO Status (Supplier)</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 260px;">
                <span class="badge bg-label-secondary">Under Construction</span>
            </div>
        </div>
    </div>
</div>

<!-- Goods Received / Recent Activity -->
<div class="row mb-2">
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Goods Received (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <div id="logReceivingChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Recent Activity</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse ($logRecentActivity as $activity)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge bg-label-primary mb-1">{{ $activity->tipe }}</span>
                                    <div class="small">{{ $activity->ref }} &bull; {{ $activity->ket }}</div>
                                </div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($activity->tanggal)->diffForHumans() }}</small>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted">No recent activity.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body d-flex flex-wrap gap-2">
                <a href="{{ route('purchase-request.index') }}" class="btn btn-outline-success"><i class="mdi mdi-plus me-1"></i>Create Manual PR</a>
                <a href="{{ route('purchase.create') }}" class="btn btn-outline-primary"><i class="mdi mdi-file-document-edit-outline me-1"></i>Create PO</a>
                <a href="{{ route('product-in.create') }}" class="btn btn-outline-info"><i class="mdi mdi-download-outline me-1"></i>Incoming Goods</a>
                <a href="{{ route('product-out.create') }}" class="btn btn-outline-warning"><i class="mdi mdi-upload-outline me-1"></i>Delivery Order</a>
                <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary"><i class="mdi mdi-clipboard-list-outline me-1"></i>Check Stock</a>
                <a href="{{ route('opname.index') }}" class="btn btn-outline-dark"><i class="mdi mdi-tune-variant me-1"></i>Stock Adjustment</a>
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
            const borderColor = isDark ? '#404152' : '#dbdade';

            const statusSoEl = document.querySelector('#logStatusSoChart');
            if (statusSoEl) {
                new ApexCharts(statusSoEl, {
                    chart: { type: 'donut', height: 260 },
                    labels: ['New', 'In Progress', 'Delivery', 'Completed'],
                    series: @json($logSoStatusSeries),
                    colors: ['#696cff', '#ffab00', '#03c3ec', '#71dd37'],
                    legend: { position: 'bottom', labels: { colors: labelColor } },
                    dataLabels: { enabled: true },
                }).render();
            }

            const receivingEl = document.querySelector('#logReceivingChart');
            if (receivingEl) {
                new ApexCharts(receivingEl, {
                    chart: { type: 'bar', height: 260, toolbar: { show: false } },
                    series: [{ name: 'Received', data: @json($logReceivingSeries) }],
                    colors: ['#696cff'],
                    plotOptions: { bar: { borderRadius: 4, columnWidth: '50%' } },
                    dataLabels: { enabled: false },
                    xaxis: { categories: @json($logReceivingLabels), labels: { style: { colors: labelColor } } },
                    yaxis: { labels: { style: { colors: labelColor } }, forceNiceScale: true },
                    grid: { borderColor, strokeDashArray: 5 },
                }).render();
            }
        })();
    </script>
@endpush
