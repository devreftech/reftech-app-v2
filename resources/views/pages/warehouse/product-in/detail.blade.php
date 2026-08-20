@extends('layouts.sales.app')
@php
    $isEditable = !$product->invoice;
    $docTitle = $product->purchaseOrder->no_gr ?? $product->no_product_in;
    // Kalau semua item udah punya harga referensi dari PO asal, gak perlu form
    // invoicing yang panjang — tinggal konfirmasi No. Invoice-nya aja lewat modal kecil.
    $grandSubtotal = $isEditable ? $detail->sum(fn ($d) => is_null($d->po_price) ? 0 : $d->po_price * $d->qty) : 0;
    $hasAnyPoPrice = $isEditable && $detail->contains(fn ($d) => !is_null($d->po_price));
    $allItemsSynced = $isEditable && $detail->isNotEmpty() && $detail->every(fn ($d) => !is_null($d->po_price));
@endphp
@section('title', 'Detail Product In ' . $docTitle)
@section('content')
    <div class="row justify-content-center gr-detail-print">
        <div class="col-xl-9 col-12">

            {{-- Alert (dipakai fitur edit inline) --}}
            <div id="alert-box" class="alert d-none mb-3 d-print-none" role="alert"></div>

            <div class="d-flex justify-content-between align-items-center mb-4 d-print-none flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">
                        Detail Product In
                        @if (is_null($product->id_purchase_order))
                            <span class="badge bg-label-secondary align-middle">GR Manual</span>
                        @endif
                    </h4>
                    <p class="text-muted mb-0 small">
                        @if ($isEditable)
                            Barang sudah diterima, menunggu invoice — klik nilai untuk mengedit.
                        @else
                            Bukti terima barang &amp; rincian invoice.
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ url('/product-in') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="mdi mdi-printer-outline me-1"></i> Print
                    </button>
                    @if ($isEditable)
                        @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                            @if ($allItemsSynced)
                                {{-- Semua item udah sinkron harga dari PO — tinggal konfirmasi No. Invoice, gak perlu form panjang. --}}
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalConfirmInvoice">
                                    <i class="mdi mdi-check-decagram-outline me-1"></i> Konfirmasi Invoice
                                </button>
                            @else
                                <a href="{{ route('product-in.edit', $product->id) }}" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-receipt-text-plus-outline me-1"></i> Isi Invoice
                                </a>
                            @endif
                        @endif
                    @else
                        <a class="btn btn-outline-secondary btn-sm" target="_blank"
                            href="{{ route('productIn.print', $product->id) }}">
                            <i class="mdi mdi-download-outline me-1"></i> Download
                        </a>
                        @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                            <a href="{{ route('product-in.edit', $product->id) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="mdi mdi-pencil-outline me-1"></i> Edit Price
                            </a>
                        @endif
                    @endif
                    <button type="button" class="btn btn-danger btn-sm" id="btn-delete-all" data-id="{{ $product->id }}">
                        <i class="mdi mdi-trash-can-outline me-1"></i> Hapus Data
                    </button>
                </div>
            </div>

            {{-- Modal Konfirmasi Invoice — cuma muncul kalau semua item udah sinkron harga dari PO,
                 jadi yang perlu diisi cuma No. Invoice-nya, harga/subtotal udah otomatis dari PO. --}}
            @if ($allItemsSynced && (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting'))
                <div class="modal fade d-print-none" id="modalConfirmInvoice" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('product-in.invoicing', $product->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Konfirmasi Invoice</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted small mb-3">
                                        Semua item udah sinkron sama harga PO
                                        @if ($product->purchaseOrder)
                                            <strong>{{ $product->purchaseOrder->no_po }}</strong>
                                        @endif
                                        — tinggal isi No. Invoice dari supplier, sisanya otomatis.
                                    </p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">No. Invoice <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="invoice" required
                                            placeholder="Nomor invoice dari supplier">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tanggal Invoice</label>
                                        <input type="date" class="form-control" name="date_invoice"
                                            value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tax</label>
                                        <select class="form-select" id="confirm-tax" name="tax">
                                            <option value="0" selected>0%</option>
                                            <option value="11">11%</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Catatan</label>
                                        <textarea class="form-control" name="note" rows="2">-</textarea>
                                    </div>
                                    <div class="border rounded p-2 small bg-light">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Subtotal (dari PO)</span>
                                            <span class="fw-semibold">Rp {{ number_format($grandSubtotal, 0, '', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Total</span>
                                            <span class="fw-bold" id="confirm-total-preview">Rp {{ number_format($grandSubtotal, 0, '', '.') }}</span>
                                        </div>
                                    </div>
                                    {{-- Field yang udah otomatis, gak perlu diisi manual --}}
                                    <input type="hidden" name="supplier" value="{{ $product->id_supplier }}">
                                    <input type="hidden" name="shipping" value="0">
                                    <input type="hidden" name="subtotal" value="{{ $grandSubtotal }}">
                                    <input type="hidden" name="total_no_tax" value="{{ $grandSubtotal }}">
                                    <input type="hidden" id="confirm-total-input" name="total" value="{{ $grandSubtotal }}">
                                    @foreach ($detail as $d)
                                        @php
                                            $rowDisc = $d->po_disc ?? 0;
                                            $rowAmount = ($d->po_price * $d->qty) - $rowDisc;
                                        @endphp
                                        <input type="hidden" name="price[]" value="{{ $d->po_price }}">
                                        <input type="hidden" name="disc[]" value="{{ $rowDisc }}">
                                        <input type="hidden" name="amount[]" value="{{ $rowAmount }}">
                                    @endforeach
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-check-decagram-outline me-1"></i> Konfirmasi & Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                    (function () {
                        var subtotal = {{ $grandSubtotal }};
                        var $tax = document.getElementById('confirm-tax');
                        var $totalInput = document.getElementById('confirm-total-input');
                        var $totalPreview = document.getElementById('confirm-total-preview');
                        function fmt(n) {
                            return 'Rp ' + Math.round(n).toLocaleString('id-ID');
                        }
                        function recalc() {
                            var tax = parseFloat($tax.value) || 0;
                            var total = subtotal + (subtotal * tax / 100);
                            $totalInput.value = total;
                            $totalPreview.textContent = fmt(total);
                        }
                        if ($tax) $tax.addEventListener('change', recalc);
                    })();
                </script>
            @endif

            {{-- Sembunyikan select produk/supplier sebagai data source utk mode edit --}}
            @if ($isEditable)
                <select id="product-options" class="d-none">
                    @foreach ($detProduct as $dp)
                        <option value="{{ $dp->id }}">
                            {{ $dp->product->commodity }} ({{ $dp->product->detail_desc }}) || {{ $dp->replacement }} - {{ $dp->product->go == 'Genuine' ? 'G' : 'R' }}
                        </option>
                    @endforeach
                </select>
                <select id="supplier-options" class="d-none">
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->supplier }}</option>
                    @endforeach
                </select>
            @endif

            {{-- Switching tab: dokumen GR vs Biaya Tambahan (landed cost dkk) --}}
            <ul class="nav nav-tabs mb-3 d-print-none" id="productInTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-detail-barang-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-detail-barang" type="button" role="tab">
                        <i class="mdi mdi-file-document-outline me-1"></i> Detail Barang
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-biaya-tambahan-tab" data-bs-toggle="tab"
                        data-bs-target="#tab-biaya-tambahan" type="button" role="tab">
                        <i class="mdi mdi-cash-plus me-1"></i> Biaya Tambahan
                        @if ($costAllocation['total_additional_cost'] > 0)
                            <span class="badge bg-label-info ms-1">Rp {{ number_format($costAllocation['total_additional_cost'], 0, '', '.') }}</span>
                        @endif
                    </button>
                </li>
            </ul>

            <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-detail-barang" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between flex-wrap gap-3 mb-3">
                        <div>
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" width="140">
                            </div>
                            <p class="mb-0 fw-bold text-dark">PT Reftech Jaya Optima</p>
                            <p class="mb-0 text-muted small">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold mb-1" style="color:#2529fa; letter-spacing:1px;">
                                GOODS RECEIPT
                            </h3>
                            <p class="mb-0 fw-bold text-dark">
                                #{{ $product->purchaseOrder->no_gr ?? $product->no_product_in }}
                            </p>
                            @if (!$isEditable)
                                <p class="mb-0 text-muted small">Invoice: {{ $product->invoice }}</p>
                            @endif
                            <p class="mb-0 text-muted small">{{ $product->date ? \Carbon\Carbon::parse($product->date)->format('d F Y') : '-' }}</p>
                        </div>
                    </div>

                    <div style="height:2px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:12px 0 20px;"></div>

                    {{-- Info Grid --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100" style="background:#fcfcfc;">
                                <p class="fw-bold text-uppercase text-muted mb-2" style="font-size:11px; letter-spacing:.5px;">Purchase Order</p>
                                <table class="table table-borderless table-sm mb-0" style="font-size:13px;">
                                    <tr>
                                        <td class="text-muted ps-0" style="width:130px;">No. PO</td>
                                        <td>:
                                            @if ($product->purchaseOrder)
                                                <a class="fw-bold text-primary" href="{{ route('purchase.show', $product->purchaseOrder->id) }}">{{ $product->purchaseOrder->no_po }}</a>
                                                <span class="text-muted">(total PO: Rp {{ number_format($product->purchaseOrder->total, 0, '', '.') }})</span>
                                            @else
                                                <span class="fw-medium text-dark">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0">Supplier</td>
                                        <td>:
                                            @if ($isEditable)
                                                <span class="inline-edit" data-field="id_supplier" data-type="select" data-value="{{ $product->id_supplier }}">
                                                    {{ optional($product->supp)->supplier ?? '-' }}
                                                </span>
                                            @else
                                                <span class="fw-medium text-dark">{{ $product->purchaseOrder->supplier->supplier ?? optional($product->supp)->supplier ?? '-' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0">No. DO</td>
                                        <td>:
                                            @if ($isEditable)
                                                <span class="inline-edit" data-field="no_do" data-type="text" data-value="{{ $product->no_do }}">
                                                    {{ $product->no_do ?? '-' }}
                                                </span>
                                            @else
                                                <span class="fw-medium text-dark">{{ $product->no_do ?? '-' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0">Ref. Internal</td>
                                        <td>: <span class="text-muted">{{ $product->no_product_in }}</span></td>
                                    </tr>
                                    @if ($isEditable)
                                        <tr>
                                            <td class="text-muted ps-0">Tipe</td>
                                            <td>:
                                                <span class="inline-edit" data-field="info" data-type="select"
                                                    data-value="{{ $product->info }}"
                                                    data-options='[{"value":"Lokal","label":"Lokal"},{"value":"Import","label":"Import"}]'>
                                                    <span class="badge bg-label-{{ $product->info == 'Import' ? 'warning' : 'info' }}">
                                                        {{ $product->info ?? '-' }}
                                                    </span>
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100" style="background:#fcfcfc;">
                                <p class="fw-bold text-uppercase text-muted mb-2" style="font-size:11px; letter-spacing:.5px;">Penerimaan</p>
                                <table class="table table-borderless table-sm mb-0" style="font-size:13px;">
                                    <tr>
                                        <td class="text-muted ps-0" style="width:130px;">Diterima Oleh</td>
                                        <td>: <span class="fw-medium text-dark">{{ $product->creator->name ?? '-' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0">Tanggal Terima</td>
                                        <td>:
                                            @if ($isEditable)
                                                <span class="inline-edit" data-field="date" data-type="date" data-value="{{ $product->date }}">
                                                    {{ $product->date ? \Carbon\Carbon::parse($product->date)->format('d-m-Y') : '-' }}
                                                </span>
                                            @else
                                                <span class="fw-medium text-dark">{{ $product->date ? \Carbon\Carbon::parse($product->date)->format('d-m-Y') : '-' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0">Catatan</td>
                                        <td>: <span class="fw-medium text-dark">{{ $product->note ?: '-' }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Item Diterima --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0"><i class="mdi mdi-package-variant-closed text-success me-1"></i>Item Diterima (Masuk Stok)</h6>
                        @if ($isEditable)
                            <button type="button" id="btn-add-row" class="btn btn-outline-primary btn-sm d-print-none">
                                <i class="mdi mdi-plus me-1"></i> Tambah Barang
                            </button>
                        @endif
                    </div>
                    <div class="table-responsive mb-2">
                        <table class="table table-bordered align-middle mb-0" id="items-table" style="font-size:13px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:50px;" class="text-center">No</th>
                                    <th>Item</th>
                                    <th class="text-center" style="width:110px;">Gudang</th>
                                    <th class="text-center" style="width:100px;">Qty</th>
                                    @if ($isEditable)
                                        <th class="text-center" style="width:130px;">Price</th>
                                        <th class="text-center" style="width:140px;">Subtotal</th>
                                    @else
                                        <th class="text-center">Modal</th>
                                        <th class="text-center">Discount</th>
                                        <th class="text-center">Amount</th>
                                    @endif
                                    @if ($isEditable)
                                        <th class="text-center d-print-none" style="width:50px;"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($detail as $idx => $d)
                                    <tr data-row-id="{{ $d->id }}">
                                        <td class="text-center row-number">{{ $idx + 1 }}</td>
                                        <td>
                                            @if ($isEditable)
                                                <span class="inline-edit item-field product-cell"
                                                    data-id="{{ $d->id }}"
                                                    data-field="id_detail_product"
                                                    data-type="select-product"
                                                    data-value="{{ $d->id_detail_product }}">
                                                    <span class="fw-semibold">{{ $d->detailProduct->product->commodity ?? '-' }}</span>
                                                    @if ($d->brand)
                                                        <span class="badge bg-label-primary ms-1">{{ $d->brand }}</span>
                                                    @endif
                                                    <small class="text-muted d-block">{{ $d->detailProduct->replacement ?? '' }}</small>
                                                    @if ($d->detailProduct->product->description ?? null)
                                                        <small class="text-muted d-block fst-italic">{{ $d->detailProduct->product->description }}</small>
                                                    @endif
                                                </span>
                                            @else
                                                <span class="fw-semibold text-dark">{{ $d->detailProduct->product->commodity ?? '-' }}</span>
                                                <span class="text-muted">({{ $d->detailProduct->replacement ?? '-' }})</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($isEditable)
                                                <span class="inline-edit item-field"
                                                    data-id="{{ $d->id }}"
                                                    data-field="warehouse"
                                                    data-type="select"
                                                    data-value="{{ $d->warehouse }}"
                                                    data-options='[{"value":"BDG","label":"BDG"},{"value":"BKS","label":"BKS"}]'>
                                                    <span class="badge bg-label-secondary">{{ $d->warehouse }}</span>
                                                </span>
                                            @else
                                                <span class="badge bg-label-secondary">{{ $d->warehouse ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold">
                                            @if ($isEditable)
                                                <span class="inline-edit item-field"
                                                    data-id="{{ $d->id }}"
                                                    data-field="qty"
                                                    data-type="number"
                                                    data-value="{{ $d->qty }}">
                                                    {{ $d->qty }} {{ $d->detailProduct->product->unit ?? '' }}
                                                </span>
                                            @else
                                                {{ $d->qty }}
                                            @endif
                                        </td>
                                        @if ($isEditable)
                                            @php
                                                $rowSubtotal = is_null($d->po_price) ? null : $d->po_price * $d->qty;
                                            @endphp
                                            <td class="text-center">
                                                @if (is_null($d->po_price))
                                                    <span class="text-muted">-</span>
                                                @elseif (Auth::user()->role == 'Logistic')
                                                    RP {{ str_repeat('*', strlen((string) $d->po_price)) }}
                                                @else
                                                    RP {{ number_format($d->po_price, 0, '', '.') }}
                                                    <span class="badge bg-label-info" title="Dari harga PO asal">PO</span>
                                                @endif
                                            </td>
                                            <td class="text-center fw-semibold">
                                                @if (is_null($rowSubtotal))
                                                    <span class="text-muted">-</span>
                                                @elseif (Auth::user()->role == 'Logistic')
                                                    RP {{ str_repeat('*', strlen((string) $rowSubtotal)) }}
                                                @else
                                                    RP {{ number_format($rowSubtotal, 0, '', '.') }}
                                                @endif
                                            </td>
                                        @else
                                            @if (Auth::user()->role == 'Logistic')
                                                <td class="text-center">RP {{ str_repeat('*', strlen((string) $d->modal)) }}</td>
                                                <td class="text-center">RP {{ str_repeat('*', strlen((string) $d->disc)) }}</td>
                                                <td class="text-center">RP {{ str_repeat('*', strlen((string) $d->amount)) }}</td>
                                            @else
                                                <td class="text-center">RP {{ number_format($d->modal, 0, '', '.') }}</td>
                                                <td class="text-center">RP {{ number_format($d->disc, 0, '', '.') }}</td>
                                                <td class="text-center">RP {{ number_format($d->amount, 0, '', '.') }}</td>
                                            @endif
                                        @endif
                                        @if ($isEditable)
                                            <td class="text-center d-print-none">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger delete-item"
                                                    data-id="{{ $d->id }}" title="Hapus item">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr id="empty-row">
                                        <td colspan="7" class="text-center text-muted">Tidak ada item.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (!$isEditable)
                                <tfoot>
                                    <tr style="font-size:13px;">
                                        <td colspan="4" style="border:none;"></td>
                                        <td colspan="2">Subtotal</td>
                                        @if (Auth::user()->role == 'Logistic')
                                            <td>: RP {{ str_repeat('*', strlen((string) $product->subtotal)) }}</td>
                                        @else
                                            <td>: RP {{ number_format($product->subtotal, 0, '', '.') }}</td>
                                        @endif
                                    </tr>
                                    <tr style="font-size:13px;">
                                        <td colspan="4" style="border:none;"></td>
                                        <td colspan="2">Tax {{ $product->tax == '11' ? '11%' : '' }}</td>
                                        @if (Auth::user()->role == 'Logistic')
                                            <td>: RP {{ str_repeat('*', strlen((string) $tax)) }}</td>
                                        @else
                                            <td>: RP {{ number_format($tax, 0, '', '.') }}</td>
                                        @endif
                                    </tr>
                                    <tr style="font-size:13px;">
                                        <td colspan="4" style="border:none;"></td>
                                        <td colspan="2">Shipping</td>
                                        <td>: RP {{ number_format($product->shipping, 0, '', '.') }}</td>
                                    </tr>
                                    <tr style="font-size:13px;">
                                        <td colspan="4" style="border:none;"></td>
                                        <td colspan="2" style="border:none;">Total</td>
                                        @if (Auth::user()->role == 'Logistic')
                                            <td style="border:none;">: RP {{ str_repeat('*', strlen((string) $product->total)) }}</td>
                                        @else
                                            <td style="border:none;">: RP {{ number_format($product->total, 0, '', '.') }}</td>
                                        @endif
                                    </tr>
                                </tfoot>
                            @else
                                @if ($hasAnyPoPrice)
                                    <tfoot>
                                        <tr style="font-size:13px;">
                                            <td colspan="5" class="text-end fw-semibold" style="border:none;">Subtotal</td>
                                            <td class="text-center fw-bold">
                                                @if (Auth::user()->role == 'Logistic')
                                                    RP {{ str_repeat('*', strlen((string) $grandSubtotal)) }}
                                                @else
                                                    RP {{ number_format($grandSubtotal, 0, '', '.') }}
                                                @endif
                                            </td>
                                            <td class="d-print-none" style="border:none;"></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            @endif
                        </table>
                    </div>

                    @if ($isEditable)
                        <div class="d-flex justify-content-end mt-3 mb-2 d-print-none">
                            <button id="btn-save" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-content-save-outline me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    @endif

                    {{-- Item Retur / Rusak --}}
                    @if ($return->isNotEmpty())
                        <hr class="my-4">
                        <h6 class="fw-bold text-danger mb-2"><i class="mdi mdi-keyboard-return me-1"></i>Item Retur (Rusak / Tidak Sesuai)</h6>
                        @foreach ($return as $retur)
                            <div class="border rounded p-3 mb-3 bg-label-danger bg-opacity-10">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                    <span class="fw-bold text-danger small">
                                        <i class="mdi mdi-file-document-outline me-1"></i>Retur {{ $retur->no_return }}
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($retur->status == 1)
                                            <span class="badge bg-label-success">Selesai</span>
                                        @else
                                            <span class="badge bg-label-warning">Menunggu Proses</span>
                                            <button type="button" class="btn btn-primary btn-sm waves-effect clear-return d-print-none"
                                                data-id="{{ $retur->id }}">Clear Return</button>
                                        @endif
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless mb-0" style="font-size:13px;">
                                        <tbody>
                                            @foreach ($retur->detail as $rd)
                                                <tr>
                                                    <td class="ps-0">
                                                        <span class="fw-semibold text-dark">{{ $rd->replacement->product->commodity ?? '-' }}</span>
                                                        <span class="text-muted">({{ $rd->replacement->replacement ?? '-' }})</span>
                                                        @if ($rd->note)
                                                            <span class="text-muted"> &mdash; {{ $rd->note }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end fw-bold" style="width:80px;">&times; {{ $rd->qty }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    {{-- Signature --}}
                    <div class="row mt-5">
                        <div class="col-4 text-center">
                            <div style="height:56px;"></div>
                            <p class="fw-bold mx-3 mb-0" style="border-top:1px solid #000; padding-top:4px;">{{ $product->purchaseOrder->supplier->supplier ?? optional($product->supp)->supplier ?? '-' }}</p>
                            <p class="text-muted small mb-0">Pengirim</p>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4 text-center">
                            <div style="height:56px;"></div>
                            <p class="fw-bold mx-3 mb-0" style="border-top:1px solid #000; padding-top:4px;">{{ $product->creator->name ?? '-' }}</p>
                            <p class="text-muted small mb-0">Penerima (Logistic)</p>
                        </div>
                    </div>
                </div>
            </div>
            </div>{{-- /tab-detail-barang --}}

            <div class="tab-pane fade" id="tab-biaya-tambahan" role="tabpanel">
                @php
                    $isLogistic = Auth::user()->role == 'Logistic';
                    $canManageCosts = Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting';
                    $moneyFmt = fn ($n) => $isLogistic ? str_repeat('*', strlen((string) $n)) : number_format($n, 0, '', '.');
                    $hppSudahDiterapkan = $product->costs->isNotEmpty() && $detail->isNotEmpty() && $detail->every(fn ($d) => !is_null($d->hpp));
                @endphp
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Biaya Tambahan</h5>
                                <p class="text-muted small mb-0">
                                    Biaya di luar harga PO (landed cost, forwarder, bea masuk, asuransi, dll) —
                                    didistribusikan ke tiap item secara proporsional ke nilai barangnya, jadi HPP-nya
                                    mencerminkan biaya riil batch penerimaan ini. Berlaku buat barang lokal maupun impor.
                                </p>
                            </div>
                            @if ($hppSudahDiterapkan)
                                <span class="badge bg-label-success"><i class="mdi mdi-check-circle-outline me-1"></i>HPP sudah diterapkan</span>
                            @endif
                        </div>

                        <div class="border rounded p-3 mb-4" style="background:#fcfcfc;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <p class="text-muted small mb-1">Nominal PO (nilai barang)</p>
                                    <p class="fw-bold fs-5 mb-0">Rp {{ $moneyFmt($costAllocation['nominal_po']) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted small mb-1">Total Biaya Tambahan</p>
                                    <p class="fw-bold fs-5 mb-0 text-primary">Rp {{ $moneyFmt($costAllocation['total_additional_cost']) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Daftar biaya tambahan --}}
                        <h6 class="fw-semibold mb-2">Daftar Biaya</h6>
                        <div class="table-responsive mb-2">
                            <table class="table table-sm table-bordered align-middle mb-0" style="font-size:13px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Label</th>
                                        <th class="text-center" style="width:160px;">Nominal</th>
                                        <th>Ditambahkan Oleh</th>
                                        @if ($canManageCosts)
                                            <th class="text-center" style="width:60px;"></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($product->costs as $cost)
                                        <tr>
                                            <td>{{ $cost->label }}</td>
                                            <td class="text-center">Rp {{ $moneyFmt($cost->amount) }}</td>
                                            <td class="text-muted">{{ $cost->creator->name ?? '-' }}</td>
                                            @if ($canManageCosts)
                                                <td class="text-center">
                                                    <form action="{{ route('product-in.costs.destroy', $cost->id) }}" method="POST"
                                                        class="d-inline form-delete-cost">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus biaya">
                                                            <i class="mdi mdi-trash-can-outline"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $canManageCosts ? 4 : 3 }}" class="text-center text-muted">Belum ada biaya tambahan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($canManageCosts)
                            <form action="{{ route('product-in.costs.store', $product->id) }}" method="POST" class="row g-2 align-items-end mb-4">
                                @csrf
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Label Biaya</label>
                                    <input type="text" class="form-control form-control-sm" name="label" required
                                        placeholder="Misal: Forwarder ABC, Bea Masuk, Asuransi...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold">Nominal (Rp)</label>
                                    <input type="number" class="form-control form-control-sm" name="amount" min="1" required
                                        placeholder="0">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="mdi mdi-plus me-1"></i> Tambah
                                    </button>
                                </div>
                            </form>
                        @endif

                        {{-- Preview alokasi ke tiap item --}}
                        <h6 class="fw-semibold mb-2">Alokasi ke Item</h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered align-middle mb-0" style="font-size:13px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Nilai Barang</th>
                                        <th class="text-center">% Porsi</th>
                                        <th class="text-center">Biaya Teralokasi</th>
                                        <th class="text-center">HPP Baru / unit</th>
                                        @if (!$isEditable)
                                            <th class="text-center">HPP Tersimpan</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($detail as $d)
                                        @php $row = $costAllocation['rows']->get($d->id); @endphp
                                        <tr>
                                            <td>
                                                <span class="fw-semibold">{{ $d->detailProduct->product->commodity ?? '-' }}</span>
                                                <span class="text-muted">({{ $d->detailProduct->replacement ?? '-' }})</span>
                                            </td>
                                            <td class="text-center">{{ $d->qty }}</td>
                                            <td class="text-center">Rp {{ $moneyFmt($row['item_value'] ?? 0) }}</td>
                                            <td class="text-center">{{ number_format(($row['share'] ?? 0) * 100, 1) }}%</td>
                                            <td class="text-center">Rp {{ $moneyFmt($row['allocated_cost'] ?? 0) }}</td>
                                            <td class="text-center fw-bold text-primary">
                                                {{ is_null($row['hpp_baru'] ?? null) ? '-' : 'Rp ' . $moneyFmt($row['hpp_baru']) }}
                                            </td>
                                            @if (!$isEditable)
                                                <td class="text-center">
                                                    {{ is_null($d->hpp) ? '-' : 'Rp ' . $moneyFmt($d->hpp) }}
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Tidak ada item.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small">
                            HPP Baru dihitung dari (Nilai Barang + Biaya Teralokasi) ÷ Qty, khusus buat item-item di batch
                            penerimaan ini — <strong>tidak</strong> dirata-ratakan dengan sisa stok lama dari batch sebelumnya.
                        </p>

                        @if ($canManageCosts)
                            <div class="d-flex justify-content-end">
                                <button type="button" id="btn-apply-hpp" class="btn btn-primary"
                                    data-url="{{ route('product-in.apply-hpp', $product->id) }}"
                                    {{ $costAllocation['total_additional_cost'] <= 0 ? 'disabled' : '' }}>
                                    <i class="mdi mdi-check-decagram-outline me-1"></i> Simpan &amp; Terapkan ke HPP
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>{{-- /tab-biaya-tambahan --}}
            </div>{{-- /tab-content --}}
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        @media print {
            body > :not(.gr-detail-print) { display: none !important; }
            .d-print-none { display: none !important; }
            /* Yang diprint selalu dokumen GR (tab Detail Barang), gak peduli tab mana
               yang lagi aktif di browser pas tombol Print diklik. */
            #tab-detail-barang { display: block !important; opacity: 1 !important; }
            #tab-biaya-tambahan { display: none !important; }
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
<script>
$(function () {
    var csrfToken = "{{ csrf_token() }}";

    @if ($isEditable)
    var updateUrl = "{{ route('product-in.logistic-update', $product->id) }}";
    var deleteItemUrl = "{{ url('/product-in/detail') }}";

    // ── Inline Edit: klik span → ganti jadi input/select ──────────────────
    $(document).on('click', '.inline-edit', function (e) {
        if ($(this).find('input, select').length) return;

        var $span   = $(this);
        var type    = $span.data('type');
        var field   = $span.data('field');
        var value   = $span.data('value');
        var options = $span.data('options');
        var $input;

        if (type === 'select-product') {
            $input = $('<select class="form-select form-select-sm">');
            $('#product-options option').each(function () {
                var sel = $(this).val() == value ? 'selected' : '';
                $input.append('<option value="' + $(this).val() + '" ' + sel + '>' + $(this).text() + '</option>');
            });
            $span.html($input);
            $input.select2({ width: '100%', dropdownParent: $span.closest('td') }).trigger('focus');
            return;
        }

        if (type === 'select') {
            $input = $('<select class="form-select form-select-sm">');
            if (field === 'id_supplier') {
                $('#supplier-options option').each(function () {
                    var sel = $(this).val() == value ? 'selected' : '';
                    $input.append('<option value="' + $(this).val() + '" ' + sel + '>' + $(this).text() + '</option>');
                });
            } else {
                $.each(options, function (i, opt) {
                    var sel = opt.value == value ? 'selected' : '';
                    $input.append('<option value="' + opt.value + '" ' + sel + '>' + opt.label + '</option>');
                });
            }
        } else if (type === 'date') {
            $input = $('<input type="date" class="form-control form-control-sm" style="width:150px">').val(value);
        } else if (type === 'number') {
            $input = $('<input type="number" class="form-control form-control-sm text-center" min="1" style="width:75px">').val(value);
        } else {
            $input = $('<input type="text" class="form-control form-control-sm">').val(value ?? $span.text().trim());
        }

        $input.attr('data-field', field);
        $span.html($input);
        $input.trigger('focus');
    });

    // ── Tambah baris baru ──────────────────────────────────────────────────
    var newRowIndex = 0;

    $('#btn-add-row').on('click', function () {
        newRowIndex++;
        var rowNum = $('#items-table tbody tr').length + 1;
        var uid = 'new-' + newRowIndex;

        var productOpts = '';
        $('#product-options option').each(function () {
            productOpts += '<option value="' + $(this).val() + '">' + $(this).text() + '</option>';
        });

        var $row = $(
            '<tr data-new-id="' + uid + '">' +
                '<td class="text-center row-number">' + rowNum + '</td>' +
                '<td>' +
                    '<select class="form-select form-select-sm new-product-select" data-uid="' + uid + '">' +
                        productOpts +
                    '</select>' +
                '</td>' +
                '<td class="text-center">' +
                    '<select class="form-select form-select-sm new-warehouse" style="width:90px; margin:auto;">' +
                        '<option value="BDG">BDG</option>' +
                        '<option value="BKS">BKS</option>' +
                    '</select>' +
                '</td>' +
                '<td class="text-center">' +
                    '<input type="number" class="form-control form-control-sm text-center new-qty" min="1" value="1" style="width:75px; margin:auto;">' +
                '</td>' +
                '<td class="text-center text-muted">-</td>' +
                '<td class="text-center text-muted">-</td>' +
                '<td class="text-center d-print-none">' +
                    '<button type="button" class="btn btn-sm btn-icon btn-outline-danger remove-new-row">' +
                        '<i class="mdi mdi-close"></i>' +
                    '</button>' +
                '</td>' +
            '</tr>'
        );

        $('#items-table tbody').append($row);
        $row.find('.new-product-select').select2({ width: '100%', dropdownParent: $row.find('td:nth-child(2)') });
        renumberRows();
    });

    $(document).on('click', '.remove-new-row', function () {
        $(this).closest('tr').remove();
        renumberRows();
    });

    // ── Hapus item ─────────────────────────────────────────────────────────
    $(document).on('click', '.delete-item', function () {
        var $btn = $(this);
        var itemId = $btn.data('id');
        var $row = $btn.closest('tr');

        if (!confirm('Hapus item ini? Stok akan dikembalikan.')) return;

        $.ajax({
            url: deleteItemUrl + '/' + itemId,
            type: 'POST',
            data: { _token: csrfToken, _method: 'DELETE' },
            success: function (res) {
                $row.remove();
                renumberRows();
                showAlert('success', res.message ?? 'Item berhasil dihapus');
            },
            error: function () {
                showAlert('danger', 'Gagal menghapus item.');
            },
        });
    });

    // ── Simpan semua perubahan ─────────────────────────────────────────────
    $('#btn-save').on('click', function () {
        var $btn = $(this).prop('disabled', true).text('Menyimpan...');

        var payload = {
            _token: csrfToken,
            no_do: getHeaderVal('no_do'),
            date: getHeaderVal('date'),
            id_supplier: getHeaderVal('id_supplier'),
            info: getHeaderVal('info'),
            items: {},
            new_items: [],
        };

        $('#items-table tbody tr[data-new-id]').each(function () {
            var $row = $(this);
            payload.new_items.push({
                id_detail_product: $row.find('.new-product-select').val(),
                qty: $row.find('.new-qty').val(),
                warehouse: $row.find('.new-warehouse').val(),
            });
        });

        $('.item-field').each(function () {
            var $el = $(this);
            var id = $el.data('id');
            var field = $el.data('field');
            var val = $el.find('input, select').length
                ? $el.find('input, select').val()
                : $el.data('value');

            if (!payload.items[id]) payload.items[id] = {};
            payload.items[id][field] = val;
        });

        $.ajax({
            url: updateUrl,
            type: 'POST',
            data: payload,
            success: function (res) {
                showAlert('success', res.message ?? 'Data berhasil disimpan');
                if (payload.new_items.length > 0) {
                    setTimeout(function () { window.location.reload(); }, 800);
                    return;
                }
                $('.inline-edit').each(function () {
                    var $span = $(this);
                    var $input = $span.find('input, select');
                    if (!$input.length) return;
                    var newVal = $input.val();
                    $span.data('value', newVal);
                    if ($input.is('select')) {
                        $span.text($input.find(':selected').text());
                    } else if ($input.attr('type') === 'date') {
                        var d = new Date(newVal);
                        $span.text(
                            String(d.getDate()).padStart(2, '0') + '-' +
                            String(d.getMonth() + 1).padStart(2, '0') + '-' +
                            d.getFullYear()
                        );
                    } else {
                        $span.text(newVal);
                    }
                });
            },
            error: function () {
                showAlert('danger', 'Gagal menyimpan, coba lagi.');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="mdi mdi-content-save-outline me-1"></i> Simpan Perubahan');
            },
        });
    });

    function getHeaderVal(field) {
        var $span = $('.inline-edit[data-field="' + field + '"]');
        var $input = $span.find('input, select');
        return $input.length ? $input.val() : $span.data('value');
    }

    function renumberRows() {
        $('#items-table tbody tr').each(function (i) {
            $(this).find('.row-number').text(i + 1);
        });
    }

    function showAlert(type, msg) {
        var $box = $('#alert-box');
        $box.removeClass('d-none alert-success alert-danger')
            .addClass('alert-' + type).text(msg);
        setTimeout(function () { $box.addClass('d-none'); }, 3500);
    }
    @endif

    // ── Clear Return ─────────────────────────────────────────────────────
    $(document).on('click', '.clear-return', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Accept it!",
            customClass: {
                confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                cancelButton: "btn btn-label-secondary waves-effect",
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: '{{ url('product-in') }}/clear-return/' + id,
                    type: 'POST',
                    data: { _method: 'POST', _token: csrfToken },
                    success: function (response) {
                        if (response == 1) {
                            Swal.fire({
                                icon: "success",
                                title: "Accepted!",
                                text: "Your file has been Accepted.",
                                customClass: { confirmButton: "btn btn-success waves-effect" },
                            });
                            setTimeout(function () { window.location.reload(); }, 2000);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Data Failed to Accept!' });
                        }
                    }
                });
            }
        });
    });

    // ── Hapus seluruh data product in ───────────────────────────────────
    $('#btn-delete-all').on('click', function () {
        var productId = $(this).data('id');
        Swal.fire({
            title: 'Hapus data ini?',
            text: 'Seluruh data product in beserta itemnya akan dihapus dan tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '{{ url("product-in") }}/' + productId,
                type: 'POST',
                data: { _token: csrfToken, _method: 'DELETE' },
                success: function (res) {
                    if (res == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus!',
                            text: 'Data product in berhasil dihapus.',
                            customClass: { confirmButton: 'btn btn-success waves-effect' },
                            buttonsStyling: false,
                        }).then(function () {
                            window.location.href = '{{ url("product-in") }}';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Data gagal dihapus.' });
                    }
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan, coba lagi.' });
                },
            });
        });
    });

    // ── Hapus 1 baris Biaya Tambahan (konfirmasi dulu) ──────────────────
    $(document).on('submit', '.form-delete-cost', function (e) {
        e.preventDefault();
        var $form = $(this);
        Swal.fire({
            title: 'Hapus biaya ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (result.isConfirmed) $form.off('submit').trigger('submit');
        });
    });

    // ── Simpan & Terapkan ke HPP ─────────────────────────────────────────
    $('#btn-apply-hpp').on('click', function () {
        var url = $(this).data('url');
        Swal.fire({
            title: 'Terapkan HPP dari Biaya Tambahan?',
            text: 'HPP tiap item bakal ditimpa sesuai hasil alokasi — HPP lama untuk batch ini bakal hilang, dan TIDAK dirata-ratakan dengan sisa stok lama.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, terapkan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-label-secondary waves-effect',
            },
            buttonsStyling: false,
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: url,
                type: 'POST',
                data: { _token: csrfToken },
                success: function () {
                    window.location.reload();
                },
                error: function () {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menerapkan HPP, coba lagi.' });
                },
            });
        });
    });
});
</script>
@endpush
