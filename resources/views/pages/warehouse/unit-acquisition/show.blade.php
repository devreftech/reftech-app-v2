@extends('layouts.sales.app')
@section('title', 'Unit Acquisition Detail')
@section('content')
    @php
        $statusUnitBadges = [
            'OK' => 'bg-label-success',
            'Rental' => 'bg-label-primary',
            'Service' => 'bg-label-warning',
            'Breakdown' => 'bg-label-danger',
            'Reserved' => 'bg-label-info',
            'Sold' => 'bg-label-dark',
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                {{ $fixed->code }}
                @if ($fixed->kondisi)
                    <span class="badge bg-label-secondary fs-6 align-middle">{{ $fixed->kondisi === 'Baru' ? 'Unit Baru' : 'Unit Second' }}</span>
                @endif
            </h4>
            <p class="text-muted mb-0">
                @if ($fixed->unit)
                    {{ $fixed->unit->brand }} {{ $fixed->unit->model }} — {{ $fixed->unit->sku }} &bull;
                @endif
                SN {{ $fixed->serial_number ?: '-' }}
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if ($fixed->qc_status === 'checking')
                <span class="badge bg-label-warning fs-6 px-3 py-2">Dalam Pengecekan</span>
            @elseif ($fixed->qc_status === 'ok')
                <span class="badge bg-label-success fs-6 px-3 py-2">OK — Siap Ditawarkan</span>
            @elseif ($fixed->qc_status === 'reject')
                <span class="badge bg-label-danger fs-6 px-3 py-2">Reject</span>
            @endif
            @if ($fixed->status_unit)
                <span class="badge {{ $statusUnitBadges[$fixed->status_unit] ?? 'bg-label-secondary' }} fs-6 px-3 py-2">
                    {{ $fixed->status_unit }}
                </span>
            @endif
            <button class="btn btn-outline-secondary btn-sm" id="backButton">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 col-12 mb-4 mb-xl-0">
            <div class="card mb-4">
                <div class="card-body d-flex flex-wrap gap-4">
                    <div>
                        <div class="text-muted small mb-1">No. Invoice</div>
                        <div class="fw-semibold">{{ $fixed->no_invoice ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Tanggal Beli</div>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($fixed->beli)->format('d-m-Y') }}</div>
                    </div>
                    <div>
                        <div class="text-muted small mb-1">Harga Jual</div>
                        <div class="fw-semibold">
                            {{ $fixed->harga_jual ? 'Rp ' . number_format($fixed->harga_jual, 0, ',', '.') : 'Belum diset' }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header p-0 border-bottom bg-transparent">
                    <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="unitDetailTabs" role="tablist">
                        @if ($fixed->unit)
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link active px-3 py-2 fw-semibold" role="tab"
                                    data-bs-toggle="tab" data-bs-target="#tab-spesifikasi" aria-selected="true">
                                    <i class="mdi mdi-format-list-bulleted-square me-1"></i>Spesifikasi
                                </button>
                            </li>
                        @endif
                        @if ($fixed->qc_status === 'ok')
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link px-3 py-2 fw-semibold {{ !$fixed->unit ? 'active' : '' }}" role="tab"
                                    data-bs-toggle="tab" data-bs-target="#tab-rental" aria-selected="false">
                                    <i class="mdi mdi-truck-delivery-outline me-1"></i>Rental
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link px-3 py-2 fw-semibold" role="tab"
                                    data-bs-toggle="tab" data-bs-target="#tab-quotation" aria-selected="false">
                                    <i class="mdi mdi-file-document-outline me-1"></i>Quotation
                                    @if ($confirmedOffers->isNotEmpty())
                                        <span class="badge bg-label-primary rounded-pill ms-1">{{ $confirmedOffers->count() }}</span>
                                    @endif
                                </button>
                            </li>
                        @endif
                        @if ($fixed->machine)
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link px-3 py-2 fw-semibold {{ $fixed->qc_status !== 'ok' && !$fixed->unit ? 'active' : '' }}" role="tab"
                                    data-bs-toggle="tab" data-bs-target="#tab-service-report" aria-selected="false">
                                    <i class="mdi mdi-notebook-edit-outline me-1"></i>Service Report
                                </button>
                            </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link px-3 py-2 fw-semibold {{ $fixed->qc_status !== 'ok' && !$fixed->machine && !$fixed->unit ? 'active' : '' }}" role="tab"
                                data-bs-toggle="tab" data-bs-target="#tab-servis-part" aria-selected="false">
                                <i class="mdi mdi-wrench-outline me-1"></i>Servis / Spare Part
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content p-0">
                    @if ($fixed->unit)
                        @php
                            $isCompressor = in_array($fixed->unit->unit, ['PISTON COMPRESSOR', 'AIR COMPRESSOR SCREW']);
                            $isDryer = in_array($fixed->unit->unit, ['REFRIGERANT AIR DRYER', 'DESICANT DRYER']);
                        @endphp
                        <div class="tab-pane fade show active p-4" id="tab-spesifikasi" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-6">
                                    @include('components.detail-row', ['label' => 'Category', 'value' => $fixed->unit->unit])
                                    @include('components.detail-row', ['label' => 'SKU', 'value' => $fixed->unit->sku])
                                    @include('components.detail-row', ['label' => 'Brand', 'value' => $fixed->unit->brand])
                                    @include('components.detail-row', ['label' => 'Model', 'value' => $fixed->unit->model])
                                    @include('components.detail-row', ['label' => 'Generation', 'value' => $fixed->unit->generation])

                                    @if ($isCompressor)
                                        @include('components.detail-row', ['label' => 'Type', 'value' => $fixed->unit->type_unit])
                                        @include('components.detail-row', ['label' => 'Motor Power', 'value' => $fixed->unit->power])
                                        @include('components.detail-row', ['label' => 'Air Capacity', 'value' => $fixed->unit->air_cap ? $fixed->unit->air_cap . ' m³/min' : null])
                                        @include('components.detail-row', ['label' => 'Max. Pressure', 'value' => $fixed->unit->bar ? $fixed->unit->bar . ' Bar' : null])
                                        @include('components.detail-row', ['label' => 'Voltage', 'value' => $fixed->unit->voltage])
                                        @include('components.detail-row', ['label' => 'Drive', 'value' => $fixed->unit->connect])
                                        @include('components.detail-row', ['label' => 'Cooling', 'value' => $fixed->unit->cooling])
                                        @include('components.detail-row', ['label' => 'Discharge', 'value' => $fixed->unit->exhaust])
                                    @elseif ($isDryer)
                                        @include('components.detail-row', ['label' => 'Air Capacity', 'value' => $fixed->unit->air_cap ? $fixed->unit->air_cap . ' m³/min' : null])
                                        @include('components.detail-row', ['label' => 'Refrigerant Type', 'value' => $fixed->unit->refrigerant_type])
                                        @include('components.detail-row', ['label' => 'PDP', 'value' => $fixed->unit->pdp])
                                    @endif
                                </div>
                                <div class="col-lg-6">
                                    @include('components.detail-row', ['label' => 'Filtration', 'value' => $fixed->unit->filtration])
                                    @include('components.detail-row', ['label' => 'Oil Content', 'value' => $fixed->unit->oil_content])
                                    @include('components.detail-row', ['label' => 'Grade', 'value' => $fixed->unit->grade])
                                    @include('components.detail-row', ['label' => 'Capacity', 'value' => $fixed->unit->capacity])
                                    @include('components.detail-row', ['label' => 'Material', 'value' => $fixed->unit->material])
                                    @include('components.detail-row', ['label' => 'Test Pressure', 'value' => $fixed->unit->test_pressure])
                                    @include('components.detail-row', ['label' => 'Inlet Pressure', 'value' => $fixed->unit->inlet_pressure])
                                    @include('components.detail-row', ['label' => 'Outlet Pressure', 'value' => $fixed->unit->outlet_pressure])
                                    @include('components.detail-row', ['label' => 'Inlet Capacity', 'value' => $fixed->unit->inlet_cap])
                                    @include('components.detail-row', ['label' => 'Outlet Capacity', 'value' => $fixed->unit->outlet_cap])
                                    @include('components.detail-row', ['label' => 'Dimension', 'value' => $fixed->unit->dimension])
                                    @include('components.detail-row', ['label' => 'Weight', 'value' => $fixed->unit->weight ? $fixed->unit->weight . ' Kg' : null])
                                    @if (!$isCompressor && !$isDryer)
                                        @include('components.detail-row', ['label' => 'Description', 'value' => $fixed->unit->desc])
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($fixed->qc_status === 'ok')
                        <div class="tab-pane fade {{ !$fixed->unit ? 'show active' : '' }} p-4" id="tab-rental" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-7 mb-4 mb-lg-0">
                                    @if ($fixed->status_unit === 'OK')
                                        {{-- Scan OUT — jadikan Rental --}}
                                        <form action="{{ route('fixed-asset.scan.out', $fixed->id) }}" method="post">
                                            @csrf
                                            <p class="text-muted small">Unit siap dikirim buat rental. Isi tujuan penyewaan.</p>

                                            @if ($confirmedOffers->isNotEmpty())
                                                <div class="mb-3">
                                                    <label class="form-label small text-muted mb-1">Penawaran yang Sudah PO buat Unit Ini</label>
                                                    <div class="d-flex flex-column gap-2">
                                                        @foreach ($confirmedOffers as $offer)
                                                            <button type="button" class="btn btn-outline-primary text-start btn-sm offer-pick"
                                                                data-client-id="{{ $offer->client->id ?? '' }}"
                                                                data-client-company="{{ $offer->client->company ?? '-' }}">
                                                                <i class="mdi mdi-file-check-outline me-1"></i>
                                                                <strong>{{ $offer->client->company ?? '-' }}</strong>
                                                                <span class="text-muted">— {{ $offer->no_quote }} @if ($offer->po_number) (PO: {{ $offer->po_number }}) @endif</span>
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                    <small class="text-muted">Klik buat langsung pilih client-nya, atau cari manual di bawah.</small>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label small text-muted mb-1">Client Penyewa</label>
                                                <select class="form-select select2-scan-client" name="id_client" id="scanClient" required>
                                                    <option value="" selected disabled>-- Cari Client --</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small text-muted mb-1">PIC Internal (Staff yang Menangani)</label>
                                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                                                <small class="text-muted">Otomatis diambil dari akun yang lagi login.</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small text-muted mb-1">Catatan (opsional)</label>
                                                <input type="text" class="form-control" name="note">
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-truck-delivery-outline me-1"></i> Jadikan Rental
                                            </button>
                                        </form>
                                    @elseif ($fixed->status_unit === 'Rental')
                                        {{-- Scan IN — terima kembali --}}
                                        <p class="text-muted small">Unit ini lagi disewa. Konfirmasi kalau unit fisiknya udah balik ke gudang.</p>
                                        @if ($lastOutScan)
                                            <div class="border rounded-3 p-3 mb-3 bg-light-subtle">
                                                <div class="small text-muted mb-1">Disewa oleh</div>
                                                <div class="fw-semibold">{{ optional($lastOutScan->client)->company ?? '-' }}</div>
                                                <div class="small text-muted mt-2 mb-1">PIC Internal</div>
                                                <div>{{ optional($lastOutScan->picInternal)->name ?? '-' }}</div>
                                                <div class="small text-muted mt-2 mb-1">Tanggal Keluar</div>
                                                <div>{{ $lastOutScan->created_at->format('d-m-Y H:i') }}</div>
                                            </div>
                                        @endif
                                        <form action="{{ route('fixed-asset.scan.in', $fixed->id) }}" method="post">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label small text-muted mb-1">Catatan (opsional)</label>
                                                <input type="text" class="form-control" name="note">
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-check-circle-outline me-1"></i> Terima Kembali
                                            </button>
                                        </form>
                                    @else
                                        <div class="alert alert-secondary mb-0">
                                            <i class="mdi mdi-information-outline me-1"></i>
                                            Status unit sekarang <strong>{{ $fixed->status_unit }}</strong> — belum bisa dijadikan Rental dari sini.
                                        </div>
                                    @endif

                                    @if ($fixed->machine && in_array($fixed->status_unit, ['OK', 'Rental']))
                                        <a href="{{ route('service-reports.unit.machine', [$fixed->machine->id_unit, $fixed->id_machine]) }}"
                                            class="btn btn-outline-secondary w-100 mt-2">
                                            <i class="mdi mdi-notebook-edit-outline me-1"></i> Buat Service Report (opsional)
                                        </a>
                                    @endif
                                </div>
                                <div class="col-lg-5 text-center">
                                    <div class="border rounded-3 p-3 h-100 d-flex flex-column justify-content-center">
                                        <img src="{{ route('fixed-asset.barcode', $fixed->id) }}" alt="Barcode {{ $fixed->code }}" class="img-fluid mb-2 mx-auto" style="max-width: 200px;">
                                        <p class="text-muted small mb-0">Scan QR ini buat buka halaman unit ini lagi — print & tempel di unit fisiknya.</p>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <h6 class="fw-semibold mb-2">Riwayat Rental</h6>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                            <th>Client</th>
                                            <th>PIC Internal</th>
                                            <th>Di-scan Oleh</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($fixed->rentalScans as $scan)
                                            <tr>
                                                <td>{{ $scan->created_at->format('d-m-Y H:i') }}</td>
                                                <td>
                                                    @if ($scan->action === 'out')
                                                        <span class="badge bg-label-primary">Keluar (Rental)</span>
                                                    @else
                                                        <span class="badge bg-label-success">Kembali (OK)</span>
                                                    @endif
                                                </td>
                                                <td>{{ optional($scan->client)->company ?? '-' }}</td>
                                                <td>{{ optional($scan->picInternal)->name ?? '-' }}</td>
                                                <td>{{ optional($scan->scannedBy)->name ?? '-' }}</td>
                                                <td>{{ $scan->note ?: '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">Belum ada riwayat rental buat unit ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade p-4" id="tab-quotation" role="tabpanel">
                            <p class="text-muted small">Penawaran (Smart Quote) yang statusnya udah PO dan nyebut unit fisik ini secara spesifik.</p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Quote</th>
                                            <th>No. PO</th>
                                            <th>Client</th>
                                            <th>Tanggal</th>
                                            <th class="text-end">Nilai Item Unit Ini</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($confirmedOffers as $offer)
                                            <tr>
                                                <td>{{ $offer->no_quote }}</td>
                                                <td>{{ $offer->po_number ?: '-' }}</td>
                                                <td>{{ optional($offer->client)->company ?? '-' }}</td>
                                                <td>{{ $offer->date ? $offer->date->format('d-m-Y') : '-' }}</td>
                                                <td class="text-end">Rp {{ number_format($offer->details->sum('amount'), 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('unit-quotation.show', $offer->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="mdi mdi-open-in-new"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">Belum ada penawaran yang udah PO buat unit ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if ($fixed->machine)
                        <div class="tab-pane fade {{ $fixed->qc_status !== 'ok' && !$fixed->unit ? 'show active' : '' }} p-0" id="tab-service-report" role="tabpanel">
                            <div class="card-datatable table-responsive">
                                <table class="table table-striped table-bordered m-0 datatable-service-report-history">
                                    <thead class="table-light border-top">
                                        <tr>
                                            <th>Service Report</th>
                                            <th>Service Type</th>
                                            <th>Job Description</th>
                                            <th>Date</th>
                                            <th>Technician</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="tab-pane fade {{ $fixed->qc_status !== 'ok' && !$fixed->machine && !$fixed->unit ? 'show active' : '' }} p-4" id="tab-servis-part" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered m-0">
                                <thead class="table-light border-top">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Spare Part</th>
                                        <th>Warehouse</th>
                                        <th>Qty</th>
                                        <th>Amount</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($services as $service)
                                        <tr style="font-size: 13px">
                                            <td>{{ \Carbon\Carbon::parse($service->date)->format('d-m-Y') }}</td>
                                            <td>{{ $service->detailProduct?->product?->commodity ?? '-' }}</td>
                                            <td>{{ $service->warehouse }}</td>
                                            <td>{{ $service->qty }}</td>
                                            <td>Rp {{ number_format($service->amount, 0, ',', '.') }}</td>
                                            <td>{{ $service->note }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada servis tercatat</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-12">
            <div class="card">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-semibold">Aksi</h6>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    @if ($fixed->qc_status === 'checking' || $fixed->kondisi === 'Baru')
                        <a href="{{ route('unit-acquisition.service.create', $fixed->id) }}"
                            class="btn btn-primary d-flex align-items-center justify-content-center gap-2 waves-effect">
                            <i class="mdi mdi-wrench-outline"></i> Tambah Servis / Spare Part
                        </a>
                    @endif

                    @if ($fixed->qc_status === 'checking')
                        @if (auth()->user()->role === 'Admin')
                            <div class="small text-muted fw-semibold text-uppercase mt-1">Konfirmasi QC</div>
                            <form action="{{ route('unit-acquisition.confirm', $fixed->id) }}" method="post">
                                @csrf
                                <input type="hidden" name="decision" value="ok">
                                <button type="submit" class="btn btn-success d-flex align-items-center justify-content-center gap-2 w-100 waves-effect">
                                    <i class="mdi mdi-check-circle-outline"></i> Konfirmasi OK — Siap Ditawarkan
                                </button>
                            </form>
                            <form action="{{ route('unit-acquisition.confirm', $fixed->id) }}" method="post">
                                @csrf
                                <input type="hidden" name="decision" value="reject">
                                <button type="submit" class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2 w-100 waves-effect">
                                    <i class="mdi mdi-close-circle-outline"></i> Reject Unit
                                </button>
                            </form>
                        @else
                            <p class="text-muted small mb-0">Menunggu konfirmasi Admin.</p>
                        @endif
                    @endif

                    @if ($fixed->qc_status === 'ok' && auth()->user()->role === 'Admin')
                        <div class="small text-muted fw-semibold text-uppercase mt-1">Pengaturan</div>
                        <form action="{{ route('unit-acquisition.status', $fixed->id) }}" method="post">
                            @csrf
                            <div class="form-floating form-floating-outline mb-2">
                                <select class="form-select" name="status_unit">
                                    <option value="OK" {{ $fixed->status_unit === 'OK' ? 'selected' : '' }}>OK</option>
                                    <option value="Service" {{ $fixed->status_unit === 'Service' ? 'selected' : '' }}>Sedang Service</option>
                                    <option value="Rental" {{ $fixed->status_unit === 'Rental' ? 'selected' : '' }}>Sedang Rental</option>
                                    <option value="Breakdown" {{ $fixed->status_unit === 'Breakdown' ? 'selected' : '' }}>Breakdown</option>
                                    <option value="Reserved" {{ $fixed->status_unit === 'Reserved' ? 'selected' : '' }}>Reserved</option>
                                    <option value="Sold" {{ $fixed->status_unit === 'Sold' ? 'selected' : '' }}>Sold</option>
                                </select>
                                <label>Status Unit</label>
                            </div>
                            <button type="submit" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2 w-100 waves-effect">
                                <i class="mdi mdi-pencil-outline"></i> Update Status
                            </button>
                        </form>
                        <form action="{{ route('unit-acquisition.harga-jual', $fixed->id) }}" method="post">
                            @csrf
                            <div class="input-group mb-2">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah-input" id="hargaJualDisplay"
                                    placeholder="0" autocomplete="off"
                                    value="{{ $fixed->harga_jual ? number_format(old('harga_jual', $fixed->harga_jual), 0, ',', '.') : '' }}">
                                <input type="hidden" name="harga_jual" id="hargaJualRaw"
                                    value="{{ old('harga_jual', $fixed->harga_jual) }}">
                            </div>
                            <button type="submit" class="btn btn-outline-primary d-flex align-items-center justify-content-center gap-2 w-100 waves-effect">
                                <i class="mdi mdi-currency-usd"></i> Simpan Harga Jual
                            </button>
                        </form>
                    @endif

                    <div class="small text-muted fw-semibold text-uppercase mt-1">Lainnya</div>
                    @if ($fixed->machine)
                        <a href="{{ route('service-reports.unit.machine', [$fixed->machine->id_unit, $fixed->id_machine]) }}"
                            class="btn {{ $fixed->status_unit === 'Rental' ? 'btn-warning' : 'btn-outline-secondary' }} d-flex align-items-center justify-content-center gap-2 waves-effect">
                            <i class="mdi mdi-notebook-edit-outline"></i>
                            {{ $fixed->status_unit === 'Rental' ? 'Service Report (Wajib Sebelum Sewa)' : 'Service Report' }}
                        </a>
                    @endif
                    <a href="{{ route('fixed.edit', $fixed->id) }}"
                        class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 waves-effect">
                        <i class="mdi mdi-square-edit-outline"></i> Edit Data
                    </a>
                    <a href="{{ route('fixed.show', $fixed->id) }}"
                        class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 waves-effect">
                        <i class="mdi mdi-finance"></i> Lihat di Finance
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush
@push('script')
    <script>
        $('#backButton').click(function() {
            window.history.back();
        });

        document.getElementById('hargaJualDisplay')?.addEventListener('input', function() {
            var raw = this.value.replace(/\D/g, '');
            this.value = raw ? String(parseInt(raw)).replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            document.getElementById('hargaJualRaw').value = raw || '';
        });

        // Form "Jadikan Rental" — Client Penyewa, select2 AJAX + shortcut dari
        // penawaran yang udah PO.
        if ($('#scanClient').length) {
            $('#scanClient').select2({
                width: '100%',
                placeholder: 'Cari nama customer...',
                minimumInputLength: 2,
                ajax: {
                    url: '/db/client/search',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) { return { q: params.term }; },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (c) {
                                return { id: c.id, text: c.company };
                            })
                        };
                    },
                },
            });

            $('.offer-pick').on('click', function () {
                var id = $(this).data('client-id');
                var company = $(this).data('client-company');
                if (!id) return;
                var option = new Option(company, id, true, true);
                $('#scanClient').append(option).trigger('change');
            });
        }

        @if ($fixed->machine)
            $(function() {
                var dtServiceReport = $('.datatable-service-report-history').DataTable({
                    ajax: {
                        type: 'GET',
                        url: '/db/service-reports/machine/{{ $fixed->id_machine }}'
                    },
                    columns: [
                        { data: 'no_service' },
                        { data: 'type' },
                        { data: 'jobdesc' },
                        { data: 'date' },
                        { data: 'technician' },
                    ],
                    columnDefs: [{
                            targets: 0,
                            render: function(data, type, full) {
                                var url = route('service-reports.show', full.id);
                                return '<a href="' + url + '" class="fw-semibold text-primary">' + (data ??
                                    '-') + '</a>';
                            }
                        },
                        {
                            targets: 2,
                            render: function(data) {
                                if (!data) return '-';
                                return data.length > 60 ?
                                    '<span title="' + data + '">' + data.substring(0, 60) + '...</span>' :
                                    data;
                            }
                        },
                        {
                            targets: 3,
                            className: 'text-center',
                            render: function(data) {
                                if (!data) return '-';
                                var d = new Date(data);
                                return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1))
                                    .slice(-2) + '-' + d.getFullYear();
                            }
                        },
                    ],
                    order: [
                        [3, 'desc']
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                        '<"table-responsive"t>' +
                        '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                });

                // Tab yang start hidden bikin DataTables ngitung lebar kolom salah.
                $('button[data-bs-target="#tab-service-report"]').on('shown.bs.tab', function () {
                    dtServiceReport.columns.adjust().draw(false);
                });
            });
        @endif
    </script>
@endpush
