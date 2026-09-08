@extends('layouts.sales.app')
@section('title', 'Goods Receipt Aksesoris Rental')
@section('content')
    <style>
        .goods-receipt-page {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .goods-receipt-page .table,
        .goods-receipt-page .table th,
        .goods-receipt-page .table td,
        .goods-receipt-page .card-title {
            font-family: inherit;
        }
        .goods-receipt-page .card,
        .goods-receipt-page .modern-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.06), 0 0 1px 0 rgba(67, 89, 113, 0.12);
            border-radius: 0.75rem !important;
        }
    </style>

    <div class="container-fluid flex-grow-1 container-p-y p-0 goods-receipt-page">
        {{-- Header Page Title --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Goods Receipt (GR) Aksesoris Rental</h4>
                <p class="text-muted mb-0 small">Verifikasi penerimaan fisik kelengkapan aksesoris rental dari supplier untuk otomatis memperbarui stok gudang.</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-label-secondary fs-6 px-3 py-2">
                    <i class="mdi mdi-file-document-outline me-1"></i>PO: {{ $purchase->no_po }}
                </span>
                <span class="badge bg-label-info fs-6 px-3 py-2" data-bs-toggle="tooltip"
                    title="Nomor ini baru dikunci setelah penerimaan disimpan">
                    <i class="mdi mdi-send-outline me-1"></i>No. GR: {{ $previewNoGr }}
                    <span class="fst-italic">(preview)</span>
                </span>
                <a href="{{ route('purchase.show', $purchase->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Purchase Order
                </a>
            </div>
        </div>

        {{-- Form action --}}
        <form action="{{ route('rental-accessories.store-goods-receipt', $purchase->id) }}" method="POST">
            @csrf

            {{-- Card Info Penerimaan --}}
            <div class="card modern-card mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-truck-delivery-outline me-2 text-primary fs-4"></i> Info Penerimaan
                    </h5>
                    <span class="text-muted small">
                        <i class="mdi mdi-account-check-outline me-1"></i>Diverifikasi oleh <strong>{{ Auth::user()->name }}</strong>
                    </span>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input type="date" class="form-control" id="gr_date" name="gr_date" required
                                    value="{{ old('gr_date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                                <label for="gr_date">Tanggal Penerimaan</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" value="{{ $purchase->company ?? $purchase->supplier->supplier ?? '-' }}" readonly disabled>
                                <label>Supplier</label>
                                <small class="text-muted d-block mt-1"><i class="mdi mdi-information-outline me-1"></i>Otomatis terisi dari Purchase Order {{ $purchase->no_po }}.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Checklist Item --}}
            <div class="card modern-card mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-checkbox-marked-circle-outline me-2 text-primary fs-4"></i> Checklist Item Aksesoris yang Diterima
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th style="min-width: 220px;">Item & Kategori Aksesoris</th>
                                    <th style="min-width: 140px;">Kode / Barcode</th>
                                    <th style="width: 120px;" class="text-center">Qty Order (PO)</th>
                                    <th style="width: 140px;" class="text-center">Qty Diterima</th>
                                    <th style="width: 150px;">Kondisi Fisik</th>
                                    <th style="min-width: 160px;">Lokasi Rak Gudang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($detail as $index => $item)
                                    @php
                                        $acc = $item->rentalAccessory;
                                        $defaultCat = $acc ? $acc->category : 'hose';
                                        $itemName = $item->product ?: ($acc ? $acc->name : 'Aksesoris');
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-semibold">
                                            {{ $index + 1 }}
                                            <input type="hidden" name="detail_id[{{ $index }}]" value="{{ $item->id }}">
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $itemName }}</div>
                                            <div class="mt-1">
                                                <select class="form-select form-select-sm" name="category[{{ $index }}]" required>
                                                    <option value="hose" {{ $defaultCat == 'hose' ? 'selected' : '' }}>Flexible Hose</option>
                                                    <option value="header" {{ $defaultCat == 'header' ? 'selected' : '' }}>Header</option>
                                                    <option value="reducer" {{ $defaultCat == 'reducer' ? 'selected' : '' }}>Reducer</option>
                                                    <option value="cable" {{ $defaultCat == 'cable' ? 'selected' : '' }}>Kabel Power</option>
                                                    <option value="nipple" {{ $defaultCat == 'nipple' ? 'selected' : '' }}>Double Nipple</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="code[{{ $index }}]" 
                                                value="{{ old("code.$index", $acc->code ?? '') }}" placeholder="Kode item/barcode">
                                        </td>
                                        <td class="text-center fw-semibold">
                                            <span class="badge bg-label-secondary fs-6">{{ $item->qty }} {{ $item->info_qty ?: 'Pcs' }}</span>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm text-center fw-semibold" 
                                                name="qty_received[{{ $index }}]" min="0" max="{{ $item->qty * 2 }}" 
                                                value="{{ old("qty_received.$index", $item->qty) }}" required>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="condition[{{ $index }}]">
                                                <option value="ok" selected>Bagus / OK</option>
                                                <option value="fair">Cukup</option>
                                                <option value="damaged">Rusak</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="location[{{ $index }}]" 
                                                value="{{ old("location.$index", $acc->location ?? '') }}" placeholder="Contoh: Rak B-02">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Tidak ada item aksesoris dalam Purchase Order ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="{{ route('purchase.show', $purchase->id) }}" class="btn btn-label-secondary">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary" {{ $detail->isEmpty() ? 'disabled' : '' }}>
                        <i class="mdi mdi-check-circle-outline me-1"></i> Konfirmasi & Simpan Goods Receipt
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
