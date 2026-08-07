@extends('layouts.sales.app')
@section('title', 'Unit Acquisition Detail')
@section('content')
    <div class="row invoice-preview">
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <h3 class="fw-bold">Unit Acquisition</h3>
                        </div>
                        <div class="text-end">
                            <span class="fw-bolder">{{ $fixed->no_invoice ?? 'no invoice' }}</span>
                            <div class="mt-1">
                                <span class="text-muted">{{ \Carbon\Carbon::parse($fixed->beli)->format('d-m-Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <h4>
                        {{ $fixed->code }}
                        @if ($fixed->kondisi)
                            <span class="badge bg-label-secondary">{{ $fixed->kondisi === 'Baru' ? 'Unit Baru' : 'Unit Second' }}</span>
                        @endif
                        @if ($fixed->qc_status === 'checking')
                            <span class="badge bg-label-warning">Dalam Pengecekan</span>
                        @elseif ($fixed->qc_status === 'ok')
                            <span class="badge bg-label-success">OK — Siap Ditawarkan</span>
                        @elseif ($fixed->qc_status === 'reject')
                            <span class="badge bg-label-danger">Reject</span>
                        @endif
                        @if ($fixed->status_unit === 'Service')
                            <span class="badge bg-label-warning">Sedang Service</span>
                        @elseif ($fixed->status_unit === 'Rental')
                            <span class="badge bg-label-primary">Sedang Rental</span>
                        @elseif ($fixed->status_unit === 'Breakdown')
                            <span class="badge bg-label-danger">Breakdown</span>
                        @elseif ($fixed->status_unit === 'Reserved')
                            <span class="badge bg-label-info">Reserved</span>
                        @elseif ($fixed->status_unit === 'Sold')
                            <span class="badge bg-label-dark">Sold</span>
                        @endif
                    </h4>
                    @if ($fixed->unit)
                        <p class="text-muted mb-2">Unit: {{ $fixed->unit->brand }} {{ $fixed->unit->model }} — {{ $fixed->unit->sku }}</p>
                    @endif
                    <p class="text-muted mb-2">Serial Number: {{ $fixed->serial_number ?: '-' }}</p>
                    <p class="text-muted mb-2">
                        Harga Jual:
                        <span class="fw-semibold">
                            {{ $fixed->harga_jual ? 'Rp ' . number_format($fixed->harga_jual, 0, ',', '.') : 'Belum diset' }}
                        </span>
                    </p>
                </div>
                <div class="table-responsive px-4 pb-4">
                    <h5>Riwayat Servis / Spare Part</h5>
                    <table class="table table-striped table-bordered m-0">
                        <thead class="table-light border-top">
                            <tr>
                                <th>Tanggal</th>
                                <th>Spare Part</th>
                                <th>Warehouse</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($services as $service)
                                <tr style="font-size: 13px">
                                    <td>{{ \Carbon\Carbon::parse($service->date)->format('d-m-Y') }}</td>
                                    <td>{{ $service->detailProduct?->product?->commodity ?? '-' }}</td>
                                    <td>{{ $service->warehouse }}</td>
                                    <td>{{ $service->qty }}</td>
                                    <td>Rp {{ number_format($service->amount, 0, ',', '.') }}</td>
                                    <td>{{ $service->note }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada servis tercatat</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($fixed->machine)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Riwayat Service Report</h5>
                    </div>
                    <div class="card-datatable table-responsive">
                        <table class="table table-striped table-bordered m-0 datatable-service-report-history">
                            <thead class="table-light border-top">
                                <tr>
                                    <th>Service Report</th>
                                    <th>Service Type</th>
                                    <th>Job Description</th>
                                    <th>Date</th>
                                    <th>Technician</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body">
                    @if ($fixed->qc_status === 'checking' || $fixed->kondisi === 'Baru')
                        <a href="{{ route('unit-acquisition.service.create', $fixed->id) }}"
                            class="btn btn-primary d-grid w-100 mb-3 waves-effect">
                            Tambah Servis / Spare Part
                        </a>
                    @endif
                    @if ($fixed->qc_status === 'checking')
                        @if (auth()->user()->role === 'Admin')
                            <form action="{{ route('unit-acquisition.confirm', $fixed->id) }}" method="post" class="mb-2">
                                @csrf
                                <input type="hidden" name="decision" value="ok">
                                <button type="submit" class="btn btn-success d-grid w-100 waves-effect">
                                    Konfirmasi OK — Siap Ditawarkan
                                </button>
                            </form>
                            <form action="{{ route('unit-acquisition.confirm', $fixed->id) }}" method="post" class="mb-3">
                                @csrf
                                <input type="hidden" name="decision" value="reject">
                                <button type="submit" class="btn btn-outline-danger d-grid w-100 waves-effect">
                                    Reject Unit
                                </button>
                            </form>
                        @else
                            <p class="text-muted small">Menunggu konfirmasi Admin.</p>
                        @endif
                    @endif
                    @if ($fixed->qc_status === 'ok' && auth()->user()->role === 'Admin')
                        <form action="{{ route('unit-acquisition.status', $fixed->id) }}" method="post" class="mb-3">
                            @csrf
                            <div class="form-floating form-floating-outline mb-2">
                                <select class="form-select" name="status_unit">
                                    <option value="OK" {{ $fixed->status_unit === 'OK' ? 'selected' : '' }}>OK</option>
                                    <option value="Service" {{ $fixed->status_unit === 'Service' ? 'selected' : '' }}>Sedang Service</option>
                                    <option value="Rental" {{ $fixed->status_unit === 'Rental' ? 'selected' : '' }}>Sedang Rental</option>
                                    <option value="Breakdown" {{ $fixed->status_unit === 'Breakdown' ? 'selected' : '' }}>Breakdown</option>
                                    <option value="Reserved" {{ $fixed->status_unit === 'Reserved' ? 'selected' : '' }}>Reserved</option>
                                    <option value="Sold" {{ $fixed->status_unit === 'Sold' ? 'selected' : '' }}>Sold</option>
                                </select>
                                <label>Status Unit</label>
                            </div>
                            <button type="submit" class="btn btn-outline-primary d-grid w-100 waves-effect">
                                Update Status
                            </button>
                        </form>
                        <form action="{{ route('unit-acquisition.harga-jual', $fixed->id) }}" method="post" class="mb-3">
                            @csrf
                            <div class="input-group mb-2">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control rupiah-input" id="hargaJualDisplay"
                                    placeholder="0" autocomplete="off"
                                    value="{{ $fixed->harga_jual ? number_format(old('harga_jual', $fixed->harga_jual), 0, ',', '.') : '' }}">
                                <input type="hidden" name="harga_jual" id="hargaJualRaw"
                                    value="{{ old('harga_jual', $fixed->harga_jual) }}">
                            </div>
                            <button type="submit" class="btn btn-outline-primary d-grid w-100 waves-effect">
                                Simpan Harga Jual
                            </button>
                        </form>
                    @endif
                    @if ($fixed->machine)
                        <a href="{{ route('service-reports.unit.machine', [$fixed->machine->id_unit, $fixed->id_machine]) }}"
                            class="btn {{ $fixed->status_unit === 'Rental' ? 'btn-warning' : 'btn-outline-secondary' }} d-grid w-100 mb-3 waves-effect">
                            {{ $fixed->status_unit === 'Rental' ? 'Buat Service Report (Wajib Sebelum Sewa)' : 'Service Report' }}
                        </a>
                    @endif
                    <a href="{{ route('fixed.edit', $fixed->id) }}"
                        class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect">
                        Edit Data
                    </a>
                    <a href="{{ route('fixed.show', $fixed->id) }}"
                        class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect">
                        Lihat di Finance
                    </a>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush
@push('script')
    <script>
        $('#backButton').click(function() {
            window.history.back();
        });

        document.getElementById('hargaJualDisplay')?.addEventListener('input', function() {
            var raw = this.value.replace(/\D/g, '');
            this.value = raw ? String(parseInt(raw)).replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            document.getElementById('hargaJualRaw').value = raw || '';
        });

        @if ($fixed->machine)
            $(function() {
                $('.datatable-service-report-history').DataTable({
                    ajax: {
                        type: 'GET',
                        url: '/db/service-reports/machine/{{ $fixed->id_machine }}'
                    },
                    columns: [
                        { data: 'no_service' },
                        { data: 'type' },
                        { data: 'jobdesc' },
                        { data: 'date' },
                        { data: 'technician' },
                    ],
                    columnDefs: [{
                            targets: 0,
                            render: function(data, type, full) {
                                var url = route('service-reports.show', full.id);
                                return '<a href="' + url + '" class="fw-semibold text-primary">' + (data ??
                                    '-') + '</a>';
                            }
                        },
                        {
                            targets: 2,
                            render: function(data) {
                                if (!data) return '-';
                                return data.length > 60 ?
                                    '<span title="' + data + '">' + data.substring(0, 60) + '...</span>' :
                                    data;
                            }
                        },
                        {
                            targets: 3,
                            className: 'text-center',
                            render: function(data) {
                                if (!data) return '-';
                                var d = new Date(data);
                                return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1))
                                    .slice(-2) + '-' + d.getFullYear();
                            }
                        },
                    ],
                    order: [
                        [3, 'desc']
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-end"f>>' +
                        '<"table-responsive"t>' +
                        '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                });
            });
        @endif
    </script>
@endpush
