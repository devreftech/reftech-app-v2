@extends('layouts.sales.app')
@section('title', 'Detail Product Set - ' . ($product->commodity ?? ''))

@php
    $unit = ($product->unit && $product->unit !== '-') ? $product->unit : 'Set';
    $componentCount = $itemProduct->count();

    // Calculate Limiter, HPP (Lowest price across vendors & incoming), and Modal Metrics
    $minStockVal = null;
    $totalHppLowest = 0;
    $totalHppLastIn = 0;
    $totalModalBase = 0;

    foreach ($itemProduct as $it) {
        $r = $it->replacement;
        if ($r) {
            $tot = ($r->stock ?? 0) + ($r->warehouse_stock ?? 0);
            if ($minStockVal === null || $tot < $minStockVal) {
                $minStockVal = $tot;
            }

            // Latest incoming goods price
            $lastIn = $r->detailProductIn ? $r->detailProductIn->sortByDesc('id')->first() : null;
            $lastInPrice = ($lastIn && $lastIn->modal > 0) ? floatval($lastIn->modal) : null;
            $totalHppLastIn += ($lastInPrice ?: floatval($r->modal ?? 0));
            $totalModalBase += floatval($r->modal ?? 0);

            // Candidate prices to find the lowest price across vendors & incoming
            $candidates = collect();
            if ($r->product && $r->product->serial) {
                foreach ($r->product->serial as $s) {
                    if ($s->sparePartVendorPrices) {
                        foreach ($s->sparePartVendorPrices as $vp) {
                            if ($vp->price_idr > 0) {
                                $candidates->push(floatval($vp->price_idr));
                            }
                        }
                    }
                }
            }
            if ($lastInPrice !== null && $lastInPrice > 0) {
                $candidates->push($lastInPrice);
            }
            if ($candidates->isEmpty() && ($r->modal > 0)) {
                $candidates->push(floatval($r->modal));
            }

            $minItemPrice = $candidates->isNotEmpty() ? $candidates->min() : floatval($r->modal ?? 0);
            $totalHppLowest += $minItemPrice;
        }
    }

    $totalStock = ($minStockVal !== null) ? $minStockVal : 0;
    $isReady = $totalStock > 0;
@endphp

@section('content')
<div class="container-fluid px-0 py-2">
    {{-- Top Header Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        @php
                            $catName = $product->category && $product->category !== '-' ? $product->category : 'Non Bearing Kit';
                            $isBearingCat = stripos($catName, 'Bearing') !== false;
                        @endphp
                        <span class="badge {{ $isBearingCat ? 'bg-label-primary' : 'bg-label-secondary' }} px-2 py-1 fw-bold" style="font-size: 11px;">
                            <i class="mdi {{ $isBearingCat ? 'mdi-cog-sync-outline' : 'mdi-package-variant-closed' }} me-1"></i>
                            {{ $catName }}
                        </span>

                        @if($isReady)
                            <span class="badge bg-label-success px-2 py-1 fw-semibold" style="font-size: 11px;">
                                <i class="mdi mdi-check-circle-outline me-1"></i> Ready Stock
                            </span>
                        @else
                            <span class="badge bg-label-danger px-2 py-1 fw-semibold" style="font-size: 11px;">
                                <i class="mdi mdi-alert-circle-outline me-1"></i> Stok Kosong
                            </span>
                        @endif
                    </div>
                    <h3 class="fw-bolder text-dark mb-1 d-flex align-items-center gap-2">
                        {{ $product->commodity }}
                    </h3>
                    <p class="text-muted mb-0 small">
                        <i class="mdi mdi-format-list-bulleted me-1"></i>{{ $product->detail_desc && $product->detail_desc !== '-' ? $product->detail_desc : 'Bundle Paket Produk' }}
                    </p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('product-set.index') }}" class="btn btn-label-secondary d-flex align-items-center gap-1">
                        <i class="mdi mdi-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                    <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#editProductSet">
                        <i class="mdi mdi-pencil-outline"></i>
                        <span>Edit Bundle</span>
                    </button>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createItemReplacement">
                        <i class="mdi mdi-plus fs-5"></i>
                        <span>Tambah Komponen</span>
                    </button>
                    @if (Auth::user()->role == 'Admin')
                        <button type="button" class="btn btn-label-danger d-flex align-items-center gap-1 delete-product-set" data-id="{{ $productSet->id }}">
                            <i class="mdi mdi-delete-outline"></i>
                            <span>Hapus</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Top 4 KPI Metrics Card --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%); border-left: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px; font-size: 11px;">
                            Total Stock Bundle
                        </span>
                        <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-package-variant-closed fs-6"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder text-dark fs-4 mb-1">
                        {{ number_format($totalStock, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">{{ $unit }}</span>
                    </h3>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <small class="text-muted fw-semibold" style="font-size: 11.5px;">Kalkulasi Komponen Min.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #e8fadf 0%, #ffffff 100%); border-left: 4px solid #71dd37 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px; font-size: 11px;">
                            Office Stock
                        </span>
                        <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-office-building-outline fs-6"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder text-dark fs-4 mb-1">
                        {{ number_format($product->stock ?? 0, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">{{ $unit }}</span>
                    </h3>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <small class="text-muted fw-semibold" style="font-size: 11.5px;">Stok Kantor Pusat</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #e8f9ff 0%, #ffffff 100%); border-left: 4px solid #03c3ec !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-info small" style="letter-spacing: .5px; font-size: 11px;">
                            Warehouse Stock
                        </span>
                        <div class="avatar avatar-xs bg-label-info rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi mdi-warehouse fs-6"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder text-dark fs-4 mb-1">
                        {{ number_format($product->warehouse_stock ?? 0, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">{{ $unit }}</span>
                    </h3>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <small class="text-muted fw-semibold" style="font-size: 11.5px;">Stok Gudang Logistik</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fff2e8 0%, #ffffff 100%); border-left: 4px solid #ffab00 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase fw-bold text-warning small" style="letter-spacing: .5px; font-size: 11px;">
                            @if (Auth::user()->role == 'Admin')
                                Estimasi HPP Bundle
                            @else
                                Total Komponen
                            @endif
                        </span>
                        <div class="avatar avatar-xs bg-label-warning rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="mdi {{ Auth::user()->role == 'Admin' ? 'mdi-cash-multiple' : 'mdi-layers-outline' }} fs-6"></i>
                        </div>
                    </div>
                    <h3 class="fw-bolder text-dark fs-4 mb-1">
                        @if (Auth::user()->role == 'Admin')
                            Rp {{ number_format($totalHppLowest, 0, ',', '.') }}
                        @else
                            {{ $componentCount }} <span class="fs-6 fw-normal text-muted">Item</span>
                        @endif
                    </h3>
                    <div class="d-flex align-items-center justify-content-between mt-1">
                        <small class="text-muted fw-semibold" style="font-size: 11.5px;">
                            @if (Auth::user()->role == 'Admin')
                                Akumulasi Harga Terendah (Best Price)
                            @else
                                Penyusun Paket Bundle
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Section --}}
    {{-- ROW 1: Full-Width Components Table Card --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            {{-- Components Table Card (FULL WIDTH) --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                            <i class="mdi mdi-layers-outline fs-6"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark">Komponen Penyusun Bundle (Replacement)</h6>
                    </div>
                    <span class="badge bg-label-primary rounded-pill px-3">{{ $componentCount }} Komponen Terdaftar</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center text-muted fw-bold text-uppercase py-3" style="width: 35px; font-size: 11px;">#</th>
                                    <th class="text-muted fw-bold text-uppercase py-3" style="font-size: 11px; min-width: 240px;">Komponen & Merk Kompatibel</th>
                                    <th class="text-center text-muted fw-bold text-uppercase py-3" style="font-size: 11px; width: 120px;">Stok</th>
                                    @if (Auth::user()->role == 'Admin')
                                        <th class="text-muted fw-bold text-uppercase py-3" style="font-size: 11px; min-width: 190px;">HPP Terendah (Best Cost)</th>
                                        <th class="text-muted fw-bold text-uppercase py-3" style="font-size: 11px; min-width: 240px;">Daftar Harga Vendor & Masuk</th>
                                    @endif
                                    <th class="text-center text-muted fw-bold text-uppercase py-3" style="width: 60px; font-size: 11px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($itemProduct as $index => $detail)
                                    @php
                                        $rep = $detail->replacement;
                                        $repOffice = $rep->stock ?? 0;
                                        $repWh = $rep->warehouse_stock ?? 0;
                                        $repTotal = $repOffice + $repWh;
                                        $repUnit = $rep->product->unit ?? 'Pcs';
                                        $isBottleneck = ($minStockVal !== null && $repTotal == $minStockVal);

                                        // Latest incoming goods (Barang Masuk Terakhir)
                                        $lastIn = $rep->detailProductIn ? $rep->detailProductIn->sortByDesc('id')->first() : null;
                                        $lastInPrice = ($lastIn && $lastIn->modal > 0) ? floatval($lastIn->modal) : null;
                                        $lastInDate = $lastIn && $lastIn->productIn && $lastIn->productIn->date 
                                            ? \Carbon\Carbon::parse($lastIn->productIn->date)->format('d M Y') 
                                            : ($lastIn && $lastIn->created_at ? $lastIn->created_at->format('d M Y') : null);
                                        $lastInSupplier = $lastIn && $lastIn->productIn ? ($lastIn->productIn->supplier ?: ($lastIn->productIn->supp->supplier ?? '-')) : '-';

                                        // Collect all vendor prices across all serial equivalents
                                        $allVendorPrices = collect();
                                        $candidatePrices = collect();

                                        $serials = ($rep->product && $rep->product->serial) ? $rep->product->serial : collect();
                                        foreach($serials as $ser) {
                                            if ($ser->sparePartVendorPrices) {
                                                foreach($ser->sparePartVendorPrices as $vp) {
                                                    if ($vp->price_idr > 0) {
                                                        $val = floatval($vp->price_idr);
                                                        $suppTitle = $vp->supplier->supplier ?? ($vp->supplier->name ?? 'Vendor');
                                                        $allVendorPrices->push([
                                                            'id'        => $vp->id,
                                                            'type'      => 'vendor',
                                                            'serial_id' => $ser->id,
                                                            'brand'     => $ser->brand,
                                                            'pn'        => $ser->pn,
                                                            'supplier'  => $suppTitle,
                                                            'price_idr' => $val,
                                                            'price_usd' => floatval($vp->price_usd),
                                                            'date'      => $vp->date ? \Carbon\Carbon::parse($vp->date)->format('d/m/Y') : '-',
                                                        ]);

                                                        $candidatePrices->push([
                                                            'type'     => 'vendor',
                                                            'price'    => $val,
                                                            'supplier' => $suppTitle,
                                                            'brand'    => $ser->brand,
                                                            'date'     => $vp->date ? \Carbon\Carbon::parse($vp->date)->format('d M Y') : '-',
                                                            'source'   => 'Vendor: ' . $suppTitle . ' (' . ($ser->brand ?: 'Brand') . ')'
                                                        ]);
                                                    }
                                                }
                                            }
                                        }

                                        // Also add incoming price to candidates
                                        if ($lastInPrice !== null && $lastInPrice > 0) {
                                            $candidatePrices->push([
                                                'type'     => 'incoming',
                                                'price'    => $lastInPrice,
                                                'supplier' => $lastInSupplier,
                                                'brand'    => 'Masuk Terakhir',
                                                'date'     => $lastInDate,
                                                'source'   => 'Masuk Terakhir' . ($lastInSupplier && $lastInSupplier !== '-' ? ' (' . $lastInSupplier . ')' : '')
                                            ]);
                                        }

                                        // Fallback to base modal
                                        if ($candidatePrices->isEmpty() && ($rep->modal > 0)) {
                                            $candidatePrices->push([
                                                'type'     => 'modal',
                                                'price'    => floatval($rep->modal),
                                                'supplier' => 'Modal Master',
                                                'brand'    => 'Master',
                                                'date'     => '-',
                                                'source'   => 'Modal Master'
                                            ]);
                                        }

                                        // Find absolute lowest price for this component
                                        $lowestPriceObj = $candidatePrices->sortBy('price')->first();
                                        $lowestPriceVal = $lowestPriceObj ? $lowestPriceObj['price'] : 0;
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-semibold text-muted align-top py-3">{{ $index + 1 }}</td>
                                        <td class="align-top py-3">
                                            <div class="fw-bold text-dark mb-0.5 d-flex align-items-center gap-2">
                                                @if($rep->product)
                                                    <a href="{{ route('product.show', $rep->product->id) }}" target="_blank" class="fs-6 text-primary text-decoration-none fw-bold d-inline-flex align-items-center gap-1" title="Lihat Detail Produk Master">
                                                        <span>{{ $rep->replacement ?? '-' }}</span>
                                                        <i class="mdi mdi-open-in-new fs-6 text-primary" style="font-size: 14px;"></i>
                                                    </a>
                                                @else
                                                    <span class="fs-6">{{ $rep->replacement ?? '-' }}</span>
                                                @endif

                                                @if($isBottleneck && $componentCount > 1)
                                                    <span class="badge bg-label-warning py-0 px-1" style="font-size: 10px;" title="Stok terendah yang membatasi stok bundle">
                                                        <i class="mdi mdi-arrow-down-bold-outline me-0.5"></i>Limiter
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Equivalent / Merk Kompatibel --}}
                                            <div class="mt-2 pt-2 border-top">
                                                <div class="d-flex flex-wrap align-items-center gap-1.5">
                                                    <span class="text-muted small fw-semibold me-1" style="font-size: 11.5px;">
                                                        <i class="mdi mdi-swap-horizontal text-primary me-0.5"></i>Merk Kompatibel:
                                                    </span>
                                                    @forelse($serials as $ser)
                                                        <span class="badge px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 11px; background-color: #f0f2f8; color: #384556; border: 1px solid #d4d8e3;">
                                                            <span><strong class="text-primary">{{ $ser->brand ?: 'Brand' }}:</strong> {{ $ser->pn ?: '-' }}</span>
                                                            @if($ser->detail && $ser->detail !== '-')
                                                                <small class="text-muted">({{ $ser->detail }})</small>
                                                            @endif
                                                            <button type="button" class="btn btn-xs p-0 delete-serial-product ms-0.5" data-id="{{ $ser->id }}" title="Hapus Merk Kompatibel Ini" style="background:none; border:none; line-height:1;">
                                                                <i class="mdi mdi-close text-muted hover-danger" style="font-size: 12px;"></i>
                                                            </button>
                                                        </span>
                                                    @empty
                                                        <span class="text-muted fst-italic small" style="font-size: 11px;">Belum ada merk lain terdaftar</span>
                                                    @endforelse

                                                    <button type="button" class="btn btn-xs btn-label-primary py-0 px-2 ms-1 btn-open-equiv-modal"
                                                        data-product-id="{{ $rep->product->id ?? '' }}"
                                                        data-commodity="{{ $rep->product->commodity ?? $rep->replacement }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalAddEquivalent"
                                                        title="Tambah Merk / Equivalent Kompatibel Lain">
                                                        <i class="mdi mdi-plus fs-6 me-0.5"></i> + Merk
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-top py-3">
                                            @if($repTotal > 0)
                                                <span class="badge bg-label-success px-2 py-1 fw-bold" data-bs-toggle="tooltip" title="Office: {{ $repOffice }} | Gudang: {{ $repWh }}">
                                                    {{ $repTotal }} {{ $repUnit }}
                                                </span>
                                            @else
                                                <span class="badge bg-label-danger px-2 py-1 fw-bold">
                                                    0 {{ $repUnit }}
                                                </span>
                                            @endif
                                            <div class="text-muted small mt-1" style="font-size: 11px;">
                                                Off: {{ $repOffice }} | Gdg: {{ $repWh }}
                                            </div>
                                        </td>
                                        @if (Auth::user()->role == 'Admin')
                                            {{-- Kolom HPP Terendah (Auto Pick Lowest Price) --}}
                                            <td class="align-top py-3">
                                                @if($lowestPriceVal > 0)
                                                    <div class="fw-bold text-dark fs-6 mb-0.5">
                                                        Rp {{ number_format($lowestPriceVal, 0, ',', '.') }}
                                                    </div>
                                                    <span class="badge bg-label-success py-0.5 px-1.5 fw-bold" style="font-size: 10px;">
                                                        <i class="mdi mdi-arrow-down-bold me-0.5"></i>Harga Terendah
                                                    </span>
                                                    <div class="text-muted small mt-1" style="font-size: 10.5px; line-height: 1.3;">
                                                        {{ $lowestPriceObj['source'] ?? '-' }}
                                                    </div>
                                                    @if($lastInPrice && $lastInPrice != $lowestPriceVal)
                                                        <div class="text-muted small mt-0.5" style="font-size: 10px;">
                                                            Masuk Terakhir: Rp {{ number_format($lastInPrice, 0, ',', '.') }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-muted fst-italic small" style="font-size: 11.5px;">Belum ada referensi harga</span>
                                                @endif
                                            </td>

                                            {{-- Kolom Perbandingan Harga Vendor & Barang Masuk --}}
                                            <td class="align-top py-3">
                                                <div class="d-flex flex-column gap-1">
                                                    {{-- Tampilkan Masuk Terakhir jika ada --}}
                                                    @if($lastInPrice !== null && $lastInPrice > 0)
                                                        @php
                                                            $isLastInLowest = ($lastInPrice == $lowestPriceVal);
                                                        @endphp
                                                        <div class="p-1.5 px-2 rounded-2 border d-flex align-items-center justify-content-between gap-1" style="font-size: 11.5px; background-color: {{ $isLastInLowest ? '#e8fadf' : '#f8f9fa' }};">
                                                            <div>
                                                                <span class="fw-semibold text-dark">Masuk: {{ $lastInSupplier }}</span>
                                                                <span class="badge bg-label-info py-0 px-1 ms-1" style="font-size: 9.5px;">Penerimaan</span>
                                                                <div class="text-muted" style="font-size: 10px;">{{ $lastInDate }}</div>
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="fw-bold {{ $isLastInLowest ? 'text-success' : 'text-dark' }}">Rp {{ number_format($lastInPrice, 0, ',', '.') }}</span>
                                                                @if($isLastInLowest && $candidatePrices->count() > 1)
                                                                    <div><span class="badge bg-success text-white py-0 px-1" style="font-size: 9px;">Termurah</span></div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Tampilkan Penawaran Vendor Lain --}}
                                                    @foreach($allVendorPrices as $vp)
                                                        @php
                                                            $isVpLowest = ($vp['price_idr'] == $lowestPriceVal);
                                                        @endphp
                                                        <div class="p-1.5 px-2 rounded-2 border d-flex align-items-center justify-content-between gap-1" style="font-size: 11.5px; background-color: {{ $isVpLowest ? '#e8fadf' : '#f8f9fa' }};">
                                                            <div>
                                                                <span class="fw-semibold text-dark">{{ $vp['supplier'] }}</span>
                                                                <span class="badge bg-label-primary py-0 px-1 ms-1" style="font-size: 9.5px;">{{ $vp['brand'] }}</span>
                                                                <div class="text-muted" style="font-size: 10px;">{{ $vp['date'] }}</div>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-1.5 text-end">
                                                                <div>
                                                                    <span class="fw-bold {{ $isVpLowest ? 'text-success' : 'text-dark' }}">Rp {{ number_format($vp['price_idr'], 0, ',', '.') }}</span>
                                                                    @if($isVpLowest && $candidatePrices->count() > 1)
                                                                        <div><span class="badge bg-success text-white py-0 px-1" style="font-size: 9px;">Termurah</span></div>
                                                                    @endif
                                                                </div>
                                                                <button type="button" class="btn btn-xs btn-icon btn-text-danger p-0 ms-1 delete-vendor-price" data-id="{{ $vp['id'] }}" title="Hapus Harga Vendor" style="background:none; border:none; line-height:1;">
                                                                    <i class="mdi mdi-close fs-6 text-danger"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    @if($allVendorPrices->isEmpty() && (!$lastInPrice || $lastInPrice <= 0))
                                                        <span class="text-muted fst-italic small mb-1" style="font-size: 11px;">Belum ada penawaran vendor</span>
                                                    @endif
                                                </div>

                                                @if($serials->count() > 0)
                                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 mt-1.5 btn-add-vp-modal"
                                                        data-serials="{{ json_encode($serials->map(fn($s) => ['id' => $s->id, 'text' => ($s->brand ?: 'Brand') . ' - ' . ($s->pn ?: '-')])) }}"
                                                        data-commodity="{{ $rep->replacement }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalAddVendorPrice">
                                                        <i class="mdi mdi-plus me-0.5"></i> + Harga Vendor
                                                    </button>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="text-center align-top py-3">
                                            <button type="button" class="btn btn-sm btn-icon btn-label-danger delete-item-set" data-id="{{ $detail->id }}" title="Hapus dari Bundle">
                                                <i class="mdi mdi-trash-can-outline fs-6"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ Auth::user()->role == 'Admin' ? '6' : '4' }}" class="text-center py-5 text-muted">
                                            <div class="avatar avatar-md bg-label-secondary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-layers-off-outline fs-4"></i>
                                            </div>
                                            <p class="fw-semibold mb-1">Belum ada komponen penyusun bundle</p>
                                            <p class="small text-muted mb-3">Tambahkan item replacement agar stok bundle dapat terkalkulasi secara otomatis.</p>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createItemReplacement">
                                                <i class="mdi mdi-plus me-1"></i> Tambah Komponen Sekarang
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: 3 Supporting Cards (Spesifikasi Bundle, Logika Kalkulasi HPP & Stok, Deskripsi & Catatan) --}}
    <div class="row g-4 mb-4">
        {{-- Card 1: Spesifikasi Bundle --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="mdi mdi-information-outline fs-6"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark">Spesifikasi Bundle</h6>
                </div>
                <div class="card-body p-4">
                    <ul class="list-group list-group-flush" style="font-size: 13px;">
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted">SKU / Commodity</span>
                            <span class="fw-bold text-dark">{{ $product->commodity }}</span>
                        </li>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted">Short Description</span>
                            <span class="fw-semibold text-dark">{{ $product->detail_desc && $product->detail_desc !== '-' ? $product->detail_desc : '-' }}</span>
                        </li>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted">Satuan (Unit)</span>
                            <span class="badge bg-label-secondary fw-semibold">{{ $unit }}</span>
                        </li>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted">Kategori</span>
                            <span class="badge {{ ($product->category ?? '') === 'Bearing Kit' ? 'bg-label-primary' : 'bg-label-secondary' }} fw-bold">
                                {{ $product->category && $product->category !== '-' ? $product->category : 'Non Bearing Kit' }}
                            </span>
                        </li>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0 border-bottom">
                            <span class="text-muted">Genuine / OEM</span>
                            <span class="badge bg-label-primary fw-semibold">{{ $product->go && $product->go !== '-' ? $product->go : 'Bundle' }}</span>
                        </li>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center border-0">
                            <span class="text-muted">Tanggal Dibuat</span>
                            <span class="fw-semibold text-dark">{{ $productSet->created_at ? $productSet->created_at->format('d M Y') : '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Card 2: Logika Kalkulasi HPP & Stok --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f7f9fc 0%, #ffffff 100%);">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-info rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="mdi mdi-calculator fs-6"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark">Logika Kalkulasi HPP & Stok</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-2 mb-3">
                        <i class="mdi mdi-lightbulb-on-outline text-warning fs-4 flex-shrink-0 mt-0.5"></i>
                        <p class="text-muted mb-0 small" style="line-height: 1.5;">
                            Stok <strong>Product Set</strong> dihitung dari <strong>stok komponen terendah</strong>, dan HPP dihitung dari akumulasi <strong>harga penawaran terendah</strong> masing-masing komponen.
                        </p>
                    </div>

                    <div class="p-3 bg-light rounded-3 d-flex flex-column gap-2" style="font-size: 12.5px;">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Komponen:</span>
                            <span class="fw-bold text-dark">{{ $componentCount }} Item</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Stok Minimum Komponen:</span>
                            <span class="fw-bold text-primary">{{ $minStockVal !== null ? $minStockVal : 0 }} {{ $unit }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold text-dark">Stok Bundle Tersedia:</span>
                            <span class="fw-bolder {{ $totalStock > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $totalStock }} {{ $unit }}
                            </span>
                        </div>

                        @if (Auth::user()->role == 'Admin')
                            <hr class="my-1">
                            <div class="text-uppercase fw-bold text-success" style="font-size: 11px; letter-spacing: .5px;">
                                <i class="mdi mdi-cash-multiple me-1"></i>Estimasi HPP Bundle (Harga Terendah)
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" title="Akumulasi harga terendah dari seluruh vendor & riwayat masuk">Total HPP Terendah:</span>
                                <span class="fw-bolder text-success fs-6">
                                    Rp {{ number_format($totalHppLowest, 0, ',', '.') }}
                                </span>
                            </div>
                            @if($totalHppLastIn > 0 && $totalHppLastIn != $totalHppLowest)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted" title="Akumulasi harga barang masuk terakhir">Total Masuk Terakhir:</span>
                                    <span class="fw-semibold text-dark">
                                        Rp {{ number_format($totalHppLastIn, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Deskripsi & Catatan --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-secondary rounded p-1 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                        <i class="mdi mdi-text-box-outline fs-6"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark">Deskripsi & Catatan</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 11px;">Deskripsi Lengkap</label>
                        @if($product->description && $product->description !== '-')
                            <div class="p-3 bg-light rounded-3 text-dark" style="font-size: 13px; line-height: 1.5; white-space: pre-wrap; max-height: 120px; overflow-y: auto;">{{ $product->description }}</div>
                        @else
                            <div class="p-3 bg-light rounded-3 text-muted fst-italic" style="font-size: 13px;">Tidak ada deskripsi tambahan.</div>
                        @endif
                    </div>

                    @if($product->note && $product->note !== '-')
                        <div>
                            <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 11px;">Catatan (Note)</label>
                            <div class="p-3 bg-light rounded-3 text-dark" style="font-size: 13px; line-height: 1.5; white-space: pre-wrap; max-height: 100px; overflow-y: auto;">{{ $product->note }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Equivalent Brand Kompatibel --}}
<div class="modal fade" id="modalAddEquivalent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('product-set.store_equivalent', $productSet->id) }}" method="post">
                @csrf
                <input type="hidden" name="id_product" id="equiv_id_product" value="">
                
                <div class="modal-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-tag-plus-outline fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">
                                Tambah Merk Kompatibel (Equivalent)
                            </h5>
                            <small class="text-muted">Daftarkan merk atau part number kompatibel untuk komponen ini.</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="text-muted small fw-semibold">Komponen Produk Terpilih:</div>
                        <div class="fw-bold text-dark fs-6 mt-0.5" id="equiv_commodity_label">-</div>
                    </div>

                    {{-- Quick Search from Master Product / Existing Equivalent --}}
                    <div class="mb-3">
                        <label for="select_existing_product" class="form-label fw-semibold text-dark d-flex align-items-center justify-content-between">
                            <span><i class="mdi mdi-magnify text-primary me-1"></i>Pilih / Cari dari Master Produk & Equivalent</span>
                            <small class="text-muted fw-normal">(Otomatis mengisi form di bawah)</small>
                        </label>
                        <select class="form-select" id="select_existing_product" data-placeholder="-- Ketik merk, PN, atau nama produk untuk mencari --">
                            <option value=""></option>
                        </select>
                    </div>

                    <div class="d-flex align-items-center my-3">
                        <hr class="flex-grow-1 my-0 text-muted">
                        <span class="px-2 small text-muted text-uppercase fw-semibold" style="font-size: 11px;">atau isi detail merk di bawah</span>
                        <hr class="flex-grow-1 my-0 text-muted">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="equiv_brand" class="form-control" name="brand" placeholder="misal: FAG, NSK, SKF" required>
                                <label for="equiv_brand">Merk / Brand * (misal: FAG, NSK)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="equiv_pn" class="form-control" name="pn" placeholder="6215-2Z-C3" required>
                                <label for="equiv_pn">Part Number (PN) *</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="equiv_detail" class="form-control" name="detail" placeholder="Keterangan tambahan (Opsional)">
                            <label for="equiv_detail">Detail / Tipe (Opsional)</label>
                        </div>
                        <input type="hidden" id="equiv_price" name="price" value="0">
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-0 d-flex align-items-center gap-2" style="font-size: 12px;">
                        <i class="mdi mdi-information-outline fs-5 text-info flex-shrink-0"></i>
                        <span>Merk dan part number ini akan tercatat sebagai alternatif kompatibel untuk komponen tersebut.</span>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-3 px-4">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                        <i class="mdi mdi-check"></i>
                        <span>Simpan Equivalent</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Tambah Penawaran Harga Vendor --}}
<div class="modal fade" id="modalAddVendorPrice" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('product-set.store_vendor_price', $productSet->id) }}" method="post">
                @csrf
                <div class="modal-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-currency-usd fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">
                                Tambah Penawaran Harga Vendor
                            </h5>
                            <small class="text-muted">Catat penawaran harga dari supplier untuk merk/komponen ini.</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="text-muted small fw-semibold">Komponen:</div>
                        <div class="fw-bold text-dark fs-6 mt-0.5" id="vp_commodity_label">-</div>
                    </div>

                    <div class="mb-3">
                        <label for="vp_select_serial" class="form-label fw-semibold text-dark">Pilih Merk / Equivalent Komponen *</label>
                        <select class="form-select" id="vp_select_serial" name="id_serial_product" required>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="vp_select_supplier" class="form-label fw-semibold text-dark">Pilih Vendor / Supplier *</label>
                        <select class="select2 form-select" id="vp_select_supplier" name="id_supplier" required data-placeholder="-- Pilih Supplier --">
                            <option value=""></option>
                            @foreach($suppliers as $supp)
                                <option value="{{ $supp->id }}">{{ $supp->supplier }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <div class="form-floating form-floating-outline">
                                <input type="number" id="vp_price_idr" class="form-control" name="price_idr" placeholder="0" required min="0">
                                <label for="vp_price_idr">Harga Penawaran (IDR) *</label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-floating form-floating-outline">
                                <input type="date" id="vp_date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                                <label for="vp_date">Tanggal Penawaran *</label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3 mb-0 d-flex align-items-center gap-2" style="font-size: 12px;">
                        <i class="mdi mdi-information-outline fs-5 text-info flex-shrink-0"></i>
                        <span>Harga ini akan tersimpan ke riwayat perbandingan harga vendor untuk komponen tersebut.</span>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-3 px-4">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-1 shadow-sm">
                        <i class="mdi mdi-check"></i>
                        <span>Simpan Harga Vendor</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('components.modal.product.set.item')
@include('components.modal.warehouse.product-set.edit')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .modal.fade .modal-dialog {
            transform: scale(0.94) translateY(-15px);
            opacity: 0;
            transition: transform 0.26s cubic-bezier(0.2, 0.9, 0.3, 1.15), opacity 0.22s ease-out !important;
        }
        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        .modal-backdrop.fade {
            opacity: 0;
            transition: opacity 0.22s ease-out !important;
        }
        .modal-backdrop.show {
            opacity: 0.45;
            backdrop-filter: blur(2px);
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
<script>
    $(document).ready(function() {
        // Init Select2 in modals
        $('#selectReplacement').select2({
            dropdownParent: $('#createItemReplacement'),
            width: '100%'
        });

        $('#edit_category_select').select2({
            dropdownParent: $('#editProductSet'),
            tags: true,
            width: '100%',
            placeholder: 'Pilih atau ketik kategori baru...'
        });

        $('#vp_select_supplier').select2({
            dropdownParent: $('#modalAddVendorPrice'),
            width: '100%',
            placeholder: '-- Pilih Supplier --'
        });

        // Init Select2 AJAX lookup for existing product / equivalent
        $('#select_existing_product').select2({
            dropdownParent: $('#modalAddEquivalent'),
            width: '100%',
            allowClear: true,
            placeholder: '-- Ketik merk, PN, atau nama produk --',
            ajax: {
                url: '{{ route('product-set.search_products') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });

        // When product/equivalent is selected, auto-populate the inputs
        $('#select_existing_product').on('select2:select', function(e) {
            var selected = e.params.data;
            if (selected) {
                if (selected.brand) {
                    $('#equiv_brand').val(selected.brand);
                }
                if (selected.pn) {
                    $('#equiv_pn').val(selected.pn);
                }
                if (selected.detail) {
                    $('#equiv_detail').val(selected.detail);
                }
                if (selected.price) {
                    $('#equiv_price').val(selected.price);
                }
            }
        });

        // Open modal add equivalent for specific product component
        $(document).on('click', '.btn-open-equiv-modal', function() {
            var productId = $(this).data('product-id');
            var commodity = $(this).data('commodity');
            $('#equiv_id_product').val(productId);
            $('#equiv_commodity_label').text(commodity);
            $('#select_existing_product').val(null).trigger('change');
            $('#equiv_brand').val('');
            $('#equiv_pn').val('');
            $('#equiv_detail').val('');
            $('#equiv_price').val(0);
        });

        // Open modal add vendor price
        $(document).on('click', '.btn-add-vp-modal', function() {
            var commodity = $(this).data('commodity');
            var serials = $(this).data('serials') || [];
            $('#vp_commodity_label').text(commodity);
            
            var $select = $('#vp_select_serial');
            $select.empty();
            if (Array.isArray(serials) && serials.length > 0) {
                serials.forEach(function(s) {
                    $select.append(new Option(s.text, s.id));
                });
            }
            $('#vp_price_idr').val('');
            $('#vp_select_supplier').val(null).trigger('change');
        });

        // Delete Item from Product Set
        $(document).on('click', '.delete-item-set', function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            Swal.fire({
                title: "Hapus Komponen dari Bundle?",
                text: "Komponen ini akan dilepaskan dari paket bundle dan stok bundle akan dikalkulasi ulang.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-2 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '/product-set/item/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "Komponen berhasil dihapus dari bundle.",
                                customClass: {
                                    confirmButton: "btn btn-success waves-effect",
                                },
                            }).then(function() {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal menghapus komponen bundle.'
                            });
                        }
                    });
                }
            });
        });

        // Delete entire Product Set
        $(document).on('click', '.delete-product-set', function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            Swal.fire({
                title: "Hapus Product Set?",
                text: "Product Set ini akan dihapus secara permanen dari sistem.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus Permanen!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-2 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '/product-set/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Terhapus!",
                                text: "Product Set berhasil dihapus.",
                                customClass: {
                                    confirmButton: "btn btn-success waves-effect",
                                },
                            }).then(function() {
                                window.location.href = '{{ route('product-set.index') }}';
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal menghapus Product Set.'
                            });
                        }
                    });
        // Delete Vendor Price
        $(document).on('click', '.delete-vendor-price', function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            Swal.fire({
                title: "Hapus Harga Vendor?",
                text: "Penawaran harga vendor ini akan dihapus dari perbandingan HPP komponen.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-2 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '/product-set/vendor-price/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "Harga vendor berhasil dihapus.",
                                timer: 1200,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal menghapus penawaran harga vendor.'
                            });
                        }
                    });
                }
            });
        });

        // Delete Compatible Equivalent Serial
        $(document).on('click', '.delete-serial-product', function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            Swal.fire({
                title: "Hapus Merk Kompatibel?",
                text: "Merk kompatibel dan seluruh riwayat harga vendor terkait akan dihapus.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-2 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '/product-set/equivalent/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "Merk kompatibel berhasil dihapus.",
                                timer: 1200,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal menghapus merk kompatibel.'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
