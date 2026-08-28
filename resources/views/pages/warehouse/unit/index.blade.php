@extends('layouts.sales.app')
@section('title', 'Unit — Siap Ditawarkan')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center py-3 mb-2">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Warehouse /</span> Unit
        </h4>
    </div>
    <p class="text-muted mb-4">
        Data unit yang sama dengan yang dilihat admin — <strong>Unit Bekas</strong> (aset perusahaan, dari
        <a href="{{ route('unit-acquisition.index') }}">Unit Acquisition</a>) dan <strong>Unit Baru</strong>
        (stok jual hasil Goods Receipt PO). Klik nama unit untuk melihat spesifikasi.
    </p>

    @php
        // Kolom spesifikasi per kategori — dijaga sinkron dengan halaman admin
        // (pages/warehouse/unit-acquisition/index.blade.php + table-unit-*.js).
        $bekasSpecs = [
            'screw'  => ['Lubricant', 'Power', 'Air Capacity'],
            'dryer'  => ['Type', 'PDP', 'FAD'],
            'filter' => ['FAD', 'Grade', 'Connection'],
            'tank'   => ['Capacity', 'Material', 'Type'],
        ];
        $baruSpecs = [
            'screw'   => ['Type', 'Lubricant', 'Power', 'Air Capacity'],
            'dryer'   => ['Type', 'PDP', 'FAD'],
            'filter'  => ['FAD', 'Grade', 'Connection'],
            'chiller' => ['Cooling Capacity', 'kW / Power'],
        ];
        $bekasPills = ['screw' => 'Compressor', 'dryer' => 'Dryer', 'filter' => 'Filter', 'tank' => 'Air Receiver Tank'];
        $baruPills  = ['screw' => 'Compressor', 'dryer' => 'Dryer', 'filter' => 'Filter', 'chiller' => 'Chiller'];
    @endphp

    <div class="card mb-4">
        {{-- ── Tab utama: Unit Bekas / Unit Baru ───────────────────────── --}}
        <div class="card-header py-2 bg-transparent border-bottom">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="unitMainTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link active px-3 py-2 fw-semibold" role="tab"
                        data-bs-toggle="tab" data-bs-target="#tab-unit-bekas" aria-selected="true">
                        Unit Bekas
                        <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="unit-second-count-badge">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link px-3 py-2 fw-semibold" role="tab"
                        data-bs-toggle="tab" data-bs-target="#tab-unit-baru" aria-selected="false">
                        Unit Baru
                        <span class="badge bg-label-success rounded-pill ms-1 d-none" id="unit-baru-count-badge">0</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content p-0">

            {{-- ── Tab: Unit Bekas (Fixed Asset / Unit Acquisition) ────────── --}}
            <div class="tab-pane fade show active" id="tab-unit-bekas" role="tabpanel">
                <p class="text-muted small px-3 pt-3 mb-0">
                    Unit bekas/trade-in yang terdaftar sebagai aset perusahaan.
                </p>
                <ul class="nav nav-pills px-3 pt-3 mb-0" role="tablist">
                    @foreach ($bekasPills as $group => $label)
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link fw-semibold {{ $loop->first ? 'active' : '' }}"
                                data-bs-toggle="pill" data-bs-target="#pill-bekas-{{ $group }}" role="tab"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $label }}
                                <span class="badge bg-label-primary rounded-pill ms-1 d-none"
                                    id="unit-acquisition-{{ $group }}-count-badge">0</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content p-0">
                    @foreach ($bekasSpecs as $group => $specLabels)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pill-bekas-{{ $group }}"
                            role="tabpanel">
                            <div class="card-datatable table-responsive pt-0">
                                <table class="datatable-unit-sales-bekas table" data-group="{{ $group }}">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Unit</th>
                                            @foreach ($specLabels as $specLabel)
                                                <th>{{ $specLabel }}</th>
                                            @endforeach
                                            <th>SN (Serial Number)</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Tab: Unit Baru (Unit Inventory) ─────────────────────────── --}}
            <div class="tab-pane fade" id="tab-unit-baru" role="tabpanel">
                <p class="text-muted small px-3 pt-3 mb-0">
                    Unit baru hasil Goods Receipt PO — stok jual langsung.
                </p>
                <ul class="nav nav-pills px-3 pt-3 mb-0" role="tablist">
                    @foreach ($baruPills as $group => $label)
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link fw-semibold {{ $loop->first ? 'active' : '' }}"
                                data-bs-toggle="pill" data-bs-target="#pill-baru-{{ $group }}" role="tab"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $label }}
                                <span class="badge bg-label-success rounded-pill ms-1 d-none"
                                    id="unit-inventory-{{ $group }}-count-badge">0</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content p-0">
                    @foreach ($baruSpecs as $group => $specLabels)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pill-baru-{{ $group }}"
                            role="tabpanel">
                            <div class="card-datatable table-responsive pt-0">
                                <table class="datatable-unit-sales-baru table" data-group="{{ $group }}">
                                    <thead>
                                        <tr>
                                            <th>Unit</th>
                                            @foreach ($specLabels as $specLabel)
                                                <th>{{ $specLabel }}</th>
                                            @endforeach
                                            <th class="text-center">Stock</th>
                                            <th>Harga Jual</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    {{-- Modal spesifikasi unit — dipakai semua tab, isinya diisi lewat JS saat diklik --}}
    <div class="modal fade" id="unitSpecModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unitSpecModalTitle">Spesifikasi Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-0 small" id="unitSpecModalBody"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection()

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-sales.js"></script>
@endpush
