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
    <div class="col-sm-6 col-lg-3 mb-4">
        <a href="{{ route('purchase.index') }}" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-label-secondary rounded"><i class="mdi mdi-truck-outline mdi-24px"></i></div>
                        </div>
                        <div>
                            <small class="text-muted d-block">PO Pending Shipment</small>
                            <h4 class="mb-0">{{ $logPoPendingCount }} <small class="fs-6 fw-normal text-muted">PO</small></h4>
                        </div>
                    </div>
                    <small class="text-primary">View Details <i class="mdi mdi-arrow-right"></i></small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Auto PR / Incoming Goods -->
<div class="row mb-2">
    <div class="col-lg-6 mb-4">
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
                            <th class="text-nowrap">Item</th>
                            <th>Qty</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logPrFromSo as $pr)
                            @php
                                $dashPrPayload = [
                                    'id' => $pr->id,
                                    'no_pr' => $pr->no_pr,
                                    'no_pending' => optional($pr->pending)->no_pending ?? '-',
                                    'date' => \Carbon\Carbon::parse($pr->date)->translatedFormat('d/m/Y'),
                                    'client' => optional(optional(optional($pr->pending)->quote)->pic)->client->company ?? (optional($pr->pending)->company ?? '-'),
                                    'user' => optional($pr->user)->name ?? '-',
                                    'items' => $pr->details->map(function($d) {
                                        return [
                                            'commodity' => optional(optional($d->equivalent)->product)->commodity ?? '-',
                                            'brand' => optional($d->equivalent)->brand ?? '',
                                            'pn' => optional($d->equivalent)->pn ?? '',
                                            'qty' => $d->qty,
                                            'unit' => optional(optional($d->equivalent)->product)->unit ?? 'pcs',
                                            'note' => $d->note ?? '',
                                        ];
                                    })->values(),
                                ];
                            @endphp
                            <tr>
                                <td><a href="{{ route('purchase-request.show', $pr->id) }}" class="fw-semibold">{{ $pr->no_pr }}</a></td>
                                <td>
                                    @if ($pr->id_pending)
                                        <a href="{{ route('pending-po.show', $pr->id_pending) }}">{{ optional($pr->pending)->no_pending ?? '-' }}</a>
                                    @else
                                        {{ optional($pr->pending)->no_pending ?? '-' }}
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <script type="application/json" id="dash-pr-data-{{ $pr->id }}">
                                        {!! json_encode($dashPrPayload) !!}
                                    </script>
                                    <button type="button" 
                                            class="btn-item-preview d-inline-flex align-items-center gap-1 btn-dashboard-pr-drawer text-nowrap"
                                            data-pr-id="{{ $pr->id }}"
                                            data-bs-toggle="tooltip" 
                                            title="Klik untuk membuka slide-over rincian PR & item">
                                        <i class="mdi mdi-package-variant-closed font-14 text-primary"></i>
                                        <span>{{ $pr->details->count() }} item</span>
                                    </button>
                                </td>
                                <td><span class="fw-semibold text-primary">{{ $pr->details->sum('qty') }}</span></td>
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
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Incoming Goods - Pending Receipt</h5>
                <span class="badge bg-label-info">{{ $logIncomingPendingCount }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>No. PO</th>
                            <th>Supplier</th>
                            <th class="text-nowrap">Item</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logIncomingPending as $po)
                            @php
                                $poItems = $po->details->count() > 0 
                                    ? $po->details->map(function($d) {
                                        $name = $d->product ?: (optional($d->productItem)->commodity ?: (optional($d->unit)->name ?: '-'));
                                        return [
                                            'name' => $name,
                                            'qty' => $d->qty,
                                            'unit' => $d->info_qty ?: 'Pcs',
                                            'note' => $d->category ?: '',
                                        ];
                                    })
                                    : $po->prAllocations->map(function($a) {
                                        $commodity = optional(optional(optional($a->detail)->equivalent)->product)->commodity ?? '-';
                                        $brand = optional(optional($a->detail)->equivalent)->brand ?? '';
                                        $pn = optional(optional($a->detail)->equivalent)->pn ?? '';
                                        $brandPn = trim($brand . ' ' . $pn);
                                        return [
                                            'name' => $commodity . ($brandPn ? ' (' . $brandPn . ')' : ''),
                                            'qty' => $a->qty,
                                            'unit' => optional(optional(optional($a->detail)->equivalent)->product)->unit ?? 'Pcs',
                                            'note' => optional($a->detail)->note ?? '',
                                        ];
                                    });

                                $dashPoPayload = [
                                    'id' => $po->id,
                                    'no_po' => $po->no_po ?: ('#' . $po->id),
                                    'supplier' => optional($po->supplier)->supplier ?? ($po->company ?: '-'),
                                    'date' => \Carbon\Carbon::parse($po->date)->translatedFormat('d/m/Y'),
                                    'cargo' => $po->on_delivery_cargo ?: ($po->delivery ?: '-'),
                                    'no_resi' => $po->on_delivery_no_resi ?: '-',
                                    'items' => $poItems->values(),
                                ];
                                $itemCount = $poItems->count();
                            @endphp
                            <tr>
                                <td><span class="fw-semibold font-monospace">{{ $po->no_po ?: '#'.$po->id }}</span></td>
                                <td>{{ optional($po->supplier)->supplier ?? ($po->company ?: '-') }}</td>
                                <td class="text-nowrap">
                                    <script type="application/json" id="dash-po-data-{{ $po->id }}">
                                        {!! json_encode($dashPoPayload) !!}
                                    </script>
                                    <button type="button" 
                                            class="btn-item-preview d-inline-flex align-items-center gap-1 btn-dashboard-po-drawer text-nowrap"
                                            data-po-id="{{ $po->id }}"
                                            data-bs-toggle="tooltip" 
                                            title="Klik untuk membuka slide-over rincian PO & item">
                                        <i class="mdi mdi-package-variant-closed font-14 text-info"></i>
                                        <span>{{ $itemCount }} item</span>
                                    </button>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($po->date)->translatedFormat('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-label-info rounded-pill px-2 py-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Delivery">
                                        <i class="mdi mdi-truck-fast-outline font-14"></i>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No goods pending receipt.</td>
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

{{-- Sliding Quick Preview Drawer (Offcanvas) for Purchase Request --}}
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="dashboardPrOffcanvas" aria-labelledby="dashboardPrOffcanvasLabel" style="width: 520px; max-width: 92vw;">
    <div class="offcanvas-header bg-label-primary border-bottom py-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary text-white font-11 rounded-pill" id="dashPrDrawerNoPr">PR-0000</span>
                <span class="badge bg-label-warning rounded-pill px-3 py-1 font-11 fw-semibold"><i class="mdi mdi-clock-outline me-1"></i>Menunggu ACC</span>
            </div>
            <h5 class="offcanvas-title fw-bold text-heading font-16" id="dashboardPrOffcanvasLabel">Rincian Purchase Request (Auto SO)</h5>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        {{-- Summary Card --}}
        <div class="card border mb-3 bg-light">
            <div class="card-body p-3">
                <div class="pb-2 mb-2 border-bottom">
                    <span class="text-muted font-11 d-block text-uppercase fw-bold">Customer / Klien:</span>
                    <h6 class="fw-bold text-heading font-14 mb-0" id="dashPrDrawerCompany">-</h6>
                </div>
                <div class="row g-2 font-12">
                    <div class="col-6">
                        <span class="text-muted d-block font-11">No. Sales Order (SO):</span>
                        <strong class="text-primary font-monospace" id="dashPrDrawerNoPending">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block font-11">Tgl Pengajuan PR:</span>
                        <strong class="text-heading" id="dashPrDrawerDate">-</strong>
                    </div>
                    <div class="col-12">
                        <span class="text-muted d-block font-11">Diajukan Oleh:</span>
                        <span class="fw-semibold text-heading" id="dashPrDrawerUser">-</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Table Card --}}
        <div class="card border mb-3">
            <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                <span class="fw-bold font-12 text-uppercase text-heading">
                    <i class="mdi mdi-format-list-bulleted me-1 text-primary"></i> Daftar Item Sparepart / Barang
                </span>
                <span class="badge bg-label-primary rounded-pill font-11"><span id="dashPrDrawerItemCount">0</span> item</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead class="table-light font-11">
                        <tr>
                            <th style="width: 35px;" class="text-center">#</th>
                            <th>Deskripsi Barang</th>
                            <th class="text-end" style="width: 80px;">Qty</th>
                        </tr>
                    </thead>
                    <tbody id="dashPrDrawerItemsBody">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="#" id="dashPrDrawerDetailLink" class="btn btn-primary d-flex align-items-center justify-content-center">
                <i class="mdi mdi-open-in-new me-1"></i> Buka Halaman Detail PR Lengkap
            </a>
        </div>
    </div>
</div>

{{-- Sliding Quick Preview Drawer (Offcanvas) for Purchase Order (Incoming Goods) --}}
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="dashboardPoOffcanvas" aria-labelledby="dashboardPoOffcanvasLabel" style="width: 520px; max-width: 92vw;">
    <div class="offcanvas-header bg-label-info border-bottom py-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-info text-white font-11 rounded-pill" id="dashPoDrawerNoPo">PO-0000</span>
                <span class="badge bg-label-info rounded-pill px-3 py-1 font-11 fw-semibold"><i class="mdi mdi-truck-fast-outline me-1"></i>Sedang Dikirim</span>
            </div>
            <h5 class="offcanvas-title fw-bold text-heading font-16" id="dashboardPoOffcanvasLabel">Rincian Pembelian PO (Incoming Goods)</h5>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        {{-- Summary Card --}}
        <div class="card border mb-3 bg-light">
            <div class="card-body p-3">
                <div class="pb-2 mb-2 border-bottom">
                    <span class="text-muted font-11 d-block text-uppercase fw-bold">Supplier / Vendor:</span>
                    <h6 class="fw-bold text-heading font-14 mb-0" id="dashPoDrawerSupplier">-</h6>
                </div>
                <div class="row g-2 font-12">
                    <div class="col-6">
                        <span class="text-muted d-block font-11">Tgl Terbit PO:</span>
                        <strong class="text-heading" id="dashPoDrawerDate">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block font-11">Ekspedisi / Cargo:</span>
                        <strong class="text-info" id="dashPoDrawerCargo">-</strong>
                    </div>
                    <div class="col-12">
                        <span class="text-muted d-block font-11">No. Resi Pengiriman:</span>
                        <span class="fw-semibold font-monospace text-heading" id="dashPoDrawerResi">-</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Table Card --}}
        <div class="card border mb-3">
            <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                <span class="fw-bold font-12 text-uppercase text-heading">
                    <i class="mdi mdi-format-list-bulleted me-1 text-info"></i> Daftar Item Pembelian / Barang
                </span>
                <span class="badge bg-label-info rounded-pill font-11"><span id="dashPoDrawerItemCount">0</span> item</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead class="table-light font-11">
                        <tr>
                            <th style="width: 35px;" class="text-center">#</th>
                            <th>Deskripsi Barang</th>
                            <th class="text-end" style="width: 80px;">Qty</th>
                        </tr>
                    </thead>
                    <tbody id="dashPoDrawerItemsBody">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-grid gap-2">
            <a href="#" id="dashPoDrawerDetailLink" class="btn btn-info text-white d-flex align-items-center justify-content-center">
                <i class="mdi mdi-open-in-new me-1"></i> Buka Halaman Detail Purchase Order (PO)
            </a>
        </div>
    </div>
</div>

<style>
.btn-item-preview {
    background: #f4f5f9;
    border: 1px solid #e2e5ec;
    color: #435971;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 600;
    white-space: nowrap;
    transition: all 0.2s;
    cursor: pointer;
}
.btn-item-preview:hover {
    background: #696cff;
    border-color: #696cff;
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(105, 108, 255, 0.25);
}
.btn-item-preview:hover i,
.btn-item-preview:hover span {
    color: #ffffff !important;
}
</style>

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

            // Slide-over drawer handler for Auto PR items preview
            document.querySelectorAll('.btn-dashboard-pr-drawer').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const prId = this.getAttribute('data-pr-id');
                    let prData = {};
                    const scriptTag = document.getElementById('dash-pr-data-' + prId);
                    if (scriptTag) {
                        try {
                            prData = JSON.parse(scriptTag.textContent);
                        } catch (err) {
                            prData = {};
                        }
                    }

                    const noPr = prData.no_pr || 'PR-0000';
                    const noPending = prData.no_pending || '-';
                    const date = prData.date || '-';
                    const client = prData.client || '-';
                    const user = prData.user || '-';
                    const items = prData.items || [];

                    const noPrEl = document.querySelector('#dashPrDrawerNoPr');
                    if (noPrEl) noPrEl.textContent = noPr;
                    const compEl = document.querySelector('#dashPrDrawerCompany');
                    if (compEl) compEl.textContent = client;
                    const pendEl = document.querySelector('#dashPrDrawerNoPending');
                    if (pendEl) pendEl.textContent = noPending;
                    const dateEl = document.querySelector('#dashPrDrawerDate');
                    if (dateEl) dateEl.textContent = date;
                    const userEl = document.querySelector('#dashPrDrawerUser');
                    if (userEl) userEl.textContent = user;
                    const countEl = document.querySelector('#dashPrDrawerItemCount');
                    if (countEl) countEl.textContent = items.length;
                    const linkEl = document.querySelector('#dashPrDrawerDetailLink');
                    if (linkEl) linkEl.setAttribute('href', '/purchase-request/' + prId);

                    const tbody = document.querySelector('#dashPrDrawerItemsBody');
                    if (tbody) {
                        tbody.innerHTML = '';
                        if (items.length > 0) {
                            items.forEach(function(item, idx) {
                                const tr = document.createElement('tr');
                                const brandPn = [item.brand, item.pn].filter(Boolean).join(' ');
                                tr.innerHTML = `
                                    <td class="text-muted text-center align-middle font-11">${idx + 1}</td>
                                    <td>
                                        <div class="fw-semibold text-heading font-12">${item.commodity || '-'}</div>
                                        ${brandPn ? `<div class="font-11 text-muted">${brandPn}</div>` : ''}
                                        ${item.note ? `<div class="font-11 text-secondary fst-italic mt-0.5">${item.note}</div>` : ''}
                                    </td>
                                    <td class="text-end align-middle">
                                        <span class="fw-bold font-13 text-primary">${item.qty}</span>
                                        <span class="font-11 text-muted">${item.unit}</span>
                                    </td>
                                `;
                                tbody.appendChild(tr);
                            });
                        } else {
                            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-3 text-muted font-12">Tidak ada rincian item.</td></tr>';
                        }
                    }

                    const offcanvasEl = document.querySelector('#dashboardPrOffcanvas');
                    if (offcanvasEl) {
                        const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                        bsOffcanvas.show();
                    }
                });
            });

            // Slide-over drawer handler for PO Incoming Goods items preview
            document.querySelectorAll('.btn-dashboard-po-drawer').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const poId = this.getAttribute('data-po-id');
                    let poData = {};
                    const scriptTag = document.getElementById('dash-po-data-' + poId);
                    if (scriptTag) {
                        try {
                            poData = JSON.parse(scriptTag.textContent);
                        } catch (err) {
                            poData = {};
                        }
                    }

                    const noPo = poData.no_po || 'PO-0000';
                    const supplier = poData.supplier || '-';
                    const date = poData.date || '-';
                    const cargo = poData.cargo || '-';
                    const noResi = poData.no_resi || '-';
                    const items = poData.items || [];

                    const noPoEl = document.querySelector('#dashPoDrawerNoPo');
                    if (noPoEl) noPoEl.textContent = noPo;
                    const suppEl = document.querySelector('#dashPoDrawerSupplier');
                    if (suppEl) suppEl.textContent = supplier;
                    const dateEl = document.querySelector('#dashPoDrawerDate');
                    if (dateEl) dateEl.textContent = date;
                    const cargoEl = document.querySelector('#dashPoDrawerCargo');
                    if (cargoEl) cargoEl.textContent = cargo;
                    const resiEl = document.querySelector('#dashPoDrawerResi');
                    if (resiEl) resiEl.textContent = noResi;
                    const countEl = document.querySelector('#dashPoDrawerItemCount');
                    if (countEl) countEl.textContent = items.length;
                    const linkEl = document.querySelector('#dashPoDrawerDetailLink');
                    if (linkEl) linkEl.setAttribute('href', '/purchase/' + poId);

                    const tbody = document.querySelector('#dashPoDrawerItemsBody');
                    if (tbody) {
                        tbody.innerHTML = '';
                        if (items.length > 0) {
                            items.forEach(function(item, idx) {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td class="text-muted text-center align-middle font-11">${idx + 1}</td>
                                    <td>
                                        <div class="fw-semibold text-heading font-12">${item.name || '-'}</div>
                                        ${item.note ? `<div class="font-11 text-muted fst-italic mt-0.5">${item.note}</div>` : ''}
                                    </td>
                                    <td class="text-end align-middle">
                                        <span class="fw-bold font-13 text-info">${item.qty}</span>
                                        <span class="font-11 text-muted">${item.unit || 'Pcs'}</span>
                                    </td>
                                `;
                                tbody.appendChild(tr);
                            });
                        } else {
                            tbody.innerHTML = '<tr><td colspan="3" class="text-center py-3 text-muted font-12">Tidak ada rincian item.</td></tr>';
                        }
                    }

                    const offcanvasEl = document.querySelector('#dashboardPoOffcanvas');
                    if (offcanvasEl) {
                        const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                        bsOffcanvas.show();
                    }
                });
            });

            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return bootstrap.Tooltip.getOrCreateInstance(tooltipTriggerEl);
            });
        })();
    </script>
@endpush
