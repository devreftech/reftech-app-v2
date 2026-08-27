@extends('layouts.sales.app')
@section('title', 'Goods Receipt Unit')
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
                <h4 class="fw-bold mb-1 text-dark">Goods Receipt (GR) Verification</h4>
                <p class="text-muted mb-0 small">Verifikasi penerimaan fisik unit dari supplier — diterima utuh sekaligus sesuai qty PO.</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-label-secondary fs-6 px-3 py-2">
                    <i class="mdi mdi-file-document-outline me-1"></i>PO: {{ $purchase->no_po }}
                </span>
                <span class="badge bg-label-info fs-6 px-3 py-2" data-bs-toggle="tooltip"
                    title="Nomor ini baru dikunci setelah penerimaan disimpan">
                    <i class="mdi mdi-send-outline me-1"></i>No. Transaksi: {{ $previewNoTransaksi }}
                    <span class="fst-italic">(preview)</span>
                </span>
                <a href="{{ route('purchase.show', $purchase->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Purchase Order
                </a>
            </div>
        </div>

        {{-- Form action --}}
        <form action="{{ route('unit-product-in.store-goods-receipt', $purchase->id) }}" method="POST">
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
                                <input type="date" class="form-control" id="date" name="date" required
                                    value="{{ old('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                                <label for="date">Tanggal Terima</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" value="{{ $purchase->company }}" readonly disabled>
                                <label>Supplier</label>
                                <small class="text-muted d-block mt-1"><i class="mdi mdi-information-outline me-1"></i>Otomatis terisi dari Purchase Order {{ $purchase->no_po }}.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Checklist Item --}}
            <div class="card modern-card mb-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-checkbox-marked-circle-outline me-2 text-primary fs-4"></i> Checklist Kesesuaian Item PO
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Item PO</th>
                                    <th style="width: 100px;">Qty PO</th>
                                    <th style="width: 160px;">Harga</th>
                                    <th style="width: 150px;">Kondisi</th>
                                    <th>Serial Number Diterima</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($detail as $key => $item)
                                    @php
                                        $u = $item->unit;
                                        $isCompressor = $u && in_array($u->unit, ['PISTON COMPRESSOR', 'AIR COMPRESSOR SCREW']);
                                        $isDryer = $u && in_array($u->unit, ['REFRIGERANT AIR DRYER', 'DESICANT DRYER']);
                                    @endphp
                                    <input type="hidden" name="detail_id[{{ $key }}]" value="{{ $item->id }}">
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $item->product }}</span>
                                            <small class="text-muted d-block" style="font-size: 11px;">PO No: {{ $purchase->no_po }}</small>
                                            @if ($u)
                                                <a href="#spec-{{ $key }}" class="small item-spec-toggle" data-bs-toggle="collapse"
                                                    role="button" aria-expanded="false" aria-controls="spec-{{ $key }}">
                                                    <i class="mdi mdi-chevron-right item-spec-icon me-1"></i>Lihat Spesifikasi
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark fs-6">{{ $item->qty }}</span>
                                            <span class="text-muted small d-block">{{ $item->info_qty ?: 'unit' }}</span>
                                        </td>
                                        <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($item->kondisi === 'Second')
                                                <span class="badge bg-label-warning">Second</span>
                                                <small class="text-muted d-block" style="font-size: 10px;">Jadi Fixed Asset, QC dulu</small>
                                            @else
                                                <span class="badge bg-label-success">Baru</span>
                                                <small class="text-muted d-block" style="font-size: 10px;">Masuk stok jual</small>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm"
                                                name="serial_number[{{ $key }}]"
                                                placeholder="Serial number unit fisik" required>
                                        </td>
                                    </tr>
                                    @if ($u)
                                        <tr class="collapse" id="spec-{{ $key }}">
                                            <td colspan="6" class="bg-light-subtle p-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        @include('components.detail-row', ['label' => 'Category', 'value' => $u->unit])
                                                        @include('components.detail-row', ['label' => 'SKU', 'value' => $u->sku])
                                                        @include('components.detail-row', ['label' => 'Brand', 'value' => $u->brand])
                                                        @include('components.detail-row', ['label' => 'Model', 'value' => $u->model])
                                                        @include('components.detail-row', ['label' => 'Generation', 'value' => $u->generation])

                                                        @if ($isCompressor)
                                                            @include('components.detail-row', ['label' => 'Type', 'value' => $u->type_unit])
                                                            @include('components.detail-row', ['label' => 'Motor Power', 'value' => $u->power])
                                                            @include('components.detail-row', ['label' => 'Air Capacity', 'value' => $u->air_cap ? $u->air_cap . ' m³/min' : null])
                                                            @include('components.detail-row', ['label' => 'Max. Pressure', 'value' => $u->bar ? $u->bar . ' Bar' : null])
                                                            @include('components.detail-row', ['label' => 'Voltage', 'value' => $u->voltage])
                                                            @include('components.detail-row', ['label' => 'Drive', 'value' => $u->connect])
                                                            @include('components.detail-row', ['label' => 'Cooling', 'value' => $u->cooling])
                                                            @include('components.detail-row', ['label' => 'Discharge', 'value' => $u->exhaust])
                                                        @elseif ($isDryer)
                                                            @include('components.detail-row', ['label' => 'Air Capacity', 'value' => $u->air_cap ? $u->air_cap . ' m³/min' : null])
                                                            @include('components.detail-row', ['label' => 'Refrigerant Type', 'value' => $u->refrigerant_type])
                                                            @include('components.detail-row', ['label' => 'PDP', 'value' => $u->pdp])
                                                        @endif
                                                    </div>
                                                    <div class="col-md-6">
                                                        @include('components.detail-row', ['label' => 'Filtration', 'value' => $u->filtration])
                                                        @include('components.detail-row', ['label' => 'Oil Content', 'value' => $u->oil_content])
                                                        @include('components.detail-row', ['label' => 'Grade', 'value' => $u->grade])
                                                        @include('components.detail-row', ['label' => 'Capacity', 'value' => $u->capacity])
                                                        @include('components.detail-row', ['label' => 'Material', 'value' => $u->material])
                                                        @include('components.detail-row', ['label' => 'Test Pressure', 'value' => $u->test_pressure])
                                                        @include('components.detail-row', ['label' => 'Inlet Pressure', 'value' => $u->inlet_pressure])
                                                        @include('components.detail-row', ['label' => 'Outlet Pressure', 'value' => $u->outlet_pressure])
                                                        @include('components.detail-row', ['label' => 'Inlet Capacity', 'value' => $u->inlet_cap])
                                                        @include('components.detail-row', ['label' => 'Outlet Capacity', 'value' => $u->outlet_cap])
                                                        @include('components.detail-row', ['label' => 'Dimension', 'value' => $u->dimension])
                                                        @include('components.detail-row', ['label' => 'Weight', 'value' => $u->weight ? $u->weight . ' Kg' : null])
                                                        @if (!$isCompressor && !$isDryer)
                                                            @include('components.detail-row', ['label' => 'Description', 'value' => $u->desc])
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white border-top p-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('unit-product-in.index') }}" class="btn btn-outline-secondary">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i> Simpan Verifikasi Goods Receipt
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('page-script')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            $(document).on('show.bs.collapse hide.bs.collapse', 'tr.collapse', function(e) {
                var shown = e.type === 'show';
                $('.item-spec-toggle[aria-controls="' + this.id + '"] .item-spec-icon')
                    .toggleClass('mdi-chevron-down', shown)
                    .toggleClass('mdi-chevron-right', !shown);
            });
        });
    </script>
@endpush
