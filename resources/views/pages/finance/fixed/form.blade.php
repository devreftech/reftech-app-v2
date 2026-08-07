@extends('layouts.sales.app')
@section('title', isset($fixed) ? 'Edit Fixed Asset' : 'Create Fixed Asset')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Finance / <a href="{{ route('fixed.index', ['type' => $fixed->type ?? '']) }}">Fixed Asset</a> /</span> {{ isset($fixed) ? 'Edit' : 'Create' }}
    </h4>
    <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework"
        action="{{ isset($fixed) ? route('fixed.update', $fixed->id) : route('fixed.store') }}" method="post"
        enctype="multipart/form-data">
        @csrf
        @if (isset($fixed))
            @method('PATCH')
        @endif
        <div class="card mb-3">
            <div class="card-body">
                <div class="form-invoice-repeater source-item">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-floating form-floating-outline mb-2">
                                <input class="form-control" type="text" placeholder="Pilih kategori dulu untuk generate kode ...."
                                    id="no-code-input" name="code" value="{{ old('code', $fixed->code ?? '') }}">
                                <label for="no-code-input">Code</label>
                            </div>
                            @if (! isset($fixed))
                                <small class="text-muted">Kode otomatis terisi setelah pilih kategori, tapi tetap bisa diedit manual.</small>
                            @endif
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select invoice-item-type" id="type" data-id="1"
                                    aria-label="Default select example" name="type"
                                    {{ isset($fixed) ? 'disabled' : '' }}>
                                    <option>---Type Penyusutan---</option>
                                    <option value="Tanah" {{ ($fixed->type ?? '') == 'Tanah' ? 'selected' : '' }}>Tanah
                                    </option>
                                    <option value="Bangunan" {{ ($fixed->type ?? '') == 'Bangunan' ? 'selected' : '' }}>Bangunan
                                    </option>
                                    <option value="Kendaraan" {{ ($fixed->type ?? '') == 'Kendaraan' ? 'selected' : '' }}>Kendaraan
                                    </option>
                                    <option value="Mesin" {{ ($fixed->type ?? '') == 'Mesin' ? 'selected' : '' }} {{ !isset($fixed) ? 'disabled' : '' }}>Mesin{{ !isset($fixed) ? ' (input lewat Barang Masuk Unit)' : '' }}
                                    </option>
                                    <option value="Peralatan Kantor" {{ ($fixed->type ?? '') == 'Peralatan Kantor' ? 'selected' : '' }}>Peralatan Kantor
                                    </option>
                                    <option value="Tools" {{ ($fixed->type ?? '') == 'Tools' ? 'selected' : '' }}>Tools
                                    </option>
                                </select>
                                <label for="type">Code</label>
                            </div>
                            @if (isset($fixed))
                                <input type="hidden" name="type" value="{{ $fixed->type }}">
                                <small class="text-muted">Kategori tidak bisa diubah lewat edit.</small>
                            @else
                                <small class="text-muted">
                                    Kategori Mesin (unit) diinput lewat
                                    <a href="{{ route('unit-product-in.create') }}">Barang Masuk Unit</a>.
                                </small>
                            @endif
                        </div>
                        <div class="col-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="Date" name="date"
                                    value="{{ old('date', $fixed->bayar ?? '') }}">
                                <label for="Date">Tanggal Beli</label>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <input class="form-control" type="date" id="pakai" name="pakai"
                                    value="{{ old('pakai', $fixed->pakai ?? '') }}">
                                <label for="pakai">Tanggal Pakai</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline mb-4">
                            <select id="supplier-dropdown" class="select2 form-select invoice-item-supplier"
                                data-allow-clear="true" name="supplier" data-id="1">
                                <option selected>Pilih Supplier...</option>
                                @foreach ($suppliers as $supp)
                                    <option value="{{ $supp->id }}" data-info="{{ $supp->info }}"
                                        {{ ($fixed->id_supplier ?? null) == $supp->id ? 'selected' : '' }}>
                                        {{ $supp->supplier }}
                                    </option>
                                @endforeach
                            </select>
                            {{-- <label for="supplier-dropdown">Supplier</label> --}}
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating form-floating-outline mb-2">
                            <input class="form-control" type="text" placeholder="Put No Voucher Here ...."
                                id="no-voucher-input" name="no_invoice" value="{{ old('no_invoice', $fixed->no_invoice ?? '') }}">
                            <label for="no-voucher-input">No Invoice</label>
                        </div>
                    </div>
                </div>
                <div class="d-flex border rounded position-relative pe-0 mb-3">
                    <div class="row w-100 p-3">
                        @php
                            $isMesin = ($fixed->type ?? old('type')) == 'Mesin';
                            $isKendaraan = ($fixed->type ?? old('type')) == 'Kendaraan';
                        @endphp
                        <div class="col-md-6 col-12 mb-md-0">
                            <label for="Keterangan" class="mb-2">Keterangan</label>
                            <div class="form-floating form-floating-outline mb-2" id="desc-text-wrapper"
                                style="{{ $isMesin ? 'display:none;' : '' }}">
                                <input class="form-control" type="text" placeholder="Put Keterangan Here ...."
                                    id="desc-input" name="desc" value="{{ old('desc', $fixed->desc ?? '') }}">
                            </div>
                            <div class="form-floating form-floating-outline mb-2" id="desc-unit-wrapper"
                                style="{{ $isMesin ? '' : 'display:none;' }}">
                                <select id="unit-dropdown" class="select2 form-select" data-allow-clear="true"
                                    name="id_unit">
                                    <option></option>
                                    @foreach ($units as $u)
                                        @php
                                            $unitLabel = collect([$u->unit, $u->brand, $u->model, $u->sku])
                                                ->filter()
                                                ->join(' - ');
                                        @endphp
                                        <option value="{{ $u->id }}" data-label="{{ $unitLabel }}"
                                            {{ ($fixed->id_unit ?? null) == $u->id ? 'selected' : '' }}>
                                            {{ $unitLabel }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="unit-dropdown">Cari Unit (Unit Global)</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-2" id="serial-number-wrapper"
                                style="{{ $isMesin ? '' : 'display:none;' }}">
                                <input class="form-control" type="text" placeholder="Put Serial Number Here ...."
                                    id="serial-number-input" name="serial_number"
                                    value="{{ old('serial_number', $fixed->serial_number ?? '') }}">
                                <label for="serial-number-input">Serial Number Mesin</label>
                            </div>
                            <div class="mb-2" id="kondisi-wrapper" style="{{ $isMesin ? '' : 'display:none;' }}">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kondisi" id="kondisi-second"
                                        value="Second"
                                        {{ (!isset($fixed) && old('kondisi', 'Second') == 'Second') || ($fixed->kondisi ?? '') == 'Second' ? 'checked' : '' }}
                                        {{ isset($fixed) ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="kondisi-second">Unit Second (perlu dicek dulu)</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kondisi" id="kondisi-baru"
                                        value="Baru" {{ ($fixed->kondisi ?? old('kondisi')) == 'Baru' ? 'checked' : '' }}
                                        {{ isset($fixed) ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="kondisi-baru">Unit Baru (langsung siap)</label>
                                </div>
                            </div>
                            @if (isset($fixed) && $isMesin)
                                <input type="hidden" name="kondisi" value="{{ $fixed->kondisi }}">
                                <small class="text-muted d-block">Kondisi unit tidak bisa diubah lewat edit.</small>
                            @endif
                            <div id="kendaraan-fields-wrapper" style="{{ $isKendaraan ? '' : 'display:none;' }}">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <select class="form-select" id="jenis-kendaraan-input" name="jenis_kendaraan">
                                                <option value="">Pilih Jenis</option>
                                                <option value="Mobil" {{ ($fixed->jenis_kendaraan ?? old('jenis_kendaraan')) == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                                <option value="Motor" {{ ($fixed->jenis_kendaraan ?? old('jenis_kendaraan')) == 'Motor' ? 'selected' : '' }}>Motor</option>
                                            </select>
                                            <label for="jenis-kendaraan-input">Jenis Kendaraan</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <select class="form-select" id="bahan-bakar-input" name="bahan_bakar">
                                                <option value="">Pilih Bahan Bakar</option>
                                                <option value="Solar" {{ ($fixed->bahan_bakar ?? old('bahan_bakar')) == 'Solar' ? 'selected' : '' }}>Solar</option>
                                                <option value="Pertalite" {{ ($fixed->bahan_bakar ?? old('bahan_bakar')) == 'Pertalite' ? 'selected' : '' }}>Pertalite</option>
                                                <option value="Listrik" {{ ($fixed->bahan_bakar ?? old('bahan_bakar')) == 'Listrik' ? 'selected' : '' }}>Listrik</option>
                                            </select>
                                            <label for="bahan-bakar-input">Bahan Bakar</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input class="form-control" type="text" placeholder="Put Merk/Model Here ...."
                                                id="merk-model-input" name="merk_model"
                                                value="{{ old('merk_model', $fixed->merk_model ?? '') }}">
                                            <label for="merk-model-input">Merk / Model</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input class="form-control" type="text" placeholder="Put Plat Nomor Here ...."
                                                id="plat-nomor-input" name="plat_nomor"
                                                value="{{ old('plat_nomor', $fixed->plat_nomor ?? '') }}">
                                            <label for="plat-nomor-input">Plat Nomor</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating form-floating-outline mb-2">
                                            <input class="form-control" type="text" placeholder="Put Nama Pemilik STNK Here ...."
                                                id="atas-nama-input" name="atas_nama"
                                                value="{{ old('atas_nama', $fixed->atas_nama ?? '') }}">
                                            <label for="atas-nama-input">Atas Nama (STNK)</label>
                                        </div>
                                        <small class="text-muted d-block">Diisi kalau STNK bukan atas nama perusahaan.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-12 mb-md-0 mb-3">
                            <p class="mb-2 repeater-title">Qty</p>
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="number" class="form-control mb-3 invoice-item-qty" placeholder="Min 1"
                                    name="qty" id="qty-1" data-id="1" min="1" value="{{ old('qty', $fixed->qty ?? '') }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-12 mb-md-0 mb-3">
                            <p class="mb-2 repeater-title">Total</p>
                            <div class="input-group input-group-merge mb-3" data-total="1">
                                <span class="input-group-text">Rp. </span>
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control invoice-item-amount-label" id="totalLabel-1"
                                        data-id="1" name="harga" placeholder="Put total Here" data-type="currency"
                                        min="0" pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                        @blur="focused = false"
                                        value="{{ old('total', isset($fixed) ? number_format($fixed->total, 0, ',', '.') : '') }}">
                                </div>
                            </div>
                            <input class="form-control invoice-item-amount" type="number" name="total"
                                id="amount-1" value="{{ old('total', $fixed->total ?? '') }}" hidden>
                        </div>
                    </div>
                </div>
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link waves-effect active" data-bs-toggle="tab"
                            data-bs-target="#form-tabs-personal" role="tab" aria-selected="true">
                            Umum
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link waves-effect" data-bs-toggle="tab"
                            data-bs-target="#form-tabs-account" role="tab" aria-selected="false" tabindex="-1">
                            Pengeluaran
                        </button>
                    </li>
                    <span class="tab-slider" style="left: 0px; width: 165.812px; bottom: 0px;"></span>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="form-tabs-personal" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" class="form-control invoice-item-umur" placeholder="Min 1"
                                        name="umur" id="umur" data-id="1" min="1"
                                        value="{{ old('umur', $fixed->umur ?? '') }}">
                                    <label for="umur">Umur Bulan Aktiva</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select invoice-item-metode" id="metode-1" data-id="1"
                                        aria-label="Default select example" name="metode">
                                        <option>---Metode Penyusutan---</option>
                                        <option value="Metode Garis Lurus" {{ ($fixed->metode ?? '') == 'Metode Garis Lurus' ? 'selected' : '' }}>Metode Garis Lurus
                                        </option>
                                        <option value="Metode Saldo Menurun" {{ ($fixed->metode ?? '') == 'Metode Saldo Menurun' ? 'selected' : '' }}>Metode Saldo Menurun
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="aktiva-1" class="select2 form-select invoice-item-aktiva"
                                        data-allow-clear="true" name="aktiva" data-id="1">
                                        <option> ---- Choose Account Here ---- </option>
                                        @foreach ($account as $accounts)
                                            <option value="{{ $accounts->id }}" data-memo="{{ $accounts->category }}"
                                                {{ ($fixed->id_aktiva ?? null) == $accounts->id ? 'selected' : '' }}>
                                                {{ $accounts->code }} - {{ $accounts->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="aktiva">Akun Aktiva</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="penyusutan-1" class="select2 form-select invoice-item-penyusutan"
                                        data-allow-clear="true" name="penyusutan" data-id="1">
                                        <option> ---- Choose Account Here ---- </option>
                                        @foreach ($account as $accounts)
                                            <option value="{{ $accounts->id }}" data-memo="{{ $accounts->category }}"
                                                {{ ($fixed->id_penyusutan ?? null) == $accounts->id ? 'selected' : '' }}>
                                                {{ $accounts->code }} - {{ $accounts->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="penyusutan">Akun Akun Penyusutan</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="beban-1" class="select2 form-select invoice-item-beban"
                                        data-allow-clear="true" name="beban" data-id="1">
                                        <option> ---- Choose Account Here ---- </option>
                                        @foreach ($account as $accounts)
                                            <option value="{{ $accounts->id }}" data-memo="{{ $accounts->category }}"
                                                {{ ($fixed->id_beban ?? null) == $accounts->id ? 'selected' : '' }}>
                                                {{ $accounts->code }} - {{ $accounts->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="beban">Akun Beban Penyusutan</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="form-tabs-account" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="bank-1" class="select2 form-select invoice-item-bank"
                                        data-allow-clear="true" name="bank" data-id="1">
                                        <option> ---- Choose Account Here ---- </option>
                                        @foreach ($account as $accounts)
                                            <option value="{{ $accounts->id }}" data-memo="{{ $accounts->category }}"
                                                {{ ($fixed->id_pengeluaran ?? null) == $accounts->id ? 'selected' : '' }}>
                                                {{ $accounts->code }} - {{ $accounts->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="bank">Akun Bank</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline mb-4">
                                    <input class="form-control" type="date" id="pay" name="pay"
                                        value="{{ old('pay', $fixed->beli ?? '') }}">
                                    <label for="pay">Tanggal Pay</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select invoice-item-status" id="status-1" data-id="1"
                                        aria-label="Default select example" name="status">
                                        <option>---Status Payment---</option>
                                        <option value="1" {{ (string) ($fixed->status ?? '') === '1' ? 'selected' : '' }}>Sudah dibayar
                                        </option>
                                        <option value="0" {{ isset($fixed) && (string) $fixed->status === '0' ? 'selected' : '' }}>Belum Dibayar
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="float-end">
                    <a href="{{ isset($fixed) ? route('fixed.show', $fixed->id) : route('fixed.index', ['type' => $fixed->type ?? '']) }}" type="button"
                        class="btn btn-lg btn-outline-secondary">
                        Back
                    </a>
                    <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                        {{ isset($fixed) ? 'Update' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
        </div>
    </form>
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/includes/repeater/jquery-repeater-invoice.js"></script>
    {{-- <script src="{{ asset('assets') }}/js/app-invoice-add.js"></script> --}}
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/repeater/repeater-invoice-expense.js"></script>
    {{-- <script src="{{ asset('assets') }}/includes/validator/quotation-validation.js"></script> --}}
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        $(() => {
            function capitalizeWords(str) {
                return str.replace(/\b\w/g, function(c) {
                    return c.toUpperCase();
                });
            }

            function terbilang(n) {
                const angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan",
                    "sepuluh", "sebelas"
                ];

                n = parseInt(n);

                if (n < 12) return angka[n];
                if (n < 20) return terbilang(n - 10) + " belas";
                if (n < 100) return terbilang(Math.floor(n / 10)) + " puluh " + terbilang(n % 10);
                if (n < 200) return "seratus " + terbilang(n - 100);
                if (n < 1000) return terbilang(Math.floor(n / 100)) + " ratus " + terbilang(n % 100);
                if (n < 2000) return "seribu " + terbilang(n - 1000);
                if (n < 1000000) return terbilang(Math.floor(n / 1000)) + " ribu " + terbilang(n % 1000);
                if (n < 1000000000) return terbilang(Math.floor(n / 1000000)) + " juta " + terbilang(n % 1000000);
                if (n < 1000000000000) return terbilang(Math.floor(n / 1000000000)) + " miliar " + terbilang(n %
                    1000000000);
                if (n < 1000000000000000) return terbilang(Math.floor(n / 1000000000000)) + " triliun " + terbilang(
                    n % 1000000000000);

                return "";
            }
            let formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            });
            const numberFormatter = new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
            $(".invoice-item-bank").on('change', function() {
                var saldo = $('option:selected', this).data('saldo');
                console.log(saldo);

                $('.invoice-item-saldo-label').val(numberFormatter.format(saldo));
            });
            $(".invoice-item-amount-label").on('keyup', function() {
                var input = $(this)
                var id = input.data('id');
                var input_val = input.val();

                // original length
                var original_len = input_val.length;

                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // send updated string to input
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                console.log(nomorInt);
                $(`#amount-${id}`).val(nomorInt);
            });
            $('.invoice-item-amount-label').on('keyup change click', function() {

                var total = 0;

                $('.invoice-item-amount').each(function() {
                    total += parseInt($(this).val()) || 0;
                });

                $('#total-label').val(numberFormatter.format(total));
                $('#total').val(total);
                let hasilTerbilang = capitalizeWords(terbilang(total).trim());
                if (hasilTerbilang === "") hasilTerbilang = "-";

                $('.invoice-item-say-total').text("Say amount: " + hasilTerbilang + " Rupiah");
            });

            function initializeSelect2Account() {
                $('.invoice-item-account').select2({
                    placeholder: ' ---- Choose Account Here ---- ',
                    allowClear: true,
                    width: '100%',
                });
            }
            // Initialize Bootstrap tooltips using jQuery
            $(document).ready(function() {
                $('[data-bs-toggle="tooltip"]').tooltip();

                $('#unit-dropdown').select2({
                    placeholder: ' ---- Cari Unit (Unit Global) ---- ',
                    allowClear: true,
                    width: '100%',
                });

                $('#type').on('change', function() {
                    if ($(this).val() === 'Mesin') {
                        $('#desc-text-wrapper').hide();
                        $('#desc-unit-wrapper').show();
                        $('#serial-number-wrapper').show();
                        $('#kondisi-wrapper').show();
                        $('#kendaraan-fields-wrapper').hide();
                        $('#desc-input').val('');
                    } else if ($(this).val() === 'Kendaraan') {
                        $('#desc-unit-wrapper').hide();
                        $('#serial-number-wrapper').hide();
                        $('#kondisi-wrapper').hide();
                        $('#desc-text-wrapper').show();
                        $('#kendaraan-fields-wrapper').show();
                    } else {
                        $('#desc-unit-wrapper').hide();
                        $('#serial-number-wrapper').hide();
                        $('#kondisi-wrapper').hide();
                        $('#kendaraan-fields-wrapper').hide();
                        $('#desc-text-wrapper').show();
                    }
                });

                @if (! isset($fixed))
                    // Kode aset otomatis mengikuti kategori terpilih, tapi berhenti auto-generate
                    // begitu user mengetik manual di field-nya (biar tidak menimpa input user).
                    var codeManuallyEdited = false;
                    $('#no-code-input').on('input', function() {
                        codeManuallyEdited = true;
                    });
                    $('#type').on('change', function() {
                        var type = $(this).val();
                        if (codeManuallyEdited || !type) {
                            return;
                        }
                        $.get('{{ route('fixed.next-code') }}', {
                            type: type
                        }, function(res) {
                            if (res && res.code) {
                                $('#no-code-input').val(res.code);
                            }
                        });
                    });
                @endif

                $('#unit-dropdown').on('change', function() {
                    var label = $(this).find(':selected').data('label') || '';
                    $('#desc-input').val(label);
                });

                // Panggil fungsi inisialisasi saat halaman dimuat
                initializeSelect2Account();

                // Jika ada elemen dinamis yang ditambahkan, gunakan event listener
                $(document).on('repeater:added', function() {
                    initializeSelect2Account();
                });
            });
            $('.invoice-item-account').on('change', function() {
                var id = $(this).data('id');
                var memo = $('option:selected', this).data('memo');
                console.log(memo);

                $(`#memo-label-${id}`).val(memo);
                $(`#memo-${id}`).val(memo);
            });

            $('.btn-add').on('click', () => {
                initializeSelect2Account();

                $('.invoice-item-account').on('change', function() {
                    var id = $(this).data('id');
                    var memo = $('option:selected', this).data('memo');
                    console.log(memo);

                    $(`#memo-label-${id}`).val(memo);
                    $(`#memo-${id}`).val(memo);
                });
                $(".invoice-item-amount-label").on('keyup', function() {
                    var input = $(this)
                    var id = input.data('id');
                    var input_val = input.val();

                    // original length
                    var original_len = input_val.length;

                    // add commas to number
                    // remove all non-digits
                    input_val = formatNumber(input_val);
                    input_val = input_val;

                    // send updated string to input
                    input.val(input_val);
                    var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                    console.log(nomorInt);
                    $(`#amount-${id}`).val(nomorInt);
                });
                $('.invoice-item-amount-label').on('keyup change click', function() {

                    var total = 0;

                    $('.invoice-item-amount').each(function() {
                        total += parseInt($(this).val()) || 0;
                    });

                    $('#total-label').val(numberFormatter.format(total));
                    $('#total').val(total);
                    let hasilTerbilang = capitalizeWords(terbilang(total).trim());
                    if (hasilTerbilang === "") hasilTerbilang = "-";

                    $('.invoice-item-say-total').text("Say amount: " + hasilTerbilang + " Rupiah");
                });


            });
            $(document).on('click', '.delete-expense', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    customClass: {
                        confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            'url': '{{ url('expense-acount') }}/' + id,
                            'type': 'POST',
                            'data': {
                                '_method': 'DELETE',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Deleted!",
                                        text: "Your file has been deleted.",
                                        customClass: {
                                            confirmButton: "btn btn-success waves-effect",
                                        },
                                    })
                                    window.setTimeout(function() {
                                        window.location.href =
                                            '/expense-acount';
                                    }, 2000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Data Failed to Delete!'
                                    });
                                }
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        Swal.fire({
                            title: "Cancelled",
                            text: "Your imaginary file is safe :)",
                            icon: "error",
                            customClass: {
                                confirmButton: "btn btn-success waves-effect",
                            },
                        });
                    }
                });
            });
        });
    </script>
@endpush
