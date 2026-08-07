@extends('layouts.sales.app')
@section('title', 'Unit Catalog')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <style>
        .select2-unit-option { line-height: 1.3; }
        .select2-unit-sku { font-weight: 600; font-size: 0.85rem; }
        .select2-unit-sub { font-size: 0.78rem; color: #6c757d; }
        .select2-container--default .select2-selection--single { height: 38px; border: 1px solid #d9dee3; border-radius: 0.375rem; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 36px; padding-left: 12px; color: #495057; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
        .select2-dropdown { border: 1px solid #d9dee3; border-radius: 0.375rem; box-shadow: 0 4px 16px rgba(0,0,0,.12); }
        .select2-container--default .select2-search--dropdown .select2-search__field { border: 1px solid #d9dee3; border-radius: 0.25rem; padding: 6px 10px; }
        .select2-results__option { padding: 8px 12px; }
        .select2-container--default .select2-results__option--highlighted { background-color: #696cff; }
        .select2-container--default .select2-results__option--highlighted .select2-unit-sub { color: rgba(255,255,255,.75); }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Sales /</span> Unit Catalog
        </h4>
        @if (Auth::user()->role == 'Admin')
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddCatalog">
                <i class="mdi mdi-plus me-1"></i> Add Unit
            </button>
        @endif
    </div>

    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        {{-- ── Top-level category tabs ─────────────────────────────────── --}}
        <div class="card-header p-0 border-bottom">
            <ul class="nav nav-tabs px-3 pt-2" id="catalogMainTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-3 py-3" data-bs-toggle="tab"
                        data-bs-target="#tab-main-compressor" type="button" role="tab">
                        <i class="mdi mdi-air-conditioner me-1"></i>Air Compressor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3 py-3" id="tab-main-dryer-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-main-dryer" type="button" role="tab">
                        <i class="mdi mdi-snowflake me-1"></i>Dryer
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3 py-3" id="tab-main-filtration-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-main-filtration" type="button" role="tab">
                        <i class="mdi mdi-filter me-1"></i>Filtration System
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3 py-3" id="tab-main-tank-btn" data-bs-toggle="tab"
                        data-bs-target="#tab-main-tank" type="button" role="tab">
                        <i class="mdi mdi-propane-tank me-1"></i>Air Receiver Tank
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">

            {{-- ── Tab: Air Compressor ─────────────────────────────────── --}}
            <div class="tab-pane fade show active p-0" id="tab-main-compressor" role="tabpanel">
                <ul class="nav nav-tabs px-3 pt-2" id="compressorTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-oil-injected-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-oil-injected"
                            type="button" role="tab">
                            <i class="mdi mdi-water me-1"></i>Oil-Injected
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-oil-free-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-oil-free"
                            type="button" role="tab">
                            <i class="mdi mdi-water-off me-1"></i>Oil-Free
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-compact-btn"
                            data-bs-toggle="tab" data-bs-target="#tab-compact"
                            type="button" role="tab">
                            <i class="mdi mdi-package-variant me-1"></i>Compact
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- Tab: Oil-Injected --}}
                    <div class="tab-pane fade show active p-3" id="tab-oil-injected" role="tabpanel">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <small class="text-muted me-1">Generation:</small>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary active btn-gen-filter" data-gen="">All</button>
                                <button type="button" class="btn btn-outline-primary btn-gen-filter" data-gen="old">Old Model</button>
                                <button type="button" class="btn btn-outline-primary btn-gen-filter" data-gen="new">New Model</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-oil-injected">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th class="text-center">Generation</th>
                                        <th class="text-center">IDR Price</th>
                                        <th class="text-center">Air Capacity</th>
                                        <th class="text-center">Pressure</th>
                                        <th class="text-center">Motor Power</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    {{-- Tab: Oil-Free --}}
                    <div class="tab-pane fade p-3" id="tab-oil-free" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-oil-free">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th class="text-center">IDR Price</th>
                                        <th class="text-center">Air Capacity</th>
                                        <th class="text-center">Pressure</th>
                                        <th class="text-center">Motor Power</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    {{-- Tab: Compact --}}
                    <div class="tab-pane fade p-5 text-center" id="tab-compact" role="tabpanel">
                        <i class="mdi mdi-package-variant-closed mdi-48px text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">Compact compressor data not available yet.</p>
                    </div>
                </div>
            </div>

            {{-- ── Tab: Dryer ──────────────────────────────────────────── --}}
            <div class="tab-pane fade p-0" id="tab-main-dryer" role="tabpanel">
                <div class="px-3 pt-3 pb-1 border-bottom">
                    <ul class="nav nav-pills gap-1" id="dryerSubTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active btn-sm" id="tab-ref-dryer-btn"
                                data-bs-toggle="pill" data-bs-target="#tab-ref-dryer"
                                type="button" role="tab">
                                <i class="mdi mdi-coolant-temperature me-1"></i>Refrigerant
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm" id="tab-desiccant-btn"
                                data-bs-toggle="pill" data-bs-target="#tab-desiccant"
                                type="button" role="tab">
                                <i class="mdi mdi-water-remove me-1"></i>Desiccant
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content p-3">
                    {{-- Sub-tab: Refrigerant --}}
                    <div class="tab-pane fade show active" id="tab-ref-dryer" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-ref-dryer">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th class="text-center">IDR Price</th>
                                        <th class="text-center">FAD</th>
                                        <th class="text-center">Voltage</th>
                                        <th class="text-center">Connection</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    {{-- Sub-tab: Desiccant --}}
                    <div class="tab-pane fade" id="tab-desiccant" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-desiccant">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th class="text-center">IDR Price</th>
                                        <th class="text-center">FAD</th>
                                        <th class="text-center">Voltage</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Tab: Filtration System ──────────────────────────────── --}}
            <div class="tab-pane fade p-3" id="tab-main-filtration" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-filtration">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th class="text-center">IDR Price</th>
                                <th class="text-center">Connection</th>
                                <th class="text-center">Filtration</th>
                                <th class="text-center">Oil Content</th>
                                <th class="text-center">Grade</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            {{-- ── Tab: Air Receiver Tank ──────────────────────────────── --}}
            <div class="tab-pane fade p-3" id="tab-main-tank" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-tank">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th class="text-center">IDR Price</th>
                                <th class="text-center">Capacity</th>
                                <th class="text-center">Pressure</th>
                                <th class="text-center">Type</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Add Unit to Catalog Modal --}}
    @if (Auth::user()->role == 'Admin')
        <div class="modal fade" id="modalAddCatalog" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Unit to Catalog</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('catalog-unit.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Select Unit <span class="text-danger">*</span></label>
                                <select class="form-select w-100" name="id_unit" id="selectUnit" required>
                                    <option value=""></option>
                                    @foreach ($availableUnits as $unit)
                                        <option value="{{ $unit->id }}"
                                            data-sku="{{ $unit->sku }}"
                                            data-brand="{{ $unit->brand }}"
                                            data-model="{{ $unit->model }}"
                                            data-category="{{ $unit->unit }}">
                                            {{ $unit->sku }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted">Units already in the catalog are not shown.</div>
                            </div>

                            <div id="previewUnit" class="alert alert-light border d-none mb-3 py-2">
                                <div class="row g-0 small">
                                    <div class="col-4 text-muted">SKU</div><div class="col-8 fw-semibold" id="prev-sku">-</div>
                                    <div class="col-4 text-muted">Brand</div><div class="col-8" id="prev-brand">-</div>
                                    <div class="col-4 text-muted">Model</div><div class="col-8" id="prev-model">-</div>
                                    <div class="col-4 text-muted">Category</div><div class="col-8" id="prev-category">-</div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">IDR Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control rupiah-input" name="price_idr_display"
                                            placeholder="0" autocomplete="off">
                                        <input type="hidden" name="price_idr" id="price-idr-raw" value="0">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">USD Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="price_usd"
                                            step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Spec Notes</label>
                                <textarea class="form-control" name="spec_note" rows="2"
                                    placeholder="Additional specification notes for this unit..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save to Catalog</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets') }}/includes/table-catalog-unit.js"></script>
    <script>
        var unitData = {
            @foreach ($availableUnits as $unit)
            {{ $unit->id }}: {
                sku:      "{{ addslashes($unit->sku) }}",
                brand:    "{{ addslashes($unit->brand) }}",
                model:    "{{ addslashes($unit->model) }}",
                category: "{{ addslashes($unit->unit) }}"
            },
            @endforeach
        };

        function formatUnitResult(option) {
            if (!option.id) return option.text;
            var d = unitData[option.id];
            if (!d) return option.text;
            return $(
                '<div class="select2-unit-option">' +
                    '<div class="select2-unit-sku">' + d.sku + '</div>' +
                    '<div class="select2-unit-sub">' + (d.brand || '') + ' ' + (d.model || '') + ' &mdash; ' + (d.category || '') + '</div>' +
                '</div>'
            );
        }

        function formatUnitSelected(option) {
            if (!option.id) return option.text;
            var d = unitData[option.id];
            if (!d) return option.text;
            return d.sku + (d.brand ? ' · ' + d.brand : '') + (d.model ? ' ' + d.model : '');
        }

        $('#selectUnit').select2({
            dropdownParent: $('#modalAddCatalog'),
            placeholder: 'Search by SKU, Brand, or Model...',
            allowClear: true,
            width: '100%',
            templateResult:    formatUnitResult,
            templateSelection: formatUnitSelected,
            matcher: function (params, data) {
                if (!params.term || params.term.trim() === '') return data;
                var d = unitData[data.id];
                if (!d) return null;
                var term     = params.term.toLowerCase();
                var haystack = [d.sku, d.brand, d.model, d.category].join(' ').toLowerCase();
                return haystack.indexOf(term) > -1 ? data : null;
            }
        });

        $('#selectUnit').on('select2:select', function () {
            var id      = $(this).val();
            var d       = unitData[id];
            var preview = document.getElementById('previewUnit');
            if (!d) { preview.classList.add('d-none'); return; }
            document.getElementById('prev-sku').textContent      = d.sku      || '-';
            document.getElementById('prev-brand').textContent    = d.brand    || '-';
            document.getElementById('prev-model').textContent    = d.model    || '-';
            document.getElementById('prev-category').textContent = d.category || '-';
            preview.classList.remove('d-none');
        });

        $('#selectUnit').on('select2:clear', function () {
            document.getElementById('previewUnit').classList.add('d-none');
        });

        document.getElementById('modalAddCatalog')?.addEventListener('hidden.bs.modal', function () {
            $('#selectUnit').val(null).trigger('change');
            document.getElementById('previewUnit').classList.add('d-none');
            document.querySelector('[name="price_idr_display"]').value = '';
            document.getElementById('price-idr-raw').value = '0';
            document.querySelector('[name="price_usd"]').value = '';
            document.querySelector('[name="spec_note"]').value = '';
        });

        document.querySelector('.rupiah-input')?.addEventListener('input', function () {
            var raw = this.value.replace(/\D/g, '');
            this.value = raw ? String(parseInt(raw)).replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            document.getElementById('price-idr-raw').value = raw || 0;
        });
    </script>
@endpush
