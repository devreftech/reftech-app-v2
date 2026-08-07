@extends('layouts.sales.app')
@section('title', 'Part Inquiry Detail')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{ route('part-inquiry.index') }}">Part Inquiry</a> /</span>
        {{ $serial->brand }} — {{ $serial->pn }}
    </h4>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        {{-- Product Info --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">Product</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">SKU</td>
                            <td>: <strong>
                                <a href="{{ route('product.show', $serial->product->id) }}">
                                    {{ $serial->product->commodity }}
                                </a>
                            </strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Genuine / OEM</td>
                            <td>: {{ $serial->product->go }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Description</td>
                            <td>: {{ $serial->product->description }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Equivalent Info --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Equivalent</h6>
                    <button class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#editSellingPrice">
                        <i class="mdi mdi-pencil-outline me-1"></i> Edit Harga Jual
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">Brand</td>
                            <td>: <strong>{{ $serial->brand }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Part Number</td>
                            <td>: <strong>{{ $serial->pn }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Harga Jual</td>
                            <td>: <strong class="text-success">Rp {{ number_format($serial->price, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Vendor Prices --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Harga Vendor</h6>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addVendorPrice">
                <i class="mdi mdi-plus me-1"></i> Tambah Harga Vendor
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Harga USD</th>
                            <th>Kurs</th>
                            <th>Harga Modal</th>
                            <th>Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vendorPrices as $vp)
                            <tr>
                                <td>{{ $vp->supplier->supplier ?? '-' }}</td>
                                <td>$ {{ number_format($vp->price_usd, 2) }}</td>
                                <td>Rp {{ number_format($vp->kurs_usd, 0, ',', '.') }}</td>
                                <td><strong>Rp {{ number_format($vp->price_idr, 0, ',', '.') }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($vp->date)->format('d M Y') }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-label-danger delete-vendor-price"
                                        data-id="{{ $vp->id }}">
                                        <i class="mdi mdi-delete-outline"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Belum ada data harga vendor.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal: Add Vendor Price --}}
    <div class="modal fade" id="addVendorPrice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('part-inquiry.vendor.store', $serial->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Harga Vendor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_supplier" required>
                                <option value="" disabled selected>-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->supplier }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga USD ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="modal_price_usd" name="price_usd"
                                    placeholder="0.00" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kurs (IDR/$) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="modal_kurs_usd" name="kurs_usd"
                                    placeholder="16000" step="1" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Modal (IDR)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="modal_price_idr" readonly placeholder="0" tabindex="-1">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date"
                                value="{{ now()->toDateString() }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Edit Selling Price --}}
    <div class="modal fade" id="editSellingPrice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('part-inquiry.selling-price.update', $serial->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Harga Jual</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Harga Jual (IDR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="selling_price"
                                value="{{ $serial->price }}" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('page-script')
<script>
    function formatRupiah(num) {
        return Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function recalcModalIdr() {
        var usd  = parseFloat($('#modal_price_usd').val()) || 0;
        var kurs = parseFloat($('#modal_kurs_usd').val()) || 0;
        $('#modal_price_idr').val(usd > 0 && kurs > 0 ? formatRupiah(usd * kurs) : '');
    }

    $('#modal_price_usd, #modal_kurs_usd').on('input', recalcModalIdr);

    // Auto-fetch kurs saat modal dibuka
    $('#addVendorPrice').on('show.bs.modal', function () {
        if ($('#modal_kurs_usd').val()) return; // sudah terisi, skip
        $.get('{{ route("exchange-rate.usd-idr") }}', function (res) {
            if (res.rate) {
                $('#modal_kurs_usd').val(res.rate).trigger('input');
                $('#modal_kurs_usd').next('.kurs-source').remove();
                $('#modal_kurs_usd').after('<small class="text-muted kurs-source">Auto: Rp ' + res.rate.toLocaleString('id-ID') + ' / USD</small>');
            }
        });
    });

    $(document).on('click', '.delete-vendor-price', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (confirm('Hapus harga vendor ini?')) {
            $.ajax({
                url: '/part-inquiry/vendor/' + id + '/delete',
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (res) {
                    if (res == 1) location.reload();
                }
            });
        }
    });
</script>
@endpush
