@extends('layouts.sales.app')
@section('title', 'Goods Receipt Verification')
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
                <p class="text-muted mb-0 small">Verifikasi kesesuaian fisik barang yang diterima dari supplier</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-label-primary fs-6 px-3 py-2">
                    <i class="mdi mdi-receipt-text-outline me-1"></i>SO: {{ $pending->no_pending }}
                </span>
                <span class="badge bg-label-secondary fs-6 px-3 py-2">
                    <i class="mdi mdi-file-document-outline me-1"></i>PO: {{ $po->no_po }}
                </span>
                <span class="badge {{ $po->no_gr ? 'bg-label-info' : 'bg-label-secondary' }} fs-6 px-3 py-2"
                    @if (!$po->no_gr) data-bs-toggle="tooltip" title="Nomor ini baru dikunci setelah verifikasi disimpan" @endif>
                    <i class="mdi mdi-send-outline me-1"></i>GR: {{ $previewNoGr }}
                    @if (!$po->no_gr)
                        <span class="fst-italic">(preview)</span>
                    @endif
                </span>
                <a href="{{ route('purchase-request.show', $pending->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Purchase Request
                </a>
            </div>
        </div>

        {{-- Form action --}}
        <form action="{{ route('purchase.store-goods-receipt', $po->id) }}" method="POST">
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
                                <input type="text" class="form-control" id="no_do" name="no_do" placeholder="Nomor DO dari Supplier" required value="{{ old('no_do', \Carbon\Carbon::now('Asia/Jakarta')->format('d/m/Y')) }}">
                                <label for="no_do">No. Delivery Order (DO) Supplier</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input type="date" class="form-control" id="gr_date" name="gr_date" required value="{{ old('gr_date', \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d')) }}">
                                <label for="gr_date">Tanggal Penerimaan</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <select id="supplier" name="supplier" class="form-select" required>
                                    <option value="">Pilih Supplier...</option>
                                    @foreach ($suppliers as $supp)
                                        <option value="{{ $supp->id }}" {{ old('supplier', $po->id_supplier) == $supp->id ? 'selected' : '' }}>
                                            {{ $supp->supplier }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="supplier">Supplier</label>
                                <small class="text-muted d-block mt-1"><i class="mdi mdi-information-outline me-1"></i>Otomatis terisi dari Purchase Order {{ $po->no_po }}, ubah bila perlu.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Checklist Item --}}
            <div class="card modern-card mb-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-checkbox-marked-circle-outline me-2 text-primary fs-4"></i> Checklist Kesesuaian Item PR
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Item PR</th>
                                    <th style="width: 100px;">Qty Order</th>
                                    <th style="width: 250px;">Verifikasi Status</th>
                                    <th style="width: 130px;">Qty Diterima</th>
                                    <th style="width: 130px;">Qty Rusak (Return)</th>
                                    <th style="width: 200px;">Replacement</th>
                                    <th style="width: 100px;">Warehouse</th>
                                    <th>Catatan Masalah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allocations as $key => $alloc)
                                    <input type="hidden" name="alloc_id[{{ $key }}]" value="{{ $alloc->id }}">
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            @php
                                                $goLabels = [
                                                    'Replacement' => 'bg-label-warning',
                                                    'Genuine'     => 'bg-label-success',
                                                    'OEM'         => 'bg-label-info',
                                                ];
                                                $goVal = $alloc->detail->equivalent->product->go ?? null;
                                            @endphp
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <span class="fw-semibold text-dark">{{ $alloc->detail->equivalent->brand }} {{ $alloc->detail->equivalent->pn }}</span>
                                                @if ($goVal && isset($goLabels[$goVal]))
                                                    <span class="badge {{ $goLabels[$goVal] }}" style="font-size: 9px;">{{ $goVal }}</span>
                                                @endif
                                            </div>
                                            <small class="text-muted d-block" style="font-size: 11px;">PR No: {{ $alloc->detail->header->no_pr ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark fs-6" id="qty-order-{{ $key }}">{{ $alloc->po_qty }}</span>
                                            <span class="text-muted small d-block">{{ $alloc->detail->equivalent->product->unit ?? 'pcs' }}</span>
                                            @if ($alloc->po_qty > $alloc->qty)
                                                <span class="badge bg-label-info mt-1" data-bs-toggle="tooltip"
                                                    title="{{ $alloc->qty }} pcs utk PR ini, +{{ $alloc->po_qty - $alloc->qty }} pcs tambahan stok">
                                                    +{{ $alloc->po_qty - $alloc->qty }} stok
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm w-100" role="group">
                                                <input type="radio" class="btn-check gr-status-radio"
                                                    name="gr_status[{{ $key }}]"
                                                    id="status-ok-{{ $key }}"
                                                    value="Sesuai"
                                                    checked
                                                    data-key="{{ $key }}"
                                                    data-qty="{{ $alloc->po_qty }}">
                                                <label class="btn btn-outline-success" for="status-ok-{{ $key }}">
                                                    <i class="mdi mdi-check-circle-outline me-1"></i> Sesuai
                                                </label>

                                                <input type="radio" class="btn-check gr-status-radio"
                                                    name="gr_status[{{ $key }}]"
                                                    id="status-diff-{{ $key }}"
                                                    value="Tidak Sesuai"
                                                    data-key="{{ $key }}"
                                                    data-qty="{{ $alloc->po_qty }}">
                                                <label class="btn btn-outline-warning" for="status-diff-{{ $key }}">
                                                    <i class="mdi mdi-alert-circle-outline me-1"></i> Selisih
                                                </label>

                                                <input type="radio" class="btn-check gr-status-radio"
                                                    name="gr_status[{{ $key }}]"
                                                    id="status-rusak-{{ $key }}"
                                                    value="Rusak"
                                                    data-key="{{ $key }}"
                                                    data-qty="{{ $alloc->po_qty }}">
                                                <label class="btn btn-outline-danger" for="status-rusak-{{ $key }}">
                                                    <i class="mdi mdi-package-variant-closed-remove me-1"></i> Rusak
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number"
                                                class="form-control form-control-sm qty-received-input text-center fw-bold"
                                                name="qty_received[{{ $key }}]"
                                                value="{{ $alloc->po_qty }}"
                                                min="0"
                                                id="qty-rec-{{ $key }}"
                                                readonly
                                                style="max-width: 100px; background-color: #f1f5f9; border-width: 2px;">
                                        </td>
                                        <td>
                                            <input type="number"
                                                class="form-control form-control-sm qty-damaged-input text-center fw-bold"
                                                name="qty_damaged[{{ $key }}]"
                                                value="0"
                                                min="0"
                                                max="{{ $alloc->po_qty }}"
                                                id="qty-damaged-{{ $key }}"
                                                data-key="{{ $key }}"
                                                readonly
                                                style="max-width: 100px; background-color: #f1f5f9; border-width: 2px;">
                                            <small class="text-muted d-block">dari qty diterima</small>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="replacement[{{ $key }}]" required style="min-width: 200px;">
                                                <option value="">-- Pilih Replacement --</option>
                                                @foreach ($fullRep[$key] as $products)
                                                    <option value="{{ $products->id }}" {{ count($fullRep[$key]) == 1 ? 'selected' : '' }}>
                                                        {{ $products->product->commodity }} ({{ $products->replacement }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="warehouse[{{ $key }}]">
                                                <option value="BDG" selected>BDG</option>
                                                <option value="BKS">BKS</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text"
                                                class="form-control form-control-sm gr-note-input"
                                                name="gr_note[{{ $key }}]"
                                                placeholder="Catatan selisih (jika ada)"
                                                id="note-{{ $key }}"
                                                readonly
                                                style="background-color: #f1f5f9;">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white border-top p-3 d-flex justify-content-end gap-2">
                    <a href="{{ route('purchase-request.show', $pending->id) }}" class="btn btn-outline-secondary">
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
            // Handle radio toggle change
            $('.gr-status-radio').on('change', function() {
                var key = $(this).data('key');
                var val = $(this).val();
                var orderedQty = $(this).data('qty');

                var $qtyInput = $('#qty-rec-' + key);
                var $damagedInput = $('#qty-damaged-' + key);
                var $noteInput = $('#note-' + key);

                if (val === 'Sesuai') {
                    // Set input back to ordered quantity, lock it
                    $qtyInput.val(orderedQty);
                    $qtyInput.prop('readonly', true);
                    $qtyInput.css('background-color', '#f1f5f9');
                    $qtyInput.css('border-color', '#d9ade8'); // soft border

                    // Tidak ada yang rusak kalau statusnya Sesuai
                    $damagedInput.val(0);
                    $damagedInput.prop('readonly', true);
                    $damagedInput.css('background-color', '#f1f5f9');

                    // Clear and lock note input
                    $noteInput.val('');
                    $noteInput.prop('readonly', true);
                    $noteInput.css('background-color', '#f1f5f9');
                } else if (val === 'Tidak Sesuai') {
                    // Selisih = kurang kirim, bukan rusak — qty diterima diedit manual,
                    // qty rusak tetap terkunci di 0.
                    $qtyInput.prop('readonly', false);
                    $qtyInput.css('background-color', '#ffffff');
                    $qtyInput.css('border-color', '#ff9f43'); // warning border
                    $qtyInput.focus();

                    $damagedInput.val(0);
                    $damagedInput.prop('readonly', true);
                    $damagedInput.css('background-color', '#f1f5f9');

                    $noteInput.prop('readonly', false);
                    $noteInput.css('background-color', '#ffffff');
                    $noteInput.attr('placeholder', 'Masukkan alasan selisih (wajib)...');
                } else if (val === 'Rusak') {
                    // Rusak = barang datang tapi ada yang cacat, perlu diretur ke supplier.
                    // Qty rusak yang diisi manual, Qty Diterima (yang masuk stok) otomatis
                    // ke-hitung: qty order dikurangi qty rusak.
                    $qtyInput.prop('readonly', true);
                    $qtyInput.css('background-color', '#f1f5f9');
                    $qtyInput.css('border-color', '#ff4d49'); // danger border

                    $damagedInput.prop('readonly', false);
                    $damagedInput.attr('max', orderedQty);
                    $damagedInput.val(0);
                    $damagedInput.css('background-color', '#ffffff');
                    $damagedInput.css('border-color', '#ff4d49');
                    $damagedInput.focus();

                    $qtyInput.val(orderedQty); // belum ada rusak yang diisi = qty diterima penuh

                    $noteInput.prop('readonly', false);
                    $noteInput.css('background-color', '#ffffff');
                    $noteInput.attr('placeholder', 'Masukkan deskripsi kerusakan (wajib)...');
                }
            });

            // Status Rusak: Qty Diterima = Qty Order - Qty Rusak, ke-update otomatis
            // tiap kali Qty Rusak diisi/diubah.
            $(document).on('input', '.qty-damaged-input', function () {
                var key = $(this).data('key');
                var status = $('input[name="gr_status[' + key + ']"]:checked').val();
                if (status !== 'Rusak') return;

                var orderedQty = parseInt($(this).attr('max'), 10) || 0;
                var damaged = parseInt($(this).val(), 10) || 0;
                if (damaged > orderedQty) {
                    damaged = orderedQty;
                    $(this).val(damaged);
                }
                $('#qty-rec-' + key).val(orderedQty - damaged);
            });

            // Kalau qty diterima diubah manual (kasus Selisih/kurang kirim), turunkan
            // juga batas atas qty rusak biar nggak lebih besar dari qty diterima.
            $(document).on('input', '.qty-received-input', function () {
                var key = $(this).attr('id').replace('qty-rec-', '');
                var status = $('input[name="gr_status[' + key + ']"]:checked').val();
                if (status === 'Rusak') return; // di mode Rusak, arah hitungnya kebalik (lihat handler di atas)

                var $damagedInput = $('#qty-damaged-' + key);
                var maxQty = parseInt($(this).val(), 10) || 0;
                $damagedInput.attr('max', maxQty);
                if ((parseInt($damagedInput.val(), 10) || 0) > maxQty) {
                    $damagedInput.val(maxQty);
                }
            });
        });
    </script>
@endpush
