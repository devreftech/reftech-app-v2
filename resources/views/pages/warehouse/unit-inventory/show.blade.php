@extends('layouts.sales.app')
@section('title', 'Detail Unit — ' . ($unit->brand ?? '') . ' ' . ($unit->model ?? ''))
@section('content')
    @php
        $isCompressor = in_array($unit->unit, ['PISTON COMPRESSOR', 'AIR COMPRESSOR SCREW']);
        $isDryer = in_array($unit->unit, ['REFRIGERANT AIR DRYER', 'DESICANT DRYER']);
        $statusBadges = [
            'available' => '<span class="badge bg-label-success">Available</span>',
            'sold' => '<span class="badge bg-label-dark">Sold</span>',
        ];
        $availableCount = $inventories->where('status', 'available')->count();
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1">{{ $unit->brand }} {{ $unit->model }}</h4>
            <p class="text-muted mb-0 small">SKU: {{ $unit->sku ?: '-' }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-label-success fs-6 px-3 py-2">{{ $availableCount }} Available</span>
            <span class="badge bg-label-info fs-6 px-3 py-2">
                Harga Jual: {{ $unit->harga_jual ? 'Rp ' . number_format($unit->harga_jual, 0, ',', '.') : 'Belum di-set' }}
            </span>
            <button type="button" class="btn btn-sm btn-label-primary" id="btnEditHargaJual">
                <i class="mdi mdi-pencil-outline me-1"></i>Set Harga Jual
            </button>
            <a href="{{ route('unit-acquisition.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div id="unit-inventory-detail-root" data-unit-id="{{ $unit->id }}"></div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-semibold"><i class="mdi mdi-format-list-bulleted-square me-1 text-primary"></i>Spesifikasi Unit</h6>
                </div>
                <div class="card-body">
                    @include('components.detail-row', ['label' => 'Category', 'value' => $unit->unit])
                    @include('components.detail-row', ['label' => 'SKU', 'value' => $unit->sku])
                    @include('components.detail-row', ['label' => 'Brand', 'value' => $unit->brand])
                    @include('components.detail-row', ['label' => 'Model', 'value' => $unit->model])
                    @include('components.detail-row', ['label' => 'Generation', 'value' => $unit->generation])

                    @if ($isCompressor)
                        @include('components.detail-row', ['label' => 'Type', 'value' => $unit->type_unit])
                        @include('components.detail-row', ['label' => 'Motor Power', 'value' => $unit->power])
                        @include('components.detail-row', ['label' => 'Air Capacity', 'value' => $unit->air_cap ? $unit->air_cap . ' m³/min' : null])
                        @include('components.detail-row', ['label' => 'Max. Pressure', 'value' => $unit->bar ? $unit->bar . ' Bar' : null])
                        @include('components.detail-row', ['label' => 'Voltage', 'value' => $unit->voltage])
                        @include('components.detail-row', ['label' => 'Drive', 'value' => $unit->connect])
                        @include('components.detail-row', ['label' => 'Cooling', 'value' => $unit->cooling])
                        @include('components.detail-row', ['label' => 'Discharge', 'value' => $unit->exhaust])
                    @elseif ($isDryer)
                        @include('components.detail-row', ['label' => 'Air Capacity', 'value' => $unit->air_cap ? $unit->air_cap . ' m³/min' : null])
                        @include('components.detail-row', ['label' => 'Refrigerant Type', 'value' => $unit->refrigerant_type])
                        @include('components.detail-row', ['label' => 'PDP', 'value' => $unit->pdp])
                    @endif

                    @include('components.detail-row', ['label' => 'Filtration', 'value' => $unit->filtration])
                    @include('components.detail-row', ['label' => 'Oil Content', 'value' => $unit->oil_content])
                    @include('components.detail-row', ['label' => 'Grade', 'value' => $unit->grade])
                    @include('components.detail-row', ['label' => 'Capacity', 'value' => $unit->capacity])
                    @include('components.detail-row', ['label' => 'Material', 'value' => $unit->material])
                    @include('components.detail-row', ['label' => 'Test Pressure', 'value' => $unit->test_pressure])
                    @include('components.detail-row', ['label' => 'Inlet Pressure', 'value' => $unit->inlet_pressure])
                    @include('components.detail-row', ['label' => 'Outlet Pressure', 'value' => $unit->outlet_pressure])
                    @include('components.detail-row', ['label' => 'Inlet Capacity', 'value' => $unit->inlet_cap])
                    @include('components.detail-row', ['label' => 'Outlet Capacity', 'value' => $unit->outlet_cap])
                    @include('components.detail-row', ['label' => 'Dimension', 'value' => $unit->dimension])
                    @include('components.detail-row', ['label' => 'Weight', 'value' => $unit->weight ? $unit->weight . ' Kg' : null])
                    @if (!$isCompressor && !$isDryer)
                        @include('components.detail-row', ['label' => 'Description', 'value' => $unit->desc])
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header py-3">
                    <h6 class="mb-0 fw-semibold"><i class="mdi mdi-currency-usd me-1 text-primary"></i>
                        Stok per Serial Number
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>Serial Number</th>
                                    <th>Harga Modal</th>
                                    <th>Biaya Rebranding</th>
                                    <th>Total Modal</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($inventories as $inv)
                                    <tr>
                                        <td>{{ $inv->serial_number ?: '-' }}</td>
                                        <td>Rp {{ number_format($inv->harga_modal, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($inv->rebrandingCosts->isEmpty())
                                                <span class="badge bg-label-secondary">Belum Ada Rincian</span>
                                            @else
                                                Rp {{ number_format($inv->biaya_rebranding, 0, ',', '.') }}
                                            @endif
                                        </td>
                                        <td>Rp {{ number_format($inv->total_modal, 0, ',', '.') }}</td>
                                        <td>{!! $statusBadges[$inv->status] ?? $inv->status !!}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="modal"
                                                data-bs-target="#modalRebranding-{{ $inv->id }}" title="Rebranding">
                                                <i class="mdi mdi-spray-bottle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada stok.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat masuk & keluar di-scope ke SEMUA unit dengan model yang sama (id_unit),
         bukan cuma satu serial number — tiap transaksi masuk beda serial number, tapi
         qty stok model ini yang nambah. Pakai DataTable karena datanya bakal terus tumbuh. --}}
    <div class="card mb-4">
        <div class="card-header py-2 bg-transparent border-bottom">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="unitInventoryHistoryTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link active px-3 py-2 fw-semibold" role="tab"
                        data-bs-toggle="tab" data-bs-target="#tab-riwayat-masuk"
                        aria-controls="tab-riwayat-masuk" aria-selected="true">
                        <i class="mdi mdi-truck-delivery-outline me-1"></i>Riwayat Barang Masuk
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link px-3 py-2 fw-semibold" role="tab"
                        data-bs-toggle="tab" data-bs-target="#tab-riwayat-keluar"
                        aria-controls="tab-riwayat-keluar" aria-selected="false">
                        <i class="mdi mdi-truck-outline me-1"></i>Riwayat Barang Keluar
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content p-0">
            <div class="tab-pane fade show active" id="tab-riwayat-masuk" role="tabpanel">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-unit-inventory-in table">
                        <thead>
                            <tr>
                                <th>Tgl Masuk</th>
                                <th>No Transaksi</th>
                                <th>Serial Number</th>
                                <th>Supplier</th>
                                <th>Harga Modal</th>
                                <th>Biaya Rebranding</th>
                                <th>Total Modal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="tab-riwayat-keluar" role="tabpanel">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-unit-inventory-out table">
                        <thead>
                            <tr>
                                <th>Tgl Keluar</th>
                                <th>No Transaksi</th>
                                <th>Serial Number</th>
                                <th>Customer</th>
                                <th>Harga Jual</th>
                                <th>Nilai Pokok</th>
                                <th>Selisih</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalHargaJualInventory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('unit-inventory.harga-jual', $unit->id) }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Set Harga Jual — {{ $unit->brand }} {{ $unit->model }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small">Berlaku buat semua serial number model ini.</p>
                        <div class="mb-3">
                            <label class="form-label">Harga Jual (Rp)</label>
                            <input type="number" class="form-control" name="harga_jual" min="0" step="1"
                                value="{{ $unit->harga_jual }}" required>
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

    {{-- Modal rincian biaya rebranding — satu per unit fisik (unit_inventory), diisi
         belakangan setelah GR, bisa lebih dari satu baris (cat, stiker, ongkos kerja, dst). --}}
    @foreach ($inventories as $inv)
        <div class="modal fade" id="modalRebranding-{{ $inv->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold mb-0">Rincian Biaya Rebranding</h5>
                            <p class="text-muted small mb-0">{{ $unit->brand }} {{ $unit->model }} — SN {{ $inv->serial_number ?: '-' }}</p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <div class="border rounded-3 p-3 text-center h-100">
                                    <div class="text-muted small mb-1">Harga Modal</div>
                                    <div class="fw-semibold">Rp {{ number_format($inv->harga_modal, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded-3 p-3 text-center h-100">
                                    <div class="text-muted small mb-1">Biaya Rebranding</div>
                                    <div class="fw-semibold">Rp {{ number_format($inv->biaya_rebranding, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border rounded-3 p-3 text-center h-100 bg-label-primary">
                                    <div class="text-muted small mb-1">Total Modal</div>
                                    <div class="fw-bold">Rp {{ number_format($inv->total_modal, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-semibold mb-2">Riwayat Rincian</h6>
                        <div class="table-responsive mb-4 border rounded-3">
                            <table class="table table-sm mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Item</th>
                                        <th class="text-end">Biaya</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($inv->rebrandingCosts as $cost)
                                        <tr>
                                            <td class="text-nowrap">{{ $cost->date ? \Carbon\Carbon::parse($cost->date)->format('d-m-Y') : '-' }}</td>
                                            <td>
                                                {{ $cost->item }}
                                                @if ($cost->note)
                                                    <small class="text-muted d-block">{{ $cost->note }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end">Rp {{ number_format($cost->amount, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('unit-inventory.rebranding-cost.destroy', $cost->id) }}"
                                                    method="post" onsubmit="return confirm('Hapus rincian biaya ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-text-danger">
                                                        <i class="mdi mdi-delete-outline"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">Belum ada rincian biaya rebranding.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <h6 class="fw-semibold mb-2">Tambah Rincian</h6>
                        <form action="{{ route('unit-inventory.rebranding-cost.store', $inv->id) }}" method="post" class="border rounded-3 p-3 bg-light-subtle">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1">Tanggal</label>
                                    <input type="date" class="form-control form-control-sm" name="date"
                                        value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-1">Item</label>
                                    <input type="text" class="form-control form-control-sm" name="item"
                                        placeholder="mis. Cat, Stiker, Ongkos Kerja" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small text-muted mb-1">Biaya (Rp)</label>
                                    <input type="number" class="form-control form-control-sm" name="amount"
                                        placeholder="0" min="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted mb-1">Catatan</label>
                                    <input type="text" class="form-control form-control-sm" name="note"
                                        placeholder="Opsional">
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="mdi mdi-plus me-1"></i>Tambah Rincian
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-unit-inventory-detail.js"></script>
    <script>
        $(document).ready(function () {
            var modalEl = document.getElementById('modalHargaJualInventory');
            if (modalEl) {
                var modal = new bootstrap.Modal(modalEl);
                $('#btnEditHargaJual').on('click', function () {
                    modal.show();
                });
            }
        });
    </script>
@endpush
