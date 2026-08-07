@extends('layouts.sales.app')
@section('title', 'Part Inquiry - Add New')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{ route('part-inquiry.index') }}">Part Inquiry</a> /</span> Add New
    </h4>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('part-inquiry.store') }}" method="POST" id="formPartInquiry">
        @csrf

        <div class="row mb-4">
        {{-- PRODUCT SECTION --}}
        <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Product</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="product_type" id="typeNew" value="new" checked>
                        <label class="form-check-label" for="typeNew">New Product</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="product_type" id="typeExisting" value="existing">
                        <label class="form-check-label" for="typeExisting">Existing Product</label>
                    </div>
                </div>

                {{-- New Product Fields --}}
                <div id="newProductFields">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="commodity" name="commodity"
                                    placeholder="W001" value="{{ old('commodity') }}">
                                <label for="commodity">SKU / Commodity <span class="text-danger">*</span></label>
                            </div>
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" id="pnAvailable" checked>
                                <label class="form-check-label small" for="pnAvailable">
                                    Ada Data PN (auto-fill PN dari SKU)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" name="go" id="go">
                                    <option value="" disabled selected>-- Pilih --</option>
                                    <option value="Genuine" {{ old('go') == 'Genuine' ? 'selected' : '' }}>Genuine</option>
                                    <option value="Replacement" {{ old('go') == 'Replacement' ? 'selected' : '' }}>Replacement</option>
                                </select>
                                <label for="go">Genuine / Replacement <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" name="category" id="category">
                                    <option value="Non Consumable Part" {{ old('category') == 'Non Consumable Part' ? 'selected' : '' }}>Non Consumable Part</option>
                                    <option value="Consumable Part" {{ old('category', 'Consumable Part') == 'Consumable Part' ? 'selected' : '' }}>Consumable Part</option>
                                </select>
                                <label for="category">Category</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating form-floating-outline">
                                <textarea class="form-control" id="description" name="description"
                                    placeholder="Description" style="height: 80px">{{ old('description') }}</textarea>
                                <label for="description">Description <span class="text-danger">*</span></label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Existing Product Fields --}}
                <div id="existingProductFields" style="display:none;">
                    <select class="select2-product w-100" name="id_product" id="id_product" style="width:100%">
                        <option value="" disabled selected>-- Cari SKU / Description --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ old('id_product') == $product->id ? 'selected' : '' }}>
                                {{ $product->commodity }} — {{ $product->description }}
                            </option>
                        @endforeach
                    </select>

                    <div id="equivalentList" class="mt-3" style="display:none;">
                        <p class="text-muted small mb-1">Equivalent yang sudah ada:</p>
                        <div class="input-group input-group-sm mb-2">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="bulkPriceDisplay"
                                placeholder="Set harga jual untuk semua equivalent" inputmode="numeric">
                            <input type="hidden" id="bulkPrice">
                            <button type="button" class="btn btn-outline-primary" id="applyBulkPrice">
                                Set ke Semua
                            </button>
                        </div>
                        <div id="bulkPriceSuccess" class="alert alert-success alert-sm py-1 px-2 d-none small"></div>
                        <div id="bulkPriceError" class="alert alert-danger alert-sm py-1 px-2 d-none small"></div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Brand</th>
                                        <th>Part Number</th>
                                        <th>Harga Jual</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="equivalentRows"></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="equivalentEmpty" class="mt-2 text-muted small" style="display:none;">
                        Belum ada equivalent untuk produk ini.
                    </div>
                </div>
            </div>
        </div>

        </div>{{-- /col-md-6 product --}}

        {{-- EQUIVALENT SECTION --}}
        <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Equivalent (Part Number)</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="brand" name="brand"
                                placeholder="Mann" value="{{ old('brand') }}">
                            <label for="brand">Brand <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="pn" name="pn"
                                placeholder="HU718/5x" value="{{ old('pn') }}">
                            <label for="pn">Part Number (PN)</label>
                        </div>
                        <small class="text-muted">Kosongkan jika PN belum didapat dari vendor</small>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="selling_price_display"
                                placeholder="0" inputmode="numeric"
                                value="{{ old('selling_price') ? number_format(old('selling_price'), 0, ',', '.') : '' }}">
                            <input type="hidden" name="selling_price" id="selling_price" value="{{ old('selling_price') }}">
                        </div>
                        <small class="text-muted">Harga Jual (IDR) <span class="text-danger">*</span></small>
                    </div>
                </div>
            </div>
        </div>

        </div>{{-- /col-md-6 equivalent --}}
        </div>{{-- /row --}}

        {{-- VENDOR PRICES SECTION --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Harga Vendor</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-label-secondary" data-bs-toggle="modal" data-bs-target="#quickAddSupplierModal">
                        <i class="mdi mdi-domain me-1"></i> Supplier Baru
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" id="addVendorRow">
                        <i class="mdi mdi-plus me-1"></i> Tambah Supplier
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="vendorTable">
                        <thead>
                            <tr>
                                <th>Supplier</th>
                                <th>Harga USD ($)</th>
                                <th>Harga Modal (IDR)</th>
                                <th>Tanggal Inquiry</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="vendorRows">
                            <tr class="vendor-row">
                                <td>
                                    <select class="form-select select2-supplier" name="vendors[0][id_supplier]" style="width:100%">
                                        <option value="">-- Cari Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->info ?: '-' }} | {{ $supplier->code ?: '-' }} | {{ $supplier->supplier }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="vendors[0][price_usd]"
                                            placeholder="0.00" step="0.01" min="0">
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control vendor-modal-display"
                                            placeholder="0" inputmode="numeric">
                                    </div>
                                    <input type="hidden" class="vendor-modal" name="vendors[0][price_idr]">
                                </td>
                                <td>
                                    <input type="date" class="form-control" name="vendors[0][date]"
                                        value="{{ now()->toDateString() }}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-label-danger remove-vendor-row">
                                        <i class="mdi mdi-delete-outline"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="mdi mdi-content-save-outline me-1"></i> Simpan
            </button>
            <a href="{{ route('part-inquiry.index') }}" class="btn btn-label-secondary">Batal</a>
        </div>
    </form>

    {{-- Modal: Edit Equivalent --}}
    <div class="modal fade" id="editEquivalentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Equivalent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editEqId">
                    <div class="mb-3">
                        <label class="form-label">Brand <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editEqBrand" placeholder="Mann">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Part Number (PN)</label>
                        <input type="text" class="form-control" id="editEqPn" placeholder="HU718/5x">
                        <small class="text-muted">Kosongkan jika PN belum didapat dari vendor</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Jual (IDR) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="editEqPriceDisplay" placeholder="0" inputmode="numeric">
                            <input type="hidden" id="editEqPrice">
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

    {{-- Modal: Quick Add Supplier --}}
    <div class="modal fade" id="quickAddSupplierModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Supplier Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="qsCode" placeholder="SUP001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="qsName" placeholder="PT Contoh Jaya">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Info <span class="text-danger">*</span></label>
                        <select class="form-select" id="qsInfo">
                            <option value="" disabled selected>-- Pilih --</option>
                            <option value="Lokal">Lokal</option>
                            <option value="Import">Import</option>
                        </select>
                    </div>
                    <div id="qsError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveQuickAddSupplier">
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
    $('.select2-product').select2({
        placeholder: '-- Cari SKU / Description --',
        allowClear: true,
        width: '100%',
    });

    var cleaveSellingPrice = new Cleave('#selling_price_display', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        delimiter: '.',
        numeralDecimalMark: ',',
        numeralDecimalScale: 0,
    });

    $('#formPartInquiry').on('submit', function () {
        $('#selling_price').val(cleaveSellingPrice.getRawValue());
    });

    var vendorIndex = 1;
    var suppliers = @json($suppliers);

    function formatSupplierLabel(s) {
        return (s.info || '-') + ' | ' + (s.code || '-') + ' | ' + s.supplier;
    }

    function buildSupplierSelect(index) {
        var $select = $('<select class="form-select select2-supplier" style="width:100%">');
        $select.attr('name', 'vendors[' + index + '][id_supplier]');
        $select.append('<option value="">-- Cari Supplier --</option>');
        suppliers.forEach(function(s) {
            $select.append($('<option>').val(s.id).text(formatSupplierLabel(s)));
        });
        return $select;
    }

    function formatSupplierOption(state) {
        if (!state.id) return state.text;
        var parts = state.text.split(' | ');
        var info = parts[0] || '-';
        var rest = parts.slice(1).join(' | ');
        var badgeColor = info === 'Lokal' ? 'success' : (info === 'Import' ? 'info' : 'secondary');
        return $('<span><span class="badge bg-label-' + badgeColor + ' me-1">' + info + '</span>' + rest + '</span>');
    }

    function initSupplierSelect2($el) {
        $el.select2({
            placeholder: '-- Cari Supplier --',
            allowClear: true,
            width: '100%',
            templateResult: formatSupplierOption,
            templateSelection: formatSupplierOption,
        });
    }

    initSupplierSelect2($('.select2-supplier'));

    function initVendorModalCleave($displayInput) {
        var $hidden = $displayInput.closest('td').find('.vendor-modal');
        var cleave = new Cleave($displayInput[0], {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            delimiter: '.',
            numeralDecimalMark: ',',
            numeralDecimalScale: 0,
            onValueChanged: function (e) {
                $hidden.val(e.target.rawValue);
            }
        });
        $displayInput.data('cleave', cleave);
        return cleave;
    }

    initVendorModalCleave($('.vendor-modal-display'));

    $('#addVendorRow').on('click', function () {
        var $select = buildSupplierSelect(vendorIndex);
        var $row = $('<tr class="vendor-row">');
        $row.append($('<td>').append($select));
        $row.append('<td><div class="input-group"><span class="input-group-text">$</span><input type="number" class="form-control" name="vendors[' + vendorIndex + '][price_usd]" placeholder="0.00" step="0.01" min="0"></div></td>');
        var $modalTd = $('<td>');
        $modalTd.append('<div class="input-group"><span class="input-group-text">Rp</span><input type="text" class="form-control vendor-modal-display" placeholder="0" inputmode="numeric"></div>');
        $modalTd.append('<input type="hidden" class="vendor-modal" name="vendors[' + vendorIndex + '][price_idr]">');
        $row.append($modalTd);
        $row.append('<td><input type="date" class="form-control" name="vendors[' + vendorIndex + '][date]" value="{{ now()->toDateString() }}"></td>');
        $row.append('<td><button type="button" class="btn btn-sm btn-label-danger remove-vendor-row"><i class="mdi mdi-delete-outline"></i></button></td>');
        $('#vendorRows').append($row);
        initSupplierSelect2($select);
        initVendorModalCleave($row.find('.vendor-modal-display'));
        vendorIndex++;
    });

    $(document).on('click', '.remove-vendor-row', function () {
        if ($('.vendor-row').length > 1) {
            $(this).closest('tr').remove();
        }
    });

    // Quick Add Supplier (AJAX, tanpa reload)
    $('#saveQuickAddSupplier').on('click', function () {
        var code = $('#qsCode').val().trim();
        var name = $('#qsName').val().trim();
        var info = $('#qsInfo').val();

        if (!code || !name || !info) {
            $('#qsError').removeClass('d-none').text('Semua field wajib diisi.');
            return;
        }

        $.ajax({
            url: '{{ route("supplier.quick-store") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', code: code, supplier: name, info: info },
            success: function (res) {
                if (res.success) {
                    var newSupplier = res.data;
                    suppliers.push(newSupplier);

                    $('.select2-supplier').each(function () {
                        var $select = $(this);
                        var option = new Option(formatSupplierLabel(newSupplier), newSupplier.id, false, false);
                        $select.append(option).trigger('change');
                    });

                    $('#qsCode').val('');
                    $('#qsName').val('');
                    $('#qsInfo').val('');
                    $('#qsError').addClass('d-none').text('');
                    $('#quickAddSupplierModal').modal('hide');
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message : 'Gagal menyimpan, coba lagi.';
                $('#qsError').removeClass('d-none').text(msg);
            }
        });
    });

    // Auto-fill PN dari SKU (bisa dimatikan kalau PN belum didapat dari vendor)
    $('#commodity').on('input', function () {
        if ($('#pnAvailable').is(':checked')) {
            $('#pn').val($(this).val());
        }
    });

    $('#pnAvailable').on('change', function () {
        if ($(this).is(':checked')) {
            $('#pn').val($('#commodity').val());
        } else {
            $('#pn').val('');
        }
    });

    // Auto-fill Brand dari Genuine/Replacement
    $('#go').on('change', function () {
        if ($(this).val() === 'Replacement') {
            $('#brand').val('FXP FILTRATION');
        } else {
            $('#brand').val('');
        }
    });

    $('input[name="product_type"]').on('change', function () {
        if ($(this).val() === 'existing') {
            $('#newProductFields').hide();
            $('#existingProductFields').show();
            $('#newProductFields input, #newProductFields select, #newProductFields textarea').prop('disabled', true);
            $('#id_product').prop('disabled', false).trigger('change');
        } else {
            $('#newProductFields').show();
            $('#existingProductFields').hide();
            $('#newProductFields input, #newProductFields select, #newProductFields textarea').prop('disabled', false);
            $('#id_product').prop('disabled', true).trigger('change');
            $('#equivalentList, #equivalentEmpty').hide();
        }
    });

    function formatRupiahSimple(num) {
        return 'Rp ' + parseInt(num || 0).toLocaleString('id-ID');
    }

    $('#id_product').on('change', function () {
        var id = $(this).val();
        $('#equivalentList, #equivalentEmpty').hide();
        $('#equivalentRows').empty();

        if (!id) return;

        $.get('{{ route("part-inquiry.product.equivalents", ":id") }}'.replace(':id', id), function (data) {
            if (data.length === 0) {
                $('#equivalentEmpty').show();
                return;
            }
            $.each(data, function (i, row) {
                $('#equivalentRows').append(
                    '<tr data-id="' + row.id + '">' +
                    '<td>' + (i + 1) + '</td>' +
                    '<td class="eq-brand">' + (row.brand || '-') + '</td>' +
                    '<td class="eq-pn">' + (row.pn || '-') + '</td>' +
                    '<td class="eq-price">' + formatRupiahSimple(row.price) + '</td>' +
                    '<td><button type="button" class="btn btn-sm btn-label-warning btn-edit-equivalent" ' +
                        'data-id="' + row.id + '" data-brand="' + (row.brand || '') + '" ' +
                        'data-pn="' + (row.pn || '') + '" data-price="' + (row.price || 0) + '">' +
                        '<i class="mdi mdi-pencil-outline"></i>' +
                    '</button></td>' +
                    '</tr>'
                );
            });
            $('#equivalentList').show();
        });
    });

    var cleaveBulkPrice = new Cleave('#bulkPriceDisplay', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        delimiter: '.',
        numeralDecimalMark: ',',
        numeralDecimalScale: 0,
        onValueChanged: function (e) {
            $('#bulkPrice').val(e.target.rawValue);
        }
    });

    $('#applyBulkPrice').on('click', function () {
        var idProduct = $('#id_product').val();
        var price = $('#bulkPrice').val();

        if (!idProduct) return;
        if (price === '' || price === undefined) {
            $('#bulkPriceSuccess').addClass('d-none');
            $('#bulkPriceError').removeClass('d-none').text('Isi harga terlebih dahulu.');
            return;
        }

        $.ajax({
            url: '{{ route("part-inquiry.product.equivalents.bulk-price", ":id") }}'.replace(':id', idProduct),
            type: 'PATCH',
            data: { _token: '{{ csrf_token() }}', selling_price: price },
            success: function (res) {
                if (res.success) {
                    $('#equivalentRows .eq-price').text(formatRupiahSimple(price));
                    $('#equivalentRows .btn-edit-equivalent').each(function () {
                        $(this).data('price', price);
                    });
                    $('#bulkPriceError').addClass('d-none').text('');
                    $('#bulkPriceSuccess').removeClass('d-none')
                        .text('Harga jual berhasil diupdate untuk ' + res.updated + ' equivalent!');
                    setTimeout(function () {
                        $('#bulkPriceSuccess').addClass('d-none');
                    }, 3000);
                }
            },
            error: function (xhr) {
                $('#bulkPriceSuccess').addClass('d-none');
                var msg = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message : 'Gagal menyimpan, coba lagi.';
                $('#bulkPriceError').removeClass('d-none').text(msg);
            }
        });
    });

    var cleaveEditPrice = new Cleave('#editEqPriceDisplay', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        delimiter: '.',
        numeralDecimalMark: ',',
        numeralDecimalScale: 0,
    });

    $(document).on('click', '.btn-edit-equivalent', function () {
        var $btn = $(this);
        $('#editEqId').val($btn.data('id'));
        $('#editEqBrand').val($btn.data('brand'));
        $('#editEqPn').val($btn.data('pn'));
        cleaveEditPrice.setRawValue($btn.data('price'));
        $('#editEqError').addClass('d-none').text('');
        $('#editEquivalentModal').modal('show');
    });

    $('#saveEditEquivalent').on('click', function () {
        var id    = $('#editEqId').val();
        var brand = $('#editEqBrand').val().trim();
        var pn    = $('#editEqPn').val().trim();
        var price = cleaveEditPrice.getRawValue();

        if (!brand || price === '') {
            $('#editEqError').removeClass('d-none').text('Brand dan Harga Jual wajib diisi.');
            return;
        }

        $.ajax({
            url: '/part-inquiry/serial/' + id + '/equivalent',
            type: 'PATCH',
            data: { _token: '{{ csrf_token() }}', brand: brand, pn: pn, price: price },
            success: function (res) {
                if (res.success) {
                    var $row = $('#equivalentRows tr[data-id="' + id + '"]');
                    $row.find('.eq-brand').text(res.data.brand);
                    $row.find('.eq-pn').text(res.data.pn);
                    $row.find('.eq-price').text(formatRupiahSimple(res.data.price));
                    $row.find('.btn-edit-equivalent')
                        .data('brand', res.data.brand)
                        .data('pn', res.data.pn)
                        .data('price', res.data.price);
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
</script>
@endpush
