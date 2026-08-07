@extends('layouts.sales.app')
@section('title', 'Input Unit Keluar')
@section('content')
    <form action="{{ route('unit-product-out.store') }}" method="post">
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
                            <input class="form-control" type="date" name="date" required
                                value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                            <label>Tanggal</label>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="form-floating form-floating-outline mb-2">
                            <input type="text" class="form-control" name="customer" placeholder="Nama Customer">
                            <label>Customer</label>
                        </div>
                    </div>
                </div>

                <div class="form-invoice-repeater source-item">
                    <div class="mb-2" data-repeater-list="group-a">
                        <div class="repeater-wrapper pt-0 pt-md-4" data-repeater-item="">
                            <div class="d-flex border rounded position-relative pe-0">
                                <div class="row w-100 p-3">
                                    <div class="col-md-3 col-12 mb-md-0 mb-3">
                                        <label class="mb-2">Sumber Unit</label>
                                        <select class="form-select field-source" required>
                                            <option value="">Pilih Unit...</option>
                                            <optgroup label="Unit Baru (Inventory)">
                                                @foreach ($unitInventories as $inv)
                                                    <option value="unit_inventory:{{ $inv->id }}">
                                                        {{ $inv->unit->brand ?? '' }} {{ $inv->unit->model ?? '' }} —
                                                        SN {{ $inv->serial_number }} (Modal Rp
                                                        {{ number_format($inv->total_modal, 0, ',', '.') }})
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                            <optgroup label="Unit Second (Fixed Asset)">
                                                @foreach ($fixedAssets as $fa)
                                                    <option value="fixed_asset:{{ $fa->id }}">
                                                        {{ $fa->code }} —
                                                        {{ $fa->unit->brand ?? '' }} {{ $fa->unit->model ?? '' }} — SN
                                                        {{ $fa->serial_number }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                        <input type="hidden" name="source_type[]" class="field-source-type">
                                        <input type="hidden" name="source_id[]" class="field-source-id">
                                    </div>
                                    <div class="col-md-3 col-12 mb-md-0 mb-3">
                                        <label class="mb-2">Harga Jual</label>
                                        <input type="number" class="form-control" name="harga_jual[]" min="0"
                                            required>
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
                        <textarea class="form-control" name="note" rows="2"></textarea>
                    </div>
                </div>

                <div class="float-end mt-4">
                    <a href="{{ route('unit-product-out.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/includes/repeater/jquery-repeater-invoice.js"></script>
@endpush
@push('script')
    <script>
        $(function() {
            function bindSourceChange(scope) {
                scope.find('.field-source').off('change').on('change', function() {
                    var val = $(this).val();
                    var parts = val.split(':');
                    $(this).siblings('.field-source-type').val(parts[0] || '');
                    $(this).siblings('.field-source-id').val(parts[1] || '');
                });
            }
            bindSourceChange($(document));

            $('.form-invoice-repeater').repeater({
                show: function() {
                    bindSourceChange($(this));
                    $(this).slideDown();
                },
                remove: function(e) {
                    confirm('Yakin hapus baris ini?') && $(this).remove(e);
                }
            });
        });
    </script>
@endpush
