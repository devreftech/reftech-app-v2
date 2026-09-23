@extends('layouts.sales.app')
@section('title', 'Direct Purchase (Pembelian Langsung) - Reftech ERP')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />

    <style>
        .dp-hero-card {
            background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);
            border-radius: 14px;
            color: #ffffff;
            box-shadow: 0 8px 24px rgba(13, 148, 136, 0.2);
            position: relative;
            overflow: hidden;
        }
        .dp-hero-card::after {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -30px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .form-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 12px;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.05);
        }
        .table-items th {
            background-color: #f8fafc;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            vertical-align: middle;
        }
        .table-items td {
            vertical-align: middle;
        }
        .currency-input-group .input-group-text {
            background-color: #f1f5f9;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y px-4">
        {{-- Breadcrumb --}}
        <div class="mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0 font-12">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('purchase.index') }}">Purchase Order</a></li>
                    @if ($sourcePr)
                        <li class="breadcrumb-item"><a href="{{ route('purchase-request.show', $sourcePr->id_pending) }}">PR {{ $sourcePr->no_pr }}</a></li>
                    @endif
                    <li class="breadcrumb-item active fw-bold">Direct Purchase (Beli Langsung)</li>
                </ol>
            </nav>
        </div>

        {{-- Hero Header --}}
        <div class="dp-hero-card p-4 mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-white rounded-pill px-3 py-1 font-11 fw-bold shadow-xs" style="color: #0f766e !important;">
                            <i class="mdi mdi-cart-arrow-down me-1"></i> DIRECT PURCHASE (NON-PO)
                        </span>
                        @if ($sourcePr)
                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1 font-11 fw-semibold">
                                Dialokasikan dari PR: {{ $sourcePr->no_pr }}
                            </span>
                        @endif
                    </div>
                    <h4 class="fw-bold text-white mb-1">Form Pembelian Langsung (Direct Purchase)</h4>
                    <p class="mb-0 text-white-50 small" style="max-width: 700px;">
                        Pengadaan barang tanpa PO resmi (marketplace, toko lokal, retail). Anda dapat menggabungkan item dari beberapa PR sekaligus dalam satu transaksi pembelian.
                    </p>
                </div>
                <div>
                    <a href="{{ $sourcePr ? route('purchase-request.show', $sourcePr->id_pending) : route('purchase.index') }}" class="btn btn-light btn-sm fw-semibold shadow-xs">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        {{-- Notification Alerts --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="fw-bold mb-1"><i class="mdi mdi-alert-circle-outline me-1"></i> Terjadi kesalahan input:</div>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Source PR Context Card --}}
        @if ($sourcePr)
            <div class="card form-card mb-4 border-start border-4" style="border-left-color: #0d9488 !important;">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-3 border-end">
                            <span class="text-muted font-11 fw-semibold text-uppercase d-block">Purchase Request</span>
                            <span class="fw-bold text-dark fs-6">{{ $sourcePr->no_pr ?: '#' . $sourcePr->id }}</span>
                        </div>
                        <div class="col-md-3 border-end">
                            <span class="text-muted font-11 fw-semibold text-uppercase d-block">Sales Order (SO)</span>
                            <span class="fw-bold text-primary">{{ $sourcePr->pending->no_pending ?? '-' }}</span>
                        </div>
                        <div class="col-md-3 border-end">
                            <span class="text-muted font-11 fw-semibold text-uppercase d-block">Tgl PR Dibuat</span>
                            <span class="text-dark">{{ $sourcePr->date ? \Carbon\Carbon::parse($sourcePr->date)->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="col-md-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-11 fw-semibold text-uppercase d-block">Item PR Awal</span>
                                <span class="badge bg-label-info">{{ count($prefillItems) }} Item Teralokasi</span>
                            </div>
                            <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalPullPrItems">
                                <i class="mdi mdi-plus me-1"></i> Tambah PR Lain
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('purchase.direct-store') }}" method="POST" id="directPurchaseForm">
            @csrf
            @if ($sourcePr)
                <input type="hidden" name="id_purchase_request" value="{{ $sourcePr->id }}">
            @endif

            <div class="row g-4">
                {{-- Left Column: Info Dokumen & Vendor --}}
                <div class="col-lg-6">
                    <div class="card form-card h-100">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h6 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                                <i class="mdi mdi-storefront-outline me-2" style="color: #0d9488;"></i> 1. Informasi Pembelian &amp; Toko
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold small text-dark">No. Referensi Direct Purchase <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-pound"></i></span>
                                        <input type="text" name="no_po" class="form-control fw-bold text-dark" value="{{ old('no_po', $previewNoDp) }}" required>
                                    </div>
                                    <small class="text-muted">Format kode transaksi langsung</small>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold small text-dark">Tanggal Pembelian <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}" required>
                                </div>

                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fw-semibold small text-dark mb-0">Pilih Rekanan Supplier <span class="text-danger">*</span></label>
                                        <button type="button" class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createSupplier" title="Tambah Supplier Baru">
                                            <i class="mdi mdi-domain-plus me-1"></i>+ Tambah Supplier Baru
                                        </button>
                                    </div>
                                    <select name="supplier" id="supplierSelect" class="form-select select2-supplier" required>
                                        <option value="">-- Pilih Rekanan Supplier Master --</option>
                                        @foreach ($suppliers as $sup)
                                            @php
                                                $infoLower = strtolower($sup->info ?? '');
                                                $areaLower = strtolower($sup->area ?? '');
                                                $isImpor = str_contains($infoLower, 'import') || str_contains($infoLower, 'impor') || str_contains($areaLower, 'china') || str_contains($areaLower, 'shanghai');
                                                $supplierType = $isImpor ? 'Impor' : 'Lokal';
                                            @endphp
                                            <option value="{{ $sup->id }}"
                                                data-type="{{ $supplierType }}"
                                                data-supplier-name="{{ $sup->supplier }}"
                                                data-address="{{ $sup->address }}"
                                                {{ old('supplier') == $sup->id ? 'selected' : '' }}>
                                                [{{ $supplierType }}] {{ $sup->supplier }} {{ $sup->address ? "({$sup->address})" : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="supplier_name" id="supplierNameInput" value="{{ old('supplier_name') }}">
                                    <small class="text-muted">Supplier wajib dipilih dari master data (klik "+ Tambah Supplier Baru" jika toko belum terdaftar)</small>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold small text-dark">No. Pesanan / No. Nota / Ref</label>
                                    <input type="text" name="no_reference" class="form-control" placeholder="Contoh: INV/2026/09/XXX" value="{{ old('no_reference') }}">
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold small text-dark">Metode Pembayaran</label>
                                    <select name="payment" class="form-select">
                                        <option value="Cash" {{ old('payment') == 'Cash' ? 'selected' : '' }}>Cash / Tunai</option>
                                        <option value="Transfer Bank" {{ old('payment') == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                                        <option value="Marketplace" {{ in_array(old('payment'), ['Marketplace', 'Marketplace (Tokopedia/Shopee)']) ? 'selected' : '' }}>Marketplace</option>
                                        <option value="Petty Cash" {{ old('payment') == 'Petty Cash' ? 'selected' : '' }}>Petty Cash Gudang</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Ekspedisi & Pengiriman --}}
                <div class="col-lg-6">
                    <div class="card form-card h-100">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h6 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                                <i class="mdi mdi-truck-fast-outline me-2" style="color: #0d9488;"></i> 2. Info Pengiriman (On Delivery)
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold small text-dark">Tipe Pembelian</label>
                                    <select name="purchase_type" id="purchaseTypeSelect" class="form-select">
                                        <option value="Lokal" {{ old('purchase_type', 'Lokal') == 'Lokal' ? 'selected' : '' }}>Lokal</option>
                                        <option value="Impor" {{ old('purchase_type') == 'Impor' ? 'selected' : '' }}>Impor</option>
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold small text-dark">Ekspedisi / Kurir / Pengambilan</label>
                                    <input type="text" name="cargo" class="form-control" placeholder="Contoh: JNE / SiCepat / GoSend / Ambil Langsung" value="{{ old('cargo') }}">
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold small text-dark">Nomor Resi / Pelacakan</label>
                                    <input type="text" name="no_resi" class="form-control font-monospace" placeholder="Nomor resi ekspedisi (jika ada)" value="{{ old('no_resi') }}">
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label fw-semibold small text-dark">Ongkos Kirim (Delivery Cost)</label>
                                    <div class="input-group currency-input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" name="delivery_cost" id="deliveryCostInput" class="form-control text-end auto-currency" placeholder="0" value="{{ old('delivery_cost', 0) }}">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-dark">Catatan Pembelian / Pengiriman</label>
                                    <textarea name="note" class="form-control" rows="2" placeholder="Catatan opsional (mis. link produk marketplace, garansi toko, dll)">{{ old('note') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Full Width: Items Table --}}
                <div class="col-12">
                    <div class="card form-card">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                                    <i class="mdi mdi-format-list-bulleted me-2" style="color: #0d9488;"></i> 3. Daftar Item Barang yang Dibeli
                                </h6>
                                <span class="text-muted small">Input kuantiti barang dan harga beli satuan</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-primary shadow-xs" data-bs-toggle="modal" data-bs-target="#modalPullPrItems" style="background-color: #0d9488; border-color: #0d9488;">
                                    <i class="mdi mdi-file-import-outline me-1"></i> + Tarik Item dari PR
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnAddItemRow">
                                    <i class="mdi mdi-plus me-1"></i> Tambah Item Manual
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-items mb-0" id="tableItems">
                                    <thead>
                                        <tr>
                                            <th style="width: 40px;" class="text-center">No</th>
                                            <th>Nama Barang / Deskripsi</th>
                                            <th style="width: 110px;" class="text-center">Qty Dibeli</th>
                                            <th style="width: 90px;" class="text-center">Satuan</th>
                                            <th style="width: 180px;" class="text-end">Harga Satuan (Rp)</th>
                                            <th style="width: 180px;" class="text-end">Subtotal (Rp)</th>
                                            <th style="width: 50px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsTableBody">
                                        @if ($sourcePr && count($prefillItems))
                                            @foreach ($prefillItems as $idx => $item)
                                                <tr class="item-row" data-pr-detail-id="{{ $item['pr_detail_id'] }}">
                                                    <td class="text-center fw-semibold row-no">{{ $idx + 1 }}</td>
                                                    <td>
                                                        <input type="hidden" name="pr_detail_id[]" value="{{ $item['pr_detail_id'] }}">
                                                        <input type="hidden" name="id_product[]" value="{{ $item['id_product'] }}">
                                                        <input type="hidden" name="product[]" value="{{ $item['name'] }}">
                                                        <div class="fw-bold text-dark">{{ $item['name'] }}</div>
                                                        @if (!empty($item['brand_pn']))
                                                            <div class="small text-muted"><i class="mdi mdi-tag-outline me-1"></i>{{ $item['brand_pn'] }}</div>
                                                        @endif
                                                        <div class="d-flex align-items-center gap-1 mt-1">
                                                            <span class="badge bg-label-primary font-10">{{ $sourcePr->no_pr }}</span>
                                                            <span class="badge bg-label-info font-10">Sisa PR: {{ $item['pr_remaining'] }} {{ $item['unit'] }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="qty[]" class="form-control text-center item-qty" min="1" step="any" value="{{ $item['qty'] }}" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="unit[]" class="form-control text-center" value="{{ $item['unit'] }}" readonly>
                                                    </td>
                                                    <td>
                                                        <div class="input-group currency-input-group">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="text" name="price[]" class="form-control text-end item-price auto-currency" placeholder="0" value="" required>
                                                        </div>
                                                    </td>
                                                    <td class="text-end fw-bold text-dark item-subtotal-cell">
                                                        Rp 0
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-remove-row" title="Hapus item">
                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="item-row">
                                                <td class="text-center fw-semibold row-no">1</td>
                                                <td>
                                                    <input type="text" name="product[]" class="form-control" placeholder="Nama barang / sparepart yang dibeli" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="qty[]" class="form-control text-center item-qty" min="1" step="any" value="1" required>
                                                </td>
                                                <td>
                                                    <input type="text" name="unit[]" class="form-control text-center" value="Pcs">
                                                </td>
                                                <td>
                                                    <div class="input-group currency-input-group">
                                                        <span class="input-group-text">Rp</span>
                                                        <input type="text" name="price[]" class="form-control text-end item-price auto-currency" placeholder="0" value="0" required>
                                                    </div>
                                                </td>
                                                <td class="text-end fw-bold text-dark item-subtotal-cell">
                                                    Rp 0
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-remove-row" disabled>
                                                        <i class="mdi mdi-trash-can-outline"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light">
                                            <td colspan="5" class="text-end fw-semibold">Subtotal Barang:</td>
                                            <td class="text-end fw-bold text-dark" id="displaySubtotal">Rp 0</td>
                                            <td></td>
                                        </tr>
                                        <tr class="bg-light align-middle">
                                            <td colspan="5" class="text-end fw-semibold">
                                                <div class="d-inline-flex align-items-center justify-content-end gap-2">
                                                    <span>Diskon:</span>
                                                    <div class="input-group input-group-sm" style="width: 220px;">
                                                        <select name="discount_type" id="discountTypeSelect" class="form-select form-select-sm" style="max-width: 80px; font-weight: 600;">
                                                            <option value="nominal" {{ old('discount_type', 'nominal') == 'nominal' ? 'selected' : '' }}>Rp</option>
                                                            <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>%</option>
                                                        </select>
                                                        <input type="text" name="discount_value" id="discountValueInput" class="form-control form-control-sm text-end" placeholder="0" value="{{ old('discount_value', '') }}">
                                                    </div>
                                                    <input type="hidden" name="diskon" id="hiddenDiskon" value="{{ old('diskon', 0) }}">
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold text-danger" id="displayDiscount">- Rp 0</td>
                                            <td></td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td colspan="5" class="text-end fw-semibold">Ongkos Kirim:</td>
                                            <td class="text-end fw-bold text-dark" id="displayDeliveryCost">Rp 0</td>
                                            <td></td>
                                        </tr>
                                        <tr class="table-primary border-top border-2">
                                            <td colspan="5" class="text-end fw-bold fs-6 text-primary">TOTAL PEMBELIAN:</td>
                                            <td class="text-end fw-bold fs-6 text-primary" id="displayGrandTotal">Rp 0</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Action Bar --}}
                <div class="col-12 text-end mb-4">
                    <a href="{{ $sourcePr ? route('purchase-request.show', $sourcePr->id_pending) : route('purchase.index') }}" class="btn btn-outline-secondary me-2">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" id="btnSubmitForm" style="background-color: #0d9488; border-color: #0d9488;">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Simpan Direct Purchase
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Modal: Tarik Item dari PR Disetujui (Multi-PR Consolidation) --}}
    <div class="modal fade" id="modalPullPrItems" tabindex="-1" aria-labelledby="modalPullPrItemsLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 shadow-xs" style="background-color: #ccfbf1; color: #0f766e;">
                                <i class="mdi mdi-file-import-outline font-22"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalPullPrItemsLabel">Tarik Item dari Purchase Request (PR)</h5>
                            <small class="text-muted font-12">
                                Pilih item dari PR yang disetujui (lintas PR/SO) untuk digabungkan ke dalam Direct Purchase ini
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-2 mb-3 align-items-center justify-content-between">
                        <div class="col-md-7 col-12">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="mdi mdi-magnify text-muted"></i></span>
                                <input type="text" id="inputSearchPrItems" class="form-control border-start-0 ps-0" placeholder="Ketik nomor PR, nomor SO, part number, atau nama barang...">
                            </div>
                        </div>
                        <div class="col-md-5 col-12 text-md-end d-flex align-items-center justify-content-md-end gap-2">
                            <span class="badge bg-label-primary py-2 px-3 font-12">
                                <span id="countSelectedPrItems">0</span> item dipilih
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnReloadPrItems" title="Muat ulang data item PR">
                                <i class="mdi mdi-refresh me-1"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive border rounded" style="max-height: 440px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" id="tablePullPrItems">
                            <thead class="table-light sticky-top" style="z-index: 2;">
                                <tr>
                                    <th class="text-center" style="width: 40px;">
                                        <input type="checkbox" class="form-check-input" id="checkAllModalPrItems">
                                    </th>
                                    <th style="width: 170px;">Dokumen PR</th>
                                    <th style="width: 150px;">SO / Proyek</th>
                                    <th>Item / Sparepart</th>
                                    <th class="text-center" style="width: 90px;">Sisa PR</th>
                                    <th class="text-center" style="width: 110px;">Qty Ambil</th>
                                    <th class="text-end" style="width: 140px;">Estimasi Harga</th>
                                    <th style="width: 130px;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyPullPrItems">
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <span class="spinner-border spinner-border-sm me-2 text-primary" role="status"></span>
                                        Memuat daftar item Purchase Request yang tersedia...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2 text-muted small d-flex align-items-center gap-1">
                        <i class="mdi mdi-information-outline text-primary"></i>
                        <span>Anda dapat mencentang beberapa item dari PR berbeda untuk digabungkan dalam satu transaksi pengadaan langsung.</span>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light bg-opacity-25 px-4 py-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs" id="btnApplyPullPrItems" disabled style="background-color: #0d9488; border-color: #0d9488;">
                        <i class="mdi mdi-check-bold me-1"></i>
                        <span>Tambahkan ke Direct Purchase</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('components.modal.warehouse.supplier.form')
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <script>
        $(document).ready(function() {
            // Select2 Custom Template with Badge Lokal / Impor
            function formatSupplierOption(state) {
                if (!state.id) {
                    return state.text;
                }
                var $el = $(state.element);
                var type = $el.data('type') || (state.text.indexOf('[Impor]') !== -1 ? 'Impor' : 'Lokal');
                var isImpor = (type === 'Impor');
                var badgeClass = isImpor ? 'bg-label-info text-info' : 'bg-label-success text-success';
                var badgeIcon = isImpor ? 'mdi-earth' : 'mdi-map-marker-outline';
                var supplierName = $el.data('supplier-name') || state.text.replace(/^\[(Lokal|Impor)\]\s*/, '').replace(/\s*\(.*\)$/, '');
                var address = $el.data('address') ? '<span class="text-muted font-11 ms-1">(' + $el.data('address') + ')</span>' : '';

                return $(
                    '<div class="d-flex align-items-center justify-content-between py-1">' +
                        '<div class="d-flex align-items-center gap-2 text-truncate">' +
                            '<span class="badge ' + badgeClass + ' rounded-pill px-2 py-0 font-10 fw-bold flex-shrink-0 d-inline-flex align-items-center gap-1">' +
                                '<i class="mdi ' + badgeIcon + '"></i> ' + type +
                            '</span>' +
                            '<span class="fw-semibold text-dark text-truncate">' + supplierName + '</span>' +
                            address +
                        '</div>' +
                    '</div>'
                );
            }

            function formatSupplierSelection(state) {
                if (!state.id) {
                    return state.text;
                }
                var $el = $(state.element);
                var type = $el.data('type') || (state.text.indexOf('[Impor]') !== -1 ? 'Impor' : 'Lokal');
                var isImpor = (type === 'Impor');
                var badgeClass = isImpor ? 'bg-label-info text-info' : 'bg-label-success text-success';
                var badgeIcon = isImpor ? 'mdi-earth' : 'mdi-map-marker-outline';
                var supplierName = $el.data('supplier-name') || state.text.replace(/^\[(Lokal|Impor)\]\s*/, '').replace(/\s*\(.*\)$/, '');

                return $(
                    '<div class="d-inline-flex align-items-center gap-2">' +
                        '<span class="badge ' + badgeClass + ' rounded-pill px-2 py-0 font-10 fw-bold d-inline-flex align-items-center gap-1">' +
                            '<i class="mdi ' + badgeIcon + '"></i> ' + type +
                        '</span>' +
                        '<span class="fw-semibold text-dark">' + supplierName + '</span>' +
                    '</div>'
                );
            }

            var $supplierSelect = $('.select2-supplier').select2({
                placeholder: 'Pilih Supplier Terdaftar atau ketik toko manual',
                allowClear: true,
                width: '100%',
                templateResult: formatSupplierOption,
                templateSelection: formatSupplierSelection,
                escapeMarkup: function(m) { return m; }
            }).on('change', function() {
                var $selected = $(this).find('option:selected');
                var selectedVal = $(this).val();
                if (selectedVal) {
                    var supplierName = $selected.data('supplier-name') || $selected.text().replace(/^\[(Lokal|Impor)\]\s*/, '').replace(/\s*\(.*\)$/, '');
                    $('#supplierNameInput').val(supplierName);

                    // Otomatis sinkronkan Tipe Pembelian di Card 2 (Info Pengiriman)
                    var purchaseType = $selected.data('type');
                    if (purchaseType) {
                        $('#purchaseTypeSelect').val(purchaseType).trigger('change');
                    }
                }
            });

            // Sinkronkan tipe pembelian saat pertama kali load jika supplier sudah terpilih
            if ($supplierSelect.val()) {
                var $initSelected = $supplierSelect.find('option:selected');
                var initType = $initSelected.data('type');
                if (initType) {
                    $('#purchaseTypeSelect').val(initType);
                }
            }

            // Format number to IDR Rupiah
            function formatRupiah(num) {
                return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function parseRupiah(str) {
                if (!str) return 0;
                var clean = str.toString().replace(/[^0-9]/g, '');
                return clean ? parseFloat(clean) : 0;
            }

            // Auto-currency formatting on input
            $(document).on('input', '.auto-currency', function() {
                var val = parseRupiah($(this).val());
                if (val === 0 && $(this).val() === '') {
                    $(this).val('');
                } else {
                    $(this).val(val.toLocaleString('id-ID'));
                }
                recalcTotals();
            });

            // Recalculate totals
            function recalcTotals() {
                var subtotal = 0;
                $('#tableItems tbody .item-row').each(function() {
                    var qty = parseFloat($(this).find('.item-qty').val()) || 0;
                    var price = parseRupiah($(this).find('.item-price').val());
                    var rowSubtotal = qty * price;
                    subtotal += rowSubtotal;
                    $(this).find('.item-subtotal-cell').text(formatRupiah(rowSubtotal));
                });

                var discountType = $('#discountTypeSelect').val() || 'nominal';
                var discountInputRaw = $('#discountValueInput').val() || '';
                var discountAmount = 0;

                if (discountType === 'percent') {
                    var percentVal = parseFloat(discountInputRaw.replace(/,/g, '.')) || 0;
                    if (percentVal > 100) percentVal = 100;
                    if (percentVal < 0) percentVal = 0;
                    discountAmount = (subtotal * percentVal) / 100;
                    if (percentVal > 0) {
                        $('#displayDiscount').text('- ' + formatRupiah(discountAmount) + ' (' + percentVal + '%)');
                    } else {
                        $('#displayDiscount').text('- Rp 0');
                    }
                } else {
                    var nominalVal = parseRupiah(discountInputRaw);
                    discountAmount = nominalVal;
                    if (discountAmount > subtotal) {
                        discountAmount = subtotal;
                    }
                    if (discountAmount > 0) {
                        $('#displayDiscount').text('- ' + formatRupiah(discountAmount));
                    } else {
                        $('#displayDiscount').text('- Rp 0');
                    }
                }

                $('#hiddenDiskon').val(Math.round(discountAmount));

                var deliveryCost = parseRupiah($('#deliveryCostInput').val());
                var grandTotal = Math.max(0, subtotal - discountAmount + deliveryCost);

                $('#displaySubtotal').text(formatRupiah(subtotal));
                $('#displayDeliveryCost').text(formatRupiah(deliveryCost));
                $('#displayGrandTotal').text(formatRupiah(grandTotal));
            }

            $('#discountTypeSelect').on('change', function() {
                var type = $(this).val();
                var rawVal = $('#discountValueInput').val();
                if (type === 'percent') {
                    $('#discountValueInput').attr('placeholder', '0 %');
                    var num = parseRupiah(rawVal);
                    if (num > 100) {
                        $('#discountValueInput').val('');
                    }
                } else {
                    $('#discountValueInput').attr('placeholder', '0');
                    var num = parseRupiah(rawVal);
                    if (num > 0) {
                        $('#discountValueInput').val(num.toLocaleString('id-ID'));
                    }
                }
                recalcTotals();
            });

            $('#discountValueInput').on('input', function() {
                var type = $('#discountTypeSelect').val();
                if (type === 'nominal') {
                    var val = parseRupiah($(this).val());
                    if (val === 0 && $(this).val() === '') {
                        $(this).val('');
                    } else {
                        $(this).val(val.toLocaleString('id-ID'));
                    }
                }
                recalcTotals();
            });

            $(document).on('input change', '.item-qty', function() {
                recalcTotals();
            });

            $('#deliveryCostInput').on('input change', function() {
                recalcTotals();
            });

            // Add manual item row
            $('#btnAddItemRow').on('click', function() {
                var rowCount = $('#tableItems tbody .item-row').length + 1;
                var newRow = `
                    <tr class="item-row">
                        <td class="text-center fw-semibold row-no">${rowCount}</td>
                        <td>
                            <input type="text" name="product[]" class="form-control" placeholder="Nama barang / sparepart yang dibeli" required>
                        </td>
                        <td>
                            <input type="number" name="qty[]" class="form-control text-center item-qty" min="1" step="any" value="1" required>
                        </td>
                        <td>
                            <input type="text" name="unit[]" class="form-control text-center" value="Pcs">
                        </td>
                        <td>
                            <div class="input-group currency-input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" name="price[]" class="form-control text-end item-price auto-currency" placeholder="0" value="0" required>
                            </div>
                        </td>
                        <td class="text-end fw-bold text-dark item-subtotal-cell">
                            Rp 0
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-remove-row" title="Hapus item">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#itemsTableBody').append(newRow);
                updateRowNumbers();
                recalcTotals();
            });

            // Remove item row
            $(document).on('click', '.btn-remove-row', function() {
                if ($('#tableItems tbody .item-row').length > 1) {
                    $(this).closest('tr').remove();
                    updateRowNumbers();
                    recalcTotals();
                } else {
                    // Reset single row to empty
                    var $tr = $(this).closest('tr');
                    $tr.removeAttr('data-pr-detail-id');
                    $tr.find('input[name="pr_detail_id[]"]').remove();
                    $tr.find('input[name="id_product[]"]').remove();
                    $tr.find('input[name="product[]"]').replaceWith('<input type="text" name="product[]" class="form-control" placeholder="Nama barang / sparepart yang dibeli" required>');
                    $tr.find('.item-qty').val(1);
                    $tr.find('input[name="unit[]"]').val('Pcs').prop('readonly', false);
                    $tr.find('.item-price').val(0);
                    $tr.find('div.badge, div.small').remove();
                    recalcTotals();
                }
            });

            function updateRowNumbers() {
                $('#tableItems tbody .item-row').each(function(index) {
                    $(this).find('.row-no').text(index + 1);
                    if ($('#tableItems tbody .item-row').length === 1 && !$(this).attr('data-pr-detail-id')) {
                        $(this).find('.btn-remove-row').prop('disabled', true);
                    } else {
                        $(this).find('.btn-remove-row').prop('disabled', false);
                    }
                });
            }

            // ── Modal Pull PR Items Handler ──
            var cachedPrItems = [];

            function loadAvailablePrItems() {
                var $tbody = $('#tbodyPullPrItems');
                $tbody.html('<tr><td colspan="8" class="text-center py-4 text-muted">' +
                    '<span class="spinner-border spinner-border-sm me-2 text-primary" role="status"></span>' +
                    'Memuat daftar item Purchase Request yang tersedia...</td></tr>');
                $('#checkAllModalPrItems').prop('checked', false);
                $('#btnApplyPullPrItems').prop('disabled', true);
                $('#countSelectedPrItems').text('0');

                $.ajax({
                    url: '{{ route("purchase-request.available-items") }}',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        cachedPrItems = res.items || [];
                        renderPrItemsTable(cachedPrItems);
                    },
                    error: function () {
                        $tbody.html('<tr><td colspan="8" class="text-center py-4 text-danger">' +
                            '<i class="mdi mdi-alert-circle-outline me-1"></i> Gagal memuat data item PR. Silakan coba lagi.</td></tr>');
                    }
                });
            }

            function renderPrItemsTable(items) {
                var $tbody = $('#tbodyPullPrItems');
                $tbody.empty();

                // Dapatkan ID PR detail yang sudah ada di tabel form
                var existingPrDetailIds = [];
                $('#tableItems tbody .item-row').each(function() {
                    var prDetailId = $(this).find('input[name="pr_detail_id[]"]').val();
                    if (prDetailId) {
                        existingPrDetailIds.push(parseInt(prDetailId));
                    }
                });

                var query = ($('#inputSearchPrItems').val() || '').trim().toLowerCase();
                var filtered = items;
                if (query) {
                    filtered = items.filter(function (it) {
                        var pool = (it.no_pr + ' ' + it.no_so + ' ' + it.brand_pn + ' ' + it.product_name + ' ' + it.note).toLowerCase();
                        return pool.indexOf(query) !== -1;
                    });
                }

                if (!filtered.length) {
                    $tbody.html('<tr><td colspan="8" class="text-center py-4 text-muted">' +
                        '<i class="mdi mdi-information-outline me-1"></i> Tidak ada item PR yang cocok / tersedia.</td></tr>');
                    return;
                }

                filtered.forEach(function (it) {
                    var alreadyInForm = existingPrDetailIds.indexOf(parseInt(it.pr_detail_id)) !== -1;
                    var formattedPrice = formatRupiah(it.price || 0);

                    var $tr = $('<tr>' +
                        '<td class="text-center">' +
                            '<input type="checkbox" class="form-check-input check-pr-item" data-id="' + it.pr_detail_id + '" ' + (alreadyInForm ? 'disabled' : '') + '>' +
                        '</td>' +
                        '<td>' +
                            '<span class="fw-bold text-primary font-monospace font-12">' + it.no_pr + '</span>' +
                            '<div class="text-muted font-11"><i class="mdi mdi-calendar-outline me-1"></i>' + it.pr_date + '</div>' +
                            (alreadyInForm ? '<span class="badge bg-label-secondary font-10">Sudah Masuk Form</span>' : '') +
                        '</td>' +
                        '<td>' +
                            '<span class="badge bg-label-dark font-11">' + it.no_so + '</span>' +
                        '</td>' +
                        '<td>' +
                            '<div class="fw-semibold text-dark font-13">' + it.product_name + '</div>' +
                            (it.brand_pn && it.brand_pn !== it.product_name ? '<div class="text-muted font-11">' + it.brand_pn + '</div>' : '') +
                        '</td>' +
                        '<td class="text-center">' +
                            '<span class="badge bg-label-info font-12">' + it.remaining_qty + ' ' + it.unit + '</span>' +
                        '</td>' +
                        '<td class="text-center">' +
                            '<input type="number" class="form-control form-control-sm text-center input-qty-take mx-auto" ' +
                                'data-id="' + it.pr_detail_id + '" min="1" max="' + it.remaining_qty + '" value="' + it.remaining_qty + '" style="width: 80px;" ' + (alreadyInForm ? 'disabled' : '') + '>' +
                        '</td>' +
                        '<td class="text-end fw-semibold text-dark font-12">' +
                            formattedPrice +
                        '</td>' +
                        '<td>' +
                            '<span class="text-muted small">' + (it.note || '-') + '</span>' +
                        '</td>' +
                    '</tr>');
                    $tbody.append($tr);
                });

                updateModalSelectionCount();
            }

            function updateModalSelectionCount() {
                var checkedCount = $('#tablePullPrItems .check-pr-item:checked').length;
                $('#countSelectedPrItems').text(checkedCount);
                $('#btnApplyPullPrItems').prop('disabled', checkedCount === 0);
                $('#btnApplyPullPrItems span').text(checkedCount > 0 ? 'Tambahkan (' + checkedCount + ' item) ke Form' : 'Tambahkan ke Direct Purchase');
            }

            $('#modalPullPrItems').on('shown.bs.modal', function () {
                if (!cachedPrItems.length) {
                    loadAvailablePrItems();
                } else {
                    renderPrItemsTable(cachedPrItems);
                }
                $('#inputSearchPrItems').focus();
            });

            $('#btnReloadPrItems').on('click', function () {
                loadAvailablePrItems();
            });

            $('#inputSearchPrItems').on('input', function () {
                renderPrItemsTable(cachedPrItems);
            });

            $('#checkAllModalPrItems').on('change', function () {
                var isChecked = $(this).is(':checked');
                $('#tablePullPrItems .check-pr-item:not(:disabled)').prop('checked', isChecked);
                updateModalSelectionCount();
            });

            $(document).on('change', '.check-pr-item', function () {
                updateModalSelectionCount();
            });

            // Apply selected PR items to Direct Purchase form table
            $('#btnApplyPullPrItems').on('click', function () {
                var selectedIds = [];
                $('#tablePullPrItems .check-pr-item:checked').each(function () {
                    selectedIds.push(parseInt($(this).data('id')));
                });

                if (!selectedIds.length) return;

                // Jika baris pertama hanya row kosong tanpa pr_detail_id dan tanpa text input, hapus dulu
                var $firstRow = $('#tableItems tbody .item-row:first');
                if ($('#tableItems tbody .item-row').length === 1 && !$firstRow.find('input[name="pr_detail_id[]"]').length && !$firstRow.find('input[name="product[]"]').val()) {
                    $firstRow.remove();
                }

                selectedIds.forEach(function (detailId) {
                    var it = cachedPrItems.find(function (x) { return x.pr_detail_id === detailId; });
                    if (!it) return;

                    var qtyToTake = parseFloat($('.input-qty-take[data-id="' + detailId + '"]').val()) || it.remaining_qty;
                    var itemPrice = it.price || 0;
                    var formattedPrice = (itemPrice > 0) ? itemPrice.toLocaleString('id-ID') : '0';
                    var rowSubtotal = qtyToTake * itemPrice;

                    var newRowHtml = `
                        <tr class="item-row" data-pr-detail-id="${it.pr_detail_id}">
                            <td class="text-center fw-semibold row-no">#</td>
                            <td>
                                <input type="hidden" name="pr_detail_id[]" value="${it.pr_detail_id}">
                                <input type="hidden" name="id_product[]" value="${it.id_product || ''}">
                                <input type="hidden" name="product[]" value="${it.product_name}">
                                <div class="fw-bold text-dark">${it.product_name}</div>
                                ${it.brand_pn && it.brand_pn !== it.product_name ? '<div class="small text-muted"><i class="mdi mdi-tag-outline me-1"></i>' + it.brand_pn + '</div>' : ''}
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <span class="badge bg-label-primary font-10">${it.no_pr}</span>
                                    <span class="badge bg-label-dark font-10">${it.no_so}</span>
                                    <span class="badge bg-label-info font-10">Sisa PR: ${it.remaining_qty} ${it.unit}</span>
                                </div>
                            </td>
                            <td>
                                <input type="number" name="qty[]" class="form-control text-center item-qty" min="1" step="any" value="${qtyToTake}" required>
                            </td>
                            <td>
                                <input type="text" name="unit[]" class="form-control text-center" value="${it.unit}" readonly>
                            </td>
                            <td>
                                <div class="input-group currency-input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" name="price[]" class="form-control text-end item-price auto-currency" placeholder="0" value="" required>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-dark item-subtotal-cell">
                                Rp 0
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-remove-row" title="Hapus item">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $('#itemsTableBody').append(newRowHtml);
                });

                $('#modalPullPrItems').modal('hide');
                updateRowNumbers();
                recalcTotals();
            });

            // ── Form Submit Validation Guard ──
            $('#directPurchaseForm').on('submit', function(e) {
                var supplierVal = $('#supplierSelect').val();
                if (!supplierVal) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Supplier Wajib Dipilih',
                            text: 'Silakan pilih rekanan supplier dari master data terlebih dahulu. Jika supplier belum terdaftar, klik "+ Tambah Supplier Baru".',
                            confirmButtonText: 'Pilih Supplier',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        }).then(function() {
                            $('#supplierSelect').select2('open');
                        });
                    } else {
                        alert('Supplier wajib dipilih dari master data terlebih dahulu.');
                        $('#supplierSelect').focus();
                    }
                    return false;
                }

                var itemCount = 0;
                var hasEmptyItem = false;
                $('#tableItems tbody .item-row').each(function() {
                    var prod = $(this).find('input[name="product[]"]').val();
                    var qty = parseFloat($(this).find('.item-qty').val()) || 0;
                    if (!prod || prod.trim() === '' || qty <= 0) {
                        hasEmptyItem = true;
                    }
                    itemCount++;
                });

                if (itemCount === 0 || hasEmptyItem) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Item Barang Belum Lengkap',
                            text: 'Pastikan seluruh baris item barang memiliki nama produk dan kuantiti minimal 1.',
                            confirmButtonText: 'Periksa Kembali',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            buttonsStyling: false
                        });
                    } else {
                        alert('Pastikan seluruh baris item barang memiliki nama produk dan kuantiti minimal 1.');
                    }
                    return false;
                }

                return true;
            });

            // Initial calculation
            recalcTotals();
            updateRowNumbers();
        });
    </script>
@endpush
