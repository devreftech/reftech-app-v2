@extends('layouts.sales.app')
@section('title', 'Part Inquiry Detail')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{ route('part-inquiry.index') }}">Part Inquiry</a> /</span>
        <span id="viewTitle">{{ $serial->brand }} — {{ $serial->pn }}</span>
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
                    <button class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#editEquivalentModal">
                        <i class="mdi mdi-pencil-outline me-1"></i> Edit Equivalent
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" width="40%">Brand</td>
                            <td>: <strong id="viewBrand">{{ $serial->brand }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Part Number</td>
                            <td>: <strong id="viewPn">
                                @if (!$serial->pn || $serial->pn === '-')
                                    <span class="badge bg-label-warning">PN Pending</span>
                                @else
                                    {{ $serial->pn }}
                                @endif
                            </strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Harga Jual</td>
                            <td>: <strong class="text-success" id="viewPrice">Rp {{ number_format($serial->price, 0, ',', '.') }}</strong></td>
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
                            <th>Harga USD ($)</th>
                            <th>Harga Modal (IDR)</th>
                            <th>Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vendorPrices as $vp)
                            <tr>
                                <td>{{ $vp->supplier->supplier ?? '-' }}</td>
                                <td>{{ $vp->price_usd > 0 ? '$ ' . number_format($vp->price_usd, 2) : '-' }}</td>
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
                                <td colspan="5" class="text-center text-muted py-3">Belum ada data harga vendor.</td>
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
                            <select class="form-select select2-supplier-modal" name="id_supplier" required style="width:100%">
                                <option value="" disabled selected>-- Pilih Supplier --</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->info ?: '-' }} | {{ $supplier->code ?: '-' }} | {{ $supplier->supplier }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga USD ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" name="price_usd"
                                    placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Modal (IDR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="modal_price_idr_display" placeholder="0" inputmode="numeric">
                                <input type="hidden" id="modal_price_idr" name="price_idr">
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

    {{-- Modal: Edit Equivalent --}}
    <div class="modal fade" id="editEquivalentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Equivalent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editEqBrand" value="{{ $serial->brand }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Part Number (PN)</label>
                        <input type="text" class="form-control" id="editEqPn"
                            value="{{ $serial->pn !== '-' ? $serial->pn : '' }}" placeholder="HU718/5x">
                        <small class="text-muted">Kosongkan jika PN belum didapat dari vendor</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Jual (IDR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="editEqPriceDisplay" placeholder="0" inputmode="numeric">
                            <input type="hidden" id="editEqPrice" value="{{ $serial->price }}">
                        </div>
                    </div>
                    <div id="editEqError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveEditEquivalent">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/cleavejs/cleave.js"></script>
@endpush

@push('page-script')
<script>
    function formatSupplierOption(state) {
        if (!state.id) return state.text;
        var parts = state.text.split(' | ');
        var info = parts[0] || '-';
        var rest = parts.slice(1).join(' | ');
        var badgeColor = info === 'Lokal' ? 'success' : (info === 'Import' ? 'info' : 'secondary');
        return $('<span><span class="badge bg-label-' + badgeColor + ' me-1">' + info + '</span>' + rest + '</span>');
    }

    $('.select2-supplier-modal').select2({
        placeholder: '-- Pilih Supplier --',
        dropdownParent: $('#addVendorPrice'),
        width: '100%',
        templateResult: formatSupplierOption,
        templateSelection: formatSupplierOption,
    });

    var cleaveModalPriceIdr = new Cleave('#modal_price_idr_display', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        delimiter: '.',
        numeralDecimalMark: ',',
        numeralDecimalScale: 0,
        onValueChanged: function (e) {
            $('#modal_price_idr').val(e.target.rawValue);
        }
    });

    var cleaveEditPrice = new Cleave('#editEqPriceDisplay', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        delimiter: '.',
        numeralDecimalMark: ',',
        numeralDecimalScale: 0,
    });
    cleaveEditPrice.setRawValue($('#editEqPrice').val());

    $('#editEquivalentModal').on('show.bs.modal', function () {
        $('#editEqError').addClass('d-none').text('');
    });

    $('#saveEditEquivalent').on('click', function () {
        var brand = $('#editEqBrand').val().trim();
        var pn    = $('#editEqPn').val().trim();
        var price = cleaveEditPrice.getRawValue();

        if (!brand || price === '') {
            $('#editEqError').removeClass('d-none').text('Brand dan Harga Jual wajib diisi.');
            return;
        }

        $.ajax({
            url: '{{ route("part-inquiry.equivalent.update", $serial->id) }}',
            type: 'PATCH',
            data: { _token: '{{ csrf_token() }}', brand: brand, pn: pn, price: price },
            success: function (res) {
                if (res.success) {
                    $('#viewBrand').text(res.data.brand);
                    $('#viewPn').html(
                        (!res.data.pn || res.data.pn === '-')
                            ? '<span class="badge bg-label-warning">PN Pending</span>'
                            : res.data.pn
                    );
                    $('#viewPrice').text('Rp ' + parseInt(res.data.price).toLocaleString('id-ID'));
                    $('#viewTitle').text(res.data.brand + ' — ' + res.data.pn);
                    $('#editEquivalentModal').modal('hide');
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message : 'Gagal menyimpan, coba lagi.';
                $('#editEqError').removeClass('d-none').text(msg);
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
