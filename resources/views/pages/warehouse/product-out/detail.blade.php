@extends('layouts.sales.app')
@section('title', 'Detail Barang Keluar - ' . ($product->no_product_out ?? ($product->no_type == '1' ? $product->invoice : $product->po)))

@php
    $totalQty = $detail->sum('qty');
    $totalItems = $detail->count();
    $subtotalCalculated = $detail->sum('amount');
    $shippingCost = (float) ($product->shipping ?? 0);
    $grandTotal = (float) ($product->total ?? ($subtotalCalculated + $shippingCost));
    $activeDocNo = $product->no_type == '1' ? $product->invoice : $product->po;
    $isOnline = strtolower($product->vers) === 'online';
@endphp

@section('content')
    {{-- Header & Action Bar (Hidden on print) --}}
    <div class="d-print-none mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mb-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('product-out.index') }}" class="text-muted">Barang Keluar</a>
                        </li>
                        <li class="breadcrumb-item active text-primary fw-semibold">Detail Pengeluaran</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h4 class="fw-bold mb-0 text-dark">
                        {{ $product->no_product_out ?: 'Product Out #' . $product->id }}
                    </h4>
                    <span class="badge rounded-pill {{ $isOnline ? 'bg-label-info' : 'bg-label-success' }} px-3 py-2">
                        <i class="mdi {{ $isOnline ? 'mdi-web' : 'mdi-store-outline' }} me-1 align-middle"></i>
                        {{ $product->vers }}
                    </span>
                    <span class="badge rounded-pill bg-label-secondary px-3 py-2">
                        <i class="mdi mdi-calendar-blank-outline me-1 align-middle"></i>
                        {{ \Carbon\Carbon::parse($product->date)->isoFormat('DD MMMM YYYY') }}
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('product-out.index') }}" class="btn btn-outline-secondary btn-sm waves-effect">
                    <i class="mdi mdi-arrow-left me-1"></i> Kembali
                </a>
                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" onclick="window.print()">
                    <i class="mdi mdi-printer-outline me-1"></i> Cetak Bukti Keluar
                </button>
                <button type="button" class="btn btn-outline-danger btn-sm waves-effect delete-invoice" data-id="{{ $product->id }}">
                    <i class="mdi mdi-trash-can-outline me-1"></i> Hapus
                </button>
            </div>
        </div>

        {{-- Quick Stats / Metrics Row --}}
        <div class="row g-3 mt-1">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted d-block small mb-1">Nomor Referensi</span>
                                <h6 class="mb-0 fw-bold text-truncate" style="max-width: 170px;" title="{{ $activeDocNo }}">
                                    {{ $activeDocNo ?: '-' }}
                                </h6>
                                <span class="badge bg-label-primary rounded-pill mt-1" style="font-size: 10px;">
                                    {{ $product->no_type == '1' ? 'Invoice Reference' : 'PO Reference' }}
                                </span>
                            </div>
                            <div class="avatar avatar-md bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-file-document-outline fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted d-block small mb-1">Total Macam &amp; Qty</span>
                                <h6 class="mb-0 fw-bold">{{ $totalItems }} SKU <span class="text-muted fw-normal">({{ $totalQty }} unit)</span></h6>
                                <span class="badge bg-label-info rounded-pill mt-1" style="font-size: 10px;">
                                    Kategori Barang Keluar
                                </span>
                            </div>
                            <div class="avatar avatar-md bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-cube-send fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted d-block small mb-1">Total Nilai Transaksi</span>
                                <h6 class="mb-0 fw-bold text-success">Rp {{ number_format($grandTotal, 0, '', '.') }}</h6>
                                <span class="text-muted small" style="font-size: 11px;">
                                    Ongkir: Rp {{ number_format($shippingCost, 0, '', '.') }}
                                </span>
                            </div>
                            <div class="avatar avatar-md bg-label-success rounded-3 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-cash-multiple fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted d-block small mb-1">Dibuat Oleh</span>
                                <h6 class="mb-0 fw-bold text-truncate" style="max-width: 170px;">
                                    {{ $product->user->name ?? 'Admin' }}
                                </h6>
                                <span class="text-muted small" style="font-size: 11px;">
                                    {{ $product->created_at ? $product->created_at->diffForHumans() : '-' }}
                                </span>
                            </div>
                            <div class="avatar avatar-md bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-account-check-outline fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Document Layout --}}
    <div class="row invoice-preview">
        {{-- Document Sheet --}}
        <div class="col-xl-9 col-lg-8 col-12 mb-lg-0 mb-4">
            <div class="card invoice-preview-card border shadow-sm printable-doc">
                {{-- Document Header --}}
                <div class="card-body pb-3">
                    <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                        <div class="mb-sm-0 mb-3">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <img src="{{ asset('/asset/logo/Reftech-Log.png') }}" alt="Reftech Logo"
                                     style="max-height: 48px; width: auto; object-fit: contain;">
                            </div>
                            <p class="mb-1 fw-bold text-dark fs-6">PT. REFTECH JAYA OPTIMA</p>
                            <p class="mb-0 text-muted small lh-sm">
                                Warehouse &amp; Logistics Department<br>
                                Sistem Informasi Manajemen Barang Keluar
                            </p>
                        </div>
                        <div class="text-sm-end">
                            <h4 class="fw-bold text-primary mb-1 text-uppercase tracking-wide">
                                Bukti Barang Keluar
                            </h4>
                            <div class="mb-1">
                                <span class="badge bg-primary text-white fs-6 px-3 py-1 fw-semibold">
                                    {{ $product->no_product_out ?: 'BK-' . str_pad($product->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            <div class="text-muted small mt-2">
                                <div><strong>Kanal:</strong> {{ $product->vers }}</div>
                                <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($product->date)->isoFormat('dddd, DD MMMM YYYY') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top"></div>

                {{-- Client & Shipment Metadata Info Box --}}
                <div class="card-body py-3 bg-light-subtle">
                    <div class="row g-3">
                        {{-- Left Box: Customer Info --}}
                        <div class="col-md-7 col-12">
                            <div class="p-3 bg-white rounded border h-100 shadow-2xs">
                                <div class="d-flex align-items-center gap-2 mb-2 pb-1 border-bottom">
                                    <i class="mdi mdi-office-building-outline text-primary fs-5"></i>
                                    <h6 class="mb-0 fw-bold text-dark">Informasi Penerima / Klien</h6>
                                </div>
                                <div class="text-dark small lh-base" style="white-space: pre-wrap; word-break: break-word;">{{ trim($product->detail_client) ?: '-' }}</div>
                            </div>
                        </div>

                        {{-- Right Box: Document Reference Info --}}
                        <div class="col-md-5 col-12">
                            <div class="p-3 bg-white rounded border h-100 shadow-2xs">
                                <div class="d-flex align-items-center gap-2 mb-2 pb-1 border-bottom">
                                    <i class="mdi mdi-information-outline text-primary fs-5"></i>
                                    <h6 class="mb-0 fw-bold text-dark">Referensi Dokumen</h6>
                                </div>
                                <table class="table table-sm table-borderless m-0 small">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted ps-0 py-1" style="width: 100px;">No. Invoice</td>
                                            <td class="py-1 fw-semibold text-dark">: {{ $product->invoice ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0 py-1">No. PO</td>
                                            <td class="py-1 fw-semibold text-dark">: {{ $product->po ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0 py-1">Tipe Aktif</td>
                                            <td class="py-1">
                                                : <span class="badge bg-label-{{ $product->no_type == '1' ? 'primary' : 'success' }} py-0 px-2" style="font-size: 11px;">
                                                    {{ $product->no_type == '1' ? 'No. Invoice' : 'No. PO' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0 py-1">Operator</td>
                                            <td class="py-1 text-dark">: {{ $product->user->name ?? 'Admin' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Notes Bar if any --}}
                    @if(!empty(trim($product->note)) && trim($product->note) !== '-')
                        <div class="mt-3 p-3 bg-white rounded border">
                            <div class="d-flex align-items-start gap-2">
                                <i class="mdi mdi-note-text-outline text-warning fs-5 mt-n1"></i>
                                <div class="small">
                                    <strong class="text-dark d-block mb-1">Catatan Khusus:</strong>
                                    <span class="text-muted" style="white-space: pre-wrap;">{{ $product->note }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Table Items --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0">
                        <thead class="table-light border-top border-bottom">
                            <tr class="text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                <th class="text-center py-3" style="width: 45px;">No.</th>
                                <th class="py-3" style="min-width: 260px;">Item / Produk</th>
                                <th class="text-center py-3" style="width: 65px;">G/R</th>
                                <th class="text-center py-3" style="width: 85px;">Gudang</th>
                                <th class="text-center py-3" style="width: 95px;">Qty</th>
                                <th class="text-end py-3" style="width: 140px;">Harga Satuan</th>
                                <th class="text-end py-3 pe-4" style="width: 150px;">Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($detail as $index => $item)
                                @php
                                    $brand = $item->serialProduct?->brand ?? ($item->detailProduct?->product?->brand ?? null);
                                    $partNumber = $item->serialProduct?->pn ?? ($item->detailProduct?->product?->part_number ?? ($item->detailProduct?->replacement ?? null));

                                    if ($brand && $partNumber) {
                                        $brandPn = $brand . ' - ' . $partNumber;
                                    } elseif ($brand) {
                                        $brandPn = $brand;
                                    } elseif ($partNumber) {
                                        $brandPn = $partNumber;
                                    } else {
                                        $brandPn = $item->detailProduct?->replacement ?? ('Item #' . ($index + 1));
                                    }

                                    $productDesc = $item->detailProduct?->product?->description 
                                        ?? ($item->detailProduct?->product?->commodity 
                                        ?? ($item->detailProduct?->replacement ?? '-'));
                                    
                                    $unitName = $item->detailProduct?->product?->unit ?? 'Unit';

                                    $goRaw = $item->detailProduct?->product?->go ?? null;
                                    $goInitial = null;
                                    if ($goRaw) {
                                        if (stripos($goRaw, 'Genuine') !== false || strtoupper($goRaw) === 'G') {
                                            $goInitial = 'G';
                                        } elseif (stripos($goRaw, 'Replacement') !== false || strtoupper($goRaw) === 'R') {
                                            $goInitial = 'R';
                                        } else {
                                            $goInitial = strtoupper(substr($goRaw, 0, 1));
                                        }
                                    }
                                @endphp
                                <tr class="item-row">
                                    {{-- No. --}}
                                    <td class="text-center align-top pt-3 pb-3">
                                        <span class="badge bg-label-secondary rounded-circle fw-bold" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 11px;">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>

                                    {{-- Item / Produk (Brand - PN di atas, Deskripsi di bawah) --}}
                                    <td class="text-start align-top pt-3 pb-3">
                                        <div class="fw-bold text-dark fs-6 lh-sm mb-1 text-start">{{ $brandPn }}</div>
                                        <div class="text-body text-start" style="font-size: 12.5px; line-height: 1.35; margin: 0; padding: 0;">{!! nl2br(e(trim(preg_replace('/^[ \t]+/m', '', $productDesc)))) !!}</div>
                                    </td>

                                    {{-- G/R (Genuine / Replacement) --}}
                                    <td class="text-center align-top pt-3 pb-3">
                                        @if($goInitial === 'G')
                                            <span class="badge bg-label-success rounded-pill fw-bold" title="Genuine" style="font-size: 11px; padding: 4px 8px;">G</span>
                                        @elseif($goInitial === 'R')
                                            <span class="badge bg-label-warning rounded-pill fw-bold" title="Replacement" style="font-size: 11px; padding: 4px 8px;">R</span>
                                        @elseif($goInitial)
                                            <span class="badge bg-label-secondary rounded-pill fw-bold" style="font-size: 11px; padding: 4px 8px;">{{ $goInitial }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Gudang --}}
                                    <td class="text-center align-top pt-3 pb-3">
                                        @if($item->warehouse === 'BDG')
                                            <span class="badge bg-label-primary px-2 py-1 fw-semibold">BDG</span>
                                        @elseif($item->warehouse === 'BKS')
                                            <span class="badge bg-label-warning px-2 py-1 fw-semibold">BKS</span>
                                        @else
                                            <span class="badge bg-label-secondary px-2 py-1">{{ $item->warehouse ?: '-' }}</span>
                                        @endif
                                    </td>

                                    {{-- Qty & Satuan --}}
                                    <td class="text-center align-top pt-3 pb-3">
                                        <span class="fw-bold fs-6 text-dark d-block">{{ $item->qty }}</span>
                                        <span class="text-muted small" style="font-size: 11px;">{{ $unitName }}</span>
                                    </td>

                                    {{-- Harga Satuan --}}
                                    <td class="text-end align-top pt-3 pb-3 text-nowrap">
                                        <span class="text-dark fw-medium">Rp {{ number_format($item->price, 0, '', '.') }}</span>
                                    </td>

                                    {{-- Total Harga --}}
                                    <td class="text-end align-top pt-3 pb-3 pe-4 text-nowrap">
                                        <span class="fw-bold text-dark fs-6">Rp {{ number_format($item->amount, 0, '', '.') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="mdi mdi-cube-outline fs-2 d-block mb-1"></i>
                                        Tidak ada item produk dalam transaksi ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Summary & Signatures Section --}}
                <div class="card-body pt-3">
                    <div class="row justify-content-between g-4">
                        {{-- Left Column: Print Signature Section --}}
                        <div class="col-md-6 col-12 d-flex flex-column justify-content-between">
                            <div class="p-3 bg-light-subtle rounded border small mb-3">
                                <div class="fw-semibold text-dark mb-1">
                                    <i class="mdi mdi-shield-check-outline text-success me-1"></i> Syarat &amp; Ketentuan Pengeluaran:
                                </div>
                                <ul class="mb-0 ps-3 text-muted" style="font-size: 11px;">
                                    <li>Barang yang telah dikeluarkan sesuai dengan spesifikasi dan jumlah di atas.</li>
                                    <li>Penerima wajib memeriksa kondisi fisik dan kelengkapan barang saat serah terima.</li>
                                </ul>
                            </div>

                            {{-- Signatures box (Clean for both screen and print) --}}
                            <div class="row text-center mt-3 pt-2 g-2">
                                <div class="col-4">
                                    <div class="text-muted small mb-4 pb-3">Diserahkan Oleh,</div>
                                    <div class="fw-semibold text-dark border-top pt-1 small">
                                        ({{ $product->user->name ?? 'Petugas Gudang' }})
                                    </div>
                                    <div class="text-muted" style="font-size: 10px;">Gudang / Logistik</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-muted small mb-4 pb-3">Diterima Oleh,</div>
                                    <div class="fw-semibold text-dark border-top pt-1 small">
                                        ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                                    </div>
                                    <div class="text-muted" style="font-size: 10px;">Penerima / Ekspedisi</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-muted small mb-4 pb-3">Mengetahui,</div>
                                    <div class="fw-semibold text-dark border-top pt-1 small">
                                        ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
                                    </div>
                                    <div class="text-muted" style="font-size: 10px;">Supervisor / Head</div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Cost Breakdown Table --}}
                        <div class="col-md-5 col-12">
                            <div class="p-3 bg-light-subtle rounded border">
                                <table class="table table-sm table-borderless m-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted py-2">Subtotal Barang ({{ $totalItems }} Item)</td>
                                            <td class="text-end py-2 fw-semibold text-dark">
                                                Rp {{ number_format($subtotalCalculated, 0, '', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-2">Biaya Pengiriman (Shipping)</td>
                                            <td class="text-end py-2 fw-semibold text-dark">
                                                Rp {{ number_format($shippingCost, 0, '', '.') }}
                                            </td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="py-3 fs-6 fw-bold text-primary">Grand Total</td>
                                            <td class="py-3 fs-5 fw-bolder text-end text-primary">
                                                Rp {{ number_format($grandTotal, 0, '', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Action & Controls (Hidden on print) --}}
        <div class="col-xl-3 col-lg-4 col-12 invoice-actions d-print-none">
            {{-- Document Format Selector Card --}}
            <div class="card mb-3 border shadow-sm">
                <div class="card-header bg-transparent border-bottom pb-2">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="mdi mdi-cog-outline text-primary"></i> Pengaturan Dokumen
                    </h6>
                </div>
                <div class="card-body pt-3">
                    <label for="changeNo" class="form-label small fw-semibold text-muted mb-1">
                        Format Tampilan Nomor
                    </label>
                    <select class="form-select form-select-sm change-no mb-2" name="changeNo" id="changeNo"
                            data-id="{{ $product->id }}">
                        <option value="1" {{ $product->no_type == '1' ? 'selected' : '' }}>
                            No Invoice ({{ $product->invoice ?: '-' }})
                        </option>
                        <option value="2" {{ $product->no_type == '2' ? 'selected' : '' }}>
                            No PO ({{ $product->po ?: '-' }})
                        </option>
                    </select>
                    <p class="text-muted mb-0" style="font-size: 11px;">
                        Pilih referensi nomor utama yang akan ditampilkan pada kop dan cetakan bukti keluar.
                    </p>
                </div>
            </div>

            {{-- Quick Actions Card --}}
            <div class="card mb-3 border shadow-sm">
                <div class="card-header bg-transparent border-bottom pb-2">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        <i class="mdi mdi-lightning-bolt-outline text-warning"></i> Aksi Cepat
                    </h6>
                </div>
                <div class="card-body pt-3 d-flex flex-column gap-2">
                    <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 w-100 waves-effect waves-light"
                            onclick="window.print()">
                        <i class="mdi mdi-printer-outline fs-5"></i> Cetak / Print Dokumen
                    </button>

                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 w-100 waves-effect btn-copy-info"
                            data-copy="{{ $product->no_product_out ?: $activeDocNo }}">
                        <i class="mdi mdi-content-copy fs-5"></i> Salin Nomor Dokumen
                    </button>

                    <a href="{{ route('product-out.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 w-100 waves-effect">
                        <i class="mdi mdi-arrow-left fs-5"></i> Kembali ke Daftar
                    </a>

                    <hr class="my-1">

                    <button type="button" class="btn btn-label-danger d-flex align-items-center justify-content-center gap-2 w-100 waves-effect delete-invoice"
                            data-id="{{ $product->id }}">
                        <i class="mdi mdi-trash-can-outline fs-5"></i> Hapus Data Barang Keluar
                    </button>
                </div>
            </div>

            {{-- Meta & Audit Log Card --}}
            <div class="card border shadow-sm">
                <div class="card-header bg-transparent border-bottom pb-2">
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2 text-muted" style="font-size: 13px;">
                        <i class="mdi mdi-clock-outline"></i> Informasi Transaksi
                    </h6>
                </div>
                <div class="card-body pt-3 small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ID Transaksi</span>
                        <span class="fw-semibold text-dark">#{{ $product->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Waktu Dibuat</span>
                        <span class="fw-semibold text-dark">
                            {{ $product->created_at ? $product->created_at->format('d/m/Y H:i') : '-' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Terakhir Diperbarui</span>
                        <span class="fw-semibold text-dark">
                            {{ $product->updated_at ? $product->updated_at->diffForHumans() : '-' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Dibuat Oleh</span>
                        <span class="fw-semibold text-dark">{{ $product->user->name ?? 'Admin' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .shadow-2xs {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .tracking-wide {
            letter-spacing: 0.05em;
        }
        .printable-doc {
            border-radius: 8px;
            background-color: #ffffff;
        }

        /* Dark mode overrides */
        html.dark-style .printable-doc {
            background-color: #2b2c40;
        }
        html.dark-style .bg-light-subtle {
            background-color: rgba(255, 255, 255, 0.03) !important;
        }
        html.dark-style .printable-doc .bg-white {
            background-color: #2b2c40 !important;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 12px !important;
            }
            .layout-navbar,
            .layout-menu,
            .layout-menu-toggle,
            .content-backdrop,
            .invoice-actions,
            .d-print-none,
            nav,
            footer {
                display: none !important;
            }
            .content-wrapper {
                padding: 0 !important;
                margin: 0 !important;
            }
            .container-xxl {
                padding: 0 !important;
                max-width: 100% !important;
            }
            .invoice-preview {
                margin: 0 !important;
            }
            .invoice-preview .col-xl-9,
            .invoice-preview .col-lg-8,
            .invoice-preview .col-12 {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }
            .printable-doc {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #000 !important;
            }
            .printable-doc .card-body {
                padding: 12px 16px !important;
            }
            .table {
                color: #000 !important;
                border-color: #ccc !important;
            }
            .table thead th {
                background-color: #f5f5f5 !important;
                color: #000 !important;
                border-color: #ccc !important;
            }
            .badge {
                border: 1px solid #999 !important;
                color: #000 !important;
                background: transparent !important;
            }
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        // SweetAlert Delete Confirmation
        $(document).on('click', '.delete-invoice', function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            Swal.fire({
                title: "Apakah Anda Yakin?",
                text: "Data pengeluaran barang ini akan dihapus dan stok barang akan dikembalikan ke gudang!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus Data!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value || result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menghapus data dan mengembalikan stok...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ url('product-out') }}/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil Dihapus!",
                                    text: "Data pengeluaran barang berhasil dihapus.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                    buttonsStyling: false,
                                });
                                window.setTimeout(function() {
                                    window.location.href = '{{ route('product-out.index') }}';
                                }, 1500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus data barang keluar. Silakan coba lagi.',
                                    customClass: {
                                        confirmButton: "btn btn-primary waves-effect",
                                    },
                                    buttonsStyling: false,
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan Sistem',
                                text: 'Terjadi kesalahan saat menghubungi server.',
                                customClass: {
                                    confirmButton: "btn btn-primary waves-effect",
                                },
                                buttonsStyling: false,
                            });
                        }
                    });
                }
            });
        });

        // Change Document Number Type (Invoice vs PO)
        $(document).on('change', '.change-no', function() {
            var selectedValue = $(this).val();
            var rowId = $(this).data('id');
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

            $.ajax({
                type: 'POST',
                url: '/product-out/' + rowId + '/change_no',
                data: {
                    status: selectedValue,
                    _token: csrfToken
                },
                success: function(response) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1200,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Tipe nomor dokumen diperbarui'
                    });
                    window.setTimeout(function() {
                        window.location.reload();
                    }, 900);
                },
                error: function(error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal menyimpan perubahan tipe nomor dokumen.',
                        customClass: {
                            confirmButton: "btn btn-primary waves-effect",
                        },
                        buttonsStyling: false,
                    });
                }
            });
        });

        // Copy Info / Document Number to Clipboard
        $(document).on('click', '.btn-copy-info', function() {
            var text = $(this).data('copy');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function() {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: 'Nomor dokumen disalin ke clipboard!'
                    });
                });
            } else {
                var tempInput = document.createElement("input");
                tempInput.value = text;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Nomor dokumen disalin!'
                });
            }
        });
    </script>
@endpush
