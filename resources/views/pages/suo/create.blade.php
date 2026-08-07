@extends('layouts.sales.app')
@section('title', 'Buat SUO')
@section('content')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">SUO /</span> Buat Urgent Order
    </h4>

    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('suo.store') }}" method="POST">
        @csrf
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Informasi Order</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    {{-- No SUO --}}
                    <div class="col-md-3">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control fw-bold bg-light" name="no_suo"
                                value="{{ $noSuo }}" readonly>
                            <label>No. SUO</label>
                        </div>
                    </div>

                    {{-- Company (select dari data client) --}}
                    <div class="col-md-3">
                        <label class="form-label mb-1">Perusahaan</label>
                        <select id="select-company" class="select2 form-select" name="company" required>
                            <option value="">-- Pilih Perusahaan --</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->company }}"
                                    data-id="{{ $client->id }}"
                                    data-address1="{{ $client->address ?? '' }}"
                                    data-address2="{{ $client->subAddress ?? '' }}"
                                    {{ old('company') == $client->company ? 'selected' : '' }}>
                                    {{ $client->company }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PIC (dynamic load berdasarkan company) --}}
                    <div class="col-md-3">
                        <label class="form-label mb-1">PIC Customer</label>
                        <select id="select-pic" class="select2 form-select" name="pic" required>
                            <option value="">-- Pilih Company Dahulu --</option>
                        </select>
                    </div>

                    {{-- Address (select dari 2 alamat client) --}}
                    <div class="col-md-3">
                        <label class="form-label mb-1">Alamat Pengiriman</label>
                        <select id="select-address" class="select2 form-select" name="address" required>
                            <option value="">-- Pilih Company Dahulu --</option>
                        </select>
                    </div>

                    {{-- Notes --}}
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" name="notes" placeholder="Catatan tambahan"
                                style="height: 50px;">{{ old('notes') }}</textarea>
                            <label>Catatan (Opsional)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Item</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-item">
                    <i class="mdi mdi-plus me-1"></i> Tambah Item
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="item-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40%">Nama Item / Part</th>
                            <th style="width:15%">Qty</th>
                            <th style="width:20%">Satuan</th>
                            <th style="width:20%">Catatan</th>
                            <th style="width:5%"></th>
                        </tr>
                    </thead>
                    <tbody id="item-body">
                        <tr>
                            <td><input type="text" class="form-control" name="item_name[]" placeholder="Nama item/part" required></td>
                            <td><input type="number" class="form-control" name="qty[]" value="1" min="1" required></td>
                            <td><input type="text" class="form-control" name="unit[]" placeholder="pcs, set, unit..."></td>
                            <td><input type="text" class="form-control" name="item_notes[]" placeholder="Opsional"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove"><i class="mdi mdi-close"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-end mb-4">
            <a href="{{ route('suo.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
                <i class="mdi mdi-send-outline me-1"></i> Kirim SUO ke Gudang
            </button>
        </div>
    </form>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css"/>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('script')
<script>
$(function () {
    // Init Select2
    $('#select-company').select2({ placeholder: '-- Pilih Perusahaan --', allowClear: true });
    $('#select-pic').select2({ placeholder: '-- Pilih Company Dahulu --', allowClear: true });
    $('#select-address').select2({ placeholder: '-- Pilih Company Dahulu --', allowClear: true });

    // Saat company dipilih → load PIC + populate alamat
    $('#select-company').on('change', function () {
        var selected  = $(this).find(':selected');
        var clientId  = selected.data('id');
        var address1  = selected.data('address1') || '';
        var address2  = selected.data('address2') || '';

        // Populate address dropdown
        $('#select-address').empty().append('<option value="">-- Pilih Alamat --</option>');
        if (address1) {
            $('#select-address').append('<option value="' + address1 + '">' + address1 + '</option>');
        }
        if (address2) {
            $('#select-address').append('<option value="' + address2 + '">' + address2 + '</option>');
        }
        if (!address1 && !address2) {
            $('#select-address').append('<option value="" disabled>Tidak ada alamat tersimpan</option>');
        }
        $('#select-address').trigger('change');

        // Reset PIC dropdown
        $('#select-pic').empty().append('<option value="">-- Pilih Company Dahulu --</option>').trigger('change');

        if (!clientId) return;

        // Load PIC via existing endpoint
        $.get('/quotation/pic/' + clientId, function (data) {
            $('#select-pic').empty().append('<option value="">-- Pilih PIC --</option>');
            if (data.length === 0) {
                $('#select-pic').append('<option value="" disabled>Tidak ada PIC</option>');
            } else {
                $.each(data, function (i, pic) {
                    $('#select-pic').append(
                        '<option value="' + pic.name_pic + '">' + pic.name_pic +
                        (pic.position ? ' (' + pic.position + ')' : '') + '</option>'
                    );
                });
            }
            $('#select-pic').trigger('change');
        });
    });

    // Tambah baris item
    var rowTemplate = `<tr>
        <td><input type="text" class="form-control" name="item_name[]" placeholder="Nama item/part" required></td>
        <td><input type="number" class="form-control" name="qty[]" value="1" min="1" required></td>
        <td><input type="text" class="form-control" name="unit[]" placeholder="pcs, set, unit..."></td>
        <td><input type="text" class="form-control" name="item_notes[]" placeholder="Opsional"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-outline-danger btn-remove"><i class="mdi mdi-close"></i></button></td>
    </tr>`;

    $('#btn-add-item').on('click', function () {
        $('#item-body').append(rowTemplate);
    });

    $(document).on('click', '.btn-remove', function () {
        if ($('#item-body tr').length > 1) {
            $(this).closest('tr').remove();
        }
    });
});
</script>
@endpush
