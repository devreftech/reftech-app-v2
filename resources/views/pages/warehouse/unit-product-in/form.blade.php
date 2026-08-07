@extends('layouts.sales.app')
@section('title', 'Input Barang Masuk Unit')
@section('content')
    <form action="{{ route('unit-product-in.store') }}" method="post">
        @csrf
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card mb-3">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12 col-lg-4">
                        <div class="form-floating form-floating-outline mb-2">
                            <input type="text" class="form-control" value="{{ $nextNoTransaksi }}" disabled>
                            <label>No Transaksi (preview)</label>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-floating form-floating-outline mb-2">
                            <select id="transaction_type" name="transaction_type" class="form-select" required>
                                <option value="">Pilih Tipe Transaksi...</option>
                                <option value="purchase_new" disabled>Beli Unit Baru (input lewat Purchase Order)</option>
                                <option value="purchase_used" {{ old('transaction_type') == 'purchase_used' ? 'selected' : '' }}>Beli Unit Second (dari Customer)</option>
                                <option value="trade_in" {{ old('transaction_type') == 'trade_in' ? 'selected' : '' }}>Trade-In (Tukar Tambah)</option>
                            </select>
                            <label>Tipe Transaksi</label>
                            <small class="text-muted">Unit baru dari supplier sekarang dibuat lewat
                                <a href="{{ route('purchase.create') }}">Purchase Order</a>, lalu diterima via
                                Goods Receipt.</small>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-floating form-floating-outline mb-2">
                            <input class="form-control" type="date" name="date" required
                                value="{{ old('date', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                            <label>Tanggal</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-lg-6 field-supplier">
                        <div class="form-floating form-floating-outline mb-2">
                            <select id="supplier-dropdown" class="select2 form-select" name="id_supplier">
                                <option value="">Pilih Supplier...</option>
                                @foreach ($suppliers as $supp)
                                    <option value="{{ $supp->id }}">{{ $supp->supplier }}</option>
                                @endforeach
                            </select>
                            <label>Supplier</label>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 field-customer">
                        <div class="form-floating form-floating-outline mb-2">
                            <input type="text" class="form-control" name="id_customer" placeholder="Nama Customer"
                                value="{{ old('id_customer') }}">
                            <label>Nama Customer</label>
                        </div>
                    </div>
                </div>

                <div class="form-invoice-repeater source-item">
                    <div class="mb-2" data-repeater-list="group-a">
                        <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                            <div class="d-flex border rounded position-relative pe-0">
                                <div class="row w-100 p-3">
                                    <div class="col-md-4 col-12 mb-md-0 mb-3">
                                        <label class="mb-2">Spek Unit (Katalog)</label>
                                        <select class="form-select select2-unit" name="id_unit[]" required>
                                            <option value="">Pilih Unit...</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->brand }} {{ $unit->model }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-12 mb-md-0 mb-3">
                                        <label class="mb-2">Serial Number</label>
                                        <input type="text" class="form-control" name="serial_number[]">
                                    </div>
                                    <div class="col-md-3 col-12 mb-md-0 mb-3">
                                        <label class="mb-2">Harga</label>
                                        <input type="number" class="form-control" name="harga[]" min="0" required>
                                    </div>
                                    <div class="col-md-3 col-12 mb-md-0 mb-3">
                                        <label class="mb-2 field-biaya-label">Biaya Tambahan</label>
                                        <input type="number" class="form-control" name="biaya_tambahan[]" min="0"
                                            value="0">
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-center justify-content-between border-start p-2">
                                    <i class="mdi mdi-close cursor-pointer bg-danger text-white btn-del" data-repeater-delete=""></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" data-repeater-create="">
                        <i class="mdi mdi-plus me-1"></i> Tambah Unit
                    </button>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <label class="mb-2">Note</label>
                        <textarea class="form-control" name="note" rows="2">{{ old('note') }}</textarea>
                    </div>
                </div>

                <div class="float-end mt-4">
                    <a href="{{ route('unit-product-in.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/includes/repeater/jquery-repeater-invoice.js"></script>
@endpush
@push('script')
    <script>
        $(function() {
            $('.form-invoice-repeater').repeater({
                show: function() {
                    $(this).find('.select2-unit').select2({
                        dropdownParent: $(this),
                        width: '100%'
                    });
                    $(this).slideDown();
                },
                remove: function(e) {
                    confirm('Yakin hapus baris ini?') && $(this).remove(e);
                }
            });

            $('.select2-unit').select2({
                width: '100%'
            });
            $('#supplier-dropdown').select2({
                width: '100%'
            });

            function toggleByType() {
                var type = $('#transaction_type').val();
                if (type === 'purchase_new') {
                    $('.field-supplier').show();
                    $('.field-customer').hide();
                    $('.field-biaya-label').text('Biaya Rebranding');
                } else {
                    $('.field-supplier').hide();
                    $('.field-customer').show();
                    $('.field-biaya-label').text('Biaya Perbaikan');
                }
            }
            $('#transaction_type').on('change', toggleByType);
            toggleByType();
        });
    </script>
@endpush
