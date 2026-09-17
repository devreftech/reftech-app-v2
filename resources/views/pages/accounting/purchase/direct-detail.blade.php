@extends('layouts.sales.app')
@section('title', 'Detail Direct Purchase - ' . $purchase->no_po)
@section('content')
    <style>
        .dp-page {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .dp-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            background: #ffffff;
            transition: all 0.2s ease;
        }
        .dp-header-badge {
            background: #ccfbf1;
            color: #0f766e;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
        }
        .dp-accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #0d9488 0%, #2dd4bf 60%, #e2e8f0 100%);
            border-radius: 4px;
        }
        .meta-box {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 10px;
            padding: 14px 16px;
        }
        .table-dp-items thead th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            border-bottom: 2px solid #cbd5e1;
        }
        .table-dp-items tbody td {
            padding: 14px;
            vertical-align: middle;
            font-size: 13.5px;
        }
        .total-highlight-row td {
            background-color: #f0fdfa !important;
            border-top: 2px solid #0d9488 !important;
        }
    </style>

    <div class="container-fluid flex-grow-1 container-p-y p-0 dp-page">
        {{-- Breadcrumb & Top Bar --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 font-12">
                        <li class="breadcrumb-item"><a href="{{ route('purchase.index') }}" class="text-muted">Pembelian</a></li>
                        <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Direct Purchase #{{ $purchase->no_po }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-bold mb-0 text-dark">Bukti Pembelian Langsung (Direct Purchase)</h4>
                    <span class="dp-header-badge">
                        <i class="mdi mdi-cart-arrow-down me-1"></i> DIRECT PURCHASE
                    </span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('purchase.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Rekap
                </a>
                <a href="{{ route('purchase.show_print', $purchase->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="mdi mdi-printer-outline me-1"></i> Cetak / PDF
                </a>
                @php
                    $hasDeliveryInfo = !empty($purchase->on_delivery_cargo) || !empty($purchase->delivery);
                    $isReceived = $purchase->receipt_status === 'Received';
                @endphp
                @if (Auth::user()->role == 'Logistic' || (Auth::user()->isDeveloper() ?? false))
                    @if ($isReceived)
                        <span class="badge bg-label-success px-3 py-2 fs-6">
                            <i class="mdi mdi-check-circle me-1"></i> Barang Sudah Diterima
                        </span>
                    @elseif ($hasDeliveryInfo)
                        <a href="{{ route('purchase.goods-receipt', $purchase->id) }}" class="btn btn-primary btn-sm shadow-xs" style="background-color: #0d9488; border-color: #0d9488;">
                            <i class="mdi mdi-package-variant-closed-check me-1"></i> Verifikasi Penerimaan (GR)
                        </a>
                    @endif
                @endif
            </div>
        </div>

        <div class="row g-4 mb-4">
            {{-- Main Document Card --}}
            <div class="col-xl-8 col-lg-7 col-12">
                <div class="card dp-card h-100">
                    <div class="card-body p-4">
                        {{-- Top Document Header --}}
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 pb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-lg">
                                    <span class="avatar-initial rounded-3" style="background-color: #ccfbf1; color: #0d9488;">
                                        <i class="mdi mdi-storefront-outline font-28"></i>
                                    </span>
                                </div>
                                <div>
                                    <span class="text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Toko / Tempat Pembelian</span>
                                    <h5 class="fw-bold text-dark mb-0 font-18">{{ $purchase->company ?: 'Pembelian Langsung' }}</h5>
                                    @if ($purchase->supplier)
                                        <span class="badge bg-label-secondary font-11 mt-1">Supplier Terdaftar: {{ $purchase->supplier->supplier }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-md-end">
                                <span class="text-muted small text-uppercase fw-bold">Nomor Transaksi DP</span>
                                <h5 class="fw-bold text-dark mb-1 font-monospace" style="color: #0d9488 !important;">#{{ $purchase->no_po }}</h5>
                                <div class="text-muted small">
                                    <i class="mdi mdi-calendar-blank-outline me-1"></i>{{ \Carbon\Carbon::parse($purchase->date)->format('d F Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="dp-accent-bar my-2"></div>

                        {{-- Metadata Row --}}
                        <div class="row g-3 my-3">
                            <div class="col-md-6 col-12">
                                <div class="meta-box h-100">
                                    <div class="text-muted font-11 text-uppercase fw-bold mb-2">
                                        <i class="mdi mdi-information-outline me-1 text-primary"></i> Detail Transaksi Toko
                                    </div>
                                    <table class="table table-sm table-borderless mb-0 font-12">
                                        <tr>
                                            <td class="text-muted ps-0" style="width: 130px;">No. Nota / Pesanan:</td>
                                            <td class="fw-bold text-dark">{{ $purchase->no_reference ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Metode Bayar:</td>
                                            <td>
                                                <span class="badge bg-label-primary font-11">{{ $purchase->payment ?: 'Cash' }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-0">Kategori:</td>
                                            <td class="fw-medium text-dark">{{ $purchase->category ?: 'Sparepart' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="meta-box h-100">
                                    <div class="text-muted font-11 text-uppercase fw-bold mb-2">
                                        <i class="mdi mdi-file-document-outline me-1 text-primary"></i> Dokumen PR / SO Terkait
                                    </div>
                                    @if ($linkedPrs && $linkedPrs->count())
                                        <div class="d-flex flex-wrap gap-1 mb-1">
                                            @foreach ($linkedPrs as $pr)
                                                <a href="{{ route('purchase-request.show', $pr->id_pending) }}" class="badge bg-label-info font-12 text-decoration-none d-inline-flex align-items-center gap-1 py-1 px-2" title="Buka Detail PR {{ $pr->no_pr }}">
                                                    <i class="mdi mdi-file-document-outline"></i>
                                                    <span>{{ $pr->no_pr }}</span>
                                                    <i class="mdi mdi-open-in-new font-10"></i>
                                                </a>
                                            @endforeach
                                        </div>
                                        <small class="text-muted font-11 d-block">Barang dibeli untuk memenuhi PR di atas</small>
                                    @elseif ($purchase->id_purchase_request)
                                        <a href="{{ route('purchase-request.show', $purchase->id_purchase_request) }}" class="badge bg-label-info font-12 text-decoration-none">
                                            <i class="mdi mdi-file-document-outline me-1"></i>PR ID: #{{ $purchase->id_purchase_request }}
                                        </a>
                                    @else
                                        <span class="text-muted fst-italic font-12">Pembelian Langsung Mandiri (Tanpa PR)</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Catatan Pembelian --}}
                        @if (!empty($purchase->note) && trim($purchase->note) !== '-' && trim($purchase->note) !== '')
                            <div class="alert alert-light border d-flex align-items-start gap-2 mb-3 py-2 px-3 rounded-3" style="background-color: #f8fafc;">
                                <i class="mdi mdi-comment-text-outline text-muted fs-5 mt-0 flex-shrink-0"></i>
                                <div>
                                    <div class="font-11 text-muted text-uppercase fw-bold">Catatan Pembelian:</div>
                                    <div class="font-12 text-dark">{{ $purchase->note }}</div>
                                </div>
                            </div>
                        @endif

                        {{-- Items Table --}}
                        <div class="table-responsive border rounded-3 mt-4">
                            <table class="table table-hover table-dp-items mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 45px;" class="text-center">No</th>
                                        <th>Nama Barang / Deskripsi</th>
                                        <th style="width: 100px;" class="text-center">Qty</th>
                                        <th style="width: 80px;" class="text-center">Satuan</th>
                                        <th style="width: 150px;" class="text-end">Harga Satuan</th>
                                        <th style="width: 160px;" class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $subtotalCalc = 0; @endphp
                                    @forelse ($dPurchase as $idx => $item)
                                        @php
                                            $itemQty = (float) $item->qty;
                                            $itemPrice = (float) $item->price;
                                            $itemAmount = $itemQty * $itemPrice;
                                            $subtotalCalc += $itemAmount;
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-semibold text-muted">{{ $idx + 1 }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $item->product }}</div>
                                                @if (!empty($item->detailProduct) && !empty($item->detailProduct->product))
                                                    <div class="small text-muted">
                                                        <i class="mdi mdi-tag-outline me-1"></i>{{ $item->detailProduct->product->part_number ?? $item->detailProduct->product->name }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center fw-bold text-dark">{{ $itemQty }}</td>
                                            <td class="text-center text-muted">{{ $item->unit ?: 'Pcs' }}</td>
                                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($itemPrice, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold text-dark">Rp {{ number_format($itemAmount, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Tidak ada rincian barang.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="5" class="text-end fw-semibold text-dark">Subtotal Barang:</td>
                                        <td class="text-end fw-bold text-dark">Rp {{ number_format($purchase->subtotal ?: $subtotalCalc, 0, ',', '.') }}</td>
                                    </tr>
                                    @if ($purchase->diskon > 0)
                                        <tr class="bg-light">
                                            <td colspan="5" class="text-end fw-semibold text-danger">Potongan Diskon:</td>
                                            <td class="text-end fw-bold text-danger">- Rp {{ number_format($purchase->diskon, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                    <tr class="bg-light">
                                        <td colspan="5" class="text-end fw-semibold text-dark">Ongkos Kirim (Delivery):</td>
                                        <td class="text-end fw-bold text-dark">Rp {{ number_format($purchase->delivery_cost ?: 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="total-highlight-row">
                                        <td colspan="5" class="text-end fw-bold fs-6" style="color: #0f766e;">TOTAL PEMBELIAN:</td>
                                        <td class="text-end fw-bold fs-6" style="color: #0f766e;">Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Column: Delivery Card & Actions --}}
            <div class="col-xl-4 col-lg-5 col-12">
                {{-- Delivery Status Card --}}
                <div class="card dp-card mb-4">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="card-title m-0 fw-bold text-dark d-flex align-items-center font-14">
                            <i class="mdi mdi-truck-fast-outline me-2" style="color: #0d9488;"></i> Info Pengiriman &amp; Resi
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalUpdateDelivery" style="color: #0d9488; border-color: #0d9488;">
                            <i class="mdi mdi-pencil-outline"></i> Edit
                        </button>
                    </div>
                    <div class="card-body p-4">
                        @php
                            $cargo = $purchase->on_delivery_cargo ?: ($purchase->delivery ?: null);
                            $noResi = $purchase->on_delivery_no_resi ?: null;
                        @endphp

                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <span class="text-muted font-12">Status Logistik:</span>
                            @if ($isReceived)
                                <span class="badge bg-label-success py-1 px-2 font-11">
                                    <i class="mdi mdi-check-all me-1"></i> Diterima Gudang
                                </span>
                            @elseif ($cargo || $noResi)
                                <span class="badge bg-label-info py-1 px-2 font-11">
                                    <i class="mdi mdi-truck-delivery me-1"></i> Sedang Dikirim
                                </span>
                            @else
                                <span class="badge bg-label-warning py-1 px-2 font-11">
                                    <i class="mdi mdi-clock-outline me-1"></i> Menunggu Resi/Kirim
                                </span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Kurir / Ekspedisi</label>
                            <div class="fw-bold text-dark font-14">
                                {{ $cargo ?: '-' }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Nomor Resi / Pelacakan</label>
                            @if ($noResi)
                                <div class="font-monospace fw-bold text-primary font-14 bg-light p-2 rounded border d-inline-block">
                                    <i class="mdi mdi-barcode-scan me-1 text-muted"></i>{{ $noResi }}
                                </div>
                            @else
                                <span class="text-muted fst-italic font-12">Belum ada nomor resi</span>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="text-muted font-11 text-uppercase fw-bold d-block mb-1">Ongkos Kirim</label>
                            <div class="fw-semibold text-dark font-13">
                                Rp {{ number_format($purchase->delivery_cost ?: 0, 0, ',', '.') }}
                            </div>
                        </div>

                        @if (!$noResi && !$isReceived)
                            <div class="alert alert-warning py-2 px-3 mb-0 font-11 rounded-3">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Nomor resi belum diinput. Anda dapat mengisinya kapan saja begitu toko mengirimkan resi pengiriman.
                            </div>
                        @endif

                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-3 d-flex align-items-center justify-content-center gap-1" data-bs-toggle="modal" data-bs-target="#modalUpdateDelivery" style="color: #0d9488; border-color: #0d9488;">
                            <i class="mdi mdi-truck-edit me-1"></i> Update Info Pengiriman &amp; Resi
                        </button>
                    </div>
                </div>

                {{-- Goods Receipt History Card (if any) --}}
                @if ($productIns && $productIns->count())
                    <div class="card dp-card mb-4">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h6 class="card-title m-0 fw-bold text-dark d-flex align-items-center font-14">
                                <i class="mdi mdi-clipboard-check-outline me-2 text-success"></i> Riwayat Penerimaan Gudang (GR)
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            @foreach ($productIns as $pi)
                                <div class="border rounded p-2 mb-2 bg-light-subtle font-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-dark">GR: #{{ $pi->no_product_in ?: $pi->id }}</span>
                                        <span class="badge bg-label-success font-10">Diterima</span>
                                    </div>
                                    <div class="text-muted font-11">
                                        <i class="mdi mdi-calendar me-1"></i>{{ \Carbon\Carbon::parse($pi->date)->format('d-m-Y') }}
                                        · Penerima: {{ $pi->user->name ?? 'Gudang' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Update Info Pengiriman & No Resi --}}
    <div class="modal fade" id="modalUpdateDelivery" tabindex="-1" aria-labelledby="modalUpdateDeliveryLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <form id="formUpdateDelivery" action="{{ route('purchase.update-delivery', $purchase->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-md flex-shrink-0">
                                <span class="avatar-initial rounded-3 shadow-xs" style="background-color: #ccfbf1; color: #0f766e;">
                                    <i class="mdi mdi-truck-fast font-22"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0" id="modalUpdateDeliveryLabel">Update Info Pengiriman &amp; Resi</h5>
                                <small class="text-muted font-12">Perbarui ekspedisi, no resi, atau ongkir pembelian</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-dark">Kurir / Ekspedisi / Metode Pengambilan <span class="text-danger">*</span></label>
                                <input type="text" name="cargo" id="modalCargoInput" class="form-control" placeholder="Contoh: JNE / SiCepat / GoSend / Ambil Sendiri" value="{{ old('cargo', $purchase->on_delivery_cargo ?: $purchase->delivery) }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small text-dark">Nomor Resi / Tracking</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-barcode-scan"></i></span>
                                    <input type="text" name="no_resi" id="modalResiInput" class="form-control font-monospace" placeholder="Masukkan nomor resi ekspedisi (bila ada)" value="{{ old('no_resi', $purchase->on_delivery_no_resi) }}">
                                </div>
                                <small class="text-muted font-11">Kosongkan jika resi belum terbit atau barang diambil langsung</small>
                            </div>

                            <div class="col-sm-6 col-12">
                                <label class="form-label fw-semibold small text-dark">Tipe Pembelian</label>
                                <select name="purchase_type" class="form-select">
                                    <option value="Lokal" {{ old('purchase_type', $prDeliveryType ?? 'Lokal') == 'Lokal' ? 'selected' : '' }}>Lokal</option>
                                    <option value="Impor" {{ old('purchase_type', $prDeliveryType ?? 'Lokal') == 'Impor' ? 'selected' : '' }}>Impor</option>
                                </select>
                            </div>

                            <div class="col-sm-6 col-12">
                                <label class="form-label fw-semibold small text-dark">Tanggal Kirim / Beli</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', $purchase->date ?: date('Y-m-d')) }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small text-dark">Ongkos Kirim (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="text" name="delivery_cost" id="modalDeliveryCostInput" class="form-control text-end auto-currency" placeholder="0" value="{{ old('delivery_cost', $purchase->delivery_cost ? number_format($purchase->delivery_cost, 0, ',', '.') : 0) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light bg-opacity-25 px-4 py-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs" id="btnSubmitDelivery" style="background-color: #0d9488; border-color: #0d9488;">
                            <i class="mdi mdi-check-circle-outline me-1"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script>
        $(document).ready(function() {
            // Auto currency formatter
            $(document).on('input', '.auto-currency', function() {
                var clean = $(this).val().replace(/[^0-9]/g, '');
                var val = clean ? parseFloat(clean) : 0;
                if (val === 0 && $(this).val() === '') {
                    $(this).val('');
                } else {
                    $(this).val(val.toLocaleString('id-ID'));
                }
            });

            // Form Submit via AJAX with SweetAlert feedback
            $('#formUpdateDelivery').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var data = form.serialize();

                var $btn = $('#btnSubmitDelivery');
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(res) {
                        $('#modalUpdateDelivery').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'Info pengiriman berhasil diperbarui.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="mdi mdi-check-circle-outline me-1"></i> Simpan Perubahan');
                        var msg = 'Gagal menyimpan info pengiriman.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: msg
                        });
                    }
                });
            });
        });
    </script>
@endpush
