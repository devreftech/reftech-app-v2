@extends('layouts.sales.app')
@section('title', 'Detail SUO — ' . $suo->no_suo)

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css"/>
    <style>
        .suo-page-header {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fc 100%);
            border: 1px solid #e9edf4;
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .suo-meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .card-suo-modern {
            border: 1px solid #e9edf4;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            overflow: hidden;
            background: #ffffff;
            margin-bottom: 1.5rem;
        }
        .card-suo-modern .card-header-modern {
            padding: 1rem 1.4rem;
            background: #fbfcfd;
            border-bottom: 1px solid #edf0f5;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .suo-info-box {
            background: #f8fafd;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            height: 100%;
        }
        .suo-info-box .info-label {
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #8c93a0;
            font-weight: 700;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .suo-info-box .info-value {
            font-size: 0.93rem;
            font-weight: 600;
            color: #2b303a;
            line-height: 1.4;
        }
        .table-suo th {
            font-size: 0.76rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #5d6371;
            background-color: #f7f9fc !important;
            padding: 10px 14px !important;
            border-color: #ebedf2 !important;
        }
        .table-suo td {
            font-size: 0.84rem;
            padding: 11px 14px !important;
            border-color: #ebedf2 !important;
            vertical-align: middle;
        }
        .table-suo tbody tr:hover {
            background-color: #fbfcfe;
        }
        .timeline-suo-item {
            position: relative;
            padding-left: 28px;
            padding-bottom: 18px;
        }
        .timeline-suo-item:last-child {
            padding-bottom: 0;
        }
        .timeline-suo-item::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 22px;
            bottom: 0;
            width: 2px;
            background: #e9edf4;
        }
        .timeline-suo-item:last-child::before {
            display: none;
        }
        .timeline-suo-dot {
            position: absolute;
            left: 0;
            top: 2px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-action-primary {
            background: linear-gradient(135deg, #696cff, #5054ea) !important;
            color: #ffffff !important;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 9px 16px;
            box-shadow: 0 3px 10px rgba(105, 108, 255, 0.25);
            transition: all 0.2s ease;
        }
        .btn-action-primary:hover,
        .btn-action-primary:focus,
        .btn-action-primary:active,
        .btn-action-primary * {
            color: #ffffff !important;
        }
        .btn-action-primary:hover {
            background: linear-gradient(135deg, #5c5fe7, #4549d6) !important;
            box-shadow: 0 5px 14px rgba(105, 108, 255, 0.35);
            transform: translateY(-1px);
        }
        .btn-action-success {
            background: linear-gradient(135deg, #71dd37, #59c021) !important;
            color: #ffffff !important;
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 9px 16px;
            box-shadow: 0 3px 10px rgba(113, 221, 55, 0.25);
            transition: all 0.2s ease;
        }
        .btn-action-success:hover,
        .btn-action-success:focus,
        .btn-action-success:active,
        .btn-action-success * {
            color: #ffffff !important;
        }
        .btn-action-success:hover {
            background: linear-gradient(135deg, #64ca2f, #4eae19) !important;
            box-shadow: 0 5px 14px rgba(113, 221, 55, 0.35);
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('content')
    {{-- Header Banner Modern --}}
    <div class="suo-page-header d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('suo.index') }}" class="text-muted text-decoration-none small d-inline-flex align-items-center">
                    <i class="mdi mdi-arrow-left fs-5 me-1"></i> Kembali ke SUO
                </a>
                <span class="text-muted">/</span>
                <span class="badge bg-label-danger fw-bold rounded-pill" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    <i class="mdi mdi-fire me-1"></i>URGENT ORDER
                </span>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <h4 class="fw-bold mb-0 font-monospace text-primary">{{ $suo->no_suo }}</h4>
                <span class="text-muted ms-1">• {{ $suo->company }}</span>
            </div>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            @php
                $statusConfig = [
                    'draft' => ['class' => 'bg-label-secondary text-secondary', 'icon' => 'mdi-file-edit-outline', 'label' => 'Draft'],
                    'submitted' => ['class' => 'bg-label-warning text-warning', 'icon' => 'mdi-clock-alert-outline', 'label' => 'Menunggu Konfirmasi Gudang'],
                    'confirmed' => ['class' => 'bg-label-info text-info', 'icon' => 'mdi-check-circle-outline', 'label' => 'Stok Dikonfirmasi'],
                    'goods_out' => ['class' => 'bg-label-primary text-primary', 'icon' => 'mdi-truck-delivery-outline', 'label' => 'Barang Keluar'],
                    'converted' => ['class' => 'bg-label-success text-success', 'icon' => 'mdi-receipt-text-check-outline', 'label' => 'Dikonversi ke Invoice'],
                ];
                $currStatus = $statusConfig[$suo->status] ?? ['class' => 'bg-label-secondary', 'icon' => 'mdi-information-outline', 'label' => strtoupper($suo->status)];
            @endphp
            <div class="suo-meta-pill {{ $currStatus['class'] }} border">
                <i class="mdi {{ $currStatus['icon'] }} fs-6"></i>
                <span>{{ $currStatus['label'] }}</span>
            </div>
            <div class="text-muted small ps-2 border-start">
                <i class="mdi mdi-calendar-blank-outline me-1"></i>{{ \Carbon\Carbon::parse($suo->created_at)->format('d M Y, H:i') }}
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible shadow-sm d-flex align-items-center mb-3">
            <i class="mdi mdi-check-circle fs-4 me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible shadow-sm d-flex align-items-center mb-3">
            <i class="mdi mdi-information fs-4 me-2"></i>
            <div>{{ session('info') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Main Content Area --}}
        <div class="col-xl-9 col-md-8 col-12">
            {{-- Info Cards Grid --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="suo-info-box">
                        <div class="info-label">
                            <i class="mdi mdi-domain text-primary"></i> Perusahaan / Customer
                        </div>
                        <div class="info-value text-truncate" title="{{ $suo->company }}">{{ $suo->company }}</div>
                        <div class="small text-muted mt-1">
                            <i class="mdi mdi-account text-secondary me-1"></i>PIC: <strong class="text-dark">{{ $suo->pic ?: '-' }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="suo-info-box">
                        <div class="info-label">
                            <i class="mdi mdi-map-marker-outline text-danger"></i> Alamat Pengiriman
                        </div>
                        <div class="info-value" style="font-size: 0.84rem; max-height: 52px; overflow-y: auto;">
                            {{ $suo->address ?: '-' }}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-12 col-12">
                    <div class="suo-info-box">
                        <div class="info-label">
                            <i class="mdi mdi-account-tie text-info"></i> Sales Pemohon & Catatan
                        </div>
                        <div class="info-value d-flex align-items-center gap-2">
                            @if ($suo->sales && $suo->sales->image)
                                <img src="{{ url($suo->sales->image) }}" class="rounded-circle" style="width:24px;height:24px;object-fit:cover;" alt="">
                            @else
                                <span class="badge bg-label-info rounded-circle p-1" style="width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;">
                                    {{ $suo->sales ? substr($suo->sales->name, 0, 1) : 'S' }}
                                </span>
                            @endif
                            <span>{{ $suo->sales->name ?? '-' }}</span>
                            @if ($suo->sales && $suo->sales->code)
                                <span class="badge bg-label-secondary" style="font-size:10px;">{{ $suo->sales->code }}</span>
                            @endif
                        </div>
                        <div class="small text-muted text-truncate mt-1" title="{{ $suo->notes }}">
                            <em>{{ $suo->notes ?: 'Tidak ada catatan tambahan.' }}</em>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Items Card --}}
            <div class="card-suo-modern">
                <div class="card-header-modern">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-format-list-bulleted-square text-primary fs-5"></i>
                        <h6 class="mb-0 fw-bold">Daftar Barang Urgent Order (SUO)</h6>
                    </div>
                    <span class="badge bg-label-secondary rounded-pill fw-semibold">{{ $suo->detail->count() }} Item</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-suo m-0">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">No</th>
                                <th>Item</th>
                                <th class="text-center" style="width: 8%;">Qty</th>
                                <th style="width: 8%;">Satuan</th>
                                <th class="text-center" style="width: 14%;">Stok Sistem</th>
                                <th class="text-center" style="width: 14%;">Status Stok</th>
                                <th style="width: 13%;">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suo->detail as $i => $item)
                                @php
                                    $avail = (float) ($item->available_stock ?? 0);
                                    $req = (float) $item->qty;
                                @endphp
                                <tr>
                                    <td class="text-center fw-semibold text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        {{-- Baris 1: Brand + Part Number --}}
                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                            @if (!empty($item->brand))
                                                <span class="badge bg-label-primary fw-bold">{{ $item->brand }}</span>
                                            @endif
                                            @if (!empty($item->part_number))
                                                <span class="badge bg-label-secondary font-monospace fw-semibold text-dark">
                                                    <i class="mdi mdi-barcode me-1"></i>{{ $item->part_number }}
                                                </span>
                                            @endif
                                        </div>
                                        {{-- Baris 2: Deskripsi --}}
                                        <div class="fw-semibold text-dark" style="font-size: 0.9rem; line-height: 1.35;">
                                            {{ $item->item_name }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-dark fw-bold px-2 py-1 fs-6">{{ $item->qty }}</span>
                                    </td>
                                    <td><span class="text-muted fw-medium">{{ $item->unit ?? '-' }}</span></td>
                                    <td class="text-center">
                                        @if ($avail >= $req)
                                            <span class="badge bg-label-success fw-bold font-monospace px-2 py-1" title="Stok sistem mencukupi kebutuhan order">
                                                <i class="mdi mdi-check-bold me-1"></i>{{ $avail }} {{ $item->stock_unit ?? $item->unit }}
                                            </span>
                                        @elseif ($avail > 0)
                                            <span class="badge bg-label-warning fw-bold font-monospace px-2 py-1" title="Stok sistem kurang dari kebutuhan order">
                                                <i class="mdi mdi-alert me-1"></i>{{ $avail }} {{ $item->stock_unit ?? $item->unit }}
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger fw-bold font-monospace px-2 py-1" title="Stok sistem saat ini kosong">
                                                <i class="mdi mdi-close-circle-outline me-1"></i>0 {{ $item->stock_unit ?? $item->unit }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($item->stock_status == 'ready')
                                            <span class="badge bg-success rounded-pill px-3 py-1">
                                                <i class="mdi mdi-check me-1"></i>Ready
                                            </span>
                                        @elseif ($item->stock_status == 'not_ready')
                                            <span class="badge bg-danger rounded-pill px-3 py-1">
                                                <i class="mdi mdi-close me-1"></i>Not Ready
                                            </span>
                                        @else
                                            @if ($item->auto_draft_status == 'ready')
                                                <span class="badge bg-label-success rounded-pill px-2 py-1" title="Draft otomatis: stok sistem mencukupi (menunggu konfirmasi fisik gudang)">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i>Ready (Draft)
                                                </span>
                                            @else
                                                <span class="badge bg-label-warning rounded-pill px-2 py-1" title="Draft otomatis: stok sistem tidak mencukupi (menunggu konfirmasi fisik gudang)">
                                                    <i class="mdi mdi-alert-circle-outline me-1"></i>Not Ready (Draft)
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    <td><span class="text-muted small">{{ $item->notes ?? '-' }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada rincian item barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Preview Item Penawaran (Smart Quote) - Di-hide khusus untuk Role Logistic --}}
            @php
                $targetQuote = $quotation ?? $unitQuotation ?? null;
            @endphp
            @if ($role !== 'Logistic' && $targetQuote && $quotationDetail->count())
                <div class="card-suo-modern">
                    <div class="card-header-modern">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="mdi mdi-file-document-check-outline text-info fs-5"></i>
                                <h6 class="mb-0 fw-bold">Referensi Item Penawaran{{ $unitQuotation ? ' (Smart Quote)' : '' }}</h6>
                            </div>
                            <small class="text-muted">
                                No. Quote: <strong class="text-dark">{{ $targetQuote->no_quote }}</strong> — {{ $targetQuote->title }}
                            </small>
                        </div>
                        <a href="{{ $unitQuotation ? route('unit-quotation.show', $unitQuotation->id) : route('quotation.show', $quotation->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="mdi mdi-eye-outline me-1"></i> Buka Penawaran
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-suo m-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No</th>
                                    <th>Item</th>
                                    <th class="text-center" style="width: 12%;">Qty</th>
                                    <th style="width: 12%;">Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quotationDetail as $i => $item)
                                    <tr>
                                        <td class="text-center fw-semibold text-muted">{{ $i + 1 }}</td>
                                        <td>
                                            {{-- Baris 1: Brand + Part Number --}}
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                @if (!empty($item->brand))
                                                    <span class="badge bg-label-primary fw-bold">{{ $item->brand }}</span>
                                                @endif
                                                @if (!empty($item->part_number))
                                                    <span class="badge bg-label-secondary font-monospace fw-semibold text-dark">
                                                        <i class="mdi mdi-barcode me-1"></i>{{ $item->part_number }}
                                                    </span>
                                                @endif
                                            </div>
                                            {{-- Baris 2: Deskripsi --}}
                                            <div class="fw-semibold text-dark" style="font-size: 0.9rem; line-height: 1.35;">
                                                {{ $item->detail_product }}
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold">{{ $item->qty }}</td>
                                        <td><span class="text-muted">{{ $item->info_qty ?? '-' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Form Pemeriksaan Stok Gudang (Role Logistic / Admin) --}}
            @if (($role == 'Logistic' || $role == 'Admin') && $suo->status == 'submitted')
                <div class="card-suo-modern border-warning">
                    <div class="card-header-modern bg-label-warning border-bottom border-warning">
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-clipboard-check-outline text-warning fs-4"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">Form Pemeriksaan Stok Gudang</h6>
                                <small class="text-muted">Periksa ketersediaan fisik stok barang sebelum meneruskan SUO ke Accounting.</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-primary d-flex align-items-center gap-3 py-2 px-3 mb-3 border-0 bg-label-primary rounded-3">
                            <i class="mdi mdi-information-outline fs-4 text-primary"></i>
                            <div style="font-size: 0.82rem;">
                                <strong>Sinkronisasi Stok Sistem:</strong> Status stok fisik di bawah telah <strong>diisi otomatis (draft)</strong> berdasarkan ketersediaan stok sistem terkini. Silakan periksa fisik barang di gudang, ubah pilihan bila terdapat perbedaan fisik, lalu simpan konfirmasi untuk diteruskan ke Accounting.
                            </div>
                        </div>

                        <form action="{{ route('suo.checkStock', $suo->id) }}" method="POST">
                            @csrf
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-suo m-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%;">No</th>
                                            <th>Item</th>
                                            <th class="text-center" style="width: 12%;">Qty Order</th>
                                            <th class="text-center" style="width: 16%;">Stok di Sistem</th>
                                            <th class="text-center" style="width: 27%;">Status Stok Fisik</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($suo->detail as $idx => $item)
                                            @php
                                                $avail = (float) ($item->available_stock ?? 0);
                                                $req = (float) $item->qty;
                                                $selectedStatus = $item->stock_status ?: $item->auto_draft_status;
                                            @endphp
                                            <tr>
                                                <td class="text-center fw-semibold text-muted">{{ $idx + 1 }}</td>
                                                <td>
                                                    {{-- Baris 1: Brand + Part Number --}}
                                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                        @if (!empty($item->brand))
                                                            <span class="badge bg-label-primary fw-bold">{{ $item->brand }}</span>
                                                        @endif
                                                        @if (!empty($item->part_number))
                                                            <span class="badge bg-label-secondary font-monospace fw-semibold text-dark">
                                                                <i class="mdi mdi-barcode me-1"></i>{{ $item->part_number }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    {{-- Baris 2: Deskripsi --}}
                                                    <div class="fw-semibold text-dark" style="font-size: 0.9rem; line-height: 1.35;">
                                                        {{ $item->item_name }}
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-label-dark fw-bold px-2 py-1">{{ $item->qty }} {{ $item->unit }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if ($avail >= $req)
                                                        <div class="d-inline-flex flex-column align-items-center">
                                                            <span class="badge bg-label-success fw-bold px-2 py-1 fs-7" title="Stok sistem mencukupi">
                                                                <i class="mdi mdi-check-bold me-1"></i>{{ $avail }} {{ $item->stock_unit ?? $item->unit }}
                                                            </span>
                                                            <small class="text-success fw-semibold mt-1" style="font-size: 0.72rem;">
                                                                Stok Cukup (+{{ $avail - $req }})
                              </small>
                                                        </div>
                                                    @elseif ($avail > 0)
                                                        <div class="d-inline-flex flex-column align-items-center">
                                                            <span class="badge bg-label-warning fw-bold px-2 py-1 fs-7" title="Stok sistem kurang dari order">
                                                                <i class="mdi mdi-alert me-1"></i>{{ $avail }} {{ $item->stock_unit ?? $item->unit }}
                                                            </span>
                                                            <small class="text-warning fw-semibold mt-1" style="font-size: 0.72rem;">
                                                                Kurang {{ $req - $avail }}
                                                            </small>
                                                        </div>
                                                    @else
                                                        <div class="d-inline-flex flex-column align-items-center">
                                                            <span class="badge bg-label-danger fw-bold px-2 py-1 fs-7" title="Stok sistem kosong">
                                                                <i class="mdi mdi-close-circle-outline me-1"></i>0 {{ $item->stock_unit ?? $item->unit }}
                                                            </span>
                                                            <small class="text-danger fw-semibold mt-1" style="font-size: 0.72rem;">
                                                                Stok Kosong
                                                            </small>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column align-items-center">
                                                        <div class="d-flex justify-content-center gap-3">
                                                            <div class="form-check custom-option custom-option-basic">
                                                                <label class="form-check-label custom-option-content p-2 cursor-pointer" for="stock_ready_{{ $item->id }}">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="stock_status[{{ $item->id }}]"
                                                                        id="stock_ready_{{ $item->id }}"
                                                                        value="ready"
                                                                        {{ $selectedStatus === 'ready' ? 'checked' : '' }} required>
                                                                    <span class="custom-option-header pb-0">
                                                                        <span class="fw-bold text-success"><i class="mdi mdi-check-circle-outline me-1"></i>Ready</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                            <div class="form-check custom-option custom-option-basic">
                                                                <label class="form-check-label custom-option-content p-2 cursor-pointer" for="stock_not_ready_{{ $item->id }}">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="stock_status[{{ $item->id }}]"
                                                                        id="stock_not_ready_{{ $item->id }}"
                                                                        value="not_ready"
                                                                        {{ $selectedStatus === 'not_ready' ? 'checked' : '' }}>
                                                                    <span class="custom-option-header pb-0">
                                                                        <span class="fw-bold text-danger"><i class="mdi mdi-close-circle-outline me-1"></i>Not Ready</span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @if (empty($item->stock_status))
                                                            <span class="badge bg-label-secondary mt-1 d-inline-flex align-items-center gap-1" style="font-size: 0.68rem;">
                                                                <i class="mdi mdi-magic-staff text-primary"></i> Otomatis draft: <strong>{{ $selectedStatus === 'ready' ? 'Ready' : 'Not Ready' }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary waves-effect d-inline-flex align-items-center gap-2 px-4 py-2 shadow-sm text-white fw-semibold">
                                    <i class="mdi mdi-check-all fs-5 text-white"></i>
                                    <span class="text-white">Simpan Konfirmasi & Teruskan ke Accounting</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar Action & Timeline --}}
        <div class="col-xl-3 col-md-4 col-12">
            {{-- Quick Action Card --}}
            <div class="card-suo-modern mb-3">
                <div class="card-header-modern">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="mdi mdi-lightning-bolt text-warning fs-5"></i>
                        Aksi & Navigasi
                    </h6>
                </div>
                <div class="card-body p-3 d-grid gap-2">
                    {{-- Accounting: approve & booking invoice --}}
                    @if (($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && !$suo->no_invoice_booking)
                        <button class="btn btn-success waves-effect w-100 d-inline-flex align-items-center justify-content-center gap-2 py-2 shadow-sm text-white fw-semibold" id="btn-approve"
                            data-bs-toggle="modal" data-bs-target="#modalApprove">
                            <i class="mdi mdi-check-circle-outline fs-5 text-white"></i>
                            <span class="text-white">Approve & Booking Invoice</span>
                        </button>
                    @endif

                    @if ($suo->no_invoice_booking)
                        <div class="bg-label-success rounded-3 p-3 border border-success border-opacity-25">
                            <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Nomor Invoice Booking</div>
                            <div class="fs-6 fw-bold font-monospace text-success mt-1">{{ $suo->no_invoice_booking }}</div>
                        </div>
                    @endif

                    {{-- Accounting: buat surat jalan --}}
                    @if (($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && $suo->no_invoice_booking)
                        <button class="btn btn-primary waves-effect w-100 d-inline-flex align-items-center justify-content-center gap-2 py-2 shadow-sm text-white fw-semibold" data-bs-toggle="modal" data-bs-target="#modalSJ">
                            <i class="mdi mdi-truck-delivery-outline fs-5 text-white"></i>
                            <span class="text-white">Buat Surat Jalan</span>
                        </button>
                    @endif

                    {{-- Link ke penawaran/invoice jika sudah ada --}}
                    @if ($suo->id_quotation)
                        <a href="{{ route('quotation.show', $suo->id_quotation) }}" class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center gap-1">
                            <i class="mdi mdi-file-document-outline"></i> Lihat Penawaran
                        </a>
                        @if ($invoice)
                            <a href="{{ url('invoice/' . $invoice->id) }}" class="btn btn-outline-success d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="mdi mdi-receipt-text-outline"></i> Lihat Invoice
                            </a>
                        @endif
                    @elseif ($suo->id_unit_quotation)
                        <a href="{{ route('unit-quotation.show', $suo->id_unit_quotation) }}" class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center gap-1">
                            <i class="mdi mdi-file-document-outline"></i> Lihat Smart Quote
                        </a>
                        @if ($invoice)
                            <a href="{{ route('invoice.show_unit', $invoice->id) }}" class="btn btn-outline-success d-inline-flex align-items-center justify-content-center gap-1">
                                <i class="mdi mdi-receipt-text-outline"></i> Lihat Invoice
                            </a>
                        @endif
                    @elseif ($role == 'Sales')
                        <button type="button" class="btn btn-outline-primary waves-effect d-inline-flex align-items-center justify-content-center gap-1"
                            data-bs-toggle="modal" data-bs-target="#modalLinkQuotation">
                            <i class="mdi mdi-link-variant"></i> Hubungkan ke Penawaran
                        </button>
                    @endif

                    {{-- Daftar Surat Jalan yang Terbit --}}
                    @if ($suo->deliveries && $suo->deliveries->count() > 0)
                        <div class="border-top pt-2 mt-1">
                            <div class="small text-muted fw-bold mb-2">Surat Jalan Terbit:</div>
                            @foreach ($suo->deliveries as $d)
                                <a href="{{ url('delivery/' . $d->id) }}" class="btn btn-label-info btn-sm w-100 mb-1 d-inline-flex align-items-center justify-content-between">
                                    <span><i class="mdi mdi-truck me-1"></i>SJ #{{ $d->id }}</span>
                                    <i class="mdi mdi-arrow-right"></i>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <a href="javascript:history.back()" class="btn btn-label-secondary w-100 mt-1">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            {{-- History Timeline Card --}}
            <div class="card-suo-modern">
                <div class="card-header-modern">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="mdi mdi-timeline-clock-outline text-primary fs-5"></i>
                        Riwayat & Progres SUO
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="timeline-suo">
                        {{-- Step 1: Dibuat Sales --}}
                        <div class="timeline-suo-item">
                            <div class="timeline-suo-dot bg-primary">
                                <i class="mdi mdi-pencil"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">Pengajuan SUO</h6>
                                <div class="text-dark small fw-semibold mt-1">{{ $suo->sales->name ?? 'Sales' }}</div>
                                <div class="text-muted" style="font-size: 0.74rem;">
                                    {{ \Carbon\Carbon::parse($suo->created_at)->format('d M Y, H:i') }}
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Dicek Logistik --}}
                        <div class="timeline-suo-item">
                            @if ($suo->confirmed_at)
                                <div class="timeline-suo-dot bg-warning">
                                    <i class="mdi mdi-clipboard-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">Konfirmasi Stok Gudang</h6>
                                    <div class="text-dark small fw-semibold mt-1">{{ $suo->confirmedBy->name ?? 'Gudang' }}</div>
                                    <div class="text-muted" style="font-size: 0.74rem;">
                                        {{ \Carbon\Carbon::parse($suo->confirmed_at)->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            @else
                                <div class="timeline-suo-dot bg-secondary opacity-50">
                                    <i class="mdi mdi-clock-outline"></i>
                                </div>
                                <div class="opacity-75">
                                    <h6 class="mb-0 fw-semibold text-muted" style="font-size: 0.85rem;">Pemeriksaan Gudang</h6>
                                    <div class="text-muted" style="font-size: 0.74rem;">Menunggu fisik barang dicek</div>
                                </div>
                            @endif
                        </div>

                        {{-- Step 3: Diapprove Accounting --}}
                        <div class="timeline-suo-item">
                            @if ($suo->approved_at)
                                <div class="timeline-suo-dot bg-success">
                                    <i class="mdi mdi-check-bold"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-success" style="font-size: 0.85rem;">Approved Accounting</h6>
                                    <div class="text-dark small fw-semibold mt-1">{{ $suo->approvedBy->name ?? 'Accounting' }}</div>
                                    <div class="text-muted" style="font-size: 0.74rem;">
                                        {{ \Carbon\Carbon::parse($suo->approved_at)->format('d M Y, H:i') }}
                                    </div>
                                    @if ($suo->no_invoice_booking)
                                        <span class="badge bg-label-success mt-1" style="font-size: 0.72rem;">
                                            {{ $suo->no_invoice_booking }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="timeline-suo-dot bg-secondary opacity-50">
                                    <i class="mdi mdi-clock-outline"></i>
                                </div>
                                <div class="opacity-75">
                                    <h6 class="mb-0 fw-semibold text-muted" style="font-size: 0.85rem;">Approval Accounting</h6>
                                    <div class="text-muted" style="font-size: 0.74rem;">Menunggu booking invoice</div>
                                </div>
                            @endif
                        </div>

                        {{-- Step 4: Pengiriman / Surat Jalan --}}
                        <div class="timeline-suo-item">
                            @if ($suo->status === 'goods_out' || $suo->status === 'converted' || ($suo->deliveries && $suo->deliveries->count() > 0))
                                <div class="timeline-suo-dot bg-info">
                                    <i class="mdi mdi-truck-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-info" style="font-size: 0.85rem;">Barang Keluar / SJ</h6>
                                    <div class="text-muted" style="font-size: 0.74rem;">Surat Jalan telah diterbitkan</div>
                                </div>
                            @else
                                <div class="timeline-suo-dot bg-secondary opacity-50">
                                    <i class="mdi mdi-truck-outline"></i>
                                </div>
                                <div class="opacity-75">
                                    <h6 class="mb-0 fw-semibold text-muted" style="font-size: 0.85rem;">Pengeluaran Barang</h6>
                                    <div class="text-muted" style="font-size: 0.74rem;">Belum ada Surat Jalan</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Booking Invoice --}}
    @if (($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && !$suo->no_invoice_booking)
    <div class="modal fade" id="modalApprove" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-label-success border-bottom">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="mdi mdi-check-decagram-outline text-success me-1"></i> Approve & Booking Invoice
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="bg-light rounded-3 p-3 mb-3 border">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">No. SUO:</span>
                            <span class="fw-bold font-monospace text-primary">{{ $suo->no_suo }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Perusahaan:</span>
                            <span class="fw-semibold text-dark">{{ $suo->company }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nomor Invoice Booking <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-monospace fs-6" id="inputNoInvoiceBooking"
                            placeholder="Memuat saran nomor...">
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">Nomor urut otomatis sesuai invoice terakhir.</small>
                            <small class="badge bg-label-info fw-semibold font-monospace" id="lastNoBooking" style="font-size: 0.74rem; display: none;"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success waves-effect fw-bold px-4" id="btn-approve-confirm">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Konfirmasi & Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Hubungkan ke Penawaran --}}
    @if ($role == 'Sales' && !$suo->id_quotation && !$suo->id_unit_quotation)
    <div class="modal fade" id="modalLinkQuotation" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold">
                        <i class="mdi mdi-link-variant text-primary me-1"></i> Hubungkan ke Penawaran — {{ $suo->no_suo }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" class="form-control" id="searchQuotationLink"
                            placeholder="Cari No. Penawaran / Judul / Perusahaan...">
                    </div>
                    <div id="listQuotationLink" style="max-height:400px; overflow-y:auto;" class="border rounded-3 p-2 bg-light">
                        <p class="text-muted text-center py-3 mb-0">Memuat data penawaran...</p>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Surat Jalan --}}
    @if (($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && $suo->no_invoice_booking)
        <div class="modal fade" id="modalSJ" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form action="{{ route('suo.storeDelivery', $suo->id) }}" method="POST" class="w-100">
                    @csrf
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-label-primary border-bottom">
                            <h5 class="modal-title fw-bold text-dark">
                                <i class="mdi mdi-truck-delivery-outline text-primary me-1"></i> Buat Surat Jalan — {{ $suo->no_suo }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Surat Jalan</label>
                                <input type="date" class="form-control" name="date"
                                    value="{{ \Carbon\Carbon::today()->toDateString() }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tujuan / Alamat Pengiriman</label>
                                <select class="form-select" name="destination" required>
                                    @if ($client)
                                        <option value="1" {{ $suo->address == $client->address ? 'selected' : '' }}>
                                            {{ $client->address }}
                                        </option>
                                        @if ($client->subAddress)
                                            <option value="2" {{ $suo->address == $client->subAddress ? 'selected' : '' }}>
                                                {{ $client->subAddress }}
                                            </option>
                                        @endif
                                    @else
                                        <option value="1" selected>{{ $suo->address }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Pengiriman</label>
                                <select class="form-select" name="type">
                                    <option value="Ekspedisi">Ekspedisi</option>
                                    <option value="Teknisi">Teknisi</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary fw-bold px-4">
                                <i class="mdi mdi-check me-1"></i> Terbitkan Surat Jalan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
<script>
function renderQuotationLinkList(data) {
    window.__quotationLinkData = window.__quotationLinkData || data;
    var $list = $('#listQuotationLink');
    if (!data.length) {
        $list.html('<p class="text-muted text-center py-3 mb-0">Tidak ada penawaran yang dapat dihubungkan.</p>');
        return;
    }
    var html = '<div class="list-group list-group-flush">';
    data.forEach(function (q) {
        var badge = q.source === 'unit_quotation'
            ? '<span class="badge bg-label-info me-1">Smart Quote</span>'
            : '<span class="badge bg-label-secondary me-1">Penawaran</span>';
        html += '<button type="button" class="list-group-item list-group-item-action rounded-3 mb-1 border btn-pick-quotation" data-id="' + q.id + '" data-no="' + (q.no_quote || '-') + '" data-source="' + q.source + '">'
            + '<div class="d-flex justify-content-between align-items-center mb-1"><strong>' + badge + (q.no_quote || '-') + '</strong>'
            + '<small class="text-muted">' + new Date(q.created_at).toLocaleDateString('id-ID') + '</small></div>'
            + '<div style="font-size:12px;" class="fw-semibold text-dark">' + (q.title || '') + '</div>'
            + '<div class="text-muted" style="font-size:12px;">' + (q.company || '') + '</div>'
            + '</button>';
    });
    html += '</div>';
    $list.html(html);
}

$(function () {
    // Load list penawaran saat modal Hubungkan ke Penawaran dibuka
    $('#modalLinkQuotation').on('show.bs.modal', function () {
        window.__quotationLinkData = null;
        $('#searchQuotationLink').val('');
        $('#listQuotationLink').html('<p class="text-muted text-center py-3 mb-0">Memuat data penawaran...</p>');
        $.get('{{ route('suo.linkableQuotations', $suo->id) }}', function (res) {
            window.__quotationLinkData = res.data;
            renderQuotationLinkList(res.data);
        });
    });

    $('#searchQuotationLink').on('keyup', function () {
        var kw = $(this).val().toLowerCase();
        var filtered = (window.__quotationLinkData || []).filter(function (q) {
            return (q.no_quote || '').toLowerCase().indexOf(kw) !== -1
                || (q.title || '').toLowerCase().indexOf(kw) !== -1
                || (q.company || '').toLowerCase().indexOf(kw) !== -1;
        });
        renderQuotationLinkList(filtered);
    });

    $(document).on('click', '.btn-pick-quotation', function () {
        var idQuotation = $(this).data('id');
        var noQuote = $(this).data('no');
        var source = $(this).data('source');
        var label = source === 'unit_quotation' ? 'Smart Quote' : 'penawaran';
        Swal.fire({
            icon: 'question',
            title: 'Hubungkan ke ' + label + '?',
            text: noQuote,
            showCancelButton: true,
            confirmButtonText: 'Ya, Hubungkan',
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            customClass: { confirmButton: 'btn btn-primary waves-effect me-2', cancelButton: 'btn btn-outline-secondary waves-effect' }
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '{{ route('suo.linkQuotation', $suo->id) }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', id_quotation: idQuotation, source: source },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil dihubungkan!',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: { confirmButton: 'btn btn-primary waves-effect' },
                        }).then(() => location.reload());
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Gagal menghubungkan.';
                    Swal.fire({ icon: 'error', title: msg, buttonsStyling: false, customClass: { confirmButton: 'btn btn-danger waves-effect' } });
                }
            });
        });
    });

    // Load nomor suggest saat modal dibuka
    $('#modalApprove').on('show.bs.modal', function () {
        $('#inputNoInvoiceBooking').val('Memuat...').prop('disabled', true);
        $('#lastNoBooking').text('').hide();
        $.get('{{ route('suo.suggestBooking', $suo->id) }}', function (res) {
            $('#inputNoInvoiceBooking').val(res.suggested).prop('disabled', false);
            if (res.last) {
                $('#lastNoBooking').html('<i class="mdi mdi-history me-1"></i>Last: ' + res.last).show();
            }
        });
    });

    // Konfirmasi & approve
    $('#btn-approve-confirm').on('click', function () {
        var noInvoice = $('#inputNoInvoiceBooking').val().trim();
        if (!noInvoice) {
            Swal.fire({ icon: 'warning', title: 'No invoice tidak boleh kosong', buttonsStyling: false, customClass: { confirmButton: 'btn btn-warning waves-effect' } });
            return;
        }
        $(this).prop('disabled', true).text('Menyimpan...');
        $.ajax({
            url: '{{ route('suo.approve', $suo->id) }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', no_invoice_booking: noInvoice },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Invoice dibooked: ' + res.no_invoice,
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-primary waves-effect' },
                        buttonsStyling: false,
                    }).then(() => location.reload());
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Gagal menyimpan', buttonsStyling: false, customClass: { confirmButton: 'btn btn-danger waves-effect' } });
                $('#btn-approve-confirm').prop('disabled', false).html('<i class="mdi mdi-check-circle-outline me-1"></i> Konfirmasi & Approve');
            }
        });
    });
});
</script>
@endpush
