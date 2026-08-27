@extends('layouts.sales.app')
@section('title', 'Input Unit Keluar')
@section('content')
    <style>
        .unit-out-page {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .unit-out-page .card,
        .unit-out-page .modern-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.06), 0 0 1px 0 rgba(67, 89, 113, 0.12);
            border-radius: 0.75rem !important;
        }

        .unit-out-page .item-row {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 0.75rem;
            transition: box-shadow .15s ease;
        }

        .unit-out-page .item-row:hover {
            box-shadow: 0 2px 8px 0 rgba(67, 89, 113, 0.1);
        }

        .unit-out-page .item-row .btn-del {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .unit-out-page .field-selisih.text-success { color: #71dd37 !important; }
        .unit-out-page .field-selisih.text-danger { color: #ff3e1d !important; }

        .select2-unit-source .select2-container { width: 100% !important; }
    </style>

    <div class="container-fluid flex-grow-1 container-p-y p-0 unit-out-page">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold mb-1 text-dark">Input Unit Keluar</h4>
                <p class="text-muted mb-0 small">Catat unit yang keluar (terjual) dari stok Unit Baru maupun Unit Second.</p>
            </div>
            <a href="{{ route('unit-product-out.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <form action="{{ route('unit-product-out.store') }}" method="post" id="formUnitOut">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Card Info Transaksi --}}
            <div class="card modern-card mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-truck-outline me-2 text-primary fs-4"></i> Info Transaksi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" value="{{ $nextNoTransaksi }}" disabled>
                                <label>No Transaksi</label>
                                <small class="text-muted d-block mt-1"><i class="mdi mdi-information-outline me-1"></i>Preview — dikunci setelah disimpan.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="date" name="date" required
                                    value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                                <label>Tanggal</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-1">Customer</label>
                            <select class="form-select select2-customer-search" name="customer" id="select-customer">
                            </select>
                            <small class="text-muted d-block mt-1"><i class="mdi mdi-information-outline me-1"></i>Ketik buat cari — cuma customer terdaftar yang muncul.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Item Unit Keluar --}}
            <div class="card modern-card mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-package-variant-closed me-2 text-primary fs-4"></i> Item Unit Keluar
                    </h5>
                    <span class="text-muted small">{{ $unitInventories->count() }} Unit Baru &bull; {{ $fixedAssets->count() }} Unit Second tersedia</span>
                </div>
                <div class="card-body p-4">
                    <div class="form-invoice-repeater source-item">
                        <div data-repeater-list="group-a">
                            <div class="repeater-wrapper" data-repeater-item="">
                                <div class="item-row d-flex position-relative mb-3">
                                    <div class="row w-100 p-3 g-3 align-items-start">
                                        <div class="col-md-5 col-12">
                                            <label class="form-label small text-muted mb-1">Sumber Unit</label>
                                            <select class="form-select select2-unit-source field-source" required>
                                                <option value="">Pilih Unit...</option>
                                                <optgroup label="Unit Baru (Inventory)">
                                                    @foreach ($unitInventories as $inv)
                                                        <option value="unit_inventory:{{ $inv->id }}"
                                                            data-nilai-pokok="{{ $inv->total_modal }}"
                                                            data-badge="Unit Baru">
                                                            {{ $inv->unit->brand ?? '' }} {{ $inv->unit->model ?? '' }} — SN {{ $inv->serial_number }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="Unit Second (Fixed Asset)">
                                                    @foreach ($fixedAssets as $fa)
                                                        <option value="fixed_asset:{{ $fa->id }}"
                                                            data-nilai-pokok="{{ $fa->nilai_buku_preview }}"
                                                            data-badge="Unit Second">
                                                            {{ $fa->code }} — {{ $fa->unit->brand ?? '' }} {{ $fa->unit->model ?? '' }} — SN {{ $fa->serial_number }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                            <input type="hidden" name="source_type[]" class="field-source-type">
                                            <input type="hidden" name="source_id[]" class="field-source-id">
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <label class="form-label small text-muted mb-1">Nilai Pokok</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="text" class="form-control field-nilai-pokok bg-light" readonly placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <label class="form-label small text-muted mb-1">Harga Jual</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control field-harga-jual" name="harga_jual[]" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-12 text-md-end">
                                            <label class="form-label small text-muted mb-1 d-block">&nbsp;</label>
                                            <i class="mdi mdi-close-circle-outline btn-del text-danger fs-4" data-repeater-delete=""
                                                title="Hapus baris ini"></i>
                                        </div>
                                        <div class="col-12">
                                            <span class="badge bg-label-secondary field-source-badge d-none"></span>
                                            <small class="text-muted ms-1">Selisih (Harga Jual &minus; Nilai Pokok):</small>
                                            <span class="fw-semibold field-selisih">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" data-repeater-create="">
                            <i class="mdi mdi-plus me-1"></i> Tambah Unit
                        </button>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-3">
                    <div class="row text-md-end g-2">
                        <div class="col-md-4 col-6">
                            <div class="text-muted small">Total Nilai Pokok</div>
                            <div class="fw-semibold" id="summaryNilaiPokok">Rp 0</div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="text-muted small">Total Harga Jual</div>
                            <div class="fw-semibold" id="summaryHargaJual">Rp 0</div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="text-muted small">Total Selisih</div>
                            <div class="fw-bold fs-5" id="summarySelisih">Rp 0</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Note --}}
            <div class="card modern-card mb-4">
                <div class="card-body p-4">
                    <label class="form-label small text-muted mb-1">Note</label>
                    <textarea class="form-control" name="note" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('unit-product-out.index') }}" class="btn btn-outline-secondary">
                    <i class="mdi mdi-close me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="mdi mdi-content-save-outline me-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js" ></script>
    <script src="{{ asset('assets') }}/includes/repeater/jquery-repeater-invoice.js"></script>
@endpush
@push('script')
    <script>
        $(function () {
            function currency(n) {
                n = Number(n) || 0;
                return "Rp " + n.toLocaleString("id-ID");
            }

            function updateRowSelisih($row) {
                var nilaiPokok = Number($row.find('.field-nilai-pokok').data('raw')) || 0;
                var hargaJual = Number($row.find('.field-harga-jual').val()) || 0;
                var selisih = hargaJual - nilaiPokok;
                var $sel = $row.find('.field-selisih');
                $sel.text(currency(selisih));
                $sel.toggleClass('text-success', selisih >= 0).toggleClass('text-danger', selisih < 0);
                updateSummary();
            }

            function updateSummary() {
                var totalPokok = 0, totalJual = 0;
                $('.item-row').each(function () {
                    totalPokok += Number($(this).find('.field-nilai-pokok').data('raw')) || 0;
                    totalJual += Number($(this).find('.field-harga-jual').val()) || 0;
                });
                var totalSelisih = totalJual - totalPokok;
                $('#summaryNilaiPokok').text(currency(totalPokok));
                $('#summaryHargaJual').text(currency(totalJual));
                $('#summarySelisih').text(currency(totalSelisih))
                    .toggleClass('text-success', totalSelisih >= 0)
                    .toggleClass('text-danger', totalSelisih < 0);
            }

            function bindRow($row) {
                var $select = $row.find('.field-source');

                $select.select2({
                    dropdownParent: $row,
                    width: '100%',
                    placeholder: 'Cari SKU / Brand / Serial Number...',
                });

                $select.on('change', function () {
                    var val = $(this).val();
                    var parts = (val || '').split(':');
                    $row.find('.field-source-type').val(parts[0] || '');
                    $row.find('.field-source-id').val(parts[1] || '');

                    var $opt = $(this).find('option:selected');
                    var nilaiPokok = $opt.data('nilai-pokok') || 0;
                    var badge = $opt.data('badge') || '';

                    $row.find('.field-nilai-pokok').val(currency(nilaiPokok).replace('Rp ', '')).data('raw', nilaiPokok);
                    $row.find('.field-source-badge').text(badge)
                        .toggleClass('bg-label-success', badge === 'Unit Baru')
                        .toggleClass('bg-label-warning', badge === 'Unit Second')
                        .toggleClass('bg-label-secondary', !badge)
                        .toggleClass('d-none', !badge);

                    updateRowSelisih($row);
                });

                $row.find('.field-harga-jual').on('input', function () {
                    updateRowSelisih($row);
                });
            }

            // Customer — select2 AJAX, cuma nge-load pas diketik (bukan dump semua
            // client), dan endpoint-nya udah filter role='Customers' di server.
            // Value option-nya sengaja nama company-nya sendiri (bukan id), soalnya
            // kolom unit_product_out.customer emang varchar bebas, bukan FK ke client.
            $('#select-customer').select2({
                width: '100%',
                placeholder: 'Cari nama customer...',
                allowClear: true,
                minimumInputLength: 2,
                templateResult: function (item) {
                    if (!item.id) return item.text;
                    var c = item.client;
                    var sub = [c.email, c.phone || c.mobile, c.area].filter(Boolean).join(' &middot; ');
                    return $('<div><div>' + (c.company || '-') + '</div>' +
                        '<div class="text-muted" style="font-size:11px;">' + sub + '</div></div>');
                },
                templateSelection: function (item) {
                    return item.text;
                },
                ajax: {
                    url: '/db/client/search',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) { return { q: params.term }; },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (c) {
                                return { id: c.company, text: c.company, client: c };
                            })
                        };
                    },
                },
            });

            bindRow($('.item-row').first());

            $('.form-invoice-repeater').repeater({
                show: function () {
                    $(this).slideDown();
                    bindRow($(this).find('.item-row'));
                },
                remove: function (e) {
                    if (confirm('Yakin hapus baris ini?')) {
                        $(this).find('.select2-unit-source').select2('destroy');
                        $(this).remove(e);
                        updateSummary();
                    }
                }
            });

            $('#formUnitOut').on('submit', function () {
                var ok = true;
                $('.field-source-type').each(function () {
                    if (!$(this).val()) ok = false;
                });
                if (!ok) {
                    alert('Ada baris yang belum pilih Sumber Unit-nya.');
                    return false;
                }
            });
        });
    </script>
@endpush
