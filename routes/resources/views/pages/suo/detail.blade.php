@extends('layouts.sales.app')
@section('title', 'Detail SUO')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">SUO /</span> {{ $suo->no_suo }}
    </h4>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible mb-3">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row">
        {{-- Main card --}}
        <div class="col-xl-9 col-md-8 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h5 class="mb-1 fw-bold">{{ $suo->company }}</h5>
                            <p class="mb-0 text-muted">PIC: {{ $suo->pic }}</p>
                            <p class="mb-0 text-muted">Alamat: {{ $suo->address }}</p>
                            @if ($suo->notes)
                                <p class="mb-0 text-muted mt-1"><em>{{ $suo->notes }}</em></p>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="badge bg-label-primary fs-6 fw-bold">{{ $suo->no_suo }}</span>
                            <br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($suo->created_at)->format('d-m-Y') }}</small>
                            <br>
                            <span class="badge mt-1
                                @if($suo->status == 'draft') bg-secondary
                                @elseif($suo->status == 'submitted') bg-warning
                                @elseif($suo->status == 'confirmed') bg-info
                                @elseif($suo->status == 'goods_out') bg-success
                                @elseif($suo->status == 'converted') bg-primary
                                @endif">
                                {{ strtoupper($suo->status) }}
                            </span>
                            @if ($suo->no_invoice_booking)
                                <p class="mt-2 mb-0 fw-semibold text-success" style="font-size:12px;">
                                    Invoice Booking: {{ $suo->no_invoice_booking }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="table-responsive">
                    <table class="table table-bordered m-0">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Nama Item / Part</th>
                                <th class="text-center">Qty</th>
                                <th>Satuan</th>
                                <th>Catatan</th>
                                @if (in_array($suo->status, ['confirmed','goods_out','converted']))
                                    <th class="text-center">Stok</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suo->detail as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-semibold">{{ $item->item_name }}</td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td>{{ $item->unit ?? '-' }}</td>
                                    <td>{{ $item->notes ?? '-' }}</td>
                                    @if (in_array($suo->status, ['confirmed','goods_out','converted']))
                                        <td class="text-center">
                                            @if ($item->stock_status == 'ready')
                                                <span class="badge bg-success">Ready</span>
                                            @elseif ($item->stock_status == 'not_ready')
                                                <span class="badge bg-danger">Not Ready</span>
                                            @else
                                                <span class="badge bg-secondary">-</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Preview item penawaran jika sudah converted --}}
                @if ($suo->status == 'converted' && $quotation && $quotationDetail->count())
                    <div class="card-body border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">
                                <i class="mdi mdi-file-document-check-outline me-1 text-primary"></i>
                                Item Penawaran
                            </h6>
                            <a href="{{ route('quotation.show', $quotation->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="mdi mdi-eye-outline me-1"></i> Lihat Penawaran
                            </a>
                        </div>
                        <p class="text-muted mb-2" style="font-size:12px;">
                            No. Penawaran: <strong>{{ $quotation->no_quote }}</strong>
                            &nbsp;|&nbsp; {{ $quotation->title }}
                        </p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" style="font-size:12px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Item / Part</th>
                                        <th class="text-center">Qty</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quotationDetail as $i => $item)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td class="fw-semibold">{{ $item->detail_product }}</td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <td>{{ $item->info_qty ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Logistic: stock check form --}}
                @if ($role == 'Logistic' && $suo->status == 'submitted')
                    <div class="card-body border-top">
                        <h6 class="fw-bold mb-3">Cek Ketersediaan Stok</h6>
                        <form action="{{ route('suo.checkStock', $suo->id) }}" method="POST">
                            @csrf
                            <table class="table table-sm table-bordered mb-3">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th>Status Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suo->detail as $item)
                                        <tr>
                                            <td>{{ $item->item_name }}</td>
                                            <td class="text-center">{{ $item->qty }} {{ $item->unit }}</td>
                                            <td>
                                                <div class="d-flex gap-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="stock_status[{{ $item->id }}]"
                                                            value="ready" required>
                                                        <label class="form-check-label text-success fw-semibold">Ready</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio"
                                                            name="stock_status[{{ $item->id }}]"
                                                            value="not_ready">
                                                        <label class="form-check-label text-danger fw-semibold">Not Ready</label>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-check me-1"></i> Simpan & Teruskan ke Accounting
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Action sidebar --}}
        <div class="col-xl-3 col-md-4 col-12">
            <div class="card">
                <div class="card-body d-grid gap-2">

                    {{-- Sales: convert to quotation --}}
                    @if (($role == 'Sales' || $role == 'Admin') && $suo->status == 'goods_out')
                        <div class="alert alert-success p-2 mb-0" style="font-size:12px;">
                            Barang sudah keluar. Silahkan buat penawaran untuk melanjutkan proses.
                        </div>
                        <a href="{{ route('suo.convert', $suo->id) }}" class="btn btn-primary waves-effect">
                            <i class="mdi mdi-file-document-plus-outline me-1"></i> Buat Penawaran
                        </a>
                    @endif

                    @if ($suo->status == 'converted' && $suo->id_quotation)
                        <div class="alert alert-primary p-2 mb-0" style="font-size:12px;">
                            SUO sudah dikonversi ke penawaran.
                        </div>
                        <a href="{{ route('quotation.show', $suo->id_quotation) }}" class="btn btn-outline-primary">
                            <i class="mdi mdi-eye-outline me-1"></i> Lihat Penawaran
                        </a>
                        @if ($invoice)
                            <a href="{{ url('invoice/' . $invoice->id) }}" class="btn btn-outline-success">
                                <i class="mdi mdi-file-document-outline me-1"></i> Lihat Invoice
                            </a>
                        @endif
                    @endif

                    {{-- Accounting: approve & buat SJ --}}
                    @if (($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && !$suo->no_invoice_booking)
                        <button class="btn btn-success waves-effect" id="btn-approve"
                            data-bs-toggle="modal" data-bs-target="#modalApprove">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Approve & Booking Invoice
                        </button>
                    @endif

                    @if (($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && $suo->no_invoice_booking)
                        <div class="alert alert-success p-2 mb-0" style="font-size:12px;">
                            Invoice dibooked: <strong>{{ $suo->no_invoice_booking }}</strong>
                        </div>
                        <button class="btn btn-info waves-effect" data-bs-toggle="modal" data-bs-target="#modalSJ">
                            <i class="mdi mdi-truck-delivery-outline me-1"></i> Buat Surat Jalan
                        </button>
                    @endif

                    @if ($suo->deliveries->count() > 0)
                        @foreach ($suo->deliveries as $d)
                            <a href="{{ url('delivery/' . $d->id) }}" class="btn btn-outline-info btn-sm">
                                <i class="mdi mdi-file-document-outline me-1"></i> Lihat SJ #{{ $d->id }}
                            </a>
                        @endforeach
                    @endif

                    <a href="javascript:history.back()" class="btn btn-outline-secondary">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            {{-- History timeline --}}
            <div class="card mt-3">
                <div class="card-header py-2">
                    <h6 class="mb-0 fw-bold" style="font-size:13px;">Riwayat SUO</h6>
                </div>
                <div class="card-body py-3">
                    <ul class="list-unstyled mb-0" style="position:relative;">

                        {{-- Step 1: Dibuat Sales --}}
                        <li class="d-flex gap-3 mb-3">
                            <div class="flex-shrink-0 mt-1">
                                <span class="badge bg-primary rounded-circle p-1" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                                    <i class="mdi mdi-pencil-outline" style="font-size:12px;"></i>
                                </span>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold" style="font-size:12px;">Dibuat oleh Sales</p>
                                <p class="mb-0 text-muted" style="font-size:11px;">{{ $suo->sales->name ?? '-' }}</p>
                                <p class="mb-0 text-muted" style="font-size:11px;">{{ \Carbon\Carbon::parse($suo->created_at)->format('d M Y, H:i') }}</p>
                            </div>
                        </li>

                        {{-- Step 2: Dicek Logistik --}}
                        @if ($suo->confirmed_at)
                            <li class="d-flex gap-3 mb-3">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="badge bg-warning rounded-circle p-1" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                                        <i class="mdi mdi-clipboard-check-outline" style="font-size:12px;"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:12px;">Dicek oleh Logistik</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;">{{ $suo->confirmedBy->name ?? '-' }}</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;">{{ \Carbon\Carbon::parse($suo->confirmed_at)->format('d M Y, H:i') }}</p>
                                </div>
                            </li>
                        @else
                            <li class="d-flex gap-3 mb-3 opacity-50">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="badge bg-secondary rounded-circle p-1" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                                        <i class="mdi mdi-clipboard-check-outline" style="font-size:12px;"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:12px;">Menunggu cek Logistik</p>
                                </div>
                            </li>
                        @endif

                        {{-- Step 3: Diapprove Accounting --}}
                        @if ($suo->approved_at)
                            <li class="d-flex gap-3 mb-0">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="badge bg-success rounded-circle p-1" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                                        <i class="mdi mdi-check-circle-outline" style="font-size:12px;"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:12px;">Diapprove oleh Accounting</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;">{{ $suo->approvedBy->name ?? '-' }}</p>
                                    <p class="mb-0 text-muted" style="font-size:11px;">{{ \Carbon\Carbon::parse($suo->approved_at)->format('d M Y, H:i') }}</p>
                                    @if ($suo->no_invoice_booking)
                                        <p class="mb-0 text-success fw-semibold" style="font-size:11px;">Invoice: {{ $suo->no_invoice_booking }}</p>
                                    @endif
                                </div>
                            </li>
                        @else
                            <li class="d-flex gap-3 mb-0 opacity-50">
                                <div class="flex-shrink-0 mt-1">
                                    <span class="badge bg-secondary rounded-circle p-1" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                                        <i class="mdi mdi-check-circle-outline" style="font-size:12px;"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-semibold" style="font-size:12px;">Menunggu approve Accounting</p>
                                </div>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Booking Invoice --}}
    @if (($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && !$suo->no_invoice_booking)
    <div class="modal fade" id="modalApprove" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve & Booking Invoice — {{ $suo->no_suo }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:13px;">{{ $suo->company }}</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">No Invoice Booking</label>
                        <input type="text" class="form-control" id="inputNoInvoiceBooking"
                            placeholder="Memuat nomor...">
                        <small class="text-danger" id="lastNoBooking"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success waves-effect" id="btn-approve-confirm">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Konfirmasi & Approve
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Surat Jalan --}}
    @if (($role == 'Admin' || $role == 'Accounting') && $suo->status == 'confirmed' && $suo->no_invoice_booking)
        <div class="modal fade" id="modalSJ" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('suo.storeDelivery', $suo->id) }}" method="POST">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Buat Surat Jalan — {{ $suo->no_suo }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="date"
                                    value="{{ \Carbon\Carbon::today()->toDateString() }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tujuan / Alamat</label>
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
                                <label class="form-label">Jenis Pengiriman</label>
                                <select class="form-select" name="type">
                                    <option value="Ekspedisi">Ekspedisi</option>
                                    <option value="Teknisi">Teknisi</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Buat Surat Jalan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css"/>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
<script>
$(function () {
    // Load nomor suggest saat modal dibuka
    $('#modalApprove').on('show.bs.modal', function () {
        $('#inputNoInvoiceBooking').val('Memuat...').prop('disabled', true);
        $('#lastNoBooking').text('');
        $.get('{{ route('suo.suggestBooking', $suo->id) }}', function (res) {
            $('#inputNoInvoiceBooking').val(res.suggested).prop('disabled', false);
            if (res.last) {
                $('#lastNoBooking').text('Last No: ' + res.last);
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
