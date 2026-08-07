@extends('layouts.sales.app')
@section('title', 'Unit Product In')
@section('content')
    <div class="container-fluid flex-grow-1 container-p-y p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Barang Masuk Unit</h4>
                <p class="text-muted mb-0 small">Satu pintu masuk semua transaksi unit: beli baru, beli second, atau
                    trade-in.</p>
            </div>
            <a href="{{ route('unit-product-in.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus me-1"></i> Input Manual
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($pendingUnitPo->count())
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">PO Unit Menunggu Diterima (Goods Receipt)</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No PO</th>
                                <th>Supplier</th>
                                <th>Tanggal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingUnitPo as $po)
                                <tr>
                                    <td>{{ $po->no_po }}</td>
                                    <td>{{ $po->company }}</td>
                                    <td>{{ \Carbon\Carbon::parse($po->date)->format('d-m-Y') }}</td>
                                    <td>
                                        <a href="{{ route('unit-product-in.goods-receipt-form', $po->id) }}"
                                            class="btn btn-sm btn-outline-primary">Terima Barang</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Riwayat Barang Masuk Unit</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No Transaksi</th>
                            <th>Tipe Transaksi</th>
                            <th>Supplier / Customer</th>
                            <th>Tanggal</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unitProductIns as $item)
                            <tr>
                                <td>{{ $item->no_transaksi }}</td>
                                <td>
                                    @switch($item->transaction_type)
                                        @case('purchase_new')
                                            <span class="badge bg-label-success">Beli Unit Baru</span>
                                        @break

                                        @case('purchase_used')
                                            <span class="badge bg-label-warning">Beli Unit Second</span>
                                        @break

                                        @case('trade_in')
                                            <span class="badge bg-label-info">Trade-In</span>
                                        @break
                                    @endswitch
                                </td>
                                <td>{{ $item->supplier->supplier ?? $item->id_customer ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                                <td>{{ $item->note }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
